<?php

namespace App\Http\Controllers\Sales;;

use App\Events\Form\PackingLogEvent;
use App\Events\Order\OrderLogEvent;
use App\Events\Product\ProductRequestFormLogEvent;
use App\Events\Product\RejectProductLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Warehouse\Out\ExitFormController;
use App\Models\Customer\Customer;
use App\Models\Form\Form;
use App\Models\Form\FormItem;
use App\Models\Form\FormLog;
use App\Models\Form\Packing\PackingForm;
use App\Models\Form\Packing\PackingFormItem;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormForm;
use App\Models\LineProduct\Product\RejectProduct\RejectProductForm;
use App\Models\LineProduct\Product\RejectProduct\RejectProductFormItem;
use App\Models\LineProduct\Product\RejectProduct\RejectProductReasonType;
use App\Models\Order\Order;
use App\Models\Utility\Option;
use App\Models\Utility\Pdf;
use App\Models\Utility\Setting;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class RejectProductController extends Controller {
    // مرجوع کردن کالا
    public static $info = [
        "route" => "sales.dashboard.reject_product.",

    ];
    var $route_path;
    var $dashboard_route = "sales.dashboard.view_order";

    //
    public function __construct() {
        $this->route_path = RejectProductController::$info["route"];
    }

    public function index( Order $order, Form $form ) {

        $result = $this->checkPermission( $order );
        if ( $result != "" ) {
            return $result;
        }

        $result = \App\Http\Controllers\Customer\RejectProductController::allowFormReject( $order, $form );
        if ( ! $result["result"] ) { // تایید شده
            return redirect()->route( $this->dashboard_route , [ $order, $form ] )->withErrors($result["message"]);
        }


      return  \App\Http\Controllers\Customer\RejectProductController::GetIndex( $order, $form,$this->route_path,$this->dashboard_route );
    }

    public function step1( Request $request, Order $order, Form $form ) {

        $result = $this->checkPermission( $order );
        if ( $result != "" ) {
            return $result;
        }

        $result = \App\Http\Controllers\Customer\RejectProductController::allowFormReject( $order, $form );
        if ( ! $result["result"] ) { // تایید شده
            return back()->withErrors( $result["message"] );
        }

        return  \App\Http\Controllers\Customer\RejectProductController::PostStep1($request, $order, $form, $this->route_path, $this->dashboard_route);
    }

    public function confirm( Request $request, Order $order, Form $form ) {

        $result = $this->checkPermission( $order );
        if ( $result != "" ) {
            return $result;
        }

        $result = \App\Http\Controllers\Customer\RejectProductController::allowFormReject( $order, $form );
        if ( ! $result["result"] ) { // تایید شده
            return back()->withErrors( $result["message"] );
        }

        return \App\Http\Controllers\Customer\RejectProductController::PostConfirm($request, $order, $form, $this->route_path, $this->dashboard_route );


    }



    public function submit_send_product( Order $order, RejectProductForm $reject_product_form ) {

        $result = $this->checkPermission( $order );
        if ( $result != "" ) {
            return $result;
        }

        $reject_product_form->status_id = 7009004; // در انتظار ورود به کارخانه(نگهبانی)
        $reject_product_form->save();

        event( new RejectProductLogEvent( $reject_product_form, 7009005 ) ); // ارسال کالا (درخواست کننده)

        return back()->with( [ "success" => "ارسال کالا با موفقیت ثبت گردید." ] );

    }

    public function download_form( Order $order, RejectProductForm $reject_product_form ) {

        $result = $this->checkPermission( $order );
        if ( $result != "" ) {
            return $result;
        }

        $static_ip = Setting::getStringValue( "static_ip" );
        $local_ip  = url( "" );

        $url = route( "DCRG_SortLink", [ $reject_product_form, $reject_product_form->getRandom() ] );

        //اگر شرکت دارای ای پی بیرونی و ای پی لوکال باشد، لینک را بر روی ای پی بیرونی تنظیم می کنیم.
        if ( $static_ip != "" ) {
            $url = \Illuminate\Support\Str::replace( $local_ip, $static_ip, $url );
        }
        $qr            = QrCode::size( 100 )->generate( $url );
        $software_name = Setting::getStringValue( "software_name" );
        $html[0]       = view( $this->view_path . "print._head" )->render();
        $html[0]       .= view( $this->view_path . "print._print_info",
                compact( "reject_product_form", "order", "qr", "software_name" ) )->render() . $html[0];
        $html[0]       .= view( ExitFormController::$view_path . "print._footer" )->render();

        Pdf::createAsHtml( $html, "P", $reject_product_form->id, "A4", "" );


    }

    public function checkPermission( $order ) {

        if ( ! \Auth::user()->posts->first()->checkButtonPermission( "sales.reject_product.index" ) ) {
            return back()->withErrors( "شما اجازه دسترسی به عملیات مورد نظر را ندارید" );
        }


    }
}
