<?php

namespace App\Imports\Product;

use App\Models\LineProduct\Import\ImportProductActual;
use App\Models\LineProduct\Import\ImportProductPricing;
use App\Models\LineProduct\Import\ImportProductProduction;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\LineProduct\Product;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ProductPricingImport implements ToCollection
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {
        //
        $cols = [
            "product_code" => 0,
            "product_caption" => 1,
            "packing_type_code" => 2,
            "packing_type_caption" => 3,
            "price" => 4,
        ];

        ImportProductPricing::where("id", ">", 0)->delete();
        $i = 0;
        foreach ($rows as $row) {
            $i++;
            if ($i == 1) {
                foreach ($cols as $k => $v) {
                    if (!isset($row[$cols[$k]]) || $row[$cols[$k]] != $k) {
                        $error_format = "فرمت فایل به درستی انتخاب نشده است." . "<br/>" . $v;
                    }
                }
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

            $product_actual_info = new ImportProductPricing();
            if (isset($product)) {
                $product_actual_info->product_id = $product->id;
            } else {
                $error_text .= "کد کالا نامعتبر است." . "<br/>";
            }

            $product_actual_info->product_code = $row[$cols["product_code"]];
            $product_actual_info->product_caption = $row[$cols["product_caption"]];

            if (isset($product) && $product->product_service_type_id == 1) {

                if ($product->warehouse_storage_type_id == 2) { // اگر نوع انبارش با بسته بندی است، نوع بسته بندی را مشخص نمایید.
                    // بسته بندی
                    $packing_type = PackingType::where("id", $row[$cols["packing_type_code"]])->first();
                    if (isset($packing_type)) {
                        $product_actual_info->packing_type_id = $packing_type->id;
                    } else {
                        $error_text .= "کد نوع بسته بندی نامعتبر است." . "<br/>";
                    }

                    $exists_packing_type = Product\ProductPackingType::where(["product_id" => $product->id ?? 0, "packing_type_id" => $packing_type->id ?? 0])->exists();
                    if (!$exists_packing_type) {
                        $error_text .= "نوع بسته بندی برای کالا مجاز نمی باشد.".$row[$cols["product_code"]]."<br/>";
                    }
                }
                $product_actual_info->packing_type_code = $row[$cols["packing_type_code"]];
                $product_actual_info->packing_type_caption = $row[$cols["packing_type_caption"]];
            }
            $product_actual_info->price = $row[$cols["price"]];
            if (!is_numeric($row[$cols["price"]])) {
                $error_text .= "<br/>" . "قیمت بروز کالا تکمیل نشده است.";
            }

            if (isset($product) && $product->supply_type_id != 2 && $product->product_service_type_id == 1) {
                $error_text .= "نوع تامین کالا معتبر نمی باشد، <br/> فقط برای کالاهایی که نوع تامین آنها سفارش خرید  می باشد، امکان ثبت قیمت وجود دارد." . "<br/>";
            }

            $product_actual_info->error = $error_format ?? $error_text;

            $product_actual_info->save();

        }

    }
}
