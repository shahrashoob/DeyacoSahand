<?php

namespace App\Http\Controllers\Import\Product;

use App\Http\Controllers\Controller;
use App\Imports\Product\BaseAndStorageImport;
use App\Imports\ProductImport;
use App\Models\LineProduct\NewProduct;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Import\BaseAndStorage;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class BaseAndStorageController extends Controller
{
    var  $route_path="import.product.base_and_storage.";
    public function index( ) {

        $model= ["name"=>"product_base_and_storage", "route" => "import.product.base_and_storage.upload", "caption" => " اطلاعات پایه و انبارش کالا " ];
        return view( "import/index", compact( "model") );

    }
    public function upload(){

        Excel::import( new BaseAndStorageImport(), request()->file( 'file_uploaded' ) );

        return redirect( )->route($this->route_path."show")->with(["success"=>"آپلود با موفقیت انجام شده"]);

    }
    public function show()
    {

        $list=BaseAndStorage::orderBy("error","desc")->paginate(50);

        $error_count=BaseAndStorage::where("error","!=","")->count();

        return view("import/product/base_and_storage_list",compact("list","error_count"));

    }

    public function update(){

        $list=BaseAndStorage::where("error","")->get();
        foreach($list as $item){

            if($item->product_id == 0){
                $product=Product::create($item->toArray());
                $product->active_status_id=1210; //غیرفعال
                $product->save();
            }
            else{
                $product=Product::find($item->product_id);
                $product->update($item->toArray());
            }

        }
        return redirect( )->route($this->route_path."index")->with(["success"=>"آپلود با موفقیت انجام شده"]);
    }
}
