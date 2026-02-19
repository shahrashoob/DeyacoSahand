<?php

namespace App\Http\Controllers\Utility\Printer;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\FabricRaw\PackingForm\PrintQRController;
use App\Models\LineProduct\Packing\PackingTypeLabelPrintingType;
use App\Models\Utility\Option;
use App\Models\Utility\Pdf;
use App\Models\Utility\Printer\Printer;
use App\Models\Utility\Printer\PrinterFile;
use App\Models\Utility\Setting;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrinterController extends Controller
{

    private $view_path = "utility.printer.";
    private $route_path = "utility.printer.";

    /*********/
    public function select_default_printer()
    {
        $worker = Worker::find(Auth::user()->id);
        $printer_option = Option::get("printers", $worker->default_printer_id, 1);
        $label_printer_option = Option::get("printers", $worker->default_label_printer_id, 2);
        $print_number_option = Option::get("print_number", $worker->default_print_number);
        $label_print_number_option = Option::get("print_number", $worker->default_label_print_number, "label");

        return view("utility.printer.select_default_printer", compact("printer_option", "label_printer_option", "print_number_option", "label_print_number_option"));
    }

    public function submit_select_default_printer(Request $request)
    {
        $worker = Worker::find(Auth::user()->id);
        $worker->default_printer_id = $request->default_printer_id;
        $worker->default_label_printer_id = $request->default_label_printer_id;
        $worker->default_print_number = $request->default_print_number > 5 ? 5 : $request->default_print_number;
        $worker->default_label_print_number = $request->default_label_print_number > 5 ? 5 : $request->default_label_print_number;
        $worker->save();

        return redirect()->route("dashboard")->with(["success" => "ثبت با موفقیت انجام شد."]);
    }

    /************/


    public function index(Request $request)
    {

        $list = Printer::paginate(20);

        $order_by_Option = null;
        $search = "";

        return view($this->view_path . "index", compact("list", "search", "order_by_Option"));
    }

    public function create()
    {

        $printer = new Printer();

        $printer_type_option = Option::get("printer_type", $printer->printer_type_id);

        return view($this->view_path . "create", compact("printer", "printer_type_option"));
    }

    public function store(Request $request)
    {

        if ($request->caption == "" || Printer::ExistsCode($request->caption)) {
            return back()->withErrors("عنوان پرینتر  تکراری است");
        }
        $printer = Printer::create($request->all());
        $printer->password = rand(11111, 999999);
        $printer->getCode();

        return redirect()->route($this->route_path . "index")->with(["success" => "یک پرینتر با موفقیت اضافه شد"]);

    }

    public function edit(Printer $printer)
    {

        $printer_type_option = Option::get("printer_type", $printer->printer_type_id);

        return view($this->view_path . "edit", compact("printer", "printer_type_option"));

    }

    public function update(Request $request, Printer $printer)
    {
        if ($request->caption == "" || Printer::ExistsCode($request->caption, $printer->id)) {
            return back()->withErrors("عنوان تکراری است");
        }
        $printer->update($request->all());

        return redirect()->route($this->route_path . "index")->with(["success" => "اطلاعات با موفقیت ذخیره شد"]);

    }

    public function test(Printer $printer)
    {
        $worker = Worker::find(Auth::user()->id);

        $software_name = Setting::getStringValue("software_name");
        $html[0] = view("utility.printer.print" . "._head")->render();
        $html[0] .= view("utility.printer.print" . "._print_info", compact("software_name",))->render() . $html[0];
        $html[0] .= view("utility.printer.print" . "._footer")->render();

        $print_file = null;

        $file_name=rand(1000,2000) . ".pdf";
        $print_file = PrinterFile::create([
            "user_id" => $worker->id,
            "filename" => $file_name,
            "status_id" => 305001, // در انتظار دانلود
            "is_landscape" =>  0,
            "printer_id" => $printer->id,
            "number_of_prints" =>1
        ]);
        $result = ["html" => $html, "print_file" => $print_file];

        $packing_type_label_printing_type = PackingTypeLabelPrintingType::where("id", ">", 0)->first();
        Pdf::labelPrinter($result["html"],
            $packing_type_label_printing_type->orientation,
            rand(1000, 20000), [
                $packing_type_label_printing_type->width,
                $packing_type_label_printing_type->long
            ],
            $result["print_file"]);


        return back()->with(["success" => "یک پرینت تست برای پرینتر ارسال گردید."]);
    }
}
