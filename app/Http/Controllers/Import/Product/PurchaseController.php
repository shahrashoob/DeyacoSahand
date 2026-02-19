<?php

namespace App\Http\Controllers\Import\Product;

use App\Http\Controllers\Controller;
use App\Imports\Product\ProductPurchaseImport;
use App\Models\LineProduct\Import\ImportProductPurchase;
use App\Models\LineProduct\Product;
use App\Models\Supplier\SupplierProduct;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class PurchaseController extends Controller
{
    var  $route_path="import.product.purchase.";
    public function index( ) {

        $model= ["name"=>"product_purchase", "route" => "import.product.purchase.upload", "caption" => " اطلاعات خرید کالا " ];
        return view( "import/index", compact( "model") );

    }
    public function upload(){

        Excel::import( new ProductPurchaseImport(), request()->file( 'file_uploaded' ) );

        return redirect( )->route($this->route_path."show")->with(["success"=>"آپلود با موفقیت انجام شده"]);

    }
    public function show()
    {

        $list=ImportProductPurchase::orderBy("error","desc")->paginate(50);

        $error_count=ImportProductPurchase::where("error","!=","")->count();

        return view("import/product/product_purchase",compact("list","error_count"));

    }
    public function update(){

        $list=ImportProductPurchase::where("error","")->get();
        foreach($list as $item){

            if($item->product_id == 0){
                return back()->withErrors("کد کالا نامعتبر است");
            }
            else{
                $product=Product::find($item->product_id);
                $product->update($item->toArray());

//                // انبار
//                $supplier_product               = SupplierProduct::firstOrCreate( [
//                    "product_id"  => $product->id,
//                    "supplier_id" => 1
//                ] );
//                $supplier_product->warehouse_id = $item->warehouse_id;
//                $supplier_product->save();
            }

        }
        return redirect( )->route($this->route_path."index")->with(["success"=>"آپلود با موفقیت انجام شده"]);
    }
}
