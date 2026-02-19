<?php

namespace App\Http\Controllers\Warehouse\WarehouseHandling;

use App\Events\Form\PackingLogEvent;
use App\Events\Machine\MachineLogEvent;
use App\Events\Product\RejectProductLogEvent;
use App\Events\Warehouse\Form\FormLogEvent;
use App\Events\Warehouse\PutInWarehouseEvent;
use App\Events\Warehouse\WarehouseHandlingEvent;
use App\Events\Warps\WarpsRequestFormLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\FabricRaw\PackingForm\PrintQRController;
use App\Http\Controllers\Utility\Script\Script1012Controller;
use App\Models\Form\Form;
use App\Models\Form\FormItem;
use App\Models\Form\Packing\PackingForm;
use App\Models\Form\Packing\PackingFormItem;
use App\Models\GoodsKindProcess\Fabric_Raw\FabricRawGrading;
use App\Models\GoodsKindProcess\Warps\RequestForm\WarpsRequestForm;
use App\Models\GoodsKindProcess\Warps\Warps;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestForm;
use App\Models\LineProduct\Product\RejectProduct\RejectProductForm;
use App\Models\Post\Post;
use App\Models\Utility\Notification\SMSMessage;
use App\Models\Utility\Option;
use App\Models\Utility\QueueOfLargeOperation;
use App\Models\Utility\Status;
use App\Models\Warehouse\Warehouse;
use App\Models\Warehouse\WarehouseHandling\WarehouseHandling;
use App\Models\Warehouse\WarehouseHandling\WarehouseHandlingPackingForm;
use App\Models\Warehouse\WarehouseProduct;
use App\Models\Worker;
use GuzzleHttp\Exception\InvalidArgumentException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class EndOfReviewController extends Controller
{
    public static $info = [
        "route" => "wh.warehouse_handling.end_of_review.",
        "enable_status" => ["304"],
        "button" => ["caption" => "پایان بررسی بسته بندی ها", "class" => "btn-primary"],
        "message" => ["confirm" => "آیا از پایان بررسی بسته بندی ها اطمینان دارید؟"],
        "allowed_status_ids" => [524000304, 524000311]

    ];
    var $view_path;
    var $route_path;
    var $dashboard_path = "wh.warehouse_handling.dashboard.index";

    public function __construct()
    {
        $this->route_path = self::$info["route"];
        $this->view_path = "warehouse.warehouse_handling.end_of_review.";
    }

    public function submit(WarehouseHandling $warehouse_handling)
    {
        $result = $this->checkPermission($warehouse_handling);
        if ($result != "") {
            return $result;
        }

        $count = WarehouseHandlingPackingForm::where("warehouse_handling_id", $warehouse_handling->id)->
        whereIn("status_id", [524000406, 524000407])->count();

        if ($count > 0) {
            return back()->withErrors("با توجه به اینکه وضعیت $count بسته بندی نامعتبر است، لطفا ابتدا وضعیت آنها را اصلاح نمایید.  ");
        }


        $warehouse_handling->status_id = 524000303; // در حال پردایش اولیه
        $warehouse_handling->save();

        event(new WarehouseHandlingEvent($warehouse_handling, 524000309));

        $data["warehouse_handling_id"] = $warehouse_handling->id;
        QueueOfLargeOperation::AddToQueue($data, 201);

        return back()->with(["success" => "پایان انبارگردانی با موفقیت ثبت گردید"]);
    }

    public function check_packing_form(WarehouseHandling $warehouse_handling, WarehouseHandlingPackingForm $warehouseHandlingPackingForm)
    {

        $result = $this->checkPermission($warehouse_handling);
        if ($result != "") {
            return $result;
        }
        switch ($warehouseHandlingPackingForm->status_id) {
            case 524000407: //خوانده نشده وضعیت نامعتبر
                if ($warehouseHandlingPackingForm->packing_form->packing_form_contents()->count() > 0) {
                    return back()->withErrors("امکان اصلاح وضعیت بسته بندی وجود ندارد، لطفا با پشتیبانی تماس بگیرید.");
                }
                $warehouseHandlingPackingForm->packing_form->status_id = 7007012;
                $warehouseHandlingPackingForm->packing_form->warehouse_status_id = 4202;
                $warehouseHandlingPackingForm->packing_form->warehouse_id = null;
                $warehouseHandlingPackingForm->packing_form->save();

                $packing_form_code = $warehouseHandlingPackingForm->packing_form->code ?? ($warehouseHandlingPackingForm->packing_form + 1000);
                $warehouseHandlingPackingForm->delete();
                return back()->with(["success" => "وضعیت  بندی " . $packing_form_code . " اصلاح گردید"]);
                break;
        }

        return back()->withErrors("لطفا با پشتیبانی تماس بگیرد.");
    }

    public function reading_packing_form_after(WarehouseHandling $warehouse_handling, WarehouseHandlingPackingForm $warehouseHandlingPackingForm)
    {

        if ($warehouseHandlingPackingForm->status_id != 524000403) {
            return back()->withErrors("وضعیت بسته جهت خواندن بسته بندی نامعتبر است.<br/> فقط بسته بندی های با وضعیت خوانده نشده (داخل انبار) امکان ثبت دارند.");
        }
        return view($this->view_path . "reading_packing_form_after", compact("warehouse_handling", "warehouseHandlingPackingForm"));

    }

    public function submit_reading_packing_form_after(Request $request, WarehouseHandling $warehouse_handling, WarehouseHandlingPackingForm $warehouseHandlingPackingForm)
    {

        // خوانده نشده (داخل انبار)
        if ($warehouseHandlingPackingForm->status_id != 524000403) {
            return back()->withErrors("وضعیت بسته جهت خواندن بسته بندی نامعتبر است.<br/> فقط بسته بندی های با وضعیت خوانده نشده (داخل انبار) امکان ثبت دارند.");
        }

        if ("DCPK/" . $request->packing_code != $warehouseHandlingPackingForm->packing_form->code) {
            return back()->withErrors("کد بسته بندی نامعتبر است.");
        }
        if ($warehouse_handling->status_id == 524000311) {
            $warehouse_handling->status_id = 524000304; // در انتظار بررسی بسته بندی ها
            $warehouse_handling->save();
            event(new WarehouseHandlingEvent($warehouse_handling, 524000310));
        }
        $result = $this->checkPermission($warehouse_handling);
        if ($result != "") {
            return $result;
        }

        $warehouseHandlingPackingForm->status_id = 524000401; // خوانده شده (داخل انبار)
        $warehouseHandlingPackingForm->save();

        return redirect()->route("wh.warehouse_handling.dashboard.packing_form_list", [$warehouse_handling, 524000403])->
        with(["success" => "بسته بندی با کد " . $warehouseHandlingPackingForm->packing_form->code . " با موفقیت خوانده شده و به لیست بسته بسته بندی های خوانده شده (داخل انبار) اضافه گردید."]);
    }

    public function checkPermission(WarehouseHandling $warehouseHandling)
    {

        $result = DashboardController::checkPermissionConditions($warehouseHandling, self::$info);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }

        return "";
    }
}
