<?php

namespace App\Http\Controllers\GoodsKindProcess\Fabric\SpecialProduction\Machine;

use App\Http\Controllers\Controller;
use App\Models\GoodsKindProcess\Fabric_Raw\FabricRaw;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\CurrentMachineInput;
use App\Models\LineProduct\Machine\Machine;
use App\Models\Utility\Pdf;
use App\Models\Utility\Printer\PrinterFile;
use App\Models\Utility\Setting;
use App\Models\Worker;
use AWS\CRT\Log;
use Illuminate\Support\Facades\Auth;
use function back;
use function view;

class AllocationCardController extends Controller {
    //
    public static $info = [
        "route"         => "fabric.special_production.machine.allocation_card.",
        "enable_status" => [
            "001",
            "002",
            "003",
            "004",
            "005",
            "006",
            "007",
            "008",
            "009",
            "010", ],
        "button"        => [ "caption" => " پرینت کارت تخصیص ", "class" => "btn-info" ],
        "view_path"     => "goods_kind_process.fabric.special_production.machine.allocation_card.",

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric.special_production.machine.dashboard.";

    public function __construct() {
        $this->route_path = AllocationCardController::$info["route"];
        $this->view_path  = AllocationCardController::$info["view_path"];
    }

    public function index( Machine $machine, $allocation_id = null ) {
        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }
        if ( isset( $allocation_id ) ) {
            $allocation = Allocation::find( $allocation_id );

        } else {
            $allocation = $machine->getCurrentAllocation();
            if ( ! $allocation ) {
                return back()->withErrors( "تخصیص جاری برای ماشین یافت نشد." );
            }

        }
        $company_name  = Setting::getStringValue( "company_name" );
        $software_name = Setting::getStringValue( "software_name" );
        foreach ( $allocation->items as $item ) {
            $product            = $item->product;
            $production         = $item->production;
            $band_code          = $item->band_code;
            $machine_allocation = $item;

            break; // با توجه به انیکه به ازای هر باند کارت تخصیص های مشابه تولید می شود، بنابراین لازم نیست دوکارت تولید شود، یکی کافی است.
        }
        $current_input_list = CurrentMachineInput::
        where( [
            "machine_id"    => $machine->id,
            "allocation_id" => ( $allocation->id ?? - 1 ),
            "goods_kind_id" => 5
        ] )->
        orderBy( "input_line_code" )->
        get();

        return  view($this->view_path."index",compact("allocation","machine","company_name","software_name","product","production","band_code","machine_allocation","current_input_list"));
    }

    public function print( Machine $machine, Allocation $allocation ) {

        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }

        $worker = Worker::find( Auth::user()->id );
        $result = $this->create_pdf_file( $machine, $allocation, $worker,"print" );
        Pdf::labelPrinter( $result["html"], "L", $allocation->id . "_" . $machine->fullCaption(), [
            60,
            87
        ], $result["print_file"] );



        return back()->with( [ "success" => "جهت دریافت لیبل پرینت شده، به محل پرینتر شماره " . $worker->default_label_printer_id . " مراجعه فرمایید." ] );
    }

    public function download( Machine $machine, Allocation $allocation ) {

        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }

        $worker = Worker::find( Auth::user()->id );
        $result = $this->create_pdf_file( $machine, $allocation, $worker,"print" );
      return  Pdf::labelPrinter( $result["html"], "L", $allocation->id . "_" . $machine->fullCaption(), [
            60,
            87
        ] );

        return back()->with( [ "success" => "جهت دریافت لیبل پرینت شده، به محل پرینتر شماره " . $worker->default_label_printer_id . " مراجعه فرمایید." ] );
    }

    public function create_pdf_file( Machine $machine, Allocation $allocation, Worker $worker, $type = "download" ) {
        $company_name  = Setting::getStringValue( "company_name" );
        $software_name = Setting::getStringValue( "software_name" );


        $current_input_list = CurrentMachineInput::
        where( [
            "machine_id"    => $machine->id,
            "allocation_id" => ( $allocation->id ?? - 1 ),
            "goods_kind_id" => 2
        ] )->
        orderBy( "input_line_code" )->
        get();


        $html[0] = "";
        $k       = 0;
        foreach ( $allocation->items as $item ) {
            $product            = $item->product;
            $production         = $item->production;
            $band_code          = $item->band_code;
            $machine_allocation = $item;
            $html[ $k ]         = view( $this->view_path . "_band_info", compact( "machine", "machine_allocation", "current_input_list", "allocation", "product", "band_code", "production", "software_name", "company_name" ) )->render();
            $k ++;
            break; // با توجه به انیکه به ازای هر باند کارت تخصیص های مشابه تولید می شود، بنابراین لازم نیست دوکارت تولید شود، یکی کافی است.
        }


        $html[0]                    = view( $this->view_path . "_head" )->render() . $html[0];
        $html[ count( $html ) - 1 ] = $html[ count( $html ) - 1 ] . view( $this->view_path . "_footer" )->render();


        $print_file = null;
        if ( $type == "print" ) {
            $print_file = PrinterFile::create( [
                "user_id"      => $worker->id,
                "filename"     => $allocation->id . "_" . $machine->fullCaption() . ".pdf",
                "status_id"    => 305001, // در انتظار دانلود
                "is_landscape" => 0,
                "printer_id"   => $worker->default_label_printer_id
            ] );
        }

        return [
            "html"       => $html,
            "print_file" => $print_file
        ];

    }

    public function checkPermission( Machine $machine ) {

        $result = DashboardController::checkPermissionConditions( $machine, AllocationCardController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}
