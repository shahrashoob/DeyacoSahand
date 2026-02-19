<?php

namespace Database\Seeders\Utility;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentMethodTypeSeeder extends Seeder
{
    private $data = [
        //
        [ "id" => 10, "caption" => 'پیش پرداخت در زمان ثبت سفارش' ],
        [ "id" => 20, "caption" => 'پیش پرداخت بعد از ثبت سفارش' ],
        [ "id" => 25, "caption" => 'پیش پرداخت اعتباری بعد از ثبت سفارش' ],
        [ "id" => 30, "caption" => 'پرداخت نقدی قبل از خروج بار' ],
        [ "id" => 40, "caption" => 'پرداخت نقدی بعد از خروج بار' ],
        [ "id" => 50, "caption" => 'پرداخت اعتباری قبل از خروج بار' ],
        [ "id" => 60, "caption" => 'پرداخت اعتباری بعد از خروج بار' ],

    ];
    private $table = 'payment_method_types';

    public function run() {
        DB::table( $this->table )->delete();
        foreach ( $this->data as $item ) {
            DB::table( $this->table )->insert( $item );
        }
    }
}
