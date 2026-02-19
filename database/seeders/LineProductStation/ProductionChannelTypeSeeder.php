<?php

namespace Database\Seeders\LineProductStation;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductionChannelTypeSeeder extends Seeder
{
    private $data = [

//        [
//            "id" => 1,
//            "caption" => 'چله نخ جوشی نمره 100 سفید تراکم 60',
//            "max_capacity" => 4000,
//            "min_capacity" => 4000,
//            "max_number_of_sequences" => 99999999,
//            "production_channel_category_id" => 1001
//        ],
//        [
//            "id" => 2,
//            "caption" => 'چله نخ جوشی نمره 100 مشکی تراکم 60',
//            "max_capacity" => 4000,
//            "min_capacity" => 4000,
//            "production_channel_category_id" => 1001
//        ],
//        [
//            "id" => 3,
//            "caption" => 'چله نخ مونو نمره 20 سفید تراکم 60',
//            "max_capacity" => 4000,
//            "min_capacity" => 4000,
//            "production_channel_category_id" => 1001
//        ],
//        [
//            "id" => 4,
//            "caption" => 'چله نخ مونو نمره 20 مشکی تراکم 60',
//            "max_capacity" => 4000,
//            "min_capacity" => 4000,
//            "production_channel_category_id" => 1001
//        ],
//        [
//            "id" => 5,
//            "caption" => 'چله کشی نمره 100/40 + نخ جوشی نمره 100 سفید تراکم 60',
//            "max_capacity" => 4000,
//            "min_capacity" => 4000,
//            "production_channel_category_id" => 1001
//        ],
//        [
//            "id" => 6,
//            "caption" => 'چله کشی نمره 100/40 + نخ جوشی نمره 100 مشکی تراکم 60',
//            "max_capacity" => 4000,
//            "min_capacity" => 4000,
//            "production_channel_category_id" => 1001
//        ],
//        [
//            "id" => 7,
//            "caption" => 'چله نخ تابیده نمره 50 سفید تراکم 60',
//            "max_capacity" => 4000,
//            "min_capacity" => 4000,
//            "production_channel_category_id" => 1001
//        ],
//        [
//            "id" => 8,
//            "caption" => 'چله نخ تابیده نمره 75 سفید تراکم 60',
//            "max_capacity" => 4000,
//            "min_capacity" => 4000,
//            "max_number_of_sequences" => 99999999,
//            "production_channel_category_id" => 1001
//        ],
//        [
//            "id" => 9,
//            "caption" => 'چله نخ جوشی 100 سفید + نخ جوشی 100 مشکی  (تراکم 60)',
//            "max_capacity" => 4000,
//            "min_capacity" => 4000,
//            "production_channel_category_id" => 1001
//        ],
//        [
//            "id" => 10,
//            "caption" => 'چله نخ تابیده نمره 75 سفید تراکم 30',
//            "max_capacity" => 4000,
//            "min_capacity" => 4000,
//            "max_number_of_sequences" => 99999999,
//            "production_channel_category_id" => 1001
//        ],
//        [
//            "id" => 11,
//            "caption" => 'چله نخ جوشی 300 سفید و ملانژ تراکم 28',
//            "max_capacity" => 4000,
//            "min_capacity" => 4000,
//            "production_channel_category_id" => 1001
//        ],
//        [
//            "id" => 12,
//            "caption" => 'چله نخ جوشی 300 سفید و شانل نمره 5 تراکم 32',
//            "max_capacity" => 4000,
//            "min_capacity" => 4000,
//            "production_channel_category_id" => 1001
//        ],
//        [
//            "id" => 15,
//            "caption" => 'چله نخ نمره 50 دنیر تابیده سفید تراکم 120',
//            "max_capacity" => 4000,
//            "min_capacity" => 4000,
//            "production_channel_category_id" => 1001
//        ],
//        [
//            "id" => 16,
//            "caption" => 'چله نخ جوشی 300 سفید تراکم 32',
//            "max_capacity" => 4000,
//            "min_capacity" => 4000,
//            "max_number_of_sequences" => 99999999,
//            "production_channel_category_id" => 1001
//        ],
//        [
//            "id" => 18,
//            "caption" => 'چله نخ ۵۰ تابیده دورنگ (سفید و مشکی ) تراکم 120',
//            "max_capacity" => 4000,
//            "min_capacity" => 4000,
//            "max_number_of_sequences" => 99999999,
//            "production_channel_category_id" => 1001
//        ],
//
//        [
//            "id" => 19,
//            "caption" => 'چله نخ نمره 50 دنیر تابیده سفید تراکم 60',
//            "max_capacity" => 4000,
//            "min_capacity" => 4000,
//            "production_channel_category_id" => 1001
//        ],
//
//        [
//            "id" => 23,
//            "caption" => 'چله نخ پنبه 30/2  تراکم 60',
//            "max_capacity" => 4000,
//            "min_capacity" => 4000,
//            "max_number_of_sequences" => 1,
//            "production_channel_category_id" => 1001
//        ],
//
//        [
//            "id" => 24,
//            "caption" => 'چله نخ نمره 120 دنیر ویسکوز سفید تابیده تراکم 60',
//            "max_capacity" => 4000,
//            "min_capacity" => 4000,
//            "max_number_of_sequences" => 99999999,
//            "production_channel_category_id" => 1001
//        ],
//        [
//            "id" => 28,
//            "caption" => 'چله نخ پنبه 30/2 تراکم 20',
//            "max_capacity" => 4000,
//            "min_capacity" => 4000,
//            "max_number_of_sequences" => 99999999,
//            "production_channel_category_id" => 1001
//        ],
//        [
//            "id" => 29,
//            "caption" => 'چله یک نخ سفید یک نخ مشکی فیلامنت 150 دنیر تراکم 48 ',
//            "max_capacity" => 4000,
//            "min_capacity" => 4000,
//            "max_number_of_sequences" => 99999999,
//            "production_channel_category_id" => 1001
//        ],
//
//        [
//            "id" => 13,
//            "caption" => 'پیش فرض چله کشی',
//            "min_capacity" => 0,
//            "max_capacity" => 999999,
//            "max_number_of_sequences" => 99999999,
//            "production_channel_category_id" => 1001
//        ],
//        [
//            "id" => 14,
//            "caption" => 'پیش فرض تکمیل',
//            "min_capacity" => 4000,
//            "max_capacity" => 999999,
//            "production_channel_category_id" => 2001
//        ],
//        [
//            "id" => 20,
//            "caption" => 'رنگرزی طیف سبز',
//            "min_capacity" => 0,
//            "max_capacity" => 999999,
//            "production_channel_category_id" => 2001
//        ],
//        [
//            "id" => 21,
//            "caption" => 'پلی استر پنبه عرض 139 سانتیمتر',
//            "min_capacity" => 0,
//            "max_capacity" => 999999,
//            "production_channel_category_id" => 2001
//        ],
//
//        [
//            "id" => 32,
//            "caption" => 'پلی استر ویسکوز عرض 145 سانتیمتر',
//            "min_capacity" => 0,
//            "max_capacity" => 999999,
//            "production_channel_category_id" => 2001
//        ],
//        [
//            "id" => 22,
//            "caption" => 'رنگرزی طیف نارنجی',
//            "min_capacity" => 0,
//            "max_capacity" => 999999,
//            "production_channel_category_id" => 2001
//        ],
//        [
//            "id" => 25,
//            "caption" => 'رنگرزی طیف طوسی',
//            "min_capacity" => 0,
//            "max_capacity" => 999999,
//            "production_channel_category_id" => 2001
//        ],
//        [
//            "id" => 26,
//            "caption" => 'رنگرزی طیف زرد',
//            "min_capacity" => 0,
//            "max_capacity" => 999999,
//            "production_channel_category_id" => 2001
//        ],
//        [
//            "id" => 27,
//            "caption" => 'رنگرزی طیف کرم',
//            "min_capacity" => 0,
//            "max_capacity" => 999999,
//            "production_channel_category_id" => 2001
//        ],
//        [
//            "id" => 31,
//            "caption" => 'رنگرزی طیف مشکی',
//            "min_capacity" => 0,
//            "max_capacity" => 999999,
//            "production_channel_category_id" => 2001
//        ],
//        [
//            "id" => 34,
//            "caption" => 'رنگرزی طیف بنفش',
//            "min_capacity" => 0,
//            "max_capacity" => 999999,
//            "production_channel_category_id" => 2001
//        ],
//
//        [
//            "id" => 33,
//            "caption" => 'چله نخ کش نمره ۱۰۰/۴۰ سفیدتراکم ۶۰',
//            "min_capacity" => 0,
//            "max_capacity" => 999999,
//            "production_channel_category_id" => 1001
//        ],
//
//        [
//            "id" => 35,
//            "caption" => 'پلی استر خارخورده عرض 275-285',
//            "min_capacity" => 0,
//            "max_capacity" => 999999,
//            "production_channel_category_id" => 2001
//        ],
//
//
//        [
//            "id" => 36,
//            "caption" => ' مواد زنی 275-285',
//            "min_capacity" => 0,
//            "max_capacity" => 999999,
//            "production_channel_category_id" => 2001
//        ],
//
//
//        [
//            "id" => 37,
//            "caption" => ' خشک کردن 290-300',
//            "min_capacity" => 0,
//            "max_capacity" => 999999,
//            "production_channel_category_id" => 2001
//        ],
//
//        [
//            "id" => 38,
//            "caption" => 'پیش فرض خارزنی',
//            "min_capacity" => 0,
//            "max_capacity" => 999999,
//            "production_channel_category_id" => 2001
//        ],
//
//        [
//            "id" => 39,
//            "caption" => 'چله دو رنگ کرم سیر مشکی300 تراکم28',
//            "min_capacity" => 0,
//            "max_capacity" => 999999,
//            "production_channel_category_id" => 1001
//        ],
//        [
//            "id" => 40,
//            "caption" => 'چله دو رنگ سفید مشکی300 تراکم28',
//            "min_capacity" => 0,
//            "max_capacity" => 999999,
//            "production_channel_category_id" => 1001
//        ],
//        [
//            "id" => 41,
//            "caption" => 'چله دو رنگ مشکی طوسی روشن  300 تراکم28',
//            "min_capacity" => 0,
//            "max_capacity" => 999999,
//            "production_channel_category_id" => 1001
//        ],
//        [
//            "id" => 43,
//            "caption" => 'چله دو رنگ ذعالی مشکی 300 تراکم28',
//            "min_capacity" => 0,
//            "max_capacity" => 999999,
//            "production_channel_category_id" => 1001
//        ],
//
//        [
//            "id" => 44,
//            "caption" => 'رنگرزی طیف آبی تیره',
//            "min_capacity" => 0,
//            "max_capacity" => 999999,
//            "production_channel_category_id" => 2001
//        ],
//        [
//            "id" => 45,
//            "caption" => 'رنگرزی طیف نسکافه ای',
//            "min_capacity" => 0,
//            "max_capacity" => 999999,
//            "production_channel_category_id" => 2001
//        ],
//
//        [
//            "id" => 46,
//            "caption" => '1.5 متری مانتویی عرض 160-137 سانتی متر',
//            "min_capacity" => 0,
//            "max_capacity" => 999999,
//            "production_channel_category_id" => 2001
//        ],
//        [
//            "id" => 44,
//            "caption" => 'پیش فرض خارزنی',
//            "min_capacity" => 0,
//            "max_capacity" => 999999,
//            "production_channel_category_id" => 2001
//        ],

        // max id 34
//        [ "id" => 10000, "caption" => 'پیش فرض', "min_capacity" => 0, "max_capacity" => 999999 ],

    ];
    private $table = 'production_channel_types';

    public function run()
    {

        foreach ($this->data as $item) {

            if (!DB::table($this->table)->where("id", $item["id"])->first()) {
                DB::table($this->table)->insert($item);
            } else {
                DB::table($this->table)->where("id", $item["id"])->update($item);
            }

        }
    }
}
