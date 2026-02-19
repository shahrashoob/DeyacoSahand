<?php

namespace App\Http\Controllers\Accounting\WelfareService\Tara\Admin;

use App\Http\Controllers\Controller;
use App\Models\Accounting\WelfareService\WelfareService;
use App\Models\Utility\Option;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public $route_path = "accounting.welfare_service.tara.admin.dashboard.";
    public $view_path = "accounting.welfare_service.tara.admin.dashboard.";

    public function index()
    {
        $list = WelfareService::get();
        $worker_option = Option::get("worker", 0);

        return view($this->view_path . "index", compact("list", "worker_option"));
    }

    public function add_user(Request $request)
    {
        $worker = Worker::find($request->user_id);
        if (!$worker) {
            return redirect()->route($this->route_path . "index")->withErrors("لطفا یک فرد را انتخاب نمایید.");
        }

        $date_of_birth = jdate(Carbon::parse($worker->date_of_birth)->timestamp)->format('Y/m/d');
        return view($this->view_path . "add_user", compact("worker", "date_of_birth"));
    }

    public function submit_add_user(Request $request, Worker $worker)
    {

        if (!$worker) {
            return redirect()->route($this->route_path . "index")->withErrors("لطفا یک فرد را انتخاب نمایید.");
        }
        $mobile = "0" . $request->mobile;

        $result = \App\Http\Controllers\Accounting\WelfareService\Tara\Client\DashboardController::Login();
        $result = \App\Http\Controllers\Accounting\WelfareService\Tara\Client\DashboardController::Create($worker, $mobile, $result["accessCode"]);


        if (!$result["success"]) {
            return redirect()->route($this->route_path . "index")->withErrors(" <br/> وب سرویس تارا:" . $result["data"]);
        }

        $welf_service = WelfareService::create([
            "user_id" => $worker->id,
            "mobile" => $mobile,
            "national_code" => $worker->national_code,
            "account_number" => $result["accountNumber"],
        ]);
        return redirect()->route($this->route_path . "index")->with("success", "ثبت نام با موفقیت انجام شد.");

    }

    public function charge(WelfareService $welfare_service)
    {
        $worker = $welfare_service->worker;


        return view($this->view_path . "charge", compact("worker", "welfare_service"));
    }


    public function submit_charge(Request $request, WelfareService $welfare_service)
    {

        $amount = $request->amount;
        if ($amount <= 0) {
            return back()->withErrors("لطفا مبلغ شارژ را به درستی وارد نمایید.");
        }

        $result = \App\Http\Controllers\Accounting\WelfareService\Tara\Client\DashboardController::Login();
        $result_trace = \App\Http\Controllers\Accounting\WelfareService\Tara\Client\DashboardController::TraceCode($welfare_service, $amount, "charge", $result["accessCode"]);
        if (!$result_trace["result"]) {
                return back()->withErrors($result_trace["error"]);
        }

        $result = \App\Http\Controllers\Accounting\WelfareService\Tara\Client\DashboardController::Charge($welfare_service, $amount, $result_trace["traceNumber"], $result["accessCode"],"charge");

        if (!$result["result"]) {
            return redirect()->route($this->route_path . "index")->withErrors(" <br/> وب سرویس تارا:" . $result["data"]);
        }


        return redirect()->route($this->route_path . "index")->with("success", "افزایش شارژ با کد رهگیری ".$result["referenceNumber"]."  انجام شد.");

    }

    public function decharge(WelfareService $welfare_service)
    {
        $worker = $welfare_service->worker;


        return view($this->view_path . "dicharge", compact("worker", "welfare_service"));
    }

    public function submit_decharge(Request $request,WelfareService $welfare_service)
    {
        $amount = $request->amount;
        if ($amount <= 0) {
            return back()->withErrors("لطفا مبلغ شارژ را به درستی وارد نمایید.");
        }

        $result = \App\Http\Controllers\Accounting\WelfareService\Tara\Client\DashboardController::Login();
        $result_trace = \App\Http\Controllers\Accounting\WelfareService\Tara\Client\DashboardController::TraceCode($welfare_service, $amount, "decharge", $result["accessCode"]);
        if (!$result_trace["result"]) {
            return back()->withErrors($result_trace["error"]);
        }

        $result = \App\Http\Controllers\Accounting\WelfareService\Tara\Client\DashboardController::DisCharge($welfare_service, $amount, $result_trace["traceNumber"], $result["accessCode"],"discharge");

        if (!$result["result"]) {
            return redirect()->route($this->route_path . "index")->withErrors(" <br/> وب سرویس تارا:" . $result["data"]);
        }


        return redirect()->route($this->route_path . "index")->with("success", "کاهش شارژ با کد رهگیری ".$result["referenceNumber"]."  انجام شد.");

    }
}