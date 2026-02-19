<?php

namespace App\Http\Controllers\HR\Post;

use App\Http\Controllers\Controller;
use App\Models\Customer\ChannelTypePost;
use App\Models\HR\User\CooperationTypePermission;
use App\Models\LineProduct\GoodsKind\GoodsKindPost;
use App\Models\LineProduct\GoodsKind\GoodsKindPropertyPost;
use App\Models\Post\Post;
use App\Models\Post\PostCooperationType;
use App\Models\Post\PostScript;
use App\Models\Post\PostStatus;
use App\Models\Utility\Address\ProvincePost;
use App\Models\Utility\Menu\ButtonPost;
use App\Models\Utility\Menu\MenuPost;
use App\Models\Utility\SpecialLicense\SpecialLicense;
use App\Models\Utility\Unit;
use Illuminate\Http\Request;

class PostInIcController extends Controller
{
    var $view_path = "hr.post.post_in_ic.";
    var $rout_path = "hr.post.post_in_ic.";

    public function index(Post $post, $search = "")
    {
        $result = SpecialLicense::CheckConnection(env("IC_APIKEY"));// در این ای پی ای چک می شود ایا اتصال به اینترنت برقرار است یا خیر
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }
        $list = [];
        if ($search != "") {
            $result_posts = Post::SearchPostInIc($search, env("IC_APIKEY"));
            if (!$result_posts["result"]) {
                return back()->withErrors($result_posts["error"]);
            }
            $list = $result_posts["posts"];
        }
        return view($this->view_path . "index", compact('list', 'post', 'search'));
    }

    public function search(Post $post, Request $request)
    {

        $result = SpecialLicense::CheckConnection(env("IC_APIKEY"));// در این ای پی ای چک می شود ایا اتصال به اینترنت برقرار است یا خیر
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }

        $search = $request->search;

        $result_posts = Post::SearchPostInIc($search, env("IC_APIKEY"));

        if (!$result_posts["result"]) {
            return back()->withErrors($result_posts["error"]);
        }
        return redirect()->route($this->rout_path . "index", [$post, $search]);
    }

    public function update(Post $post, $post_id)
    {
        $result = SpecialLicense::CheckConnection(env("IC_APIKEY"));// در این ای پی ای چک می شود ایا اتصال به اینترنت برقرار است یا خیر
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }
        $result_posts = Post::ShowPostInIc($post_id, env("IC_APIKEY"));// پستی که کاربر انتخاب کرده تا به جای پست فعلی جایگزین شود
        if (!$result_posts["result"]) {
            return back()->withErrors($result_posts["error"]);
        }
        //منوها...................
//        return $result_posts = $this->test($post_id);
        MenuPost::where('post_id', $post->id)->delete();
        foreach ($result_posts["menu_post"] as $menu_post) {
            $menu = ['post_id' => $post->id, 'menu_id' => $menu_post];
            MenuPost::insert($menu);
        }
        ////..........................کانال توزیع
        ChannelTypePost::where('post_id', $post->id)->delete();
        foreach ($result_posts["channel_type_post"] as $channel_type_post) {
            $channel_type = ['post_id' => $post->id, 'channel_type_id' => $channel_type_post];
            ChannelTypePost::insert($channel_type);
        }
        //...................استان ها
        ProvincePost::where('post_id', $post->id)->delete();
        foreach ($result_posts["province_post"] as $province_post) {
            $channel_type = ['post_id' => $post->id, 'province_id' => $province_post];
            ProvincePost::insert($channel_type);
        }
        //وضعیت های ............................
        PostStatus::join("status", "status.id", "status_id")->
        whereIn("status.status_type_id", [350, 351, 7007, 5231, 4640,])->
        where('post_status.post_id', $post->id)->
        delete();
        foreach ($result_posts["post_status"] as $post_status) {
            $status = ['post_id' => $post->id, 'status_id' => $post_status['status_id'], 'permission_type_id' => $post_status['permission_type_id']];
            PostStatus::insert($status);
        }
        //رسته های کالا............................
        GoodsKindPost::where('post_id', $post->id)->delete();
        foreach ($result_posts["goods_kind_post"] as $goods_kind_post) {
            $goods_kind = ['post_id' => $post->id, 'goods_kind_id' => $goods_kind_post];
            GoodsKindPost::insert($goods_kind);
        }
        //مشخصات رسته های کالا......................
        GoodsKindPropertyPost::where('post_id', $post->id)->delete();
        foreach ($result_posts["goods_kind_post_property"] as $goods_kind_post_property) {
            $goods_kind_post_property_post = ['post_id' => $post->id, 'goods_kind_id' => $goods_kind_post_property['goods_kind_id'], 'goods_kind_property_id' => $goods_kind_post_property['goods_kind_property_id']];
            GoodsKindPropertyPost::insert($goods_kind_post_property_post);
        }

        //اشیا هوشمند..............................
        PostScript::where('post_id', $post->id)->delete();
        foreach ($result_posts["post_script"] as $post_script) {
            $script = ['post_id' => $post->id,
                'script_id' => $post_script['script_id'],
                'allow_edit' => $post_script['allow_edit'],
                'allow_view_log' => $post_script['allow_view_log'],
            ];
            PostScript::insert($script);
        }

        //شاغلین...................................

        CooperationTypePermission::where('post_id', $post->id)->delete();
        foreach ($result_posts["cooperation_type_permission"] as $cooperation_type_permission) {
            $cooperation_type_permissions = ['post_id' => $post->id, 'cooperation_type_id' => $cooperation_type_permission];
            CooperationTypePermission::insert($cooperation_type_permissions);
        }


        // عملیات ها ی مرتبط دسترسی های ملیات ...............................
        ButtonPost::join("buttons", "buttons.id", "button_id")->
        whereIn("buttons.status_type_id", [7007, 5231, 4640])->
        where('button_post.post_id', $post->id)->
        delete();
        foreach ($result_posts["button_post"] as $button_post) {
            $button = ['post_id' => $post->id, 'button_id' => $button_post['button_id'], 'permission_type_id' => $button_post['permission_type_id'], 'button_name' => $button_post['button_name']];
            ButtonPost::insert($button);
        }
        //عملیات های مرتبط با دسترسی منو...................................
        ButtonPost::join("buttons", "buttons.id", "button_id")->
        whereNotNull("buttons.menu_id")->
        select('button_post.button_name', 'button_post.permission_type_id', 'button_post.button_id')->
        where('button_post.post_id', $post->id)->
        delete();
        foreach ($result_posts["button_post_menu"] as $button_post_menu) {
            $button_post_menus = ['post_id' => $post->id, 'button_id' => $button_post_menu['button_id'], 'permission_type_id' => $button_post_menu['permission_type_id'], 'button_name' => $button_post_menu['button_name']];
            ButtonPost::insert($button_post_menus);
        }
        //همکاری با ما ..............................
        PostCooperationType::where('post_id', $post->id)->delete();
        foreach ($result_posts["post_cooperation_type"] as $post_cooperation_type) {
            $cooperation_type = ['post_id' => $post->id, 'cooperation_type_id' => $post_cooperation_type];
            PostCooperationType::insert($cooperation_type);
        }

        return redirect()->back()->with(["success" => "اطلاعات پست با موفقیت بروزرسانی گردید."]);

    }

    public function test($post_id)
    {
        $post = Post::where('id', $post_id)->first();
        $menu_post = MenuPost::where('post_id', $post->id)->pluck('menu_id')->toArray(); // منو های دسترسی
        $channel_type_post = ChannelTypePost::where('post_id', $post->id)->pluck('channel_type_id')->toArray();// کانال توزیع
        $province_post = ProvincePost::where('post_id', $post->id)->pluck('province_id')->toArray();// استان

        $post_status = PostStatus::join("status", "status.id", "status_id")->
        whereIn("status.status_type_id", [350, 351, 7007, 5231, 4640,])->
        where('post_status.post_id', $post->id)->
        select('post_status.status_id', 'post_status.permission_type_id')->
        get()->
        toArray();;// وضعیت

        $goods_kind_post = GoodsKindPost::where('post_id', $post->id)->pluck('goods_kind_id')->toArray();//رسته های کالا
        $goods_kind_post_property = GoodsKindPropertyPost::where('post_id', $post->id)->get()->toArray();//مشخصات رسته های کالا
        $post_script = PostScript::where('post_id', $post->id)->get()->toArray();//اشیا هوشمند
        $post_cooperation_type = PostCooperationType::where('post_id', $post->id)->pluck('cooperation_type_id')->toArray();//همکاری با ما
        $cooperation_type_permission = CooperationTypePermission::where('post_id', $post->id)->pluck('cooperation_type_id')->toArray();//شاغلین
        $button_post = ButtonPost::join("buttons", "buttons.id", "button_id")->
        whereIn("buttons.status_type_id", [7007, 5231, 4640])->
        select('button_post.button_name', 'button_post.permission_type_id', 'button_post.button_id')->
        where('button_post.post_id', $post->id)->
        get()->
        toArray();
        $button_post_menu = ButtonPost::join("buttons", "buttons.id", "button_id")->
        whereNotNull("buttons.menu_id")->
        select('button_post.button_name', 'button_post.permission_type_id', 'button_post.button_id')->
        where('button_post.post_id', $post->id)->
        get()->
        toArray();
        return $response = [
            'result' => true,
            'post' => $post,
            'menu_post' => $menu_post,
            'channel_type_post' => $channel_type_post,
            'province_post' => $province_post,
            'post_status' => $post_status,
            'goods_kind_post' => $goods_kind_post,
            'goods_kind_post_property' => $goods_kind_post_property,
            'post_script' => $post_script,
            'post_cooperation_type' => $post_cooperation_type,
            'button_post' => $button_post,
            'cooperation_type_permission' => $cooperation_type_permission,
            'button_post_menu' => $button_post_menu,


        ];

    }
}
