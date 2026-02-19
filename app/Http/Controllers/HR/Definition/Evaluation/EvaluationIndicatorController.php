<?php

namespace App\Http\Controllers\HR\Definition\Evaluation;

use App\Http\Controllers\Controller;
use App\Models\HR\Evaluation\EvaluationIndicator;
use App\Models\Utility\Option;
use Illuminate\Http\Request;

class EvaluationIndicatorController extends Controller
{
    private $view_path = "hr.definition.evaluation.evaluation.";
    private $route_path = "hr.definition.evaluation.evaluation.";


    //نمایش لیست شاخص های ارزیابی
    public function index()
    {
       $list = EvaluationIndicator::paginate();
        return view($this->view_path . "index",compact('list'));

    }



    //نمایش فرم ایجاد شاخص های ارزیابی
    public function create()
    {

        $evaluation_completion_type=Option::get("evaluation_completion_type");

        return view($this->view_path . "create",compact('evaluation_completion_type'));

    }
    //  ایجاد شاخص های ارزیابی
    public function store(Request $request)
    {
        $request->validate([
            'caption' => ['required'],
            'evaluation_completion_type_id'=> ['required'],

        ]);

        if (EvaluationIndicator::where("caption", $request->caption)->exists()) {
            return back()->withErrors("عنوان شاخص تکراری می باشد.");
        }
        $evaluation_indicator = EvaluationIndicator::create([
            'caption' => $request->caption,
            'evaluation_completion_type_id'=>$request->evaluation_completion_type_id,
        ]);

        return redirect()->route($this->route_path . "index")->with(["success" => "یک شاخص ارزیابی با موفقیت اضافه شد"]);

    }

 //نمایش فرم ویرایش ارزیابی
    public function edit(EvaluationIndicator $evaluation_indicator){

        $evaluation_completion_type=Option::get("evaluation_completion_type");
        return view($this->view_path . "edit",compact('evaluation_completion_type','evaluation_indicator'));
    }
    //ویرایش فرم ارزیابی
    public function update(EvaluationIndicator $evaluation_indicator,Request $request){


        if (EvaluationIndicator::where("caption", $request->caption)->where('id','!=',$evaluation_indicator->id)->exists()) {
            return back()->withErrors("عنوان شاخص تکراری می باشد.");
        }
        $evaluation_indicator->caption = $request->caption;
        $evaluation_indicator->evaluation_completion_type_id= $request->evaluation_completion_type_id;
        $evaluation_indicator->save();
        return redirect()->route($this->route_path . "index")->with(["success" => "اطلاعات با موفقیت ذخیره شد"]);

    }
}
