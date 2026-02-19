<?php

namespace App\Http\Controllers\Import\Product;

use App\Http\Controllers\Controller;
use App\Imports\Product\BaseAndStorageImport;
use App\Imports\Product\ProductProductionImport;
use App\Models\LineProduct\Import\BaseAndStorage;
use App\Models\LineProduct\Import\ImportProductProduction;
use App\Models\LineProduct\Product;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ProductionController extends Controller
{
    var  $route_path="import.product.production.";
    public function index( ) {

        $model= ["name"=>"product_production", "route" => "import.product.production.upload", "caption" => " اطلاعات تولید کالا " ];
        return view( "import/index", compact( "model") );

    }
    public function upload(){

        Excel::import( new ProductProductionImport(), request()->file( 'file_uploaded' ) );

        return redirect( )->route($this->route_path."show")->with(["success"=>"آپلود با موفقیت انجام شده"]);

    }
    public function show()
    {

        $list=ImportProductProduction::orderBy("error","desc")->paginate(50);

        $error_count=ImportProductProduction::where("error","!=","")->count();

        return view("import/product/product_production",compact("list","error_count"));

    }
    public function update(){

        $list=ImportProductProduction::where("error","")->get();
        foreach($list as $item){

            if($item->product_id == 0){
               return back()->withErrors("کد کالا نامعتبر است");
            }
            else{
                $product=Product::find($item->product_id);
                $product->update($item->toArray());
            }

        }
        return redirect( )->route($this->route_path."index")->with(["success"=>"آپلود با موفقیت انجام شده"]);
    }
}
