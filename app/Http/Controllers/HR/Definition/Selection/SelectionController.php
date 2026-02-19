<?php

namespace App\Http\Controllers\HR\Definition\Selection;

use App\Http\Controllers\Controller;
use App\Models\HR\Selection\Selection;
use App\Models\HR\Selection\SelectionIndicator;
use App\Models\Utility\Option;
use Illuminate\Http\Request;

class SelectionController extends Controller
{
    private $view_path = "hr.definition.selection.selection.";
    private $route_path = "hr.definition.selection.selection.";

    //نمایش صفحه افزودن گزینش
    public function create()
    {
        $selection_type_option = Option::get("selection_type");

        return view($this->view_path . "create", compact('selection_type_option'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'selection_type_id' => ['required'],
            "caption" => ['required'],

        ]);
        if (Selection::where("caption", $request->caption)->exists()) {
            return back()->withErrors("عنوان تکراری است.");
        }

        $selection = Selection::create([
            'selection_type_id' => $request->selection_type_id,
            "caption" => $request->caption,


        ]);

        return redirect()->route($this->route_path . "index")->with(["success" => "یک گزینش با موفقیت اضافه شد"]);
    }

    //نمایش صفحه لیست گزینش

    public function index()
    {
        $selections = selection::all();
        return view($this->view_path . "index", compact('selections'));
    }

    public function destroy($id)
    {
        $selection = Selection::find($id);
        // چک کنید آیا هیچ شاخصی برای این گزینش تعریف نشده است یا نه
        $has_indicators = SelectionIndicator::where("selection_id", $id)->exists();
        if ($has_indicators) {
            return back()->withErrors("برای گزینش " . $selection->caption ." ابتدا شاخص های آن را حذف نمایید.");
        }

        $selection->delete();
        return redirect()->route($this->route_path . "index")->with(["success" => "یک گزینش با موفقیت حذف گردید"]);

    }

    // نمایش ادیت گزینش
    public function edit(Selection $selection)
    {

        $selection_option = Option::get("selection_type", $selection->education_type_id);

        return view($this->view_path . "edit", compact("selection_option", 'selection'));

    }

    //ادیت گزینش
    public function update(Request $request, Selection $selection)
    {

        $selection->caption = $request->input('caption');
        $selection->selection_type_id = $request->input('selection_type_id');
        $selection->save();
        return redirect()->route($this->route_path . "index")->with(["success" => "اطلاعات با موفقیت ذخیره شد"]);

    }



}
