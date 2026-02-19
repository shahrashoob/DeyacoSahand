<?php

namespace App\Imports\Product;

use App\Models\LineProduct\Import\ImportLotNumber;
use App\Models\LineProduct\LotNumber;
use App\Models\LineProduct\Product;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class LotNumberImport implements ToCollection {
    /**
     * @param Collection $collection
     */
    public function collection( Collection $rows ) {
        ImportLotNumber::where( "id", ">", 0 )->delete();
        $cols = [
            "product_code"    => 0,
            "lot_number_code" => 1,
            "nosa_code"       => 2,
        ];

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

            $newLotNumber = new ImportLotNumber();
            $error_text   = "";
            $warning_text   = "";
            $product      = Product::GetIdFromCode( $row[ $cols["product_code"] ] );
            if ( ! isset( $product ) ) {
                $newLotNumber->product_id = 0;
                $error_text               .= "کد محصول در سامانه تعریف نشده است" . " ";
            } else {
                $newLotNumber->product_id = $product->id;
            }

            if ( $row[ $cols["lot_number_code"] ] == "" ) {
                $error_text .= "کد لات نمی تواند خالی باشد." . " ";
            }
            if(LotNumber::where(["product_id"=>$product->id,"code"=>$row[ $cols["lot_number_code"] ]])){
                $warning_text .= "این کد قبلا در سامانه تعریف شده است" . "<br/>";
            }
            $newLotNumber->product_code = $row[ $cols["product_code"] ];
            $newLotNumber->nosa_code = $row[ $cols["nosa_code"] ];
            $newLotNumber->code = $row[ $cols["lot_number_code"] ];

            $newLotNumber->error = $error_format ?? $error_text;
            $newLotNumber->warning =$warning_text;

            $newLotNumber->save();

            if ( $newLotNumber->error == "" ) {
                $count = ImportLotNumber::where( [ "product_id"  => $newLotNumber->product_id,
                                          "code" => $newLotNumber->code
                ] )->
                count();

                if ( $count > 1 ) {
                    $newLotNumber->error .= " رکورد تکراری می باشد.";
                    $newLotNumber->save();
                }
            }
        }
    }
}
