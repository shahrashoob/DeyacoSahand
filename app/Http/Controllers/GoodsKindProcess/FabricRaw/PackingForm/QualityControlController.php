<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\PackingForm;

use App\Events\Form\PackingLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\FabricRaw;
use App\Models\Form\Packing\PackingForm;
use App\Models\Form\Packing\PackingFormItem;
use App\Models\Form\Packing\PackingFormLayer;
use App\Models\LineProduct\Carrier\Carrier;
use App\Models\LineProduct\Degree;
use App\Models\LineProduct\Packing\DischargeType;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\LineProduct\Product;
use App\Models\Post\PostUser;
use App\Models\QualityControl\QualityControlPackingForm;
use App\Models\QualityControl\QualityControlProductFault;
use App\Models\QualityControl\QualityControlProductFaultPropertyValue;
use App\Models\QualityControl\QualityControlUser;
use App\Models\Utility\JsonDataList;
use App\Models\Utility\Option;
use App\Models\Utility\Setting;
use App\Models\Utility\SpecialLicense\SpecialLicense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QualityControlController extends Controller
{
    public static $info = [
        "route" => "fabric_raw.packing_form.quality_control.",
        "enable_status" => ["026",],
        "button" => ["caption" => "کنترل کیفیت", "class" => "btn-primary"],

    ];
    var $view_path = "goods_kind_process.fabric_raw.packing_form.quality_control.";
    var $route_path;
    var $dashboard_route = "fabric_raw.packing_form.";
    var $session_name = "packing_form_amount_";

    public function __construct()
    {
        $this->route_path = QualityControlController::$info["route"];
    }

    public function index(PackingForm $packing_form)
    {

        $result = $this->checkPermission($packing_form);
        if ($result != "") {
            return $result;
        }

        if ($packing_form->items->count() == 0) {
            return back()->withErrors("برای بسته بندی هیچ همبافتی یافت نشد.");
        }

        $data_row_result = $this->getRowData($packing_form);
        if (!$data_row_result["result"]) {
            return back()->withErrors($data_row_result["error"]);
        }
        $data_row = $data_row_result["data"];

        $jsn_data_list = JsonDataList::where(
            [
                "other_id" => $packing_form->id,
                "message_type_id" => 370,
            ])->first();

//        if ($jsn_data_list) {
//            return $this->control($packing_form);
//        }

        $jsn_data_list = JsonDataList::firstOrcreate(
            [
                "other_id" => $packing_form->id,
                "message_type_id" => 370,
            ],
            [
                "data" => json_encode($data_row)
            ]);

        $qc_data = json_decode($jsn_data_list->data, true);

        if (isset($qc_data["end_of_qc"])) {
            return redirect()->route($this->dashboard_route . "index")->with(["success" => "اطلاعات کنترل کیفیت در حال پردازش می باشد، لطفا چند لحظه دیگر بررسی فرمایید."]);
        }

        if (isset($qc_data["packing_type_id"]) && $qc_data["packing_type_id"] != "") {
            return $this->control($packing_form);
        }

        $post_ids = Setting::getStringValue("posts_allows_quality_control");
        $post_ids = json_decode($post_ids, true);
        $post_ids[] = -1;

        $user_ids = PostUser::whereIn("post_id", $post_ids)->pluck("user_id")->toArray();
        $user_ids[] = -1;
        $worker_option = Option::get("worker", 0, 0, $user_ids);

        $goods_kind_id = $packing_form->items()->first()->product->goods_kind_id;
        $packing_type_option = Option::get("packing_type", 0, $goods_kind_id);
        $degree_option = [];
        if (isset($qc_data["current_packing_form_item"][1]["product"]["goods_kind_id"])) {
            $goods_kind_id = $qc_data["current_packing_form_item"][1]["product"]["goods_kind_id"];
            $degree_option = Option::get("degree", 0, $goods_kind_id);
        }


        return view($this->view_path . "index", compact("packing_form", "worker_option", "packing_type_option", "degree_option"));

    }

    public function submit_partner(PackingForm $packing_form, Request $request)
    {
        $result = $this->checkPermission($packing_form);
        if ($result != "") {
            return $result;
        }

        if ($packing_form->items->count() == 0) {
            return back()->withErrors("برای بسته بندی هیچ آیتمی یافت نشد.");
        }

        $jsn_data_list = JsonDataList::where(
            [
                "other_id" => $packing_form->id,
                "message_type_id" => 370,
            ])->first();

        if (!$jsn_data_list) {
            return back()->withErrors("اطلاعات کنترل کیفیت نامعتر است، لطفا مجدد تلاش کنید.");
        }

        $qc_data = json_decode($jsn_data_list->data, true);

        if (isset($qc_data["end_of_qc"])) {
            return redirect()->route($this->dashboard_route . "index")->with(["success" => "اطلاعات کنترل کیفیت در حال پردازش می باشد، لطفا چند لحظه دیگر بررسی فرمایید."]);
        }

        $qc_data ["user_ids"] = $request->user_ids;
        $qc_data["packing_type_id"] = $request->packing_type_id;

        $jsn_data_list->data = json_encode($qc_data);
        $jsn_data_list->save();

        return redirect()->route($this->route_path . "control", $packing_form);
    }

    public function reset(PackingForm $packing_form)
    {
        $result = $this->checkPermission($packing_form);
        if ($result != "") {
            return $result;
        }

        if ($packing_form->items->count() == 0) {
            return back()->withErrors("برای بسته بندی هیچ آیتمی یافت نشد.");
        }
        $jsn_data_list = JsonDataList::where(
            [
                "other_id" => $packing_form->id,
                "message_type_id" => 370,
            ])->first();

        if (!$jsn_data_list) {
            return back()->withErrors("اطلاعات کنترل کیفیت نامعتر است، لطفا مجدد تلاش کنید.");
        }
        $qc_data = json_decode($jsn_data_list->data, true);

        if (isset($qc_data["end_of_qc"])) {
            return redirect()->route($this->dashboard_route . "index")->with(["success" => "اطلاعات کنترل کیفیت در حال پردازش می باشد، لطفا چند لحظه دیگر بررسی فرمایید."]);
        }

        $jsn_data_list->delete();


        return redirect()->route($this->route_path . "index", $packing_form);

    }

    public function control(PackingForm $packing_form)
    {
        $result = $this->checkPermission($packing_form);
        if ($result != "") {
            return $result;
        }

        if ($packing_form->items->count() == 0) {
            return back()->withErrors("برای بسته بندی هیچ آیتمی یافت نشد.");
        }

        $jsn_data_list = JsonDataList::where(
            [
                "other_id" => $packing_form->id,
                "message_type_id" => 370,
            ])->first();

        if (!$jsn_data_list) {
            return back()->withErrors("اطلاعات کنترل کیفیت نامعتر است، لطفا مجدد تلاش کنید.");
        }
        $qc_data = json_decode($jsn_data_list->data, true);
//        // اضافه کردن Token
//        $token = Auth::user()->createToken('web-token')->plainTextToken;
//        $qc_data["token"] = $token;
//        $jsn_data_list->data = json_encode($qc_data);
//        $jsn_data_list->save();

        // اگر وسط کار ثبت نقص آن را در نظر نگرفت آن را حذف می کنیم، اشتباه است ولی فعلا این قرار را می گذاریم.
        unset($qc_data["item_faults_current_properties"]);

//return $qc_data;
        if (isset($qc_data["end_of_qc"])) {
            return redirect()->route($this->dashboard_route . "index")->with(["success" => "اطلاعات کنترل کیفیت در حال پردازش می باشد، لطفا چند لحظه دیگر بررسی فرمایید."]);
        }
        $degree_option = [];

        if (isset($qc_data["current_packing_form_item"])) {
//            $goods_kind_id=-1;
//            foreach ($qc_data["current_packing_form_item"] as $key=> $c_item){
//
//                if(isset($qc_data["current_packing_form_item"][$key]["product"]["goods_kind_id"])){
//                    $goods_kind_id=$qc_data["current_packing_form_item"][$key]["product"]["goods_kind_id"];
//                    break;
//                }
//            }
//            if($goods_kind_id==-1){
//                return back()->withErrors("با توجه به باندهای ورودی، رسته کالایی قابل شناسایی نیست، لطفا با پشتیبانی تماس بگیرید.");
//            }

            $goods_kind_id = $packing_form->items()->first()->goods_kind_id;
            $degree_option = Option::get("degree", 0, $goods_kind_id);
        }

        $product_list = [];
        foreach ($qc_data["current_packing_form_item"] as $band_code => $item) {
            if (isset($item["product_id"])) {
                $product = Product::find($item["product_id"]);
                $product_list[$item["id"]] = $product;
            }
        }

        return view($this->view_path . "control", compact("product_list", "packing_form", "qc_data", "degree_option"));

    }

    public function end_of_qc(PackingForm $packing_form)
    {
        $result = $this->checkPermission($packing_form);
        if ($result != "") {
            return $result;
        }

        $new_packing_form_status = 7007020; // در انتظار تکمیل اطلاعات بسته بدی

        $jsn_data_list = JsonDataList::where(
            [
                "other_id" => $packing_form->id,
                "message_type_id" => 370,
            ])->first();

        if (!$jsn_data_list) {
            return back()->withErrors("اطلاعات کنترل کیفیت نامعتر است، لطفا مجدد تلاش کنید.");
        }

        $qc_data = json_decode($jsn_data_list->data, true);
        if (isset($qc_data["end_of_qc"])) {
            return redirect()->route($this->dashboard_route . "index")->with(["success" => "اطلاعات کنترل کیفیت در حال پردازش می باشد، لطفا چند لحظه دیگر بررسی فرمایید."]);
        }
        // قفط کردن اطلاعات کنترل کیفیت
        $qc_data["end_of_qc"] = 1;
        $jsn_data_list->data = json_encode($qc_data);
        $jsn_data_list->save();

        $end_of_qc = true;
        foreach ($qc_data["is_end_of_quality_control"] as $band_code => $value) {
            if ($value == false) {
                $end_of_qc = false;
            }
        }

        if (!$end_of_qc) {
            return back()->withErrors("فرایند کنترل کیفیت هنوز تکمیل نشده است.");
        }

        $parent_packing_form_item_list = $packing_form->items()->with("production_form_item", "product")->get()->keyBy("id");
        $packing_form_new_list = [];
        $amount_after_control_for_parent = [];
        $new_master_packing_form_item_list = []; // لیست آیتم های جدید را نگه می داریم تا بعدا نقص ها را به آنها اضافه کنیم.
        //  ثبت بسته بندی ها
        for ($band_code = 1; $band_code <= count($qc_data["new_packing_form"]); $band_code++) {

            $packing_number = 0;

            foreach ($qc_data["new_packing_form"][$band_code] as $band_code2 => $new_packing_form_item) {
                if ($packing_number != $new_packing_form_item["packing_form_number"]) {

                    $carrier = null;

                    $packing_form_new = PackingForm::create([
                        "packing_type_id" => $qc_data["packing_type_id"],
                        "carrier_id" => $carrier->id ?? null, //
                        "status_id" => $new_packing_form_status,
                        "degree_id" => $new_packing_form_item["degree_id"],
                        "packing_form_parent_id" => $packing_form->id
                    ]);

                    $packing_form_new->getCode();
                    $packing_form_new_list[] = $packing_form_new;
                    event(new PackingLogEvent($packing_form_new, "7007001"));
                    $packing_number = $new_packing_form_item["packing_form_number"];
                }
                if (!isset($amount_after_control_for_parent[$new_packing_form_item["packing_form_item_id"]])) {
                    $amount_after_control_for_parent[$new_packing_form_item["packing_form_item_id"]] = 0;
                }
                $parent_packing_form_item = $parent_packing_form_item_list[$new_packing_form_item["packing_form_item_id"]];

                $amount_after_control = $new_packing_form_item["end_point"] - $new_packing_form_item["start_point"];

                $new_master_packing_form_item = PackingFormItem::create([
                    "packing_form_id" => $packing_form_new->id,
                    "product_id" => $parent_packing_form_item->product_id,
                    'production_form_id' => isset($parent_packing_form_item["production_form_item"]) ? $parent_packing_form_item["production_form_item"]->production_form_id : null,
                    'production_form_item_id' => $parent_packing_form_item["production_form_item_id"],
                    "lot_number_id" => $parent_packing_form_item->lot_number_id,
                    "degree_id" => $new_packing_form_item["degree_id"],
                    "amount" => $amount_after_control,
                    "amount_after_control" => $amount_after_control,
                    "final_amount" => $amount_after_control,
                    "sub_amount" => $parent_packing_form_item->sub_amount * $amount_after_control / $parent_packing_form_item->final_amount,
                    "init_sub_amount" => $parent_packing_form_item->sub_amount * $amount_after_control / $parent_packing_form_item->final_amount,
                    "sub_amount2" => $parent_packing_form_item->product->frame_ratio_unit2 ? $amount_after_control / $parent_packing_form_item->product->frame_ratio_unit2 : null,
                    "status_id" => $new_packing_form_status,
                    "version_code" => $parent_packing_form_item->version_code,
                    "band_code" => 1
                ]);

                // پر کردن جدول آیتم های کنترل کیفیت انجام شده
                QualityControlPackingForm::create([
                    "packing_form_id" => $packing_form->id,
                    "packing_form_item_id" => $parent_packing_form_item->id,
                    "production_id" => 0,
                    'production_form_id' => isset($parent_packing_form_item["production_form_item"]->production_form_id) ? $parent_packing_form_item["production_form_item"]->production_form_id : 0,
                    'production_form_item_id' => isset($parent_packing_form_item["production_form_item"]->production_form_id) ? $parent_packing_form_item["production_form_item"]->id : 0,
                    "product_id" => $parent_packing_form_item->product_id,
                    "degree_id" => $new_packing_form_item["degree_id"],
                    "lot_number_id" => $parent_packing_form_item->lot_number_id,
                    "band_code" => $band_code,
                    "start_point" => $new_packing_form_item["start_point"],
                    "end_point" => $new_packing_form_item["end_point"],
                    "amount" => 0,
                    "final_amount" => 0,
                    "amount_after_control" => $amount_after_control,
                    "sub_amount" => $parent_packing_form_item->sub_amount * $amount_after_control / $parent_packing_form_item->final_amount,
                ]);

                $amount_after_control_for_parent[$new_packing_form_item["packing_form_item_id"]] += $amount_after_control;


                $new_master_packing_form_item->getCode();

                if (!isset($new_master_packing_form_item_list[$parent_packing_form_item->id])) {
                    $new_master_packing_form_item_list[$parent_packing_form_item->id] = [];
                }

                $new_master_packing_form_item_list[$parent_packing_form_item->id][] = $new_master_packing_form_item;

            }
        }


        // نقص های کالا
        for ($band_code = 1; $band_code <= count($qc_data["new_packing_form"]); $band_code++) {
//            echo "band_code:$band_code<br/>";
//            $packing_number = 0;

            if (isset($qc_data["item_faults"][$band_code])) {
                foreach ($qc_data["item_faults"][$band_code] as $product_fault_id => $item_fault_list) {

                    foreach ($item_fault_list as $item_fault) {

                        $parent_packing_form_item = $parent_packing_form_item_list[$item_fault["packing_form_item_id"]];

                        $product_fault_base = [
                            'packing_form_id' => $packing_form->id,
                            'packing_form_item_id' => $item_fault["packing_form_item_id"],
                            'production_form_id' => isset($parent_packing_form_item["production_form_item"]->production_form_id) ? $parent_packing_form_item["production_form_item"]->production_form_id : 0,
                            'production_form_item_id' => isset($parent_packing_form_item["production_form_item"]->production_form_id) ? $parent_packing_form_item["production_form_item"]->id : 0,
                            'product_id' => $parent_packing_form_item["product_id"],
                            "band_code" => $band_code,
                            'product_fault_id' => $product_fault_id,
                            'start_point' => isset($item_fault["start_point"]) ? $item_fault["start_point"] : null,
                            'end_point' => isset($item_fault["end_point"]) ? $item_fault["end_point"] : null,
                            'point' => isset($item_fault["point"]) ? $item_fault["point"] : null,
                            'fault_is_fixed' => isset($item_fault["product_fault_fixed_type_id"]) ? $item_fault["product_fault_fixed_type_id"] : null
                        ];

                        $quality_item = QualityControlProductFault::create($product_fault_base);

                        $start_point = 0;

                        // نقص ها به آیتم های جدید هم اضافه می شود.
                        foreach ($new_master_packing_form_item_list[$item_fault["packing_form_item_id"]] as $new_master_packing_form_item) {
                            $end_point = $start_point + $new_master_packing_form_item->final_amount;
                            // بررسی اینکه باید نقص به آیتم های جدید هم اضافه شود یا خیر
                            $b1 = $quality_item->start_poit && $start_point <= $quality_item->start_point && $end_point >= $quality_item->start_point;
                            $b2 = $quality_item->end_point && $start_point <= $quality_item->end_point && $end_point >= $quality_item->end_point;
                            $b3 = $quality_item->point && $start_point <= $quality_item->point && $end_point >= $quality_item->point;

                            if ($b1 || $b2 || $b3) {


                                $product_fault_base["packing_form_id"] = $new_master_packing_form_item->packing_form_id;
                                $product_fault_base["packing_form_item_id"] = $new_master_packing_form_item->id;
                                $product_fault_base["parent_quality_control_product_fault_id"] = $quality_item->id;

                                QualityControlProductFault::create($product_fault_base);
                            }

                            $start_point = $end_point;
                        }
                        // property

                        foreach ($qc_data["faults"] as $fault) {

                            if (isset($fault["properties"]) && $fault["id"] == $product_fault_id) {
                                foreach ($fault["properties"] as $property) {
                                    if (isset($item_fault["property_value"][$property["id"]])) {

                                        $property_value = [
                                            'quality_control_product_fault_id' => $quality_item->id,
                                            'packing_form_id' => $packing_form->id,
                                            'packing_form_item_id' => $item_fault["packing_form_item_id"],
                                            'production_form_id' => $parent_packing_form_item["production_form_item"]->production_form_id,
                                            'production_form_item_id' => $parent_packing_form_item["production_form_item_id"],
                                            'product_id' => $parent_packing_form_item["product_id"],
                                            'product_fault_id' => $product_fault_id,
                                            'product_fault_property_id' => $property["id"],
                                            'value' => $item_fault["property_value"][$property["id"]],
                                        ];

                                        QualityControlProductFaultPropertyValue::create($property_value);
                                    }
                                }
                            }
                        }

                    }
                }
            }
        }

        // همکاران
        if (isset($qc_data["user_ids"])) {
            foreach ($qc_data["user_ids"] as $user_id) {
                if ($user_id) {
                    $qc_user = [
                        'packing_form_id' => $packing_form->id,
                        'user_id' => $user_id,
                        'is_supervisor' => 0
                    ];
                    QualityControlUser::create($qc_user);

                }
            }
        }

        QualityControlUser::firstOrCreate([
            'packing_form_id' => $packing_form->id,
            'user_id' => Auth::id(),
        ],
            ['is_supervisor' => 1]);


//        foreach ($packing_form_new_list as $packing_form_new) {
//            $packing_form_new_for_weight = PackingForm::find($packing_form_new->id);
//            // بروزر رسانی وزن خالص و ناخالص
//            $result_weight = PackingForm::UpdateWeight($packing_form_new_for_weight);
//        }

        // بروز رسانی مقدار آیتم ها
        foreach ($parent_packing_form_item_list as $parent_packing_form_item) {
            $parent_packing_form_item->amount_after_control = $amount_after_control_for_parent[$parent_packing_form_item->id];
            $parent_packing_form_item->final_amount = $amount_after_control_for_parent[$parent_packing_form_item->id];
            $parent_packing_form_item->save();
        }
// تغییر وضعیت فرم تولید
        $packing_form->status_id = 7007007; //تغییر یافته
        $packing_form->warehouse_status_id = 4206; //تغییر یافته
        $packing_form->save();

// بروز رسانی مقدار سیستم برای بسته بندی های جدید
        $packing_form->updateAmount();

        event(new PackingLogEvent($packing_form, "7007037"));

        if ($packing_form->carrier) {
            $packing_form->carrier->SetEmpty();
        }
        $result_check = PackingForm::CheckChangePackingIsOK($packing_form);

        if (!$result_check["result"]) {
            return redirect()->route($this->dashboard_route . "index")->withErrors($result_check["error"]);

        }

        return redirect()->route($this->dashboard_route . "index")->with(["success" => "اطلاعات کنترل کیفیت با موفقیت ثبت گردید."]);


    }


    public
    function submit_change_api(Request $request)
    {
        $qc_final_amount = $request->qc_final_amount;
        $band_code = $request->band_code;
        $action_type = $request->action_type;
        $degree_option = [];
        $packing_form = PackingForm::find($request->packing_form_id);
        $jsn_data_list = JsonDataList::where(
            [
                "other_id" => $packing_form->id,
                "message_type_id" => 370,
            ])->first();

        $qc_data = json_decode($jsn_data_list->data, true);
        if ($request->qc_final_amount + 0 < $qc_data["last_amount_control"][$band_code]) {
            $error_message = "مقدار کنونی نمی تواند از آخرین متراژ ثبت شده کوچکتر باشد." . $qc_data["last_amount_control"][$band_code];
            return view($this->view_path . "_section_new", compact("error_message", "packing_form", "qc_data"));

        }

        switch ($action_type) {
            case "add_point_fault": // اضافه کردن عیب نقطه ای
                if (!isset($qc_data["item_faults"][$request->band_code])) {
                    $qc_data["item_faults"][$request->band_code] = [];
                }
                if (!isset($qc_data["item_faults"][$request->band_code][$request->product_fault_id])) {
                    $qc_data["item_faults"][$request->band_code][$request->product_fault_id] = [];
                }
                $qc_data["item_faults"][$request->band_code][$request->product_fault_id][] = [
                    "packing_form_item_id" => $qc_data["current_packing_form_item"][$request->band_code]["id"],
                    "point" => $qc_final_amount
                ];

                if (isset($qc_data["faults"][$request->product_fault_id]["properties"])) {
                    // نقص مشخصه دارد و باید مقدار همه مشخصه ها پرسیده شود.
                    $x = $qc_data["faults"][$request->product_fault_id]["properties"];

                    $qc_data["item_faults_current_properties"][$request->band_code]["properties"] =
                        $x;
                    $qc_data["item_faults_current_properties"][$request->band_code]["product_fault_id"] =
                        $request->product_fault_id;

                } //                elseif (in_array($qc_data["faults"][$request->product_fault_id]["product_fault_fixed_type_id"], [1, 3])) {
                else {

                    $qc_data["item_faults_current_properties"][$request->band_code]["properties"] = [];
                    $qc_data["item_faults_current_properties"][$request->band_code]["product_fault_id"] =
                        $request->product_fault_id;

                }

                break;
            case "add_start_point_fault": // اضافه کردن شروع عیب پیوسته
                if (!isset($qc_data["item_faults"][$request->band_code])) {
                    $qc_data["item_faults"][$request->band_code] = [];
                }
                if (!isset($qc_data["item_faults"][$request->band_code][$request->product_fault_id])) {
                    $qc_data["item_faults"][$request->band_code][$request->product_fault_id] = [];
                }
                $qc_data["item_faults"][$request->band_code][$request->product_fault_id][] = [
                    "packing_form_item_id" => $qc_data["current_packing_form_item"][$request->band_code]["id"],
                    "start_point" => $qc_final_amount
                ];
                break;
            case "add_end_point_fault": // اضافه کردن پایان عیب پیوسته
                if (!isset($qc_data["item_faults"][$request->band_code])) {
                    $qc_data["item_faults"][$request->band_code] = [];
                }
                if (!isset($qc_data["item_faults"][$request->band_code][$request->product_fault_id])) {
                    $qc_data["item_faults"][$request->band_code][$request->product_fault_id] = [];
                }

                // چک کردن صحت مقدار پایانی
                $last_amount_control = $qc_data["last_amount_control"][$band_code];
                if ($qc_final_amount - $last_amount_control <= 0) {
                    $error_message = "مقدار محاسبه شده برای نقص نامعتر است.";
                    $error_message .= "<br/>کنتور شروع نقص: " . $last_amount_control;
                    $error_message .= "<br/>کنتور پایان نقص: " . $qc_final_amount;
                    return view($this->view_path . "_section_new", compact("error_message", "packing_form", "qc_data"));

                }

                $count = count($qc_data["item_faults"][$request->band_code][$request->product_fault_id]) - 1;
                $qc_data["item_faults"][$request->band_code][$request->product_fault_id][$count]["end_point"] =
                    $qc_final_amount;

                if (isset($qc_data["faults"][$request->product_fault_id]["properties"])) {
                    // نقص مشخصه دارد و باید مقدار همه مشخصه ها پرسیده شود.
                    $x = $qc_data["faults"][$request->product_fault_id]["properties"];

                    $qc_data["item_faults_current_properties"][$request->band_code]["properties"] =
                        $x;
                    $qc_data["item_faults_current_properties"][$request->band_code]["product_fault_id"] =
                        $request->product_fault_id;
                } elseif (in_array($qc_data["faults"][$request->product_fault_id]["product_fault_fixed_type_id"], [1, 3])) {
                    $qc_data["item_faults_current_properties"][$request->band_code]["properties"] = [];
                    $qc_data["item_faults_current_properties"][$request->band_code]["product_fault_id"] =
                        $request->product_fault_id;
                }
                break;
            // تکمیل فرم مشخصات نقص
            case "form_faults_property":


                $count = count($qc_data["item_faults"][$request->band_code][$request->product_fault_id]) - 1;
                $qc_data["item_faults"][$request->band_code][$request->product_fault_id][$count]["property_value"] = json_decode($request->property_values);

                $qc_data["item_faults"][$request->band_code][$request->product_fault_id][$count]["product_fault_fixed_type_id"] = $request->product_fault_fixed_type_id;

                unset($qc_data["item_faults_current_properties"][$request->band_code]);
                break;

            case "form_degree":
                if (!isset($qc_data["degree_id_current"][$request->band_code])) {
                    $error_message = "نوع پایان آیتم برای باند " . $request->band_code . "مشخص نشده است";
                    return view($this->view_path . "_section_new", compact("error_message", "packing_form", "qc_data"));
                }

                $degree_id = $request->property_values;

                $result_btn_end = $this->btn_end_action($packing_form, $qc_data, $band_code, $degree_id, $qc_final_amount);
                if (!$result_btn_end["result"]) {
                    $error_message = $result_btn_end["error"];
                    $goods_kind_id = $qc_data["current_packing_form_item"][$band_code]["product"]["goods_kind_id"];
                    $degree_option = Option::get("degree", 0, $goods_kind_id);

                    return view($this->view_path . "_section_new", compact("error_message", "packing_form", "qc_data", "degree_option"));

                }

                $qc_data = $result_btn_end["qc_data"];
                $qc_data["degree_id_current"][$band_code] = null;


                break;
            // پایان آیتم
            case "btn_end_item":
            case "btn_end_item_new_packing":
                // بررسی درصد جمع شدگی
                if (!isset($qc_data["new_packing_form"][$request->band_code])) {
                    $error_message = "شناسه باند نامعتبر است." . $request->band_code;
                    return view($this->view_path . "_section_new", compact("error_message", "packing_form", "qc_data"));
                }
                $new_packing_form_count = count($qc_data["new_packing_form"][$band_code]);

                // چک کردن صحت مقدار پایانی
                $start_point = $qc_data["new_packing_form"][$band_code][$new_packing_form_count - 1]["start_point"];
                $packing_form_item_amount = $qc_final_amount - $start_point;

                $current_packing_form_item = $qc_data["current_packing_form_item"][$band_code];
                $current_packing_form_item = PackingFormItem::find($current_packing_form_item["id"]);

                // چک کردن درصد جمع شدگی
                $max = Setting::getDoubleValue("max_shrinkage_percent");
                $min = Setting::getDoubleValue("min_shrinkage_percent");

                // اضافه کردن مقدار آیتم های قبلی به مقدار نهایی آیتم
                $packing_form_item_amount_shrinkage = $packing_form_item_amount;
                for ($k = 0; $k < $new_packing_form_count - 1; $k++) {
                    if ($qc_data["new_packing_form"][$band_code][$k]["packing_form_item_id"] == $qc_data["new_packing_form"][$band_code][$new_packing_form_count - 1]["packing_form_item_id"])
                        $packing_form_item_amount_shrinkage +=
                            $qc_data["new_packing_form"][$band_code][$k]["end_point"] -
                            $qc_data["new_packing_form"][$band_code][$k]["start_point"];
                }

                $special_list_data = ChangePackingController::HasSpecialLicense($packing_form);
                $shrinkage_percent = PackingFormItem::final_shrinkage_percent($current_packing_form_item, $packing_form_item_amount_shrinkage);
                $message = "مقدار واقعی " . $current_packing_form_item->product->caption . " برابر با " . $current_packing_form_item->amount . " " . $current_packing_form_item->product->unit->caption . " می باشد و مقدار   " . $packing_form_item_amount_shrinkage . " " . $current_packing_form_item->product->unit->caption . " برای این کالا در تغییر بسته بندی قابل قبول نمی باشد. "// ."<br/> درصد جمع شدگی:".round($shrinkage_percent).",min=$min,max=$max"
                ;

                if (isset($special_list_data[$current_packing_form_item->product_id][$band_code]["max"])) {
                    $max = $special_list_data[$current_packing_form_item->product_id][$band_code]["max"];
                }
                $product_shrinkage_info[] = [
                    "product_id" => $current_packing_form_item->product_id,
                    "final_amount" => $current_packing_form_item->amount,
                    "new_amount" => $packing_form_item_amount,
                    "band" => $band_code,
                ];

                if (round($shrinkage_percent) > round($max)) {
                    // ذخیره اطلاعات چون در سمت مجوز به آنها نیاز داریم.
                    $qc_data["product_shrinkage_info"] = $product_shrinkage_info;

                    $jsn_data_list->data = json_encode($qc_data);
                    $jsn_data_list->save();

                    $qc_data["last_amount_control"][1] = $qc_final_amount;
                    $error_message = $message . "<br/>" .
                        ($shrinkage_percent >= 0 ? // ثبت مجوز فقط برای جمع شدگی های مثبت است.
                            SpecialLicense::GetLink(10, $packing_form->id, "ثبت درخواست مجوز جهت تغییر بسته بندی  ", $packing_form->id)
                            :
                            ""
                        );
                    return view($this->view_path . "_section_new", compact("error_message", "packing_form", "qc_data"));
                }

                if (isset($special_list_data[$current_packing_form_item->product_id][$band_code]["min"])) {
                    $min = $special_list_data[$current_packing_form_item->product_id][$band_code]["min"];
                }
                if ($shrinkage_percent < $min) {

                    // ذخیره اطلاعات چون در سمت مجوز به آنها نیاز داریم.
                    $qc_data["product_shrinkage_info"] = $product_shrinkage_info;

                    $jsn_data_list->data = json_encode($qc_data);
                    $jsn_data_list->save();
                    $qc_data["last_amount_control"][1] = $qc_final_amount;
                    $error_message = $message . "<br/>" .
                        ($shrinkage_percent >= 0 ? // ثبت مجوز فقط برای جمع شدگی های مثبت است.
                            SpecialLicense::GetLink(10, $packing_form->id, "ثبت درخواست مجوز جهت تغییر بسته بندی  ", $packing_form->id)
                            :
                            ""
                        );
                    return view($this->view_path . "_section_new", compact("error_message", "packing_form", "qc_data"));

                }
            // جهت چک کردن
//            $qc_data["last_amount_control"][1]=$qc_final_amount;
//            $error_message="test ok:$shrinkage_percent, min:$min, max:$max";
//            return view($this->view_path . "_section_new", compact("error_message", "packing_form", "qc_data"));

            case "btn_end_new_packing":
            case "btn_end":

                if (!isset($qc_data["new_packing_form"][$request->band_code])) {
                    $error_message = "شناسه باند نامعتبر است." . $request->band_code;
                    return view($this->view_path . "_section_new", compact("error_message", "packing_form", "qc_data"));
                }
                $new_packing_form_count = count($qc_data["new_packing_form"][$band_code]);

                // چک کردن صحت مقدار پایانی
                $start_point = $qc_data["new_packing_form"][$band_code][$new_packing_form_count - 1]["start_point"];
                if ($qc_final_amount - $start_point <= 0) {
                    $error_message = "مقدار محاسبه شده برای آیتم نامعتر است.";
                    $error_message .= "<br/>کنتور شروع آیتم: " . $start_point;
                    $error_message .= "<br/>کنتور پایان آیتم: " . $qc_final_amount;
                    return view($this->view_path . "_section_new", compact("error_message", "packing_form", "qc_data"));

                }

                // پر کردن مقدار پایانی
                $qc_data["new_packing_form"][$band_code][$new_packing_form_count - 1]["end_point"] = $qc_final_amount + 0;

                $qc_data["degree_id_current"][$band_code] = $action_type;
                $goods_kind_id = $qc_data["current_packing_form_item"][$band_code]["product"]["goods_kind_id"];
                $degree_option = Option::get("degree", 0, $goods_kind_id);
                break;
        }

        $qc_data["last_amount_control"][1] = $qc_final_amount;

        // آیا کنترل کیفیت به اتمام رسیده یا خبر


        $jsn_data_list->data = json_encode($qc_data);
        $jsn_data_list->save();


        return view($this->view_path . "_section_new", compact("packing_form", "qc_data", "degree_option"));

    }

    public
    function btn_end_action(PackingForm $packing_form, $qc_data, $band_code, $degree_id, $qc_final_amount)
    {
        $degree_id = trim($degree_id, "\"");
        $degree = Degree::find($degree_id);
        if (!$degree) {
            return [
                "result" => false,
                "error" => "لطفا درجه کالا را انتخاب کنید."
            ];
        }
        $btn_end_type = $qc_data["degree_id_current"][$band_code];
        $new_packing_form_count = count($qc_data["new_packing_form"][$band_code]);
        $current_packing_form_item = $qc_data["current_packing_form_item"][$band_code];
        $packing_form_number = $qc_data["new_packing_form"][$band_code][$new_packing_form_count - 1]["packing_form_number"];
        $qc_data["new_packing_form"][$band_code][$new_packing_form_count - 1]["degree_id"] = $degree_id;
        $qc_data["new_packing_form"][$band_code][$new_packing_form_count - 1]["degree"] = Degree::where("id", $degree_id)->first();
        $get_new_item = false;
        switch ($btn_end_type) {
            case "btn_end_item": // پایان آیتم
                // پر کردن مقدار پایانی
                $get_new_item = true;
                $qc_data["contour_of_band"][$band_code] = $qc_final_amount;
                break;
            case "btn_end_item_new_packing":
                $packing_form_number++;
                $get_new_item = true;
                $qc_data["contour_of_band"][$band_code] = $qc_final_amount;
                break;
            case "btn_end_new_packing":
                $packing_form_number++;
                break;
            case "btn_end":
                break;
        }


        if ($get_new_item) {
            //آیتم بعدی را می گیریم.
            $next_packing_form_item =
                $packing_form->items()->
                where("band_code", $band_code)->
                // اگر به صورت LIFO است نزولی و آیدی بعدی باید کوچکتر باشد
                // اگر به صورت FiFo است صعدی و آیتم بعدی باید بزرگتر باشد.
                where("packing_form_item.id", $qc_data["discharge_type_order"] == "desc" ? "<" : ">", $current_packing_form_item["id"])->
                orderBy("id", $qc_data["discharge_type_order"])->
                with("product")->
                first();
        } else {
            $next_packing_form_item = PackingFormItem::where("id", $qc_data["current_packing_form_item"][$band_code]["id"])->
            with("product")->
            first();
        }


        $packing_form_item_remaining = $packing_form->items()->
        where("band_code", $band_code)->
        // اگر به صورت LIFO است نزولی و آیدی بعدی باید کوچکتر باشد
        // اگر به صورت FiFo است صعدی و آیتم بعدی باید بزرگتر باشد.
        where("packing_form_item.id", $qc_data["discharge_type_order"] == "desc" ? "<" : ">", $current_packing_form_item["id"])->
        orderBy("id", $qc_data["discharge_type_order"])->
        with("product")->
        count();

        $qc_data["is_last_packing_form_item"][$band_code] = $packing_form_item_remaining <= 1;

        if (!$next_packing_form_item || ($btn_end_type == "btn_end_item" && $packing_form_item_remaining == 0)) {
            $qc_data["current_packing_form_item"][$band_code] = null;
            $qc_data["is_end_of_quality_control"][$band_code] = true;
        } else {
            $qc_data["new_packing_form"][$band_code][$new_packing_form_count] =
                [
                    "packing_form_number" => $packing_form_number, // شماره بسته بندی از 1 شروع می کنیم و در هر باز بسته بندی جدید یکی اضافه می شود.
                    "packing_form_item_id" => $next_packing_form_item->id,
                    "start_point" => $qc_final_amount,
                    "end_point" => null,
                    "degree_id" => null,
                ];
            $qc_data["current_packing_form_item"][$band_code] = $next_packing_form_item;
        }


        return [
            "result" => true,
            "qc_data" => $qc_data,
        ];

    }

    public
    function getRowData(PackingForm $packing_form)
    {
        $data = [
            "unit_caption" => "",
            "items" => [], // آیتم های بسته بندی مبدا
            "faults" => [], // همه نقص های کالا
            "packing_form" => "",
            "last_amount_control" => [], // آخرین مقداری که به ازای هر باند کنترل شده
            "item_faults" => [],// عیب های ثبت شده به تفکیک هر باند
            "item_faults_current_properties" => [], // مشخصاتی از هر باند که اکنون باید پرسیده شود
            "current_packing_form_item" => [], // آیتم جاری بسته بدی مبدا
            "is_last_packing_form_item" => [], // آیا این آخرین آیتم فرم ورودی است؟
            "degree_id_current" => [], // مشخصات درجه که الان باید پرسیده شود.(مقدار فیلد نوع عملیات را مشخص می کند)
            "discharge_type_order" => "",
            "is_end_of_quality_control" => [], //آیا پایان بسته بندی شده است؟
            "contour_of_band" => [], // کنتور هر باند که تاکنون پیمایش شده است.
            "max_shrinkage_percent" => Setting::getDoubleValue("max_shrinkage_percent"),
            "min_shrinkage_percent" => Setting::getDoubleValue("min_shrinkage_percent")
        ];


        foreach ($packing_form->items as $item) {
            //محاسبه کانال تولید سطح بالای آیتم ها
            $production_channel_caption = "";
            $production_item_list = [];
            $parent_product = $item->production_form_item->production->parent_production->product ?? null;
            if ($parent_product) {
                foreach ($parent_product->line_product_station as $line_item) {
                    $production_item_list[$line_item->production_channel_type_id] = $line_item->production_channel_type->caption;
                }
            }
            foreach ($production_item_list as $production_channel_item) {
                $production_channel_caption .= $production_channel_item;
                if (count($production_item_list) > 1) {
                    $production_channel_caption .= "<br/>";
                }
            }

            $data["items"][$item->band_code][$item->id] = [
                "final_amount" => $item->final_amount,
                "lot_number_id" => $item->lot_number_id,
                "band_code" => $item->band_code,
                "packing_form_item_id" => $item->id,
                "parent_production_channel_type_id" => $production_channel_caption
            ];
        }

        $discharge_type = $packing_form->packing_type->discharge_type; // نوع تخلیه بسته بندی
        if (!$discharge_type) {
            return [
                "result" => false,
                "error" => "در تعریف نوع بسته بندی " . $packing_form->packing_type->caption . " نوع تخلیه مشخص نشده است."
            ];
        }
        $discharge_type_order = DischargeType::GetOrder($discharge_type->id);
        $data["discharge_type_order"] = $discharge_type_order;

        foreach ($data["items"] as $band_code => $item_band_code) {
//            $band_code += 0;

            // اولین آیتمی که باید کنترل شود با توجه به نوع تخلیه بسته بندی
            $data["current_packing_form_item"][$band_code] = $packing_form->items()->
            where("band_code", $band_code)->
            orderBy("id", $discharge_type_order)->with("product")->
            first();

            // آیا این آیتم آخرین آیتم بسته بندی هست؟
            $data["is_last_packing_form_item"][$band_code] = $packing_form->items()->
                where("band_code", $band_code)->count() == 1;

            $data["last_amount_control"][$band_code] = 0;


            // ایجاد اولین بسته بندی جدید
            $data["new_packing_form"][$band_code][0] =
                // اولین آیتم بسته بندی
                [
                    "packing_form_number" => 1, // شماره بسته بندی از 1 شروع می کنیم و در هر باز بسته بندی جدید یکی اضافه می شود.
                    "packing_form_item_id" => $data["current_packing_form_item"][$band_code]["id"],
                    "start_point" => 0,
                    "end_point" => null,
                    "degree_id" => null,
                ];

            $data["is_end_of_quality_control"][$band_code] = false;

            // کنتور هر باند که تاکنون شماره شده است.
            $data["contour_of_band"][$band_code] = 0;
        }

        $goods_kind = $packing_form->items()->first()->product->goods_kind;

        $data["unit_caption"] = $packing_form->items()->first()->product->unit;

        // تهیه لیست همه نقص های کالا و مشخصات نقص ها
        foreach ($goods_kind->product_fault as $goods_kind_product_fault) {
            $faults = $goods_kind_product_fault->product_fault;
            if (!$faults || count($faults) == 0) {
                return
                    [
                        "result" => false,
                        "error"=>" لیست نقص ها برای رسته کالایی کالا انتخاب نشده است."
                    ];

            }
            $data["faults"][$goods_kind_product_fault->product_fault_id] = $faults->toArray();;

            $product_fault_properties = Product\Fault\ProductFaultProperties::
            join("product_fault_product_fault_properties", "product_fault_property_id", "product_fault_properties.id")->
            where("product_fault_id", $goods_kind_product_fault->product_fault_id)->
            select("product_fault_properties.*")->
            get();
            if (count($product_fault_properties) > 0) {
                $data["faults"][$goods_kind_product_fault->product_fault_id]["properties"] =
                    $product_fault_properties->toArray();
            }

            foreach ($product_fault_properties as $product_fault_property) {
                $data["fault_property_option"][$product_fault_property->id] =
                    Option::get("product_fault_property_options", 0, $product_fault_property->id);

            }

        }
        return [
            "result" => true,
            "data" => $data
        ];
    }

    public
    function checkPermission(PackingForm $packing_form)
    {

        $result = FabricRaw\PackingFormController::checkPermissionConditions($packing_form, self::$info);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }

        return "";
    }


}
