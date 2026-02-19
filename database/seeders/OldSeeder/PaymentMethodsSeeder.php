<?php

namespace Database\Seeders\OldSeeder;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentMethodsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    private $data = [
        //
        [ "id" => 100, "caption" => 'پرداخت نقدی' ],
        [ "id" => 200, "caption" => ' اعتباری ' ],
        [ "id" => 310, "caption" => ' چک یک ماه ' ],
        [ "id" => 320, "caption" => ' چک دو ماه ' ],
        [ "id" => 330, "caption" => ' چک سه ماه ' ],

    ];
    private $table = 'payment_methods';

    public function run() {
        DB::table( $this->table )->delete();
        foreach ( $this->data as $item ) {
            DB::table( $this->table )->insert( $item );
        }
    }
}
