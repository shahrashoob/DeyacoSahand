<?php

namespace App\Http\Controllers\QualityControl\InputForm;

use App\Events\Warehouse\Form\FormLogEvent;
use App\Http\Controllers\Controller;
use App\Models\Form\Form;
use Illuminate\Http\Request;

class ConfirmQualityController extends Controller {
    //
    var $dashboard_path = "quality_control.dashboard.index";
    public function confirm_input_form( Request $request, Form $form ) {


        if ( $form->status_id != 500000535 || $form->form_type_id != 304 ) { // در انتظار تایید کنترل کیفیت
            return back()->withErrors( "این فرم قبلا تایید شده است." );
        }

        if ( ! \Auth::user()->posts->first()->checkButtonPermission( "quality_control.reject_product.cheek_quality.index" ) ) {
            return back()->withErrors( "شما اجازه دسترسی به عملیات مورد نظر را ندارید" );
        }

        $form->status_id = Form::nextStatusForInputForm( $form );
        $form->save();

        event( new FormLogEvent( $form, "" ) );

        return back()->with( [ "success" => "فرم ورود با موفقیت تایید شد." ] );

    }

    public function reject_input_form( Request $request, Form $form ) {


        if ( $form->status_id != 500000535 || $form->form_type_id != 304 ) { // در انتظار تایید کنترل کیفیت
            return back()->withErrors( "این فرم قبلا تایید شده است." );
        }

        if ( ! \Auth::user()->posts->first()->checkButtonPermission( "quality_control.reject_product.cheek_quality.index" ) ) {
            return back()->withErrors( "شما اجازه دسترسی به عملیات مورد نظر را ندارید" );
        }

        $form->status_id = 500000100; // عدم تایید
        $form->save();

        event( new FormLogEvent( $form, "" ) );

        return back()->with( [ "success" => "عدم تایید فرم انبار با موفقیت ثبت گردید." ] );

    }
}
