<?php

namespace App\Http\Controllers\GoodsKindProcess\General\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Events\Warps\WarpsRequestFormLogEvent;
use App\Http\Controllers\Controller;
use App\Models\GoodsKindProcess\Warps\RequestForm\WarpsRequestForm;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestForm;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormForm;
use Illuminate\Http\Request;


class GeneralMaterialDeliveryRejectController extends Controller {

    var $view_path = "goods_kind_process.general.machine.material_delivery_reject.";
    var $route_path;
    var $dashboard_route;

    public function __construct() {

    }

    public function index( Machine $machine ) {

        $product_request_form = ProductRequestForm::where( [
            "applicant_type_id" => 40,
            "applicant_id"      => $machine->warehouse_id,
            "status_id"         => 7005004,// در انتظار تایید برگ خروج
        ] )->first();

        $forms = $product_request_form->forms;
        if ( count( $forms ) == 0 ) {
            return back()->withErrors( "برگ خروج از انبار یافت نشده، لطفا با پشتیبانی تماس بگیرید." );
        }

        $route_path=$this->route_path;
        $dashboard_route = $this->dashboard_route;
        return view( $this->view_path . "index", compact( "product_request_form", "machine","route_path","dashboard_route" ) );


    }

    public function submit( Request $request, Machine $machine ) {

        $product_request_form = ProductRequestForm::where( [
            "applicant_type_id" => 40,
            "applicant_id"      => $machine->warehouse_id,
            "status_id"         => 7005004,// در انتظار تایید برگ خروج
        ] )->first();

        $forms = $product_request_form->forms;
        if ( count( $forms ) == 0 ) {
            return back()->withErrors( "برگ خروج از انبار یافت نشده، لطفا با پشتیبانی تماس بگیرید." );
        }

        foreach ( $product_request_form->forms as $product_request_form_form ) {
            $key = "form_" . $product_request_form_form->form_id;
            if ( $request->$key ) {
                $result = $product_request_form->rejectRequest( $product_request_form_form->form, null, $request->description );
                // در این تابع وضعیت جدید فرم ثبت می شود.

                if ( $result["result"] ) {

                } else {
                    return back()->withErrors( $result["error"] );
                }
            }
        }

        // لاگ ماشین
        $machine_log                        = new MachineLog();
        $machine_log->machine_event_type_id = 705;// عدم تایید تحویل مواد اولیه
        event( new MachineLogEvent( $machine, $machine_log ) );

        return redirect()->route( $this->dashboard_route . "view", compact( "machine" ) )->with( [ "success" => "عملیات با موفقیت انجام شد." ] );

    }

}
