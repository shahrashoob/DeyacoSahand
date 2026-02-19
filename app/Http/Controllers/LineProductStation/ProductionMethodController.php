<?php

namespace App\Http\Controllers\LineProductStation;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\GoodsKind;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\ProductType;
use App\Models\Production\ProductionMethod;
use App\Models\Production\ProductionMethods;
use App\Models\Utility\Option;
use Illuminate\Http\Request;

class ProductionMethodController extends Controller
{
    private $view_path = "line_product_station.production_method.";
    private $route_path = "line_product_station.production_method.";

    public function index()
    {

        $list = ProductionMethod::paginate();
        return view($this->view_path . "index", compact("list"));
    }

    public function create()
    {

        return view($this->view_path . "create");
    }

    public function store(Request $request)
    {

        if (!$request->description || strlen($request->description) < 10) {
            return back()->withErrors("لطفا متن روش تولید را حداقل 10 کاراکتر وارد نمایید.");
        }
        ProductionMethod::create($request->all());
        return redirect()->route($this->route_path . "index")->with(["success" => " یک روش تولید با موفقیت اضافه شد"]);

    }

    public function edit(ProductionMethod $production_method)
    {

        return view($this->view_path . "edit", compact("production_method"));

    }

    public function update(Request $request, ProductionMethod $production_method)
    {
        if (!$request->description || strlen($request->description) < 10) {
            return back()->withErrors("لطفا متن روش تولید را حداقل 10 کاراکتر وارد نمایید.");
        }
        $production_method->update($request->all());
        return redirect()->route($this->route_path . "index")->with(["success" => "اطلاعات با موفقیت ذخیره شد"]);

    }

}