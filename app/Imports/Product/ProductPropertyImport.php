<?php

namespace App\Imports\Product;

use App\Models\LineProduct\GoodsKind;
use App\Models\LineProduct\GoodsKindProperty;
use App\Models\LineProduct\GoodsKindPropertyOption;
use App\Models\LineProduct\Import\ImportProductProperty;
use App\Models\LineProduct\Product;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use phpDocumentor\Reflection\DocBlock\Tags\Property;

class ProductPropertyImport implements ToCollection {
    /**
     * @param Collection $collection
     */
    public function collection( Collection $rows ) {

        ImportProductProperty::where( "id", ">", 0 )->delete();
        $i          = 0;
        $goods_kind = new GoodsKind();
        foreach ( $rows as $row ) {
            $i ++;
            if ( $i == 1 ) {
                $goods_kind_id = $row[0];
                $goods_kind    = GoodsKind::find( $goods_kind_id );
                if ( ! $goods_kind ) {
                    $format_error                       = "فرمت فایل به درستی انتخاب نشده است." . "<br/>";
                    $new_import_property                = new ImportProductProperty();
                    $new_import_property->product_code  = $row[0];
                    $new_import_property->goods_kind_id = $goods_kind_id;
                    $new_import_property->error         = $format_error;
                    $new_import_property->row_id        = $i;
                    $new_import_property->col_id        = 0;
                    $new_import_property->save();

                    return null;
                }
                $j = 0;
                foreach ( $row as $item ) {
                    $cols[ $j ] = $item;
                    $j ++;
                }


            }
            if ( $i <= 5 ) {
                continue;
            }
            $error_text = "";

            $product = Product::GetIdFromCode( $row[0] );
            if ( ! isset( $product ) ) {
                $error_text                         .= "کد کالا نادرست است." . "<br/>";
                $new_import_property                = new ImportProductProperty();
                $new_import_property->product_code  = $row[0];
                $new_import_property->goods_kind_id = $goods_kind_id;
                $new_import_property->error         = $error_text;
                $new_import_property->row_id        = $i;
                $new_import_property->col_id        = 0;
                $new_import_property->save();

                continue;
            }

            $j = 1;
            foreach ( $goods_kind->property as $item ) {

                $error_text = "";
                if (isset( $cols[ $j ]) && $cols[ $j ] == $item->id ) {
                    $value      = $row[ $j ];
                    if ( !isset($value) && $item->required) {
                        $error_text .= "این فیلد اجباری است و نمی تواند خالی باشد." . "<br/>";
                    }
                    if ( ! is_numeric( $value ) && $item->field_type_id == 1 && $item->required ) {
                        $error_text .= "مقدار این فیلد باید عدد باشد." . "<br/>";
                    }
                    $option_item_exists = GoodsKindPropertyOption::where( [
                        "goods_kind_id"          => $goods_kind_id,
                        "goods_kind_property_id" => $item->id,
                        "id"                     => $value
                    ] )->exists();

                    if (isset($value)&& $item->field_type_id == 3 && ! $option_item_exists ) {
                        $error_text .= "مقدار این فیلد باید از لیست انتخاب شود." . "<br/>";
                    }

                } else {
                    $format_error = "فرمت فایل به درستی انتخاب نشده است." . "<br/>";

                }

                $new_import_property                = new ImportProductProperty();
                $new_import_property->product_code  = $row[0];
                $new_import_property->product_id    = $product->id ?? 0;
                $new_import_property->property_id   = $item->id;
                $new_import_property->goods_kind_id = $goods_kind_id;
                $new_import_property->error         = $format_error ?? $error_text;
                $new_import_property->row_id        = $i;
                $new_import_property->col_id        = $j+1;
                $new_import_property->value         = $value??"";
                $new_import_property->save();

                $j ++;
            }


        }
    }
}
