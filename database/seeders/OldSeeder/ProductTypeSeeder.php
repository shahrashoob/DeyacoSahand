<?php

namespace Database\Seeders\OldSeeder;

use Illuminate\Database\Seeder;

class ProductTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    private $data = [
        //
//        ['id' => 1, 'caption' => '  شکلات  ',"goods_kind_id"=>100],
//        ['id' => 2, 'caption' => '  حلوا کنجدی  ',"goods_kind_id"=>100],
//        ['id' => 3, 'caption' => '  روغن کلزا  ',"goods_kind_id"=>100],
//        ['id' => 4, 'caption' => '  روغن ذرت  ',"goods_kind_id"=>100],
//        ['id' => 5, 'caption' => '  ارده سفید  ',"goods_kind_id"=>100],
//        ['id' => 6, 'caption' => '  کرم ارده  ',"goods_kind_id"=>100],
//        ['id' => 7, 'caption' => '  روغن آفتابگردان  ',"goods_kind_id"=>100],
//        ['id' => 8, 'caption' => '  کره  ',"goods_kind_id"=>100],
//        ['id' => 9, 'caption' => '  کنجد  ',"goods_kind_id"=>100],
//        ['id' => 10, 'caption' => '  حلوا ارده  ',"goods_kind_id"=>100],
//        ['id' => 11, 'caption' => '  ارده قرمز  ',"goods_kind_id"=>100],
//        ['id' => 12, 'caption' => '  حلوا شکری  ',"goods_kind_id"=>100],
//        ['id' => 13, 'caption' => '  روغن ارده  ',"goods_kind_id"=>100],
//        ['id' => 14, 'caption' => '  شیره انگور  ',"goods_kind_id"=>100],
//        ['id' => 15, 'caption' => '  روغن بکر  ',"goods_kind_id"=>100],
//        ['id' => 16, 'caption' => '  قاووت  ',"goods_kind_id"=>100],
//        ['id' => 17, 'caption' => '  روغن کانولا کنجد  ',"goods_kind_id"=>100],
//        ['id' => 18, 'caption' => '  شیره توت  ',"goods_kind_id"=>100],
//        ['id' => 19, 'caption' => '  روغن ذرت کانولا  ',"goods_kind_id"=>100],
//        ['id' => 20, 'caption' => '  سه شیره  ',"goods_kind_id"=>100],
//        ['id' => 21, 'caption' => '  حلوا کشی  ',"goods_kind_id"=>100],
//        ['id' => 22, 'caption' => '  شیره خرما  ',"goods_kind_id"=>100],
//        ['id' => 23, 'caption' => '  روغن سرخ کردنی  ',"goods_kind_id"=>100],
//        ['id' => 24, 'caption' => '  ارده سفید با شیره  ',"goods_kind_id"=>100],
//        ['id' => 25, 'caption' => '  کرم نخودچی  ',"goods_kind_id"=>100],
//        ['id' => 26, 'caption' => '  کرم بیسکویت  ',"goods_kind_id"=>100],
//        ['id' => 27, 'caption' => '  ارده قرمز با شیره  ',"goods_kind_id"=>100],
//        ['id' => 28, 'caption' => '  بیسکویت  ',"goods_kind_id"=>100],
//        ['id' => 29, 'caption' => '  حاجی بادام  ',"goods_kind_id"=>100],
//        ['id' => 30, 'caption' => '  حر  ',"goods_kind_id"=>100],
//        ['id' => 31, 'caption' => '  روغن سرخ کردنی حاوی کنجد  ',"goods_kind_id"=>100],
//        ['id' => 32, 'caption' => '  روغن های مخلوط  ',"goods_kind_id"=>100],
//        ['id' => 33, 'caption' => '  روغن تصفیه  ',"goods_kind_id"=>100],
//        ['id' => 34, 'caption' => '  شربت  ',"goods_kind_id"=>100],
//        ['id' => 35, 'caption' => '  عرقيجات  ',"goods_kind_id"=>100],
//        ['id' => 36, 'caption' => '  عسل  ',"goods_kind_id"=>100],
//        ['id' => 37, 'caption' => '  مربا  ',"goods_kind_id"=>100],
//        ['id' => 38, 'caption' => '  سایر  ',"goods_kind_id"=>100],
//        ['id' => 39, 'caption' => '  نوشیدنی  ',"goods_kind_id"=>100],
//        ['id' => 40, 'caption' => '  پودر کیک  ',"goods_kind_id"=>100],
//        ['id' => 41, 'caption' => '  ارده بادام زمینی  ',"goods_kind_id"=>100],
//        ['id' => 42, 'caption' => '  مواد اولیه  ',"goods_kind_id"=>100],
//        ['id' => 43, 'caption' => '  بسته بندی  ',"goods_kind_id"=>100],
//        ['id' => 44, 'caption' => '  شیره توت تکمیلی  ',"goods_kind_id"=>100],
//        ['id' => 45, 'caption' => '  شیره خرما تکمیلی  ',"goods_kind_id"=>100],
//        ['id' => 46, 'caption' => '  شیره انگور تکمیلی  ',"goods_kind_id"=>100],
//        ['id' => 47, 'caption' => '  سه شیره تکمیلی  ',"goods_kind_id"=>100],
//        ['id' => 48, 'caption' => '  ارده تکمیلی  ',"goods_kind_id"=>100],
//        ['id' => 49, 'caption' => '  شکر  ',"goods_kind_id"=>100],
//        ['id' => 50, 'caption' => '  آجیل و خشکبار  ',"goods_kind_id"=>100],
//        ['id' => 51, 'caption' => ' روغن سویا',"goods_kind_id"=>100],
//        ['id' => 52, 'caption' => ' روغن بادام زمینی',"goods_kind_id"=>100],
//        ['id' => 53, 'caption' => ' پکییج ',"goods_kind_id"=>100],

//        ['id' => 21001, 'caption' => '100% پنبه',"goods_kind_id"=>210],
//        ['id' => 21002, 'caption' => 'پلی استر',"goods_kind_id"=>210],
//        ['id' => 21003, 'caption' => 'پلی استر پنبه',"goods_kind_id"=>210],
//        ['id' => 22001, 'caption' => '100% پنبه',"goods_kind_id"=>220],
//        ['id' => 23001, 'caption' => 'پیش فرض',"goods_kind_id"=>230],
//        ['id' => 24001, 'caption' => 'پیش فرض',"goods_kind_id"=>240],
//        ['id' => 25001, 'caption' => 'پیش فرض',"goods_kind_id"=>250],
//        ['id' => 26001, 'caption' => 'پیش فرض',"goods_kind_id"=>260],


    ];
    private $table = 'product_types';

    public function run()
    {
//        DB::table($this->table)->delete();
//        foreach ($this->data as $item) {
//            DB::table($this->table)->insert($item);
//        }
    }
}
