<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Dobby\DesignForm;

use App\Events\Fabric_Raw\FabricRawDesignFormLogEvent;
use App\Http\Controllers\Controller;
use App\Models\GoodsKindProcess\Fabric_Raw\Desing\FabricRawDesignForm;
use App\Models\GoodsKindProcess\Warps\RequestForm\WarpsRequestForm;
use App\Models\GoodsKindProcess\Warps\Warps;
use Illuminate\Http\Request;
use function back;
use function event;
use function redirect;
use function view;

class WarpsDeliveryConfirmationController extends Controller {
    public static $info = [
        "route"         => "fabric_raw.design_form.warps_delivery_confirmation.",
        "enable_status" => [ "003" ],
        "button"        => [ "caption" => "تایید تحویل چله از انبار", "class" => "btn-primary" ],
        "view_path"     => "goods_kind_process.fabric_raw.design_form.warps_delivery_confirmation.",
    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric_raw.design_form.dashboard.";

    public function __construct() {
        $this->route_path = WarpsDeliveryConfirmationController::$info["route"];
        $this->view_path  = WarpsDeliveryConfirmationController::$info["view_path"];
    }

    public function index( FabricRawDesignForm $design_form ) {

        $result = $this->checkPermission( $design_form );
        if ( $result != "" ) {
            return $result;
        };


        $warps_request_form = WarpsRequestForm::where( [
            "status_id"     => 7005004, // در انتظار تایید درخواست کننده
            "allocation_id" => $design_form->allocation_id
        ] )->first();

        if ( ! isset( $warps_request_form ) ) {
            return back()->withErrors( "وضعیت فرم تحویل چله از انبار 'در انتظار تایید درخواست کننده' نمی باشد." );
        }

        return view( $this->view_path . "index", compact( "warps_request_form", "design_form" ) );

    }

    public function submit( Request $request, FabricRawDesignForm $design_form ) {
        $result = $this->checkPermission( $design_form );
        if ( $result != "" ) {
            return $result;
        };

        $warps_request_form = WarpsRequestForm::where( [
            "status_id"     => 7005004, // در انتظار تایید درخواست کننده
            "allocation_id" => $design_form->allocation_id
        ] )->first();

        if ( ! isset( $warps_request_form ) ) {
            return back()->withErrors( "وضعیت فرم تحویل چله از انبار 'در انتظار تایید درخواست کننده' نمی باشد." );
        }


        // تحویل چله و عملیات مربوط به آن
        Warps::warpsDeliveryConfirmation( $design_form->allocation );

        $design_form->status_id = DashboardController::$perfix_design_form_status_code . "005";
        $design_form->save();
        event( new FabricRawDesignFormLogEvent( $design_form ) );

        return redirect()->route( $this->dashboard_route . "view", compact( "design_form" ) );

    }

    public function checkPermission( FabricRawDesignForm $design_form ) {

        $result = DashboardController::checkPermissionConditions( $design_form, WarpsDeliveryConfirmationController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}
