<?php

namespace Database\Seeders\OldSeeder;

use App\Models\Order\TransKind;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransKindSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */

    private $data = [
        //نوع رخداد ورود
        [ "id" => 100, "nosa_code" => 0, "caption" => ' خرید داخلی', "entry_type_id" => 1 ,"has_warehouse_transaction"=>1], // در نوسا کد 0 است.
        [ "id" => 1, "nosa_code" => 1, "caption" => ' خرید خارجی', "entry_type_id" => 1 ,"has_warehouse_transaction"=>1],
        [ "id" => 2, "nosa_code" => 2, "caption" => ' دریافت از تولید ', "entry_type_id" => 1,"has_warehouse_transaction"=>1],
        [ "id" => 3, "nosa_code" => 3, "caption" => 'دریافت از پیمانکار ', "entry_type_id" => 1 ,"has_accounting_document"=>1,"has_warehouse_transaction"=>1],
        [ "id" => 4, "nosa_code" => 4, "caption" => ' دریافت امانی', "entry_type_id" => 1,"has_warehouse_transaction"=>1 ],
        [ "id" => 5, "nosa_code" => 5, "caption" => ' ورود متفرقه', "entry_type_id" => 1,"has_warehouse_transaction"=>1],
        // [ "id" => 1005,"nosa_code"=>-1, "caption" => ' ورود متفرقه(ویژه سامانه)',"entry_type_id"=> 1 ],
        [ "id" => 18, "nosa_code" => 18, "caption" => 'برگشت از خروج فروش (دوره جاری)', "entry_type_id" => 1 ,"has_warehouse_transaction"=>1],
        [ "id" => 30, "nosa_code" => 30, "caption" => 'برگشت از خروج فروش (دوره قبل)', "entry_type_id" => 1 ,"has_warehouse_transaction"=>1],
        [ "id" => 38, "nosa_code" => 38, "caption" => 'کسری انبار گردانی', "entry_type_id" => 1 ,"has_warehouse_transaction"=>1],
        [ "id" => 101, "nosa_code" => -1, "caption" => 'ورود تغییر بسته بندی (سامانه)', "entry_type_id" => 1 ],
        [ "id" => 102, "nosa_code" => -1, "caption" => 'ورود تراکنش اصلاحی (سامانه)', "entry_type_id" => 1 ],
        [ "id" => 103, "nosa_code" => -1, "caption" => 'ورود ادغام بسته بندی (سامانه)', "entry_type_id" => 1 ],
        [ "id" => 104, "nosa_code" => -1, "caption" => 'ورود تغییر کالا به ضایعات (سامانه)', "entry_type_id" => 1 ],
        [ "id" => 20, "nosa_code" => 20, "caption" => 'برگشت تحویل به تولید دوره جاری', "entry_type_id" => 1,"has_warehouse_transaction"=>1 ],
        [ "id" => 32, "nosa_code" => 32, "caption" => 'برگشت تحویل به تولید دوره قبل', "entry_type_id" => 1,"has_warehouse_transaction"=>1 ],


        //نوع رخداد خروج
        [ "id" => 6, "nosa_code" => 6, "caption" => 'فروش', "entry_type_id" => 2,"has_accounting_document"=>1,"has_warehouse_transaction"=>1,"has_sale_invoice"=>1 ],
        [ "id" => 7, "nosa_code" => 7, "caption" => 'مصرف', "entry_type_id" => 2,"has_warehouse_transaction"=>1 ],
        [ "id" => 8, "nosa_code" => 8, "caption" => 'تحویل به تولید', "entry_type_id" => 2,"has_warehouse_transaction"=>1 ],
        [ "id" => 9, "nosa_code" => 9, "caption" => 'تحویل به پیمانکار', "entry_type_id" => 2,"has_warehouse_transaction"=>1],
        [ "id" => 10, "nosa_code" => 10, "caption" => 'تحویل امانی', "entry_type_id" => 2 ,"has_warehouse_transaction"=>1],
        [ "id" => 11, "nosa_code" => 11, "caption" => 'خروج متفرقه', "entry_type_id" => 2 ,"has_warehouse_transaction"=>1],
        [ "id" => 12, "nosa_code" => 12, "caption" => 'برگشت از خرید داخلی (دوره جاری)', "entry_type_id" => 2 ,"has_warehouse_transaction"=>1],
        [ "id" => 13, "nosa_code" => 13, "caption" => 'برگشت از خرید خارجی (دوره جاری)', "entry_type_id" => 2 ,"has_warehouse_transaction"=>1],
        //[ "id" => 1011,"nosa_code"=>-1, "caption" => 'خروج متفرقه(ویژه سامانه)',"entry_type_id"=> 2 ],
        [ "id" => 16, "nosa_code" => 16, "caption" => 'برگشت از ورود', "entry_type_id" => 2 ,"has_warehouse_transaction"=>1],
        [ "id" => 24, "nosa_code" => 24, "caption" => 'برگشت از خرید داخلی (دوره قبل)', "entry_type_id" => 2 ,"has_warehouse_transaction"=>1],
        [ "id" => 25, "nosa_code" => 25, "caption" => 'برگشت از خرید خارجی (دوره قبل)', "entry_type_id" => 2 ,"has_warehouse_transaction"=>1],

        [ "id" => 39, "nosa_code" => 39, "caption" => 'مازاد انبار گردانی', "entry_type_id" => 2,"has_warehouse_transaction"=>1 ],
        [ "id" => 201, "nosa_code" => -1, "caption" => 'خروج تغییر بسته بندی (سامانه)', "entry_type_id" => 2 ],
        [ "id" => 202, "nosa_code" => -1, "caption" => 'خروج تراکنش اصلاحی (سامانه)', "entry_type_id" => 2 ],
        [ "id" => 203, "nosa_code" => -1, "caption" => 'خروج ادغام بسته بندی (سامانه)', "entry_type_id" => 2 ],
        [ "id" => 204, "nosa_code" => -1, "caption" => 'خروج تغییر کالا به ضایعات (سامانه)', "entry_type_id" => 2 ],


    ];
    private $table = 'trans_kinds';

    public function run() {
        DB::table( $this->table )->delete();
        foreach ( $this->data as $item ) {
            DB::table( $this->table )->insert( $item );
        }


    }
}
