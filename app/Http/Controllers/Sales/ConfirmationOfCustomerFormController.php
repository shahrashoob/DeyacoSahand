<?php

namespace App\Http\Controllers\Sales;

use App\Events\Order\OrderLogEvent;
use App\Events\Product\ProductRequestFormLogEvent;
use App\Events\Warehouse\Form\FormLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Warehouse\Out\ExitFormController;
use App\Models\Customer\Customer;
use App\Models\File\File;
use App\Models\Form\Form;
use App\Models\Form\Packing\PackingForm;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormForm;
use App\Models\Order\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * تایید برگ خروج از طرف مشتری
 */
class ConfirmationOfCustomerFormController extends Controller {
    public static $info = [
        "route" => "sales.confirmation_of_customer_form.",
        "view"  => "sales.confirmation_of_customer_form.",

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "sales.dashboard.";

    //
    public function __construct() {
        $this->route_path = self::$info["route"];
        $this->view_path  = self::$info["view"];
    }

    public function index( Order $order, Form $form ) {

        if ( ! \Auth::user()->posts->first()->checkButtonPermission( "sales._confirm_customer_form" ) ) {
            return redirect()->route( "DCEF_QR", [
                $form,
                $form->getRandom()
            ] )->withErrors( "شما اجازه دسترسی به عملیات مورد نظر را ندارید" );
        }


        $packing_form = [];
        foreach ( $form->item as $item ) {
            $packing_form[ $item->packing_form_item->packing_form->id ] = 1;
        }

        return view( $this->view_path . "index", compact( "order", "form", "packing_form" ) );

    }

    public function confirm_exist_form( Request $request, Order $order, Form $form ) {


        $result = $this->checkPermission( $order );
        if ( $result != "" ) {
            return $result;
        }

        if ( $form->status_id != 500000500 ) { // در انتظار تایید درخواست کننده
            return back()->withErrors( "این برگ خروج قبلا تایید شده است." );
        }

        $product_request_form_form = ProductRequestFormForm::join( "product_request_forms", "product_request_forms.id", "product_request_form_id" )->
        where( "product_request_form_form.form_id", $form->id )->
        when( $order, function ( $query ) use ( $order ) {
            return $query->where( "order_id", $order->id );
        } )->first();

        if ( ! $product_request_form_form ) {
            return back()->withErrors( "این برگ خروج برای سفارش وجود ندارد، لطفا با پشتیبانی تماس بگیرید." );
        }


        $product_request_form_forms_count = ProductRequestFormForm::where(
            "form_id", $form->id
        )->count();

        if($product_request_form_forms_count == 0){
            return  back()->withErrors("فرم درخواست مرتبط با این فرم یافت نشد.");
        }

        if (!isset($request->image_file)) {
            return back()->withErrors( "بارگذاری تصویر تاییدیه از طرف مشتری الزامی است." );

        }
        $file = File::uploadFile($request->file('image_file'), $form->id . "_" . rand(1000, 9000) . ".png", 110, "upload/sales/", true);

        $message="<a target='_block' href='".asset("upload/sales/".($file->filename??''))."' > تصویر تاییدیه از طرف مشتری</a>";

        $result = ExitFormController::ConfirmApplicant($form,$message);

        if ($result["result"]) {

            event(new FormLogEvent($form, $message, Auth::id()));


            return back()->with(["success" => "برگه خروج از انبار با موفقیت تایید شد."]);
        } else {
            return back()->withErrors($result["error"]);
        }


    }


    public function reject_exist_form( Request $request, Order $order, Form $form ) {

        $result = $this->checkPermission( $order );
        if ( $result != "" ) {
            return $result;
        }


        if ( $form->status_id != 500000500 ) { // در انتظار تایید درخواست کننده
            return back()->withErrors( "این برگ خروج قبلا تایید شده است." );
        }


        $product_request_form_form = ProductRequestFormForm::join( "product_request_forms", "product_request_forms.id", "product_request_form_id" )->
        where( "product_request_form_form.form_id", $form->id )->
        when( $order, function ( $queery ) use ( $order ) {
            return $queery->where( "order_id", $order->id );
        } )->first();

        if ( ! $product_request_form_form ) {
            return back()->withErrors( "این برگ خروج برای سفارش وجود ندارد، لطفا با پشتیبانی تماس بگیرید." );
        }
        $product_request_form_forms = ProductRequestFormForm::where(
            "form_id", $form->id
        )->get();

        if(count($product_request_form_forms) == 0){
            return  back()->withErrors("فرم درخواست مرتبط با این فرم یافت نشد.");
        }



        $result = $product_request_form_form->product_request_form->rejectRequest( $form );
        if ( $result["result"] ) {
            foreach ( $product_request_form_forms as $item ) {
                event( new ProductRequestFormLogEvent( $item->product_request_form, "", $form->id, 7005011 ) );

            }
            return redirect()->route( "sales.dashboard.view_order", $order )->with( [ "success" => "عدم تایید فرم خروج با موفقیت ثبت گردید." ] );
        } else {
            return back()->withErrors( $result["error"] );
        }


    }

    public function checkPermission(){
        if ( ! \Auth::user()->posts->first()->checkButtonPermission( "sales._confirm_customer_form" ) ) {
            return back()->withErrors( "شما اجازه دسترسی به عملیات (تایید وصول مطالبات برگ خروج از انبار برای فروش ) مورد نظر را ندارید" );
        }
    }
}
