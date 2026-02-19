<?php

namespace App\Imports;

use App\Models\Form\Packing\PackingForm;
use App\Models\LineProduct\Carrier\Carrier;
use App\Models\LineProduct\Carrier\CarrierType;
use App\Models\LineProduct\Degree;
use App\Models\LineProduct\LotNumber;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\LineProduct\Product;
use App\Models\Warehouse\AddingExistingProduct;
use App\Models\Warehouse\Warehouse;
use App\Models\Warehouse\WarehouseProduct;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class AddingExistingProductsImport implements ToCollection {
    /**
     * @param Collection $collection
     */
    public function collection( Collection $rows ) {
        $cols = [
            "warehouse_code"      => 0,
            "product_code"        => 1,
            "lot_number_code"     => 2,
            "degree_code"         => 3,
            "carrier_code"        => 4,
            "packing_form_number" => 5,
            "packing_type_code"   => 6,
            "amount"              => 7,
            "sub_amount"          => 8,
            "ic"                  => 9,
            "opp_kind"            => 10,
            "trans_kind"          => 11,
        ];

        AddingExistingProduct::where( "id", ">", 0 )->delete();
        $i              = 0;
        $warehouse_list = [];

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


            $error_text   = "";
            $warning_text = "";


            if ( is_numeric( $row[ $cols["amount"] ] ) && $row[ $cols["amount"] ] < 0 ) {
                $error_text .= "مقدار نمی تواند کوچکتر مساوی صفر باشد." . "<br/>";
            }


            // بررسی کد انبار
            $warehouse = Warehouse::where( [ "code" => $row[ $cols["warehouse_code"] ] ] )->first();
            if ( ! isset( $warehouse ) ) {
                $error_text .= "کد انبار نا معتبر است" . "<br/>";
            } else {
                $warehouse_list[ $warehouse->id ] = $warehouse->id;
            }

            if ( count( $warehouse_list ) > 1 ) {
                $error_text .= "در هر بارگذاری بسته بندی فقط یک انبار را انتخاب نمایید. " . "<br/>";
            }


            if ( $row[ $cols["opp_kind"] ] != 1 ) {
                $error_text .= "نوع طرف حساب نامعتبر است" . "<br/>";
            }
            if ( $row[ $cols["trans_kind"] ] + 0 != 39 ) {
                $error_text .= "نوع رخداد باید 39 - مازاد انبار گردانی باشد." . "<br/>";
            }

            // بررسی IC
            if ( $row[ $cols["ic"] ] == "" ) {
                $error_text .= " لطفا مرکز هزینه (IC) را مشخص نمایید." . "<br/>";
            }


            // بررسی بسته بندی ها
            $packing_form = PackingForm::where( "code", $row[ $cols["packing_form_number"] ] )->first();
            if ( ! $packing_form ) {
                $error_text .= "کد بسته بندی در سامانه وجود ندارد" . "<br/>";
            } else {
                if ( $packing_form->getFinalAmount() != $row[ $cols["amount"] ] + 0 ) {
                    $error_text .= "مقدار بسته بندی وارد شده معتبر نمی باشد." . "<br/>";
                }

                if ( $packing_form->status_id != "7007003" ) {
                    $error_text .= "بسته بندی در وضعیت تحویل شده به انبار نمی باشد." . "<br/>";
                }

                // بررسی انبار ورود کالا
                foreach ( $packing_form->items as $packing_form_item ) {
                    $wp = WarehouseProduct::where( [
                        "packing_form_item_id" => $packing_form_item->id,
                        "output"               => 0
                    ] )->first();
                    if ( ! $wp ) {
                        $error_text .= "تراکنش ورود برای بسته بندی ثبت نشده است." . "<br/>";
                    } elseif
                    ( $wp->warehouse_id != ( $warehouse->id ?? 0 ) ) {
                        $error_text .= "این بسته بندی در انبار " . ( $wp->warehouse->caption ?? "نامشخص" ) . " موجود شده است." . "<br/>";

                    }

                    $wp_output = WarehouseProduct::where( [
                        "packing_form_item_id" => $packing_form_item->id,
                        "input"               => 0
                    ] )->
                    where("output","!=",0)->
                    first();
                    if($wp_output){
                        $error_text .= "برای این بسته بندی قبلا تراکنش خروج از انبار با فرم ".($wp_output->form->code??$wp_output->form_id)." ثبت شده است." . "<br/>";

                    }
                }
            }

            $new_warehouse_form_handling     = new AddingExistingProduct();
            $new_warehouse_form_handling->id = $i;

            $new_warehouse_form_handling->warehouse_code = $row[ $cols["warehouse_code"] ];
            $new_warehouse_form_handling->warehouse_id   = $warehouse->id ?? 0;

            $new_warehouse_form_handling->packing_form_number = $row[ $cols["packing_form_number"] ];
            $new_warehouse_form_handling->packing_form_id     = $packing_form->id ?? 0;


            $new_warehouse_form_handling->opp_kind   = $row[ $cols["opp_kind"] ];
            $new_warehouse_form_handling->trans_kind = $row[ $cols["trans_kind"] ];
            $new_warehouse_form_handling->amount     = $row[ $cols["amount"] ];
            $new_warehouse_form_handling->ic         = $row[ $cols["ic"] ];

            $new_warehouse_form_handling->error     = $error_format ?? $error_text;
            $new_warehouse_form_handling->warning   = $warning_text;
            $new_warehouse_form_handling->has_error = $warning_text != "" || $error_text != "";

            $new_warehouse_form_handling->save();

        }

        $ic_count = AddingExistingProduct::distinct( "ic" )->count();
        if ( $ic_count != 1 ) {
            $new_warehouse_form_handling->error     .= "در هر فایل بارگذاری بسته بندی باید فقط یک ic وجود داشته باشد." . "<br/>";
            $new_warehouse_form_handling->has_error = 1;
            $new_warehouse_form_handling->save();
        }
    }
}
