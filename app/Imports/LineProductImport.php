<?php

namespace App\Imports;

use App\Models\LineProduct;
use App\Models\LineProduct\Machine\MachineType;
use App\Models\LineProduct\Station;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\LineProduct\Line;
use App\Models\LineProduct\Product;

class LineProductImport implements ToCollection {
    /**
     * @param Collection $collection
     */
    public function collection( Collection $rows ) {

        $cols = [
            "product_code"      => 0,
            "line_code"         => 1,
            "station_code"      => 2,
            "machine_type_code" => 3,

            "min_of_production" => 4,
            "setup_time"        => 5,
            "efficiency"        => 6,
                "priority_number"   => 7,
        ];
        LineProduct\Import\LineProductStation::where("id",">",0)->delete();
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

            $product    = Product::GetIdFromCode( $row[ $cols["product_code"] ] );
            if ( ! $product ) {
                $error_text = "کد کالا معتبر نمی باشد" . "<br/>";
            }

            $line = Line::GetIdFromCode( $row[ $cols["line_code"] ] );
            if ( ! $line ) {
                $error_text = "کد خط معتبر نمی باشد" . "<br/>";
            }

            $station = Station::GetIdFromCode( $row[ $cols["station_code"] ] );
            if ( ! $station ) {
                $error_text = "کد ایستگاه معتبر نمی باشد" . "<br/>";
            }

            $machine_type = MachineType::GetIdFromCode( $row[ $cols["machine_type_code"] ] );
            if ( ! $machine_type ) {
                $error_text = "کد گروه ماشین معتبر نمی باشد" . "<br/>";
            }

            if ( ! is_numeric( $row[ $cols["min_of_production"] ] ) ) {
                $error_text = "مقدار حداقل تولید معتبر نمی باشد" . "<br/>";
            }
            if ( ! is_numeric( $row[ $cols["setup_time"] ] ) ) {
                $error_text = "مقدار زمان ستاپ معتبر نمی باشد" . "<br/>";
            }
            if ( ! is_numeric( $row[ $cols["efficiency"] ] ) ) {
                $error_text = "مقدار کارایی شاخص خرد معتبر نمی باشد" . "<br/>";
            }
            if ( ! is_numeric( $row[ $cols["priority_number"] ] ) ) {
                $error_text = "مقدار اولویت معتبر نمی باشد" . "<br/>";
            }


            $new_list_station               = new  LineProduct\Import\LineProductStation();
            $new_list_station->product_code = $row[ $cols["product_code"] ];
            $new_list_station->product_id   = $product->id ?? "";
            $new_list_station->line_code    = $row[ $cols["line_code"] ];
            $new_list_station->line_id      = $line->id ?? "";
            $new_list_station->station_code = $row[ $cols["station_code"] ];
            $new_list_station->station_id   = $station->id ?? "";

            $new_list_station->machine_type_code = $row[ $cols["machine_type_code"] ];
            $new_list_station->machine_type_id   = $machine_type->id ?? "";

            $new_list_station->min_of_production = $row[ $cols["min_of_production"] ];

            $new_list_station->setup_time        = $row[ $cols["setup_time"] ];
            $new_list_station->efficiency        = $row[ $cols["efficiency"] ];
            $new_list_station->priority_number   = $row[ $cols["priority_number"] ];

            $new_list_station->error=$error_format??$error_text;

            $new_list_station->save();
        }
    }
}
