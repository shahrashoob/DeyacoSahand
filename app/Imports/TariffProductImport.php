<?php

namespace App\Imports;

use App\Models\Accounting\Tariff\NewTariffProduct;
use App\Models\Accounting\Tariff\Tariff;
use App\Models\LineProduct\Degree;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\LineProduct\Product;
use App\Models\Utility\Unit;
use App\Models\Warehouse\Warehouse;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use PhpOffice\PhpSpreadsheet\Calculation\Web\Service;

class TariffProductImport implements ToCollection {
    /**
     * @param Collection $collection
     */
    var $tariff_id;

    public function collection( Collection $rows ) {
        //
        $cols = [
            "tariff_id"                  => 0,
            "product_code"               => 1,
            "product_caption"            => 2,
            "degree_code"                => 3,
            "degree_caption"             => 4,
            "packing_type_code"          => 5,
            "packing_type_caption"       => 6,
            "warehouse_code"             => 7,
            "warehouse_caption"          => 8,
            "number_in_carton"           => 9,
            "unit_caption"               => 10,
            "fea"                        => 11,
            "min_buy"                    => 12,
            "max_buy"                    => 13,
            "tax"                        => 14,
            "fare"                       => 15,
            "consumer_price"             => 16,
            "type_of_sale_of_product_id" => 17,
            "service_code"                 =>18,
            "service_caption"                 =>19,
            "increase_percentage_deadline_per_day"=>20,
            "customer_product_code"=>21,
            "customer_product_caption"=>22,

        ];

        NewTariffProduct::where( "id", ">", 0 )->delete();
        $i                     = 0;
        $master_product_degree = [];
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
            $product    = Product::GetIdFromCode( $row[ $cols["product_code"] ] );
            $service=null;

            if ( ! isset( $product ) ) {
                $error_text .= "کد محصول در سامانه تعریف نشده است." . "<br/>";
            }

            if ( $product && $product->possibility_of_sale == 0 ) {
                $error_text .= "محصول امکان فروش ندارد." . "<br/>";;
            } else {

                $type_of_sale_product = Product\TypeOfSaleProduct\TypeOfSaleOfProduct::find( $row[ $cols["type_of_sale_of_product_id"] ] );
                if ( ! $type_of_sale_product ) {
                    $error_text .= " نوع فروش (" . $row[ $cols["type_of_sale_of_product_id"] ] . ") در سیستم تعریف نشده است." . "<br/>";
                }
                else{
                    $type_of_sale_product_product = Product\TypeOfSaleProduct\TypeOfSaleProductProduct::
                    where( [
                        "product_id"                 => $product->id??0,
                        "type_of_sale_of_product_id" => $row[ $cols["type_of_sale_of_product_id"] ]
                    ] )->
                    first();
                    if ( ! $type_of_sale_product_product ) {

                        $error_text .= "نوع فروش (" . $type_of_sale_product->caption . ") برای محصول انتخاب نشده است.". "<br/>";
                    }

                    if($type_of_sale_product_product ){

                        if($type_of_sale_product_product->type_of_sale_of_product_id ==2) {
                            if ($row[$cols["service_code"]] == "") {
                                $error_text .= "از آنجایی که نوع فروش کالا کارمزدی است، کد خدمت کالا باید انتخاب شده باشد." . "<br/>";
                            } else {
                                $service = Product::GetIdFromCode($row[$cols["service_code"]]);;
                                if (!$service) {
                                    $error_text .= "کد خدمت برای فروش کارمزدی نامعتبر است." . "<br/>";
                                }

                                if (isset($service) && $type_of_sale_product_product->service_id != $service->id) {
                                    $error_text .= "کد خدمت وارد شده با کد خدمت ثبت شده برای کالا مغایرت دارد." . "<br/>";
                                }
                            }
                        }
                        elseif($row[$cols["service_code"]] ){
                            $error_text .= "از آنجایی که نوع فروش عادی می باشد، نیاز به کد خدمت نمی باشد." . "<br/>";
                        }
                    }

                }

            }


            $tariff = Tariff::find( $row[ $cols["tariff_id"] ] );
            if ( ! isset( $tariff ) ) {
                $error_text .= "کد تعرفه در سامانه تعریف نشده است." . "<br/>";
            }
            if ( ( isset( $tariff ) && $this->tariff_id != $row[ $cols["tariff_id"] ] ) ) {
                $error_text .= "کد تعرفه نامعتیر است." . "<br/>";
            }

            $degree = Degree::
            where( "code", $row[ $cols["degree_code"] ] )->
            where( "goods_kind_id", $product->goods_kind_id ?? - 1 )->
            first();
            if ( ! isset( $degree ) ) {
                $error_text .= "کد درجه نامعتبر است." . "<br/>";
            }


            $packing_type = PackingType::
            where( "code", $row[ $cols["packing_type_code"] ] )->
            first();
            if ( ! isset( $packing_type ) || $packing_type->active_status_id == 1210 ) {
                $error_text .= "کد بسته بندی نامعتبر است یا بسته بندی غیر فعال می باشد." . "<br/>";
            }

            //چک کردن درجه اصلی
            if ( isset( $degree ) && $degree->degree_type_id == 2 ) {

                $master_degree           = Degree::
                where( "goods_kind_id", $product->goods_kind_id ?? - 1 )->
                where( "degree_type_id", 1 )->
                first();
                $master_product_degree[] = [ "product_id"      => $product->id ?? 0,
                                             "degree_id"       => $master_degree->id ?? 0,
                                             "packing_type_id" => $packing_type->id ?? 0
                ];

            }

            $product_packing_type = Product\ProductPackingType::where( [
                "product_id"      => $product->id ?? 0,
                "packing_type_id" => $packing_type->id ?? 0
            ] )->first();
            if ( ! $product_packing_type ) {
                $error_text .= "این نوع بسته بندی برای کالا وجود ندارد." . "<br/>";
            }
            if ( $product_packing_type && $product_packing_type->is_it_salable == 0 ) {
                $error_text .= "این نوع بسته بندی قابلیت فروش ندارد." . "<br/>";
            }


            $warehouse = Warehouse::where( "code", $row[ $cols["warehouse_code"] ] )->first();
            if ( ! isset( $warehouse ) ) {
                $error_text .= "کد انبار نامعتبر است." . "<br/>";
            }

            $triff_product = new NewTariffProduct();


            if ( isset( $product ) && $row[ $cols["number_in_carton"] ] != $product->number_in_carton ) {
                $error_text .= "تعداد در کارتن نامعتبر است" . "<br/>";
            }
            $unit_id = Unit::GetIdFromCaption( $row[ $cols["unit_caption"] ] );
            if ( isset( $product ) && $unit_id != $product->unit_id ) {
                $error_text .= "واحد نامعتبر  است" . "<br/>";
            }

            if ( isset( $product ) && Degree::getCountMasterDegree( $product->goods_kind_id ) != 1 ) {
                $error_text .= "اطلاعات درجه بندی در رسته کالا نامعتبر است، لطفا با واحد پشتیبانی سامانه تماس بگیرید." . "<br/>";;
            }


            $error_text .= $product && $product->hasBOM() ? "" : " BOM محصول تعریف نشده است" . " <br/> " . " - لطفا با واحد برنامه ریزی تماس بگیرید ";

            $error_text .= $product && $product->hasLineProduct() ? "" : " برای محصول خط تولید تعریف نشده است " . " <br/> " . "  - لطفا با واحد برنامه ریزی تماس بگیرید   ";


            if ( $row[ $cols["max_buy"] ] < 1 ) {
                $error_text .= "حداکثر خرید باید 1 باشد." . "<br/>";
            }
            if ( $row[ $cols["min_buy"] ] < 0 ) {
                $error_text .= "حداقل خرید باید 0 باشد." . "<br/>";
            }

            if ( $row[ $cols["fea"] ] < 1 ) {
                $error_text .= "حداقل قیمت باید 1 باشد." . "<br/>";
            }

            if ( $row[ $cols["consumer_price"] ] < 0 ) {
                $error_text .= "قیمت مصرف کننده نمی تواند منفی باشد" . "<br/>";
            }
            if ( $row[ $cols["tax"] ] < 0 ) {
                $error_text .= "مالیات   نمی تواند منفی باشد" . "<br/>";
            }
            if ( $row[ $cols["fea"] ] < 0 ) {
                $error_text .= "عوارض نمی تواند منفی باشد" . "<br/>";
            }

            if ( $row[ $cols["increase_percentage_deadline_per_day"] ] < 0 ) {
                $error_text .= "به قیمت واحد فاکتور با توجه به راس پرداخت، n درصد به ازای هر روز اضافه شود، باید مقداری بزرگتر مساوی سفر باشد." . "<br/>";
            }

            $exist = NewTariffProduct::where( [
                "degree_id"       => $degree->id ?? 0,
                "product_id"      => $product->id ?? 0,
                "packing_type_id" => $packing_type->id ?? 0
            ] )->exists();
            if ( $exist ) {
                $error_text .= "این ردیف تکراری می باشد." . "<br/>";
            }

            $triff_product->tariff_id       = $row[ $cols["tariff_id"] ];
            $triff_product->fea             = $row[ $cols["fea"] ];
            $triff_product->tax             = $row[ $cols["tax"] ];
            $triff_product->fare            = $row[ $cols["fare"] ];
            $triff_product->consumer_price  = $row[ $cols["consumer_price"] ];
            $triff_product->max_buy         = $row[ $cols["max_buy"] ];
            $triff_product->min_buy         = $row[ $cols["min_buy"] ];
            $triff_product->product_code    = $row[ $cols["product_code"] ];
            $triff_product->product_id      = $product->id ?? "";
            $triff_product->service_code      =$row[ $cols["service_code"] ];
            $triff_product->service_id      = $service->id ?? "";
            $triff_product->degree_id       = $degree->id ?? "";
            $triff_product->packing_type_id = $packing_type->id ?? "";
            $triff_product->warehouse_id    = $warehouse->id ?? "";
            $triff_product->type_of_sale_of_product_id    =  $row[ $cols["type_of_sale_of_product_id"] ];
            $triff_product->increase_percentage_deadline_per_day    =  $row[ $cols["increase_percentage_deadline_per_day"] ];
            $triff_product->customer_product_code    =  $row[ $cols["customer_product_code"] ];
            $triff_product->customer_product_caption    =  $row[ $cols["customer_product_caption"] ];
            $triff_product->error           = $error_format ?? $error_text;
            $triff_product->row_id          = $i;
            $triff_product->save();

        }

        //چک کردن اینکه به ازای همه کالا های درجه اصلی در تعرفه وجود داشته باشد
        $product_list = [];
        foreach ( $master_product_degree as $item ) {
            $count = NewTariffProduct::where( $item )->count();
            if ( $count == 0 ) {
                $product_list[ $item["product_id"] ] = $item;
            }
        }

        foreach ( $product_list as $item ) {
            $ntp        = NewTariffProduct::where( "product_id", $item["product_id"] )->where( "packing_type_id", $item["packing_type_id"] )->first();
            $ntp->error = $ntp->error . "<br/>" . "درجه اصلی این کالا - بسته بندی در تعرفه وجود ندارد.";
            $ntp->save();
        }
    }
}
