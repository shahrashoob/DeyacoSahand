<?php

namespace App\Imports;

use App\Models\Form\Packing\PackingForm;
use App\Models\HR\Shift\NewShiftWorkDay;
use App\Models\LineProduct\Degree;
use App\Models\LineProduct\GoodsKind\GoodsKindLotNumberPropertyValue;
use App\Models\LineProduct\GoodsKind\LotNumberProperty;
use App\Models\LineProduct\LotNumber;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\LineProduct\Product;
use App\Models\Utility\JsonDataList;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Morilog\Jalali\CalendarUtils;

class ProductionModule1Import implements ToCollection
{
    /**
     * @param Collection $collection
     */
    var $allocation_id;
    var $user_id;
    var $max_row = 72;
    var $packing_types;

    public function collection(Collection $rows)
    {
        //
        $cols = [
            "product_code" => 0,
            "product_caption" => 1,
            "degree_code" => 2,
            "lot_number_code" => 3,
            "amount" => 4,
            "sub_amount" => 5,
            "sub_amount2" => 6,
            "sub_packing_number" => 7,
            "gross_weight" => 8,
            "pin1" => 9,
        ];


        $i = 0;
        $error_format = "";

        $result_data = [];
        $degree_list = [];
        $packing_form_list = [];
        $product_ids = [];
        if (count($rows) > $this->max_row) {
            $result_data["error"] = "حداکثر تعداد سطر قابل قبول برای بارگذاری ".$this->max_row." ردیف می باشد، در حالی که فایل بارگذاری شده دارای ".count($rows)."  ردیف می باشد.";
            JsonDataList::SetData($this->allocation_id, 700, $result_data);
            return;
        }
        foreach ($rows as $row) {
            $i++;
            if ($i == 1) {
                foreach ($cols as $k => $v) {
                    if (!isset($row[$cols[$k]]) || $row[$cols[$k]] != $k) {
                        $error_format = "فرمت فایل به درستی انتخاب نشده است." . "<br/>";
                    }
                }
            }
            if ($error_format) {
                $result_data["error"] = $error_format;

                JsonDataList::SetData($this->allocation_id, 700, $result_data);
                return;
            }
            if ($i <= 2) {
                continue;
            }
            foreach ($cols as $k => $v) {
                if (!isset($row[$v])) {
                    $row[$v] = 0;
                }
            }
            $error_text = "";
            $product = Product::GetIdFromCode($row[$cols["product_code"]]);
            if (!$product) {
                $error_text .= " در ردیف $row کد کالای " . $row[$cols["product_code"]] . " نامعتبر است. " . "<br/>";
                continue;
            }

            if (!in_array($row[$cols["degree_code"]], $degree_list)) {
                $degree = Degree::
                where("code", $row[$cols["degree_code"]])->
                where("goods_kind_id", $product->goods_kind_id ?? -1)->
                first();
                if (!isset($degree)) {
                    $error_text .= "کد درجه " . $row[$cols["degree_code"]] . " نامعتبر است." . "<br/>";
                }
                else {
                    $degree_list[$degree->code] = $degree->id;
                }
            }
            $product_ids[] = $product->id;


            // -اگر واحد فرعی دارد باید حتما وارد شده باشد
            $sub_amount =(float) $row[$cols["sub_amount"]] + 0;
            if ($product->sub_unit_id && $sub_amount <= 0) {
                $error_text .= "واحد فرعی نامعتبر است." . "<br/>";
            }
            // -اگر واحد فرعی 2 دارد باید حتما وارد شده باشد
            $sub_amount2 = (float)$row[$cols["sub_amount2"]] + 0;
            if ($product->sub_unit2_id && $sub_amount2 <= 0) {
                $error_text .= "واحد فرعی 2 نامعتبر است." . "<br/>";
            }

            // لات اگر وجود ندارد با توجه به اولین ردیف تعریف می شود به تفکیک هر کالا
            $lot_number = LotNumber::where(["code" => $row[$cols["lot_number_code"]], "product_id" => $product->id])->first();

            if (!$lot_number) {
                $lot_number = LotNumber::create([
                    "product_id" => $product->id,
                    "code" => $row[$cols["lot_number_code"]],
                    "user_id" => $this->user_id
                ]);
            }

            // اگر لات تعریف شده بود و متغیر کیلوگرم بر متر داشت، برای همه بسته بنیدی ها باید چک شود.
            if (!$lot_number->isSetAllProperty()) {

                if (!LotNumberProperty::check_property_check_for_getting($lot_number, 1)) {

                    $allowed_percentage = $product->goods_kind->allowed_percentage_in_all_lot_number / 100;
                    if ($row[$cols["amount"]] + 0 <= 0) {
                        $error_text .= "با توجه به مشخصات کالا لازم است تا مقدار فرعی کالا ثبت شود.";
                    } else {
                        $kgInMeterLot = ((float)$row[$cols["sub_amount"]] + 0) / ((float)$row[$cols["amount"]] + 0);
                        if ($product->weight > $kgInMeterLot * (1 + $allowed_percentage) || $product->weight < $kgInMeterLot * (1 - $allowed_percentage)) {
                            $error_text .= "با توجه به  وزن کالای " . $product->caption
                                . " (" . $product->weight . " kg" . " )  امکان ثبت مقدار  " . $kgInMeterLot .
                                " برای مشخصه کیلوگرم بر متر کالا امکان پذیر نمی باشد. <br/>
                        لطفا اطلاعات وارد شده را بررسی کرده و در صورت نیاز با واحد پشتیبانی تماس بگیرید. " . "<br/>";
                        } else {
                            GoodsKindLotNumberPropertyValue::create([
                                "product_id" => $product->id,
                                "lot_number_id" => $lot_number->id,
                                "goods_kind_lot_number_property_id" => 1,
                                "value" => $kgInMeterLot
                            ]);
                        }
                    }

                }


            }

// اگر واحد اصلی متر است.
            if (
                $product->unit->weight_conversion_rate == 0 &&
                $product->sub_unit->weight_conversion_rate != 0
            ) {
                if ($lot_number->isSetAllProperty()) {
                    $result_check_lot_number = LotNumber::checkLotProperty($lot_number, $product, $row[$cols["amount"]] + 0, $row[$cols["sub_amount"]] + 0);
                    if (!$result_check_lot_number["result"]) {
                        $error_text .= $result_check_lot_number["error"];
                    }
                }
            }
            $packing_type = null;
            if (isset($this->packing_types[$product->id])) {
                $packing_type = $this->packing_types[$product->id];

                if ($packing_type->layers()->count() > 1) {

                    if ((int)$row[$cols["sub_packing_number"]] + 0 <= 0) {
                        $error_text .= "با توجه به اینکه نوع بسته بندی چند لایه است، لازم است تا تعداد بسته بندی های فرعی وارد شود." . "<br/>";
                    }
                }
            } else {
                $error_text .= "کالای مورد نظر در هیچ کدام از تخصیص ها وجود ندارد" . "<br/>";
            }

            if (!isset($packing_type_weights[$product->id])) {
                $packing_type_weights[$product->id] = 0;
                if ($packing_type) { // اگر نوع بسته بندی معتبر بود، اجازه دهد که وزن آن محاسبه شود.
                    $packing_type_weight_result = PackingType::getWeight($packing_type);
                    if (!$packing_type_weight_result["result"]) {
                        $error_text .= $packing_type_weight_result["error"] . "<br/>";
                        $packing_type_weights[$product->id] = 0;
                    } else {
                        $packing_type_weight = $packing_type_weight_result["weight"];

                        $packing_type_weights[$product->id] = $packing_type_weight;
                    }
                }
            }
            $packing_type_weight = $packing_type_weights[$product->id];
            $get_amount_from_weight_result = Product::getAmountFromWeight($product, (float)$row[$cols["gross_weight"]]+0,(float) $packing_type_weight, (float)$row[$cols["amount"]]+0, (float)$row[$cols["sub_amount"]]+0);

            if (!$get_amount_from_weight_result["result"]) {
                $error_text .= $get_amount_from_weight_result["error"] . "<br/>";
            }

            $pin_exsit = PackingForm::where("pin1", $row[$cols["pin1"]])->exists();
            if ($pin_exsit) {
                $error_text .= "پین تکراری است." . "<br/>";
            }
            $packing_form_list[] = [
                "product_code" => $product->code,
                "product_id" => $product->id,
                "degree_code" => $row[$cols["degree_code"]],
                "degree_id" => isset($degree_list[$row[$cols["degree_code"]]])?$degree_list[$row[$cols["degree_code"]]]:0,
                "lot_number_code" => $row[$cols["lot_number_code"]],
                "lot_number_id" => $lot_number->id,
                "amount" => $row[$cols["amount"]],
                "sub_amount" => $row[$cols["sub_amount"]],
                "sub_amount2" => $row[$cols["sub_amount2"]],
                "sub_packing_number" => $row[$cols["sub_packing_number"]],
                "gross_weight" => $row[$cols["gross_weight"]],
                "weight" => isset($get_amount_from_weight_result["weight"]) ? $get_amount_from_weight_result["weight"] : 0,
                "pin1" => $row[$cols["pin1"]],
                "packing_type_id" => $packing_type->id ?? 0,
                "error" => $error_text,
            ];
        }

        $result_data["packing_form_list"] = $packing_form_list;
        $result_data["product_ids"] = $product_ids;
        $result_data["error"] = $error_text;

        JsonDataList::SetData($this->allocation_id, 700, $result_data);
    }

}
