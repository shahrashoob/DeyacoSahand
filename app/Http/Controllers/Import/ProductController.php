<?php

namespace App\Http\Controllers\Import;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Imports\ProductImport;
use App\Imports\ProductInventoryImport;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\NewProduct;
use App\Models\LineProduct\ProductInventory;
use App\Models\LineProduct\NewProductInventory;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Utility\Option;
use App\Models\Utility\Call;

class ProductController extends Controller
{

    public function index_base_and_storage( ) {

        $model= ["name"=>"product", "route" => "import.product.upload", "caption" => "فایل لیست محصولات " ];
        return view( "import/index", compact( "model") );

    }
    public function upload_base_and_storage(){

        Excel::import( new ProductImport, request()->file( 'file_uploaded' ) );

        return redirect( )->route("import.product.show")->with(["success"=>"آپلود با موفقیت انجام شده"]);

    }
    public function show_base_and_storage()
    {

        $list=NewProduct::orderBy("error","desc")->paginate(50);

        $error_count=NewProduct::where("error","!=","")->count();

        return view("import/product_list",compact("list","error_count"));

    }

    public function update_base_and_storage(){

     $list=NewProduct::where("error","")->get();
        foreach($list as $item){

            if($item->product_id == 0){
                $product=Product::create($item->toArray());
            }
            else{
                $product=Product::find($item->product_id);
                $product->update($item->toArray());
            }

        }
        return redirect( )->route("dashboard")->with(["success"=>"آپلود با موفقیت انجام شده"]);
    }
    ########################################### Inventory

    public function inventory_index( ) {


        $step=3;
        $stepInfo       = Option::stepInfo( "call_steps", $step );

        $model= ["name"=>"product_inventory", "route" => "import.product.inventory.upload", "caption" => "فایل لیست موجودی انبار " ];
        return view( "import/index", compact( "model","stepInfo","step"  ) );

    }
    public function inventory_upload(){

        Excel::import( new ProductInventoryImport, request()->file( 'file_uploaded' ) );

         return redirect( )->route("import.product.inventory.show");
    }
    public function inventory_show()
    {
        $list=NewProductInventory:: paginate(50);

        $error_count=NewProductInventory::where("error","!=","")->count();

        return view("import/product_inventory_list",compact("list","error_count"));

    }
    public function inventory_update(){

        $list=NewProductInventory::select(["product_id","end_inventory"])-> get()->toArray();

        ProductInventory::where("id",">",0)->delete();
        ProductInventory::insert($list);

        $call = Call::where(["status_id"=> 3300])->first();
        $call->user_id=Auth::user()->id;
        $call->save();
        if(!$call){
            return back()->withErrors("لطفا ابتدا فراخوانی را ایجاد کنید.");
        }
        $call->update([
            "warehouse_import_status_id"=>3330
        ]);
        return redirect()->route("call.rscript")->with(["success"=>"آپلود با موفقیت انجام شد."]);
    }
}
