<?php

namespace App\Http\Controllers\GoodsKindProcess\General\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Warehouse\ProductionWarehouse\DashboardController;
use App\Models\Form\FormItem;
use App\Models\Form\Packing\PackingForm;
use App\Models\GoodsKindProcess\Fabric_Raw\Fabric_Raw;
use App\Models\GoodsKindProcess\Warps\RequestForm\WarpsRequestForm;
use App\Models\GoodsKindProcess\Warps\Warps;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\CurrentMachineInput;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Product\BOM\BOMFaultIllegal;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestForm;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormForm;
use App\Models\Utility\Option;
use App\Models\Utility\Pdf;
use App\Models\Utility\Printer\PrinterFile;
use App\Models\Utility\Setting;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use function back;
use function event;
use function redirect;
use function view;

class GeneralAllocationCardController extends Controller
{
    // goods_kind_process/general/machine/allocation_card
    var $view_path = "goods_kind_process.general.machine.allocation_card.";
    var $route_path;
    var $dashboard_route;
    public function index(Machine $machine, $allocation_id = null)
    {
        if (isset($allocation_id)) {
            $allocation = Allocation::find($allocation_id);

        } else {
            $allocation = $machine->getCurrentAllocation();
            if (!$allocation) {
                return back()->withErrors("تخصیص جاری برای ماشین یافت نشد.");
            }

        }
        $company_name = Setting::getStringValue("company_name");
        $software_name = Setting::getStringValue("software_name");
        $product_fault_list=[];
        $machine_allocation=null;
        foreach ($allocation->items as $item) {
            $product = $item->product;
            $production = $item->production;
            $band_code = $item->band_code;

            $product_fault_list = BOMFaultIllegal::
            where("product_id", $production->parent_production->product_id ?? 0)->
                groupBy("product_fault_id")->
            get();

            if (!$machine_allocation) {
                $machine_allocation = $item;
            } elseif ($machine_allocation->production_id != $item->production_id) {
                if (!isset($machine_allocation->other_allocation_count)) {
                    $machine_allocation->other_allocation_count = 0;
                }

                // دیگر تخصیص های همراه کالا اگر کارت تولید آن متفاوت بود
                $machine_allocation->other_allocation_count++;
                $other = "other_allocation_" . $machine_allocation->other_allocation_count;
                $machine_allocation->$other = $machine_allocation;
            }
        }
        $current_input_list[$allocation->id]=
         CurrentMachineInput::
        where([
            "machine_id" => $machine->id,
            "allocation_id" => ($allocation->id ?? -1),
            "goods_kind_id" => 2
        ])->
        groupBy("input_line_code","material_id")->
        orderBy("input_line_code")->
        get();

        $route_path = $this->route_path;
        $dashboard_route=$this->dashboard_route;
        return view($this->view_path . "index", compact("allocation", "machine", "company_name", "software_name", "product", "production", "band_code", "machine_allocation", "current_input_list", "route_path","dashboard_route","product_fault_list"));
    }

    public function print(Machine $machine, Allocation $allocation)
    {

        $worker = Worker::find(Auth::user()->id);
        $result = $this->create_pdf_file($machine, $allocation, $worker, "print");
        Pdf::labelPrinter($result["html"], "L", $allocation->id . "_" . $machine->fullCaption(), [
            60,
            87
        ], $result["print_file"]);

        return back()->with(["success" => "جهت دریافت لیبل پرینت شده، به محل پرینتر شماره " . $worker->default_label_printer_id . " مراجعه فرمایید."]);
    }

    public function download(Machine $machine, Allocation $allocation)
    {

        $worker = Worker::find(Auth::user()->id);
        $result = $this->create_pdf_file($machine, $allocation, $worker, "print");
        return Pdf::labelPrinter($result["html"], "L", $allocation->id . "_" . $machine->fullCaption(), [
            60,
            87
        ]);

        return back()->with(["success" => "جهت دریافت لیبل پرینت شده، به محل پرینتر شماره " . $worker->default_label_printer_id . " مراجعه فرمایید."]);
    }

    public function create_pdf_file(Machine $machine, Allocation $allocation, Worker $worker, $type = "download")
    {
        $company_name = Setting::getStringValue("company_name");
        $software_name = Setting::getStringValue("software_name");


        $current_input_list = CurrentMachineInput::
        where([
            "machine_id" => $machine->id,
            "allocation_id" => ($allocation->id ?? -1),
            "goods_kind_id" => 2
        ])->
        orderBy("input_line_code")->
        get();

        $view_path="goods_kind_process.general.machine.allocation_card.";
        $html[0] = "";
        $k = 0;
        foreach ($allocation->items as $item) {
            $product = $item->product;
            $production = $item->production;
            $band_code = $item->band_code;
            $machine_allocation = $item;
            $product_fault_list = BOMFaultIllegal::
            where("product_id", $production->parent_production->product_id ?? 0)->
            groupBy("product_fault_id")->
            get();
            $html[$k] = view($view_path . "_band_info", compact("machine", "machine_allocation", "current_input_list", "allocation", "product", "band_code", "production", "software_name", "company_name","product_fault_list"))->render();

            $k++;
            break; // با توجه به انیکه به ازای هر باند کارت تخصیص های مشابه تولید می شود، بنابراین لازم نیست دوکارت تولید شود، یکی کافی است.
        }


        $html[0] = view($view_path . "_head")->render() . $html[0];
        $html[count($html) - 1] = $html[count($html) - 1] . view($view_path . "_footer")->render();


        $print_file = null;
        if ($type == "print") {
            $print_file = PrinterFile::create([
                "user_id" => $worker->id,
                "filename" => $allocation->id . "_" . $machine->fullCaption() . ".pdf",
                "status_id" => 305001, // در انتظار دانلود
                "is_landscape" => 0,
                "printer_id" => $worker->default_label_printer_id
            ]);
        }

        return [
            "html" => $html,
            "print_file" => $print_file
        ];

    }

}
