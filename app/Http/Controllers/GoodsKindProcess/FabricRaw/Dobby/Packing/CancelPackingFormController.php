<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Dobby\Packing;

use App\Events\Form\PackingLogEvent;
use App\Events\ProductionForm\ProductionFormLogEvent;
use App\Http\Controllers\Controller;
use App\Models\Form\Packing\PackingForm;
use App\Models\GoodsKindProcess\Fabric_Raw\FabricRawGrading;
use Illuminate\Http\Request;
use function back;
use function event;
use function redirect;

class CancelPackingFormController extends Controller {
    var $view_path = "goods_kind_process.fabric_raw.packing.cancel_packing_form.";
    var $route_path = "fabric_raw.packing.cancel_packing_form.";
    var $dashboard_path = "fabric_raw.packing.dashboard.";
    public static $info = [
        "route"         => "fabric_raw.packing.cancel_packing_form.",
        "enable_status" => [ "001" ],
        "button"        => [ "caption" => "کنسل کردن بسته بندی", "class" => "btn-danger" ],
        "message"       => [ "confirm" => "آیا از کنسل کردن فرم بسته بندی اطمینان دارید؟" ]
    ];

    public function submit( Request $request, PackingForm $packing_form ) {

        $result = $this->checkPermission( $packing_form );
        if ( $result != "" ) {
            return $result;
        }
        $packing_form->carrier->SetEmpty();

        $packing_form->status_id = 7007004; // کنسل شده
        $packing_form->save();
        event( new PackingLogEvent( $packing_form, 7007004 ) );

        $changeProductionFormStatusCalled=true;
        foreach ( $packing_form->items as $item ) {
            $item->fabric_raw_grading->status_id            = 7006002; // در انتظار بسته بندی
            $item->fabric_raw_grading->packing_form_item_id = 0;
            $item->fabric_raw_grading->final_amount = null;
            $item->fabric_raw_grading->second_shrinkage_percent = null;
            $item->fabric_raw_grading->save();

            if($changeProductionFormStatusCalled){
                $this->changeProductionFormStatus($item->fabric_raw_grading);
                $changeProductionFormStatusCalled=false;
            }

        }

        return redirect()->route( $this->dashboard_path . "view", $packing_form )->
        with( [ "success" => "عملیات با موفقیت انجام شد." ] );
    }

    public function checkPermission( PackingForm $packing_form ) {
        $result = DashboardController::checkPermissionConditions( $packing_form, CancelPackingFormController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

    }

    public function changeProductionFormStatus( FabricRawGrading $fabric_raw_grading ) {

        // تغییر وضعیت آیتم فرم آیتم تولید
        $allow_change_status_production_form_item = true;
        foreach ( $fabric_raw_grading->production_form_item->fabric_grading as $item ) {
            $allow_change_status_production_form_item =
                $allow_change_status_production_form_item &&
                (
                    isset( $item->status_id ) &&
                    $item->status_id == 7006002 // در انتظار بسته بندی
                );
        }

        if ( $allow_change_status_production_form_item ) {
            $fabric_raw_grading->production_form_item->status_id = 7002004; //در انتظار ثبت حامل بسته بندی
            $fabric_raw_grading->production_form_item->save();

            event( new ProductionFormLogEvent(
                $fabric_raw_grading->production_form_item->production_form,
                7007004, // حذف فرم بسته بندی
                $fabric_raw_grading->production_form_item
            ) );
        }


    }
}
