<?php

namespace App\Http\Controllers\QualityControl\RejectProduct;

use App\Events\Form\PackingLogEvent;
use App\Events\Product\RejectProductLogEvent;
use App\Http\Controllers\Controller;
use App\Models\Form\Packing\PackingForm;
use App\Models\Form\Packing\PackingFormItem;
use App\Models\LineProduct\Product\RejectProduct\RejectProductForm;
use App\Notifications\SMSNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class ConfirmQualityController extends Controller {
    //
    var $dashboard_path = "quality_control.dashboard.index";

    public function confirm_reject_product_form( RejectProductForm $reject_product_form ) {

        if ( $reject_product_form->status_id != 7009003 ) {
            return back()->withErrors( "وضعیت فرم مرجوعی جهت تایید نامعتبر است." );
        }

        if ( ! \Auth::user()->posts->first()->checkButtonPermission( "quality_control.reject_product.cheek_quality.index" ) ) {
            return back()->withErrors( "شما اجازه دسترسی به عملیات مورد نظر را ندارید" );
        }

        foreach ( $reject_product_form->items as $item ) {
            //اگر بسته سالم نیست، تغییر بسته بندی انجام شود.
            if ( ! $item->packing_is_safe ) {
                $new_packing_form             = $this->changePacking( $item->packing_form, $item->amount_remaining );
                $item->parent_package_form_id = $item->packing_form_id;
                $item->packing_form_id        = $new_packing_form->id;
                $item->save();
            }
        }

        $reject_product_form->status_id = 7009007; // در انتظار ارسال کالا (درخواست دهنده)
        $reject_product_form->save();

        // ارسال پیامک برای مشتری
        $template = "rejectproductformforcustomer";

        $worker = $reject_product_form->order->customer->user;

        $token   = $reject_product_form->getCode();
        $token2  = $reject_product_form->order->code();
        $token3  ="_APP_NAME_"."DCRG". $reject_product_form->id . "/" . $reject_product_form->getRandom();
        $token10 = $reject_product_form->order->customer->caption;
        $token20 = "";

        Notification::send(
            "00" . ( $worker->mobile_country->area_code ?? "98" ) . $worker->mobile,
            new SMSNotification( $template, $token, $token2, $token3, $token10, $token20 )
        );

        event( new RejectProductLogEvent( $reject_product_form, 7009002 ) ); // تایید کنترل کیفیت

        return redirect()->route( $this->dashboard_path )->with( [ "success" => "فرم مرجوعی با موفقیت تایید شد." ] );

    }

    public function changePacking( PackingForm $packing_form, $amount_remaining ) {

        $packing_form_used = PackingForm::create( [
            "packing_type_id"        => $packing_form->packing_type_id,
            "carrier_id"             => $packing_form->carrier_id,
            "status_id"              => $packing_form->status_id,
            "packing_form_parent_id" => $packing_form->id,
        ] );

        event( new PackingLogEvent( $packing_form_used, "7007001" ) );


        $packing_form_remaining = PackingForm::create( [
            "packing_type_id"        => $packing_form->packing_type_id,
            "carrier_id"             => $packing_form->carrier_id,
            "status_id"              => $packing_form->status_id,
            "packing_form_parent_id" => $packing_form->id,
        ] );

        event( new PackingLogEvent( $packing_form_remaining, "7007001" ) );

        $amount_used = 0;
        $amount_all  = $packing_form->getFinalAmount(-1);
        foreach ( $packing_form->items()->orderByDesc( "id" )->get() as $packing_from_item ) {
            $amount_final_item_used      = min( $packing_from_item->final_amount, $amount_all - $amount_remaining + $amount_used );
            $amount_final_item_remaining = $packing_from_item->final_amount - $amount_final_item_used;
            if ( $amount_final_item_used > 0 ) {
                $ini_sub_amount=$amount_final_item_used * $packing_from_item->sub_amount / $packing_from_item->final_amount;
                PackingFormItem::create( [
                    "packing_form_id"         => $packing_form_used->id,
                    "production_form_item_id" => $packing_from_item->production_form_item_id,
                    "product_id"              => $packing_from_item->product_id,
                    "lot_number_id"           => $packing_from_item->lot_number_id,
                    "degree_id"               => $packing_from_item->degree_id,
                    "amount"                  => $amount_final_item_used * $packing_from_item->amount / $packing_from_item->final_amount,
                    "amount_after_control"    => $amount_final_item_used * $packing_from_item->amount / $packing_from_item->amount_after_control,
                    "final_amount"            => $amount_final_item_used,
                    "sub_amount"              => $ini_sub_amount,
                    "init_sub_amount"              => $ini_sub_amount,
                    "status_id"               => $packing_from_item->status_id,
                    "band_code"               => $packing_from_item->band_code,
                ] );
            }

            if ( $amount_final_item_remaining > 0 ) {
                PackingFormItem::create( [
                    "packing_form_id"         => $packing_form_remaining->id,
                    "production_form_item_id" => $packing_from_item->production_form_item_id,
                    "product_id"              => $packing_from_item->product_id,
                    "lot_number_id"           => $packing_from_item->lot_number_id,
                    "degree_id"               => $packing_from_item->degree_id,
                    "amount"                  => $amount_final_item_remaining * $packing_from_item->amount / $packing_from_item->final_amount,
                    "amount_after_control"    => $amount_final_item_remaining * $packing_from_item->amount / $packing_from_item->amount_after_control,
                    "final_amount"            => $amount_final_item_remaining,
                    "sub_amount"              => $amount_final_item_remaining * $packing_from_item->sub_amount / $packing_from_item->final_amount,
                    "init_sub_amount"              => $amount_final_item_remaining * $packing_from_item->sub_amount / $packing_from_item->final_amount,
                    "status_id"               => $packing_from_item->status_id,
                    "band_code"               => $packing_from_item->band_code,
                ] );
            }
        }


        $packing_form->status_id = 7007007; //تغییر یافته
        $packing_form->save();
        event( new PackingLogEvent( $packing_form, "7007008" ) );


        return $packing_form_remaining;
    }

}
