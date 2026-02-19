<?php

namespace App\Http\Controllers\QualityControl;

use App\Http\Controllers\Controller;
use App\Models\Form\Form;
use App\Models\LineProduct\Product\RejectProduct\RejectProductForm;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromView;
use function view;

class DashboardController extends Controller
{
    // quality_control/dashboard
    var $view_path = "quality_control.dashboard.";
    var $route_path = "quality_control.dashboard.";

    public function index(Request $request)
    {

        $list_reject_product = RejectProductForm::whereIn("status_id", [
            7009003,
            7009004,
            7009005,
            7009006,
            7009007,
            7009008
        ])->orderBy("id", "desc")->
        paginate(15);

        $list_input_forms = Form::
        where("status_id", 500000535)->
        where("form_type_id", 304)->
        get();

        $list_output_forms = Form::
        where("status_id", 500000535)->
        where("form_type_id", 0)->
        select("forms.*")->
        get();

        return view($this->view_path . "index", compact("list_reject_product", "list_input_forms", "list_output_forms"));
    }

    public function view_reject_product_form(RejectProductForm $reject_product_form)
    {
        return view("quality_control.reject_product.dashboard.view", compact("reject_product_form"));

    }

    public function view_input_form(Form $form)
    {
        if ($form->form_type_id != 304) {
            return back()->withErrors("فرم ورود مورد نظر یافت نشد.");
        }

        return view("quality_control.input_form.dashboard.view", compact("form"));

    }

    public function view_output_form(Form $form)
    {
        if ($form->form_type_id != 0) {
            return back()->withErrors("فرم ورود مورد نظر یافت نشد.");
        }
        $route_back = $this->route_path . "index";
        return view("quality_control.output_form.dashboard.view", compact("form", "route_back"));

    }

}
