<?php

namespace App\Http\Controllers\Accounting\Client;

use App\Http\Controllers\Accounting\Contract\PrintController;
use App\Http\Controllers\Controller;
use App\Models\Accounting\Client\ClientFactor;
use App\Models\Accounting\Client\ClientTransaction;
use App\Models\Utility\Address\Province;
use App\Models\Utility\Notification\SMSMessageResult;
use App\Models\Utility\Pdf;
use App\Models\Utility\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FactorController extends Controller
{
    public $route_path = "accounting.client.factor.";
    public $view_path = "accounting.client.factor.";

    public function index()
    {
        $list = ClientFactor::orderByDesc("created_at")->paginate(50);
        if (!$list) {
            return back()->withErrors("شما هنوز هیچ تراکنشی انجام نداده اید.");
        }
        $list2 = [];
        $list2 = ClientTransaction::where('client_transaction_type_id', 1)->orderByDesc("created_at")->paginate(50);
        return view($this->view_path . "index", compact("list", 'list2'));
    }

    public function print(ClientFactor $client_factor)
    {
        $client_factor = ClientFactor::where('id', $client_factor->id)->first();
        $company_name = Setting::getStringValue('company_name');
        $national_code = Setting::getIntegerValue('national_code');
        $economic_number = Setting::getIntegerValue('economic_number');
        $company_address = Setting::getStringValue('company_address');
        $company_city_name = Setting::getStringValue('company_city_name');
        $company_postal_code = Setting::getStringValue('company_postal_code');
        $company_phone_number = Setting::getStringValue('company_phone_number');
        $company_province_id = Setting::getIntegerValue('company_province_id');
        $company_province = Province::where('id', $company_province_id)->first();
        $html[0] = view($this->view_path . "print", compact("client_factor", 'company_province', 'company_city_name', 'company_phone_number', 'company_postal_code', 'company_name', 'national_code', 'economic_number', 'company_address'))->render();
        $file_name ='فاکتور'. $client_factor->caption;
        Pdf::createAsHtml($html, "L", $file_name, "A4", " ", false);
    }

    public function show(ClientFactor $client_factor)
    {
        $list = ClientTransaction::whereIn('client_transaction_type_id', [1,5])
            ->where('client_factor_id', $client_factor->id)
            ->orderByDesc("created_at")
            ->paginate(50);

        return view($this->view_path . "show", compact('list', 'client_factor'));
    }
}
