<?php

namespace App\Http\Controllers\Contractor\Panel;

use App\Http\Controllers\Controller;
use App\Models\Contractor\ContractorAllocation;
use App\Models\Form\Form;
use App\Models\Form\Packing\PackingForm;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormForm;
use Illuminate\Http\Request;

class ConfirmationOfReceiptOfProductController extends Controller {
    public static $info = [
        "route"         => "contractor.panel.confirmation_of_receipt_of_product.",
        "view"          => "contractor.panel.confirmation_of_receipt_of_product.",
        "enable_status" => [ "108" ],
        "button"        => [ "caption" => "تایید دریافت مواد اولیه", "class" => "btn-primary" ],

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "contractor.panel.dashboard.";

    //
    public function __construct() {
        $this->route_path = ConfirmationOfReceiptOfProductController::$info["route"];
        $this->view_path  = ConfirmationOfReceiptOfProductController::$info["view"];
    }

    public function index( ContractorAllocation $contractor_allocation, ProductRequestFormForm $product_request_form_form, Form $form ) {

        $result = $this->checkPermission( $contractor_allocation );
        if ( $result != "" ) {
            return $result;
        }

        if ( $form->status_id == 500000200 ) { // تایید شده
            return back()->withErrors( "این فرم قبلا تایید شده است." );
        }
        if ( $form->status_id != 500000500 ) { // تایید درخواست کننده
            return back()->withErrors( "وضعیت فرم در انتظار تایید درخواست کننده نمی باشد، " );
        }

        $contractor = $contractor_allocation->contractor;

        $packing_form = [];
        foreach ( $form->item as $item ) {
            $packing_form[ $item->packing_form_item->packing_form->id ] = 1;
        }

        return view( $this->view_path . "index", compact( "contractor_allocation", "contractor", "product_request_form_form", "packing_form", "form" ) );
    }

    public function submit( Request $request, ContractorAllocation $contractor_allocation, ProductRequestFormForm $product_request_form_form, Form $form ) {

        $result = $this->checkPermission( $contractor_allocation );
        if ( $result != "" ) {
            return $result;
        }

        if ( $form->status_id == 500000200 ) { // تایید شده
            return back()->withErrors( "این فرم قبلا تایید شده است." );
        }
        if ( $form->status_id != 500000500 ) { // تایید درخواست کننده
            return back()->withErrors( "وضعیت فرم در انتظار تایید درخواست کننده نمی باشد، " );
        }
        $packing_form_list = [];
        foreach ( $form->item as $item ) {
            $packing_form_list[ $item->packing_form_item->packing_form->id ] = 1;
        }
        $packing_form_key = array_keys( $packing_form_list );

        $packing_forms       = PackingForm::whereIn( "id", $packing_form_key )->get();
        $packing_and_carrier = [];
        $submit_codes        = [];

        // چک کردن حامل های وارد شده
        foreach ( $packing_forms as $item ) {
            $packing_and_carrier[ $item->getCodeNumber() ]    = 1;
            $packing_and_carrier[ $item->carrier->code ?? 0 ] = 1;

            $id             = "packing_" . $item->id;
            $submit_codes[] = $request->$id;
        }

        if ( count( $submit_codes ) == 0 ) {
            return back()->withErrors( "لطفا حداقل یک کد بسته بندی / حامل را وارد نمایید." );
        }

        $confirm = true;
        foreach ( $submit_codes as $key => $item ) {

            if ( ! in_array( $item, array_keys( $packing_and_carrier ) ) ) {
                $confirm = false;
            }
        }

        if ( $confirm == false ) {
            return back()->withErrors( "کدهای بسته بندی/حامل ها به درستی وارد نشده است." );
        }

        // در صورت مجاز بودن فرم تایید و تراکنش انبار ثبت شود.
        $result = $product_request_form_form->product_request_form->checkIfValidConfirmRequest( $form->id );

        // در این تابع وضعیت جدید فرم ثبت می شود.
        $product_request_form_form->product_request_form->updateExistFormStatusForm( $form, 7005002 ); // تایید دریافت کالا

        if ( $result["result"] ) {
            return redirect()->route( $this->dashboard_route . "view", $contractor_allocation )->with( [ "success" => "فرم با موفقیت تایید شد." ] );
        } else {
            return back()->withErrors( $result["error"] );
        }
    }

    public function checkPermission( ContractorAllocation $contractor_allocation ) {

        $result = DashboardController::checkPermissionConditions( $contractor_allocation, ConfirmationOfReceiptOfProductController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}
