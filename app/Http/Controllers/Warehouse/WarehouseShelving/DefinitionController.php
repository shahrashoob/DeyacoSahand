<?php

namespace App\Http\Controllers\Warehouse\WarehouseShelving;

use App\Http\Controllers\Controller;
use App\Models\Accounting\Account;
use App\Models\Accounting\CostCenter;
use App\Models\LineProduct\Machine\MachineType;
use App\Models\LineProduct\Packing\PackingTypeLabelPrintingType;
use App\Models\Utility\Option;
use App\Models\Utility\Pdf;
use App\Models\Utility\Printer\PrinterFile;
use App\Models\Utility\Setting;
use App\Models\Utility\Status;
use App\Models\Warehouse\Shelving\ShelvingCell;
use App\Models\Warehouse\Warehouse;
use App\Models\Warehouse\WarehouseShelving\WarehouseShelving;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Milon\Barcode\DNS1D;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use function back;
use function redirect;
use function session;
use function view;

class DefinitionController extends Controller
{
    //
    private $view_path = "warehouse.warehouse_shelving.definition.";
    private $route_path = "wh.warehouse_shelving.definition.";

    public function index(Warehouse $warehouse, Request $request)
    {

        $list = WarehouseShelving::
        where("warehouse_id", $warehouse->id)->
        where("parent_id",0)->paginate(50);

        $post_user = \Auth::user()->posts->first();
        $allow_view_qr=$post_user->checkButtonPermission("wh.warehouse_shelving.dashboard.view_qr");


        return view($this->view_path . "index", compact("list", "warehouse","allow_view_qr"));
    }

    public function list(Warehouse $warehouse, WarehouseShelving $warehouseShelving)
    {

        $list = WarehouseShelving::where("parent_id", $warehouseShelving->id)->paginate(50);

        $post_user = \Auth::user()->posts->first();
        $allow_view_qr=$post_user->checkButtonPermission("wh.warehouse_shelving.dashboard.view_qr");

        return view($this->view_path . "list", compact("list", "warehouse", "warehouseShelving","allow_view_qr"));
    }

    public function create(Warehouse $warehouse)
    {

        $status_option = Option::get("status", 1200, 1100);

        return view($this->view_path . "create", compact("warehouse", "status_option"));
    }

    public function store(Warehouse $warehouse, Request $request)
    {

        $request["warehouse_id"] = $warehouse->id;
        $request["parent_id"] = 0;
        $request["warehouse_shelving_type_id"] = 1;
        $warehouse_shelving = WarehouseShelving::create($request->all());

        // بروز رسانی کد ها
        WarehouseShelving::UpdateFullCodeFroAll($warehouse_shelving);

        return redirect()->route($this->route_path . "index", $warehouse)->with(["success" => "یک سایت با موفقیت اضافه شد"]);

    }

    public function create_sub_line(Warehouse $warehouse, WarehouseShelving $warehouseShelving)
    {

        $status_option = Option::get("status", 1200, 1100);
        return view($this->view_path . "create_sub_line", compact("warehouse", "warehouseShelving", "status_option"));
    }

    public function store_sub_line(Request $request, Warehouse $warehouse, WarehouseShelving $warehouseShelving)
    {


        $result = WarehouseShelving::AllowCreateSubLines($warehouseShelving,$request);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }

        $request["parent_id"] = $warehouseShelving->id;
        $request["warehouse_id"] = $warehouse->id;
        $request["warehouse_shelving_type_id"] = $warehouseShelving->warehouse_shelving_type_id+1;
        $new_warehouseShelving = WarehouseShelving::create($request->all());

        WarehouseShelving::UpdateFullCodeFroAll($warehouseShelving);

        return redirect()->route($this->route_path . "list", [$warehouse, $warehouseShelving])->with(["success" => "یک *** با موفقیت اضافه شد"]);

    }

    public function edit(Warehouse $warehouse,WarehouseShelving $warehouseShelving)
    {

        $status_option = Option::get("status", $warehouseShelving->warehouse_shelving_line_status_id, 1100);
        return view($this->view_path . "edit", compact("warehouseShelving","warehouse","status_option"));

    }

    public function update(Request $request,Warehouse $warehouse, WarehouseShelving $warehouseShelving)
    {

        $result = WarehouseShelving::AllowCreateSubLines($warehouseShelving,$request,true);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }



        $warehouseShelving->update($request->all());
        $warehouseShelving=WarehouseShelving::find($warehouseShelving->id);

        WarehouseShelving::UpdateFullCodeFroAll($warehouseShelving->parent?$warehouseShelving->parent:$warehouseShelving);


        return redirect()->route($this->route_path .($warehouseShelving->parent?"list":"index"), [$warehouse, $warehouseShelving->parent])->with(["success" => "ویرایش با موفقیت انجام شد."]);

    }



    public function shelving_print_label( Warehouse $warehouse, WarehouseShelving $warehouseShelving,$packing_type_label_printing_type_id)
    {
        $worker = Worker::find(Auth::user()->id);
        if (!$worker->default_printer_id) {
            return redirect()->route("utility.printer.select_default_printer")->withErrors("لطفا پرینتر پیش فرض را انتخاب نمایید.");
        }

        $packing_type_label_printing_type = PackingTypeLabelPrintingType::find($packing_type_label_printing_type_id);
        $result = $this->create_pdf_file($warehouse, $warehouseShelving, $worker, "print",$packing_type_label_printing_type);

        Pdf::labelPrinter($result["html"],
            $packing_type_label_printing_type->orientation,
            $warehouse->id, [
                $packing_type_label_printing_type->width,
                $packing_type_label_printing_type->long
            ],
            $result["print_file"]
        );
        return back()->with(["success" => "جهت دریافت لیبل پرینت شده، به محل پرینتر شماره " . $worker->default_label_printer_id . " مراجعه فرمایید."]);

    }
    public function shelving_download_label( Warehouse $warehouse, WarehouseShelving $warehouseShelving,$packing_type_label_printing_type_id)
    {
        $worker = Worker::find(Auth::user()->id);
        if (!$worker->default_printer_id) {
            return redirect()->route("utility.printer.select_default_printer")->withErrors("لطفا پرینتر پیش فرض را انتخاب نمایید.");
        }
        $packing_type_label_printing_type = PackingTypeLabelPrintingType::find($packing_type_label_printing_type_id);

        $result = $this->create_pdf_file($warehouse, $warehouseShelving, $worker, "download",$packing_type_label_printing_type);

        Pdf::labelPrinter($result["html"],
            $packing_type_label_printing_type->orientation,
            $warehouse->id, [
                $packing_type_label_printing_type->width,
                $packing_type_label_printing_type->long
            ]
        );
    }

    public static function create_pdf_file(Warehouse $warehouse, WarehouseShelving $warehouseShelving, $worker, $type,$packing_type_label_printing_type)
    {

        $static_ip = "https://deyaco.ir/" . env("APP_NAME");
        $local_ip = url("");

        $url = route("DCWS_ShortLink", $warehouseShelving->id);

        $barcode_id =$warehouseShelving->id;
        $url = \Illuminate\Support\Str::replace($local_ip, $static_ip, $url);



        $qr_size=$packing_type_label_printing_type->qr_size;
        switch ($packing_type_label_printing_type->id){
            case 15:
                $qr_size=100;
                break;
            case 2:
                $qr_size=120;
                break;
        }
        $qr = QrCode::size($qr_size)->generate($url);
//        $barcode = DNS1D::getBarcodeSVG($barcode_id, 'C39', 1.6, 30);
        $barcode_pin = "";



        $software_name = Setting::getStringValue("software_name");
            $updown=$warehouseShelving->updown_code;
        $cell_code=$warehouseShelving->fullCode();
        $html[0] = view("warehouse.warehouse_shelving.definition.shelving_print_label._head")->render();
        $html[0] .= view("warehouse.warehouse_shelving.definition.shelving_print_label.".$packing_type_label_printing_type->id."._print_info", compact("software_name","warehouse", 'qr', 'updown', 'cell_code'))->render() . $html[0];
        $html[0] .= view("warehouse.warehouse_shelving.definition.shelving_print_label._footer")->render();

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

    public function DCWS_ShortLink(WarehouseShelving $warehouseShelving)
    {

    }


//
//    public function destroy( CostCenter $cost_center ) {
//        if (
//            MachineType::where( "ic", $cost_center->id )->exists()
//        ) {
//            return back()->withErrors( "به دلیل استفاده شدن در رکوردهای دیگر، امکان حذف وجود ندارد" );
//        }
//        $cost_center->delete();
//
//        return redirect()->route( $this->route_path . "index" )->with( [ "success" => "یک آیتم با موفقیت حذف گردید" ] );
//
//    }
}
