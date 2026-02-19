<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Dobby\ProductionForm;

use App\Events\ProductionForm\ProductionFormLogEvent;
use App\Http\Controllers\Controller;
use App\Models\GoodsKindProcess\Fabric_Raw\FabricRaw;
use App\Models\GoodsKindProcess\Fabric_Raw\FabricRawGrading;
use App\Models\Production\ProductionFormItem;
use App\Models\Utility\Option;
use App\Models\Utility\Setting;
use Illuminate\Http\Request;

class GradingController extends Controller {
    public static $info = [
        "route"                              => "fabric_raw.production_form.grading.",
        "enable_status"                      => [ "003" ],
        "production_form_item_enable_status" => [ "003", "0" ],
        "button"                             => [ "caption" => "ثبت درجه بندی", "class" => "btn-primary" ],
        "view_path"                          => "goods_kind_process.fabric_raw.production_form.grading.",

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric_raw.production_form.dashboard.";
    var $session_name = "production_form_item_amount_";

    public function __construct() {
        $this->route_path = GradingController::$info["route"];
        $this->view_path  = GradingController::$info["view_path"];
    }

    public function index( ProductionFormItem $production_form_item ) {

        $result = $this->checkPermission( $production_form_item );
        if ( $result != "" ) {
            return $result;
        }
        if ( $production_form_item->lot_numbers->count() == 0 ) {
            return back()->withErrors( "برای فرم تولید هیچ همبافتی یافت نشد." );
        }

        $production_form = $production_form_item->production_form;

        if ( $production_form_item->status_id == 7002004 ) {
            return back()->withErrors( "این باند قبلا درجه بندی شده است، لطفا باندهای دیگر را درجه بندی کنید" );
        }

        return view( $this->view_path . "index", compact( "production_form_item", "production_form" ) );

    }

    public function submit( Request $request, ProductionFormItem $production_form_item ) {

        $result = $this->checkPermission( $production_form_item );
        if ( $result != "" ) {
            return $result;
        }

        $amount_after_control_list = $request->data["lot_number"];

        $grading_status = FabricRawGrading::where( [ "production_form_item_id" => $production_form_item->id ] )->first();
        if ( isset( $grading_status ) && $grading_status->status_id == 7006004 ) {
            return back()->withErrors( "این فرم قبلا درجه بندی شده است و امکان تغییر در آن وجود ندارد." );
        }

        foreach ( $amount_after_control_list as $amount ) {
            if ( $amount < .01 ) {
                return back()->withErrors( "لطفا عدد معتبر برای متراژ پارچه وارد نمایید." );
            }
        }

        session( [
            $this->session_name . $production_form_item->id => $amount_after_control_list
        ] );

        //حذف همه رکوردهای معلق درجه بندی
        FabricRawGrading::where( [
            "production_form_item_id" => $production_form_item->id,
            "status_id"               => 7006001
        ] )->delete();


        return redirect()->route( $this->route_path . "section", [ $production_form_item, 1 ] );
    }

    public function section( ProductionFormItem $production_form_item ) {
        $result = $this->checkPermission( $production_form_item );
        if ( $result != "" ) {
            return $result;
        }

        $latest_grading = FabricRawGrading::where( [ "production_form_item_id" => $production_form_item->id ] )->orderByDesc( "id" )->first();
        if ( isset( $latest_grading ) && $latest_grading->status_id == 7006004 ) {
            return back()->withErrors( "این فرم قبلا درجه بندی شده است و امکان تغییر در آن وجود ندارد." );
        }

        $amount_after_control_list = session( $this->session_name . $production_form_item->id );

        $amount_after_control = array_sum( $amount_after_control_list );
        // ایجاد یک رکورد در صورت جدید بودن
        // return $this->getLotStartRevers( 0, $amount_after_control_list );
        if ( ! isset( $latest_grading ) ) {
            FabricRawGrading::AddNewItem( $production_form_item, $latest_grading, $this->getLotStartRevers( 0, $amount_after_control_list ) );
        }


        $list_grading = FabricRawGrading::where( [ "production_form_item_id" => $production_form_item->id ] )->get();

        $degree_option = Option::get( "degree", 0, $production_form_item->product->goods_kind_id );

        $production_form = $production_form_item->production_form;


        return view( $this->view_path . "section", compact( "production_form_item", "production_form", "amount_after_control", "list_grading", "degree_option", "amount_after_control_list" ) );

    }

    public function submit_section( Request $request, ProductionFormItem $production_form_item ) {
        $result = $this->checkPermission( $production_form_item );
        if ( $result != "" ) {
            return $result;
        }
        $amount_after_control_list = session( $this->session_name . $production_form_item->id );
        $amount_after_control      = array_sum( $amount_after_control_list );

        if ( ! isset( $request->end_point ) || $request->end_point > $amount_after_control ) {
            return back()->withErrors( "مقدار پایانی نباید از متراژ کنترل خام بیشتر باشد." );
        }

        //آخرین رکورد
        $latest_grading = FabricRawGrading::where( [ "production_form_item_id" => $production_form_item->id ] )->orderByDesc( "id" )->first();
        if ( isset( $latest_grading ) && $latest_grading->status_id == 7006004 ) {
            return back()->withErrors( "این فرم قبلا درجه بندی شده است و امکان تغییر در آن وجود ندارد." );
        }

        if ( $request->end_point + 0 <= $latest_grading->start_point ) {
            return back()->withErrors( "متراژ پایانی باید از متراژ ابتدایی بیشتر باشد." );
        }

        if ( $latest_grading->lot_number_id != $this->getLotEndRevers( $request->end_point, $amount_after_control_list ) ) {
            return back()->withErrors( "متراژ وارد شده بیش از متراژ همبافت جاری است." );
        }

        $latest_grading->end_point            = $request->end_point;
        $latest_grading->degree_id            = $request->degree_id;
        $latest_grading->amount_after_control = $latest_grading->end_point - $latest_grading->start_point;
        $latest_grading->save();

        if ( $latest_grading->end_point != $amount_after_control ) {
            FabricRawGrading::AddNewItem( $production_form_item, $latest_grading, $this->getLotStartRevers( $latest_grading->end_point ?? 0, $amount_after_control_list ) );
        }

        return redirect()->route( $this->route_path . "section", $production_form_item );
    }

    public function end_of_section( ProductionFormItem $production_form_item ) {

        $result = $this->checkPermission( $production_form_item );
        if ( $result != "" ) {
            return $result;
        }

        $amount_after_control_list = session( $this->session_name . $production_form_item->id );
        $amount_after_control      = array_sum( $amount_after_control_list );


        //آخرین رکورد
        $latest_grading = FabricRawGrading::where( [ "production_form_item_id" => $production_form_item->id ] )->orderByDesc( "id" )->first();
        if ( isset( $latest_grading ) && $latest_grading->status_id == 7006004 ) {
            return back()->withErrors( "این فرم قبلا درجه بندی شده است و امکان تغییر در آن وجود ندارد." );
        }

        if ( $amount_after_control != $latest_grading->end_point ) {
            return redirect()->route()->withErrors( "درجه بندی با مشکل مواجه شده، لطفا یکبار دیگر امتحان کنید" );
        }

        // محاسبه درصد جمع شدگی
        $initial_shrinkage_percent =
            FabricRaw::getShrinkagePercent( $production_form_item->amount, $amount_after_control );

        if ( $initial_shrinkage_percent < 0 && Setting::getIntegerValue( "fabric_raw_checked_initial_shrinkage_percent" ) ) {
            return back()->withErrors( "   با توجه به متراژ، درصد جمع شدگی (" . $initial_shrinkage_percent . ") برای پارچه قابل قبول نمی باشد، لطفا با مدیریت واحد تماس بگیرید. " );
        }

        $fabric_row_grading_list = FabricRawGrading::
        where( [ "production_form_item_id" => $production_form_item->id ] )->
        get();
        foreach ( $fabric_row_grading_list as $item ) {
            $item->status_id = 7006002; // در انتظار بسته بندی
            $item->amount    = $item->amount_after_control / $amount_after_control * $production_form_item->amount;
            $item->initial_shrinkage_percent =FabricRaw::getShrinkagePercent($item->amount, $item->amount_after_control );

            $item->save();
        }


        $production_form_item->status_id                 = 7002004; // در انتظار ثبت حامل
        $production_form_item->amount_after_control      = $amount_after_control;
        $production_form_item->initial_shrinkage_percent = $initial_shrinkage_percent;
        $production_form_item->save();

        event( new ProductionFormLogEvent(
            $production_form_item->production_form,
            700202, // ثبت درجه بندی
            $production_form_item
        ) );


        // تغییر وضعیت فرم تولید
        $allow_change_status_production_form = true;
        foreach ( $production_form_item->production_form->items as $item ) {
            $allow_change_status_production_form =
                $allow_change_status_production_form && ( isset( $item->status_id ) && $item->status_id == 7002004 );
        }

        if ( $allow_change_status_production_form ) {
            // تغییر وضعیت فرم تولید
            $production_form_item->production_form->status_id = 7002004; //در انتظار ثبت حامل بسته بندی

            $production_form_item->production_form->save();
            event( new ProductionFormLogEvent(
                $production_form_item->production_form,
                700203 // پایان درجه بندی
            ) );
            // تغییر وضعیت حامل غلطک پارچه به به خالی
            $production_form_item->production_form->carrier->SetEmpty();

        }

        return redirect()->route( $this->dashboard_route . "view", $production_form_item->production_form )->with( [ "success" => "درجه با موفقیت ثبت گردید." ] );

    }

    public function delete_section( ProductionFormItem $production_form_item, FabricRawGrading $fabric_raw_grading ) {
        $result = $this->checkPermission( $production_form_item );
        if ( $result != "" ) {
            return $result;
        }

        //آخرین رکورد
        if ( $fabric_raw_grading->status_id == 7006004 ) {
            return back()->withErrors( "این فرم قبلا درجه بندی شده است و امکان تغییر در آن وجود ندارد." );
        }

        FabricRawGrading::
        where( [ "production_form_item_id" => $production_form_item->id ] )->
        where( "id", ">=", $fabric_raw_grading->id )->
        delete();

        $latest_grading            = FabricRawGrading::where( [ "production_form_item_id" => $production_form_item->id ] )->orderByDesc( "id" )->first();
        $amount_after_control_list = session( $this->session_name . $production_form_item->id );


        // ایجاد یک رکورد
        FabricRawGrading::AddNewItem( $production_form_item, $latest_grading, $this->getLotStartRevers( isset( $latest_grading->end_point ) ? $latest_grading->end_point : 0, $amount_after_control_list ) );

        return redirect()->route( $this->route_path . "section", $production_form_item )->with( [ "success" => "حذف با موفقیت انجام شد" ] );

    }

    public function checkPermission( ProductionFormItem $production_form_item ) {

        $result = DashboardController::checkPermissionConditions( $production_form_item->production_form, GradingController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }

    public function getLotStartRevers( $start, $list ) {
        $sum_list     = array_sum( $list );
        $start_range  = $sum_list;
        $end_range    = $sum_list;
        $lot_id_start = - 1;
        $start        = $sum_list - $start;

        $list_revers = array_values( $list );
        $key_revers  = ( array_keys( $list ) );
        $i           = 0;
        foreach ( $list as $key => $value0 ) {
            $start_range -= $list_revers[ $i ];
            if ( $start_range < $start && $start <= $end_range ) {
                $lot_id_start = $key_revers[ $i ];
            }

            $end_range -= $list_revers[ $i ];

            $i ++;
        }

        return $lot_id_start;

    }

    public function getLotEndRevers( $end, $list ) {
        $sum_list    = array_sum( $list );
        $start_range = $sum_list;
        $end_range   = $sum_list;
        $lot_id_end  = - 2;
        $end         = $sum_list - $end;

        $list_revers = array_values( $list );
        $key_revers  = ( array_keys( $list ) );
        $i           = 0;
        foreach ( $list as $key => $value0 ) {
            $start_range -= $list_revers[ $i ];

            if ( $start_range <= $end && $end < $end_range ) {
                $lot_id_end = $key_revers[ $i ];
            }

            $end_range -= $list_revers[ $i ];

            $i ++;
        }

        return $lot_id_end;

    }

}
