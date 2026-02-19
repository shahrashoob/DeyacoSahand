<?php

namespace App\Imports;

use App\Models\Form\Packing\NewPackingFormHandling;
use App\Models\Form\Packing\PackingForm;
use App\Models\LineProduct\Carrier\Carrier;
use App\Models\LineProduct\Carrier\CarrierType;
use App\Models\LineProduct\Degree;
use App\Models\LineProduct\LotNumber;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\LineProduct\Product;
use App\Models\Warehouse\Warehouse;
use App\Models\Warehouse\WarehouseProduct;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class PackingFormHandlingImport implements ToCollection {
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

        NewPackingFormHandling::where( "id", ">", 0 )->delete();
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

            $new_packing_form_handling = new NewPackingFormHandling();

            $error_text   = "";
            $warning_text = "";

            $product = Product::GetIdFromCode( $row[ $cols["product_code"] ] );
            if ( isset( $product ) ) {
                $new_packing_form_handling->product_id = $product->id;
            } else {
                $error_text .= "کد کالا نامعتبر است." . "<br/>";
            }

            if ( is_numeric( $row[ $cols["amount"] ] ) && $row[ $cols["amount"] ] <= 0 ) {
                $error_text .= "مقدار نمی تواند کوچکتر مساوی صفر باشد." . "<br/>";
            }

            if ( is_numeric( $row[ $cols["sub_amount"] ] ) && $row[ $cols["amount"] ] < 0 ) {
                $error_text .= "مقدار فرعی نمی تواند کوچکتر از صفر باشد." . "<br/>";
            }


            // بررسی درجه
            $degree = Degree::where( [
                "goods_kind_id" => $product->goods_kind_id ?? 0,
                "code"          => $row[ $cols["degree_code"] ]
            ] )->first();
            if ( ! isset( $degree ) ) {
                $error_text .= "کد درجه نا معتبر است" . "<br/>";
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
            if ( isset( $degree ) && isset( $warehouse ) && $degree->warehouse_id != $warehouse->id ) {
                $error_text .= "کد انبار با کد درجه مطابقت ندارد." . "<br/>";
            }

            $lot_number = LotNumber::
            where( "code", $row[ $cols["lot_number_code"] ] ."")->
            where( "product_id", $product->id ?? "" )->
            first();
            if ( ! isset( $lot_number ) || $row[ $cols["lot_number_code"] ]."" == "" ) {
                $error_text .= "کد لات  در سیستم تعریف نشده است، و یا حذف گردیده" . "<br/>";
                $lot_number = null;
            }


            if ( $row[ $cols["opp_kind"] ] != 1 ) {
                $error_text .= "نوع طرف حساب نامعتبر است" . "<br/>";
            }
            if ( $row[ $cols["trans_kind"] ] + 0 != 38 ) {
                $error_text .= "نوع رخداد باید 38 - کسری انبار گردانی باشد." . "<br/>";
            }


            $packing_type = PackingType::where( "code", $row[ $cols["packing_type_code"] ] )->first();
            if ( $packing_type ) {
                $layers      = $packing_type->layers;
                $first_layer = isset( $layers ) ? $layers[0] : null;
            } else {
                $error_text .= "نوع بسته بندی به درستی انتخاب نشده است." . "<br/>";
            }

            $packing_type_product=Product\ProductPackingType::where([
                "product_id"=>$product->id??0,
                "packing_type_id"=>$packing_type->id??0
            ])->first();
            if(!$packing_type_product){
                $error_text .= "نوع بسته بندی برای کالا وجود ندارد." . "<br/>";
            }


            $carrier = null;
            if ( isset( $layers ) && count( $layers ) > 0 && isset( $first_layer->carrier_type ) && $first_layer->carrier_type->has_number_ability ) {

                $carrier = Carrier::
                where( [
                    "code"            => $row[ $cols["carrier_code"] ],
                    "carrier_type_id" => $first_layer->carrier_type->id
                ] )->first();

                if ( isset( $carrier ) && $carrier->firstProductId() != - 1 && $carrier->firstProductId() != $product->id ) {
                    $warning_text .= "این حامل قبلا با کالای دیگری پر شده است، در صورت انتخاب حامل با کالای جدید تکمیل می گردد." . "<br/>";
                }
                if ( $row[ $cols["carrier_code"] ] != "" ) {
                    $result = Carrier::firstOrCreate(
                        $row[ $cols["carrier_code"] ],
                        $first_layer->carrier_type->id,
                        5320001, // خالی
                        null );
                    if ( ! $result["result"] ) {
                        $error_text .= $result["message"] . "<br/>";
                    } else {
                        $carrier = $result["carrier"];
                    }
                }

            }

            // بررسی IC
            if ( $row[ $cols["ic"] ] == "" ) {
                $error_text .= " لطفا مرکز هزینه (IC) را مشخص نمایید." . "<br/>";
            }


            // بررسی شماره ردیف بسته بندی
            if ( is_numeric( $row[ $cols["packing_form_number"] ] ) && $row[ $cols["packing_form_number"] ] < 1 ) {
                $error_text .= "ردیف بسته بندی نمی تواند عددی کوچکتر از 1 باشد." . "<br/>";
            }
            $packing_form = PackingForm::where( "code", $row[ $cols["packing_form_number"] ] )->first();
            if ( $packing_form ) {
                $error_text .= "در ستون ردیف بسته بندی فقط یک عدد صحیح وارد کنید." . "<br/>";
            }

// بررسی نوع بسته بندی
            $packing_type = PackingType::find( $row[ $cols["packing_type_code"] ] );
            if ( ! isset( $packing_type ) || $row[ $cols["packing_type_code"] ] == "" ) {
                $error_text .= "کد نوع بسته بندی  در سامانه تعریف نشده است." . "<br/>";
            }


            $new_packing_form_handling->id = $i;

            $new_packing_form_handling->warehouse_code = $row[ $cols["warehouse_code"] ];
            $new_packing_form_handling->warehouse_id   = $warehouse->id ?? 0;

            $new_packing_form_handling->product_code = $row[ $cols["product_code"] ];
            $new_packing_form_handling->product_id   = $product->id ?? 0;


            $new_packing_form_handling->lot_number_id   = $lot_number->id ?? "";
            $new_packing_form_handling->lot_number_code = $row[ $cols["lot_number_code"] ];

            $new_packing_form_handling->degree_id   = $degree->id ?? "";
            $new_packing_form_handling->degree_code = $row[ $cols["degree_code"] ];

            $new_packing_form_handling->carrier_id   = $carrier->id ?? "";
            $new_packing_form_handling->carrier_code = $row[ $cols["carrier_code"] ];

            $new_packing_form_handling->packing_type_id   = $packing_type->id ?? "";
            $new_packing_form_handling->packing_type_code = $row[ $cols["packing_type_code"] ];

            $new_packing_form_handling->packing_form_number = $row[ $cols["packing_form_number"] ];


            $new_packing_form_handling->opp_kind   = $row[ $cols["opp_kind"] ];
            $new_packing_form_handling->amount     = $row[ $cols["amount"] ];
            $new_packing_form_handling->sub_amount = $row[ $cols["sub_amount"] ];
            $new_packing_form_handling->ic         = $row[ $cols["ic"] ];

            $new_packing_form_handling->error     = $error_format ?? $error_text;
            $new_packing_form_handling->warning   = $warning_text;
            $new_packing_form_handling->has_error = $warning_text != "" || $error_text != "";

            $new_packing_form_handling->save();

        }

        $ic_count = NewPackingFormHandling::distinct( "ic" )->count();
        if ( $ic_count != 1 && isset($new_packing_form_handling) ) {
            $new_packing_form_handling->error     .= "در هر فایل بارگذاری بسته بندی باید فقط یک ic وجود داشته باشد." . "<br/>";
            $new_packing_form_handling->has_error = 1;
            $new_packing_form_handling->save();
        }

        if ( NewPackingFormHandling::count() > 20 && isset($new_packing_form_handling)) {
            $new_packing_form_handling->error     .= "لطفا در هر بار بارگذاری تعداد ردیف های اکسل کمتر از 20 باشد." . "<br/>";
            $new_packing_form_handling->has_error = 1;
            $new_packing_form_handling->save();
        }
    }
}
