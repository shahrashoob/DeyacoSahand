<?php

namespace App\Http\Controllers\HR\Employment\Admin\Customer;


use App\Http\Controllers\Controller;
use App\Models\Accounting\Tariff\Tariff;
use App\Models\HR\Employment\Employment;
use App\Models\Utility\Option;
use Illuminate\Http\Request;


class TariffController extends Controller
{
    protected $view_path = "hr.employment.admin.customer.tariff.";
    protected $route_path = "hr.employment.admin.customer.tariff.";


    public function create(Employment $employment)
    {

        $currencies_option = Option::get("currency");
        $tariff = new Tariff();
        return view($this->view_path . "create", compact("currencies_option", "tariff", "employment"));
    }

    public function store(Request $request, Employment $employment)
    {

        $tarrif = \App\Http\Controllers\Accounting\Tariff\TariffController::CreateTariff($request);
        if (!$tarrif['result']) {
            return back()->withErrors($tarrif['error']);
        }
        return redirect()->route("hr.employment.admin.customer.drafting_contract.index", $employment)->with(["success" => "تعرفه با موفقیت اضافه شد"]);
    }


}
