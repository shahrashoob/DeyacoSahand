<?php

namespace App\Http\Controllers\LineProductStation\Product;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\GoodsKind\GoodsKindSettingValue;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\BOM\BOMDegree;
use App\Models\LineProduct\Product\BOM\BOMReplace;
use App\Models\LineProduct\Product\ConsumedProduct\ConsumedProduct;
use App\Models\Utility\Option;
use Illuminate\Http\Request;

class ConsumedProductController extends Controller
{
    // line_product_station/product/consumed_product
    public $route_path = "line_product_station.product.consumed_product.";
    public $view_path = "line_product_station.product.consumed_product.";

    public function index(Product $product)
    {
        return self::GetIndex($product, $this->view_path, $this->route_path, null);
    }


    public function submit(Request $request, Product $product)
    {
        $result = self::PostSubmit($product, $request);

        Product\Version\ProductVersion::GetVersion($product, true, false);
        if ($result["result"]) {
            return back()->with(["success" => $result["message"]]);
        } else {
            return back()->withErrors($result["error"]);
        }

    }

    public function delete(ConsumedProduct $consumed_product, Product $product, $material_id)
    {
        $result = self::GetDelete($consumed_product, $product, $material_id);
        Product\Version\ProductVersion::GetVersion($product, true, false);
        if ($result["result"]) {
            return back()->with(["success" => $result["message"]]);
        } else {
            return back()->withErrors($result["error"]);
        }

    }

    public function replace(ConsumedProduct $consumed_product, Product $product)
    {
        return $result = self::GetReplace($consumed_product, $product, $this->view_path, $this->route_path, null);

    }

    public function store_replace(Request $request, ConsumedProduct $consumed_product, Product $product)
    {

        $result = self::PostSubmitReplace($request, $consumed_product);
        if (!$result["result"]) {
            return back()->with($result["error"]);
        }
        return redirect()->route($this->route_path . "index", $product)->with(["success" => "ماده اولیه جدید با موفقیت جایگزین ماده اولیه قبلی گردید."]);
    }

    public function change_choose_material(Product $product, $material_id = null)
    {
        $result = self::GetChangeChooseMaterial($product, $material_id);
        if ($result["result"]) {
            return back()->with(["success" => $result["message"]]);
        } else {
            return back()->withErrors($result["error"]);
        }
    }

    public static function GetReplace(ConsumedProduct $consumed_product, Product $product, $view_path, $route_path, $product_creation_process)
    {
        if ($consumed_product->product_id != $product->id) {
            return back()->withErrors("اطلاعات درخواستی نامعتیر است، لطفا یکبار دیگر تلاش کنید.");
        }

        $bom_item_replace = Product\BOM\BOMItem::where([
            "product_id" => $consumed_product->product_id,
            "material_id" => $consumed_product->material_id
        ])->get();

        $product_option = Option::get("product_all", 0, [$consumed_product->material->goods_kind_id]);
        return view($view_path . "replace", compact("product", "view_path", "route_path", "consumed_product", "product_creation_process", "product_option", "bom_item_replace"));

    }

    public static function PostSubmitReplace(Request $request, ConsumedProduct $consumedProduct)
    {
        $new_material = Product::find($request->new_material_id);
        if (!$new_material) {
            return [
                "result" => false,
                "error" => "کد کالا نادرست است، لطفا مجدد تلاش کنید."
            ];
        }
        if ($new_material->goods_kind_id != $consumedProduct->material->goods_kind_id) {
            return [
                "result" => false,
                "error" => "رسته کالایی نادرست است، لطفا مجدد تلاش کنید."
            ];
        }

        // لیست آیتم هایی که قابلیت جابجایی دارد
        $bom_item_replace = Product\BOM\BOMItem::where([
            "product_id" => $consumedProduct->product_id,
            "material_id" => $consumedProduct->material_id
        ])->get();


        // لیست آیتم هایی که می خواهد جابجا کند.
        $bom_item_selected = $request->bom_item;

        if ($consumedProduct->product->supply_type_id == 1) {
            if (!$bom_item_selected) {
                return [
                    "result" => false,
                    "error" => "لطفا حداقل یک ردیف BOM را انتخاب نمایید."
                ];
            }
            $bom_item_selected = array_keys($bom_item_selected);
        }
        // BOM
        Product\BOM\BOMItem::where([
            "product_id" => $consumedProduct->product_id,
            "material_id" => $consumedProduct->material_id
        ])->
        when($bom_item_selected && count($bom_item_selected) > 0, function ($query) use ($bom_item_selected) {
            return $query->whereIn("id", $bom_item_selected);
        })->
        update([
            "material_id" => $new_material->id
        ]);
        Product\BOM\BOMItem::where([
            "product_id" => $consumedProduct->product_id,
            "dependent_on_material_id" => $consumedProduct->material_id
        ])->
        update([
            "dependent_on_material_id" => $new_material->id
        ]);

        BOMDegree::
        where([
            "product_id" => $consumedProduct->product_id,
            "material_id" => $consumedProduct->material_id
        ])->
        when($bom_item_selected && count($bom_item_selected) > 0, function ($query) use ($bom_item_selected) {
            return $query->whereIn("bill_of_material_item_id", $bom_item_selected);
        })->
        update([
            "material_id" => $new_material->id
        ]);

        Product\BOM\BOMFaultIllegal:: where([
            "product_id" => $consumedProduct->product_id,
            "material_id" => $consumedProduct->material_id
        ])->
        update([
            "material_id" => $new_material->id
        ]);

        $list = BOMReplace::
        where([
            "product_id" => $consumedProduct->product_id,
            "material_id" => $consumedProduct->material_id
        ])->
        delete();

        foreach ($consumedProduct->product->bom as $bom) {
            // بازسازی کالاهای جایگزین برای کالا
            Product\BOM\BOMPermutation::CreateBOMMood($bom);
        }

        // اگر کل آیتم ها را جابجا می کند، باید ماده اولیه را هم جابجا کنیم، در غیر این صورت نباید ماده اولیه را جابجا کنیم.
        if (!$bom_item_selected || count($bom_item_selected) == count($bom_item_replace)) {
            // کالای مصرفی
            $consumedProduct->material_id = $new_material->id;
            $consumedProduct->save();
        }

        return [
            "result" => true
        ];

    }

    public static function GetIndex(Product $product, $view_path, $route_path, $product_creation_process, $packing_type_list = [])
    {

        $list_default = GoodsKindSettingValue::getArrayValue($product->goods_kind_id, "default_consumed_goods_kinds");
        $product_option = Option::get("product_all", 0, $list_default);

        // آیا کالا به صورت کارمزدی به فروش می رسد.
        $product_is_wage_work = Product\TypeOfSaleProduct\TypeOfSaleProductProduct::where([
            "product_id" => $product->id,
            "type_of_sale_of_product_id" => 2 // کارمزدی
        ])->first();

        if (!$product_is_wage_work) {
            // اگر یکی از کالاهای سطح بالای آن هم کارمزدی بود نمایش داده شود.
            $parent_product = ConsumedProduct::where("material_id", $product->id)->pluck("product_id");
            $parent_product[] = -1;
            $product_is_wage_work=Product\TypeOfSaleProduct\TypeOfSaleProductProduct::
            whereIn("product_id", $parent_product)->
            where([
                "type_of_sale_of_product_id" => 2 // کارمزدی
            ])->first();
        }

        return view($view_path . "index", compact("product", "product_option", "product_is_wage_work", "product_creation_process", "view_path", "route_path", "packing_type_list"));

    }

    public static function PostSubmit(Product $product, Request $request)
    {

        $material = Product::find($request->material_id);
        if (!$material) {
            return [
                "result" => false,
                "error" => "کالای مصرفی به درستی انتخاب نشده است."
            ];

        }

        if (
            ConsumedProduct::where([
                "product_id" => $product->id,
                "material_id" => $material->id
            ])->exists()) {
            return [
                "result" => false,
                "error" => "این کالای مصرفی قبلا تعریف شده است."
            ];

        }

        ConsumedProduct::create([
            "product_id" => $product->id,
            "material_id" => $request->material_id
        ]);

        return [
            "result" => true,
            "message" => "یک کالای مصرفی با موفقیت اضافه گردید"
        ];
    }

    public static function GetDelete(ConsumedProduct $consumed_product, Product $product, $material_id)
    {

        // کالاهایی که در دست تعریف هستند، شناسه مواد اولیه ندارند و می توانیم مستقیم حذف کنیم.
        if ($material_id == -1 && $consumed_product->status_id == 3400002) {
            $consumed_product->delete();
            return [
                "result" => true,
                "message" => "حذف با موفقیت انجام شد." . "<br/>" . "با توجه به اینکه کالای حذف شده در دست طراحی می باشد، در صورت عدم نیاز، درخواست طراحی آن را نیز حذف نمایید."
            ];
        }
        if ($consumed_product->product_id != $product->id || $consumed_product->material_id != $material_id) {
            return [
                "result" => false,
                "error" => "کد محصول نا معتبر است."
            ];
        }

        $bom_item = Product\BOM\BOMItem::where([
            "product_id" => $product->id,
            "material_id" => $material_id
        ])->first();

        if ($bom_item) {
            return [
                "result" => false,
                "error" => "از آنجایی که این کالای مصرفی در " . $bom_item->bom->caption . " استفاده شده است،امکان حذف آن وجود ندارد."
            ];

        }

        ConsumedProduct::where([
            "product_id" => $product->id,
            "material_id" => $material_id
        ])->delete();

        return [
            "result" => true,
            "message" => "حذف با موفقیت انجام شد."
        ];

    }

    public static function GetChangeChooseMaterial(Product $product, $material_id)
    {

        // در زمان سفارش گذاری،
        //امکان انتخاب روش ارسال کالای مصرفی به مشتری داده شود؟

        if (!$material_id) {
            return [
                "result" => false,
                "error" => "کالای مصرفی به درستی انتخاب نشده است."
            ];
        }

        $consumed_product = ConsumedProduct::where([
            "product_id" => $product->id,
            "material_id" => $material_id
        ])->first();

        if ($consumed_product) {
            $consumed_product->in_ordering_customer_can_choose = !$consumed_product->in_ordering_customer_can_choose;
            $consumed_product->save();

            return [
                "result" => true,
                "message" => "تغییرات برای کالای مصرفی ذخیره گردید."
            ];

        }
        return [
            "result" => false,
            "error" => "کالای مصرفی به درستی انتخاب نشده است."
        ];
    }
}
