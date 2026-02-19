<?php

namespace Database\Seeders\GoodsKind\FabricRaw;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MachineOffReasonSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    // 0000 00000
    private $data = [
        [ "id" => 1601, "caption" => ' انجام مقدمات تغییر کالیته ', "station_id" => 16 ],
        [ "id" => 1602, "caption" => ' انجام مرحله دوم تغییر کالیته ', "station_id" => 16 ],
        [ "id" => 1603, "caption" => ' نداشتن طراحی ', "station_id" => 16 ],
        [ "id" => 1605, "caption" => '  بارگذاری چله ', "station_id" => 16 ],
        [ "id" => 1606, "caption" => ' چله گذاری ', "station_id" => 16 ],
        [ "id" => 1607, "caption" => ' در انتظار گره زنی ', "station_id" => 16 ],
        [ "id" => 1608, "caption" => ' گره زنی ', "station_id" => 16 ],
        [ "id" => 1609, "caption" => ' در انتظار شروع لامل ریزی ', "station_id" => 16 ],
        [ "id" => 1610, "caption" => 'راه اندازی تغییر کالیته ', "station_id" => 16 ],
        [ "id" => 1611, "caption" => 'راه اندازی شیفت ', "station_id" => 16 ],
        [ "id" => 1612, "caption" => 'لامل ریزی ', "station_id" => 16 ],
        [ "id" => 1613, "caption" => 'کنترل تغییر کالیته ', "station_id" => 16 ],
        [ "id" => 1614, "caption" => 'نداشتن چله ', "station_id" => 16 ],
        [ "id" => 1615, "caption" => 'نداشتن برنامه (سفارش) ', "station_id" => 16 ],
        [ "id" => 1616, "caption" => 'نداشتن چله جهت تعویض چله ', "station_id" => 16 ],
        [ "id" => 1617, "caption" => 'در انتظار تعویض چله ', "station_id" => 16 ],
        [ "id" => 1618, "caption" => 'تعویض چله ', "station_id" => 16 ],
        [ "id" => 1619, "caption" => 'در انتظار گره زنی تعویض چله', "station_id" => 16 ],
        [ "id" => 1620, "caption" => 'گره زنی تعویض چله', "station_id" => 16 ],
        [ "id" => 1630, "caption" => 'در انتظار شروع مقدمات تغیر کالیته', "station_id" => 16 ],
        [ "id" => 1640, "caption" => 'تغییر نخ پود', "station_id" => 16 ],
        [ "id" => 1650, "caption" => 'تعویض چله (جهت تغیر کالیته)  ', "station_id" => 16 ],
        [ "id" => 1660, "caption" => 'در انتظار گره زنی (جهت تغیر کالیته)  ', "station_id" => 16 ],
        [ "id" => 1670, "caption" => 'در حال گره زنی (جهت تغیر کالیته)  ', "station_id" => 16 ],
        [ "id" => 1680, "caption" => 'راه اندازی شیفت جهت تغییر کالیته  ', "station_id" => 16 ],
        [ "id" => 1690, "caption" => 'عملیات نگهداری و  تعمیرات ', "station_id" => 16 ],
        [ "id" => 1700, "caption" => 'تغییر نخ پود (دستور توقف)  ', "station_id" => 16 ],
        [ "id" => 1710, "caption" => ' استخراج پارچه پایانی (دستور توقف)  ', "station_id" => 16 ],
        [ "id" => 1720, "caption" => ' آماده سازی جهت لامل ریزی  ', "station_id" => 16 ],
//        [ "id" => 1725, "caption" => ' در انتظار استخراج چله (دستور توقف)  ', "station_id" => 16 ],
        [ "id" => 1730, "caption" => ' استخراج چله (دستور توقف)  ', "station_id" => 16 ],
        [ "id" => 1740, "caption" => ' در انتظار برای شروع تغییر کالیته  ', "station_id" => 16 ],
        [ "id" => 1750, "caption" => 'در انتظار شروع استخراج چله و چله گذاری  تغییر کالیته  ', "station_id" => 16 ],
        [ "id" => 1760, "caption" => ' استخراج چله و چله گذاری  تغییر کالیته  ', "station_id" => 16 ],
        [ "id" => 1770, "caption" => 'در انتظار برای شروع  تغیر کالیته', "station_id" => 16 ],
        [ "id" => 1780, "caption" => 'تغییر کالیته', "station_id" => 16 ],
        [ "id" => 1790, "caption" => 'شروع شانه کشی', "station_id" => 16 ],
        [ "id" => 1800, "caption" => ' شانه کشی', "station_id" => 16 ],
        [ "id" => 1810, "caption" => 'شروع تغییر عرض ماشین', "station_id" => 16 ],
        [ "id" => 1820, "caption" => ' تغییر عرض ماشین', "station_id" => 16 ],
        [ "id" => 1830, "caption" => 'در انتظار شروع نمونه گیری', "station_id" => 16 ],


        [ "id" => 2000, "caption" => '  نداشتن سفارش ', "station_id" => 16 ],

        // ماژول چله کشی matthys
        [ "id" => 2010, "caption" => 'در انتظار پایان قفسه گذاری', "station_id" => 50 ],
        [ "id" => 2020, "caption" => 'در انتظار شروع چله کشی', "station_id" => 50 ],
        [ "id" => 2030, "caption" => 'در انتظار شروع برگردان', "station_id" => 50 ],

        [ "id" => 4000, "caption" => '  نداشتن سفارش ', "station_id" => 50 ],

        // ماژول پارچه تکمیل Jet
        [ "id" => 5901, "caption" => ' انتظار برای انجام ستاپ (setup) ', "station_id" => 50 ],
        [ "id" => 5902, "caption" => ' انتظار برای شروع عملیات ', "station_id" => 50 ],
        [ "id" => 5904, "caption" => ' انتظار برای تنظیمات نهایی ', "station_id" => 50 ],
        [ "id" => 5905, "caption" => ' انتظار برای تایید کنترل کیفیت ', "station_id" => 50 ],
        [ "id" => 5906, "caption" => ' انتظار برای شروع تست', "station_id" => 50 ],
        [ "id" => 5907, "caption" => ' انتظار برای تایید تست', "station_id" => 50 ],

        [ "id" => 8000, "caption" => '  نداشتن سفارش ', "station_id" => 50 ],
    ];
    private $table = 'machine_off_reasons';

    public function run() {

        foreach ( $this->data as $item ) {
            if ( ! DB::table( $this->table )->where( "id", $item["id"] )->first() ) {

                DB::table( $this->table )->insert( $item );
            }
            else{
                DB::table( $this->table )->where( [ "id" => $item["id"] ] )->update( $item );
            }
        }
    }
}
