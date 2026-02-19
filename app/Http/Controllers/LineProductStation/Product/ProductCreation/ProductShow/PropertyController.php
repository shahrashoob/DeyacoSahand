<?php

namespace App\Http\Controllers\LineProductStation\Product\ProductCreation\ProductShow;

use App\Http\Controllers\Controller;
use App\Models\File\File;
use App\Models\LineProduct\GoodsKind\GoodsKindPropertyDependentValue;
use App\Models\LineProduct\GoodsKind\GoodsKindPropertyPost;
use App\Models\LineProduct\GoodsKindProperty;
use App\Models\LineProduct\GoodsKindPropertyValue;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\ProductCreation\ProductCreationProcess;
use App\Models\Post\PostUser;
use App\Models\Utility\Option;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    public $route_path = "line_product_station.product.product_creation.product_show.property.";
    public $view_path = "line_product_station.product.product_creation.product_show.property.";

    public function index(ProductCreationProcess $product_creation_process)
    {

        $post_ids = PostUser::getCurrentPostByShiftWorkAndLeaveOvertime( "post_ids");
       $allowed_goods_kind_property_ids= GoodsKindPropertyPost::where([
            "goods_kind_id"=>$product_creation_process->goods_kind_id
        ])->
            whereIN("post_id",$post_ids)->pluck("goods_kind_property_id")->toArray();
        return \App\Http\Controllers\LineProductStation\Product\PropertyController::GetIndex($product_creation_process->product, $this->view_path, $this->route_path, $product_creation_process,$allowed_goods_kind_property_ids);
    }

}
