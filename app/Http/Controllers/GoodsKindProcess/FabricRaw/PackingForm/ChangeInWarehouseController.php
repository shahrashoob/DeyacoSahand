<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\PackingForm;

use App\Events\Form\PackingLogEvent;
use App\Events\Warehouse\PutInWarehouseEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\FabricRaw;
use App\Models\Form\Form;
use App\Models\Form\FormItem;
use App\Models\Form\Packing\PackingForm;
use App\Models\Form\Packing\PackingFormItem;
use App\Models\Form\Packing\PackingFormLayer;
use App\Models\LineProduct\Carrier\Carrier;
use App\Models\LineProduct\Degree;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\Utility\Option;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChangeInWarehouseController extends Controller
{
    public static $info = [
        "route" => "fabric_raw.packing_form.change_in_warehouse.",
        "enable_status" => ["003"],
        "button" => ["caption" => "تغییر بسته بندی (انبار)", "class" => "btn-primary"],

    ];
    var $view_path = "goods_kind_process.fabric_raw.packing_form.change_in_warehouse.";
    var $route_path;
    var $dashboard_route = "fabric_raw.packing_form.";
    var $session_name = "packing_form_amount_";

    public function __construct()
    {
        $this->route_path = ChangeInWarehouseController::$info["route"];
    }

    public function index(PackingForm $packing_form)
    {

        $result = $this->checkPermission($packing_form);
        if ($result != "") {
            return $result;
        }

        if ($packing_form->items->count() == 0) {
            return back()->withErrors("برای بسته بندی هیچ آیتمی یافت نشد.");
        }

        if ($packing_form->warehouse_status_id != 4201) {
            return back()->withErrors("با توجه به اینکه " . ($packing_form->warehouse_status->caption ?? "") . "، امکان تغییر بسته بندی وجود ندارد.");
        }


        session([
            $this->session_name . "packing_item_temp" => null,
            $this->session_name . "latest_packing_form_item" => null
        ]);


        // در صورتی که بسته بندی موجود در انبار باشد، باید متراژ بسته بندی جدید و قدیم با هم برابر باشد.
        if ($packing_form->status_id == 7007003) {
            $final_amount_list = [];

            // در صورتی که دو تکه از یک کالا با لات مثل هم روی یک باند قرار داشته باشند، آنها را با هم جمع می شوند.
            foreach ($packing_form->items as $item) {

                if (!isset($final_amount_list[$item->band_code][($item->lot_number->id ?? 0)])) {
                    $final_amount_list[$item->band_code][($item->lot_number->id ?? 0)] = 0;
                }
                $final_amount_list[$item->band_code][($item->lot_number->id ?? 0)] += $item->final_amount;

            }

            session([
                $this->session_name . $packing_form->id => $final_amount_list,
            ]);

            //حذف همه رکوردهای معلق درجه بندی
            PackingFormItem::where([
                "packing_form_id" => $packing_form->id,
                "status_id" => 7007006
            ])->delete();

            return redirect()->route($this->route_path . "section", [$packing_form, 1]);
        }

        return view($this->view_path . "index", compact("packing_form"));

    }

//
//    public function submit( Request $request, PackingForm $packing_form ) {
//
//
//        $result = $this->checkPermission( $packing_form );
//        if ( $result != "" ) {
//            return $result;
//        }
//        $final_amount_list        = [];
//        $sum_final_amount         = 0;
//        $packing_form_item_amount = $request->data["packing_form_item"];
//
//        // در صورتی که دو تکه از یک کالا با لات مثل هم روی یک باند قرار داشته باشند، آنها را با هم جمع می شوند.
//        foreach ( $packing_form->items as $item ) {
//
//            if ( ! isset( $final_amount_list[ $item->band_code ][ ( $item->lot_number->id ?? 0 ) ] ) ) {
//                $final_amount_list[ $item->band_code ][ ( $item->lot_number->id ?? 0 ) ] = 0;
//            }
//            $final_amount_list[ $item->band_code ][ ( $item->lot_number->id ?? 0 ) ] += $packing_form_item_amount[ $item->id ];
//            $sum_final_amount                                                        += $packing_form_item_amount[ $item->id ];
//        }
//
//
//        foreach ( $final_amount_list as $band ) {
//            foreach ( $band as $amount ) {
//                if ( $amount < .01 ) {
//                    return back()->withErrors( "لطفا عدد معتبر برای متراژ پارچه وارد نمایید." );
//                }
//            }
//        }
//
//
//        session( [
//            $this->session_name . $packing_form->id => $final_amount_list
//        ] );
//
//        //حذف همه رکوردهای معلق درجه بندی
//        PackingFormItem::where( [
//            "packing_form_id" => $packing_form->id,
//            "status_id"       => 7007006
//        ] )->delete();
//
//
//        return redirect()->route( $this->route_path . "section", [ $packing_form, 1 ] );
//    }


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
            $new->amount = 0;
            $new->amount_after_control = 0;
            $new->final_amount = 0;
            $new->band_code = $band_code;
            $new->target_band_code = 1;
            $new->packing_form_item_id = $latest_packing_item->id;
            $new->row_number = count($packing_item_temp);
            $packing_item_temp[count($packing_item_temp)] = $new;

            $latest_packing_item->row_number = count($packing_item_temp);
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


        // return $latest_packing_item_temp;
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

//return $latest_packing_form_item_real;

        $packing_type_option = Option::get("packing_type_product", 0, $latest_packing_item_temp->product->id);

        $packing_list = PackingType::
        join("goods_kind_packing_type", "packing_type_id", "packing_types.id")->
        select("packing_types.id", "packing_types.caption")->
        where("goods_kind_id", $latest_packing_item_temp->product->goods_kind_id)->
        get();

        $carrier_count_for_each_packing = [];
        $carrier_band_for_each_packing = [];
        $max_carrier = 0;
        $has_number_ability = [];
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
                $has_number_ability[$item->id] = $layer->carrier_type->has_number_ability ?? 0;
            }

        }

        $carrier_count_for_each_packing = json_encode($carrier_count_for_each_packing);
        $carrier_band_for_each_packing = json_encode($carrier_band_for_each_packing);
        $has_number_ability = json_encode($has_number_ability);

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


        $max = $latest_packing_sum_before_band + $final_amount_list[$band_code][$latest_packing_item_temp->lot_number_id];
//        if ( $request->end_point + 0 > $max ) {
//            return back()->withErrors( "متراژ وارد شده معتبر نمیباشد." );
//        }


        if (!isset($request->end_point) || $request->end_point > $sum_final_amount) {
            return back()->withErrors("مقدار پایانی نباید از متراژ کنترل خام بیشتر باشد.");
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
            }
        }

        // درجه تغییر نمی کند
        $degree = Degree::find($latest_packing_item_temp->degree_id);

        $latest_packing_item_temp->final_amount = $request->end_point - $latest_packing_sum_before;
        $latest_packing_item_temp->amount_after_control = $request->end_point - $latest_packing_sum_before;
        $latest_packing_item_temp->amount = ($latest_packing_item_temp->final_amount * $sum_amount) / $sum_final_amount;
        $latest_packing_item_temp->sub_amount = $latest_packing_item_temp->final_amount * $latest_packing_item_temp->product->weight;
        $latest_packing_item_temp->sub_amount2 = $latest_packing_item_temp->product->frame_ratio_unit2 ? $latest_packing_item_temp->final_amount / $latest_packing_item_temp->product->frame_ratio_unit2:null;
        $latest_packing_item_temp->packing_type_id = $request->packing_type_id;
        $latest_packing_item_temp->packing_type_caption = $packing_type->caption;

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

        if ($latest_packing_item_temp->final_amount + $latest_packing_sum_before < $sum_final_amount) {
            if (round($request->end_point, 6) != round($max, 6)) {
                //ایجاد یک رکورد مشابه قبلی، چون احتمالا یک همبافت را دو تکه کردن
                $latest_packing_item_same = session($this->session_name . "latest_packing_form_item");
                $new = $latest_packing_item_same->replicate();
                $new->id = null;
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
                $latest_packing_item_same = session($this->session_name . "latest_packing_form_item");

                $latest_packing_item = $packing_form->items()->
                where("band_code", $band_code)->
                where("id", "<", $latest_packing_item_same->id)->
                // اگر دو تکه با لات یکسان پست سر هم باشند، سامانه یک رکورد در نظر می گیرد.
                where("lot_number_id", "!=", $latest_packing_item_same->lot_number_id)->
                orderByDesc("id")->
                first();

                /****************************/
                //  ممکن است در دو کالایی که با لات مثل هم هستند پست سر هم نباشند، اگر این اتفاق بیفتد برای اولین بار مشکلی نیست و جمع کل آنها را در نظر می گیرد ولی
                // برای بار دوم باید از آن رد شود و به آیتم بعدی برسد.
                $before_checking_lot_number = false;
                foreach ($packing_item_temp as $item_packing_item_for_check) {
                    if ($item_packing_item_for_check->lot_number_id == $latest_packing_item->lot_number_id) {
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

                $new = $latest_packing_item->replicate();
                $new->id = null;
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

    public function end_of_section(Request $request, PackingForm $packing_form)
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
            // $packing_type_list[ $packing_type->id ]["band_code"]        = $carrier;
        }

//        // ایجاد بسته بندی های جدید
//        foreach ( $packing_item_temp as $item ) {
//
//            $carrier = $packing_type_list[ $item->packing_type_id ]["carrier"];
//
//            if ( ! isset( $packing_form_list[ $item->degree_id ][ $item->packing_type_id ][ $carrier->id ?? 0 ] ) ) {
//
//                //فرض می کنیم که بسته بندی فقط یک لایه برای حامل داشته باشد، بنابراین اگر حداقل یکی از لایه ها حامل داشته باشد، آن را به عنوان حامل بسته بندی در نظر می گیریم.
//
//                $packing_form_new = PackingForm::create( [
//                    "packing_type_id"        => $item->packing_type_id,
//                    // شماره حامل بسته بندی فقط شماره حامل آخرین لایه است.
//                    "carrier_id"             => $carrier->id ?? null, //
//                    "status_id"              => 7007005, // در انتظار تحویل به انبار
//                    "degree_id"              => $item->degree_id,
//                    "packing_form_parent_id" => $packing_form->id
//                ] );
//
//                if ( $carrier ) {
//                    $carrier->addProduct( $item->product_id );
//                    $carrier->SetStatus( 5320004, "تغییر بسته بندی" . $packing_form_new->getCode(), 5320106 );
//                }
//                $packing_form_list[ $item->degree_id ][ $item->packing_type_id ][ $carrier->id ?? 0 ]          = $packing_form_new;
//                $packing_form_list[ $item->degree_id ][ $item->packing_type_id ][ $carrier->id ?? 0 ]["count"] = 0; // تعداد استفاده
//            }
//        }

        // اختصاص شناسه بسته بندی به هر آیتم
        foreach ($packing_item_temp as $item) {
            $carrier = $packing_type_list[$item->packing_type_id]["carrier"];
            $item->code = null;
            if ($item->new_carrier_id == 1 || !isset($packing_form_list[$item->packing_type_id][$carrier->id ?? 0]["count"])) {// بسته بندی بدون حامل و نیاز به یک بسته بندی جدید می باشد.

                $packing_form_new = PackingForm::create([
                    "packing_type_id" => $item->packing_type_id,
                    "carrier_id" => $carrier->id ?? null, //
                    "status_id" => 7007005, // در انتظار تحویل به انبار
                    "degree_id" => $item->degree_id,
                    "packing_form_parent_id" => $packing_form->id,
                    "applicant_type_id" => $packing_form->applicant_type_id??null,
                    "applicant_id" => $packing_form->applicant_id??null,
                ]);

                $packing_form_list[$item->packing_type_id][$carrier->id ?? 0] = $packing_form_new;
                $packing_form_list[$item->packing_type_id][$carrier->id ?? 0]["count"] = 0; // تعداد استفاده
            }


            $item->packing_form_id = $packing_form_list[$item->packing_type_id][$carrier->id ?? 0]->id;
            $item->status_id = 7007005; // در انتظار تحویل به انبار
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

            event(new PackingLogEvent($packing_form_new, "7007001"));
        }

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

        $before_status = $packing_form->status_id;

        // تغییر وضعیت فرم بسته بندی
        $packing_form->status_id = 7007007; //تغییر یافته
        $packing_form->warehouse_status_id = 4206; //تغییر یافته
        $packing_form->save();

        // بروز رسانی مقدار سیستمی
        $packing_form->updateAmount();

        // در صورتی که وضعیت بسته بندی موجود در انبار باشد، باید یک فرم خروج و یک فرم رورد ایجاد گردد و همزمان تایید شود.

        // لیست بسته بندی های تغیر یافته جدید
        $packing_form_list_changed = PackingForm::where("packing_form_parent_id", $packing_form->id)->get();
        $packing_form_out_text = "";
        $worker = Worker::find(Auth::id());
        foreach ($packing_form_list_changed as $packing_form_new) {
            $packing_form_out_text .= $packing_form_new->getCode() . ", ";

            //پرینت بسته بندی های
            if ($request->print_new_label) {
                PrintQRController::direct_print($packing_form_new, $worker);
            }

        }
        $packing_form_out_text = trim($packing_form_out_text, ", ");
        if ($before_status == 7007003) {

            ChangeInWarehouseController::ExitInputForm($packing_form, $packing_form_list_changed);

        } else {
            event(new PackingLogEvent($packing_form, "7007008"));
        }


        return redirect()->route($this->dashboard_route . "index")->with(["success" => "تغییر بسته بندی با موفقیت انجام شد."]);
    }

    public function checkPermission(PackingForm $packing_form)
    {

        $result = FabricRaw\PackingFormController::checkPermissionConditions($packing_form, ChangeInWarehouseController::$info);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }

        return "";
    }

    // تغییر بسته بندی های داخل انبار
    // یک بسته بندی به یک یا چند بسته بندی دیگر تبدیل می شود.
    public static function ExitInputForm(
        PackingForm $packing_form,
        $packing_form_list_changed,
        $packing_form_return_to_warehouse = 0,
        $packing_form_pieces_count_for_input=0,  // تعداد بسته بندی فرعی که مانده است
        $packing_form_amount_select=0, // مقدار از کالای بسته بندی که مانده است.
        $packing_form_sub_amount_select=0, // مقدار فرعی از کالای بسته بندی که مانده است.
    )
    {

        $ic = $packing_form->warehouse->ic;
        $warehouse_id = $packing_form->warehouse_id;
        // ثبت فرم خروج از انبار
        $form_output = Form::CreateFrom([
            "order_id" => 0,
            "order_list_id" => 0,
            "user_id" => Auth::user()->id,
            "trans_kind" => 201, // خروج تغییر بسته بندی (سامانه)
            "ic" => $ic,
            "warehouse_id" => $warehouse_id,
            "status_id" => 500000200 //تایید شده
        ]);
        $form_output->getCode("DCEF");// Warehouse Exit Form
        $ic = "";
        foreach ($packing_form->items as $item) {
            $form_item = FormItem::create([
                "form_id" => $form_output->id,
                "product_id" => $item->product_id,
                "amount" => $item->final_amount,
                "sub_amount" => $item->sub_amount,
                "carrier_id" => $packing_form->carrier_id,
                "degree_id" => $item->degree_id,
                "lot_number_id" => $item->lot_number_id,
                "packing_type_id" => $packing_form->packing_type_id,
                "packing_form_item_id" => $item->id,
                "io_line_code" => null,
                "product_request_form_item_id" => null,
                "description" => "خروج از انبار بابت تغییر بسته بندی " . $packing_form->code
            ]);


        }

        $form_output->save();


        event(new PackingLogEvent($packing_form, "7007011", null, "", $form_output->id));
        event(new PutInWarehouseEvent($form_output));

        // ثبت فرم ورود به انبار
        $form_input = Form::CreateFrom([
            "order_id" => 0,
            "order_list_id" => 0,
            "production_card_id" => 0,
            "user_id" => Auth::user()->id,
            "form_type_id" => 304,
            "trans_kind" => 101,// ورود تغییر بسته بندی (سامانه)
            "warehouse_id" => $warehouse_id,
            "status_id" => 500000200, //تایید شده
            "ic" => $ic
        ]);
        $form_input->getCode();

        // لیست بسته بندی های تغیر یافته جدید
        foreach ($packing_form_list_changed as $packing_form_new) {
            foreach ($packing_form_new->items as $item) {
                $form_item = FormItem::create([
                    "form_id" => $form_input->id,
                    "product_id" => $item->product_id,
                    "amount" => $item->final_amount,
                    "sub_amount" => $item->sub_amount,
                    "carrier_id" => $packing_form_new->carrier_id,
                    "degree_id" => $item->degree_id,
                    "lot_number_id" => $item->lot_number_id,
                    "packing_type_id" => $packing_form_new->packing_type_id,
                    "packing_form_item_id" => $item->id,
                    "io_line_code" => null,
                    "product_request_form_item_id" => null,
                    "description" => "ورود به انبار بابت تغییر بسته بندی انبار از بسته بندی " . $packing_form->code

                ]);
            }

            // بروزر رسانی وزن خالص و ناخالص
            $result_weight = PackingForm::UpdateWeight($packing_form_new);
            if ($result_weight["result"]) {
                $packing_form_new->weight = $result_weight["weight"];;
                $packing_form_new->gross_weight = $result_weight["gross_weight"];
            }

            $packing_form_new->status_id = 7007003; // تحویل شده به انبار
            $packing_form_new->form_id = $form_input->id;
            $packing_form_new->warehouse_id = $warehouse_id;
            $packing_form_new->save();
            event(new PackingLogEvent($packing_form_new, "7007011", null, "", $form_input->id));

        }

        // بسته بندی اصلی را دوباره به انبار وارد می کنیم.
        if ($packing_form_return_to_warehouse > 0) {
            // یعنی بسته بندی اصلی را دوباره به انبار وارد کنید با مقدار جدید
            $before_sub_packing_form_number = $packing_form->sub_packing_form_number;
            $packing_form->sub_packing_form_number = $packing_form_pieces_count_for_input;
            $packing_form->status_id = 7007003; // تحویل شده به انبار
            $packing_form->warehouse_id = $warehouse_id;
            $packing_form->save();

            foreach ($packing_form->items as $item) {
                $new_amount = [
                    "amount" => $item->amount - $packing_form_amount_select,
                    "amount_after_control" =>  $item->amount - $packing_form_amount_select,
                    "final_amount" =>  $item->amount - $packing_form_amount_select,
                    "sub_amount" =>  $item->sub_amount - $packing_form_sub_amount_select,
                ];
                $item->update([
                    "amount" => $new_amount["amount"],
                    "amount_after_control" => $new_amount["amount_after_control"],
                    "final_amount" => $new_amount["final_amount"],
                    "sub_amount" => $new_amount["sub_amount"],
                ]);

                $form_item = FormItem::create([
                    "form_id" => $form_input->id,
                    "product_id" => $item->product_id,
                    "amount" => $new_amount["final_amount"],
                    "sub_amount" => $new_amount["sub_amount"],
                    "carrier_id" => $packing_form->carrier_id,
                    "degree_id" => $item->degree_id,
                    "lot_number_id" => $item->lot_number_id,
                    "packing_type_id" => $packing_form->packing_type_id,
                    "packing_form_item_id" => $item->id,
                    "io_line_code" => null,
                    "product_request_form_item_id" => null,
                    "description" => "ورود به انبار بابت تغییر بسته بندی انبار از بسته بندی " . $packing_form->code

                ]);
            }
            event(new PackingLogEvent($packing_form, "7007011", null, "", $form_input->id));

            // بروزر رسانی وزن خالص و ناخالص
            $result_weight = PackingForm::UpdateWeight($packing_form);
            if ($result_weight["result"]) {
                $packing_form->weight = $result_weight["weight"];;
                $packing_form->gross_weight = $result_weight["gross_weight"];
                $packing_form->save();
            }

        }

        event(new PutInWarehouseEvent($form_input));
    }

}
