<?php

namespace App\Http\Controllers\LineProductStation\Product;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\LotNumber;
use App\Models\LineProduct\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LotNumberController extends Controller
{
    //
    var $route_path="line_product_station.product.lot_number.";
    var $view_path="line_product_station.product.lot_number.";
    function index(Product $product){

        return self::GetIndex($product, $this->view_path,$this->route_path,null);
    }
    function store(
        Request $request, Product $product
    ) {
        $result = self::PostStore($request, $product);
        if ($result["result"]) {
            return back()->with(["success" => $result["message"]]);
        } else {
            return back()->withErrors($result["error"]);
        }

    }
    public function edit(Product $product,LotNumber $lot_number){
        return self::GetEdit($product,$lot_number, $this->view_path,$this->route_path,null);
    }
    public function update(Request $request,Product $product,LotNumber $lot_number){

        $result = self::PostUpdate($request, $product,$lot_number);
        if ($result["result"]) {
            return redirect()-> route($this->route_path."index",$product)->with(["success" => $result["message"]]);
        } else {
            return back()->withErrors($result["error"]);
        }
    }


    public static function GetIndex(Product $product, $view_path,$route_path,$product_creation_process)
    {
        return view($view_path . "index", compact("product", "product_creation_process",        "view_path","route_path"));
    }

    public static function PostStore(Request $request, Product $product)
    {
        $result = LotNumber::ExistsCode( $request->code, $product->id );
        if ( $result ) {
            return [
                "result" => false,
                "error" =>"کد لات (همبافت) تکراری است"
            ];
        }

        $lot             = new LotNumber();
        $lot->code       = $request->code;
        $lot->product_id = $product->id;
        $lot->user_id=Auth::id();
        $lot->save();

        return [
            "result" => true,
            "message" => "لات (همبافت) با موفقیت اضافه گردید"
        ];


    }

    public static function GetEdit(Product $product,LotNumber $lot_number, $view_path,$route_path,$product_creation_process)
    {
        return view($view_path . "edit", compact("product","lot_number", "product_creation_process",        "view_path","route_path"));
    }

    public static function PostUpdate(Request $request, Product $product,LotNumber $lot_number)
    {
        $result = LotNumber::ExistsCode( $request->code, $product->id ,$lot_number->id);
        if ( $result ) {
            return [
                "result" => false,
                "error" =>"کد لات (همبافت) تکراری است"
            ];
        }
        $lot_number->update($request->all());

        return [
            "result" => true,
            "message" => "عملیات با موفقیت انجام شد."
        ];


    }
}
