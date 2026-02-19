<?php

namespace App\Http\Controllers\Customer;

use App\Events\Form\PackingLogEvent;
use App\Events\Order\OrderLogEvent;
use App\Events\Product\ProductRequestFormLogEvent;
use App\Events\Warehouse\Form\FormLogEvent;
use App\Events\Warehouse\PutInWarehouseEvent;
use App\Http\Controllers\Controller;
use App\Models\Customer\Customer;
use App\Models\Form\Form;
use App\Models\Form\FormItem;
use App\Models\Form\Packing\PackingForm;
use App\Models\Form\Packing\PackingFormItem;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormForm;
use App\Models\Order\Order;
use App\Models\Warehouse\WarehouseProduct;
use App\Models\Worker;
use Illuminate\Http\Request;

class ConfirmationOrReceiptOfProductController extends Controller {
    // تایید برگ خروج
    public static $info = [
        "route" => "customer_group.confirmation_of_receipt_of_product.",
        "view"  => "customer.group.confirmation_of_receipt_of_product.",

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "customer_group.order.";

    //
    public function __construct() {
        $this->route_path = ConfirmationOrReceiptOfProductController::$info["route"];
        $this->view_path  = ConfirmationOrReceiptOfProductController::$info["view"];
    }

    public function index( Order $order, Form $form ) {

        $result = $this->checkPermission( $order );
        if ( $result != "" ) {
            return $result;
        }

        if ( $form->status_id == 500000200 ) { // تایید شده
            return back()->withErrors( "این فرم قبلا تایید شده است." );
        }

        $checking_carrier_at_delivery_of_product_customer = $order->customer->checking_carrier_at_delivery_of_product;

        $packing_form_list = [];
        if ( $checking_carrier_at_delivery_of_product_customer ) {
            $packing_form_item_ids = FormItem::where( "form_id", $form->id )->pluck( "packing_form_item_id" )->toArray();

            $packing_form_ids = PackingFormItem::whereIn( "id", $packing_form_item_ids )->pluck( "packing_form_id" )->toArray();

            $packing_form_list = PackingForm::whereIn( "id", $packing_form_ids )->orderByDesc( "updated_at" )->get();
        }

        return view( $this->view_path . "index", compact( "order", "form", "packing_form_list", "checking_carrier_at_delivery_of_product_customer" ) );

    }

    public function confirm_exist_form( Request $request, Order $order, Form $form ) {

        // اگر وضعیت همه بسته بندی ها تحویل شده به مشتری باشد، فرم تایید می شود در غیر اینصورت فرم در همان وضعیت می ماند.

        $result = $this->checkPermission( $order );
        if ( $result != "" ) {
            return $result;
        }

        if ( $form->status_id == 500000200 ) { // تایید شده
            return back()->withErrors( "این فرم قبلا تایید شده است." );
        }
        if ( $form->status_id != 500000500 ) { // تایید درخواست کننده
            redirect()->route( $this->dashboard_route . "show", $order )->withErrors( "وضعیت فرم در انتظار تایید درخواست کننده نمی باشد، لطفا با واحد پشتیبانی تماس بگیرید." );
        }

        $product_request_form_form = ProductRequestFormForm::join( "product_request_forms", "product_request_forms.id", "product_request_form_id" )->
        where( "product_request_form_form.form_id", $form->id )->
        where( "order_id", $order->id )->first();

        if ( ! $product_request_form_form ) {
            return back()->withErrors( "این فرم برای سفارش وجود ندارد، لطفا با پشتیبانی تماس بگیرید." );
        }

        $checking_carrier_at_delivery_of_product_customer = $order->customer->checking_carrier_at_delivery_of_product;


        $packing_form_item_ids = FormItem::where( "form_id", $form->id )->pluck( "packing_form_item_id" )->toArray();

        $packing_form_ids = PackingFormItem::whereIn( "id", $packing_form_item_ids )->distinct( "packing_form_id" )->pluck( "packing_form_id" )->toArray();

        $packing_form_list = PackingForm::whereIn( "id", $packing_form_ids )->get();

        $confirm_packing_count = [];

        if ( $checking_carrier_at_delivery_of_product_customer ) {
            foreach ( $packing_form_list as $packing_form ) {

                $conform = false;
                // چک کردن کد حامل
                foreach ( $request->data["carrier_code"] as $carrier_code ) {

                    if ( $checking_carrier_at_delivery_of_product_customer && $packing_form->carrier && $packing_form->carrier->code == $carrier_code ) {
                        $conform                                    = true;
                        $confirm_packing_count[ $packing_form->id ] = 1;
                        $this->confirm_packing_form( $packing_form, $form );

                    }
                }

                if ( ! $conform ) {

                    // چک کردن کد بسته بندی
                    foreach ( $request->data["packing_form_code"] as $packing_form_code ) {

                        $checked =
                            ( $checking_carrier_at_delivery_of_product_customer )
                            ||
                            ( ! $checking_carrier_at_delivery_of_product_customer && isset( $request->data["check_box"][ $packing_form_code ] ) );

                        if ( $checked && $packing_form->getCode() == "DCPK/" . $packing_form_code ) {

                            $confirm_packing_count[ $packing_form->id ] = 1;
                            $this->confirm_packing_form( $packing_form, $form );
                        }
                    }
                }

            }
        } else {

            // نیازی به بررسی تیک بسته بندی ها نیست.
            foreach ( $packing_form_list as $packing_form ) {
                $this->confirm_packing_form( $packing_form, $form );
            }
        }

        $result = $this->change_form_status( $form, $packing_form_ids, $product_request_form_form->product_request_form, $order );

        if ( $result ) {
            return redirect()->route( $this->dashboard_route . "show", $order )->with( [ "success" => "فرم با موفقیت تایید شد." ] );
        } else {
            $packing_form_confirm_count = PackingForm::
            whereIn( "id", $packing_form_ids )->
            where( "status_id", 7007014 )->  // ثبت و تایید مشتری
            count();

            return back()->withErrors( "تا کنون " . $packing_form_confirm_count . " بسته بندی تایید شده است و لازم است تا دیگر بسته بندی ها را نیز تایید/عدم تایید کنید." );
        }
    }

    public function confirm_packing_form( PackingForm $packing_form, Form $form ) {
        PackingForm::ConfirmToApplicant( $packing_form, $form, 30 );

    }

    public function change_form_status( Form $form, $packing_form_ids, $product_request_form, $order ) {

        $result = $this->checkPermission( $order );
        if ( $result != "" ) {
            return $result;
        }

        $packing_form_confirm_count = PackingForm::
        whereIn( "id", $packing_form_ids )->
        where( "status_id", 7007014 )->  // ثبت و تایید مشتری
        count();

        if ( $packing_form_confirm_count == count( $packing_form_ids ) ) {


            // در صورت مجاز بودن فرم تایید و تراکنش انبار ثبت شود.
            $product_request_form->checkIfValidConfirmRequest( $form->id );

            $product_request_form->updateExistFormStatusForm( $form, 7005002 );


            return true;
        }

        return false;
    }

    public function checkPermission( $order ) {

        $worker   = Worker::find( \Auth::user()->id );
        $customer = Customer::where( "user_id", $worker->id )->first();

        // باید مشتری باشد.
        if ( ! $customer ) {
            return back()->withErrors( "صفحه مورد نظر یافت نشد" );
        }
        if ( $order->customer_id != ( $customer->id ?? 0 ) ) {

            return redirect()->route( "customer_group.order.index" )->withErrors( "سفارش مورد نظر یافت نشد." );
        }


    }
}
