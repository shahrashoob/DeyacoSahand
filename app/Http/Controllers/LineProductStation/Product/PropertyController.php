<?php

namespace App\Http\Controllers\LineProductStation\Product;

use App\Http\Controllers\Controller;
use App\Models\File\File;
use App\Models\LineProduct\GoodsKind;
use App\Models\LineProduct\GoodsKind\GoodsKindPropertyDependentValue;
use App\Models\LineProduct\GoodsKindProperty;
use App\Models\LineProduct\GoodsKindPropertyValue;
use App\Models\LineProduct\Product;
use App\Models\Utility\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PropertyController extends Controller
{
    public $route_path = "line_product_station.product.property.";
    public $view_path = "line_product_station.product.property.";

    public function index(Product $product)
    {
        return self::GetIndex($product, $this->view_path, $this->route_path, null);

    }

    public
    function submit(
        Request $request, Product $product
    )
    {
        $result = self::PostSubmit($request, $product);
        if ($result["result"]) {
            return redirect()->route("line_product_station.product.consumed_product.index", $product)->with(["success" => $result["message"]]);
        } else {
            return back()->withErrors($result["error"]);
        }

    }

    public function upload_image(Product $product, GoodsKindProperty $goods_kind_property)
    {
        return self::GetUploadImage($product, $goods_kind_property, $this->view_path, $this->route_path, null);

    }

    public static function GetUploadImage(Product $product, GoodsKindProperty $goods_kind_property, $view_path, $route_path, $product_creation_process)
    {
        if ($goods_kind_property->field_type_id != 4) {
            return back()->withErrors("نوع مشخصه از نوع تصویر نمی باشد.");
        }

        return view($view_path . "upload_image", compact("product", "goods_kind_property", "route_path", "product_creation_process"));

    }

    public function submit_upload_image(Request $request, Product $product, GoodsKindProperty $goods_kind_property, $product_creation_process_id = 0)
    {


        $this->validate($request, ['file_uploaded' => 'required|mimes:jpeg,png,jpg,gif,svg,zip,xlsx,xls,doc,docx,pptx|max:548',]);


        $result = self::PostSubmitUploadImage($request, $product, $goods_kind_property);
        if ($result["result"]) {
            if ($product_creation_process_id) {
                return redirect()->route("line_product_station.product.product_creation.property.index", $product_creation_process_id)->with(["success" => $result["message"]]);
            }
            return redirect()->route("line_product_station.product.property.index", $product)->with(["success" => $result["message"]]);
        } else {
            return back()->withErrors($result["error"]);
        }
//        return redirect()->route("utility.file.product.show_property", [
//            $product,
//            $goods_kind_property,
//            "product.edit_property"
//        ])->
//        with(["success" => "آپلود با موفقیت انجام شد."]);

    }

    public static function PostSubmitUploadImage(Request $request, Product $product, GoodsKindProperty $goods_kind_property)
    {
        if ($goods_kind_property->field_type_id != 4) {
            return [
                "result" => true,
                "error" => "نوع مشخصه از نوع تصویر نمی باشد."
            ];
        }

        $path = "upload/product/goods_kind_property/";
        $file = File::uploadFile(
            $request->file("file_uploaded"),
            $product->id . "_" . $goods_kind_property->id . "_" . Str::random(15) . File::get_file_extension($request->file("file_uploaded")->getClientOriginalName()),
            4,
            $path, true);

        GoodsKindPropertyValue::
        where("product_id", $product->id)->
        where("goods_kind_property_id", $goods_kind_property->id)->
        delete();

        $goods_kind_value = new GoodsKindPropertyValue();
        $goods_kind_value->product_id = $product->id;
        $goods_kind_value->goods_kind_property_id = $goods_kind_property->id;
        $goods_kind_value->value = $file->id;
        $goods_kind_value->save();

        return [
            "result" => true,
            "message" => "آپلود با موفقیت انجام شد."
        ];
    }

    public function delete(Product $product, GoodsKindProperty $goods_kind_property)
    {
        $result = self::GetDelete($product, $goods_kind_property);
        if ($result["result"]) {
            return back()->with(["success" => $result["message"]]);
        } else {
            return back()->withErrors($result["error"]);
        }

    }

    public static function GetDelete(Product $product, GoodsKindProperty $goods_kind_property)
    {

        if ($goods_kind_property->field_type_id != 4) {
            return back()->withErrors("نوع مشخصه از نوع تصویر نمی باشد.");
        }

        $goods_kind_property_value = GoodsKindPropertyValue::where([
            "goods_kind_property_id" => $goods_kind_property->id,
            "product_id" => $product->id
        ])->first();
        $file = File::find($goods_kind_property_value->value ?? 0);
        if (!$goods_kind_property_value || !$file) {
            return [
                "result" => false,
                "error" => "فایل معتبر نمی باشد."
            ];
        }


        $goods_kind_property_value->delete();
        return [
            "result" => true,
            "error" => "حذف با موفقیت انجام شد."
        ];
    }

    public static function GetIndex(Product $product, $view_path, $route_path, $product_creation_process, $property_ids = null)
    {
        if ($property_ids == null) {
            $property_ids = GoodsKindProperty::
            where("goods_kind_id", $product->goods_kind->id)->
            where("status_id", 1200)-> // Active
            pluck("id");
        }
        $property = GoodsKindProperty::whereIn("id", $property_ids)->orderBy('priority_number')->get();
        $property_value = GoodsKindPropertyValue::where("product_id", $product->id)->pluck("value", "goods_kind_property_id");

        $property_option = [];
        foreach ($property as $item) {
            if ($item->field_type_id == 3) { //Select
                $value = isset($property_value[$item->id]) ? $property_value[$item->id] : 0;
                $property_option[$item->id] = Option::get("goods_kind_property_option", $value, $item->id);
            } elseif ($item->field_type_id == 5) { // True/False
                $value = isset($property_value[$item->id]) ? $property_value[$item->id] : 0;
                $property_option[$item->id] = Option::get("goods_kind_property_option_type5", $value, $item->id);
            }
        }

        $ids = GoodsKindPropertyDependentValue::whereIn("goods_kind_property_id", $property_ids)->orderBy("id")->pluck("goods_kind_property_id");
        $parent_ids = GoodsKindPropertyDependentValue::whereIn("goods_kind_property_id", $property_ids)->orderBy("id")->pluck("goods_kind_property_parent_id");
        $parent_value = GoodsKindPropertyDependentValue::whereIn("goods_kind_property_id", $property_ids)->orderBy("id")->pluck("parent_value");
        $compare = GoodsKindPropertyDependentValue::whereIn("goods_kind_property_id", $property_ids)->orderBy("id")->pluck("compare");


        return view(
            $view_path . "index", compact(
            "property_option",
            "property_value",
            "property",
            "product", "product_creation_process", "view_path", "route_path",
            "ids", "parent_ids", "parent_value", "compare", "property_ids"));
    }

    public static function PostSubmit(Request $request, Product $product)
    {
        $valid_property = explode(",", $request->valid_property);

        if (count($valid_property) == 0) {
            return [
                "result" => false,
                "message" => "هیچ مشخصه ای مقدار دهی نشده است."
            ];
        }
        $property = GoodsKindProperty::where("goods_kind_id", $product->goods_kind->id)->get();
        GoodsKindPropertyValue::
        join("goods_kind_properties", "goods_kind_property_id", "goods_kind_properties.id")->
        where("product_id", $product->id)->
        whereNotIn("field_type_id", [4])-> // تصویر
        delete();
        foreach ($property as $item) {
            $id = "property_" . $item->id;
            if (isset($request->$id) && in_array($item->id, $valid_property)) {
                // echo "<br/>" . $item->id . ( $request->$id );
                $goods_kind_value = new GoodsKindPropertyValue();
                $goods_kind_value->product_id = $product->id;
                $goods_kind_value->goods_kind_property_id = $item->id;
                $goods_kind_value->value = $request->$id;
                $goods_kind_value->save();
            }

        }

        // بروز رسانی ویژگی های اصلی کالا
        GoodsKind::UpdateProperty($product->goods_kind, $product->id);

        Product\Version\ProductVersion::GetVersion($product,true,false);
        return [
            "result" => true,
            "message" => "اطلاعات با موفقیت ذخیره شد"
        ];


    }
}
