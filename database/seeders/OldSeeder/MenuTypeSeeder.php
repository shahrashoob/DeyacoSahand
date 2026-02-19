<?php

namespace Database\Seeders\OldSeeder;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuTypeSeeder extends Seeder
{
    private $data = [
        //
        [ "id" => 1, "caption" => ' صفحه اصلی ' ,"icon"=>"fa fa-home","has_submenu"=>0],
        [ "id" => 100, "caption" => ' تولید ' ,"icon"=>"fa fa-crosshairs","has_submenu"=>1],
        [ "id" => 110, "caption" => ' چله کشی ' ,"icon"=>"fas fa-industry","has_submenu"=>1],
        [ "id" => 115, "caption" => ' ریسندگی  ' ,"icon"=>"fas fa-industry","has_submenu"=>1],
        [ "id" => 120, "caption" => ' تولید  ' ,"icon"=>"fas fa-industry","has_submenu"=>1],
        [ "id" => 125, "caption" => ' تکمیل  ' ,"icon"=>"fas fa-industry","has_submenu"=>1],
        [ "id" => 127, "caption" => ' بسته بندی  ' ,"icon"=>"fas fa-industry","has_submenu"=>1],
        [ "id" => 200, "caption" => ' انبار ' ,"icon"=>"fa fa-warehouse","has_submenu"=>1],
        [ "id" => 210, "caption" => ' نگهداری و تعمیرات ' ,"icon"=>"fa fa-wrench","has_submenu"=>1],
        [ "id" => 300, "caption" => ' بازرگانی ' ,"icon"=>"fa fa-shopping-cart","has_submenu"=>1],
        [ "id" => 310, "caption" => ' فروش ' ,"icon"=>"fa fa-crosshairs","has_submenu"=>1],
        [ "id" => 1100, "caption" => ' مشتریان ' ,"icon"=>"fa fa-crosshairs","has_submenu"=>1],
        [ "id" => 1110, "caption" => ' مدیریت مشتریان ' ,"icon"=>"fa fa-crosshairs","has_submenu"=>1],
        [ "id" => 320, "caption" => ' منابع انسانی ' ,"icon"=>"fa fa-users","has_submenu"=>1,"color"=>""],
        [ "id" => 330, "caption" => ' روابط عمومی ' ,"icon"=>"fa fa-phone","has_submenu"=>1,"color"=>""],
        [ "id" => 400, "caption" => ' برنامه ریزی ' ,"icon"=>"fa fa-star","has_submenu"=>1],
        [ "id" => 410, "caption" => 'کنترل کیفیت ' ,"icon"=>"fa fa-flask","has_submenu"=>1,"color"=>""],
        [ "id" => 420, "caption" => ' پایانه بار ' ,"icon"=>"fa fa-car","has_submenu"=>1,"color"=>"text-danger"],
        [ "id" => 430, "caption" => ' تامین کنندگان ' ,"icon"=>"fa fa-asterisk","has_submenu"=>1,"color"=>""],
        [ "id" => 500, "caption" => ' مدیریت ' ,"icon"=>"fa fa-user-circle","has_submenu"=>1],
        [ "id" => 900, "caption" => 'اطلاعات پایه' ,"icon"=>"fa fa-info","has_submenu"=>1],
        [ "id" => 910, "caption" => 'بارگذاری اطلاعات پایه' ,"icon"=>"fa fa-upload","has_submenu"=>1],
        [ "id" => 1000, "caption" => ' گزارش ها ' ,"icon"=>"fa fa-list","has_submenu"=>1],
        [ "id" => 550, "caption" => ' تنظیمات ' ,"icon"=>"fa fa-cog","has_submenu"=>1],
        [ "id" => 1200, "caption" => ' ماشین آلات ' ,"icon"=>"fa fa-cogs","has_submenu"=>1],
        [ "id" => 1300, "caption" => ' پشتیبانی ' ,"icon"=>"fa fa-question-circle","has_submenu"=>1],
        [ "id" => 1400, "caption" => 'پیمانکاران' ,"icon"=>"fa fa-asterisk","has_submenu"=>1],
        [ "id" => 1500, "caption" => 'نگهبانی' ,"icon"=>"fa  fa-user-shield","has_submenu"=>1],
        [ "id" => 1600, "caption" => 'میز کار' ,"icon"=>"fa fa-money-check","has_submenu"=>1,"priority_number"=>1000],
        [ "id" => 1700, "caption" => 'داشبورد هوشمند' ,"icon"=>"fa fa-user-astronaut","has_submenu"=>1,"priority_number"=>800],
        [ "id" => 1800, "caption" => 'هوش تجاری (BI)' ,"icon"=>"fa fa-user-astronaut","has_submenu"=>1,"priority_number"=>900],
        [ "id" => 1900, "caption" => 'مجوزها' ,"icon"=>"fa fa-unlock-alt","has_submenu"=>1,"priority_number"=>950],
        [ "id" => 2000, "caption" => 'طراحی کالا' ,"icon"=>"fa fa-american-sign-language-interpreting","has_submenu"=>1,"priority_number"=>500],
        [ "id" => 2100, "caption" => 'همکاری با ما ' ,"icon"=>"fa fas fa-handshake","has_submenu"=>1,"priority_number"=>600],
        [ "id" => 3100, "caption" => 'گفتگو' ,"icon"=>"fa fas fa-comment","has_submenu"=>1,"priority_number"=>1100],

        [ "id" => 3200, "caption" => 'اعتبار حساب' ,"icon"=>"fa fa-money-bill-alt","has_submenu"=>1,"priority_number"=>400],
        [ "id" => 3300, "caption" => 'فروشگاه' ,"icon"=>"fa fa-solid fa-store","has_submenu"=>1,"priority_number"=>500],
        [ "id" => 3400, "caption" => 'بروزرسانی' ,"icon"=>"fa feather icon-trending-up","has_submenu"=>1,"priority_number"=>-1],
        [ "id" => 3500, "caption" => 'خدمات رفاهی' ,"icon"=>"fa feather icon-trending-up","has_submenu"=>1,"priority_number"=>600],
    ];
    private $table = 'menu_types';

    public function run() {
        DB::table( $this->table )->delete();
        foreach ( $this->data as $item ) {
            DB::table( $this->table )->insert( $item );
        }
    }
}
