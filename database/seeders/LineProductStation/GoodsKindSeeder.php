<?php

namespace Database\Seeders\LineProductStation;

use App\Models\LineProduct\GoodsKind;
use App\Models\LineProduct\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GoodsKindSeeder extends Seeder
{
    private $data = [

        [
            "id" => 1,
            "code" => "01",
            "caption"=>"الیاف",
            "caption_en" => "Fibers",
        ],
        [
            "id" => 2,
            "code" => "02",
            "caption"=>"نخ",
            "caption_en" => "Yarn",
        ],
        [
            "id" => 3,
            "code" => "03",
            "caption"=>"چله",
            "caption_en" => "Warps",
        ],
        [
            "id" => 4,
            "code" => "04",
            "caption"=>"پارچه خام",
            "caption_en" => "Fabric_Raw",
        ],
        [
            "id" => 5,
            "code" => "05",
            "caption"=>"پارچه تکمیل شده",
            "caption_en" => "Fabric",
        ],
        [
            "id" => 6,
            "code" => "06",
            "caption"=>"قطعات",
            "caption_en" => "Parts",
        ],
        [
            "id" => 7,
            "code" => "07",
            "caption"=>"مواد اولیه",
            "caption_en" => "RawMaterial",
        ],
        [
            "id" => 8,
            "code" => "08",
            "caption_en" => "Waste",
            "caption" => "ضایعات نساجی",
        ],
        [
            "id" => 9,
            "code" => "09",
            "caption_en" => "AcidAndBase",
            "caption" => "اسیدها و بازها",
            "production_algorithm_type_id" => 1
        ],
        [
            "id" => 10,
            "code" => "10",
            "caption_en" => "TextileDyestuffs",
            "caption" => "رنگهای نساجی",
            "production_algorithm_type_id" => 1
        ],
        [
            "id" => 11,
            "code" => "11",
            "caption_en" => "TextileAuxiliaryMaterials",
            "caption" => "مواد کمکی نساجی",
            "production_algorithm_type_id" => 1
        ],
        [
            "id" => 12,
            "code" => "12",
            "caption_en" => "ElectrostaticPowderCoeting",
            "caption" => "رنگ های پودری الکترواستاتیک",
            "production_algorithm_type_id" => 1
        ],
        [
            "id" => 13,
            "code" => "13",
            "caption_en" => "LiquidPpaints",
            "caption" => "رنگ های مایع",
            "production_algorithm_type_id" => 1
        ],
        [
            "id" => 14,
            "code" => "14",
            "caption_en" => "SprayPaints",
            "caption" => "رنگ های اسپری",
            "production_algorithm_type_id" => 1
        ],
        [
            "id" => 15,
            "code" => "15",
            "caption_en" => "Thermoplastic Polymers",
            "caption" => "پلیمرهای ترموپلاستیک",
            "production_algorithm_type_id" => 1
        ],
        [
            "id" => 16,
            "code" => "16",
            "caption_en" => "Thermoset Polymers",
            "caption" => "پلیمرهای ترموست",
            "production_algorithm_type_id" => 1
        ],
        [
            "id" => 17,
            "code" => "17",
            "caption_en" => "Natural Polymers",
            "caption" => "پلیمرهای طبیعی",
            "production_algorithm_type_id" => 1
        ],
        [
            "id" => 18,
            "code" => "18",
            "caption_en" => "Sewn goods",
            "caption" => "کالاهای دوخته شده",
            "production_algorithm_type_id" => 1
        ],
        [
            "id" => 19,
            "code" => "19",
            "caption_en" => "رزین های اپوکسی ( پیش سازهای پلیمری واکنش پذیر )",
            "caption" => "Epoxy resins (reactive polymer precursors)",
            "production_algorithm_type_id" => 1
        ],
        [
            "id" => 20,
            "code" => "20",
            "caption_en" => "عوامل پخت (اتصال عرضی)",
            "caption" => "Curing agents (crosslinking)",
            "production_algorithm_type_id" => 1
        ],
        [
            "id" => 21,
            "code" => "21",
            "caption_en" => "کربنات",
            "caption" => "Carbonate",
            "production_algorithm_type_id" => 1
        ],
        [
            "id" => 22,
            "code" => "22",
            "caption_en" => "مواد معدنی پرکننده",
            "caption" => "Mineral fillers",
            "production_algorithm_type_id" => 1
        ],
        [
            "id" => 23,
            "code" => "23",
            "caption_en" => "رنگدانه های عمومی",
            "caption" => "General pigments",
            "production_algorithm_type_id" => 1
        ],
        [
            "id" => 24,
            "code" => "24",
            "caption_en" => "افزودنی های پلیمری",
            "caption" => "Polymer additives",
            "production_algorithm_type_id" => 1
        ],


    ];
    private $table = 'goods_kinds';

    public function run()
    {

        foreach ($this->data as $item) {

            if (!DB::table($this->table)->where("id", $item["id"])->first()) {
                DB::table($this->table)->insert($item);
            } else {
                unset($item["caption_en"]);
                unset($item["production_algorithm_type_id"]);
                DB::table($this->table)->where("id", $item["id"])->update($item);
            }

        }
    }

    public static function getStatusIdFromProduct(GoodsKind $goods_kind, $supply_type_id, $type)
    {
        switch ($type) {
            // در زمان ایجاد کارت تولید، با توجه به رسته کالایی، مشخص می شود که اولین وضعیت کارت تولید باید چه چیزی باشد.
            case "first_status_for_product_creation":
                if ($supply_type_id == 3) {
                    return 7008001; // تخصیص پیمانکار
                } elseif ($supply_type_id == 4) {
                    return 7011001; // تخصیص مشتری
                } elseif ($supply_type_id == 2) {
                    return 7012001; // تخصیص تامین کننده
                }
                switch ($goods_kind->caption_en) {
                    case "Fabric_Raw":
                        return 7001001;
                        break;
                    case "Warps":
                        return 7201001;
                        break;
                    case "Fabric":
                        return 7301001;
                        break;
                    case "Yarn":
                        return null;
                        break;
                }
                break;
            // اولین وضعیت در زمان ایجاد فرم تولید برای پیمانکاران
            case "first_status_for_production_form":
                switch ($goods_kind->caption_en) {
                    case "Fabric_Raw":
                        return 7002001; // در حال بافت پارچه
                        break;
                    case "Warps":
                        return 7202002; // در حال چله کشی
                        break;
                    case "Fabric":
                        return 7302001; // در حال تولید
                        break;
                }
                break;
            // وضعیتی که در آن کارت های رسته کالایی خاتمه یافته شده اند.
            case "termination_status_of_production_card":
                switch ($goods_kind->caption_en) {
                    case "Fabric_Raw":
                        return 7001004; // خاتمه یافته پارچه
                        break;
                    case "Warps":
                        return 7201004; // خاتمه یافته چله کشی
                        break;
                    case "Fabric":
                        return 7301004; // خاتمه یافته در پارچه تکمیل شده
                        break;
                }
                break;

        }
        1 / 0;
    }
}
