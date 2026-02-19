<?php

namespace Database\Seeders\Accounting\Store;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    var $data = [
        [
            'id' => 1,
            'caption' => ' مجوز مشاهده بارکد بسته بندی های خوانده نشده در بارگیری',
            'description' =>"در زمان بارگیری انبار اگر به هر دلیل بارکد یک بسته بندی توسط اپراتور خوانده نشده باشد، اپراتور می تواند با ثبت این مجوز نسبت به مشاهده بسته بندی های خوانده نشده اقدام نماید.",
            'price'=>10000,
            "other_id"=>14
        ],
        [
            'id' => 2,
            'caption' => ' مجوز برگشت کالا توسط مشتری خارج از مهلت تعیین شده',
            'description' =>"مشتری بعد از دریافت کالا در صورتی که بخواهد خارج از مهلت تعیین شده در سامانه کالا را مرجوع نماید، می تواند از طریق این مجوز اقدام نماید.",
            'price'=>10000,
            "other_id"=>15
        ],
        [
            'id' => 3,
            'caption' => 'ماژول نمایش نقص های غیر مجاز کالا بر روی بسته بندی',
            'description' =>"در صورتی که کالایی نقص غیر مجاز داشته باشد، لیست نقص های غیر مجاز بر روی لیبل بسته بندی کالا نمایش داده می شود.",
            'price'=>10000,
            "other_id"=>0
        ],
        [
            'id' => 4,
            'caption' => 'بسته پشتیبانی ماهیانه',
            'description' =>"",
            'price'=>10000,
            "other_id"=>0
        ],
        [
            'id' => 5,
            'caption' => 'ماژول کنترل کیفیت',
            'description' =>"ماژول کنترل کیفیت، ابزاری است که برای نظارت، آزمایش و تضمین کیفیت محصولات و خدمات استفاده می‌شود. این ماژول شامل اجزایی مانند ثبت درخواست‌های کنترل کیفیت، انجام آزمایش‌ها، ثبت نتایج و گزارش‌دهی است. ",
            'price'=>10000,
            "other_id"=>0
        ],
        [
            'id' => 6,
            'caption' => 'ماژول هشدار تولید بیشتر',
            'description' =>"در صورت تولید بیش از حد مجاز تعیین شده، سامانه با ارسال پیام هشدار، مسئولان مربوطه را نسبت به این اقدام مطلع می کند",
            'price'=>10000,
            "other_id"=>0
        ],
        [
            'id' => 7,
            'caption' => 'مجوز تولید بیشتر',
            'description' =>"در صورت نیاز به تولید کالا بیش از مقدار کارت تولید، با ثبت درخواست مجوز و دریافت تاییدیه های مورد نیاز این امر امکان پذیر می شود. ",
            'price'=>10000,
            "other_id"=>0
        ],


    ];
    private $table = 'stores';

    public function run() {

            foreach ($this->data as $item) {
                if ( ! DB::table( $this->table )->
                where( "id", $item["id"] )->first() ) {
                DB::table($this->table)->insert($item);
            }
        }
    }
}
