<?php

namespace App\Http\Controllers\GoodsKindProcess\Warps\ProductionFrom;

use App\Events\Warehouse\Form\FormLogEvent;
use App\Http\Controllers\Controller;
use App\Models\Form\Form;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\Post\PostStatus;
use App\Models\Utility\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DashboardController extends Controller {
    var $view_path = "goods_kind_process.warps.production_form.dashboard.";
    var $route_path = "warps.production_form.dashboard.";

    public function index( Request $request ) {
        return redirect()->route( "warps.production_form.implementation_period_form.index" );
    }

//    public function show_form( Form $form ) {
//        $result = $this->checkPermission( $form );
//        if ( $result != "" ) {
//            return $result;
//        };
//
//        $form_item = $form->item()->first();
//        $product   = $form_item->product;
//
//        return view( $this->view_path . "show_form", compact( "form", "product", "form_item" ) );
//    }
//
//    public function confirm_warehouse( Form $form ) {
//
//        $result = $this->checkPermission( $form );
//        if ( $result != "" ) {
//            return $result;
//        };
//        $form->status_id = 500000410;
//        $form->save();
//        event( new FormLogEvent( $form ) );
//
//        return redirect()->route( "warps.production_form.dashboard.index" )->with( [ "success" => "فرم با موفقیت ثبت گردید" ] );
//
//    }

    public static function get_controller_info() {
        return $controller_info = [
            "01" => ImplementationPeriodFormController::$info,
            "02" => LogController::$info,
        ];
    }


    public static function checkPermissionConditions( Form $form, $info = false ) {

        if ( $form->form_type_id != 301 ) {
            return [
                "result"  => false,
                "message" => "نوع فرم به درستی انتخاب نشده است",
            ];
        }
        $allowed_status_ids = PostStatus::getAllowedStatus();
        if ( ! in_array( $form->status_id, $allowed_status_ids ) ) {
            return [
                "result"  => false,
                "message" => "شما اجازه مشاهده فرم را ندارید.",
            ];
        }

        if ( $info != false ) {
            if ( ! in_array( $form->status_id, $info['enable_status'] ) ) {
                return back()->withErrors( "  وضعیت فرم جهت عملیات نامعتبر است" );
            }
        }

        return [
            "result" => true,
        ];

    }

    public function checkPermission( Form $form ) {
        $result = DashboardController::checkPermissionConditions( $form );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

    }

}
