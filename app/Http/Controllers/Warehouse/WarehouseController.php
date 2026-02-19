<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\Packing\PackingTypeLabelPrintingType;
use App\Models\Order\TransKind;
use App\Models\Utility\Option;
use App\Models\Utility\Pdf;
use App\Models\Utility\Printer\PrinterFile;
use App\Models\Utility\Setting;
use App\Models\Warehouse\Shelving\ShelvingCell;
use App\Models\Warehouse\Warehouse;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Milon\Barcode\DNS1D;
use Mpdf\Tag\S;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class WarehouseController extends Controller
{
    private $view_path = "warehouse.warehouse.";
    private $route_path = "wh.warehouse.";

    public function index(Request $request)
    {

        if ($request->isMethod('post')) {
            $search = $request->search;
            $order_by = $request->order_by;
        } else {
            $search = session("search_warehouse");
            $order_by = session("order_by_warehouse");
        }

        $order_by = $order_by == "" ? "warehouse_type_id__asc" : $order_by;

        session(["search_warehouse" => $search, "order_by_warehouse" => $order_by]);


        $list = Warehouse::
        where("code", "like", "%" . $search . "%")->
        orWhere("caption", "like", "%" . $search . "%")->
        when($order_by != "", function ($query) use ($order_by) {
            $order_by = Str::of($order_by)->explode("__");

            return $query->orderBy($order_by[0], $order_by[1]);
        })->paginate(50);

        $order_by_Option = Option::OrderBy("order_by_warehouse", $order_by);

        return view($this->view_path . "index", compact("list", "search", "order_by_Option"));
    }

    public  function create()
    {

        $warehouse = new Warehouse();
        $warehouse_packing_type_ids = [3,4];
        $packing_type_label_printing_type_option =Option::get("label_packing_type_list_option",0,Warehouse::$PackingTypeLabelPrintingTypesValidExitFormIds,$warehouse_packing_type_ids);

        $shift_option = Option::get("shift");
        return view($this->view_path . "create", compact("warehouse_packing_type_ids","packing_type_label_printing_type_option","warehouse", "shift_option"));
    }

    public function store(Request $request)
    {

        if ($request->code == "" || Warehouse::ExistsCode($request->code)) {
            return back()->withErrors("کد انبار تکراری است");
        }

        $request["cheek_loading_control_for_exist_form"] = $request->cheek_loading_control_for_exist_form ? 1 : 0;
        $request["check_amount_product_in_entry"] = $request->check_amount_product_in_entry ? 1 : 0;
        $request["allow_entry_with_pin"] = $request->allow_entry_with_pin ? 1 : 0;
        $request["exit_form_label_printing_type_ids"]=json_encode($request->exit_form_label_printing_type_ids);
        $request["allow_select_partial_of_packing_in_output"]= $request->allow_select_partial_of_packing_in_output ? 1 : 0;

        Warehouse::create($request->all());


        return redirect()->route($this->route_path . "index")->with(["success" => "انبار با موفقیت اضافه شد"]);

    }

    public function edit(Warehouse $warehouse)
    {


        $shift_option = Option::get("shift", $warehouse->shift_id);
        $warehouse_packing_type_ids = json_decode($warehouse->exit_form_label_printing_type_ids);
        $packing_type_label_printing_type_option =Option::get("label_packing_type_list_option",0,Warehouse::$PackingTypeLabelPrintingTypesValidExitFormIds,$warehouse_packing_type_ids);


        return view($this->view_path . "edit", compact("packing_type_label_printing_type_option", "warehouse_packing_type_ids", "warehouse", "shift_option"));

    }

    public function update(Request $request, Warehouse $warehouse)
    {
        if ($request->code == "" || $warehouse::ExistsCode($request->code, $warehouse->id)) {
            return back()->withErrors("کد تکراری است");
        }

        $request["cheek_loading_control_for_exist_form"] = $request->cheek_loading_control_for_exist_form ? 1 : 0;
        $request["check_amount_product_in_entry"] = $request->check_amount_product_in_entry ? 1 : 0;
        $request["allow_entry_with_pin"] = $request->allow_entry_with_pin ? 1 : 0;
        $request["exit_form_label_printing_type_ids"]=json_encode($request->exit_form_label_printing_type_ids);
        $request["allow_select_partial_of_packing_in_output"]= $request->allow_select_partial_of_packing_in_output ? 1 : 0;
        $warehouse->update($request->all());

        return redirect()->route($this->route_path . "index")->with(["success" => "اطلاعات با موفقیت ذخیره شد"]);

    }


    /*****************************************/
    public function shelving_index(Warehouse $warehouse)
    {
        $shelving_cells_type = ShelvingCell::GetName();
        $shelving_cells = ShelvingCell::where("warehouse_id", $warehouse->id)->orderBy("id")->firstOrCreate(["warehouse_id" => $warehouse->id]);
        return view($this->view_path . "shelving_index", compact("warehouse", "shelving_cells", "shelving_cells_type"));
    }

    public function shelving_submit(Request $request, Warehouse $warehouse)
    {

        $request->all();
        $shelving_cells_type = ShelvingCell::GetName();
        $list = [];
        foreach ($shelving_cells_type as $id => $item) {
            $list["line" . $id . "_number"] = isset($request->cell['number'][$id]) ? $request->cell['number'][$id] : null;
            $list["line" . $id . "_type"] = isset($request->cell['type'][$id]) ? $request->cell['type'][$id] : null;
            $list["line" . $id . "_part"] = isset($request->cell['part'][$id]) ? $request->cell['part'][$id] : null;

            $status_id = (isset($request->cell['status'][$id])) ? 1200 : 1210;
            $list["line" . $id . "_status_id"] = $status_id;


        }

        ShelvingCell::updateOrCreate(
            ["warehouse_id" => $warehouse->id],
            $list
        );
        return back()->with(["success" => "اطلاعات با موفقیت ثبت گردید."]);

    }

    public function shelving_print_label(Request $request, Warehouse $warehouse)
    {
        $worker = Worker::find(Auth::user()->id);
        if (!$worker->default_printer_id) {
            return redirect()->route("utility.printer.select_default_printer")->withErrors("لطفا پرینتر پیش فرض را انتخاب نمایید.");
        }

        $cell_info = $request->cell_info;

        $result_code = ShelvingCell::GetCodeLable($warehouse, $cell_info);

        if (!$result_code["result"]) {
            return back()->withErrors($result_code["error"]);
        }

        $result = $this->create_pdf_file($warehouse, $result_code["code"], $request->updown, $worker, "download");
        $packing_type_label_printing_type = PackingTypeLabelPrintingType::find(12);
        Pdf::labelPrinter($result["html"],
            $packing_type_label_printing_type->orientation,
            $warehouse->id, [
                $packing_type_label_printing_type->width,
                $packing_type_label_printing_type->long
            ]
        );
    }

    public static function create_pdf_file(Warehouse $warehouse, $cell_code, $updown, $worker, $type)
    {
        $static_ip = "https://deyaco.ir/" . env("APP_NAME");
        $local_ip = url("");

        $url = "123466";
        $barcode_id = "12345";
        $url = \Illuminate\Support\Str::replace($local_ip, $static_ip, $url);

        $packing_type_label_printing_type = PackingTypeLabelPrintingType::find(12);
        $qr = QrCode::size($packing_type_label_printing_type->qr_size)->generate($url);
        $barcode = DNS1D::getBarcodeSVG($barcode_id, 'C39', 1.6, 30);
        $barcode_pin = "";


        $software_name = Setting::getStringValue("software_name");
        $html[0] = view("warehouse.warehouse.shelving_print_label._head")->render();
        $html[0] .= view("warehouse.warehouse.shelving_print_label._print_info", compact("warehouse", 'barcode', 'qr', 'updown', 'cell_code'))->render() . $html[0];
        $html[0] .= view("warehouse.warehouse.shelving_print_label._footer")->render();

        $print_file = null;
        if ($type != "download") {
            $print_file = PrinterFile::create([
                "user_id" => $worker->id,
                "filename" => $barcode_id . ".pdf",
                "status_id" => 305001, // در انتظار دانلود
                "is_landscape" => $packing_type_label_printing_type->orientation == "L" ? 1 : 0,
                "printer_id" => $worker->default_label_printer_id,
                "number_of_prints" => $worker->default_label_print_number
            ]);
        }

        return ["html" => $html, "print_file" => $print_file];
    }

}
