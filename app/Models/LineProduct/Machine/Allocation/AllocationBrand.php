<?php

namespace App\Models\LineProduct\Machine\Allocation;

use App\Models\Form\Packing\PackingForm;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\LineProduct\Product;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AllocationBrand extends Model
{
    use HasFactory;

    protected $table = "allocation_brands";
    protected $fillable = [
        "allocation_id",
        "allocation_doff_id",
        "doff_number",
        "amount_of_brand",
    ];

    public function packing_type()
    {
        return $this->belongsTo(PackingType::class);
    }

    public static function AddList($allocation_id, $list)
    {
        foreach ($list as $item) {
            $item["allocation_id"] = $allocation_id;
            AllocationDoffs::create($item);
        }

    }

    public static function CheckBrandForBrand(Product $product, $amount, $brand_amount, $type)
    {

//        if (!$product->frame_ratio_unit2 || $product->sub_unit2_id != 1400) {
//            return [
//                "result" => true,
//                "doff_frame_list" => null,
//                "doff_amount_list" => null
//            ];
//        }


        if ($type != "weight") {
            return [
                "result" => false,
                "error" => "الگوریتم محاسبه مقدار برند در حالتی که نوع محاسبه چیزی جز وزن کالا باشد، در دست پیاده سازی می باشد."
            ];
        }
        /*
                * وزن کالا
                */
        $weight = $amount // متراژ کل پارچه
            * $product->weight;
        $brand_amount_weight = $brand_amount * $product->weight;
        /**
         * محاسبه تعداد داف و متغیر های alfa, m_alfa, beta
         */
        $number_of_brand = 1;
        $kw=0;
        while ($weight / $number_of_brand > $brand_amount_weight) {
            $number_of_brand++;
            $kw++;
            if($kw> 100){
                return [
                    "result" => false,
                    "error" => "محاسبه تعداد داف در ماژول CheckBrandForBrand غیرقابل محاسبه می باشد، لطفا با واحد پشتیبانی تماس بگیرید.".
                        "weight:".$weight."<br/>".
                        "number_of_brand: ".$number_of_brand."<br/>"
                ];
            }
        }
//return "$weight / $number_of_brand > $brand_amount_weight";
//        return $number        _of_brand;
        $brand_frame_list = []; // هر برند چند قاب است.
        for ($k = 0; $k < $number_of_brand; $k++) {
            if ($product->frame_ratio_unit2) {
                $brand_frame_list[$k] = floor(round($amount / ($number_of_brand * ($product->frame_ratio_unit2 ?? 1)), 8));
            }
            $brand_amount_list[$k] = (round($amount / ($number_of_brand), 5));
        }

        if (!$product->frame_ratio_unit2 || $product->sub_unit2_id != 1400) {
            return [
                "result" => true,
                "brand_frame_list" => $brand_frame_list,
                "brand_amount_list" => $brand_amount_list,
            ];
        }

        $ratio = $brand_amount /$product->frame_ratio_unit2+0;
        if (abs($ratio - round($ratio)) >0.00001) {
            return [
                "result" => false,
                "error" => " با توجه به اینکه واحد فرعی 2 کالا قاب می باشد، مقدار لوگو(عیب یابی) باید مضربی از واحد فرعی 2 باشد." .
                    "<br/>" . " مقدار لوگو(عیب یابی): $brand_amount" . $product->unit->caption .
                    "<br/>" . " مقدار قاب: " . $product->frame_ratio_unit2 . " " . $product->unit->caption.
                    "<br/>ratio=".($ratio - round($ratio))
            ];
        }
        // چک کردن اینکه تعداد قاب ضحیح است.

        for ($k = 0; $k < $number_of_brand; $k++) {
            $brand_frame_list[$k] = floor($brand_amount_list[$k] / $product->frame_ratio_unit2);
            $brand_amount_list[$k] =round( $product->frame_ratio_unit2 * $brand_frame_list[$k],8);
        }

        $w = 0;

        while ($w < 100) {
            $w++;
            $sum_brand = array_sum($brand_amount_list);

            if (round($amount, 8) == round($sum_brand, 8)) {
                return [
                    "result" => true,
                    "brand_frame_list" => $brand_frame_list,
                    "brand_amount_list" => $brand_amount_list,
                    "number_of_brand" => $number_of_brand,
                    "amount" => $amount,
                ];
            }

            if ($amount / 2 > $sum_brand) {
                return [
                    "result" => false,
                    "error" => "تعداد برند با توجه به مقدار داف به صورت صحیح محاسبه نشده است، لطفا با واحد پشتیانی تماس بگیرید." . "<br/>" .
                        "مقدار برند:" . $amount . "<br/>مقدار کل برند:" . $sum_brand . "<br/>",
                    "brand_frame_list" => $brand_frame_list,
                    "brand_amount_list" => $brand_amount_list,
                    "number_of_brand" => $number_of_brand,
                    "amount" => $amount,
                ];
            }

            $minValue = min($brand_frame_list);
            $minIndex = array_search($minValue, $brand_frame_list);
            $brand_frame_list[$minIndex]++;
            $brand_amount_list[$minIndex] = round($product->frame_ratio_unit2 * $brand_frame_list[$minIndex],8);

        }

        return [
            "result" => false,
            "error" => "تعداد داف با توجه به قاب قابل محاسبه نیست، لطفا با واحد پشتیانی تماس بگیرید." . "$w<br/>" . $amount . "<br/>" . array_sum($brand_amount_list) . "<br/>",
            "brand_frame_list" => $brand_frame_list,
            "brand_amount_list" => $brand_amount_list,
            "number_of_brand" => $number_of_brand,
            "amount" => $amount,
        ];
    }


    public static function GetBrandForItem($brand_amount, $doff_amount_list, Product $product)
    {

        $key = 0;
        $brand_info = [];
        $brand_info[$key] = [];
        foreach ($doff_amount_list as $doff_frame_item) {


            if (!isset($brand_info[$key]["brand_amount_list"])) {

                $brand_info[$key]["brand_amount_list"] = [];
                $brand_info[$key]["brand_frame_list"] = [];

            }

            $result_brand = AllocationBrand::CheckBrandForBrand($product, $doff_frame_item, $brand_amount, "weight");
            if (!$result_brand["result"]) {
                $result_brand["error"] = "ماژول محاسبه برند می گوید:" . "<br/>" . $result_brand["error"];
                return $result_brand;
            }
            $brand_info[$key]["brand_amount_list"] = $result_brand["brand_amount_list"];
            $brand_info[$key]["brand_frame_list"] = $result_brand["brand_frame_list"];

            $key++;

        }
        return $brand_info;

    }

}
