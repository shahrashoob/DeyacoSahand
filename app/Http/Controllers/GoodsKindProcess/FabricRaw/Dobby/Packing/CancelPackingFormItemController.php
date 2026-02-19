<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Dobby\Packing;

use App\Events\Form\PackingLogEvent;
use App\Events\ProductionForm\ProductionFormLogEvent;
use App\Http\Controllers\Controller;
use App\Models\Form\Packing\PackingForm;
use App\Models\Form\Packing\PackingFormItem;
use App\Models\GoodsKindProcess\Fabric_Raw\FabricRawGrading;
use App\Models\Post\PostStatus;
use Illuminate\Support\Facades\Auth;
use function back;
use function event;
use function redirect;

class CancelPackingFormItemController extends Controller {

    var $view_path = "goods_kind_process.fabric_raw.packing.cancel_packing_form_item.";
    var $route_path = "fabric_raw.packing.cancel_packing_form_item.";
    var $dashboard_path = "fabric_raw.packing.dashboard.";
    public static $info = [
        "route"         => "fabric_raw.packing.cancel_packing_form_item.",
        "enable_status" => [],
        "button"        => [ "caption" => "کنسل کردن آیتم های بسته بندی", "class" => "btn-danger" ],
    ];

    public function index( PackingForm $packing_form, PackingFormItem $packing_form_item ) {

        $result = $this->checkPermissionConditions( $packing_form );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        if ( $packing_form_item->packing_form_id != $packing_form->id ) {
            return back()->withErrors( "درخواست نامعتبر می باشد." );
        }

        if ( $packing_form->items->count() <= 1 ) {
            return back()->withErrors( "در فرم بسته بندی باید حداقل یک کالا وجود داشته باشد." );
        }

        $packing_form_item->fabric_raw_grading->status_id            = 7006002; // در انتظار بسته بندی
        $packing_form_item->fabric_raw_grading->packing_form_item_id = 0;
        $packing_form_item->fabric_raw_grading->final_amount = null;
        $packing_form_item->fabric_raw_grading->second_shrinkage_percent = null;
        $packing_form_item->fabric_raw_grading->save();


        event( new PackingLogEvent( $packing_form, 7007004, null, "حذف ردیف " . $packing_form_item->getCode() ) );
        $packing_form_item->delete();

        $this->changeProductionFormStatus($packing_form_item->fabric_raw_grading);

        return redirect()->route( $this->dashboard_path . "view", $packing_form )->
        with( [ "success" => "عملیات با موفقیت انجام شد." ] );

    }

    public function checkPermissionConditions( PackingForm $packing_form ) {

        $allowed_status_ids = PostStatus::getAllowedStatus();
        if ( ! in_array( $packing_form->status_id, $allowed_status_ids ) ) {
            return [
                "result"  => false,
                "message" => "شما اجازه مشاهده فرم را ندارید.",
            ];
        }
        if ( $packing_form->status_id != 7007001 ) { // در حال تکمیل
            return [
                "result"  => false,
                "message" => "وضعیت فرم بسته بندی جهت عملیات نامعتبر است",
            ];
        }
        $post_user = Auth::user()->posts->first();
        if ( ! $post_user->checkButtonPermission( CancelPackingFormItemController::$info["route"] . "index" ) ) {
            return [
                "result"  => false,
                "message" => "دسترسی  عملیات برای شما تعریف نشده است",
            ];
        }


        return [
            "result" => true,
        ];

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
                7007003, // حذف آیتم بسته بندی
                $fabric_raw_grading->production_form_item
            ) );
        }

    }
}
