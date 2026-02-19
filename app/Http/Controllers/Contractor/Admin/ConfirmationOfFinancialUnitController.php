<?php

namespace App\Http\Controllers\Contractor\Admin;

use App\Events\Product\ProductRequestFormLogEvent;
use App\Events\Warehouse\Form\FormLogEvent;
use App\Http\Controllers\Controller;
use App\Models\Contractor\Contractor;
use App\Models\Form\Form;
use App\Models\Form\Packing\PackingForm;
use App\Models\LineProduct\Machine\MachineAllocation;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormForm;
use App\Models\Order\Order;
use Illuminate\Http\Request;

class ConfirmationOfFinancialUnitController extends Controller {
    public static $info = [
        "route" => "contractor.admin.confirmation_of_financial_unit.",
        "view"  => "contractor.admin..confirmation_of_financial_unit.",

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route="contractor.admin.dashboard.log";

    //
    public function __construct() {
        $this->route_path = ConfirmationOfFinancialUnitController::$info["route"];
        $this->view_path  = ConfirmationOfFinancialUnitController::$info["view"];
    }

    public function index( MachineAllocation  $machine_allocation, Form $form ) {

        $result = $this->checkPermission();
        if ( $result != "" ) {
            return $result;
        }

        $packing_form = [];
        foreach ( $form->item as $item ) {
            $packing_form[ $item->packing_form_item->packing_form->id ] = 1;
        }

        return view( $this->view_path . "index", compact( "machine_allocation", "form", "packing_form" ) );

    }

    public function confirm_exist_form( Request $request,MachineAllocation  $machine_allocation, Form $form ) {

        $result = $this->checkPermission(  );
        if ( $result != "" ) {
            return $result;
        }

        if ( $form->status_id != 500000520 ) { // در انتظار تایید واحد مالی
            return back()->withErrors( "این برگ خروج قبلا تایید شده است." );
        }

        $product_request_form_form = ProductRequestFormForm::join( "product_request_forms", "product_request_forms.id", "product_request_form_id" )->
        where( "product_request_form_form.form_id", $form->id )->
        where( [ "applicant_type_id" => 20, "applicant_id" => $machine_allocation->contractor_id ] )->
        select( "product_request_form_form.*" )->
        first();

        if ( ! $product_request_form_form ) {
            return back()->withErrors( "این برگ خروج برای پیمانکار وجود ندارد، لطفا با پشتیبانی تماس بگیرید." );
        }

        $product_request_form_forms = ProductRequestFormForm::where(
            "form_id", $form->id
        )->get();

        if(count($product_request_form_forms) == 0){
            return  back()->withErrors("فرم درخواست مرتبط با این فرم یافت نشد.");
        }
        else{
            $product_request_form=$product_request_form_forms[0]->product_request_form;
        }

        // ارسال درخواست برای پیمانکارانی که سامانه دارند.
        $result_call_api_input = Contractor::CallApiAddInputFormForContractor($machine_allocation->contractor,$product_request_form,$form,null);
        if (!$result_call_api_input["result"]) {
            return back()->withErrors($result_call_api_input["error"]);
        }
        foreach ( $product_request_form_forms as $item ) {
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

    public function reject_exist_form( Request $request,  MachineAllocation $machine_allocation, Form $form ) {

        $result = $this->checkPermission(  );
        if ( $result != "" ) {
            return $result;
        }

        if ( $form->status_id != 500000520 ) { // در انتظار تایید واحد مالی
            return back()->withErrors( "این برگ خروج قبلا تایید شده است." );
        }

        $product_request_form_form = ProductRequestFormForm::join( "product_request_forms", "product_request_forms.id", "product_request_form_id" )->
        where( "product_request_form_form.form_id", $form->id )->
        where( [ "applicant_type_id" => 20, "applicant_id" => $machine_allocation->contractor_id ] )->
        select( "product_request_form_form.*" )->
        first();

        if ( ! $product_request_form_form ) {
            return back()->withErrors( "این برگ خروج برای پیمانکار وجود ندارد، لطفا با پشتیبانی تماس بگیرید." );
        }


        $product_request_form_forms = ProductRequestFormForm::where(
            "form_id", $form->id
        )->get();




        $result = $product_request_form_form->product_request_form->rejectRequest( $form );
        if ( $result["result"] ) {

            foreach ( $product_request_form_forms as $item ) {
                event( new ProductRequestFormLogEvent( $item->product_request_form, "", $form->id, 7005011 ) );

            }

            return redirect()->route( $this->dashboard_route, $machine_allocation )->with( [ "success" => "عدم تایید برگ خروج با موفقیت ثبت گردید." ] );
        } else {
            return back()->withErrors( $result["error"] );
        }

    }


    public function checkPermission(){
        if ( ! \Auth::user()->posts->first()->checkButtonPermission( "contractor._confirm_financial_unit" ) ) {
            return back()->withErrors( "شما اجازه دسترسی به عملیات ( تایید نهایی برگ خروج از انبار برای پیمانکاران) مورد نظر را ندارید" );
        }
    }
}
