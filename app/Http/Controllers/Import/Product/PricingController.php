<?php

namespace App\Http\Controllers\Import\Product;

use App\Exports\Product\ProductPricingSampleFormatExport;
use App\Http\Controllers\Controller;
use App\Imports\Product\ProductPricingImport;
use App\Models\LineProduct\GoodsKind;
use App\Models\LineProduct\Import\ImportProductPricing;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\ProductPackingType;
use App\Models\Utility\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class PricingController extends Controller
{
    var $route_path = "import.product.pricing.";
    var $view_path = "import.product.pricing.";

    public function index()
    {

        $model = ["name" => "product_pricing", "route" => "import.product.pricing.upload", "caption" => " قیمت بروز مواد اولیه "];
        return view("import/index", compact("model"));

    }

    public function upload()
    {

        Excel::import(new ProductPricingImport(), request()->file('file_uploaded'));

        return redirect()->route($this->route_path . "show")->with(["success" => "آپلود با موفقیت انجام شده، در صورت تایید لیست قیمت کالاها بروز رسانی می شود."]);

    }

    public function show()
    {

        $list = ImportProductPricing::orderBy("error", "desc")->paginate(50);

        $error_count = ImportProductPricing::where("error", "!=", "")->count();

        return view("import/product/pricing", compact("list", "error_count"));

    }

    public function update()
    {

        $list = ImportProductPricing::where("error", "")->select("product_id", "packing_type_id", "price")->get()->toArray();
        $product_ids=[];
        foreach ($list as $item) {
            $product_ids[] = $item['product_id'];
        }
        $product_ids[]=-1;
        Product\Pricing\ProductPricing::whereIn("product_id", $product_ids)->delete();
        Product\Pricing\ProductPricing::insert($list);

        // اضافه کردن لاگ به جداول
        $log = Product\Pricing\ProductPricingLog::create(["user_id" => Auth::id()]);
        $list_product_log = [];
        foreach ($list as $item) {
            $item["product_pricing_log_id"] = $log->id;
            $list_product_log [] = $item;
        }

        Product\Pricing\ProductPricingProductLog::insert($list_product_log);

        return redirect()->route($this->route_path . "index")->with(["success" => "لیست قیمت با موفقیت بروزرسانی گردید."]);
    }

    public function get_sampling_product_pricing()
    {

        $goods_kind_option = Option::get("goods_kind");
        return view($this->view_path . "get_sampling_product_pricing", compact("goods_kind_option"));


    }

    public function submit_sampling_product_pricing(Request $request)
    {

        $goods_kind            = GoodsKind::find( $request->goods_kind_id );
        $export                = new ProductPricingSampleFormatExport();
        $export->goods_kind_id = $request->goods_kind_id;

        return Excel::download( $export, 'product_pricing.' . $goods_kind->caption_en . '.xlsx' );

    }
}

