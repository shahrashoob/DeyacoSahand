<?php

namespace App\Http\Controllers\HR\Post;

use App\Http\Controllers\Controller;
use App\Models\HR\Selection\PostSelectionEducation;
use App\Models\HR\Selection\Selection;
use App\Models\HR\Selection\SelectionPostSetting;
use App\Models\Post\Post;
use App\Models\Utility\Option;
use Illuminate\Http\Request;
use function Symfony\Component\String\b;


class PostSelectionEducationController extends Controller
{
    private $view_path = "hr.post.post_selection_education.";
    private $route_path = "hr.post.post_selection_education.";

    public function create(Post $post, Selection $selection, SelectionPostSetting $post_selection_setting)
    {

        $education_option = Option::get("education");
        $post_selection_education = PostSelectionEducation::where("post_id", $post->id)->where("selection_id", $selection->id)->where("post_selection_setting_id", $post_selection_setting->id)->get();
        return view($this->view_path . "create", compact('education_option', 'selection', 'post', 'post_selection_education', 'post_selection_setting'));
    }

    public function store(Request $request, Post $post, Selection $selection, SelectionPostSetting $post_selection_setting)
    {
        $request->validate([
            'education_id' => ['required'],
        ]);
        if (PostSelectionEducation::where("education_id", $request->education_id)->where("selection_id", $selection->id)->where("post_id", $post->id)->exists()) {
            return back()->withErrors("پیش نیاز انتخابی شما تکراری است.");
        }
        PostSelectionEducation::create([
            'post_id' => $post->id,
            'education_id' => $request->education_id,
            'selection_id' => $selection->id,
            'post_selection_setting_id' => $post_selection_setting->id,
        ]);

        return redirect()->route($this->route_path . "create", [$post, $selection, $post_selection_setting])->with(["success" => "یک گزینش با موفقیت اضافه شد"]);

    }

    public function destroy(Post $post, Selection $selection, SelectionPostSetting $post_selection_setting, PostSelectionEducation $post_selection_education)
    {

        if ($post->id != $post_selection_education->post_id) {
            return back()->withErrors("اطلاعات گزینش مورد نظر نامعتبر است.");
        }

        $post_selection_education->delete();
        return redirect()->route($this->route_path . "create", [$post, $selection, $post_selection_setting])->with(["success" => "یک تنظیمات پیش نیاز با موفقیت حذف گردید"]);

    }
}
