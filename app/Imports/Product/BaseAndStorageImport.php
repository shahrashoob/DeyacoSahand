<?php

namespace App\Imports\Product;

use App\Models\LineProduct\GoodsKind;
use App\Models\LineProduct\Import\BaseAndStorage;
use App\Models\LineProduct\LineGroup;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\ProductType;
use App\Models\Utility\Unit;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class BaseAndStorageImport implements ToCollection {
    /**
     * @param Collection $collection
     */
    public function collection( Collection $rows ) {
        //
        $cols = [
            "product_code"        => 0,
            "caption"             => 1,
            "unit_caption"        => 2,
            "sub_unit_caption"    => 3,
            "goods_type_id"       => 4,
            "goods_kind_id"       => 5,
            "product_type_id"     => 6,
            "number_in_carton"    => 7,
            "weight"              => 8,
            "supply_type_id"      => 9,
            "possibility_of_sale" => 10,
            "min_inventory"       => 11,
            "max_inventory"       => 12,
        ];

        BaseAndStorage::where( "id", ">", 0 )->delete();
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

            $product = Product::GetIdFromCode( $row[ $cols["product_code"] ] );

            $new_product = new BaseAndStorage();
            if ( isset( $product ) ) {
                $new_product->product_id = $product->id;
            }


            $error_text = "";

            $new_product->code    = $row[ $cols["product_code"] ];
            $new_product->caption = $row[ $cols["caption"] ];

            $new_product->number_in_carton = $row[ $cols["number_in_carton"] ];
            if ( $new_product->number_in_carton < 1 ) {
                $error_text .= " تعداد در واحد اصلی نمی تواند عددی کوچکتر از 1 باشد" . "<br/>";
            }

            $new_product->supply_type_id = $row[ $cols["supply_type_id"] ];

            $unit_id    = Unit::GetIdFromCaption( $row[ $cols["unit_caption"] ] );
            $error_text .= $unit_id == - 100 ? "واحد اصلی نامعتبر است" . " (" . $row[ $cols["unit_caption"] ] .")<br/>": "";

            $sub_unit_id = Unit::GetIdFromCaption( $row[ $cols["sub_unit_caption"] ] );
            $error_text  .= $sub_unit_id == - 100 && $row[ $cols["sub_unit_caption"] ] != "" ? "واحد فرعی نامعتبر است" . "(" . $row[ $cols["sub_unit_caption"] ].")<br/>" : "";

            $new_product->unit_id = $unit_id;
            if ( $sub_unit_id != - 100 ) {
                $new_product->sub_unit_id = $sub_unit_id;
            }


            $product_type = ProductType::where( "id", $row[ $cols["product_type_id"] ] )->first();

            if ( ! $product_type ) {
                $error_text .= "<br/>" . "گروه کالایی معتبر نمی باشد.";
            }

            $new_product->product_type_id = $row[ $cols["product_type_id"] ];

            $new_product->weight = $row[ $cols["weight"] ];
            if ( $row[ $cols["weight"] ] == "" && $new_product->weight != 0 ) { //|| $product->weight==0
                $error_text .= "وزن نامعتبر می باشد" . "<br/>" . $row[ $cols["weight"] ] . "A";
            }

            $new_product->goods_type_id = $row[ $cols["goods_type_id"] ];
            if ( $new_product->goods_type_id > 2 || $new_product->goods_type_id < 1 ) {
                $error_text .= " کد نوع کالا نامعتبر است" . "<br/>";
            }


            $new_product->goods_kind_id = $row[ $cols["goods_kind_id"] ];
            $goods_kind=GoodsKind::find($row[$cols["goods_kind_id"]]);
            if(!$goods_kind){
                $error_text .= " کد رسته کالا نامعتبر است" . "<br/>";
            }
            $new_product->possibility_of_sale = $row[ $cols["possibility_of_sale"] ];
            if ( !is_numeric($row[ $cols["min_inventory"] ]) ) {
                $error_text .= "<br/>" . "امکان فروش نامعتبر است";
            }

            $new_product->min_inventory = $row[ $cols["min_inventory"] ];
            if ( !is_numeric($row[ $cols["min_inventory"] ]) ) {
                $error_text .= "<br/>" . "حداقل موجودی نامعتبر است";
            }

            $new_product->max_inventory = $row[ $cols["max_inventory"] ];
            if ( !is_numeric($row[ $cols["max_inventory"] ]) ) {
                $error_text .= "<br/>" . "حداکثر موجودی نامعتبر است";
            }
            $new_product->error =$error_format?? $error_text;

            $new_product->save();

        }

    }
}
