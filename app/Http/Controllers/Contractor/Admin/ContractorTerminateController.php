<?php

namespace App\Http\Controllers\Contractor\Admin;

use App\Events\Contractor\ContractorLogEvent;
use App\Events\ProductionCard\ProductionCardLogEvent;
use App\Http\Controllers\Controller;
use App\Models\Form\Form;
use App\Models\LineProduct\Machine\MachineAllocation;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestForm;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormForm;
use App\Models\Production\Production;

class ContractorTerminateController extends Controller {
    public static $info = [
        "route"         => "contractor.admin.contractor_terminate.",
        "view"          => "contractor.admin.contractor_terminate.",
        "enable_status" => [ "001", "002", "004" ],
        "button"        => [ "caption" => "خاتمه یافته کردن دستور پیمان", "class" => "btn-primary" ],

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "contractor.admin.dashboard.";

    public function __construct() {
        $this->route_path = self::$info["route"];
        $this->view_path  = self::$info["view"];
    }

    public function index( Production $production ) {

        $result = $this->checkPermission( $production );
        if ( $result != "" ) {
            return $result;
        }

        if ( $production->waiting_status_id == 7008005 ) {
            return back()->withErrors( "این دستور پیمان قبلا خاتمه یافته شده است." );
        }

        $contractor_allocation_list = MachineAllocation::
        where( "production_id", $production->id )->
        where( "status_id", "!=", 5310106 )->
        get();

        foreach ( $contractor_allocation_list as $contractor_allocation ) {
            $product_request_form_ids = ProductRequestForm::where( "allocation_id", $contractor_allocation->allocation_id )->pluck( "id" )->toArray();

            $form = Form::join( "product_request_form_form", "forms.id", "form_id" )->
            whereIn( "product_request_form_id", $product_request_form_ids )->
            whereNotIn( "forms.status_id", [ 500000100 ] )-> // عدم تایید
            select( "forms.*" )->
            first();
            if ( $form ) {
                return back()->withErrors( "با توجه به مواد اولیه دستور پیمان با برگ خروج " . $form->code . " تحویل شده است، امکان خاتمه یافته کردن کارت وجود ندارد." );
            }
        }

        // خاتمه یافته کردن دستور پیمان
        foreach ( $contractor_allocation_list as $contractor_allocation ) {
            $contractor_allocation->status_id = 5310106; // خاتمه یافته
            $contractor_allocation->save();
            event( new ContractorLogEvent( $contractor_allocation->contractor, 5310109, $contractor_allocation->production, $contractor_allocation ) );
        }
        $production->waiting_status_id = 7008005;
        $production->status_id         = 520;
        $production->save();
        event( new ProductionCardLogEvent( $production ) );

        return back()->with( [ "success" => "خاتمه یافته کردن کارت با موفقیت انجام شد." ] );


    }

    public function checkPermission( Production $production ) {

        $result = DashboardController::checkPermissionConditions( $production, self::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}
