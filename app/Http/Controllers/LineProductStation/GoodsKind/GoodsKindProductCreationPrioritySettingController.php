<?php

namespace App\Http\Controllers\LineProductStation\GoodsKind;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\GoodsKind;
use App\Models\LineProduct\Product\ProductCreation\ProductCreationProcessPriority;
use App\Models\Utility\Option;
use App\Models\Utility\Setting;
use Illuminate\Http\Request;


class GoodsKindProductCreationPrioritySettingController extends Controller
{
    // تنظیمات
    private $view_path = "line_product_station.goods_kind.setting.product_creation_priority.";
    private $route_path = "line_product_station.goods_kind.setting.product_creation_priority.";

    public function index(GoodsKind $goods_kind)
    {

        $next_status_option_list = [];
        $before_status_option_list = [];
        $post_option_list=[];
        // به ازای هر ماژول گزینه ها را ست می کنیم.
        $priority_setting = ProductCreationProcessPriority::
        join("buttons", "buttons.id", "button_id")->
        where("goods_kind_id", $goods_kind->id)->
        select("product_creation_process_priority.*")->
        orderBy("priority_number")->
        paginate(20);

        $next_status_option_default = Option::get("status",0, 5231);
        $before_status_option_default = Option::get("status", 0, 5231);
        $post_option_default = Option::get("posts", 0);

        foreach ($priority_setting as $item) {
            $next_status_option_list[$item->button_id] = $next_status_option_default;
            foreach ($next_status_option_list[$item->button_id]["items"] as &$option_item){
                if($option_item["value"]== $item->next_status_id){
                    $option_item["selected"]=1;
                }
            }

            $before_status_option_list[$item->button_id] = $before_status_option_default;
            foreach ($before_status_option_list[$item->button_id]["items"] as &$option_item){
                if($option_item["value"]== $item->before_status_id){
                    $option_item["selected"]=1;
                }
            }

            $post_option_list[$item->button_id] = $post_option_default;
            foreach ($post_option_list[$item->button_id]["items"] as &$option_item){
                if($option_item["value"]== $item->post_id){
                    $option_item["selected"]=1;
                }
            }
        }


        return view($this->view_path . "index", compact("priority_setting", "next_status_option_list", "before_status_option_list", "goods_kind","post_option_list"));
    }

    public function submit(Request $request, GoodsKind $goods_kind)
    {
        $priority_setting = ProductCreationProcessPriority::
        where("goods_kind_id", $goods_kind->id)->
        get();

        foreach ($priority_setting as $item) {
            $key_next = "next_status_id_" . $item->button_id;
            $key_before = "before_status_id_" . $item->button_id;
            $description = "description_" . $item->button_id;
            $post="post_id_". $item->button_id;
            $priority_id="priority_".$item->button_id;
            if(isset($request->$priority_id)) {
              //  return $request->$key_next;
                $item->next_status_id = $request->$key_next;
                $item->before_status_id = $request->$key_before;
                $item->description = $request->$description;
                $item->post_id = $request->$post;
                $item->save();
            }
        }
        $goods_kind->has_sampling_required_in_product_creation = $request->has_sampling_required_in_product_creation ? 1 : 0;
        $goods_kind->save();

        return back()->with(["success" => "اطلاعات با موفقیت ذخیره گردید"]);
    }
}
