<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\PackingForm;

use App\Events\Form\PackingLogEvent;
use App\Http\Controllers\Controller;
use App\Models\Form\Packing\PackingForm;
use App\Models\LineProduct\Carrier\CarrierType;
use App\Models\LineProduct\LotNumber;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\LineProduct\Product;
use App\Models\Post\Post;
use App\Models\Post\PostSmartObject;
use App\Models\Post\PostUser;
use App\Models\Utility\Pdf;
use App\Models\Utility\Printer\PrinterFile;
use App\Models\Utility\Printer\PrinterFiles;
use App\Models\Utility\Setting;
use App\Models\Utility\SmartObject;
use App\Models\Utility\SpecialLicense\SpecialLicense;
use App\Models\Utility\Status;
use App\Models\Worker;
use Illuminate\Http\Request;
use App\Http\Controllers\GoodsKindProcess\FabricRaw;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Milon\Barcode\DNS1D;
use Psy\Util\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CompleteInformationController extends Controller
{

    // تکمیل اطلاعات بسته بندی (وزن و وزن ناخالص)
    public static $info = [
        "route" => "fabric_raw.packing_form.complete_information.",
        "enable_status" => [
            "020",
        ],
        "button" => ["caption" => "تکمیل اطلاعات بسته بندی", "class" => "btn-primary"],

    ];
    public static $view_path = "goods_kind_process.fabric_raw.packing_form.complete_information.";

    var $dashboard_route = "fabric_raw.packing_form.";

    public function __construct()
    {
    }

    public function index(PackingForm $packing_form)
    {


        $result = $this->checkPermission($packing_form);
        if ($result != "") {
            return $result;
        }

        /************************************/
        // گرفتن باسکول
        $url_scale=route("hr.personal.select_smart_object", [2, "fabric_raw.packing_form.complete_information.index", $packing_form->id]);
        $result_smart_object = SmartObject::GetScaleValue();
        if (!$result_smart_object["result"]) {
            if (isset($result_smart_object["warning"])) {
                return redirect()->route("hr.personal.select_smart_object", [2, "fabric_raw.packing_form.complete_information.index", $packing_form->id])->
                withErrors("با توجه به اینکه برای شما چند باسکول  تعریف شده است، لطفا یکی از باسکول ها را انتخاب نمایید.");
            } else {
                return redirect()->back()->withErrors($result_smart_object["error"]);
            }
        }
        $smart_object_value = $result_smart_object["smart_object_value"];
        $smart_object = $result_smart_object["smart_object"];
        /**********************************/


        $result = PackingType::getWeight($packing_form->packing_type, $packing_form->carrier);

        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }

        foreach ($packing_form->packing_form_contents as $sub_packing_form) {
            $result = PackingType::getWeight($sub_packing_form->packing_type, $sub_packing_form->carrier);

            if (!$result["result"]) {
                return back()->withErrors($result["error"]);
            }
        }

        return view(CompleteInformationController::$view_path . "index",
            compact("packing_form", "smart_object_value", "smart_object", "url_scale"));
    }

    public function submit(Request $request, PackingForm $packing_form)
    {

        $result = $this->checkPermission($packing_form);
        if ($result != "") {
            return $result;
        }

        $result = self::CompleteInformation($packing_form, $request->gross_weight, false, true, 7007005);

        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }


        $packing_form->status_id = 7007005; // در انتظار تحویل به انبار
        $packing_form->save();
        event(new PackingLogEvent($packing_form, "7007027"));

        return redirect()->route("fabric_raw.packing_form.view", $packing_form)->with(["success" => "تکمیل اطلاعات با موفقیت انجام شد"]);


    }

    public static function CompleteInformation(PackingForm $packing_form, $gross_weight, $checkLotProperty = false, $allow_request_special_license = false, $next_status_id_for_special_license = null)
    {

        $packing_form_item = $packing_form->items()->first();

        $result = PackingType::getAmountFromWeight(
            $packing_form_item->product,
            $packing_form->packing_type,
            $gross_weight,
            $packing_form->sub_packing_form_number,
            $packing_form->carrier,
            $packing_form->getFinalAmount(-1),
            $packing_form->getSubAmount(-1));

        if (!$result["result"]) {
            return $result;
        }
        $packing_form_final_amount = $result["final_amount"];
        if (isset($packing_form->carrier->carrier_type)) {
            $result_check_carrier_amount = CarrierType::checkAmount($packing_form->carrier->carrier_type, $result["final_amount"], $packing_form->sub_packing_form_number);
            if (!$result_check_carrier_amount["result"]) {
                return $result_check_carrier_amount;
            }
        }

        //             بررسی مشخصات لات
        foreach ($packing_form->items as $item) {

            $result_packing_form_item = self::checkPackingFormItem($item, $packing_form, $result["weight"], $packing_form_final_amount, $checkLotProperty, $allow_request_special_license, $next_status_id_for_special_license,$gross_weight);

            if (!$result_packing_form_item["result"]) {
                return $result_packing_form_item;
            }
        }


        $packing_form->weight = $result["weight"];
        $packing_form->gross_weight = $result["gross_weight"];
        $packing_form->save();

        if (
            $packing_form_item->product->unit->weight_conversion_rate == 0 &&
            isset($packing_form_item->product->sub_unit) &&
            $packing_form_item->product->sub_unit->weight_conversion_rate != 0
        ) {
            // واحد فرعی را به نسبت تقسیم می کنیم.
            foreach ($packing_form->items as $item) {
                $item->sub_amount = $result["weight"] * $item->final_amount / $packing_form->getFinalAmount(-1);
                $item->save();
            }
        } elseif ($packing_form_item->product->unit->weight_conversion_rate != 0) {
            // واحد اصلی را به نسبت تقسیم می کنیم.
            foreach ($packing_form->items as $item) {
                $item->amount = $result["weight"] * $item->sub_amount / $packing_form->getSubAmount(-1);
                $item->final_amount = $item->amount;
                $item->amount_after_control = $item->amount;
                $item->save();
            }
        }


        return [
            "result" => true,
            "weight" => $result["weight"],
            "gross_weight" => $result["gross_weight"]
        ];

    }

    public static function checkPackingFormItem($packing_form_item, $packing_form, $weight, $packing_form_final_amount, $checkLotProperty, $allow_request_special_license, $next_status_id_for_special_license,$gross_weight)
    {

        //            // بررسی مقدار متراژ و مقدار وزن با مقدار محاسباتی برای همه لات ها
        $allowed_percentage_in_all_lot_number =
            $packing_form_item->product->goods_kind->allowed_percentage_in_all_lot_number / 100;

        if (
            $packing_form_item->product->unit->weight_conversion_rate == 0 &&
            isset($packing_form_item->product->sub_unit) &&
            $packing_form_item->product->sub_unit->weight_conversion_rate != 0
        ) {

            $sub_amount_result = Product::getSubAmount($packing_form_item->product, $packing_form_item->final_amount);
            if (!$sub_amount_result["result"]) {
                return $sub_amount_result;
            }
            $sub_amount = $sub_amount_result["sub_amount"];
            $weight_item = $weight * $packing_form_item->final_amount / $packing_form->getFinalAmount();
//echo "$weight * $packing_form_item->final_amount /". $packing_form->getFinalAmount();
            if ($checkLotProperty) {
                // چک کردن مشخصه کیلوگرم بر واحد کالا
                $result_check_lot_number = LotNumber::checkLotProperty($packing_form_item->lot_number, $packing_form_item->product, $packing_form_item->final_amount, $weight_item);
                if (!$result_check_lot_number["result"]) {
                    return $result_check_lot_number;
                }
            }

            if ( $weight_item > $sub_amount * (1 + $allowed_percentage_in_all_lot_number) || $weight_item < $sub_amount * (1 - $allowed_percentage_in_all_lot_number)) {
                if (!self::HasSpecialLicense($packing_form)) {
                    return [
                        "result" => false,
                        "error" => "وزن نا خالص ".$gross_weight. " کیلوگرم "." برای " . $packing_form_item->product->caption . " در ردیف بسته بندی با کد  " .
                            $packing_form_item->code  .
                            " نامعتبر است." .
                            "<br/>   مقدار نهایی آیتم بسته بندی:".$packing_form_item->final_amount." ".$packing_form_item->product->unit->caption.
                            "<br/> وزن خالص :".round($weight_item, 6)." کیلوگرم".
                            "<br/> وزن تثوری :".round($sub_amount, 6)." کیلوگرم".

                            (
                            $allow_request_special_license && $gross_weight>0 ?
                                "<br/>" .
                                SpecialLicense::GetLink(6, $packing_form->id, "ثبت درخواست مجوز جهت ثبت وزن واقعی بسته بندی  ", $sub_amount, $weight_item, $packing_form_final_amount, $next_status_id_for_special_license,$gross_weight)
                                :
                                ""
                            )

                    ];
                }
            }
        } elseif ($packing_form_item->product->unit->weight_conversion_rate != 0) {

            $amount = $packing_form_item->sub_amount;
            $weight_item = $weight * $packing_form_item->sub_amount / $packing_form->getSubAmount();

            if ($checkLotProperty) {
                // چک کردن مشخصه کیلوگرم بر واحد کالا
                $result_check_lot_number = LotNumber::checkLotProperty($packing_form_item->lot_number, $packing_form_item->product, $packing_form_item->sub_amount, $weight_item);
                if (!$result_check_lot_number["result"]) {
                    return $result_check_lot_number;
                }
            }

//            if ( $weight_item > $amount * ( 1 + $allowed_percentage ) || $weight_item < $amount * ( 1 - $allowed_percentage ) ) {
//
//                return [
//                    "result" => false,
//                    "error"  => "وزن (".$weight_item.") برای ".$packing_form_item->product->caption." در  بسته بندی " .
//                                $packing_form->getCode() ." ($amount)".
//                                " نامعتبر است."
//                ];
//            }
        } else {
            return [
                "result" => false,
                "error" => "واحد فرعی و اصلی نامعتبر است، لطفا با پشیتبانی تماس بگیرید."
            ];
        }
        return [
            "result" => true,
        ];

    }

    public static function HasSpecialLicense(PackingForm $packing_form)
    {

        $special_license = SpecialLicense::where([
            "special_license_type_id" => 6,
            "reference_id" => $packing_form->id,
            "status_id" => 6040002, // تایید شده
        ])->first();
        if (!$special_license) {
            return false;
        } else {
            return true;
        }
    }


    public function checkPermission(PackingForm $packing_form)
    {

        $result = FabricRaw\PackingFormController::checkPermissionConditions($packing_form, CompleteInformationController::$info);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }

        return "";
    }

}
