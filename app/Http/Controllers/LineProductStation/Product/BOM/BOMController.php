<?php

namespace App\Http\Controllers\LineProductStation\Product\BOM;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\BOM;
use App\Models\LineProduct\Product;
use App\Models\Utility\Option;
use Illuminate\Http\Request;

class BOMController extends Controller
{
    var $view_path = "line_product_station.product.bom.bom.";
    var $route_path = "line_product_station.product.bom.";

    public function index(Product $product,$show_route_code="01")
    {

        return self::GetIndex($product, $this->view_path, $this->route_path, null,$show_route_code);
    }

    public static function GetIndex(Product $product, $view_path, $route_path, $product_creation_process,$show_route_code)
    {
        return view($view_path . "index", compact("product", "product_creation_process", "view_path", "route_path","show_route_code"));
    }

    public function create(Request $request, Product\ProductRoute $product_route)
    {
        return self::GetCreate($request, $product_route, null);
    }

    public static function GetCreate(Request $request, Product\ProductRoute $product_route, $product_creation_process)
    {

        if (Product\BOM\BOM::where(["product_id" => $product_route->product_id, "product_route_id" => $product_route->id])->count() > 0) {
            return back()->withErrors("امکان تعریف بیش از یک BOM برای مسیرها فعال نمی باشد.");
        }
        $request["product_id"] = $product_route->product->id;
        $request["product_route_id"] = $product_route->id;
        $request["active_status_id"] = 1200;
        $request["caption"] = "BOM" . ($product_route->product->bom->count() + 1);


        $bom = Product\BOM\BOM::create($request->all());


        return redirect()->route("line_product_station.product.bom_item.create", [$bom, $product_creation_process])->with(["success" => "یک BOM با موفقیت اضافه گردید."]);

    }

    public function edit(Product\BOM\BOM $bom)
    {
        return self::GetEdit($bom, $this->view_path, $this->route_path, null);
    }

    public static function GetEdit(Product\BOM\BOM $bom, $view_path, $route_path, $product_creation_process)
    {
        $status_option = Option::get("status", $bom->active_status_id, 1100);

        return view($view_path . "edit", compact("status_option", "bom", "product_creation_process", "view_path", "route_path"));
    }


    public function update(Request $request, Product\BOM\BOM $bom)
    {
        return self::PostUpdate($request, $bom, $this->route_path, null);
    }

    public static function PostUpdate(Request $request, Product\BOM\BOM $bom, $route_path, $product_creation_process)
    {

        $exist_item = Product\BOM\BOM::exist($bom->product->id, $request->caption, $bom->id);
        if ($exist_item) {
            return back()->withErrors("این مسیر قبلا تعریف شده است.");
        }

        $bom->update($request->all());

        if ($product_creation_process) {
            return redirect()->route($route_path . "index", [$product_creation_process,$bom->product_route->code])->with(["success" => "اطلاعات با موفقیت ذخیره گردید."]);
        } else {
            return redirect()->route($route_path . "index", [$bom->product,$bom->product_route->code])->with(["success" => "اطلاعات با موفقیت ذخیره گردید."]);

        }

    }


    public function destroy(Product $product, Product\BOM\BOM $bom, $delete_all = 0)
    {
        $route_url = route($this->route_path . "destroy", [$product, $bom]);
        return self::GetDestroy($product, $bom, $delete_all, $route_url);

    }

    public static function GetDestroy(Product $product, Product\BOM\BOM $bom, $delete_all, $route_url)
    {

        if ($product->id != $bom->product_id) {
            return back()->withErrors("درخواست نا معتبر");
        }

        if ($bom->items()->count() > 0 && $delete_all == 0) {
            return back()->withErrors("این گروه BOM دارای یک یا چند ردیف می باشد و امکان حذف آن وجود ندارد" .
                "<br/> <a href='$route_url/1'> <i class='fa fa-trash text-danger'></i> BOM را همراه با کل ردیف ها حذف کنید </a>"
            );
        }
        foreach ($bom->items as $item) {
            $item->delete();
        }

        $bom->delete();

        return back()->with(["success" => "حذف با موفقیت انجام شد."]);
    }
}
