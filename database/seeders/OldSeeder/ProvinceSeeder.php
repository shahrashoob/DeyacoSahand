<?php

namespace Database\Seeders\OldSeeder;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    private $data = [
        //
        [ 'id' =>1 , 'caption' =>' اردبيل' ],
        [ 'id' =>2 , 'caption' =>' اصفهان' ],
        [ 'id' =>3 , 'caption' =>' البرز' ],
        [ 'id' =>4 , 'caption' =>' ايلام' ],
        [ 'id' =>5 , 'caption' =>' آذربايجان شرقي' ],
        [ 'id' =>6 , 'caption' =>' آذربايجان غربي' ],
        [ 'id' =>7 , 'caption' =>' بوشهر' ],
        [ 'id' =>8 , 'caption' =>' تهران' ],
        [ 'id' =>9 , 'caption' =>' چهارمحال وبختياري' ],
        [ 'id' =>10 , 'caption' =>' خراسان جنوبي' ],
        [ 'id' =>11 , 'caption' =>' خراسان رضوي' ],
        [ 'id' =>12 , 'caption' =>' خراسان شمالي' ],
        [ 'id' =>13 , 'caption' =>' خوزستان' ],
        [ 'id' =>14 , 'caption' =>' زنجان' ],
        [ 'id' =>15 , 'caption' =>' سمنان' ],
        [ 'id' =>16 , 'caption' =>' سيستان وبلوچستان' ],
        [ 'id' =>17 , 'caption' =>' فارس' ],
        [ 'id' =>18 , 'caption' =>' قزوين' ],
        [ 'id' =>19 , 'caption' =>' قم' ],
        [ 'id' =>20 , 'caption' =>' كردستان' ],
        [ 'id' =>21 , 'caption' =>' كرمان' ],
        [ 'id' =>22 , 'caption' =>' كرمانشاه' ],
        [ 'id' =>23 , 'caption' =>' كهگيلويه وبويراحمد' ],
        [ 'id' =>24 , 'caption' =>' گلستان' ],
        [ 'id' =>25 , 'caption' =>' گيلان' ],
        [ 'id' =>26 , 'caption' =>' لرستان' ],
        [ 'id' =>27 , 'caption' =>' مازندران' ],
        [ 'id' =>28 , 'caption' =>' مركزي' ],
        [ 'id' =>29 , 'caption' =>' هرمزگان' ],
        [ 'id' =>30 , 'caption' =>' همدان' ],
        [ 'id' =>31 , 'caption' =>' يزد' ],
        [ 'id' =>100 , 'caption' =>'   خارج از کشور' ],

    ];
    private $table = 'provinces';

    public function run() {
        DB::table( $this->table )->delete();
        foreach ( $this->data as $item ) {
            DB::table( $this->table )->insert( $item );
        }
    }
}
