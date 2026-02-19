<?php

namespace App\Http\Controllers\HR\Post;

use App\Http\Controllers\Controller;

use App\Models\HR\Selection\Selection;
use App\Models\HR\Selection\SelectionPostSetting;
use App\Models\HR\Selection\SelectionSelector;
use App\Models\Post\Post;
use App\Models\Utility\Option;
use Illuminate\Http\Request;


class PostSelectionController extends Controller
{
    private $view_path = "hr.post.post_selection.";
    private $route_path = "hr.post.post_selection.";
    private $dashboard_path = "hr.post.employment.index";


    public function create(Post $post)
    {
        $selection_setting = SelectionPostSetting::where("post_id", $post->id)->get();

        $selection_active_option = Option::get("selection_active");

        return view($this->view_path . "create", compact('selection_active_option', 'post', 'selection_setting'));

    }

    public function store(Request $request, Post $post)
    {

        $request->validate([
            'priority_number' => ['required'],
            "minimum_score_to_confirm_selection" => ['required'],
            'selection_id' => ['required'],

        ]);
        $exist = SelectionPostSetting::
        where([
            "selection_id" => $request->selection_id,
            "post_id" => $post->id
        ])->
        exists();
        if ($exist) {
            return back()->withErrors("این گزینش قبلا برای پست تعریف شده است.");
        }

        SelectionPostSetting::create([
            'selection_id' => $request->selection_id,
            'post_id' => $post->id,
            "priority_number" => $request->priority_number,
            'minimum_score_to_confirm_selection' => $request->minimum_score_to_confirm_selection,
        ]);

        return redirect()->route($this->dashboard_path, $post)->with(["success" => "یک گزینش با موفقیت برای پست اضافه شد"]);
    }


    public function destroy(Post $post, SelectionPostSetting $post_selection_setting)
    {

        if ($post->id != $post_selection_setting->post_id) {
            return back()->withErrors("اطلاعات گزینش جهت حذف نامعتبر است.");
        }
        $post_selection_setting->delete();
        return redirect()->route($this->dashboard_path, $post)->with(["success" => "یک  گزینش با موفقیت حذف گردید"]);

    }

    public function add_selector(Selection $selection, Post $post)
    {


        $post_option = Option::get("posts",0,0,[],"",[$post->id]);

        $committee_option = Option::get("committee");
        $selection_selectors= SelectionSelector::
        where([
            "selection_id" => $selection->id,
            "post_id" => $post->id,
        ])->
        get();

        return view($this->view_path . "add_selector", compact('selection_selectors','selection', 'post', 'post_option', 'committee_option'));


    }

    public function store_add_selector(Request $request, Selection $selection, Post $post)
    {

        $selection_post_setting = SelectionPostSetting::where('post_id', $post->id)->where('selection_id', $selection->id)->first();

        if (!$selection_post_setting) {
            return back()->withErrors("اطلاعات گزینش پست یافت نشد، لطفا با پشتیبانی تماس بگیرید.");
        }
        if (isset($request->post_id)) {
            $exist = SelectionSelector::
            where([
                "selection_post_setting_id" => $selection_post_setting->id,
                "selection_id" => $selection->id,
                "post_id" => $post->id,
                "post_selection_id" => $request->post_id,
            ])->
            exists();
            if ($exist) {
                return back()->withErrors("این پست قبلا برای این گزینش کننده تعریف شده است.");
            }

            SelectionSelector::create([
                "post_id" => $post->id,
                "selection_id" => $selection->id,
                "post_selection_id" => $request->post_id,
                "selection_post_setting_id" => $selection_post_setting->id,
                'confirmation_is_required' => $request->confirmation_is_required,
            ]);
        }

        if (isset($request->committee_id)) {

            $exist = SelectionSelector::
            where([
                "selection_post_setting_id" => $selection_post_setting->id,
                "selection_id" => $selection->id,
                "post_id" => $post->id,
                "committee_id" => $request->committee_id,
            ])->
            exists();
            if ($exist) {
                return back()->withErrors("این کمیته قبلا برای این گزینش کننده تعریف شده است.");
            }
            SelectionSelector::create([
                "committee_id" => $request->committee_id,
                "minimum_percent_of_committee" => isset($minimum_percent_of_committee[$request->committee_id]) ? $minimum_percent_of_committee[$request->committee_id] : 100,
                "selection_id" => $selection->id,
                "post_id" => $post->id,
                "selection_post_setting_id" => $selection_post_setting->id,
                'confirmation_is_required' => $request->confirmation_is_required,
            ]);
        }
        return redirect()->route($this->route_path . "add_selector", [$selection, $post])->with(["success" => "گزینش کننده با موفقیت برای " . $selection->caption . " در پست " . $post->caption . "  اضافه کرد."]);
    }

    public function destroy_add_selector(SelectionSelector $selection_selector, Selection $selection, Post $post){

        if ($post->id != $selection_selector->post_id && $selection->id != $selection_selector->selection_id) {
            return back()->withErrors("اطلاعات گزینش کننده مورد نظر نامعتبر است.");
        }
        $selection_selector->delete();

        return redirect()->route($this->route_path . "add_selector", [$selection, $post])->with(["success" => "یک گزینش کننده با موفقیت حذف گردید."]);

    }
    public function percent_of_committee(Selection $selection, Post $post)
    {

        if ($selection->selection_selector_committee->count() == 0) {
            return redirect()->route($this->route_path . "add_selector", [$selection, $post])->with(["success" => "اطلاعات با موفقیت ذخیره گردید"]);

        }

        return view($this->view_path . "percent_of_committee", compact("selection", 'post'));
    }

    public function submit_percent_of_committee(Request $request, Selection $selection, SelectionSelector $selectionselector, Post $post)
    {

        foreach ($selection->selection_selector_committee as $item) {
            $item->minimum_percent_of_committee = $request->minimum_percent_of_committee;
            $item->save();
        }
        return redirect()->route($this->route_path . "add_selector", [$selection, $post])->with(["success" => "اطلاعات با موفقیت ذخیره گردید"]);

    }

}
