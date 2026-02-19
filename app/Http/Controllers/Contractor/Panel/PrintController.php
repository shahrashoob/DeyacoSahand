<?php

namespace App\Http\Controllers\Contractor\Panel;

use App\Http\Controllers\Controller;
use App\Models\Contractor\ContractorAllocation;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Packing\PackingTypeLabelPrintingType;
use App\Models\Utility\Pdf;
use App\Models\Utility\Printer\PrinterFile;
use App\Models\Utility\Setting;
use App\Models\Utility\Transport\Transport;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Exception\UnableToBuildUuidException;

class PrintController extends Controller {
    //
    public static $info = [
        "route"         => "contractor.panel.print.",
        "view"          => "contractor.panel.print.",
        "enable_status" => [ "102", "103", "104", "105", "105", "106", "108" ],
        "button"        => [ "caption" => "پرینت گزارش", "class" => "btn-primary" ],

    ];


    public function report_1( ContractorAllocation $contractor_allocation ) {

        $result = $this->checkPermission( $contractor_allocation );
        if ( $result != "" ) {
            return $result;
        }

        $header_text = Setting::getStringValue( "transport_loading_header_text" );

        $date_time = jdate( Carbon::now()->timestamp )->format( 'H:i Y/m/d ' );
        $view_path = PrintController::$info["view"] . "print_report_1.";
        $html      = [];
        $html[0]   = view( $view_path . "_head" )->render();
        $html[0]   .= view( $view_path . "_print_transport", compact( "contractor_allocation", "header_text", "date_time" ) )->render() . $html[0];
        $html[0]   .= view( $view_path . "_footer" )->render();
        Pdf::createAsHtml( $html,
            "P",
            $contractor_allocation->production->serial(),
            "A4",
            " "
        );
    }

    public function allocation_card( ContractorAllocation $contractor_allocation ) {
        $result = $this->checkPermission( $contractor_allocation );
        if ( $result != "" ) {
            return $result;
        }
        $worker = Worker::find( Auth::id() );
        if ( ! $worker->default_printer_id ) {
            return redirect()->route( "utility.printer.select_default_printer" )->withErrors( "لطفا پرینتر پیش فرض را انتخاب نمایید." );
        }
        $software_name = Setting::getStringValue( "software_name" );
        $view_path     = PrintController::$info["view"] . "allocation_card.";
        $html          = [];
        $html[0]       = view( $view_path . "_head" )->render();
        $html[0]       .= view( $view_path . "_print_info", compact( "contractor_allocation", "software_name" ) )->render() . $html[0];
        $html[0]       .= view( $view_path . "_footer" )->render();

        $packing_type_label_printing_type = PackingTypeLabelPrintingType::find( 2 );

        $print_file = PrinterFile::create( [
            "user_id"      => $worker->id,
            "filename"     => "contractor_allocation" . $contractor_allocation->id . ".pdf",
            "status_id"    => 305001, // در انتظار دانلود
            "is_landscape" => $packing_type_label_printing_type->orientation == "L" ? 1 : 0,
            "printer_id"   => $worker->default_label_printer_id
        ] );
        Pdf::labelPrinter( $html,
            $packing_type_label_printing_type->orientation,
            "contractor_allocation" . $contractor_allocation->id, [
                $packing_type_label_printing_type->width,
                $packing_type_label_printing_type->long
            ],
            $print_file
        );

        return back()->with( [ "success" => "برای دریافت کارت دستور پیمان به محل لیبل پرینتر " . $worker->default_label_printer_id . " مراجعه فرمایید." ] );
    }

    public function download_transport_card( Transport $transport, $random ) {

        if ( $transport->random != $random ) {
            return back()->withErrors( "اطلاعات بار نادرست است." );
        }
        $result = \App\Http\Controllers\Utility\Transport\PrintQRController::create_pdf_file( $transport, "download" );
        Pdf::createAsHtml( $result["html"],
            "L",
            $transport->id, "A5"," "
        );
    }

    public function allocation_form(Allocation $allocation,PackingTypeLabelPrintingType $packing_type_label_printing_type )
    {

    }

    public function create_pdf_allocation_form( Allocation $allocation,$packing_type_label_printing_type , $worker, $type)
    {
        $static_ip = "https://deyaco.ir/" . env("APP_NAME");
        $local_ip = url("");

        $url = route("contractor.panel.dashboard.", [$allocation->items()->first()]);

        $url = \Illuminate\Support\Str::replace($local_ip, $static_ip, $url);

        $software_name = Setting::getStringValue( "software_name" );
        $view_path     = PrintController::$info["view"] . "allocation_card.";
        $html          = [];
        $html[0]       = view( $view_path . "_head" )->render();
        $html[0]       .= view( $view_path . "_print_info", compact( "contractor_allocation", "software_name" ) )->render() . $html[0];
        $html[0]       .= view( $view_path . "_footer" )->render();

        $print_file = null;
        if ($type != "download") {
            $print_file = PrinterFile::create([
                "user_id" => $worker->id,
                "filename" => "allocation_".$allocation->id . ".pdf",
                "status_id" => 305001, // در انتظار دانلود
                "is_landscape" => $packing_type_label_printing_type->orientation == "L" ? 1 : 0,
                "printer_id" => $worker->default_printer_id,
                "number_of_prints" => $worker->default_print_number
            ]);
        }

        return ["html" => $html, "print_file" => $print_file];
    }
    public function checkPermission( ContractorAllocation $contractor_allocation ) {

        $result = DashboardController::checkPermissionConditions( $contractor_allocation, PrintController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}
