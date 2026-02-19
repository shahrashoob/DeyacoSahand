<?php

namespace App\Http\Controllers\Accounting\Store;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Utility\Script\Script1016Controller;
use App\Models\Accounting\Client\ClientFactor;
use App\Models\Accounting\Client\ClientTransaction;
use App\Models\Accounting\Store\Store;
use App\Models\Utility\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public $route_path = "accounting.store.dashboard.";
    public $view_path = "accounting.store.dashboard.";

    public function index()
    {

        $currentDate = Carbon::now();
        $list = Store::where('validity_date', '>=', $currentDate)->get();// لیست کالایی که قیمتشان اعتبار دارد
        $support_end_date = Setting::getStringValue("support_end_date");
        $support_end_date = jdate(Carbon::parse($support_end_date)->timestamp)->format('Y/m/d');

        return view($this->view_path . "index", compact('list', "support_end_date"));
    }

    public function create(Request $request, Store $store)
    {

        if ($store->status_id != 4500001) {
            return back()->withErrors("این کالا قبلا خریداری شده است و در وضعیت " . $store->status->caption . " قراردارد.");
        }

        $has_active_contract=Setting::getIntegerValue("has_active_contract");
        if($has_active_contract==0 && !in_array($store->id,[4])){ // بروزرسانی
            return back()->withErrors("با توجه به اینکه کالای انتخاب شده در نسخه فعلی سامانه قرار ندارد، امکان خرید و فعال سازی آن مقدور نمی باشد، لطفا سامانه را به آخرین ورژن بروزرسانی کنید.  ".

                "<br/> جهت بروزرسانی از منو بروزرسانی اقدام نمایید."

            );
        }

        $store = $this->updatePrice($request, $store);

        $post_user = Auth::user()->posts->first();
        $permission_client_buy = $post_user->checkButtonPermission("accounting.client.buy.index");
        $tax_calculation_percentage = Setting::getIntegerValue('tax_calculation_percentage');
        $credit = Setting::getIntegerValue('credit');
        $tax = $store->price_sum * ($tax_calculation_percentage / 100);
        $total_price = $store->price_sum + $tax;
        $months_number = $request->months_number;
        return view($this->view_path . "create", compact("months_number", 'store', 'tax', 'total_price', 'permission_client_buy', 'credit'));
    }

    public function store(Request $request, Store $store)
    {
        $credit = Setting::getIntegerValue('credit');
        if ($credit < $request->price) {
            return back()->withErrors("اعتبار شما جهت خرید کافی نمی باشد.");
        }

        $store = $this->updatePrice($request, $store);

        $tax_calculation_percentage = Setting::getIntegerValue('tax_calculation_percentage');// درصد مالیات
        //وقتی کالا خریداری شد از اعتبار کم می کنیم
        $setting_credit = Setting::where('key', 'credit')->first();
        $setting_credit->integer_value = $setting_credit->integer_value - $request->price;
        $setting_credit->save();


        $client_transaction = ClientTransaction::create([
            "user_id" => Auth::user()->id,
            "amount" => $store->price_sum,
            "client_transaction_type_id" => 5, //  ماژول مجوز مشاهده بارکد بسته بندی های خوانده نشده در بارگیری
            'other_id' => $store->id,
        ]);

        $client_factor = ClientFactor::create([
            'client_factor_type_id' => $store->id == 4 ? 3 : 2,//  ماژول مجوز مشاهده بارکد بسته بندی های خوانده نشده در بارگیری
            'caption' => $store->caption,
            'sum_amount' => $store->price,
            'tax' => $store->price_sum * ($tax_calculation_percentage / 100),
            'total_amount' => $request->price,
        ]);

        $client_transaction->client_factor_id = $client_factor->id;
        $client_transaction->save();

        $store->client_transaction_id = $client_transaction->id;
        $store->client_factor_id = $client_factor->id;
        $store->status_id = 4500002;//خریداری شده در انتظار انجام تنظیمات

        unset($store->price_sum); // قیمت مجموع که نیاز نیست ذخیره شود.

        if ($store->id == 4) {
            $store->status_id = 4500001; // باز هم می تواند بخرد

            $support_end_date = Setting::getStringValue("support_end_date");
            $support_end_date = Carbon::parse($support_end_date);
            $support_end_date->addMonths($request->months_number + 0);
            $setting_value = Setting::where("key", "support_end_date")->first();
            $setting_value->string_value = $support_end_date->format("Y/m/d");
            $setting_value->save();
            Script1016Controller::UpdateSupportStatus();


        }
        $store->save();


        return redirect()->route($this->route_path . "index")->with(["success" => " خرید شما با موفقیت ثبت شد. " . $request->price . "ریال از اعتبار شما کسر شد. برای دریافت فاکتور به منوی اعتبار حساب/لیست فاکتورها مراجعه نمایید."]);
    }

    public function updatePrice($request, Store $store)
    {
        $store->price_sum = $store->price;
        switch ($store->id) {
            case 4: // پشتیبانی
                $store->price = $store->price + 0;
                $store->price_sum = ($store->price + 0) * $request->months_number + 0;
                break;
        }
        return $store;
    }
}
