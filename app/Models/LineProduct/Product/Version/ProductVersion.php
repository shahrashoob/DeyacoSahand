<?php

namespace App\Models\LineProduct\Product\Version;

use App\Http\Controllers\SampleController;
use App\Models\Accounting\CostCenter;
use App\Models\Accounting\Offer;
use App\Models\Accounting\Tariff\ProductTariff;
use App\Models\Customer\Customer;
use App\Models\File\File;
use App\Models\Form\Packing\PackingFormActualCost;
use App\Models\LineProduct\GoodsKindPropertyValue;
use App\Models\LineProduct\Machine\Allocation\AllocationBrand;
use App\Models\LineProduct\Machine\CurrentMachineInput;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\BOM\BOMPermutation;
use App\Models\Utility\Unit;
use App\Models\Worker;
use Carbon\Carbon;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use function PHPUnit\Framework\isNull;

class ProductVersion extends Model
{
    use HasFactory;
    use Loggable;

    protected $connection = 'mysql';
    protected $fillable = [
        "product_id",
        "version_code",
        "goods_kind_id",

        "caption",
        "code",

        "unit_id",
        "sub_unit_id",
        "sub_unit2_id",

        "weight",
        "frame_ratio_unit2",
        "property_json",
        "consume_json",
        "bill_of_material_log_id",

        "user_id",

        "change_product_cols",
        "change_property",
        "change_consume",
        "change_bom",
        'bom_permutation_json'

    ];
    public static $version_cols = [

        "weight",
        "frame_ratio_unit2",

    ];

    public function get_datetime()
    {
        return jdate(Carbon::parse($this->created_at)->timestamp)->format('H:i Y/m/d ');
    }

    public function worker()
    {
        return $this->belongsTo(Worker::class, "user_id", "id");
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, "unit_id", "id");
    }

    public function sub_unit()
    {
        return $this->belongsTo(Unit::class, "sub_unit_id", "id");
    }

    public function sub_unit2()
    {
        return $this->belongsTo(Unit::class, "sub_unit2_id", "id");
    }

    public static function GetVersion(Product $product, $check_status = true, $is_only_log = false)
    {
        $its_new_version = 0;
        $change_product_cols = 0;
        $change_property = 0;
        $change_consume = 0;
        $change_bom_permutation = 0;

        if ($check_status && $product->active_status_id == 1210) {
            return [
                "result" => false,
                "error" => "به دلیل غیر فعال بودن کالا، امکان محاسبه ورژن کالا وجود ندارد."
            ];
        }

        if (!in_array($product->goods_kind_id, [4, 5])) {
            return [
                "result" => false,
                "error" => "الگوریتم ورژن کالا مشخص نشده است."
            ];
        }
        if ($is_only_log) {
            return [
                "result" => false,
                "error" => "فقط لاگ کردیم."
            ];
        }

        $consumed_str = $product->consumed_product()->orderBy("material_id")->pluck("material_id");
        $consumed_str = json_encode($consumed_str);

        $current_property_value = GoodsKindPropertyValue::where("product_id", $product->id)->orderBy("goods_kind_property_id")->pluck("value", "goods_kind_property_id");
        $current_property_value = json_encode($current_property_value);
        $version_sum = 0;

        $list = $product->product_versions()->where("is_only_log", 0)->orderBy("id")->get();
        foreach ($list as $product_version) {

            $change_bom_permutation = self::ChangeBOMPermutation($product, $product_version);
            $change_product_cols = self::ChangeCurrentVersionText($product, $product_version);
            $change_property = self::ChangeVersion($current_property_value, $product_version->property_json);

            // $change_consume = self::ChangeVersion($consumed_str, $product_version->consume_json);
            $change_bom = 0;

            $version_sum = $change_product_cols + $change_property + $change_consume + $change_bom + $change_bom_permutation;
            $product->product_version_id = $product_version->id;
            $product->save();

            if ($version_sum == 0) {

                return [
                    "result" => true,
                    "type" => "before_version",
                    "product_version_id" => $product_version->id,
                    "version_code" => $product_version->version_code,
                    "product" => $product,
                ];
            }
//echo "=$change_product_cols + $change_property + $change_consume + $change_bom<br/>";
        }

//        $version_str="";
//        $before_string="";
//        foreach (self::$version_cols as $col) {
//            $value = trim($product->$col) . "_*_";
//            $version_str .= $value;
//
//            $value2 = trim($product_version->$col) . "_*_";
//            $before_string .= $value2;
//        }
//        return $version_str."<br/>".$before_string;
        $product_version = ProductVersion::create($product->toArray());

        $product_version->product_id = $product->id;
        $product_version->change_product_cols = $change_product_cols;
        $product_version->change_property = $change_property;
        $product_version->change_consume = $change_consume;
        $product_version->change_bom_permutation = $change_bom_permutation;
        $product_version->is_only_log = $is_only_log ? 1 : 0; // فقط لاگ می کنیم و ورژن نمی زنیم.
        //$product_version->change_bom = $change_bom;
        // $product_version->change_log_id = $last_bom_log_id;

        $product_version->property_json = $current_property_value;
        $product_version->consume_json = $consumed_str;


        // $bom_permutation_json
        $bom_permutation_json = BOMPermutation::where([
            "product_id" => $product->id,
        ])->
        pluck("id")->toArray();
        $product_version->bom_permutation_json = json_encode($bom_permutation_json);

        $product_version->bill_of_material_log_id = 0;
        $product_version->user_id = Auth::id();
        $product_version->save();

        // ذخیره ورژن کالا
        if ($is_only_log == false) {
            $product_version->version_code = ProductVersion::where("product_id", $product->id)->count();
        } else {
            $product_version->version_code = "";
        }
        $product_version->save();

        if ($is_only_log == false) {
            $product->product_version_id = $product_version->id;
            $product->save();
        }

        return [
            "result" => true,
            "type" => "new_version",
            "product_version_id" => $product_version->id,
            "version_code" => $product_version->version_code,
            "product" => $product,
        ];
    }

    public static function ChangeBOMPermutation($product, $product_version)
    {
        $count1 = BOMPermutation::where([
            "product_id" => $product->id,
        ])->
        get()->count();

        $count2 = 0;
        if ($product_version->bom_permutation_json) {
            $count2 = count(json_decode($product_version->bom_permutation_json));
        }


        if ($count1 == $count2) {
            return 0;
        } else {
            return 1;
        }
    }

    public static function ChangeCurrentVersionText(Product $product, $product_version)
    {
        $version_str = "";
        $before_string = "";

        foreach (self::$version_cols as $col) {
            $value = trim($product->$col) . "_*_";
            $version_str .= $value;

            $value2 = trim($product_version->$col) . "_*_";
            $before_string .= $value2;
        }
        if ($before_string === $version_str) {
            return 0;
        } else {
            return 1;// $before_string."<br/>".$version_str;
        }


    }

    public static function ChangeVersion($current_str, $before_json = "")
    {
        $before_json = json_decode($before_json, true);
        $before_json = self::normalizeArray($before_json);


        $current_str = json_decode($current_str, true);
        $current_str = self::normalizeArray($current_str);

        if ($current_str === $before_json) {
            return 0;
        } else {
            return 1;
        }
    }

    public static function ChangeCurrentVersionBOM(Product $product)
    {
        return 0;
    }

    public static function normalizeArray($array)
    {
        if (!is_array($array)) {
            // اگر مقدار رشته است، فاصله‌ها را حذف کن
            return is_string($array) ? trim($array) : $array;
        }

        // مرتب‌سازی کلیدها (برای اینکه ترتیب مهم نباشد)
        ksort($array);

        foreach ($array as $key => $value) {
            $array[$key] = self::normalizeArray($value);
        }

        return $array;
    }
}