<?php

namespace App\Http\Controllers\HR\Post;

use App\Http\Controllers\Controller;
use App\Models\Post\Post;
use App\Models\Post\PostChatSetting;
use App\Models\Post\PostEntryStatus;
use App\Models\Utility\Menu\Button;
use App\Models\Utility\Menu\ButtonPost;
use App\Models\Utility\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EntryStatusPermissionController extends Controller
{
    // hr/post/entry_status_permission
    private $view_path = "hr.post.entry_status_permission.";
    private $route_path = "hr.post.entry_status_permission.";

    //نمایش لیست تنظیمات گفتگوی برخط
    public function index(Post $post)
    {
        $post_user = Auth::user()->posts->first();
        if (!$post_user->checkButtonPermission("hr.post.entry_status_permission.index")) {
            return back()->withErrors("شما به عملیات مورد نظر دسترسی ندارید");
        }

        $entry_status_list = Status::whereIn("status_type_id", [4620])->get();
        $start_remote_work =
        ButtonPost::join("buttons", "buttons.id", "button_id")->
        where("post_id", $post->id)->
        where("name", "hr.personal.start_remote_work.index")->
        exists();

        $end_remote_work = ButtonPost::join("buttons", "buttons.id", "button_id")->
        where("post_id", $post->id)->
        where("name", "hr.personal.end_remote_work.index")->
        exists();

        return view($this->view_path . "index", compact('post', 'entry_status_list', "start_remote_work", "end_remote_work"));
    }

    public function submit(Request $request, Post $post)
    {
        $post_user = Auth::user()->posts->first();
        if (!$post_user->checkButtonPermission("hr.post.entry_status_permission.index")) {
            return back()->withErrors("شما به عملیات مورد نظر دسترسی ندارید");
        }

        $data = $request["data"];
        ######################## Entry Status
        PostEntryStatus::
        where("post_id", $post->id)->
        delete();


        if (isset($data["entry_status"])) {
            if (isset($data["entry_status"]["personal"])) {
                foreach ($data["entry_status"]["personal"] as $key => $item) {
                    //

                    PostEntryStatus::create([
                        "post_id" => $post->id,
                        "status_id" => $key,
                        "allow_show_personal_menu" => 1,
                        "allow_show_all_menu" => isset($data["entry_status"]["all_menu"][$key]) ? 1 : 0,
                        "allow_filter_menu" => isset($data["entry_status"]["filter_menu"][$key]) ? 1 : 0,
                    ]);
                }
            }


        }

        ############################### remote work
        $button_start_remote_work = Button::where("name", "hr.personal.start_remote_work.index")->first();
        if($request->has_start_remote_work){
            ButtonPost::firstOrCreate(["button_id" => $button_start_remote_work->id,"post_id"=>$post->id]);
        }
        else{
            ButtonPost::where(["button_id" => $button_start_remote_work->id,"post_id"=>$post->id])->delete();
        }

        $button_end_remote_work = Button::where("name", "hr.personal.end_remote_work.index")->first();
        if($request->has_end_remote_work){
            ButtonPost::firstOrCreate(["button_id" => $button_end_remote_work->id,"post_id"=>$post->id]);
        }
        else{
            ButtonPost::where(["button_id" => $button_end_remote_work->id,"post_id"=>$post->id])->delete();
        }

        ######################## Post Info
        $request["the_worker_has_permission_to_entering_from_static_ip"] = isset($request->the_worker_has_permission_to_entering_from_static_ip) ? 1 : 0;

        $post->update($request->all());

        return back()->with(["success" => "تنظیمات باموفقیت ذخیره گردید."]);
    }

}
