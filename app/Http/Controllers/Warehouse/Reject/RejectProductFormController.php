<?php

namespace App\Http\Controllers\Warehouse\Reject;

use App\Events\Product\RejectProductLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Garding\RejectProductController;
use App\Http\Controllers\Sales\ConfirmationOfDraftFormController;
use App\Http\Controllers\Sales\ConfirmationOfFinancialUnitController;
use App\Models\Form\Form;
use App\Models\Form\FormItem;
use App\Models\LineProduct\Packing\PackingTypeLabelPrintingType;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestForm;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormForm;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormItem;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormLog;
use App\Models\LineProduct\Product\RejectProduct\RejectProductForm;
use App\Models\Utility\Pdf;
use App\Models\Utility\Printer\Printer;
use App\Models\Utility\Printer\PrinterFile;
use App\Models\Utility\Setting;
use App\Models\Worker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\F;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RejectProductFormController extends Controller {

    public static $view_path = "warehouse.reject.reject_product_form.";
    public static $route_path = "wh.reject.reject_product_form.";

    public function DCRG_SortLink( RejectProductForm $reject_product_form, $key ) {

        if ( $key != $reject_product_form->random ) {
            return back()->withErrors( "آدرس صفحه مورد نظر معتبر نمی باشد." );
        }

        return view( RejectProductFormController::$view_path . "view", compact( "reject_product_form" ) );

    }

    public function confirm_form( Request $request, RejectProductForm $reject_product_form ) {

        switch ( $reject_product_form->status_id ) {
            case 7009004:
                $controller = new  RejectProductController();

                return $controller->confirm_form( $request, $reject_product_form );
                break;
        }

        return back()->withErrors( "تایید فرم با خطایی مواجه شده است، لطفا با پشتیبانی تماس بگیرید." );
    }


}
