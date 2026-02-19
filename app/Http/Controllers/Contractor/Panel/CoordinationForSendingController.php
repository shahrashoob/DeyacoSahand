<?php

namespace App\Http\Controllers\Contractor\Panel;

use App\Events\Contractor\ContractorLogEvent;
use App\Events\ProductionCard\ProductionCardLogEvent;
use App\Http\Controllers\Controller;
use App\Models\Contractor\Contractor;
use App\Models\Contractor\ContractorAllocation;
use App\Models\Form\Packing\PackingForm;
use App\Models\Form\Packing\PackingFormItem;
use App\Models\LineProduct\LineProductStation;
use App\Models\LineProduct\Machine\MachineAllocation;
use App\Models\LineProduct\Product\BOM\BOM;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestForm;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormItem;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormPackingType;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormSessionData;
use App\Models\Production\Production;
use App\Models\Production\ProductionFormItem;
use App\Models\Utility\JsonDataList;
use App\Models\Warehouse\WarehouseProduct;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CoordinationForSendingController extends Controller
{
    public static $info = [
        "route" => "contractor.panel.coordination_for_sending.",
        "view" => "contractor.panel.coordination_for_sending.",
        "enable_status" => ["107"],
        "button" => ["caption" => "هماهنگی جهت دریافت مواد اولیه", "class" => "btn-primary"],

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "contractor.panel.dashboard.";

    //
    public function __construct()
    {
        $this->route_path = CoordinationForSendingController::$info["route"];
        $this->view_path = CoordinationForSendingController::$info["view"];
    }

    public function index(ContractorAllocation $contractor_allocation)
    {

        $result = $this->checkPermission($contractor_allocation);
        if ($result != "") {
            return $result;
        }

        $line_product_station = LineProductStation::
        whereNotNull("contractor_operation_id")->
        where("contractor_id", $contractor_allocation->contractor_id)->
        where("product_id", $contractor_allocation->product_id)->
        first();
        if (!$line_product_station) {
            return back()->withErrors("خط تولید برای کالا یافت نشد، لطفا با پشیتبانی تماس بگیرید.");
        }
        if (count($contractor_allocation->production->packing_types) == 0) {
            return back()->withErrors("بسته بندی مجاز فروش برای پیمان مشخص نشده است، لطفا با پشتیبانی تماس بگیرد.");
        }

        $contractor = $contractor_allocation->contractor;
        // لیست بسته های موجود در انبار
        $package_list = [];
        foreach ($contractor_allocation->allocation->items as $allocation_item) {
            $line_product_station = LineProductStation::where("product_id", $allocation_item->product_id)->
            where("contractor_id", $contractor->id)->first();
            // گرفتن اولین BOM
            $bom = BOM::where("product_route_id", $line_product_station->product_route_id ?? 0)->first();
            if (!$bom) {
                return back()->withErrors("با توجه به اینکه هیچ ردیف BOMی برای کالا تعریف نشده است، امکان هماهنگی دریافت مواد اولیه وجود ندارد.");
            }
            if ($contractor->show_packing_forms_in_warehouse) {

                if ($bom) {
                    foreach ($bom->items as $bom_item) {
                        $package_list[$bom_item->id]["package"] = $this->getPackingInWarehouse($bom_item);
                        $package_list[$bom_item->id] ["bom_item"] = $bom_item;

                    }
                }
            }
        }
        $min_date=jdate(now())->format('Y/m/d');
        return view($this->view_path . "index", compact("min_date","contractor_allocation", "contractor", "package_list"));
    }

    public function submit(Request $request, ContractorAllocation $contractor_allocation)
    {


        $datetime = $request->coordination_time_for_receive_product ;
        $contractor = $contractor_allocation->contractor;

        $result = $this->checkPermission($contractor_allocation);
        if ($result != "") {
            return $result;
        }


        $result = self::PostSubmit($datetime, $contractor, $contractor_allocation, $request->message);
        if ($result["result"]) {
            return redirect()->route($this->dashboard_route . "index", $contractor_allocation)->
            with(["success" => $result["message"]]);
        } else {
            return redirect()->route($this->dashboard_route . "index", $contractor_allocation)->
            withErrors($result["error"]);
        }
    }

    /**
     * @param $datetime :  2024/6/10 14:00 / تاریخ هماهنگی
     * @param Contractor $contractor
     * @param ContractorAllocation $contractor_allocation
     * @param $message
     * @return array
     */
    public static function PostSubmit($datetime, Contractor $contractor, ContractorAllocation $contractor_allocation, $message, $json_data = null)
    {

        $time = Carbon::parse(Carbon::parse($datetime)->format('h:i:s'));

        $start_of_work_time = new Carbon($contractor->start_of_work_time);
        $end_of_work_time = new Carbon($contractor->end_of_work_time);

        if ($time->gt($end_of_work_time) || $start_of_work_time->gt($time)) {
            return [
                "result" => false,
                "error" => "زمان کار انبار از ساعت " . $contractor->start_of_work_time . " تا ساعت " . $contractor->end_of_work_time . " می باشد، لطفا ساعت هماهنگی را اصلاح فرمایید."
            ];
        }

        $next_datetime = new Carbon();
        $next_datetime->addHour($contractor->minimum_time_required_to_start_coordination);

        if ($next_datetime->gt(new Carbon($datetime))) {
            return [
                "result" => false,
                "error" => "امکان هماهنگی ارسال بار  تا قبل از " . $contractor->minimum_time_required_to_start_coordination . " ساعت آینده وجود ندارد. "
            ];

        }

        // بررسی بسته بندی های پیشنهادی
        $suggested_packing_ids = [];
        $suggested_packing_codes = [];
        if (isset($json_data["suggested_packing_codes"])) {
            $suggested_packing_codes = $json_data["suggested_packing_codes"];
            if (count($suggested_packing_codes) == 0) {
                return [
                    "result" => false,
                    "error" => " لیست بسته بندی های پیشنهادی جهت ارسال خالی است."
                ];
            }
            $suggested_packing_ids = PackingForm::whereIn("code", $suggested_packing_codes)->
            pluck("id")->
            toArray();
            if (count($suggested_packing_codes) != count($suggested_packing_ids)) {
                return [
                    "result" => false,
                    "error" => " لیست بسته بندی های پیشنهادی جهت ارسال نامعتبر است."
                ];
            }

            $json_data["suggested_packing_ids"] = $suggested_packing_ids;
        }


        // تغییر وضعیت تخصیص
        foreach ($contractor_allocation->allocation->items as $allocation_item) {
            $allocation_item->status_id = 5310103; // در انتظار تحویل مواد اولیه
            $allocation_item->save();
            event(new ContractorLogEvent($contractor, 5310103, $allocation_item->production, $allocation_item, $message));
        }
        // ثبت تاریخ هماهنگی
        $contractor_allocation->allocation->coordination_time_for_receive_product = $datetime;
        $contractor_allocation->allocation->save();

        $other["user_id"] = Auth::user()->id;

        $result = ProductRequestForm::newRequest(
            $contractor_allocation->allocation, $contractor->id,
            20, 1, $other,
            $datetime,
            $message
        );
        if (isset($result) && !$result["result"]) {
            return $result;
        }

        // ثبت بسته بندی های پیشنهادی
        if (count($suggested_packing_codes) > 0) {


            $product_request_form = $result["product_request_form"];
            $jsn_data_list = JsonDataList::create([
                "other_id" => $product_request_form->id,
                "message_type_id" => 330, // اطلاعات اضافه درخواست
                "data" => json_encode($json_data)
            ]);
            $product_request_form->json_data_id = $jsn_data_list->id;
            $product_request_form->save();
        }

        // تغییر وضعیت پیمان
        $contractor_allocation->production->waiting_status_id = 7008002;  // در حال تولید توسط n پیمانکار
        $contractor_allocation->production->save();
        event(new ProductionCardLogEvent($contractor_allocation->production));

        return [
            "result" => true,
            "message" => "عملیات با موفقیت انجام شد."
        ];

    }

    public function getPackingInWarehouse($bom_item)
    {

        $packing_type_ids = [0];
        foreach ($bom_item->material->packing_types as $item) {
            $packing_type_ids[] = $item->id;
        };


        return PackingForm::join("packing_form_item", "packing_form_id", "packing_forms.id")->
        where("product_id", $bom_item->material_id)->
        where("warehouse_status_id", 4201)->
        whereIn("packing_type_id", $packing_type_ids)->
        groupBy("packing_forms.id")->
        select("packing_forms.id", "packing_forms.code")->
        paginate(10);
    }

    public function checkPermission(ContractorAllocation $contractor_allocation)
    {

        $result = DashboardController::checkPermissionConditions($contractor_allocation, CoordinationForSendingController::$info);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }

        return "";
    }

    public function get_production_info(Request $request)
    {

        $packing_form = PackingForm::find($request->packing_form_id);
        $contractor_allocation = ContractorAllocation::find($request->contractor_allocation_id);
        $contractor = Contractor::find($request->contractor_id);


        if (!$contractor || !$contractor_allocation || $contractor->id != $contractor_allocation->contractor->id || !$packing_form) {
            return "<div class='alert-danger'>درخواست با خطا مواجه شده، لطفا دوباره تلاش کنید.</div>";
        }

        $production_form_item_ids = PackingFormItem::
        where("packing_form_id", $packing_form->id)->
        distinct("production_form_item_id")->
        pluck("production_form_item_id")->
        toArraY();

        $production_ids = ProductionFormItem::whereIn("id", $production_form_item_ids)->
        pluck("production_id")->toArray();
        $production_list = Production::whereIn("id", $production_ids)->get();

        $production_amount = ProductionFormItem::whereIn("production_id", $production_ids)->
        groupBy("production_id")->
        addSelect(DB::raw("production_id ,sum(final_amount) as final_amount"))->pluck("final_amount", "production_id");


        $number_of_doffs = MachineAllocation::whereIn("production_id", $production_ids)->
        groupBy("production_id")->
        addSelect(DB::raw("production_id ,sum(max_number_of_doffs) as max_number_of_doffs"))->pluck("max_number_of_doffs", "production_id");

        $number_of_doffs_done = MachineAllocation::whereIn("production_id", $production_ids)->
        groupBy("production_id")->
        addSelect(DB::raw("production_id ,sum(number_of_doffs_done) as number_of_doffs_done"))->pluck("number_of_doffs_done", "production_id");


        $allocation_amount = MachineAllocation::whereIn("production_id", $production_ids)->
        groupBy("production_id")->
        addSelect(DB::raw("production_id ,sum(allocation_amount) as allocation_amount"))->pluck("allocation_amount", "production_id");


        return view($this->view_path . "_production_card_list", compact("allocation_amount", "packing_form", "production_list", "production_amount", "number_of_doffs_done", "number_of_doffs"));

    }
}
