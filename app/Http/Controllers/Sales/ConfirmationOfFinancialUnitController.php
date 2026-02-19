<?php

namespace App\Http\Controllers\Sales;

use App\Events\Product\ProductRequestFormLogEvent;
use App\Events\Warehouse\Form\FormLogEvent;
use App\Http\Controllers\Controller;
use App\Models\Customer\Customer;
use App\Models\Form\Form;
use App\Models\Form\Packing\PackingForm;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormForm;
use App\Models\Order\Order;
use Illuminate\Http\Request;

/**
 * تایید نهایی مالی
 */
class ConfirmationOfFinancialUnitController extends Controller {
    public static $info = [
        "route" => "sales.confirmation_of_financial_unit.",
        "view"  => "sales.confirmation_of_financial_unit.",

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "sales.dashboard.";

    //
    public function __construct() {
        $this->route_path = ConfirmationOfFinancialUnitController::$info["route"];
        $this->view_path  = ConfirmationOfFinancialUnitController::$info["view"];
    }

    public function index( Order $order, Form $form ) {

        if ( ! \Auth::user()->posts->first()->checkButtonPermission( "sales._exist_form_list" ) ) {
            return redirect()->route("DCEF_QR",[$form,$form->getRandom()])->withErrors( "شما اجازه دسترسی به عملیات مورد نظر را ندارید" );
        }


        $packing_form = [];
        foreach ( $form->item as $item ) {
            $packing_form[ $item->packing_form_item->packing_form->id ] = 1;
        }

        return view( $this->view_path . "index", compact( "order", "form", "packing_form" ) );

    }

    public function confirm_exist_form( Request $request,Order  $order, Form $form ) {

        $result = $this->checkPermission( $order );
        if ( $result != "" ) {
            return $result;
        }

        if ( $form->status_id != 500000520 ) { // در انتظار تایید واحد مالی
            return back()->withErrors( "این برگ خروج قبلا تایید شده است." );
        }

        $product_request_form_form = ProductRequestFormForm::join( "product_request_forms", "product_request_forms.id", "product_request_form_id" )->
        where( "product_request_form_form.form_id", $form->id )->
        where( "order_id", $order->id )->first();

        if ( ! $product_request_form_form ) {
            return back()->withErrors( "این برگ خروج برای سفارش وجود ندارد، لطفا با پشتیبانی تماس بگیرید." );
        }

        $product_request_form_form = ProductRequestFormForm::where(
            "form_id", $form->id
        )->get();

        if(count($product_request_form_form) == 0){
            return  back()->withErrors("فرم درخواست مرتبط با این فرم یافت نشد.");
        }
        else{
            $product_request_form=$product_request_form_form[0]->product_request_form;
        }


        // ارسال درخواست برای مشتریانی که سامانه دارند.
        $result_call_api_input = Customer::CallApiAddInputFormForCustomer($order->customer,$product_request_form,$form,null);
        if (!$result_call_api_input["result"]) {
            return back()->withErrors($result_call_api_input["error"]);
        }

        foreach ( $product_request_form_form as $item ) {
            event( new ProductRequestFormLogEvent( $item->product_request_form, "", $form->id, 7005007 ) );

        }

        // در صورت مجاز بودن فرم تایید و تراکنش انبار ثبت شود.
        $result = $product_request_form->checkIfValidConfirmRequest( $form->id );

        // در این تابع وضعیت جدید فرم ثبت می شود.
        $product_request_form->updateExistFormStatusForm($form);

        if ( ! $result["result"] ) {
            return back()->withErrors( $request["error"] );
        }

        return back()->with( [ "success" => "برگ حروج از انبار با موفقیت تایید شد." ] );

    }

    public function reject_exist_form( Request $request, Order $order, Form $form ) {

        $result = $this->checkPermission( $order );
        if ( $result != "" ) {
            return $result;
        }

        if ( $form->status_id != 500000520 ) { // در انتظار تایید واحد مالی
            return back()->withErrors( "این برگ خروج قبلا تایید شده است." );
        }

        $product_request_form_form = ProductRequestFormForm::join( "product_request_forms", "product_request_forms.id", "product_request_form_id" )->
        where( "product_request_form_form.form_id", $form->id )->
        where( "order_id", $order->id )->
        first();

        if ( ! $product_request_form_form ) {
            return back()->withErrors( "این برگ خروج برای سفارش وجود ندارد، لطفا با پشتیبانی تماس بگیرید." );
        }


        $product_request_form_forms = ProductRequestFormForm::where(
            "form_id", $form->id
        )->get();


        $result = $product_request_form_form->product_request_form->rejectRequest( $form );
        if ( $result["result"] ) {

            foreach ( $product_request_form_forms as $item ) {
                event( new ProductRequestFormLogEvent( $item->product_request_form, "", $form->id, 7005011 ) );

            }
            return redirect()->route( "sales.dashboard.view_order", $order )->with( [ "success" => "عدم تایید فرم خروج با موفقیت ثبت گردید." ] );
        } else {
            return back()->withErrors( $result["error"] );
        }
        return back()->with( [ "success" => "برگ حروج از انبار با موفقیت تایید شد." ] );

    }


    public function checkPermission(){
        if ( ! \Auth::user()->posts->first()->checkButtonPermission( "sales._confirm_financial_unit" ) ) {
            return back()->withErrors( "شما اجازه دسترسی به عملیات (تایید نهایی برگ خروج از انبار  برای فروش) مورد نظر را ندارید" );
        }
    }
}
