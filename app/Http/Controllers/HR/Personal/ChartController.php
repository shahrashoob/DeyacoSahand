<?php

namespace App\Http\Controllers\HR\Personal;

use App\Http\Controllers\Controller;
use App\Models\Post\Post;
use App\Models\Post\PostUser;
use App\Models\Post\PostWorker;

class ChartController extends Controller
{
    private $view_path = "hr.personal.chart.";

    public function show_chart()// تابعی که چارت سازمانی را نمایش می دهد.
    {
        $post = \Auth::user()->posts->first()->post;
        $post = Post::find(1001);
        $nodes = $this->node($post, 0);//فراخوانی تابع نود که ورودی های چارت را می گیرد

        return view($this->view_path . "show_chart", compact('nodes'));
    }

    public function node(Post $post, $PID)// تابع نود که ورودی های چارت را می گیرد
    {
        $nodes = [];
        $node_list = [];
        $post_user_list = PostUser::
        join("posts", "post_id", "posts.id")->
        where("active_status_id", 1200)->
        where("post_id", $post->id)->
        groupBy("user_id")->
        get();//در اینجا چارت از جدول postuserفراخوانی میشود.
        if (count($post_user_list) == 0) {
            // اگر هیچ کاربری به این پست اختصاص نیافته باشد
            $nodes[] = [
                'id' => $post->id,
                'pid' => $PID,
                'name' => " ",
                'title' => $post->caption,
                'img' => " ",
            ];
        } elseif (count($post_user_list) == 1) {
            // اگر یک کاربر به این پست اختصاص یافته باشد
            $nodes[] = [
                'id' => $post->id,
                'pid' => $PID,
                'name' => $post_user_list[0]->worker->fullname(),
                'title' => $post_user_list[0]->post->caption,
                'img' => $post_user_list[0]->worker->image->path ?? "",
            ];

        } elseif (count($post_user_list) > 1) {
            // اگر بیش از یک کاربر به این پست اختصاص یافته باشد
            $nodes[] = [
                'id' => $post->id,
                'pid' => $PID,
                'groupName' => $post_user_list[0]->post->caption,
                'tags' => ["node-with-subtrees"]
            ];

            foreach ($post_user_list as $post_user_item) {
                $nodes[] = [
                    'id' => $post->id . "_" . $post_user_item->worker->id,
                    'stpid' => $post->id,
                    'name' => $post_user_item->worker->fullname(),
                    'title' => $post_user_item->post->caption,
                    'img' => $post_user_item->worker->image->path ?? "",
                ];

            }
            return $nodes;
        }
        // اگر این پست زیرپست داشته باشد
        $child_post = Post::
        where("parent_id", $post->id,)->
        where("active_status_id", 1200)->
        get();
        foreach ($child_post as $cpost) {
            $node_list_child = $this->node($cpost, $post->id);
            $nodes = array_merge($nodes, $node_list_child);

        }
        $node_list = array_merge($node_list, $nodes);

        return $node_list;

    }


}
