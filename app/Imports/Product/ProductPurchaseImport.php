<?php

namespace App\Imports\Product;

use App\Models\LineProduct\Import\ImportProductProduction;
use App\Models\LineProduct\Import\ImportProductPurchase;
use App\Models\LineProduct\Product;
use App\Models\Warehouse\Warehouse;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ProductPurchaseImport implements ToCollection
{
    /**
    * @param Collection $collection
    */

    public function collection( Collection $rows ) {
        //
        $cols = [
            "product_code"                => 0,
            "min_buy"              => 1,
            "max_buy"              => 2,
            "batch_buy"                       => 3,
            "warehouse_code"            => 4,
        ];

        ImportProductPurchase::where( "id", ">", 0 )->delete();
        $i = 0;
        foreach ( $rows as $row ) {
            $i ++;
            if ( $i == 1 ) {
                foreach ( $cols as $k => $v ) {
                    if ( ! isset( $row[ $cols[$k] ] ) || $row[ $cols[$k] ] != $k ) {
                        $error_format = "فرمت فایل به درستی انتخاب نشده است." . "<br/>";
                    }
                }
            }
            if ( $i <=2) {
                continue;
            }
            foreach ( $cols as $k => $v ) {
                if ( ! isset( $row[ $v ] ) ) {
                    $row[ $v ] = 0;
                }
            }

            $error_text = "";

            $product = Product::GetIdFromCode( $row[ $cols["product_code"] ] );

            $production_info = new ImportProductPurchase();
            if ( isset( $product ) ) {
                $production_info->product_id = $product->id;
            } else {
                $error_text .= "کد کالا نامعتبر است." . "<br/>";
            }


            $production_info->product_code    = $row[ $cols["product_code"] ];


            $production_info->max_buy = $row[ $cols["max_buy"] ];
            if ( ! is_numeric( $row[ $cols["max_buy"] ] ) ) {
                $error_text .= "<br/>" . "حداکثر  خرید تکمیل نشده است";
            }
            $production_info->min_buy = $row[ $cols["min_buy"] ];
            if ( ! is_numeric( $row[ $cols["min_buy"] ] ) ) {
                $error_text .= "<br/>" . "حداکثر خرید تکمیل نشده است";
            }

            $production_info->batch_buy = $row[ $cols["batch_buy"] ];
            if ( ! is_numeric( $row[ $cols["batch_buy"] ] ) ) {
                $error_text .= "<br/>" . " بچ خرید نامعتبر است";
            }

            // بررسی کد انبار
            $warehouse = Warehouse::where( [ "code" => $row[ $cols["warehouse_code"] ] ] )->first();
            if ( ! isset( $warehouse ) ) {
                $error_text .= "کد انبار نا معتبر است" . "<br/>";
            }
            $production_info->warehouse_code = $row[ $cols["warehouse_code"] ];
            $production_info->warehouse_id = $warehouse->id??0;

            if(isset($product) && $product->supply_type_id != 2){
                $error_text.="نوع تامین کالا، از نوع سفارش خرید نمی باشد."."<br/>";
            }
            $production_info->error =$error_format?? $error_text;

            $production_info->save();

        }

    }
}
