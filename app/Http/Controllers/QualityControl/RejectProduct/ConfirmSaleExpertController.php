<?php

namespace App\Http\Controllers\QualityControl\RejectProduct;

use App\Events\Form\PackingLogEvent;
use App\Events\Product\RejectProductLogEvent;
use App\Models\Form\Packing\PackingForm;
use App\Models\Form\Packing\PackingFormItem;
use App\Models\LineProduct\Product\RejectProduct\RejectProductForm;
use App\Notifications\SMSNotification;
use Illuminate\Support\Facades\Notification;

/**
 *  تایید فرم مرجوعی توسط کارشناس فروش
 */
class ConfirmSaleExpertController
{
    var $dashboard_path = "quality_control.dashboard.index";

    public function confirm_reject_product_form( RejectProductForm $reject_product_form ) {

        if ( $reject_product_form->status_id != 7009008 ) {
            return back()->withErrors( "وضعیت فرم مرجوعی جهت تایید نامعتبر است." );
        }

        if ( ! \Auth::user()->posts->first()->checkButtonPermission( "quality_control.reject_product.confirm_sale_expert.confirm_reject_product_form" ) ) {
            return back()->withErrors( "شما اجازه دسترسی به عملیات مورد نظر را ندارید" );
        }


        $reject_product_form->status_id = 7009003; // در انتظار تایید کنترل کیفیت
        $reject_product_form->save();


        event( new RejectProductLogEvent( $reject_product_form, 7009006 ) ); // تایید کارشناس فروش

        return redirect()->route( $this->dashboard_path )->with( [ "success" => "فرم مرجوعی با موفقیت تایید شد." ] );

    }

}