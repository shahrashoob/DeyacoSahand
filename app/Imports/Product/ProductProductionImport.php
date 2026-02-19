<?php

namespace App\Imports\Product;

use App\Models\LineProduct\GoodsKind;
use App\Models\LineProduct\Import\BaseAndStorage;
use App\Models\LineProduct\Import\ImportProductProduction;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\ProductType;
use App\Models\Utility\Unit;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ProductProductionImport implements ToCollection {
    /**
     * @param Collection $collection
     */
    public function collection( Collection $rows ) {
        //
        $cols = [
            "product_code"                => 0,
            "min_production"              => 1,
            "max_production"              => 2,
            "batch"                       => 3,
            "extra_production"            => 4,
            "percent_of_extra_production" => 5
        ];

        ImportProductProduction::where( "id", ">", 0 )->delete();
        $i = 0;
        foreach ( $rows as $row ) {
            $i ++;
            if ( $i == 1 ) {
                foreach ( $cols as $k => $v ) {
                    if ( ! isset( $row[ $cols[ $k ] ] ) || $row[ $cols[ $k ] ] != $k ) {
                        $error_format = "فرمت فایل به درستی انتخاب نشده است." . "<br/>";
                    }
                }
            }
            if ( $i <= 2 ) {
                continue;
            }
            foreach ( $cols as $k => $v ) {
                if ( ! isset( $row[ $v ] ) ) {
                    $row[ $v ] = 0;
                }
            }

            $error_text = "";

            $product = Product::GetIdFromCode( $row[ $cols["product_code"] ] );

            $production_info = new ImportProductProduction();
            if ( isset( $product ) ) {
                $production_info->product_id = $product->id;
            } else {
                $error_text .= "کد کالا نامعتبر است." . "<br/>";
            }


            $production_info->product_code = $row[ $cols["product_code"] ];


            $production_info->max_production = $row[ $cols["max_production"] ];
            if ( ! is_numeric( $row[ $cols["max_production"] ] ) ) {
                $error_text .= "<br/>" . "حداکثر تولید تکمیل نشده است";
            }
            $production_info->min_production = $row[ $cols["min_production"] ];
            if ( ! is_numeric( $row[ $cols["min_production"] ] ) ) {
                $error_text .= "<br/>" . "حداکثر تولید تکمیل نشده است";
            }

            $production_info->batch = $row[ $cols["batch"] ];
            if ( ! is_numeric( $row[ $cols["batch"] ] ) ) {
                $error_text .= "<br/>" . " بچ تولید نامعتبر است";
            }

            $production_info->percent_of_extra_production = $row[ $cols["percent_of_extra_production"] ];
            if ( ! is_numeric( $row[ $cols["percent_of_extra_production"] ] ) || $row[ $cols["percent_of_extra_production"] ] > 100 || $row[ $cols["percent_of_extra_production"] ] < 0 ) {
                $error_text .= "<br/>" . "درصد اضافه تولید نامعتبر است";
            }
            $production_info->extra_production = $row[ $cols["extra_production"] ];
            if ( ! is_numeric( $row[ $cols["extra_production"] ] ) || $row[ $cols["extra_production"] ] < 0 ) {
                $error_text .= "<br/>" . " اضافه تولید نامعتبر است";
            }


            if ( isset( $product ) && $product->supply_type_id != 1 ) {
                $error_text .= "نوع برای کالا، از نوع تولید داخلی  نمی باشد."."<br/>";
            }

            $production_info->error = $error_format ?? $error_text;

            $production_info->save();

        }

    }
}
