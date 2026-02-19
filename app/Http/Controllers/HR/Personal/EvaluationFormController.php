<?php

namespace App\Http\Controllers\HR\Personal;

use App\Http\Controllers\Controller;
use App\Http\Controllers\re;
use App\Models\HR\Evaluation\EvaluationForm\EvaluationForm;
use App\Models\HR\Evaluation\EvaluationForm\EvaluationFormExport;
use App\Models\HR\Evaluation\EvaluationForm\EvaluationFormIndicator;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EvaluationFormController extends Controller
{
    private $view_path = "hr.personal.evaluation_form.";
    private $route_path = "hr.personal.evaluation_form.";

    public function index()//نمایش سوابق ارزیابی
    {
        $user_id = Auth::id();
        $worker=Worker::find($user_id);
        $list = EvaluationForm::where('user_id', $user_id)->get();
        return view($this->view_path . "index", compact('list',"worker"));
    }

    public function view(EvaluationForm $evaluation_form)//نمایش ریز ارزیابی عملکرد
    {
        $list=EvaluationFormIndicator::where('evaluation_form_id' , $evaluation_form->id)->get();
        return view($this->view_path . "view",compact('list','evaluation_form'));
    }
}
