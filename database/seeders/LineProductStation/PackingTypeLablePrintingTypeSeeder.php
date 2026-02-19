<?php

namespace Database\Seeders\LineProductStation;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PackingTypeLablePrintingTypeSeeder extends Seeder
{

    private $data = [
        //

        [ "id" => 1,"is_for_packing_forms"=>1,"priority_number"=>101,"caption2"=>"S95*123 -001", "caption" => 'قالب 101 - سایز 95*123 mm - عمودی - بدون پین ',"size"=>"","printer_type_id"=>2, "width" => "95", "long" => "123" ,"orientation"=>"P","qr_size"=>100],
        [ "id" => 5,"is_for_packing_forms"=>1,"priority_number"=>102, "caption" => 'قالب 102 - سایز 95*123 mm - عمودی - با پین - شامل مشخصات اصلی رسته کالایی - شامل کد کالای مقصد و کد پیگیری ',"size"=>"", "printer_type_id"=>2,"width" => "95", "long" => "123" ,"orientation"=>"P","qr_size"=>100 ],
        [ "id" => 13,"is_for_packing_forms"=>1,"priority_number"=>103, "caption" => 'قالب 103 - سایز 95*123 mm - عمودی - با پین',"size"=>"","printer_type_id"=>2, "width" => "95", "long" => "123" ,"orientation"=>"P","qr_size"=>100],
        [ "id" => 104,"is_for_packing_forms"=>1,"priority_number"=>104, "caption" => ' قالب 104 - سایز 95*123 mm - عمودی - با پین - بدون واحد اصلی و فرعی و مشخصات رسته نخ',"size"=>"","printer_type_id"=>2, "width" => "95", "long" => "123" ,"orientation"=>"P","qr_size"=>120],
        [ "id" => 105,"is_for_packing_forms"=>1,"priority_number"=>105, "caption" => ' قالب 105 - سایز 95*123 mm - عمودی - با پین - شامل واحد اصلی و فرعی و مشخصات رسته نخ',"size"=>"","printer_type_id"=>2, "width" => "95", "long" => "123" ,"orientation"=>"P","qr_size"=>120],
        [ "id" => 106,"is_for_packing_forms"=>1,"priority_number"=>106, "caption" => ' قالب 106 - سایز 95*123 mm - عمودی - با پین - شامل واحد اصلی و فرعی ',"size"=>"","printer_type_id"=>2, "width" => "95", "long" => "123" ,"orientation"=>"P","qr_size"=>120],


//        [ "id" => 11,"is_for_packing_forms"=>1, "caption" => 'قالب 011 - سایز 95*123 mm - عمودی - با پین',"size"=>"", "printer_type_id"=>2,"width" => "95", "long" => "123" ,"orientation"=>"P","qr_size"=>100 ],
        [ "id" => 2,"is_for_packing_forms"=>1,"priority_number"=>201, "caption" => ' قالب 201 - سایز 90*60 mm - افقی - بدون پین - شامل مشخصات اصلی رسته کالایی',"size"=>"","printer_type_id"=>2, "width" => "60", "long" => "87" ,"orientation"=>"L","qr_size"=>75 ],
        [ "id" => 6,"is_for_packing_forms"=>1,"priority_number"=>202, "caption" => ' قالب 202 - سایز 90*60 mm - افقی - بدون پین - شامل مشخصات اصلی رسته کالایی - واحد اصلی بزرگ',"size"=>"","printer_type_id"=>2, "width" => "60", "long" => "87" ,"orientation"=>"L","qr_size"=>75 ],
        [ "id" => 7,"is_for_packing_forms"=>1,"priority_number"=>203, "caption" => ' قالب 203 - سایز 90*60 mm - افقی - بدون پین - شامل مشخصات اصلی رسته کالایی - واحد اصلی بزرگ - بدون وزن ناخالص',"size"=>"","printer_type_id"=>2, "width" => "60", "long" => "87" ,"orientation"=>"L","qr_size"=>75 ],
        [ "id" => 204,"is_for_packing_forms"=>1,"priority_number"=>204, "caption" => ' قالب 204 - سایز 90*60 mm - افقی - بدون پین - شامل مشخصات اصلی رسته کالایی - واحد اصلی بزرگ - بدون QR',"size"=>"","printer_type_id"=>2, "width" => "60", "long" => "87" ,"orientation"=>"L","qr_size"=>75 ],
        [ "id" => 205,"is_for_packing_forms"=>1,"priority_number"=>205, "caption" => ' قالب 205 - سایز 90*60 mm - افقی - بدون پین - شامل کد و نام مشتری - واحد اصلی بزرگ - بدون QR',"size"=>"","printer_type_id"=>2, "width" => "60", "long" => "87" ,"orientation"=>"L","qr_size"=>75 ],
        [ "id" => 206,"is_for_packing_forms"=>1,"priority_number"=>206, "caption" => ' قالب 206 - سایز 90*60 mm - افقی - بدون پین - انگلیسی - با واحد یارد',"size"=>"","printer_type_id"=>2, "width" => "60", "long" => "87" ,"orientation"=>"L","qr_size"=>75 ],
        [ "id" => 207,"is_for_packing_forms"=>1,"priority_number"=>207, "caption" => ' قالب 207 - سایز 90*60 mm - افقی - با پین - شامل مالک - واحد اصلی بزرگ',"size"=>"","printer_type_id"=>2, "width" => "60", "long" => "87" ,"orientation"=>"L","qr_size"=>75 ],
        [ "id" => 208,"is_for_packing_forms"=>1,"priority_number"=>208, "caption" => ' قالب 208 - سایز 90*60 mm - افقی - با پین - شامل کد و نام مشتری - واحد اصلی بزرگ',"size"=>"","printer_type_id"=>2, "width" => "60", "long" => "87" ,"orientation"=>"L","qr_size"=>70 ],
        [ "id" => 209,"is_for_packing_forms"=>1,"priority_number"=>209, "caption" => ' قالب 209 - سایز 90*60 mm - افقی - با پین - شامل برند ELKAVA',"size"=>"","printer_type_id"=>2, "width" => "60", "long" => "95" ,"orientation"=>"L","qr_size"=>80 ],
        [ "id" => 210,"is_for_packing_forms"=>1,"priority_number"=>210, "caption" => ' قالب 210 - سایز 90*60 mm - افقی - با پین - شامل تاریخ تولید- واحد اصلی بزرگ',"size"=>"","printer_type_id"=>2, "width" => "60", "long" => "87" ,"orientation"=>"L","qr_size"=>60 ],

        [ "id" => 12,"is_for_packing_forms"=>0,"priority_number"=>0,"caption2"=>"A5-012", "caption" => 'قالب 012 - سایز A5 - افقی - جهت برگ خروج با مشخصات اصلی رسته کالایی',"size"=>"A5","printer_type_id"=>1, "width" => "148", "long" => "210" ,"orientation"=>"L","qr_size"=>100 ],
        [ "id" => 3,"is_for_packing_forms"=>0,"priority_number"=>0,"caption2"=>"A5-003", "caption" => 'قالب 003 - سایز A5 - افقی',"size"=>"A5","printer_type_id"=>1, "width" => "148", "long" => "210" ,"orientation"=>"L","qr_size"=>100 ],
        [ "id" => 4,"is_for_packing_forms"=>0,"priority_number"=>0,"caption2"=>"A4-004", "caption" => 'قالب 004 - سایز A4 - عمودی',"size"=>"A4", "printer_type_id"=>1,"width" => "260", "long" => "357" ,"orientation"=>"P","qr_size"=>100 ],
        [ "id" => 8,"is_for_packing_forms"=>0,"priority_number"=>0,"caption2"=>"A4-008", "caption" => 'قالب 008 - سایز A4 - افقی',"size"=>"A4", "printer_type_id"=>1,"width" => "260", "long" => "357" ,"orientation"=>"L","qr_size"=>500 ],
        [ "id" => 9,"is_for_packing_forms"=>0,"priority_number"=>0,"caption2"=>"A4-009", "caption" => 'قالب 009 - سایز A4 - افقی - نام مستعار A4S1',"size"=>"A4", "printer_type_id"=>1,"width" => "260", "long" => "357" ,"orientation"=>"L","qr_size"=>500 ],
        [ "id" => 1013,"is_for_packing_forms"=>0,"priority_number"=>0,"caption2"=>"A5-1013", "caption" => 'قالب 1013 - سایز A5 - افقی',"size"=>"A5", "printer_type_id"=>1,"width" => "260", "long" => "357" ,"orientation"=>"P","qr_size"=>100 ],
        [ "id" => 1014,"is_for_packing_forms"=>0,"priority_number"=>0,"caption2"=>"A4-1014", "caption" => 'قالب 1014 - سایز A4 - افقی - شامل نام و تصویر کالا - نام مستعار A4S1014 ',"size"=>"A4", "printer_type_id"=>1,"width" => "260", "long" => "357" ,"orientation"=>"P","qr_size"=>100 ],



        [ "id" => 14,"is_for_packing_forms"=>1,"priority_number"=>301, "caption" => 'قالب 301 - اندازه 100*mm100 - بدون پین',"size"=>"","printer_type_id"=>2, "width" => "100", "long" => "100" ,"orientation"=>"P","qr_size"=>100],
        [ "id" => 302,"is_for_packing_forms"=>1,"priority_number"=>302, "caption" => 'قالب 302 - اندازه 100*mm100 - بدون پین - شامل نام و کد کالای مشتری',"size"=>"","printer_type_id"=>2, "width" => "100", "long" => "100" ,"orientation"=>"P","qr_size"=>100],
        [ "id" => 303,"is_for_packing_forms"=>1,"priority_number"=>303, "caption" => 'قالب 303 - اندازه 100*mm100 - با پین - شامل نام و کد کالای مشتری - ساده بدون جدول',"size"=>"","printer_type_id"=>2, "width" => "100", "long" => "100" ,"orientation"=>"P","qr_size"=>100],
        [ "id" => 304,"is_for_packing_forms"=>1,"priority_number"=>304, "caption" => 'قالب 304 - اندازه 100*mm100 - با پین - شامل نام و کد کالای مشتری - شامل لوگو Partack Poshesh',"size"=>"","printer_type_id"=>2, "width" => "100", "long" => "100" ,"orientation"=>"P","qr_size"=>100],
        [ "id" => 305,"is_for_packing_forms"=>1,"priority_number"=>305, "caption" => 'قالب 305 - اندازه 100*mm100 - با پین - شامل نام و کد کالای مشتری - شامل لوگو LEROUX',"size"=>"","printer_type_id"=>2, "width" => "100", "long" => "100" ,"orientation"=>"P","qr_size"=>100],
        [ "id" => 306,"is_for_packing_forms"=>1,"priority_number"=>306, "caption" => 'قالب 306 - اندازه 100*mm100 - با پین - شامل نام و کد کالای مشتری - ساده بدون جدول 2 (کارتن سفید)',"size"=>"","printer_type_id"=>2, "width" => "100", "long" => "100" ,"orientation"=>"P","qr_size"=>100],


        [ "id" => 15,"is_for_packing_forms"=>0,"priority_number"=>401, "caption" => '  قالب 401 - سایز 80*50 mm - افقی - بدون پین - شامل نام کالا',"size"=>"","printer_type_id"=>2, "width" => "50", "long" => "80" ,"orientation"=>"L","qr_size"=>75 ],


        [ "id" => 501,"is_for_packing_forms"=>1,"priority_number"=>501, "caption" => '   قالب 501 - سایز 100*50 mm - افقی - با پین - شامل فقط نام کالا - دوبل',"size"=>"","printer_type_id"=>2, "width" => "45", "long" => "110" ,"orientation"=>"L","qr_size"=>65 ],
        [ "id" => 502,"is_for_packing_forms"=>1,"priority_number"=>502, "caption" => '  قالب 502 - سایز 100*50 mm - افقی - با پین - شامل فقط کد کالا و نام کالا',"size"=>"","printer_type_id"=>2, "width" => "50", "long" => "100" ,"orientation"=>"L","qr_size"=>65 ],
        [ "id" => 503,"is_for_packing_forms"=>1,"priority_number"=>503, "caption" => '  قالب 503 - سایز 100*50 mm - افقی - با پین - شامل فقط کد کالا و نام کالا - دوبل',"size"=>"","printer_type_id"=>2, "width" => "45", "long" => "110" ,"orientation"=>"L","qr_size"=>65 ],

        [ "id" => 601,"is_for_packing_forms"=>1,"priority_number"=>601, "caption" => '  قالب 601 - سایز 55*50 mm - عمودی - با پین - شامل فقط نام کالا',"size"=>"","printer_type_id"=>2, "width" => "50", "long" => "54" ,"orientation"=>"L","qr_size"=>65 ],

    ];
    private $table = 'packing_type_label_printing_types';

    public function run() {
        DB::table( $this->table )->delete();
        foreach ( $this->data as $item ) {
            DB::table( $this->table )->insert( $item );
        }
    }
}
