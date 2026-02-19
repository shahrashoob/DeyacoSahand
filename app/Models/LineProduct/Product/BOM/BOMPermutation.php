<?php

namespace App\Models\LineProduct\Product\BOM;

use App\Models\LineProduct\Degree;
use App\Models\LineProduct\Product;
use App\Models\Utility\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BOMPermutation extends Model
{
    use HasFactory;

    protected $table = "bill_of_material_permutation";
    protected $fillable = [
        "bill_of_material_id",
        "product_id",
        "weight",
        "active_status_id",
        "code"
    ];


    public function bom()
    {
        return $this->belongsTo(BOM::class);
    }

    public function active_status()
    {
        return $this->belongsTo(Status::class, "active_status_id");
    }

    public function items()
    {
        return $this->hasMany(BOMPermutationItem::class, "bill_of_material_permutation_id")->orderBy("material_id");
    }

    public function getTitleFroView()
    {
        $title = "";
        foreach ($this->items as $item) {
            $title .= $item->material->fullCaption() . "\n";
        }

        return $title;
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function CreateMood()
    {

    }

    /**
     * @param \App\Models\LineProduct\Product\BOM\BOM $bom
     *
     * @return bool[]|ایجاد حالت هایی که یک کالا می تواند تولید شود.
     */
    public static function CreateBOMMood(BOM $bom)
    {
        //Enabled addition item
        BOMPermutation::where([
            "bill_of_material_id" => $bom->id,
            "product_id" => $bom->product_id,
        ])->
        update(["active_status_id" => 1210]);

        if ($bom->replaces()->count() == 0) {
            return ["result" => true];
        }
        $list = [];
        $material_list = [];

        foreach ($bom->items()->orderBy("material_id")->get() as $bom_item) {
            $bom_list = [];
            $bom_list[] = [
                "material_id" => $bom_item->material_id,
                "product_id" => $bom_item->material_id,
                "bill_of_material_item_id" => $bom_item->id
            ];
            $material_list[] = ["product_id" => $bom_item->material_id];

            foreach ($bom_item->replaces()->orderBy("replace_product_id")->get() as $replace) {
                $bom_list[] = [
                    "material_id" => $bom_item->material_id,
                    "product_id" => $replace->replace_product_id,
                    "bill_of_material_item_id" => $bom_item->id,
                    "bill_of_material_replace_id" => $replace->id
                ];
            }
            $list[] = $bom_list;
        }


        $permutation_list = self::Permutation($list);
        $valid_permutation_list = [];
        // بررسی اینکه چک کنیم کالا درست تولید می شود
        // اگر یک ردیف کالای تکراری در BOM داشته باشیم و جایگزین های آنها با هم فرق داشته باشد، جزء کالاهای مجاز نیست و باید از جایگزین ها حذف شود.
        $remove_permutation_list = [];
        $k = 0;
        foreach ($permutation_list as $permutation) {
            $result = 0;
            $permutation_i = $permutation;
            $permutation_j = $permutation;
            foreach ($permutation_i as $item_material) {
                $material_id = $item_material["material_id"];
                $product_id = $item_material["product_id"];
                $bill_of_material_item_id = $item_material["bill_of_material_item_id"];

                foreach ($permutation_j as $item_product) {
                    if (
                        $item_product["material_id"] == $material_id &&
                        $product_id != $item_product["product_id"] &&
                        $bill_of_material_item_id != $item_product["bill_of_material_item_id"]

                    ) {
                        $result += 1;
                    }
                }

            }
            $k++;
            if (!$result) {
                $valid_permutation_list[$k] = $permutation;
            }
        }


        //Enabled addition item
        BOMPermutation::where([
            "bill_of_material_id" => $bom->id,
            "product_id" => $bom->product_id,
        ])->
        update(["active_status_id" => 1210]);


        $permutation_list_before_added = [];

        // جایگشت کالای اصلی
        $permutation_original_product = self::getCodeFromList($bom->product, $material_list);


        foreach ($valid_permutation_list as $permutation) {


            $permutation_code = self::getCodeFromList($bom->product, $permutation);
            if ($permutation_code == $permutation_original_product) {
                continue;
            }

            $bom_permutation_old_list = BOMPermutation::where([
                "bill_of_material_id" => $bom->id,
                "product_id" => $bom->product_id,
            ])->where("code", $permutation_code)->
            first();

            if ($bom_permutation_old_list) {
                // چون از قبل وجود داشته است، فقط شناسه آیتم های جایگشت را حذف و مجدد اضافه می کنیم.
                // چون ممکن است، یک ردیف BOM حذف و دوباره اضافه شود، در این حالت جایگشت جدید ایجاد نمی شود ولی شناسه آیتم های BOM فرق می کند
                $permutation_list_before_added[$bom_permutation_old_list->id] = $bom_permutation_old_list->id;

                BOMPermutationItem::where([
                    "bill_of_material_permutation_id" => $bom_permutation_old_list->id,
                ])->delete();


                foreach ($permutation as $permutation_item) {
                    BOMPermutationItem::create([
                        "bill_of_material_permutation_id" => $bom_permutation_old_list->id,
                        "bill_of_material_id" => $bom->id,
                        "product_id" => $bom->product_id,
                        "material_id" => $permutation_item["product_id"],
                        "bill_of_material_item_id" => $permutation_item["bill_of_material_item_id"],
                        "bill_of_material_replace_id" => isset($permutation_item["bill_of_material_replace_id"]) ?
                            $permutation_item["bill_of_material_replace_id"] : null,
                    ]);

                }
                continue;
            }

            $bom_permutation = BOMPermutation::create([
                "bill_of_material_id" => $bom->id,
                "product_id" => $bom->product_id,
                "code" => $permutation_code
            ]);

            foreach ($permutation as $permutation_item) {
                BOMPermutationItem::create([
                    "bill_of_material_permutation_id" => $bom_permutation->id,
                    "bill_of_material_id" => $bom->id,
                    "product_id" => $bom->product_id,
                    "material_id" => $permutation_item["product_id"],
                    "bill_of_material_item_id" => $permutation_item["bill_of_material_item_id"],
                    "bill_of_material_replace_id" => isset($permutation_item["bill_of_material_replace_id"]) ?
                        $permutation_item["bill_of_material_replace_id"] : null,
                ]);

            }

            $bom_permutation->getCode();

        }


        BOMPermutation::where([
            "bill_of_material_id" => $bom->id,
            "product_id" => $bom->product_id,
        ])->
        whereIn("id", $permutation_list_before_added)->
        update(["active_status_id" => 1200]);

        Product\Version\ProductVersion::GetVersion($bom->product,true,false);
    }

    public static function Permutation($VList)
    {
// همه حالت های جایگزینی تولید را محاسبه می کنیم.
        if (count($VList) <= 1) {
            $set = [];
            foreach ($VList[0] as $V) {
                $set[] = [$V];
            }

            return $set;
        }

        $V1 = $VList[0];
        unset($VList[0]);

        $V_1List = [];
        foreach ($VList as $V) {
            $V_1List[] = $V;
        }

        $set_k_1 = self::Permutation($V_1List);

        foreach ($V1 as $v_i) {

            foreach ($set_k_1 as $set_k_1_item) {
                $new_set = [];
                $new_set[] = $v_i;
                foreach ($set_k_1_item as $set_k_1_item_value) {
                    $new_set[] = $set_k_1_item_value;
                }
                $set[] = $new_set;

            }

        }

        return $set;

    }

    public static function BeforeAddedId($permutation, $list, Product $product)
    {

        $permutation_code = self::getCodeFromList($product, $permutation);
        echo $permutation_code . "<br/>=>";
        foreach ($list as $key => $item) {
            //  echo $item->getCode() . "==" . $permutation_code . "=>".( $item->getCode() == $permutation_code?"OK":"NO")."<br/>";
            if ($item->getCode() == $permutation_code) {
                return $item->id;
            }
        }

        return -1;
    }

    public function getCode()
    {
        if ($this->code != "") {
            return $this->code;
        }
        $code = "***";

        return $code;
    }

    public function getSystemCode()
    {
        return "SP " . $this->id;
    }

    public static function getCodeFromList(Product $product, $list)
    {
        $code = "P" . $product->id;
        foreach ($list as $item) {
            $code .= "_M" . $item["product_id"];
        }

        return $code;
    }

    public static function GetSPCodeFromMaterialId(BOM $bom, $material_ids)
    {
        sort($material_ids);

        $permutation_original_product = self::getCodeFromList($bom->product, []);
        $code = "P" . $bom->product_id;
        foreach ($material_ids as $item) {
            $code .= "_M" . $item;
        }

        $bill_of_material_permutation_list = BOMPermutationItem::where("bill_of_material_id", $bom->id)->
        whereIn("material_id", $material_ids)->
        groupBy("bill_of_material_permutation_id")->
        get();
        foreach ($bill_of_material_permutation_list as $bill_of_material_permutation_item) {
            $bom_permutation_item_list = BOMPermutationItem::where("bill_of_material_id", $bom->id)->
            where("bill_of_material_permutation_id", $bill_of_material_permutation_item->bill_of_material_permutation_id)->
            orderBy("material_id")->
            get();

            $permutation_original_product = self::getCodeFromList($bom->product, []);
            $code2 = "P" . $bom->product_id;
            foreach ($bom_permutation_item_list as $item) {
                $code2 .= "_M" . $item->material_id;
            }

            if ($code == $code2) {
                return [
                    "result" => true,
                    "bill_of_material_permutation" => $bom_permutation_item_list[0]->bill_of_material_permutation
                ];
            }
        }
        return [
            "result"=>false,
            "error"=>"با توجه به اطلاعات ارایه شده SP در سیستم تعریف نشده است."
        ];
    }
}
