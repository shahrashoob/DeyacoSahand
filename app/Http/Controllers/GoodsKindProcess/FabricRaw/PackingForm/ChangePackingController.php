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
use App\Models\LineProduct\Packing\PackingType;
use App\Models\LineProduct\Product;
use App\Models\Utility\Option;
use App\Models\Utility\Setting;
use App\Models\Utility\SpecialLicense\SpecialLicense;
use Illuminate\Http\Request;

class ChangePackingController extends Controller
{
    public static $info = [
        "route" => "fabric_raw.packing_form.change_packing.",
        "enable_status" => ["005", "010", "013", "020","026"],
        "button" => ["caption" => "تغییر بسته بندی", "class" => "btn-primary"],

    ];
    var $view_path = "goods_kind_process.fabric_raw.packing_form.change_packing.";
    var $route_path;
    var $dashboard_route = "fabric_raw.packing_form.";
    var $session_name = "packing_form_amount_";

    public function __construct()
    {
        $this->route_path = ChangePackingController::$info["route"];
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


        session([
            $this->session_name . "packing_item_temp" => null,
            $this->session_name . "latest_packing_form_item" => null
        ]);

        return view($this->view_path . "index", compact("packing_form"));

    }


    public function submit(Request $request, PackingForm $packing_form)
    {


        $result = $this->checkPermission($packing_form);
        if ($result != "") {
            return $result;
        }
        $final_amount_list = [];
        $packing_form_item_amount = $request->data["packing_form_item"];
        $max = Setting::getDoubleValue("max_shrinkage_percent");
        $min = Setting::getDoubleValue("min_shrinkage_percent");

        $special_list_data = self::HasSpecialLicense($packing_form);

        $product_shrinkage_info = [];
        $message_error = "";
        $min_shrinkage_percent = 0;
        // در صورتی که دو تکه از یک کالا با لات مثل هم روی یک باند قرار داشته باشند، آنها را با هم جمع می شوند.
        foreach ($packing_form->items as $item) {

            if (!isset($final_amount_list[$item->band_code][($item->lot_number->id ?? 0)])) {
                $final_amount_list[$item->band_code][($item->lot_number->id ?? 0)] = 0;
            }
            $final_amount_list[$item->band_code][($item->lot_number->id ?? 0)] += $packing_form_item_amount[$item->id];

            $shrinkage_percent = PackingFormItem::final_shrinkage_percent($item, $packing_form_item_amount[$item->id]);
            $message = "مقدار واقعی " . $item->product->caption . " برابر با " . $item->final_amount . " " . $item->product->unit->caption . " می باشد و مقدار   " . $packing_form_item_amount[$item->id] . " " . $item->product->unit->caption . " برای این کالا در تغییر بسته بندی قابل قبول نمی باشد. ";

            if (isset($special_list_data[$item->product_id][$item->band_code]["max"])) {
                $max = $special_list_data[$item->product_id][$item->band_code]["max"];
            }

            if (round($shrinkage_percent) > round($max)) {
              //  return $shrinkage_percent."-".$max;// 26.95 -26.92
                $product_shrinkage_info[] = [
                    "product_id" => $item->product_id,
                    "final_amount" => $item->amount,
                    "new_amount" => $packing_form_item_amount[$item->id],
                    "band" => $item->band_code,
                ];
                $message_error .= $message . "<br/>";

            }
            if (isset($special_list_data[$item->product_id][$item->band_code]["min"])) {
                $min = $special_list_data[$item->product_id][$item->band_code]["min"];
            }
            if ($shrinkage_percent < $min) {
                $product_shrinkage_info[] = [
                    "product_id" => $item->product_id,
                    "final_amount" => $item->amount,
                    "new_amount" => $packing_form_item_amount[$item->id],
                    "band" => $item->band_code,
                ];
                $message_error .= $message . "<br/>";
            }

            if ($min_shrinkage_percent > $shrinkage_percent) {
                $min_shrinkage_percent = $shrinkage_percent;
            }
        }

        if ($message_error != "") {
            session(["product_shrinkage_info" => $product_shrinkage_info]);
            return back()->withErrors(
                $message_error .
                ($min_shrinkage_percent >= 0 ? // ثبت مجوز فقط برای جمع شدگی های مثبت است.
                    SpecialLicense::GetLink(10, $packing_form->id, "ثبت درخواست مجوز جهت تغییر بسته بندی  ", $packing_form->id)
                    :
                    ""
                )
            );
        }


        $final_amount = 0;
        foreach ($final_amount_list as $band) {
            foreach ($band as $amount) {
                if ($amount < .0001) {
                    return back()->withErrors("لطفا عدد معتبر برای واحد کالا وارد نمایید.");
                }
                $final_amount += $amount;
            }
        }

        // چک کردن حداقل و حداکثر جمع شدگی مجاز کل
        $shrinkage_percent = $packing_form->final_shrinkage_percent($final_amount);
        if (round($shrinkage_percent) > round($max)) {
            return back()->withErrors("درصد جمع شدگی  برای کالا بیش از مقدار قابل قبول است.");
        }
        if ($shrinkage_percent < $min) {
            1 / 0;

            return back()->withErrors("درصد جمع شدگی  برای کالا کمتر از حداقل  مقدار قابل قبول است.");
        }


        session([
            $this->session_name . $packing_form->id => $final_amount_list
        ]);

        //حذف همه رکوردهای معلق درجه بندی
        PackingFormItem::where([
            "packing_form_id" => $packing_form->id,
            "status_id" => 7007006
        ])->delete();


        return redirect()->route($this->route_path . "section", [$packing_form, 1]);
    }


    public function section(PackingForm $packing_form, $band_code)
    {
        $result = $this->checkPermission($packing_form);
        if ($result != "") {
            return $result;
        }


        // ایجاد یک رکورد در صورت جدید بودن
        $packing_item_temp = session($this->session_name . "packing_item_temp");

//        $packing_item_temp = null;
        // ایجاد سشن
        if (!isset($packing_item_temp) || count($packing_item_temp) == 0) {
            $latest_packing_item = $packing_form->items()->orderByDesc("id")->where(["band_code" => $band_code])->first();
            $new = $latest_packing_item->replicate();
            $new->id = null;
            $new->before_final_amount=$new->final_amount;
            $new->amount = 0;
            $new->amount_after_control = 0;
            $new->final_amount = 0;
            $new->band_code = $band_code;
            $new->target_band_code = 1;
            $new->row_number = 0;
            $new->packing_form_item_id = $latest_packing_item->id;

            $latest_packing_item->row_number = 0;
            session([
                $this->session_name . "packing_item_temp" => [0 => $new],
                $this->session_name . "latest_packing_form_item" => $latest_packing_item
            ]);
            $packing_item_temp = session($this->session_name . "packing_item_temp");

        }


        $latest_packing_item_temp = $packing_item_temp[count($packing_item_temp) - 1];

        // باد جدید
        if ($latest_packing_item_temp->band_code != $band_code) {
            $latest_packing_item = $packing_form->items()->orderByDesc("id")->where(["band_code" => $band_code])->first();
            $new = $latest_packing_item->replicate();
            $new->id = null;
            $new->before_final_amount=$new->final_amount;
            $new->amount = 0;
            $new->amount_after_control = 0;
            $new->final_amount = 0;
            $new->band_code = $band_code;
            $new->target_band_code = 1;
            $new->packing_form_item_id = $latest_packing_item->id;
            $new->row_number = count($packing_item_temp);

            $packing_item_temp[count($packing_item_temp)] = $new;


            session([
                $this->session_name . "packing_item_temp" => $packing_item_temp,
                $this->session_name . "latest_packing_form_item" => $latest_packing_item
            ]);

            return redirect()->route($this->route_path . "section", [$packing_form, $band_code]);
        }


// مقدار کل و مقدار کل هر باند
        // مقدار کل به تفکیک هر باند
        $final_amount_list = session($this->session_name . $packing_form->id);
        if (!$final_amount_list) {
            return redirect()->route($this->dashboard_route . "index")->withErrors("نشست شما به پایان رسیده لطفا دوباره تلاش کنید.");
        }

        // مقدار کل باند جاری
        $sum_final_amount_bands = 0;
        for ($k = 1; $k <= count($final_amount_list); $k++) {
            $sum_final_amount_bands += array_sum($final_amount_list[$k]);
        }

        $sum_final_amount = array_sum($final_amount_list[$band_code]);


        //آخرین رکورد بسته بندی که مورد پردازش قرار گرفته
        $latest_packing_form_item_real = session($this->session_name . "latest_packing_form_item");

        $latest_packing_sum_before = 0;
        $latest_packing_sum_before_band = 0;
        $latest_packing_sum_same = 0;

        foreach ($packing_item_temp as $item) {
            if ($item->band_code == $band_code) {
                $latest_packing_sum_before += $item->final_amount;
                if ($item->lot_number_id == $latest_packing_item_temp->lot_nameber_id) {
                    $latest_packing_sum_same += $item->final_amount;
                }
                if ($item->row_number < $latest_packing_form_item_real->row_number &&
                    ( // آیتم های فرم تولید برابر نباشد(برای اینکه دوبار حساب نکند)، اگر برابر بودند باید لات های مختلف باشند.
                        $item->production_form_item_id != $latest_packing_form_item_real->production_form_item_id ||
                        ($item->production_form_item_id == $latest_packing_form_item_real->production_form_item_id &&
                            $item->lot_number_id != $latest_packing_form_item_real->lot_number_id
                        )
                    )

                ) {
                    $latest_packing_sum_before_band += $item->final_amount;
                }

            }
        }


        $degree_option = Option::get("degree", 0, $latest_packing_item_temp->product->goods_kind_id);

//return $latest_packing_item_temp->product;


        $packing_type_option = Option::get("packing_type_product", 0, $latest_packing_item_temp->product->id);

        $packing_list = PackingType::
        join("goods_kind_packing_type", "packing_type_id", "packing_types.id")->
        select("packing_types.id", "packing_types.caption")->
        where("goods_kind_id", $latest_packing_item_temp->product->goods_kind_id)->
        get();

        $carrier_count_for_each_packing = [];
        $carrier_band_for_each_packing = [];
        $max_carrier = 0;
        $has_number_ability             = [];
        foreach ($packing_list as $item) {
            $carrier_count_for_each_packing[$item->id]["layer_count"] = 0;
            $max_carrier =
                $item->layers->count() > $max_carrier ?
                    $item->layers->count() : $max_carrier;

            foreach ($item->layers as $layer) {
                $carrier_count_for_each_packing[$item->id][$layer->layer_code] = isset($layer->carrier_type) ? 1 : 0;
                $carrier_count_for_each_packing[$item->id]["layer_count"] = isset($layer->carrier_type) ? 1 : 0;
                $carrier_band_for_each_packing[$item->id]["band_number"] = $layer->carrier_type->band_number ?? 1;
                //در صورتی که نوع حامل شماره پذیر نیست، حامل نمی گیرد.
                $has_number_ability[$item->id] =  $layer->carrier_type->has_number_ability ?? 0 ;

            }

        }

        $carrier_count_for_each_packing = json_encode($carrier_count_for_each_packing);
        $carrier_band_for_each_packing = json_encode($carrier_band_for_each_packing);
        $has_number_ability  = json_encode( $has_number_ability );
        $packing_item = $packing_form->items()->where(["band_code" => $band_code])->first();

        $option = [
            ["value" => 1, "text" => "بله"],
            ["value" => 2, "text" => "خیر", "selected" => 1]
        ];


        return view($this->view_path . "section", compact(
            "packing_item",
            "max_carrier",
            "has_number_ability",
            "carrier_count_for_each_packing",
            "carrier_band_for_each_packing",
            "packing_type_option",
            "latest_packing_item_temp",
            "latest_packing_sum_same",
            "latest_packing_sum_before",
            "latest_packing_sum_before_band",
            "packing_form",
            "packing_item_temp",
            "sum_final_amount",
            "sum_final_amount_bands",
            "degree_option",
            "final_amount_list", "option",
            "band_code"
        ));


    }

    public function submit_section(Request $request, PackingForm $packing_form, $band_code)
    {

        $result = $this->checkPermission($packing_form);
        if ($result != "") {
            return $result;
        }


        $final_amount_list = session($this->session_name . $packing_form->id);
        $sum_final_amount = array_sum($final_amount_list[$band_code]);

        $sum_amount = $packing_form->items()->sum("amount");


        //آخرین رکورد
        $packing_item_temp = session($this->session_name . "packing_item_temp");
        $latest_packing_item_temp = $packing_item_temp[count($packing_item_temp) - 1];

        $latest_packing_form_item_real = session($this->session_name . "latest_packing_form_item");

        $latest_packing_sum_before = 0;
        $latest_packing_sum_before_band = 0;
        $latest_packing_sum_same = 0;

        foreach ($packing_item_temp as $item) {
            if ($item->band_code == $band_code) {
                $latest_packing_sum_before += $item->final_amount;
                if ($item->lot_number_id == $latest_packing_item_temp->lot_nameber_id) {
                    $latest_packing_sum_same += $item->final_amount;
                }
                if ($item->row_number < $latest_packing_form_item_real->row_number &&
                    ( // آیتم های فرم تولید برابر نباشد(برای اینکه دوبار حساب نکند)، اگر برابر بودند باید لات های مختلف باشند.
                        $item->production_form_item_id != $latest_packing_form_item_real->production_form_item_id ||
                        ($item->production_form_item_id == $latest_packing_form_item_real->production_form_item_id &&
                            $item->lot_number_id != $latest_packing_form_item_real->lot_number_id
                        )
                    )
                ) {
                    $latest_packing_sum_before_band += $item->final_amount;
                }

            }
        }

//return $latest_packing_sum_before_band;
        $max = $latest_packing_sum_before_band + $final_amount_list[$band_code][$latest_packing_item_temp->lot_number_id];
//        if ( $request->end_point + 0 > $max ) {
//            return back()->withErrors( "متراژ وارد شده معتبر نمیباشد." );
//        }


        if (!isset($request->end_point) || round($request->end_point, 6) > round($sum_final_amount, 6)) {
            return back()->withErrors("مقدار پایانی نباید از متراژ کنترل خام بیشتر باشد.");
        }

        $degree = Degree::find($request->degree_id);
        if (!$degree) {
            return back()->withErrors("درجه معتبر نمی باشد");
        }

        // بررسی خالی بودن حامل
        // پیدا کردن نوع حامل از روی نوع بسته بندی
        $packing_type = PackingType::find($request->packing_type_id);
        if (!$packing_type) {
            return back()->withErrors("نوع بسته بندی معتبر نمی باشد");
        }

        $layer_count = $packing_type->layers->count();
        if ($layer_count == 0) {
            return back()->withErrors("تعریف لایه های بسته بندی معتبر نمی باشد، لطفا با پشتیبانی تماس بگیرید.");
        }

        $carrier_list = [];
        $k = 0;
        foreach ($packing_type->layers as $layer) {

            // اگر نوع حامل نال باشد، نیاز به چک کردن نداریم.
            if ($layer->carrier_type && $layer->carrier_type->has_number_ability) {
                $carrier_id = "carrier_id_" . $k;
                $k++;
                $result = Carrier::firstOrCreate($request->$carrier_id, $layer->carrier_type_id, 5320001, null);
                if (!$result["result"]) {
                    return back()->withErrors($result["message"]);
                }
                $carrier = $result["carrier"];
                if ($carrier->status_id != 5320001) {
                    return redirect()->back()->withErrors("شماره غلطک وارد شده خالی نیست، لطفا یک شماره غلطک خالی وارد کنید.");
                }
                $carrier_list[$request->$carrier_id] = $carrier;

                // بررسی درست بودن وزن بسته بندی
                $packing_type_weight_result = PackingType::getWeight($packing_type, $carrier);
                if (!$packing_type_weight_result["result"]) {
                    return redirect()->back()->withErrors($packing_type_weight_result["error"]);
                }
            } else {
                // بررسی درست بودن وزن بسته بندی
                $packing_type_weight_result = PackingType::getWeight($packing_type);
                if (!$packing_type_weight_result["result"]) {
                    return redirect()->back()->withErrors($packing_type_weight_result["error"]);
                }
            }
        }


        $latest_packing_item_temp->final_amount = $request->end_point - $latest_packing_sum_before;
        $latest_packing_item_temp->amount_after_control = $request->end_point - $latest_packing_sum_before;
        $latest_packing_item_temp->amount = ($latest_packing_item_temp->final_amount * $sum_amount) / $sum_final_amount;
        $latest_packing_item_temp->sub_amount = $latest_packing_item_temp->final_amount * $latest_packing_item_temp->product->weight;

        $latest_packing_item_temp->sub_amount2 = $latest_packing_item_temp->product->frame_ratio_unit2 ? $latest_packing_item_temp->final_amount / $latest_packing_item_temp->product->frame_ratio_unit2:null;

        $latest_packing_item_temp->before_final_amount=0;
        $latest_packing_item_temp->packing_type_id = $request->packing_type_id;
        $latest_packing_item_temp->packing_type_caption = $packing_type->caption;
        $latest_packing_item_temp->degree_id = $request->degree_id;
        $latest_packing_item_temp->degree_caption = $degree->caption;
        $latest_packing_item_temp->new_carrier_id = count($packing_item_temp) == 1 ? 1 : $request->new_carrier_id;

        $packing_item_temp[count($packing_item_temp) - 1] = $latest_packing_item_temp;

        // ذخیره موقت حامل ها
        $k = 0;
        $carrier_cods = "";
        foreach ($packing_type->layers as $layer) {
            // اگر نوع حامل نال باشد، نیاز به چک کردن نداریم.
            if ($layer->carrier_type && $layer->carrier_type->has_number_ability) {
                $carrier_id = "carrier_id_" . $k;
                $band_carrier_id = "band_carrier_id_" . $k;
                $layer_carrier_id = "layer_carrier_" . $layer->layer_code;
                $k++;

                $latest_packing_item_temp->$layer_carrier_id = $carrier_list[$request->$carrier_id]->id;
                $latest_packing_item_temp->target_band_code = $request->$band_carrier_id;

                $carrier_cods = $request->$carrier_id;
            }
        }
        $latest_packing_item_temp->carrier_codes = $carrier_cods;
//return $packing_item_temp;
//return $latest_packing_item_temp->final_amount ."+". $latest_packing_sum_before ."<". $sum_final_amount;
        if (round($latest_packing_item_temp->final_amount + $latest_packing_sum_before, 7) < round($sum_final_amount, 7)) {
            if (round($request->end_point, 6) != round($max, 6)) {
//return $request->end_point ."!=". $max;
                //ایجاد یک رکورد مشابه قبلی، چون احتمالا یک همبافت را دو تکه کردن
                $latest_packing_item_same = session($this->session_name . "latest_packing_form_item");
                $new = $latest_packing_item_same->replicate();
                $new->id = null;
                $new->before_final_amount=$new->final_amount;
                $new->amount = 0;
                $new->amount_after_control = 0;
                $new->final_amount = 0;
                $new->row_number = count($packing_item_temp);
                $new->packing_form_item_id = $latest_packing_item_same->packing_form_item_id;
                $latest_packing_item_same->row_number = $new->row_number;
                session([
                    $this->session_name . "latest_packing_form_item" => $latest_packing_item_same
                ]);

            } else {
//
                $latest_packing_item_same = session($this->session_name . "latest_packing_form_item");


                // اگر دو تکه کالا با لات های یکسان پست سر هم تولید شده بودند، باید بررسی کنیم که حداکثر به مقدار جمغ کل آنها بتواند تخصیص بدهد.
                $latest_packing_item = $packing_form->items()->
                where("band_code", $band_code)->
                where("id", "<", $latest_packing_item_same->id)->
                // اگر دو تکه با لات یکسان باشند، سامانه یک رکورد در نظر می گیرد.
                where("lot_number_id", "!=", $latest_packing_item_same->lot_number_id)->
                orderByDesc("id")->
                first();

                /****************************/
                //  ممکن است در دو کالایی که با لات مثل هم هستند پست سر هم نباشند، اگر این اتفاق بیفتد برای اولین بار مشکلی نیست و جمع کل آنها را در نظر می گیرد ولی
                // برای بار دوم باید از آن رد شود و به آیتم بعدی برسد.
                $before_checking_lot_number = false;
                foreach ($packing_item_temp as $item_packing_item_for_check) {
                    if (
                        $item_packing_item_for_check->lot_number_id == $latest_packing_item->lot_number_id &&
                        $item_packing_item_for_check->band_code == $latest_packing_item->band_code

                    ) {
                        $before_checking_lot_number = true;
                    }
                }
                if ($before_checking_lot_number) {

                    // قبلا این کالا - همبافت بررسی شده است، بنابراین دیگر لازم نیست آن را بررسی کنیم.
                    $latest_packing_item = $packing_form->items()->
                    where("band_code", $band_code)->
                    where("id", "<", $latest_packing_item_same->id)->
                    // اگر دو تکه با لات یکسان باشند، سامانه یک رکورد در نظر می گیرد.
                    where("lot_number_id", "!=", $latest_packing_item_same->lot_number_id)->
                    where("lot_number_id", "!=", $latest_packing_item->lot_number_id)->
                    orderByDesc("id")->
                    first();
                }
                /************************/
                if (!$latest_packing_item) {
                    return back()->withErrors("عملیات تغییر بسته بندی نامعتبر است، لطفا با پشتیبانی تماس بگیرید. ");
                }
                $new = $latest_packing_item->replicate();
                $new->id = null;
                $new->before_final_amount=$new->final_amount;
                $new->amount = 0;
                $new->amount_after_control = 0;
                $new->final_amount = 0;
                $new->band_code = $band_code;
                $new->packing_form_item_id = $latest_packing_item->id;

                $new->row_number = count($packing_item_temp);
                $latest_packing_item->row_number = $new->row_number;

                session([
                    $this->session_name . "latest_packing_form_item" => $latest_packing_item
                ]);
            }


            $packing_item_temp[] = $new;
            //  return  $latest_packing_item_same;
        }

        session([
            $this->session_name . "packing_item_temp" => $packing_item_temp
        ]);

        return redirect()->route($this->route_path . "section", [$packing_form, $band_code]);
    }

    public function end_of_section(PackingForm $packing_form)
    {

        $result = $this->checkPermission($packing_form);
        if ($result != "") {
            return $result;
        }

        $packing_item_temp = session($this->session_name . "packing_item_temp");

        if (!$packing_item_temp) {
            return back()->withErrors("لیست بسته بندی  ها یافت نشد.");
        }

        $packing_form_list = [];

        $packing_type_list = [];

        $new_packing_form_list = [];

        // گرفتن وضعیت فرم های بسته بندی جدید
        $new_packing_form_status = $this->getNewStatusId($packing_form);

// خالی کردن حامل
        if ($packing_form->carrier) {
            if ($packing_form->status_id == 7007013) {
                $packing_form->carrier->SetStatus(
                    5320006, // پر در حال تکمیل
                    null,
                    5320110,
                    null
                );
            } else {
                $packing_form->carrier->SetEmpty();
            }
        }

        // بررسی حامل ها
        foreach ($packing_item_temp as $item) {

            $k = 0;
            $packing_type = PackingType::find($item->packing_type_id);
            $has_carrier_type = false;
            $carrier = null;
            foreach ($packing_type->layers as $layer) {
                // اگر نوع حامل نال باشد، نیاز به چک کردن نداریم.
                if ($layer->carrier_type && $layer->carrier_type->has_number_ability) {

                    $layer_carrier_id = "layer_carrier_" . $layer->layer_code;
                    $k++;
                    $carrier = Carrier::find($item->$layer_carrier_id);
                    if ($carrier->status_id != 5320001) {
                        return redirect()->back()->withErrors("شماره غلطک وارد شده خالی نیست، لطفا یک شماره غلطک خالی وارد کنید.");
                    }
                    $has_carrier_type = true;


                }
            }
            $packing_type_list[$packing_type->id] = $packing_type;
            $packing_type_list[$packing_type->id]["has_carrier_type"] = $has_carrier_type;
            $packing_type_list[$packing_type->id]["carrier"] = $carrier;
//            $packing_type_list[ $packing_type->id ]["band_code"]        = $carrier;
        }

        $packing_form_new_log = [];

        // اختصاص شناسه بسته بندی به هر آیتم
        foreach ($packing_item_temp as $item) {
            $carrier = $packing_type_list[$item->packing_type_id]["carrier"];
            $item->code = null;

            if ($item->new_carrier_id == 1 || !isset($packing_form_list[$item->packing_type_id][$carrier->id ?? 0]["count"])) {//  نیاز به یک بسته بندی جدید می باشد.

                $packing_form_new = PackingForm::create([
                    "packing_type_id" => $item->packing_type_id,
                    "carrier_id" => $carrier->id ?? null, //
                    "status_id" => $new_packing_form_status,
                    "degree_id" => $item->degree_id,
                    "packing_form_parent_id" => $packing_form->id,
                    "applicant_type_id" => $packing_form->applicant_type_id??null,
                    "applicant_id" => $packing_form->applicant_id??null,
                ]);
                $new_packing_form_list[] = $packing_form_new;
                $packing_form_list[$item->packing_type_id][$carrier->id ?? 0] = $packing_form_new;
                $packing_form_list[$item->packing_type_id][$carrier->id ?? 0]["count"] = 0; // تعداد استفاده

                // تغییر وضعیت حامل
                if ($carrier) {
                    $carrier->SetStatus("5320007", null, 5320110);
                }
            }

            $item->packing_form_id = $packing_form_list[$item->packing_type_id][$carrier->id ?? 0]->id;
            $item->status_id = $new_packing_form_status;
            $item->band_code = $item->target_band_code ?? 1;

            //ایجاد ایتم بسته بندی جدید
            $packing_form_item = PackingFormItem::create($item->toArray());

            // ثبت لایه های بسته بندی
            PackingFormLayer::create([
                "packing_form_item_id" => $packing_form_item->id,
                "carrier_id" => $packing_form_new->carrier_id,
                "band_code" => $packing_form_item->band_code,
                "layer_code" => 1
            ]);

            $packing_form_list[$item->packing_type_id][$carrier->id ?? 0]["count"] += 1; // تعداد استفاده

            if (!isset($packing_form_new_log[$packing_form_new->id])) {
                event(new PackingLogEvent($packing_form_new, "7007001"));
                $packing_form_new_log[$packing_form_new->id] = $packing_form_new->id;
            }
            $packing_form_new_for_weight = PackingForm::find($packing_form_new->id);
            // بروزر رسانی وزن خالص و ناخالص
            $result_weight = PackingForm::UpdateWeight($packing_form_new_for_weight);
            if ($result_weight["result"]) {
                $packing_form_new_for_weight->weight = $result_weight["weight"];;
                $packing_form_new_for_weight->gross_weight = $result_weight["gross_weight"];
                $packing_form_new_for_weight->save();
            }
        }


        // تغییر وضعیت فرم تولید
        $packing_form->status_id = 7007007; //تغییر یافته
        $packing_form->warehouse_status_id = 4206; //تغییر یافته
        $packing_form->save();

        // بروز رسانی مقدار سیستم برای بسته بندی های جدید
        $packing_form->updateAmount();

        // بروز رسانی وزن خالص و ناخالص بسته بندی ها
        if ($new_packing_form_status != 7007020) { // تکیمل اطلاعات
            foreach ($new_packing_form_list as $packing_form_new_weight) {
                // بروزر رسانی وزن خالص و ناخالص
                $result_weight = PackingForm::UpdateWeight($packing_form_new_weight);
                if ($result_weight["result"]) {
                    unset($packing_form_new_weight->count);
                    $packing_form_new_weight->weight = $result_weight["weight"];;
                    $packing_form_new_weight->gross_weight = $result_weight["gross_weight"];
                    $packing_form_new_weight->save();
                }

            }
        }


        event(new PackingLogEvent($packing_form, "7007008"));

        $result_check = PackingForm::CheckChangePackingIsOK($packing_form);

        if (!$result_check["result"]) {
            return redirect()->route($this->dashboard_route . "index")->withErrors($result_check["error"]);

        }


        return redirect()->route($this->dashboard_route . "index")->with(["success" => "تغییر بسته بندی با موفقیت انجام شد."]);
    }

    public function checkPermission(PackingForm $packing_form)
    {

        $result = FabricRaw\PackingFormController::checkPermissionConditions($packing_form, ChangePackingController::$info);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }

        return "";
    }

    public function getNewStatusId(PackingForm $packing_form)
    {
        if (in_array($packing_form->status_id ,[ 7007020 ,7007026])) { // در انتظار تکمیل اطلاعات بسته بندی/ کنترل کیفیت
            return $packing_form->status_id;  /// در انتظار تکمیل اطلاعات بسته بندی / کنترل کیفیت
        } else {
            // اگر وضعیت بسته بندی در انتظار تغییر بسته بندی باشد و ماشین مربوطه تیک بررسی موجودی آن فعال باشد، باید بشود در انتظار تکمیل اطلاعات
            $new_packing_form_status = 7007005; // در انتظار تحویل به انبار
            $first_production_form_item = $packing_form->items()->first();
            $machine = $first_production_form_item->production_form_item->production_form->machine ?? null;
            if (isset($machine) && $machine->check_inventory_for_allocation && $packing_form->status_id == 7007013) {
                $new_packing_form_status = 7007020; //در انتظار تکمیل اطلاعات بسته بندی
            }

            return $new_packing_form_status;
        }
    }

    public static function HasSpecialLicense(PackingForm $packing_form)
    {

        $special_license_list = SpecialLicense::where([
            "special_license_type_id" => 10,
            "reference_id" => $packing_form->id,
            "status_id" => 6040002, // تایید شده
        ])->get();

        $list = [];
        foreach ($special_license_list as $special_license) {
            $json_data = $special_license->getObject1();
            foreach ($json_data["product_shrinkage_info"] as $item) {
                // return $item;
                $shrinkage_percent = round((1 - $item["new_amount"] / $item["final_amount"]) * 100, 2);
                // مشخص کردن حداکثر مقدار قابل قبول هر کالا - باند
                if ($item["final_amount"] < $item["new_amount"]) {
                    if (!isset($list[$item["product_id"]][$item["band"]]["min"])) {
                        $list[$item["product_id"]][$item["band"]]["min"] = 200;
                    }

                    $min = $shrinkage_percent;
                    if ($min < $list[$item["product_id"]][$item["band"]]["min"]) {
                        $list[$item["product_id"]][$item["band"]]["min"] = $min + 0;
                    }

                }
                // مشخص کردن حداقل مقدار قابل قبول هر کالا - باند
                if ($item["final_amount"] > $item["new_amount"]) {
                    if (!isset($list[$item["product_id"]][$item["band"]]["max"])) {
                        $list[$item["product_id"]][$item["band"]]["max"] = -200;
                    }
                    $max = $shrinkage_percent;
                    if ($max > $list[$item["product_id"]][$item["band"]]["max"]) {
                        $list[$item["product_id"]][$item["band"]]["max"] = $max + 0;
                    }

                }

            }
        }

        return $list;
    }


}
