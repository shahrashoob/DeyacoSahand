<?php

namespace App\Http\Controllers\HR\Post;

use App\Http\Controllers\Controller;
use App\Models\HR\Evaluation\EvaluationType;
use App\Models\HR\Selection\SelectionPostSetting;
use App\Models\Post\Evaluation\PostEvaluation;
use App\Models\Post\Evaluation\PostEvaluationIndicator;
use App\Models\Post\Post;
use App\Models\Post\PostChatSetting;
use App\Models\Post\PostDocumentType;
use App\Models\Utility\Document\DocumentType;
use App\Models\Utility\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatSettingController extends Controller
{
    ///  hr/post/chat
    private $view_path = "hr.post.chat.";
    private $route_path = "hr.post.chat.";

    //نمایش لیست تنظیمات گفتگوی برخط
    public function index(Post $post)
    {

        ################### Post Chat Setting
        $chat_with_posts = PostChatSetting::where("post_id", $post->id)->pluck("chat_with_post_id", "chat_with_post_id")->toArray();
        $post->can_chat_with_posts = count($chat_with_posts) ? 1 : 0;

        $post->chat_with_customers = PostChatSetting::where("post_id", $post->id)->sum("chat_with_customers") ? 1 : 0;
        $post->chat_with_suppliers = PostChatSetting::where("post_id", $post->id)->sum("chat_with_suppliers") ? 1 : 0;
        $post->chat_with_contractors = PostChatSetting::where("post_id", $post->id)->sum("chat_with_contractors") ? 1 : 0;

        $post_user = Auth::user()->posts->first();
        $active_post = $post_user->checkButtonPermission("hr.post.list_active");
        $inactive_post = $post_user->checkButtonPermission("hr.post.list_inactive");
        $post_list = Post::
        select("posts.id", "caption")->
        where("active_status_id", 0)->
        when($active_post, function ($query) {
            return $query->orWhere("active_status_id", 1200);
        })->
        when($inactive_post, function ($query) {
            return $query->orWhere("active_status_id", 1210);
        })->
        get();
        return view($this->view_path . "index", compact('post', "post_list", "chat_with_posts"));
    }

    public function submit(Request $request, Post $post)
    {
        $data = $request->data;
        ####################### Post Chat Setting
        PostChatSetting::where("post_id", $post->id)->delete();
        if (isset($data["chat_with_posts"])) {
            foreach ($data["chat_with_posts"] as $chat_with_post_id => $value)
                PostChatSetting::create([
                    "post_id" => $post->id,
                    "chat_with_post_id" => $chat_with_post_id
                ]);
        }
        if (isset($request->chat_with_customers)) {
            PostChatSetting::create([
                "post_id" => $post->id,
                "chat_with_customers" => 1
            ]);
        }
        if (isset($request->chat_with_suppliers)) {
            PostChatSetting::create([
                "post_id" => $post->id,
                "chat_with_suppliers" => 1
            ]);
        }
        if (isset($request->chat_with_contractors)) {
            PostChatSetting::create([
                "post_id" => $post->id,
                "chat_with_contractors" => 1
            ]);
        }

        return back()->with(["success" => "تنظیمات باموفقیت ذخیره گردید."]);
    }

}
