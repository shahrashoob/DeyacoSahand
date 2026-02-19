<?php

namespace App\Imports;

use App\Models\Customer\Customer;
use App\Models\Customer\NewAccountBalance;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class AccountBalanceImport implements ToCollection {
    /**
     * @param Collection $collection
     */
    public $code;

    public function collection( Collection $rows ) {
      //
        $cols = [
            "detailed_code" => 2,
            "caption"       => 3,
            "debtor"        => 6,
            "creditor"      => 7,
        ];

        $i = 0;
        foreach ( $rows as $row ) {
            $i ++;
            if ( $i <= 2 ) {
                continue;
            }
            foreach ( $cols as $k => $v ) {
                if ( ! isset( $row[ $v ] ) ) {
                    $row[ $v ] = 0;
                }
            }

            if ( $row[ $cols["detailed_code"] ] == "" ) {
                continue;
            }

            $customer = Customer::where( "detailed_code", $row[ $cols["detailed_code"] ] )->first();

            $newAB = NewAccountBalance::create(
                [
                    "customer_id"   => $customer->id ?? 0,
                    "caption"       => $row[ $cols["caption"] ],
                    "detailed_code" => $row[ $cols["detailed_code"] ],
                    "debtor"        => $row[ $cols["debtor"] ],
                    "creditor"      => $row[ $cols["creditor"] ],
                    "error"         => isset( $customer ) ? "" : "کد تفضیلی مشتری در سامانه یافت نشد",
                    "code"          => $this->code
                ]
            );


        }
    }
}
