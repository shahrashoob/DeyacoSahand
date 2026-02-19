<?php

namespace App\Http\Controllers\LineProductStation;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\GoodsKind;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\ProductType;
use App\Models\Utility\Option;
use Illuminate\Http\Request;

class ProductTypeController extends Controller
{
    private $view_path="line_product_station.product_type.";
    private $route_path="line_product_station.product_type.";
    public function index(GoodsKind $goods_kind){

        return view($this->view_path."index", compact("goods_kind"));
    }
    public function create(GoodsKind $goods_kind){
        $product_type=new ProductType();
        $goods_kind_option=Option::get("goods_kind");
        return view($this->view_path."create",compact("product_type","goods_kind_option","goods_kind"));
    }
    public function store(Request $request,GoodsKind $goods_kind){
        if($request->caption=="" || ProductType::ExistsCode($request->caption,$goods_kind)){
            return back()->withErrors("عنوان تکراری است");
        }
        $request["goods_kind_id"]=$goods_kind->id;
        $product_type = ProductType::create($request->all());
        return redirect()->route($this->route_path."index",$goods_kind)->with(["success" => "زیررسته با موفقیت اضافه شد"]);

    }
    public function edit(ProductType $product_type){

        $goods_kind_option=Option::get("goods_kind");
        return view($this->view_path."edit",compact("product_type","goods_kind_option"));

    }
    public function update(Request $request,ProductType $product_type){
        if($request->caption=="" || ProductType::ExistsCode($request->caption,$product_type->goods_kind)){
            return back()->withErrors("عنوان تکراری است");
        }
        $product_type->update($request->all());
        return redirect()->route($this->route_path."index",$product_type->goods_kind_id)->with(["success" => "اطلاعات با موفقیت ذخیره شد"]);

    }
    public function destroy(ProductType $product_type){
        if(
            Product::where("product_type_id",$product_type->id)->exists()
        ){
            return back()->withErrors("به دلیل استفاده شدن در رکوردهای دیگر، امکان حذف وجود ندارد");
        }
        $goods_kind_id=$product_type->goods_kind_id;
        $product_type->delete();
        return redirect()->route($this->route_path."index",$goods_kind_id)->with(["success" => "یک آیتم با موفقیت حذف گردید"]);

    }
}
