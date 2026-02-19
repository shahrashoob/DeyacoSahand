<?php

namespace App\Http\Controllers\Customer;

use App\Events\Order\OrderLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Warehouse\Out\ExitFormController;
use App\Models\Customer\AccountBalance;
use App\Models\Customer\Customer;
use App\Models\Form\Form;
use App\Models\LineProduct\Packing\PackingTypeLabelPrintingType;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestForm;
use App\Models\LineProduct\Product\RejectProduct\RejectProductForm;
use App\Models\Order\Order;
use App\Models\Utility\Pdf;
use App\Models\Utility\Setting;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller {
    //

    public function index() {

        $user_id  = \Auth::user()->id;
        $customer = Customer::findWidthUserId( $user_id );

        if ( ! $customer ) {
            return redirect()->route( "sales.customer.index" );
        }
        $list = Order::where( "customer_id", $customer->id )->orderByDesc( "created_at" )->paginate( 50 );

        return view( "customer.group.order.index", compact( "list", "customer" ) );
    }

    public function DCOF_SortLink( $order_code ) {

        $order = Order::where( "id", $order_code )->first();
        if ( $order ) {
            return $this->show( $order );
        }

        return back()->withErrors( "صفحه مورد نظر یافت نشد." );
    }

    public function show( Order $order ) {

        $result = $this->checkPermission( $order );
        if ( $result != "" ) {
            return $result;
        }
        $user_id  = \Auth::user()->id;
        $is_customer = Customer::where("user_id", \Auth::id())->exists();

        if ( $order->status_id == 304010 ) {
            return redirect()->back()->withErrors( "لطفا ابتدا سفارش را تایید نمایید." );
        }

        $show_confirm_btns = $order->status_id == 304030;
        $order             = $order->calculate();
        $form_list         = $order->getExitFormList();
        foreach ( $form_list as $item ) {
            $item->allow_reject_product = RejectProductController::allowFormReject( $order, $item )["result"];
        }

        $reject_product_form_list = RejectProductForm::where( "order_id", $order->id )->get();


        return view( "customer.group.order.show", compact( "order","is_customer", "show_confirm_btns", "form_list", "reject_product_form_list" ) );
    }

    public function download_form(Order $order, Form $form, PackingTypeLabelPrintingType $packing_type_label_printing_type, $print_type = "product")
    {
        $result = $this->checkPermission( $order );
        if ( $result != "" ) {
            return $result;
        }
        $this->checkPermission( $order );

        $worker = Worker::find(Auth::user()->id);

        $result = ExitFormController::create_pdf_file($form, $worker, "download", $packing_type_label_printing_type, 1, $print_type);

        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }

        Pdf::labelPrinter($result["html"],
            $packing_type_label_printing_type->orientation,
            $form->code, [
                $packing_type_label_printing_type->width,
                $packing_type_label_printing_type->long
            ]
        );


    }

    public function confirm( Request $request, Order $order ) {

        $result = $this->checkPermission( $order );
        if ( $result != "" ) {
            return $result;
        }
        if ( $order->status_id != 304030 ) {
            return redirect()->route( "customer_group.order.show", $order )->withErrors( "امکان تغییر در وضعیت درخواست وجود ندارد" );
        }

        $order->nextOrderPermission( $request->comment, $request->comment );

        return redirect()->route( "customer_group.order.index" )->with( [ "success" => "پیش فاکتور با موفقیت تایید شد." ] );
    }

    public function reject( Order $order ) {

        $result = $this->checkPermission( $order );
        if ( $result != "" ) {
            return $result;
        }

        if ( $order->status_id != 304030 ) {
            return back()->withErrors( "امکان تغییر در وضعیت درخواست وجود ندارد" );
        }
        $order->status_id = 35060;
        $order->save();
        event( new OrderLogEvent( $order, 35065 ) );

        return redirect()->route( "customer_group.order.index" )->with( [ "success" => "درخواست با موفقیت خاتمه یافته شد" ] );

    }

    public function log( Order $order ) {

        $result = $this->checkPermission( $order );
        if ( $result != "" ) {
            return $result;
        }

        return view( "customer.group.order.log", compact( "order" ) );
    }

    public function view_form( Order $order, Form $form ) {

        $result = $this->checkPermission( $order );
        if ( $result != "" ) {
            return $result;
        }

        $user_id  = \Auth::user()->id;
        $customer = Customer::findWidthUserId( $user_id );

        if ( ! $customer || $customer->id != $order->customer_id ) {
            return redirect()->back()->withErrors( "شما اجازه دسترسی به این سفارش را ندارید." );
        }

        $result = RejectProductController::allowFormReject( $order, $form );
        if ( $result["result"] ) { // تایید شده
            return redirect()->route( "customer_group.order.reject_product.index", [ $order, $form ] );
        }


        return view( "customer.group.order.view_form", compact( "form", "order" ) );
    }

    public function view_product_request_form( Order $order, ProductRequestForm $product_request_form ) {

        $result = $this->checkPermission( $order );
        if ( $result != "" ) {
            return $result;
        }

        $user_id  = \Auth::user()->id;
        $customer = Customer::findWidthUserId( $user_id );

        if ( ! $customer || $customer->id != $order->customer_id ) {
            return redirect()->back()->withErrors( "شما اجازه دسترسی به این سفارش را ندارید." );
        }

        return view( "customer.group.order.view_product_request_form", compact( "product_request_form", "order" ) );
    }

    public function view_reject_product_form( Order $order, RejectProductForm $reject_product_form ) {

        $result = $this->checkPermission( $order );
        if ( $result != "" ) {
            return $result;
        }

        $user_id  = \Auth::user()->id;
        $customer = Customer::findWidthUserId( $user_id );

        if ( ! $customer || $customer->id != $order->customer_id ) {
            return redirect()->back()->withErrors( "شما اجازه دسترسی به این سفارش را ندارید." );
        }

        $reject_product_in_send_product = Setting::getStringValue( "reject_product_in_send_product" );

        return view( "customer.group.order.view_reject_product_form", compact( "reject_product_in_send_product", "reject_product_form", "order" ) );

    }


    public function checkPermission( $order ) {

        $worker    = Worker::find( \Auth::user()->id );
        $customer  = Customer::where( "user_id", $worker->id )->first();

        // باید مشتری باشد.
        if ( ! $customer ) {
            return back()->withErrors( "صفحه مورد نظر یافت نشد" );
        }
        if ( $order->customer_id != ( $customer->id ?? 0 ) ) {

            return redirect()->route( "customer_group.order.index" )->withErrors( "سفارش مورد نظر یافت نشد." );
        }


    }
}
