<?php

namespace App\Http\Controllers\Garding;

use App\Events\Form\PackingLogEvent;
use App\Events\Product\RejectProductLogEvent;
use App\Events\Warehouse\Form\FormLogEvent;
use App\Http\Controllers\Controller;
use App\Models\Form\Form;
use App\Models\Form\FormItem;
use App\Models\LineProduct\Product\RejectProduct\RejectProductForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RejectProductController extends Controller {
    var $view_path = "guarding.reject_product.";
    var $route_path = "guarding.reject_product.";

    public function index() {

        $list = RejectProductForm::whereIn( "status_id", [ 7009004 ] )->paginate();

        return view( $this->view_path . "index", compact( "list" ) );

    }

    public function view( RejectProductForm $reject_product_form ) {

        return view( $this->view_path . "view", compact( "reject_product_form" ) );

    }

    public function confirm_form( Request $request, RejectProductForm $reject_product_form ) {


        if ( $reject_product_form->status_id != 7009004 ) { // در انتظار تایید نگهبانی
            return back()->withErrors( "این فرم قبلا تایید شده است." );
        }

        if ( ! \Auth::user()->posts->first()->checkButtonPermission( "guarding.reject_product.confirm_from" ) ) {
            return back()->withErrors( "شما اجازه دسترسی به عملیات مورد نظر را ندارید" );
        }

        $warehouse_id = 0;
        foreach ( $reject_product_form->items as $reject_product_form_item ) {
            foreach ( $reject_product_form_item->packing_form->items as $item ) {
                if ( ! $item->degree->warehouse ) {
                    return back()->withErrors( "انبار مرتبط با درجه کالا یافت نشد، لطفا با پشتیبانی تماس بگیرد." );
                }
                if ( $warehouse_id != 0 && $warehouse_id != $item->degree->warehouse_id ) {
                    return back()->withErrors( "از آنجایی که درجه های کالاو انبار هر درجه متفاوت است، امکان تشخیص انبار ورودی وجود ندارد، لطفا با پشیتبانی تماس بگیرید." );
                }
                $warehouse_id = $item->degree->warehouse_id;
            }
        }


        //ایجاد فرم ورود به انبار
        $form = Form::CreateFrom( [
            "order_id"           => 0,
            "order_list_id"      => 0,
            "production_card_id" => 0,
            "user_id"            => Auth::user()->id,
            "form_type_id"       => 306,
            "trans_kind"         => 30, // برگشت از خروج فروش (دوره قبل)
            "warehouse_id"       => $warehouse_id,
            "status_id"          => 500000410, // در انتظار تایید انبار
            "ic"                 => $reject_product_form->applicant->getIC()
        ] );
        $form->getCode();

        foreach ( $reject_product_form->items as $reject_product_form_item ) {
            foreach ( $reject_product_form_item->packing_form->items as $item ) {
                FormItem::create( [
                    "form_id"              => $form->id,
                    "packing_form_item_id" => $item->id,
                    "packing_type_id"      => $reject_product_form_item->packing_form->packing_type_id,
                    "product_id"           => $item->product_id,
                    "amount"               => $item->final_amount,
                    "sub_amount"           => $item->sub_amount,
                    "carrier_id"           => $reject_product_form_item->packing_form->carrier_id,
                    "degree_id"            => $item->degree_id,
                    "lot_number_id"        => $item->lot_number_id,
                    "description"          => "مرجوع نمودن کالا با بسته بندی " . ( $item->code ?? "" ) . " به علت " . $reject_product_form->reject_product_reason_type->caption
                ] );
                $item->status_id = 500000410; //  در انتظار تایید انبار
                $item->save();
            }
        }

        event( new FormLogEvent( $form ) );

        foreach ( $reject_product_form->items as $reject_product_form_item ) {
            $reject_product_form_item->packing_form->warehouse_status_id = 4204;// بسته  در مسیر تحویل به انبار است
            $reject_product_form_item->packing_form->status_id = 7007002; //  در انتظار تایید انبار
            $reject_product_form_item->packing_form->save();
            event( new PackingLogEvent( $reject_product_form_item->packing_form, 7007014 ) ); // ورود محموله به کارخانه

            // تغییر وضعیت حامل
            if ( $reject_product_form_item->packing_form->carrier ) {
                $reject_product_form_item->packing_form->carrier->SetStatus( 5320007, null, 5320107, null, null ); //تحویل به انبار
            }
        }


        $reject_product_form->status_id = 7009005; // در انتظار بررسی کنترل کیفیت
        $reject_product_form->input_form_id =$form->id;
        $reject_product_form->save();
        event( new RejectProductLogEvent( $reject_product_form, 7009003 ) ); // تایید نگهبانی


        return back()->with( [ "success" => "فرم مرجوعی  با موفقیت تایید شد." ] );

    }
}
