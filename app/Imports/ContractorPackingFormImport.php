<?php

namespace App\Imports;

use App\Models\Contractor\ImportContractorPackingForm;
use App\Models\LineProduct\Carrier\Carrier;
use App\Models\LineProduct\Degree;
use App\Models\LineProduct\LotNumber;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ContractorPackingFormImport implements ToCollection {
    /**
     * @param Collection $collection
     */
    var $contractor_allocation;
    var $packing_type;

    public function collection( Collection $rows ) {
        //
        $cols = [
            "parent_packing_form_row"     => 0, // ردیف بسته بندی اصلی
            "parent_packing_form_carrier" => 1,

            "packing_form_row"     => 2,
            "packing_form_carrier" => 3,

            "amount"          => 4,
            "sub_amount"      => 5,
            "sub_amount2"     => 6,
            "degree_code"     => 7,
            "lot_number_code" => 8,
        ];

        $warehouse_id = 0;
        ImportContractorPackingForm::where( "machine_allocation_id", $this->contractor_allocation->id )->delete();
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
            $error_text   = "";
            $warning_text = "";

            if ( $row[ $cols["amount"] ] <= 0 ) {
                $error_text .= "مقدار نمی تواند مقداری صفر و کمتر باشد." . "<br/>";
            }
            if ( $row[ $cols["sub_amount"] ] < 0 ) {
                $error_text .= "مقدار فرعی نمی تواند مقداری صفر و کمتر باشد." . "<br/>";
            }
            if ( $row[ $cols["sub_amount2"] ] < 0 ) {
                $error_text .= "مقدار فرعی2 نمی تواند مقداری صفر و کمتر باشد." . "<br/>";
            }

            // بررسی حامل اصلی

            $carrier_type_id = $this->packing_type->layers()->orderByDesc( "layer_code" )->first()->carrier_type_id;
            if ( $carrier_type_id ) {
                if ( $row[ $cols["parent_packing_form_carrier"] ] ) {
                    $result = Carrier::
                    firstOrCreate(
                        $row[ $cols["parent_packing_form_carrier"] ],
                        $carrier_type_id,
                        5320001,
                        $this->contractor_allocation->product_id
                    );
                    if ( $result["result"] ) {
                        $parent_packing_form_carrier = $result["carrier"];
                    } else {
                        $error_text .= $result["message"] . "<br/>";
                    }
                }
                else {
                    $error_text .= "ثبت شماره حامل بسته بندی اصلی الزامی است." . "<br/>";
                }
            }
            else {
                if ( $row[ $cols["parent_packing_form_carrier"] ] ) {
                    $error_text .= "این نوع بسته بندی فاقد حامل می باشد، لطفا ستون شماره حامل بسته بندی اصلی را خالی نمایید." . "<br/>";
                }
            }


            // بررسی حامل فرعی
            $sub_packing_type = $this->packing_type->first_packing_type;
            if ( $sub_packing_type ) {
                $carrier_type_id = $sub_packing_type->layers()->orderByDesc( "layer_code" )->first()->carrier_type_id;
                if ( $carrier_type_id ) {
                    // شماره حامل ثبت شده
                    if ( $row[ $cols["packing_form_carrier"] ] ) {
                        $result = Carrier::
                        firstOrCreate(
                            $row[ $cols["packing_form_carrier"] ],
                            $carrier_type_id,
                            5320001,
                            $this->contractor_allocation->product_id
                        );
                        if ( $result["result"] ) {
                            $packing_form_carrier = $result["carrier"];
                        } else {
                            $error_text .= $result["message"] . "<br/>";
                        }
                    } else {
                        $error_text .= "ثبت شماره حامل بسته بندی فرعی الزامی است." . "<br/>";
                    }
                } else {
                    if ( $row[ $cols["packing_form_carrier"] ] ) {
                        $error_text .= "این نوع بسته بندی فاقد حامل می باشد، لطفا ستون شماره حامل بسته بندی فرعی را خالی نمایید." . "<br/>";
                    }
                }

            }

            if ( ! $sub_packing_type && $row[ $cols["packing_form_row"] ] ) {
                $error_text .= $this->packing_type->caption . " دارای بسته بندی فرعی نمی باشد." . "<br/>";
            }


            $degree = Degree::
            where( "code", $row[ $cols["degree_code"] ] )->
            where( "goods_kind_id", $this->contractor_allocation->product->goods_kind_id ?? 0 )->
            first();
            if ( ! isset( $degree ) ) {
                $error_text .= "کد درجه نامعتبر است." . "<br/>";
            }

            if ( $warehouse_id != 0 && $warehouse_id != $degree->warehouse_id ) {
                $error_text .= "با توجه به تفاوت درجه کالا و انبار کالای مربوط به آن، امکان ثبت درجه های متفاوت از کالا در یک بسته بندی وجود ندارد. " . "<br/>";
            }

            $lot_number = LotNumber::where( "code", $row[ $cols["lot_number_code"] ] )->
            where( "product_id", $this->contractor_allocation->product_id ?? 0 )->
            first();
            if ( ! isset( $lot_number ) ) {
                $warning_text .= "کد لات در سیستم وجود ندارد، در صورت تایید فرم لات جدید ایجاد می شود. " . "<br/>";
            }


            ImportContractorPackingForm::create( [
                "machine_allocation_id" => $this->contractor_allocation->id,

                "parent_packing_form_row" => $row[ $cols["parent_packing_form_row"] ],
                "parent_packing_type_id"  => $this->packing_type->id ?? null,

                "parent_packing_form_carrier"    => $row[ $cols["parent_packing_form_carrier"] ],
                "parent_packing_form_carrier_id" => $parent_packing_form_carrier->id ?? null,

                "packing_form_row" => $row[ $cols["packing_form_row"] ],
                "packing_type_id"  => $sub_packing_type->id ?? null,

                "packing_form_carrier"    => $row[ $cols["packing_form_carrier"] ],
                "packing_form_carrier_id" => $packing_form_carrier->id ?? null,

                "amount"      => $row[ $cols["amount"] ],
                "sub_amount"  => $row[ $cols["sub_amount"] ],
                "sub_amount2" => $row[ $cols["sub_amount2"] ],

                "degree_code" => $row[ $cols["degree_code"] ],
                "degree_id"   => $degree->id ?? null,

                "lot_number_code" => $row[ $cols["lot_number_code"] ],
                "lot_number_id"   => $lot_number->id ?? null,

                "error"   => $error_format ?? $error_text,
                "warning" => $warning_text,

            ] );


        }
    }
}
