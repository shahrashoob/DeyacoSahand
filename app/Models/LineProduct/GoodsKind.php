<?php

namespace App\Models\LineProduct;

use App\Models\LineProduct\Carrier\CarrierType;
use App\Models\LineProduct\GoodsKind\GoodsKindClassification;
use App\Models\LineProduct\GoodsKind\GoodsKindProductFault;
use App\Models\LineProduct\Machine\MachineModuleType;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\Production\ProductionAlgorithmType;
use App\Models\Production\ProductionFormStatus;
use App\Models\Production\ProductionWaitingStatus;
use App\Models\Utility\Status;
use Database\Seeders\GoodsKind\FabricRaw\Dobby\DobbySeeder;
use Database\Seeders\LineProductStation\GoodsKindSeeder;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class GoodsKind extends Model
{
    use HasFactory;
    use Loggable;

    protected $fillable = [
        "caption",
        "caption_en",
        "production_algorithm_type_id",
        "required_parent_production",
        "allowed_percentage_to_be_lower",
        "allowed_percentage_to_be_higher",
        "allowed_percentage_in_lot_number_property",
        "allowed_percentage_in_all_lot_number",
        "be_lower_in_confirm_exit_form",
        "possibility_of_issuing_a_production_manually",
        "checking_carrier_at_delivery_of_product",
        "checking_compatibility_grade_in_delivery",
        "possibility_of_issuing_a_sample_production_manually",
        "max_number_for_sampling_production_card",
        "record_entry_into_warehouse_manually",
        "record_out_of_warehouse_manually",
        "error_rate_in_checking_bom_weight",
        "allowed_percentage_in_complete_form_information",
        'has_sampling_required_in_product_creation',
        'min_diff_of_production_and_allocation_in_the_end_of_production',
        'max_diff_of_production_and_allocation_in_the_end_of_production',
        'send_sms_in_create_allocation_machine_to_post_id1',

        "property1_id",
        "property2_id",
        "property3_id",

        "allow_show_sub_amount_in_exit_forms",
        "active_status_id",
        "allow_select_partial_of_packing_in_output",
        "number_check_of_quality_control_input_warehouse",
        "percent_check_of_quality_control_input_warehouse"

    ];

    public static function ExistsCode($caption, $id = false)
    {
        if ($id) {
            return GoodsKind::where("caption", $caption)->where("id", "!=", $id)->exists();
        }

        return GoodsKind::where("caption", $caption)->exists();
    }

    public function production_algorithm_type()
    {
        return $this->belongsTo(ProductionAlgorithmType::class);
    }

    public function property()
    {
        return $this->hasMany(GoodsKindProperty::class)->orderBy("priority_number");
    }

    public function property_1()
    {
        return $this->belongsTo(GoodsKindProperty::class, "property1_id");
    }

    public function property_2()
    {
        return $this->belongsTo(GoodsKindProperty::class, "property2_id");
    }

    public function property_3()
    {
        return $this->belongsTo(GoodsKindProperty::class, "property3_id");
    }

//    public function lot_number_property()
//    {
//        return $this->hasMany(GoodsKindLotNumberProperty::class)->orderBy("priority_number");
//    }

    public function classification()
    {
        return $this->hasMany(GoodsKindClassification::class, "goods_kind_id", "id")->orderBy("goods_kind_classification_type_id");
    }

    public function degree()
    {
        return $this->hasMany(Degree::class);
    }

    public function carrier_type()
    {
        return $this->belongsToMany(CarrierType::class);
    }

    public function product_fault()
    {
        return $this->hasMany(GoodsKindProductFault::class);
    }

    public function packing_type()
    {
        return $this->belongsToMany(PackingType::class);
    }

    public function active_status()
    {
        return $this->belongsTo(Status::class);
    }

    public function production_waiting_status()
    {
        return $this->hasMany(ProductionWaitingStatus::class, "goods_kind_id", "id");
    }

    public function production_form_status()
    {
        return $this->hasMany(ProductionFormStatus::class, "goods_kind_id", "id");
    }

    public function getCode()
    {
        if ($this->code != "") {
            return $this->code;
        }
        $code = Str::of($this->id)
            ->when($this->id < 10, function ($string) {
                return Str::of('0')->append($string);
            }); // 00
        $this->code = $code;
        $this->save();

        return $code;
    }

    public static function getByCaptionEn($caption_en)
    {
        return GoodsKind::where("caption_en", $caption_en)->first();
    }

    public function getModuleList($status_type_id = false)
    {
        $list = [];
        switch ($this->caption_en) {
            case "Fabric_Raw":
                $list = [
                    7001 => [
                        "caption" => "کارت تولید",
                        "status_type_id" => 7001,
                        "controller_info" => \App\Http\Controllers\GoodsKindProcess\FabricRaw\ProductionCardController::get_controller_info_for_permission()
                    ],
                    7002 => [
                        "caption" => "فرم تولید",
                        "status_type_id" => 7002,
                        "controller_info" => \App\Http\Controllers\GoodsKindProcess\FabricRaw\ProductionFormController::get_controller_info_all()
                    ]


                ];
                break;
            case "Fabric":
                $list = [
                    7301 => [
                        "caption" => "کارت تولید",
                        "status_type_id" => 7301,
                        "controller_info" => \App\Http\Controllers\GoodsKindProcess\Fabric\ProductionCardController::get_controller_info_for_permission()
                    ],
                    7302 => [
                        "caption" => "فرم تولید",
                        "status_type_id" => 7302,
                        "controller_info" => \App\Http\Controllers\GoodsKindProcess\Fabric\ProductionFormController::get_controller_info_all()
                    ]
                ];
                break;

            case "Warps":
                $list = [
//                    5000 => [
//                        "caption"         => "تولید چله کشی (ویژه دوره پیاده سازی)",
//                        "status_type_id"  => 5000,
//                        "controller_info" => \App\Http\Controllers\GoodsKindProcess\Warps\ProductionFrom\DashboardController::get_controller_info()
//                    ],
                    7201 => [
                        "caption" => "کارت تولید",
                        "status_type_id" => 7201,
                        "controller_info" => \App\Http\Controllers\GoodsKindProcess\Warps\ProductionCardController::get_controller_info_for_permission()
                    ],
                    7202 => [
                        "caption" => "فرم تولید",
                        "status_type_id" => 7202,
                        "controller_info" => \App\Http\Controllers\GoodsKindProcess\Warps\ProductionFormController::get_controller_info_all()
                    ]
                ];
                break;
            default:
                $list = [];
                break;
        }
        if ($status_type_id) {
            return $list[$status_type_id];
        } else {
            return $list;
        }
    }

    public function getStatusId(Product $product, $type)
    {
        return self::getStatusIdFromProduct($product->goods_kind, $product->supply_type_id, $type);
    }

    public static function getStatusIdFromProduct(GoodsKind $goodsKind, $supply_type_id, $type)
    {
        // این تابع باید بعدا به اطلاعات seed جدول های رسته کالایی اضافه شود.
        return GoodsKindSeeder::getStatusIdFromProduct($goodsKind, $supply_type_id, $type);
    }


    public static function getAmountFromMachineLog(Product $product, $start_machine_log, $end_machine_log)
    {

        if (!isset($start_machine_log)) {
            return 0;
        }
        if (!isset($end_machine_log)) {
            return 0;
        }

        $sumCounter = $end_machine_log->sumCounter("calculate") - $start_machine_log->sumCounter();

        switch ($product->goods_kind_id) {
            case 3: // چله
                return $sumCounter;
                break;
            case 4: // پارچه خام

                $tarakom_pod = GoodsKindPropertyValue::where("product_id", $product->id)->whereIn("goods_kind_property_id", [
                    220381 // تراکم نهایی پود (تئوری)
                ])->sum("value");

                $amount = $tarakom_pod > 0 ? ($sumCounter) / ($tarakom_pod * 100) : -1;
                return $amount > 0 ? $amount : 0;
                break;
        }

        1 / 0;

    }


    public static function getMachineContourValueFromAmount(Product $product, $amount)
    {
// این تابع مقدار کالا بر اساس واحد اصلی را می گیرد و مقدار مجموع کنتور ماشین را بر می گرداند.

        switch ($product->goods_kind_id) {
            case 3: // چله
                return $amount;
                break;
            case 4: // پارچه خام
                $tarakom_pod = GoodsKindPropertyValue::where("product_id", $product->id)->whereIn("goods_kind_property_id", [
                    220381 // تراکم نهایی پود (تئوری)
                ])->sum("value");

                return ($amount) * $tarakom_pod * 100;
                break;
        }

        1 / 0;


    }

    /**
     * @param GoodsKind $goods_kind
     * @param $product_id
     * @return void
     * برورز رسانی مشخه های اصلی و فرعی رسته های کالایی
     */
    public static function UpdateProperty(GoodsKind $goods_kind, $product_id = null, $property = false)
    {

        $products = Product::
        where("goods_kind_id", $goods_kind->id)->
        when($product_id, function ($query) use ($product_id) {
            return $query->where('id', $product_id);
        })->
        when($property, function ($query) use ($property) {
            return $query->where(function ($query2) use ($property) {
                return $query2->
                orWhere('property1_caption', "")->
                orWhere('property2_caption', "")->
                orWhere('property3_caption', "")->
                orWhere('property1_caption', "***")->
                orWhere('property2_caption', "***");
                orWhere('property3_caption', "***");
            });
        })->
        get();


        foreach ($products as $product) {

            // property1
            $property_value = GoodsKindPropertyValue::where(["product_id" => $product->id])->
            whereIn("goods_kind_property_id", [$goods_kind->property1_id ?? -1, $goods_kind->property2_id ?? -1, $goods_kind->property3_id ?? -1])->
            pluck("value", "goods_kind_property_id");

            if (isset($property_value[$goods_kind->property1_id])) {
                if ($goods_kind->property_1 && $goods_kind->property_1->field_type_id == 3) {
                    $option = GoodsKindPropertyOption::where("id", $property_value[$goods_kind->property1_id])->first();
                    $product->property1_caption = $option->caption ?? "***";
                } else {
                    $product->property1_caption = $property_value[$goods_kind->property1_id];
                }
            } else {
                $product->property1_caption = "***";
            }
            // property2
            if (isset($property_value[$goods_kind->property2_id])) {
                if ($goods_kind->property_2 && $goods_kind->property_2->field_type_id == 3) {
                    $option = GoodsKindPropertyOption::where("id", $property_value[$goods_kind->property2_id])->first();
                    $product->property2_caption = $option->caption ?? "***";
                } else {
                    $product->property2_caption = $property_value[$goods_kind->property2_id];
                }
            } else {
                $product->property2_caption = "***";
            }
            // property3
            if (isset($property_value[$goods_kind->property3_id])) {

                if ($goods_kind->property_3 && $goods_kind->property_3->field_type_id == 3) {
                    $option = GoodsKindPropertyOption::where("id", $property_value[$goods_kind->property3_id])->first();
                    $product->property3_caption = $option->caption ?? "***";
                } else {
                    $product->property3_caption = $property_value[$goods_kind->property3_id];
                }
            } else {
                $product->property3_caption = "***";
            }

            $product->save();

        }

        return [
            "result" => true
        ];

    }

}
