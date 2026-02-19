<?php

namespace App\Http\Controllers\Supplier\Admin;

use App\Http\Controllers\Controller;
use App\Models\Form\FormGeneralItem;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\MachineAllocation;
use App\Models\Utility\Pdf;
use App\Models\Utility\Transport\Transport;
class PrinterController extends Controller
{
//
    var $view_path = "supplier.admin.print.";
    var $route_path = "supplier.admin.print.";
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
}

