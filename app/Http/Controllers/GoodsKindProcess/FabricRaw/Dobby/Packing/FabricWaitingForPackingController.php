<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Dobby\Packing;

use App\Events\Form\PackingLogEvent;
use App\Events\ProductionForm\ProductionFormLogEvent;
use App\Http\Controllers\Controller;
use App\Models\Form\Packing\PackingForm;
use App\Models\Form\Packing\PackingFormItem;
use App\Models\GoodsKindProcess\Fabric_Raw\FabricRaw;
use App\Models\GoodsKindProcess\Fabric_Raw\FabricRawGrading;
use App\Models\LineProduct\Carrier\Carrier;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\Post\PostStatus;
use App\Models\Utility\Option;
use App\Models\Utility\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use function back;
use function event;
use function redirect;
use function session;
use function view;

class FabricWaitingForPackingController extends Controller {
    //
    var $view_path = "goods_kind_process.fabric_raw.packing.fabric_waiting_for_packing.";
    var $route_path = "fabric_raw.packing.fabric_waiting_for_packing.";
    public static $info = [
        "route"         => "fabric_raw.packing.fabric_waiting_for_packing.",
        "enable_status" => [],
        "button"        => [ "caption" => "ثبت حامل", "class" => "btn-primary" ],
    ];

    public function index( Request $request ) {


        if ( $request->isMethod( 'post' ) ) {
            $search    = $request->search;
            $order_by  = $request->order_by;
            $status_id = $request->status_id;
            $degree_id = $request->degree_id;
        } else {
            $search    = session( "search_packing" );
            $status_id = session( "status_id_packing" );
            $order_by  = session( "order_by_packing" );
            $degree_id = session( "degree_id_packing" );
        }
        session( [
            "search_packing"    => $search,
            "order_by_packing"  => $order_by,
            "status_id_packing" => $status_id,
            "degree_id_packing" => $degree_id,
        ] );

        $allowed_status_ids = PostStatus::getAllowedStatus();
        FabricRawGrading::updateCode();

        $list = FabricRawGrading::
        join( "products", "products.id", "product_id" )->
        whereIn( "status_id", $allowed_status_ids )->
        when( $search != "", function ( $query ) use ( $search ) {
            return $query->where( function ( $query ) use ( $search ) {
                return $query->orWhere( "fabric_raw_grading.code", "like", "%" . $search . "%" )->
                orWhere( "products.code", "like", "%" . $search . "%" )->
                orWhere( "products.caption", "like", "%" . $search . "%" );
            } );

        } )->
        when( $order_by != "", function ( $query ) use ( $order_by ) {
            $order_by = Str::of( $order_by )->explode( "__" );

            return $query->orderBy( $order_by[0], $order_by[1] );
        } )->
        when( $degree_id != "", function ( $query ) use ( $degree_id ) {
            return $query->where( "degree_id", $degree_id );
        } )->
        when( $status_id != "", function ( $query ) use ( $status_id ) {
            return $query->where( "status_id", $status_id );
        } )->
        select( "fabric_raw_grading.id",
            "fabric_raw_grading.code", "product_id", "fabric_raw_grading.amount", "fabric_raw_grading.final_amount", "fabric_raw_grading.amount_after_control",
            "degree_id", "fabric_raw_grading.status_id", "packing_form_item_id",
            "lot_number_id" )->

        paginate( 50 );;


        $order_by_Option = Option::OrderBy( "waiting_packing", $order_by );
        $degree_Option   = Option::get( "degree", $degree_id, 4 ); // 3 => پارچه خام
        $status_Option   = Option::get( "status", $status_id, 7006 ); // 3 => وضعیت

        return view( $this->view_path . "index", compact( "list", "search", "order_by_Option", "degree_Option", "status_Option" ) );

    }

    public function view( FabricRawGrading $fabric_raw_grading ) {

        $allowed_status_ids = PostStatus::getAllowedStatus();
        if ( ! in_array( $fabric_raw_grading->status_id, $allowed_status_ids ) ) {//  درجه بندی شده
            return back()->withErrors( "شما مجوز مشاهده فرم را ندارید." );
        }

        $info = FabricWaitingForPackingController::$info;


        $packing_type_option = Option::get( "packing_type", 0, $fabric_raw_grading->product->goods_kind_id );

        return view( $this->view_path . "view", compact( "fabric_raw_grading", "info", "packing_type_option" ) );
    }

    public function submit( Request $request, FabricRawGrading $fabric_raw_grading ) {

        $allowed_status_ids = PostStatus::getAllowedStatus();
        if ( ! in_array( $fabric_raw_grading->status_id, $allowed_status_ids ) ) {//  درجه بندی شده
            return back()->withErrors( "شما مجوز مشاهده فرم را ندارید." );
        }
        $message_row  = "";
        $message      = "";
        $carrier_code = $request->carrier_id;

        // بررسی بسته بندی
        $packing_type = PackingType::find( $request->packing_type_id );
        if ( ! $packing_type ) {
            return back()->withErrors( "نوع بسته بندی معتبر نمی باشد." );
        }

        $first_layer = $packing_type->layers->where( "layer_code", 1 )->first();
        if ( ! $first_layer ) {
            return back()->withErrors( "تعریف نوع حامل در  بسته بندی معتبر نمی باشد، لطفا با پشتیبانی تماس بگیرید." );

        }

        $result = Carrier::firstOrCreate( $carrier_code, $first_layer->carrier_type_id, 5320001, null );
        if ( isset( $result["carrier"] ) && in_array( $result["carrier"]->status_id, [ 5320001, 5320006 ] ) ) {
            $carrier = $result["carrier"];
        } elseif ( ! $result["result"] ) {
            return redirect()->back()->withErrors( $result["message"] );
        }


        //آیا حامل دیگری هست که در حال تکمیل شدن باشد
        $last_packing_form = PackingForm::where( [
            "lot_number_id" => $fabric_raw_grading->lot_number_id,
            "product_id"    => $fabric_raw_grading->product_id,
            "degree_id"     => $fabric_raw_grading->degree_id,
            "status_id"     => 7007001
        ] )->
        where( "status_id", "!=", 7007004 )->
        where( "carrier_id", "!=", $carrier->id )->
        first();
        if ( isset( $last_packing_form ) ) {
            return redirect()->back()->withErrors(
                " پالت دیگری با شماره فرم  " . $last_packing_form->getCode() . " در حال تکمیل است، لطفا پالت قبلی را تعیین تکلیف نمایید. "
            );

        }

        $firstProductFromCarrier = $carrier->firstProductId();
        if (
            ! in_array( $carrier->status_id, [ 5320001, 5230006 ] ) ||
            ( $firstProductFromCarrier != $fabric_raw_grading->product_id && $firstProductFromCarrier != - 1 )
        ) {
            $message_row .= $carrier->getCaption() . " قبلا توسط کالای دیگری تکمیل شده است " . "</br/>";;
        }


        $packing_form = PackingForm::
        where( [ "carrier_id" => $carrier->id ] )->
        where( "status_id", "!=", 7007004 )->
        orderByDesc( "id" )->first();

        if ( isset( $packing_form ) ) {
            $message_row   = $carrier->getCaption() .
                             " قبلا با  " . $packing_form->product->caption;
            $product_error = false;
            $lat_error     = false;
            $degree_error  = false;
            if ( $packing_form->product_id != $fabric_raw_grading->product_id ) {
                $product_error = true;
            }
            if ( $packing_form->lot_number_id != $fabric_raw_grading->lot_number_id ) {
                $lat_error = " و لات \"" . ( $packing_form->lot_number->code ?? "***" ) . " \" ";
            }
            if ( $packing_form->degree_id != $fabric_raw_grading->degree_id ) {
                $degree_error = " و درجه  \"" . ( $packing_form->degree->caption ?? "***" ) . "\" ";
            }

            if ( $product_error || $lat_error || $degree_error ) {
                $message = $message_row . $lat_error . $degree_error;
                $message = $message . " تکمیل شده است. ";

            }
        }

        if ( $message != "" ) {
            return back()->withErrors( $message );
        }

        $carrier_not_allowed = PackingForm::where( "status_id", 7007002 )->pluck( "carrier_id" )->toArray();
        if ( in_array( $carrier->id, $carrier_not_allowed ) ) {
            return back()->withErrors( "این حامل در حال تحویل به انبار است و لطفا حامل دیگری را انتخاب کنید." );
        }

        // محاسبه درصد جمع شدگی یک آیتم
        $second_shrinkage_percent = FabricRaw::getShrinkagePercent(
            $fabric_raw_grading->amount_after_control,
            $request->final_amount );

        $final_shrinkage_percent = FabricRaw::getShrinkagePercent(
            $fabric_raw_grading->amount,
            $request->final_amount );

        if ( $second_shrinkage_percent < 0 && Setting::getIntegerValue( "fabric_raw_checked_second_shrinkage_percent" ) ) {
            return back()->withErrors( "   با توجه به متراژ، درصد جمع شدگی (" . $second_shrinkage_percent . ") برای پارچه قابل قبول نمی باشد، لطفا با مدیریت واحد تماس بگیرید. " );
        }
        if ( $final_shrinkage_percent < 0 && Setting::getIntegerValue( "fabric_raw_checked_second_shrinkage_percent" ) ) {
            return back()->withErrors( "   با توجه به متراژ، درصد جمع شدگی (" . $final_shrinkage_percent . ") برای پارچه قابل قبول نمی باشد، لطفا با مدیریت واحد تماس بگیرید. " );
        }


        $carrier->addProduct( $fabric_raw_grading->product_id );

        $packing_form = PackingForm::
        where( [ "carrier_id" => $carrier->id ] )->
        where( "status_id", "!=", 7007004 )->
        orderByDesc( "id" )->
        first();

        if ( ! isset( $packing_form ) ) {
            $packing_form = PackingForm::create( [
                "product_id"      => $fabric_raw_grading->product_id,
                "lot_number_id"   => $fabric_raw_grading->lot_number_id,
                "degree_id"       => $fabric_raw_grading->degree_id,
                "carrier_id"      => $carrier->id,
                "packing_type_id" => $packing_type->id,
                "status_id"       => 7007001, // در حال تکمیل
            ] );
            event( new PackingLogEvent( $packing_form, 7007001 ) );
        }

        $packing_form_item = PackingFormItem::create( [
            "packing_form_id"         => $packing_form->id,
            "production_form_item_id" => $fabric_raw_grading->production_form_item_id,
            "fabric_raw_grading_id"   => $fabric_raw_grading->id,
            "amount"                  => $fabric_raw_grading->amount,
            "amount_after_control"    => $fabric_raw_grading->amount_after_control,
            "final_amount"            => $request->final_amount,
            "sub_amount"              => 0,
            "init_sub_amount"              => 0,
        ] );

        $fabric_raw_grading->second_shrinkage_percent = $second_shrinkage_percent;
        $fabric_raw_grading->final_shrinkage_percent  = $final_shrinkage_percent;
        $fabric_raw_grading->final_amount             = $request->final_amount;
        $fabric_raw_grading->status_id                = 7006003;//شماره پالت ثبت شد
        $fabric_raw_grading->packing_form_item_id     = $packing_form_item->id;
        $fabric_raw_grading->save();


        // تغییر وضعیت آیتم فرم تولید
        $allow_change_status_production_form_item = true;
        $sum_final_amount                         = 0;
        foreach ( $fabric_raw_grading->production_form_item->fabric_grading as $item ) {
            $sum_final_amount                         += $item->final_amount;
            $allow_change_status_production_form_item =
                $allow_change_status_production_form_item && ( isset( $item->status_id ) && $item->status_id == 7006003 );
        }

        if ( $allow_change_status_production_form_item ) {

            // درصد جمع شدگی ثانویه برای باند
            $second_shrinkage_percent = FabricRaw::getShrinkagePercent(
                $fabric_raw_grading->production_form_item->amount_after_control,
                $sum_final_amount );
            // درصد جمع شدگی کلی برای باند
            $general_shrinkage_percent = FabricRaw::getShrinkagePercent(
                $fabric_raw_grading->production_form_item->amount,
                $sum_final_amount );

            $fabric_raw_grading->production_form_item->status_id                 = 7002006; //در انتظار تایید انبار
            $fabric_raw_grading->production_form_item->general_shrinkage_percent = $general_shrinkage_percent;
            $fabric_raw_grading->production_form_item->second_shrinkage_percent  = $second_shrinkage_percent;
            $fabric_raw_grading->production_form_item->save();

            event( new ProductionFormLogEvent(
                $fabric_raw_grading->production_form_item->production_form,
                700204,
                $fabric_raw_grading->production_form_item
            ) );
        }


        // تغییر وضعیت فرم تولید
        $allow_change_status_production_form = true;
        foreach ( $fabric_raw_grading->production_form->items as $item ) {
            $allow_change_status_production_form =
                $allow_change_status_production_form && ( isset( $item->status_id ) && $item->status_id == 7002006 );
        }

        if ( $allow_change_status_production_form ) {
            $fabric_raw_grading->production_form->status_id = 7002006; //در انتظار تایید انبار
            $fabric_raw_grading->production_form->save();

            event( new ProductionFormLogEvent(
                $fabric_raw_grading->production_form,
                700207
            ) );
        }

        // تغییر وضعیت حامل به پر در حال پر شدن
        if ( $carrier->status_id != 5320006 ) {
            $carrier->SetStatus( 5320006 );
        }

        return redirect()->route( $this->route_path . "other_form", $fabric_raw_grading );

    }

    public function other_form( FabricRawGrading $fabric_raw_grading ) {

        $allowed_status_ids = PostStatus::getAllowedStatus();
        if ( ! in_array( $fabric_raw_grading->status_id, $allowed_status_ids ) ) {//  درجه بندی شده
            return back()->withErrors( "شما مجوز مشاهده فرم را ندارید." );
        }

        $list = FabricRawGrading::where( [
            "lot_number_id" => $fabric_raw_grading->lot_number_id,
            "product_id"    => $fabric_raw_grading->product_id,
            "degree_id"     => $fabric_raw_grading->degree_id,
            "status_id"     => 7006002 // در انتظار بسته بندی
        ] )->get();

        if ( count( $list ) == 0 ) {
            return redirect()->route( $this->route_path . "index" )->with( [ "success" => "حامل  بسته بندی با موفقیت ثبت گردید." ] );

        } else {
            return view( $this->view_path . "other_form", compact( "list", "fabric_raw_grading" ) );
        }
    }

    public function view_other_form( FabricRawGrading $fabric_raw_grading, PackingForm $packing_form ) {

        $allowed_status_ids = PostStatus::getAllowedStatus();
        if ( ! in_array( $fabric_raw_grading->status_id, $allowed_status_ids ) ) {//  درجه بندی شده
            return back()->withErrors( "شما مجوز مشاهده فرم را ندارید." );
        }

        $info = FabricWaitingForPackingController::$info;


        $packing_type_option = Option::get( "packing_type", $packing_form->packing_type_id, $fabric_raw_grading->product->goods_kind_id );
        $carrier             = $packing_form->carrier;

        return view( $this->view_path . "view", compact( "fabric_raw_grading", "info", "packing_type_option", "carrier" ) );

    }

    public static function get_controller_info() {
        return $controller_info = [
            "01" => FabricWaitingForPackingController::$info
        ];
    }
}
