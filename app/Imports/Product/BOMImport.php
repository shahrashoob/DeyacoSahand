<?php

namespace App\Imports\Product;

use App\Models\LineProduct\Degree;
use App\Models\LineProduct\NewBOM;
use App\Models\LineProduct\NewBOMDegree;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Station;
use App\Models\LineProduct\StationOperation;
use App\Models\Warehouse\Warehouse;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class BOMImport implements ToCollection {
    /**
     * @param Collection $collection
     */
    public function collection( Collection $rows ) {
        NewBOM::where( "id", ">", 0 )->delete();
        NewBOMDegree::where( "id", ">", 0 )->delete();
        $cols            = [
            "product_code"           => 0,
            "material_code"          => 1,
            "amount"                 => 2,
            "number"                 => 3,
            "percent_of_use"         => 4,
            "station_code"           => 5,
            "station_operation_code" => 6,
            "warehouse_code"         => 7,
        ];
        $cols_number     = 7;
        $max_degree_cols = 20;
        for ( $k = 1; $k <= $max_degree_cols; $k ++ ) {
            $cols[ "degree_" . $k ] = $cols_number + $k;
        }

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

            $newBOM     = new NewBOM();
            $error_text = "";
            $product    = Product::GetIdFromCode( $row[ $cols["product_code"] ] );
            if ( ! isset( $product ) ) {
                $newBOM->product_id = 0;
                $error_text         .= "کد محصول در سامانه تعریف نشده است" . " ";
            } else {
                $newBOM->product_id = $product->id;
            }

            $material = Product::GetIdFromCode( $row[ $cols["material_code"] ] );
            if ( ! isset( $material ) ) {
                $newBOM->material_id = 0;
                $error_text          .= "کد ماده اولیه در سامانه تعریف نشده است" . " ";
            } else {
                $newBOM->material_id = $material->id;
            }

            $newBOM->amount = $row[ $cols["amount"] ];
            if ( $newBOM->amount < 0 ) {
                $error_text = " مقدار نمی تواند منفی باشد" . "<br/>";
            }
            $newBOM->number = $row[ $cols["number"] ];
            if ( ! is_numeric( $newBOM->number ) || ( is_numeric( $newBOM->number ) && fmod( $newBOM->number + 0, 1 ) > 0 ) ) {
                $error_text .= "تعداد باید یک عدد صحیح، بزرگتر از 1 باشد." . "<br/>";
            }
            $newBOM->percent_of_use = $row[ $cols["percent_of_use"] ];
            if ( ! is_numeric( $newBOM->percent_of_use ) || ( is_numeric( $newBOM->percent_of_use ) && fmod( $newBOM->number + 0, 1 ) > 0 ) ) {
                $error_text .= "تعداد باید یک عدد صحیح و بزرگتر از 1 باشد." . "<br/>";
            }

            if ( isset( $product ) && $product->supply_type_id != 1 ) {
                $error_text .= "نوع تامین کالا، از نوع تولید داخلی  نمی باشد." . "<br/>";
            }

            $station = Station::
            where( [ "code" => $row[ $cols["station_code"] ] ] )->first();
            if ( ! isset( $station ) ) {
                $error_text .= "کد ایستگاه کاری معتبر نمی باشد." . "<br/>";
            } else {
                $newBOM->station_id = $station->id;
            }

            if ( isset( $station ) && $station->active_status_id != 1200 ) {
                $error_text .= "ایستگاه کاری فعال نیست" . "<br/>";

            }
            $operation = StationOperation::where(
                [
                    "code"       => $row[ $cols["station_operation_code"] ],
                    "station_id" => $station->id ?? 0
                ] )->first();

            if ( ! $operation ) {
                $error_text .= "کد عملیات در ایستگاه کاری نامعتبر است". "<br/>";;
            } else {
                $newBOM->station_operation_id = $operation->id;
            }

            $warehouse = Warehouse::where( [ "code" => $row[ $cols["warehouse_code"] ] ] )->first();
            if ( ! isset( $warehouse ) ) {
                $error_text .= "کد انبار معتبر نمی باشد." . "<br/>";
            } else {
                $newBOM->warehouse_id = $warehouse->id;
            }
            $newBOM->save();

            // دریافت درجه های کالا
            $is_empty_col=1;
            for ( $k = 1; $k <= $max_degree_cols; $k ++ ) {
                $degree_code = $row[ $cols[ "degree_" . $k ] ];
                if ( $row[ $cols[ "degree_" . $k ] ] == "" ) {
                    $is_empty_col= $is_empty_col==1?2:$is_empty_col;
                    continue;
                }
                if($is_empty_col==2){
                    $is_empty_col=3;
                    $error_text .="ستون های درجه مجاز را به ترتیب از راست به چپ تکمیل نمایید. "."<br/>";
                }
                $degree = Degree::where( [
                    "goods_kind_id" => $product->goods_kind_id ?? 0,
                    "code"          => $degree_code
                ] )->first();

                if ( ! $degree ) {
                    $error_text .= " درجه مجار " . $k . " معتبر نمی باشد.". "<br/>";;
                } else {
                    $bom_degree_exists = NewBOMDegree::where( [
                        "new_bill_of_material_id"  => $newBOM->id ?? 0,
                        "product_id"  => $product->id ?? 0,
                        "material_id" => $material->id ?? 0,
                        "degree_id"   => $degree->id
                    ] )->exists();

                    if ( $bom_degree_exists ) {
                        $error_text .= "کد درجه " . $degree_code . " تکراری است.". "<br/>";;
                    }


                    $bom_degree_exists = NewBOMDegree::create( [
                        "new_bill_of_material_id"  => $newBOM->id ?? 0,
                        "product_id"  => $product->id ?? 0,
                        "material_id" => $material->id ?? 0,
                        "degree_id"   => $degree->id
                    ] );
                }


            }

            $newBOM->product_code   = $row[ $cols["product_code"] ];
            $newBOM->material_code  = $row[ $cols["material_code"] ];
            $newBOM->station_code   = $row[ $cols["station_code"] ];
            $newBOM->warehouse_code = $row[ $cols["warehouse_code"] ];
            $newBOM->error          = $error_format ?? $error_text;

            $newBOM->save();

            if ( $newBOM->error == "" ) {
                $count = NewBOM::where( [
                    "product_id"  => $newBOM->product_id,
                    "material_id" => $newBOM->material_id
                ] )->
                count();

                if ( $count > 1 ) {
                    $newBOM->error .= " رکورد تکراری می باشد.". "<br/>";;
                    $newBOM->save();
                }
            }
        }
    }

}
