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
use App\Models\LineProduct\GoodsKind\GoodsKindClassificationProduct;
use App\Models\LineProduct\GoodsKind\GoodsKindPost;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestForm;
use App\Models\LineProduct\Product\RejectProduct\RejectProductForm;
use App\Models\Post\Post;
use App\Models\Utility\Notification\SMSMessage;
use App\Models\Utility\Option;
use App\Models\Utility\Status;
use App\Models\Warehouse\Warehouse;
use App\Models\Warehouse\WarehouseHandling\WarehouseHandling;
use App\Models\Warehouse\WarehouseHandling\WarehouseHandlingPackingForm;
use App\Models\Warehouse\WarehouseHandling\WarehouseHandlingProduct;
use App\Models\Warehouse\WarehouseProduct;
use App\Models\Warehouse\WarehouseProductBlock;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class NewHandlingController extends Controller
{
    public static $info = [
        "route" => "wh.warehouse_handling.new_handling.",
        "view_path" => "warehouse.warehouse_handling.new_handling.",

    ];
    var $view_path;
    var $route_path;
    var $dashboard_path = "wh.warehouse_handling.dashboard.index";

    public function __construct()
    {
        $this->route_path = self::$info["route"];
        $this->view_path = self::$info["view_path"];
    }

    public function index(Request $request)
    {
        $result = $this->checkPermission();
        if ($result != "") {
            return $result;
        }
        $warehouse_option = Option::get("warehouse", 0, 0, [1]);

        $smart_object_option = Option::get("smart_object_post", 0, 2);
        $product_option = Option::get("product_all", 0);

        $allowed_goods_kind_ids = GoodsKindPost::getAllowedGoodsKindId();
        $goods_kind_option = Option::get("goods_kind", 0, 0, $allowed_goods_kind_ids);
        $classification_option = Option::get("goods_kind_classification_group_option", 0, 0);

        return view($this->view_path . "index", compact("goods_kind_option", "classification_option", "warehouse_option", "smart_object_option", "product_option"));
    }

    public function submit(Request $request)
    {
        $result = $this->checkPermission();
        if ($result != "") {
            return $result;
        }
        if (!$request->all_product && !isset($request->product_ids)) {
            return back()->withErrors("لطفا حداقل یک کالا جهت انبارگردانی انتخاب نمایید");
        }

        if ($request->breaking_by_classification && !isset($request->classification_id)) {
            return back()->withErrors("با توجه به اینکه انتخاب کرده اید همه کالاها به تفکیک طبقه بندی، لطفا حداقل یک طبقه را انتخاب نمایید.");
        }

        // بررسی اینکه هر بسته بندی که داخل انبار است، کالاهایی که داخل انبار است، در انبارگردانی وجود دارد.
        if (isset($request->product_ids)) {

            $result_product_permission = $this->CheckProdcutPermission($request->warehouse_id, $request->product_ids);
            if (!$result_product_permission["result"]) {
                return back()->withErrors($result_product_permission["error"]);
            }
        }
        $product_ids_for_check = [];
        if (isset($request->classification_id)) {
            $product_ids_list = GoodsKindClassificationProduct::where("goods_kind_classification_id", $request->classification_id)->
            pluck("goods_kind_classification_option_id", "product_id")->toArray();

            $product_ids_for_check = array_keys($product_ids_list);

        } else if (!isset($request->product_ids)) {
            $old_warehouse_handling = WarehouseHandling::where("warehouse_id", $request->warehouse_id)->
            whereNotIn("status_id", [524000322, 524000323])-> // تایید شده و تایید نشده
            first();
            if ($old_warehouse_handling) {
                $message = " با توجه به اینکه  " . "  انبار گردانی شماره " . $old_warehouse_handling->id .
                    " در وضعیت " . $old_warehouse_handling->status->caption .
                    " می باشد، امکان ثبت مجدد انبار گردانی برای این انبار وجود ندارد. " . "<br/>";
                return back()->withErrors($message);
            }

        } else {

            $product_ids_for_check = $request->product_ids;
        }


        // چک کردن اینکه کالاهای انتخاب شده در هیچ انبارگردانی دیگری نباشند.
        if (count($product_ids_for_check) > 0) {
            // return $request->product_ids;
            $old_warehouse_handling_product = WarehouseHandlingProduct::
            join("warehouse_handling", "warehouse_handling_id", "warehouse_handling.id")->
            where("warehouse_id", $request->warehouse_id)->
            whereIn("product_id", $product_ids_for_check)->
            select("warehouse_handling_product.*")->
            whereNotIn("status_id", [524000322, 524000323])-> // تایید شده و تایید نشده
            get();

            if (count($old_warehouse_handling_product) > 0) {
                $message = "";
                foreach ($old_warehouse_handling_product as $item) {
                    $message .= " با توجه به اینکه  " . $item->product->caption . " در انبار گردانی شماره " . $item->warehouse_handling_id .
                        " وجود دارد و این انبارگردانی " . " در وضعیت " . $item->warehouse_handling->status->caption .
                        " می باشد، امکان ثبت مجدد انبار گردانی برای این کالا وجود ندارد. " . "<br/>";
                }

                return back()->withErrors($message);
            }
        }


        $request["user_id"] = Auth::id();
        $request["status_id"] = 524000301; //   در انتظار شروع انبار گردانی


        if (isset($request->classification_id)) {

            // چون به صورت طبقه بندی انبارگردانی را ایجاد کرده اند،
            // به ازای هر گروه طبقه بندی در رسته کالایی
            // که انبارگردانی ایجاد می شود و کالاهای ان دسته در آن قرار می گیرد.
            $warehouse_handling_list = [];
            $goods_kind_classification_option_ids = GoodsKindClassificationProduct::where("goods_kind_classification_id", $request->classification_id)->
            pluck("product_id", "goods_kind_classification_option_id")->toArray();

            foreach ($goods_kind_classification_option_ids as $id => $value_not_important) {
                $warehouse_handling = WarehouseHandling::create($request->all());
                foreach ($product_ids_list as $product_id => $goods_kind_classification_option_id) {

                    //
                    if ($goods_kind_classification_option_id == $id) {
                        WarehouseHandlingProduct::create([
                            "warehouse_handling_id" => $warehouse_handling->id,
                            "product_id" => $product_id
                        ]);
                    }
                }

                WarehouseProductBlock::UpdateBlockedByWarehouseHandling($warehouse_handling);
                event(new WarehouseHandlingEvent($warehouse_handling, 524000301));

            }

            return redirect()->route($this->dashboard_path)->with(["success" => count($goods_kind_classification_option_ids) . " انبار گردانی جدید با توجه به طبقه بندی انتخاب شده  ایجاد گردید"]);

        } else {
            $warehouse_handling = WarehouseHandling::create($request->all());

            if (isset($request->product_ids)) {
                foreach ($request->product_ids as $product_id) {
                    WarehouseHandlingProduct::create([
                        "warehouse_handling_id" => $warehouse_handling->id,
                        "product_id" => $product_id
                    ]);
                }
            }

            WarehouseProductBlock::UpdateBlockedByWarehouseHandling($warehouse_handling);
            event(new WarehouseHandlingEvent($warehouse_handling, 524000301));

            return redirect()->route($this->dashboard_path)->with(["success" => "یک انبار گردانی جدید با موفقیت ایجاد گردید"]);
        }
    }

    public function CheckProdcutPermission($warehouse_id, $product_ids)
    {
        $packing_form_ids = PackingForm::
        join("packing_form_item", "packing_form_id", "packing_forms.id")->
        whereIn("product_id", $product_ids)->
        where("warehouse_id", $warehouse_id)->
        where("warehouse_status_id", 4201)->
        pluck("packing_forms.id")->toArray();
        $packing_form_ids[] = -1;
        $list = PackingForm::
        join("packing_form_item", "packing_form_id", "packing_forms.id")->
        whereIn("packing_form_id", $packing_form_ids)->
        whereNotIn("product_id", $product_ids)->
        select("packing_forms.*")->
        get();
        $message = "";
        foreach ($list as $item) {
            if (!in_array($item->product_id, $product_ids)) {
                $message .= "<br/>" . $item->code;
            }
        }
        if ($message != "") {
            return [
                "result" => false,
                "error" =>
                    "امکان ثبت انبارگردانی با کالاهای مجاز انتخاب شده وجود ندارد، زیرا در بسته بندی های زیر کالاهایی وجود دارد که جزء کالاهای غیر مجاز در انبارگردانی می باشد." . $message
            ];

        }

        return [
            "result" => true,
        ];
    }

    public function checkPermission()
    {
        $post_user = Auth::user()->posts->first();
        if (!$post_user->checkButtonPermission(self::$info["route"] . "index")) {
            return back()->withErrors("دسترسی  عملیات برای شما تعریف نشده است");
        }

        return "";
    }

}




