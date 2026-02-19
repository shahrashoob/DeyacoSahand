<?php

namespace App\Models\LineProduct;

use App\Http\Controllers\SampleController;
use App\Models\Accounting\CostCenter;
use App\Models\Accounting\Offer;
use App\Models\Accounting\Tariff\ProductTariff;
use App\Models\Customer\Customer;
use App\Models\File\File;
use App\Models\Form\Packing\PackingFormActualCost;
use App\Models\LineProduct\Machine\Allocation\AllocationBrand;
use App\Models\LineProduct\Machine\CurrentMachineInput;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\LineProduct\Product\BOM\BOMItem;
use App\Models\LineProduct\Product\ConsumedProduct\ConsumedProduct;
use App\Models\LineProduct\Product\Pricing\ProductPricing;
use App\Models\LineProduct\Product\Pricing\ProductPricingLog;
use App\Models\LineProduct\Product\Pricing\ProductPricingProductLog;
use App\Models\LineProduct\Product\ProductPackingType;
use App\Models\LineProduct\Product\ProductRoute;
use App\Models\LineProduct\Product\ProductServiceType;
use App\Models\LineProduct\Product\ShadeNumber;
use App\Models\LineProduct\Product\TypeOfSaleProduct\TypeOfSaleOfProduct;
use App\Models\LineProduct\Product\TypeOfSaleProduct\TypeOfSaleOfProductCategory;
use App\Models\LineProduct\Product\TypeOfSaleProduct\TypeOfSaleProductProduct;
use App\Models\LineProduct\Product\Waste\ProductWaste;
use App\Models\Order\OrderList;
use App\Models\Supplier\Supplier;
use App\Models\User;
use App\Models\Utility\Algorithm\Algorithm;
use App\Models\Utility\Setting;
use App\Models\Utility\Status;
use App\Models\Utility\Unit;
use App\Models\Warehouse\Warehouse;
use App\Models\Warehouse\WarehouseProduct;
use App\Models\Warehouse\WarehouseStorageType;
use Carbon\Carbon;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Models\LineProduct\Product\BOM\BOM;
use function PHPUnit\Framework\isNull;

class Product extends Model
{
    use HasFactory;
    use Loggable;

    protected $connection = 'mysql';
    protected $fillable = [
        "caption",
        "code",
        "unit_id",
        "sub_unit_id",
        "sub_unit2_id",
        "unit_of_measure_type_id_in_production",
        "unit_of_measure_type_id_in_sale",
//        "percent_of_extra_production",
//        "min_production",
//        "max_production",
//        "batch",
        "min_inventory",
        "max_inventory",
        "product_have_specific_location",
        "default_packing_type_id",
        "exist_product_service_type_id",
//        "extra_production",
        "product_type_id",
        "goods_type_id",
        "active_status_id",
        "supply_type_id",
        "number_in_carton",
        "weight",
        "predictive_weight",
        "line_group_id",
        "factory_id",
        "goods_kind_id",
        "min_buy",
        "max_buy",
        "batch_buy",
        "possibility_of_sale",
        "service_id",
        "product_service_type_id",
        "service_id_in_employer_system", // کد خدمت برای کالاهای پیمانکاری
        "warehouse_storage_type_id",
        "frame_ratio_unit2", // نسبت قاب به واحد اصلی

        "property1_caption",
        "property2_caption",
        "property3_caption",

        "have_testing_before_production",
        "testing_is_on_line_production",
        "testing_amount",
        "product_planing_algorithm_id",
        "type_of_sale_of_products_by_category_id",
        "product_version_id",
        "current_order_needed_amount",
        "remaining_order_amount",
        "last_ran_planing_algorithm",
        "production_algorithm_type_id",
        "order_point_algorithm_type_id",
        "lidetime_algorithm_type_id",
        "in_ implementation",
        "in_order_by_user",
        "in_order_by_deyaco_script",
        "by_deyaco_script",
    ];

    public static function GetIdFromCode($code)
    {
        if ($code == "" || !$code) {
            return null;
        }
        $product = Product::where("code", $code . "")->first();

        return isset($product) ? $product : null;
    }

    public function bom()
    {
        return $this->hasMany(Product\BOM\BOM::class);
    }

    public function product_reservoirs()
    {
        return $this->hasMany(Product\ProductReservoir::class);
    }

    public function product_versions()
    {
        return $this->hasMany(Product\Version\ProductVersion::class);
    }

    public function version()
    {
        return $this->belongsTo(Product\Version\ProductVersion::class, "product_version_id");
    }

    public function product_warehouse_storage_type()
    {
        return $this->hasMany(Product\ProductWarehouseStorageType::class);
    }

    public function warehouse_storage_type()
    {
        return $this->belongsTo(WarehouseStorageType::class);
    }

    public function default_packing_type()
    {
        return $this->belongsTo(PackingType::class, "default_packing_type_id");
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function unit_of_measure_type_in_production()
    {
        return $this->belongsTo(Unit\UnitOfMeasureType::class, "unit_of_measure_type_id_in_production");
    }

    public function unit_of_measure_type_in_sale()
    {
        return $this->belongsTo(Unit\UnitOfMeasureType::class, "unit_of_measure_type_id_in_sale");
    }

    public function product_service_type()
    {
        return $this->belongsTo(ProductServiceType::class);
    }

    public function product_planing_algorithm()
    {
        return $this->belongsTo(Algorithm::class, "product_planing_algorithm_id");
    }

    public function production_algorithm_type()
    {
        return $this->belongsTo(Algorithm::class, "production_algorithm_type_id");
    }

    public function order_point_algorithm_type()
    {
        return $this->belongsTo(Algorithm::class, "order_point_algorithm_type_id");
    }

    public function lidetime_algorithm_type()
    {
        return $this->belongsTo(Algorithm::class, "lidetime_algorithm_type_id");
    }

    public function last_ran_planing_algorithm_datetime()
    {
        if ($this->last_ran_planing_algorithm) {
            return jdate(Carbon::parse($this->last_ran_planing_algorithm)->timestamp)->format('H:i Y/m/d ');
        }
        return "---";
    }

    public function service_in_employer_system()
    {
        return $this->belongsTo(Product::class, 'service_id_in_employer_system');
    }

    public function exist_product_service_type()
    {
        return $this->belongsTo(ProductServiceType::class, 'exist_product_service_type_id');

    }

    public function type_of_sale_of_products()
    {
        return $this->hasMany(TypeOfSaleProductProduct::class);
    }

    public function type_of_sale_of_products_by_category()
    {
        return $this->belongsTo(TypeOfSaleOfProductCategory::class);
    }
//
//    public function production_channel_type() {
//        return $this->belongsTo( ProductionChannel::class );
//    }

    public function supplier()
    {
        return $this->belongsToMany(Supplier::class)->withPivot('warehouse_id');
    }

    public function lot_number()
    {
        return $this->hasMany(LotNumber::class);
    }

    public function shade_number()
    {
        return $this->hasMany(ShadeNumber::class);
    }

    public function consumed_product()
    {
        return $this->hasMany(ConsumedProduct::class, "product_id", "id");
    }

    public function sub_unit()
    {
        return $this->belongsTo(Unit::class, "sub_unit_id", "id");
    }

    public function sub_unit2()
    {
        return $this->belongsTo(Unit::class, "sub_unit2_id", "id");
    }

    public function cost_center()
    {
        return $this->belongsTo(CostCenter::class, "ic", "id");
    }

    public function goods_type()
    {
        return $this->belongsTo(GoodsType::class);
    }

    public function goods_kind()
    {
        return $this->belongsTo(GoodsKind::class, "goods_kind_id", "id");
    }

    public function product_type()
    {
        return $this->belongsTo(ProductType::class);
    }

    public function packing_types()
    {
        return $this->belongsToMany(PackingType::class);
    }

    public function property_value()
    {
        return $this->hasMany(GoodsKindPropertyValue::class)->
        join("goods_kind_properties", "goods_kind_properties.id", "goods_kind_property_id")->
        where("goods_kind_properties.status_id", 1200)->
        orderBy("priority_number");
    }


    /**
     * آیا در مسیر محصول کارت تولید، مسیر وجود دارد که بچ آن وابسته به تعداد بسته بندی باشد
     * @return mixed
     */
    public function has_batch_with_packaging_number($type = "exists")
    {
        switch ($type) {
            case "exists":
                return
                    LineProductStation::where([
                        "product_id" => $this->id,
                        "material_unit_type_id_dependent_to_batch" => 4
                    ])->exists();
            case "first":
                return
                    LineProductStation::where([
                        "product_id" => $this->isDateAttribute(),
                        "material_unit_type_id_dependent_to_batch" => 4
                    ])->first();
        }
        1 / 0;
    }

    public function property_permission_value($user_id = null)
    {

        $post_ids = \Auth::user()->posts->pluck("post_id");

        return $this->hasMany(GoodsKindPropertyValue::class)->
        join("goods_kind_properties", "goods_kind_properties.id", "goods_kind_property_values.goods_kind_property_id")->
        join("goods_kind_property_post", "goods_kind_property_post.goods_kind_property_id", "goods_kind_properties.id")->
        where("goods_kind_properties.status_id", 1200)->
        whereIn("post_id", $post_ids)->
        orderBy("priority_number");
    }

    public function route()
    {
        return $this->hasMany(ProductRoute::class, "product_id")->where("supply_type_id", $this->supply_type_id)->orderBy("id");
    }

    public function wastes()
    {
        return $this->hasMany(ProductWaste::class, "product_id");
    }

    public function line_product_station()
    {
        return $this->hasMany(LineProductStation::class, "product_id")->orderBy("id");
    }

    public function image()
    {
        return $this->belongsTo(File::class);
    }

    public function get_first_bom_from_route($machine)
    {
        if (!$machine) {
            return null;
        }
        $line_product_station = LineProductStation::where([
            "product_id" => $this->id,
            "machine_type_id" => $machine->machine_type_id
        ])->
        first();
        if (!$line_product_station) {
            return null;
        }
        $bom = \App\Models\LineProduct\Product\BOM\BOM::where(
            [
                "product_id" => $this->id,
                "product_route_id" => $line_product_station->product_route_id,
            ]
        )->first();

        return $bom;
    }

    public function getPropertyValue($goods_kind_property_id, $type = "property_value", $null_if_not_set = true, $caption_show = true)
    {
        switch ($type) {
            case "property_value":
                return GoodsKindPropertyValue::where([
                    "product_id" => $this->id,
                    "goods_kind_property_id" => $goods_kind_property_id
                ])->first();
                break;
            case "value":
                $item = GoodsKindPropertyValue::where([
                    "product_id" => $this->id,
                    "goods_kind_property_id" => $goods_kind_property_id
                ])->first();

                if ($item) {
                    return $item->value;
                }

                return "";
                break;

            case "caption_value":
                $property_value = GoodsKindPropertyValue::where([
                    "product_id" => $this->id,
                    "goods_kind_property_id" => $goods_kind_property_id
                ])->first();
                $property = GoodsKindProperty::find($goods_kind_property_id);

                if (!isset($property_value) && $null_if_not_set) {
                    return ($property->caption ?? "") . ": ---";
                }
                $value = $property_value->value ?? "";
                if ($property && $property->field_type_id == 3) {

                    $option = GoodsKindPropertyOption::where([
                        "goods_kind_property_id" => $property->id ?? 0,
                        "id" => $value
                    ])->first();
                    $value = $option->caption ?? "---";
                }

                return ($property->caption ?? "") . ": <b> " . $value . "</b>";
                break;
            case "caption":
                $property_value = GoodsKindPropertyValue::where([
                    "product_id" => $this->id,
                    "goods_kind_property_id" => $goods_kind_property_id
                ])->first();
                $property = GoodsKindProperty::find($goods_kind_property_id);

                if (!isset($property_value) && $null_if_not_set) {
                    return ($property->caption ?? "") . ": ---";
                }
                $value = $property_value->value ?? "";
                if ($property && $property->field_type_id == 3) {

                    $option = GoodsKindPropertyOption::where([
                        "goods_kind_property_id" => $property->id ?? 0,
                        "id" => $value
                    ])->first();
                    $value = $option->caption ?? "---";

                }

                return $value;
                break;
            case "caption_value_normal":
                $property_value = GoodsKindPropertyValue::where([
                    "product_id" => $this->id,
                    "goods_kind_property_id" => $goods_kind_property_id
                ])->first();
                $property = GoodsKindProperty::find($goods_kind_property_id);

                if (!isset($property_value) && $null_if_not_set) {
                    return ($property->caption ?? "") . ": ---";
                }
                $value = $property_value->value ?? "";
                if ($property && $property->field_type_id == 3) {

                    $option = GoodsKindPropertyOption::where([
                        "goods_kind_property_id" => $property->id ?? 0,
                        "id" => $value
                    ])->first();
                    $value = $option->caption ?? "---";

                }

                return ($property->caption ?? "") . ":  " . $value . "";
                break;
        }


    }

    public function supply_type()
    {
        return $this->belongsTo(SupplyType::class);
    }

    public function line_group()
    {
        return $this->belongsTo(LineGroup::class);
    }

    public function active_status()
    {
        return $this->belongsTo(Status::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class, "active_status_id");
    }

    public function bill_of_material()
    {
        return $this->hasMany(Product\BOM\BOM::class, "product_id", "id");
    }

    public function bill_of_material_replace_product()
    {
        return $this->hasMany(Product\BOM\BOMReplace::class, "product_id", "id");
    }

    public function bill_of_material_permutation()
    {
        return $this->hasMany(Product\BOM\BOMPermutation::class, "product_id", "id");
    }

    public function degree()
    {
        return $this->hasMany(Degree::class);
    }

    public function replace_product()
    {
        return $this->hasMany(ReplaceProduct::class, "product_id", "id");

    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, "warehouse_id", "id");
    }

    public function getMasterDegree($type = "id")
    {

        $result = Degree::getMainDegree($this->goods_kind_id);
        if ($result["result"]) {
            switch ($type) {
                case "id":
                    return $result["degree"]->id ?? null;
            }
        }
        1 / 0;
    }

    ###########################################

    public static function getAmountFromWeight(Product $product, $gross_weight, $packing_type_weight, $amount = null, $sub_amount = null, $check_weight = true, $allow_use_algorithm = false)
    {
        $final_amount = -99999999;
        $sub_amount_max = -9999999;
        if ($product->unit->weight_conversion_rate == 0) {
            // واحد اصلی وزنی نیست
            // مقدار داریم و وزن ناخالص نداریم
            $final_amount = $amount;

            $weight = $gross_weight - $packing_type_weight;
            if ($allow_use_algorithm and $final_amount == null) {
                //پارچه خام
                if ($product->goods_kind_id == 4) {
                    $final_amount = $weight / $product->weight;
                }
                //چله
                if ($product->goods_kind_id == 3) {
                    $final_amount = $weight / $product->weight;
                }
            }
        } else {

            // واحد اصلی وزنی است

            $final_amount = ($gross_weight - $packing_type_weight) * $product->unit->weight_conversion_rate;
            $weight = $gross_weight - $packing_type_weight;


        }

        if ($product->sub_unit) {
            if ($product->sub_unit->weight_conversion_rate == 0) {
                // واحد فرعی وزنی نیست
                if ($sub_amount == null) {
                    return [
                        "result" => false,
                        "error" => "مقدار واحد فرعی به درستی وارد نشده است."
                    ];
                }
                $sub_amount_max = $sub_amount;
            } else {
                // واحد فرعی وزنی است.
                $sub_amount_max = $weight * $product->sub_unit->weight_conversion_rate;
            }
        }

        // اگر مقدار نال باشد، یعنی امکان محاسبه مقدار وجود نداشته است.
        if ($final_amount == -99999999) {
            return [
                "result" => false,
                "error" => "با توجه به واحد اصلی کالا، امکان محاسبه مقدار کالا از روی وزن وجود ندارد."
            ];
        }

        if ($weight < 0 && $check_weight) {
            return [
                "result" => false,
                "error" => " مقدار وزن خالص نامعتبر است."
            ];
        }
        if ($final_amount < 0 && $check_weight) {
            return [
                "result" => false,
                "error" => " مقدار کالا نامعتبر است."
            ];
        }


        if ($gross_weight == 0 && $check_weight) {
            return [
                "result" => true,
                "weight" => 0,
                "gross_weight" => 0,
                "final_amount" => 0,
                "sub_amount" => 0
            ];
        }

        return [
            "result" => true,
            "weight" => $weight < 0 ? 0 : $weight,
            "gross_weight" => $gross_weight + 0,
            "final_amount" => $final_amount < 0 ? 0 : $final_amount,
            "sub_amount" => $sub_amount_max < 0 ? 0 : $sub_amount_max
        ];
    }

    public static function getSubAmount(Product $product, $amount)
    {
        if (!$product->sub_unit) {
            return [
                "result" => true,
                "sub_amount" => 0
            ];
        }
        //اگر واحد فرعی کالا وزنی است، امکان محاسبه مقدار فرعی وجود دارد.
        if ($product->sub_unit->weight_conversion_rate == 0) {
            return [
                "result" => false,
                "error" => "با توجه به واحد فرعی کالا، امکان محاسبه مقدار فرعی وجود ندارد."
            ];
        }
        $sub_amount = $amount * $product->weight * $product->sub_unit->weight_conversion_rate;

        return ["result" => true, "sub_amount" => $sub_amount];

    }

    public static function ExistsCode($value, $id = false, $col = 'code')
    {
        if ($id) {
            return Product::where($col, $value)->where("id", "!=", $id)->exists();
        }

        return Product::where($col, $value)->exists();
    }

    public function hasBOM()
    {
        if ($this->goods_type_id == 1 && $this->supply_type_id == 1) {
            return BOM::where("product_id", $this->id)->exists();
        }

        return true;
    }

    public function hasLineProduct()
    {
        if ($this->goods_type_id == 1 && $this->supply_type_id == 1) {
            return LineProductStation::where("product_id", $this->id)->exists();
        }

        return true;
    }

    public function ValidSelfBOM()
    {
        if (BOM::where(["product_id" => $this->id, "material_id" => $this->id])->exists()) {
            return $this->supply_type_id == 1 ? false : true;
        }

        return true;
    }

    public function fullCaption()
    {
        return $this->code . " - " . $this->caption;
    }

    public function has_product_type_permission($packing_type_id)
    {
        return ProductPackingType::where([
            "product_id" => $this->id,
            "packing_type_id" => $packing_type_id
        ])->exists();
    }

    public function is_it_salable($packing_type_id)
    {
        $item = ProductPackingType::where([
            "product_id" => $this->id,
            "packing_type_id" => $packing_type_id
        ])->first();


        return $item->is_it_salable ?? false;

    }


    public function getOffers(Customer $customer)
    {

        $offer_customer = Offer::
        where("offer_type_id", 510)->
        where("customer_id", $customer->id)->
        where("status_id", 522000200)->
        where("product_id", $this->id)->
        where("start_datetime", "<=", Carbon::now())->
        where("end_datetime", ">", Carbon::now()->addDay(-1)->format("Y-m-d"))->get();

        if (count($offer_customer) == 0) {
            $offer_customer = Offer::
            where("offer_type_id", 500)->
            where("status_id", 522000200)->
            where("channel_type_id", $customer->channel_id)->
            where("product_id", $this->id)->
            where("start_datetime", "<=", Carbon::now())->
            where("end_datetime", ">", Carbon::now()->addDay(-1)->format("Y-m-d"))->get();
        }

        return $offer_customer;

    }

    public function gerCurrentOffer(Customer $customer, $carton)
    {

        $offer_customer = Offer::
        where("offer_type_id", 510)->
        where("status_id", 522000200)->
        where("customer_id", $customer->id)->
        where("product_id", $this->id)->
        where("start_datetime", "<=", Carbon::now())->
        where("end_datetime", ">", Carbon::now()->addDay(-1)->format("Y-m-d"))->
        where("min_buy", "<=", $carton)->
        where("max_buy", ">=", $carton)->
        first();

        if (!$offer_customer) {
            $offer_customer = Offer::
            where("offer_type_id", 500)->
            where("status_id", 522000200)->
            where("channel_type_id", $customer->channel_id)->
            where("product_id", $this->id)->
            where("start_datetime", "<=", Carbon::now())->
            where("end_datetime", ">", Carbon::now()->addDay(-1)->format("Y-m-d"))->
            where("min_buy", "<=", $carton)->
            where("max_buy", ">=", $carton)->
            first();
        }

        return $offer_customer;

    }

    public function getImgName()
    {
        return Str::of($this->code)->replace("/", "-") . ".png";
    }

    public function orderPriorityNumberLineProductStation()
    {
        $list = $this->line_product_station;
        $i = 1;
        foreach ($list as $item) {
            $item->priority_number = $i++;

            $item->save();
        }
    }

    public function getWarehouseForForm(Degree $degree)
    {

        if (in_array($this->supply_type_id, [1, 3])) {
            if (!isset($degree->warehouse_id)) {
                $result["result"] = false;
                $result["error"] = "هیچ انباری برای درجه کالا تعریف نشده است.";
            } else {
                $result["result"] = true;
                $result["warehouse_id"] = $degree->warehouse_id;
            }
        } else {
            $warehouse_id = count($this->supplier) == 0 ? 0 : $this->supplier->first()["pivot"]->warehouse_id;
            if ($warehouse_id == 0) {

                // از درجه اصلی کالا انبار را انتخاب می کنیم
                $result["result"] = true;
                $result["warehouse_id"] = $degree->warehouse_id;
//                $result["result"] = false;
//                $result["error"] = "در بخش اطلاعات خرید محصول، انبار وارد نشده است";
            } else {
                $result["result"] = true;
                $result["warehouse_id"] = $warehouse_id;
            }
        }


        return $result;
    }

    public function getInventory($degree_id = null, $warehouse_id = null, $carrier_id = null, $TRUNCATE = 5, $lot_number_id = null)
    {
        return WarehouseProduct::getProductInventoryList([$this->id], $degree_id, $warehouse_id, $carrier_id, $lot_number_id, $TRUNCATE)[$this->id];
    }

    public function getReserveAmount($degree_id = null)
    {
        return OrderList::getProductReserveAmount($this->id, $degree_id)->amount ?? 0;
    }

    public function getProductTariffWithMasterDegree($tariff_id, $mysql_erp_id = false)
    {

        if ($mysql_erp_id) {
            $degree = Degree::
            on("mysql_erp_" . $mysql_erp_id)->
            where([
                "goods_kind_id" => $this->goods_kind_id ?? 0,
                // "degree_type_id" => 1
            ])->
            first();

            return ProductTariff::
            on("mysql_erp_" . $mysql_erp_id)->
            where([
                "tariff_id" => $tariff_id,
                "product_id" => $this->id,
                "degree_id" => $degree->id ?? 0
            ])->
            first();
        }
        $degree = Degree::
        where(["goods_kind_id" => $this->goods_kind_id ?? 0, "degree_type_id" => 1])->
        first();

        return ProductTariff::
        where([
            "tariff_id" => $tariff_id,
            "product_id" => $this->id,
            "degree_id" => $degree->id ?? 0
        ])->
        first();
    }

    public static function CheckWeight(Product $product, BOM $bom)
    {

        $weight = 0;
        foreach ($bom->items as $bom_item) {
            if ($bom_item->material_id == $product->id) {
                continue; // اگر BOM کالا خودش بود، رد شود.
            }
            $weight +=
                CurrentMachineInput::getConsumedAmount($bom_item->amount, $bom_item->number, $bom_item->percent_of_use) *
                $bom_item->material->weight;


        }
        $message = "دستیار هوشمند نساجی دیاکو در BOM کالا (" . $product->caption . ") ناهنجاری اطلاعات تشخیص داده است. <br/> وزن کالا نامعتبر است، وزن کالا تا لطفا با واحد اطلاعات پایه تماس بگیرید. ";

        if (round($product->weight, 7) > round($weight * (1 + $product->goods_kind->error_rate_in_checking_bom_weight / 100), 7)) {
            return [
                "result" => false,
                "error" => $message
//                 ."=>".$product->weight." -> ".$weight
            ];
        }

        if (round($product->weight + 1, 7) < round($weight * (1 - $product->goods_kind->error_rate_in_checking_bom_weight / 100) + 1, 7)) {
            return [
                "result" => false,
                "error" => $message
//                    ."=>".$product->weight." -> ".$weight
            ];

        }


        // بررسی وزن جایگشت ها
        foreach ($bom->permutations()->where("active_status_id", 1200)->get() as $permutation) {
            $weight = 0;

            foreach ($permutation->items as $permutation_item) {

                // کالای جایگزین است
                if ($permutation_item->bom_replace) {
                    // echo "replace".$permutation_item->bom_replace->amount."<br/>";
                    $weight +=
                        CurrentMachineInput::getConsumedAmount($permutation_item->bom_replace->amount, $permutation_item->bom_replace->number, $permutation_item->bom_replace->percent_of_use) *
                        $permutation_item->bom_replace->replace_product->weight;
                } else {
                    // echo "bom".$permutation_item->bom_item->amount."<br/>";
                    // کالای اصلی در BOM
                    $weight +=
                        CurrentMachineInput::getConsumedAmount($permutation_item->bom_item->amount, $permutation_item->bom_item->number, $permutation_item->bom_item->percent_of_use) *
                        $permutation_item->bom_item->material->weight;
                }


            }
            if ($permutation->weight <= 0) {
                return [
                    "result" => false,
                    "error" => "وزن کالاهای جایگزین تولید (کد " . $permutation->getSystemCode() . ")" . " ثبت نشده است، لطفا با واحد اطلاعات پایه تماس بگیرید."
                ];
            }

            $message = "دستیار هوشمند نساجی دیاکو در اطلاعات جایگزین تولید (کد  " . $permutation->getSystemCode() . ")" . " (" . $product->caption . ") ناهنجاری اطلاعات تشخیص داده است. <br/> لطفا با واحد اطلاعات پایه تماس بگیرید. ";
            $other = "";
            //   $other = ($permutation->weight)."---".$weight ."---". ((1 - $product->goods_kind->error_rate_in_checking_bom_weight / 100))."--".$permutation_item->id;
            if ($permutation->weight + 1 < $weight * (1 - $product->goods_kind->error_rate_in_checking_bom_weight / 100) + 1) {

                return [
                    "result" => false,
                    "error" => $message . $other
                ];
            }
            if ($permutation->weight + 1 > $weight + 1) {

                return [
                    "result" => false,
                    "error" => $message . $other
                ];
            }

        }


        return ["result" => true];
    }

    public function costBaseOnCurrentDay($packing_type_id, $number_format = "")
    {
        return self::GetCostBaseOnCurrentDay($this->id, $packing_type_id, $number_format);

    }

    public static function GetCostBaseOnCurrentDay($product_id, $packing_type_id, $number_format = "")
    {
        $actual_cost = ProductPricing::where([
            "product_id" => $product_id,
            "packing_type_id" => $packing_type_id
        ])->first();

        switch ($number_format) {
            case "number_format":
                if (isset($actual_cost->price)) {
                    $number = round($actual_cost->price);
                    return number_format($number);
                }

                break;
            case "number_format_with_logs":
                $price = "---";
                $list =
                    ProductPricingProductLog::
                    with("product_pricing_log")->
                    where(["product_id" => $product_id, "packing_type_id" => $packing_type_id])->
                    get();
                if (count($list) > 0) {
                    $price = "بدون قیمت";
                    if (isset($actual_cost->price)) {
                        $number = round($actual_cost->price);
                        $price = number_format($number);
                    }
                    $text = "
                    <div class='btn-group '>
                        <button class='btn drp-icon  dropdown-toggle' type='button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='true'>$price</button>
                        <div class='dropdown-menu ' x-placement='top-start' >";
                    foreach ($list as $item) {
                        $text .= "<a class='dropdown-item' href='#!'>" . ($item->product_pricing_log->worker->fullname() . " - " . $item->get_datetime() . ": " . $item->price) . "</a>";
                    }
                    $text .= "</div>
                        </div>";
                    return $text;
                }
                return $price;
                break;
        }


        return "---";

    }


    // محاسبه قیمت تمام شده بر اساس آخرین قیمت
    public static function ProductPricingPredict(Product $product, $level = 0, $consumed_amount = 0)
    {

        $html_tree = " <details>";
        //echo "<br/>".$product->id;
//        if (Cache::has('ProductPricingPredict_'.$product->id)) {
//            //  return Cache::get('ProductPricingPredict_'.$product->id);
//        }
        $predict_price = 0;
        if ($product->supply_type_id == 2) {

            // میانگین قیمت کالا در بسته بندی های مختلف
            $avg = Product\Pricing\ProductPricing::where("product_id", $product->id)->avg("price");
            if ($avg == 0) {
                return [
                    "result" => false,
                    "error" => "قیمت خرید " . $product->fullCaption() . " مشخص نشده است."
                ];
            }

            return [
                "result" => true,
                "predict_price" => $avg,
                "html_tree" => $product->caption
            ];
        }
        // اگر کالای پیمانکاری باشد، هزینه خدمت متناظر پیمانکاری هم اضافه می گردد.
        if ($product->supply_type_id == 3) {
            if (!$product->service_id_in_employer_system) {
                return [
                    "result" => false,
                    "error" => "نوع خدمت برای کالای پیمانکاری  " . $product->fullCaption() . " مشخص نشده است."
                ];
            }
            $avg = Product\Pricing\ProductPricing::where("product_id", $product->service_id_in_employer_system)->avg("price");
            $predict_price += $avg;
        }
        // اگر نوع تامین تحویل امانی باشد، هیچ هزینه ای بابت کالا دریافت نمی شود و مبلغ آن صفر می شود.
        if ($product->supply_type_id == 4) {
            return [
                "result" => true,
                "predict_price" => 0,
                "html_tree" => $product->caption . "(تحویل امانی)"
            ];
        }
        $bom = $product->bom()->first();
        $calculate_method = "";
        $message = "";
        if ($bom) {
            $html_tree .= " <p >" . " _*calculate_method*_" . "</p>";
            $html_tree .= "  <div class='folder'>";

            $bom_item_list = BOMItem::where("bill_of_material_id", $bom->id)->
            selectRaw("bill_of_material_item.material_id,sum( amount * number * percent_of_use /100 ) as consumed_amount")->
            with("material")->
            groupBy("material_id")->
            get();
            $calculate_method = "<table style='text-align: center;margin: auto'><tr><td>مبلغ کل  </td><td></td><td>مبلغ واحد</td><td></td><td>مقدار مصرف</td><td>نام کالا</td></tr>";
            foreach ($bom_item_list as $bom_item) {
                if ($bom_item->material_id == $product->id) {
                    continue; // ممکن است، یک کالا در BOM خودش هم باشد، اگر این اتفاق اوفتاد، رد می شویم.
                }

//                $bom_item_predict = Cache::lock('ProductPricingPredict_' . $bom_item->material_id, 120)->get(function () use ($bom_item,$level) {
//                    // Lock acquired for 120 seconds and automatically released...
//                    // echo "<br/>".$bom_item->material_id;
//                    return self::ProductPricingPredict($bom_item->material,$level+1);
//                });

                $bom_item_predict = self::ProductPricingPredict($bom_item->material, $level + 1, $bom_item->consumed_amount);

                if ($bom_item_predict["result"]) {

                    $material_price = $bom_item->consumed_amount *
                        $bom_item_predict["predict_price"];
                    //echo "<br/>".$bom_item->material_id."=>consume:".$bom_item->consumed_amount."=>predict". $bom_item_predict["predict_price"];
                    $predict_price += $material_price;

                    // $html_tree .= $bom_item_predict["html_tree"];
                    $calculate_method .= "<tr>";
                    $calculate_method .= "<td>" . number_format(($material_price)) . "</td>";
                    $calculate_method .= "<td>" . "=" . "</td>";
                    $calculate_method .= "<td>" . number_format(($bom_item_predict["predict_price"])) . "</td>";
                    $calculate_method .= "<td>" . "*" . "</td>";
                    $calculate_method .= "<td>" . $bom_item->consumed_amount . "</td>" .
                        "<td>" . $bom_item_predict["html_tree"] . "</td>";

                    $calculate_method .= "</tr>";


                } else {
                    return $bom_item_predict;
                }
            }
            $calculate_method .= "<tr><td>" . number_format(round($predict_price)) . "</td><td colspan='5'>جمع کل (ریال)</td></tr>";

            $calculate_method .= "</table>";
            $html_tree .= "  </div>";
        }

        $html_tree .= " <summary>" . ($level != 0 ? $product->caption : number_format(round($predict_price))) . "</summary>";

        $html_tree .= " </details>";
        $html_tree = str_replace("_*calculate_method*_", $calculate_method, $html_tree);
        $html_tree = str_replace("_*consumed_amount*_", $consumed_amount, $html_tree);

        return [
            "result" => true,
            "predict_price" => $predict_price,
            "html_tree" => $html_tree
        ];

    }

    public function costBaseOnCurrentDayPredict($number_format = "", $type = "value")
    {
        $predict = self::ProductPricingPredict($this);

        switch ($type) {
            case "value":
                if ($predict["result"]) {
                    return $number_format == "number_format" ?
                        $number_format(round($predict["predict_price"])) :
                        $predict["predict_price"];
                }

                $title = $predict["error"];

                return " <a class='text-danger' data-toggle='tooltip' data-placement='bottom' title='$title'>
                            عدم امکان محاسبه
                        </a>";
                break;
            case "result":
//                return [
//                    "result" => true,
//                    "predict_price" => -100
//                ];


                return ($predict);
        }


    }

    public function costBaseOnLatestPricePredict()
    {
        $product = $this;

        $bom = $product->bom()->first();
        if ($bom) {
            foreach ($bom->items as $bom_item) {

                // آخرین قیمت یک واحد کالا که برای مواد اولیه محاسبه شده است.
                $cost_of_one_unit =
                    PackingFormActualCost::
                    where("product_id", $bom_item->material_id)->
                    where("packing_form_actual_costs.status_id", 6060001)-> // بهای تمام شده محاسبه شده است.
                    where("packing_form_actual_costs.actual_cost_type_id", 1000)-> // بهای تمام شده
                    orderBy("id", "desc")->
                    first()->
                    cost_of_one_unit ?? 0;
                if ($cost_of_one_unit != 0) {

                    return $predict_price =
                        CurrentMachineInput::getConsumedAmount(
                            $bom_item->amount,
                            $bom_item->number,
                            $bom_item->percent_of_use
                        ) *
                        $cost_of_one_unit;

                } else {
                    $title = "به دلیل  اینکه آخرین قیمت محاسبه شده برای " . $bom_item->material->caption . " موجود نیست، امکان محاسبه وجود ندارد.";

                    return " <a class='text-danger' data-toggle='tooltip' data-placement='bottom' title='$title'>
                            عدم امکان محاسبه
                        </a>";
                }
            }
        }
    }

    public function costBaseOnAverageLatestPricePredict()
    {

        $financial_year = Setting::FinancialYear();
        $start_date_time = $financial_year["start_date_time"];
        $last_date_time = $financial_year["last_date_time"];

        $product = $this;

        $bom = $product->bom()->first();
        if ($bom) {
            foreach ($bom->items as $bom_item) {

                // آخرین قیمت یک واحد کالا که برای مواد اولیه محاسبه شده است.
                $cost_of_one_unit =
                    PackingFormActualCost::
                    where("product_id", $bom_item->material_id)->
                    where("packing_form_actual_costs.status_id", 6060001)-> // بهای تمام شده محاسبه شده است.
                    where("packing_form_actual_costs.actual_cost_type_id", 1000)-> // بهای تمام شده
                    where("packing_form_actual_costs.created_at", ">=", $start_date_time)->
                    where("packing_form_actual_costs.created_at", "<", $last_date_time)->
                    avg("cost_of_one_unit");

                if ($cost_of_one_unit != 0) {

                    return $predict_price =
                        CurrentMachineInput::getConsumedAmount(
                            $bom_item->amount,
                            $bom_item->number,
                            $bom_item->percent_of_use
                        ) *
                        $cost_of_one_unit;

                } else {
                    $title = "به دلیل   قیمت محاسبه شده برای " . $bom_item->material->caption . " موجود نیست، امکان محاسبه وجود ندارد.";

                    return " <a class='text-danger' data-toggle='tooltip' data-placement='bottom' title='$title'>
                            عدم امکان محاسبه
                        </a>";


                }
            }
        }
    }

    /*
     * چک کردن مقدار قاب با توجه به مقدار کارت تولید
     */
    public static function CheckFrameForDoffs(Product $product, $amount, $number_of_doff = null, $brand_amount = null)
    {

        if (!$product->frame_ratio_unit2 || $product->sub_unit2_id != 1400) {
            // کالا قاب ندارد ولی ممکن است لوگو داشته باشد، بنابراین اگر مقدار لوگو (برند) داشت مقدار برند هر داف را محاسبه می کنیم.

            if ($brand_amount) {
                $doff_amount_list = []; // هر داف چند قاب است.
                for ($k = 0; $k < $number_of_doff; $k++) {
                    $doff_amount_list[$k] = (round($amount / ($number_of_doff), 8));
                }

                return [
                    "result" => true,
                    "doff_frame_list" => null,
                    "doff_amount_list" => $doff_amount_list,
                    "brand_info" => AllocationBrand::GetBrandForItem($brand_amount, $doff_amount_list, $product)
                ];
            }


            return [
                "result" => true,
                "doff_frame_list" => null,
                "doff_amount_list" => null,
                "brand_info" => null,
            ];
        }

        $frame = floor(round($amount / $product->frame_ratio_unit2, 6));

        // چک کردن اینکه تعداد قاب ضحیح است.
        if (round($amount, 6) + 0 != round($frame * $product->frame_ratio_unit2, 6) + 0) {

            $min_frame = $frame - 1;
            $max_frame = $frame + 1;
            $min = $min_frame * $product->frame_ratio_unit2;
            $max = $max_frame * $product->frame_ratio_unit2;
            $middle = $frame * $product->frame_ratio_unit2;
            $unit_caption = $product->unit->caption ?? "";
            return [
                "result" => false,
                "middle_amount" => $middle,
                "error" => "با توجه به اینکه واحد فرعی 2 کالای " . $product->caption . " قاب می باشد و ضریب تبدیل آن " . $product->frame_ratio_unit2 . " می باشد، امکان ثبت(تخصیص) کارت با مقدار " . $amount . " مجاز نمی باشد." .
                    "<br/>" . " مقدار پیشنهادی:" .

                    ($min > 0 ? " $min $unit_caption ($min_frame قاب) " . "," : "") .

                    ($middle > 0 ? " $middle $unit_caption ($frame قاب) " . "," : "") .

                    " $max $unit_caption ($max_frame قاب)"
//                ."=>".round($amount, 6) ."||".round($frame * $product->frame_ratio_unit2, 6)."||".($amount / $product->frame_ratio_unit2)
            ];
        }

        // نیاز به تعداد داف نداریم.
        if ($number_of_doff == null) {
            return [
                "result" => true,
                "doff_frame_list" => null,
                "doff_amount_list" => null,
                "brand_info" => null,
            ];
        }

        $doff_frame_list = []; // هر داف چند قاب است.
        for ($k = 0; $k < $number_of_doff; $k++) {
            $doff_frame_list[$k] = floor(round($amount / ($number_of_doff * $product->frame_ratio_unit2), 8));
        }
        $w = 0;

        while ($w < 100) {
            $w++;
            $sum_doff = array_sum($doff_frame_list);

            if ($frame == $sum_doff) {
                $doff_amount_list = [
                    "result" => true,
                    "doff_frame_list" => $doff_frame_list,
                    "doff_amount_list" => array_map(fn($n) => round($n * $product->frame_ratio_unit2, 6), $doff_frame_list)
                ];

                $doff_amount_list["brand_info"] = AllocationBrand::GetBrandForItem($brand_amount, $doff_amount_list["doff_amount_list"], $product);

                return $doff_amount_list;
            }

            if ($frame < $sum_doff) {
                return [
                    "result" => false,
                    "error" => "تعداد داف با توجه به قاب به صورت صحیح محاسبه نشده است، لطفا با واحد پشتیانی تماس بگیرید."
                ];
            }

            $minValue = min($doff_frame_list);
            $minIndex = array_search($minValue, $doff_frame_list);

            $doff_frame_list[$minIndex]++;
//            return $doff_frame_list;
        }

        return [
            "result" => false,
            "error" => "تعداد داف با توجه به قاب قابل محاسبه نیست، لطفا با واحد پشتیانی تماس بگیرید."
        ];
    }
}
