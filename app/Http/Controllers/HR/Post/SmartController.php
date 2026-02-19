<?php

namespace App\Http\Controllers\HR\Post;

use App\Http\Controllers\Controller;
use App\Models\Post\PostSmart;
use App\Models\Post\PostSmartObject;
use App\Models\Utility\Option;
use Illuminate\Http\Request;
use App\Models\Post\Post;
use Illuminate\Support\Facades\Auth;

class SmartController extends Controller
{
    //
    var $view_path = "hr.post.smart_object.";

    public function index(Post $post)
    {
        $post_user = Auth::user()->posts->first();
        $smart_object_setting = $post_user->checkButtonPermission("hr.post.smart_object_setting");
        if (!$smart_object_setting) {
            return back()->withErrors("شما به عملیات مورد نظر دسترسی ندارید");
        }

        $smart_object_ids = PostSmartObject::where("post_id", $post->id)->pluck("smart_object_id")->toArray();
        $smart_object_option = Option::get("smart_object_multiple", $smart_object_ids, 2, 0);
        return view($this->view_path . "index", compact("post", "smart_object_option"));
    }

    public function submit(Request $request, Post $post)
    {

        PostSmartObject::where("post_id", $post->id)->delete();

        if ($request->smart_object_ids) {
            foreach ($request->smart_object_ids as $smart_object_id) {
                if ($smart_object_id) {
                    PostSmartObject::create([
                        "post_id" => $post->id,
                        "smart_object_id" => $smart_object_id
                    ]);
                }
            }
        }

        $post->allow_enter_gross_weight_by_worker_to_posts =isset( $request->allow_enter_gross_weight_by_worker_to_posts)?1:0;
        $post->save();

        return back()->with(["success" => "اطلاعات با موفقیت ذخیره گردید."]);

    }

}
