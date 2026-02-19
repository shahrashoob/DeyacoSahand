<?php

namespace App\Http\Controllers\HR\Definition\Selection;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HR\Definition\Selection\SelectionIndicator\Education;
use App\Http\Controllers\HR\Definition\Selection\SelectionIndicator\File;
use App\Models\HR\Selection\Selection;
use App\Models\HR\Selection\SelectionIndicator;
use App\Models\Utility\Option;
use Illuminate\Http\Request;

class SelectionIndicatorController extends Controller
{

    private $view_path = "hr.definition.selection.selection_indicator.";
    private $route_path = "hr.definition.selection.selection_indicator.";

    //نمایش صفحه افزودن محتوای گزینش
    public function create(Selection $selection)
    {
        $selection_indicators = SelectionIndicator::where("selection_id", $selection->id)->get();
        $field_type_option = Option::get("field_type");
        return view($this->view_path . "create", compact('selection', 'field_type_option', 'selection_indicators'));
    }


    //ثبت محتوای گزینش یا شاخص گزینش
    public function store(Request $request, Selection $selection)
    {

        $request->validate([
            "caption" => ['required'],
            'field_type_id' => ['required'],
            'weight' => ['required'],
            'min_score' => ['required'],
        ]);
        $exist = SelectionIndicator::
        where([
            "selection_id" => $selection->id,
            "caption" => $request->caption,
        ])->
        exists();
        if ($exist) {
            return back()->withErrors("این شاخص قبلا برای این گزینش تعریف شده است.");
        }
        $sum_weight = $selection->selection_indicators()->sum('weight');
        $new_sum = $sum_weight + $request->weight;
        if ($new_sum > 100) {
            return back()->withErrors("جمع وزن‌های شاخص‌ها نمی‌تواند بیشتر از 100 باشد.");
        }
        $selection_indicator = SelectionIndicator::create([
            'caption' => $request->caption,
            'selection_id' => $selection->id,
            'field_type_id' => $request->field_type_id,
            'weight' => $request->weight,
            'min_score' => $request->min_score,

        ]);

        if ($selection->active_selection($selection_indicator) == 1200) {
            $selection->active_status_id = 1200;//فعال
        } else {
            $selection->active_status_id = 1210;//غیر فعال
        }
        $selection->save();

        return redirect()->route($this->route_path . "create", $selection)->with(["success" => "یک شاخص برای " . $selection->caption . " با موفقیت اضافه شد"]);
    }


    // نمایش ادیت شاخص گزینش
    public function edit(SelectionIndicator $selection_indicator)
    {

        $field_type_option = Option::get("field_type");
        return view($this->view_path . "edit", compact("field_type_option", 'selection_indicator'));

    }

    //ادیت شاخص گزینش
    public function update(Request $request, SelectionIndicator $selection_indicator)
    {
        // چک کردن که آیا عنوان جدید با عنوان فعلی یکسان است یا نه
        if ($request->caption != $selection_indicator->caption) {
            // اگر عنوان تغییر کرده است، بررسیم می شود که آیا عنوان تکراری است یا نه
            $exist = SelectionIndicator::where([
                "selection_id" => $selection_indicator->selection->id,
                "caption" => $request->caption,
            ])->exists();
            if ($exist) {
                return back()->withErrors("این شاخص قبلا برای این گزینش تعریف شده است.");
            }
        }
        $sum_weight = $selection_indicator->selection->selection_indicators()->where('id', '!=', $selection_indicator->id)->sum('weight');
        $new_sum = $sum_weight + $request->weight;
        if ($new_sum > 100) {
            return back()->withErrors("جمع وزن‌های شاخص‌ها نمی‌تواند بیشتر از 100 باشد.");
        }

        $selection_indicator->caption = $request->caption;
        $selection_indicator->field_type_id = $request->field_type_id;
        $selection_indicator->weight = $request->weight;
        $selection_indicator->min_score = $request->min_score;
        $selection_indicator->save();

        if ($selection_indicator->selection->active_selection($selection_indicator) == 1200) {
            $selection_indicator->selection->active_status_id = 1200;//فعال
        } else {
            $selection_indicator->selection->active_status_id = 1210;//غیر فعال
        }
        $selection_indicator->selection->save();

        return redirect()->route($this->route_path . "create", $selection_indicator->selection_id)->with(["success" => "اطلاعات با موفقیت ذخیره شد"]);

    }

    public function destroy(Selection $selection, SelectionIndicator $selection_indicator)
    {
        if ($selection->id != $selection_indicator->selection_id) {
            return back()->withErrors("اطلاعات شاخص جهت حذف نامعتبر است.");
        }
        $selection_indicator->delete();

        if ($selection->active_selection($selection_indicator) == 1200) {
            $selection->active_status_id = 1200;//فعال
        } else {
            $selection->active_status_id = 1210;//غیر فعال
        }

        $selection->save();
        return redirect()->route($this->route_path . "create", $selection)->with(["success" => "یک شاخص با موفقیت حذف گردید"]);

    }


}
