<?php

namespace App\Http\Controllers\Accounting\Client;

use App\Events\Order\OrderLogEvent;
use App\Http\Controllers\Controller;
use App\Models\Accounting\Client;
use App\Models\HR\User\UserAcademicDegree;
use App\Models\Utility\Setting;
use App\Models\Utility\SpecialLicense\SpecialLicense;
use App\Models\Worker;
use Shetabit\Payment\Facade\Payment;
use Illuminate\Support\Facades\Auth;
use Shetabit\Multipay\Exceptions\InvalidPaymentException;
use Illuminate\Http\Request;
use Shetabit\Multipay\Invoice;

class BuyController extends Controller
{
    public $route_path = "accounting.client.buy.";
    public $view_path = "accounting.client.buy.";

    public function index()// در اینجا مقدار شارژ را مشخد می کند
    {
        $min_of_charge = Setting::getIntegerValue("min_of_charge");
        if (!$min_of_charge) {
            return back()->withErrors("حداقل مبلغ شارژ تعیین نشده لطفا با پشتبانی تماس بگیرید.");
        }

        $max_of_charge = Setting::getIntegerValue("max_of_charge");
        if (!$max_of_charge) {
            $max_of_charge = 1000000000;
        }

        $message = "";
        $min_of_charge_for_payment = Setting::getIntegerValue("min_of_charge_for_payment");
        $credit = Setting::getIntegerValue("credit");
        if ($credit <= $min_of_charge_for_payment) {
            $message = "اعتبار حساب  کافی نمی باشد، لطفا  اعتبار حساب را افزایش دهید." . "<br/>";
        }

        return view($this->view_path . "index", compact("min_of_charge", "max_of_charge", "message"));
    }

    public function store(Request $request)
    {
        $amount = $request->amount;
        $min_of_charge = Setting::getIntegerValue("min_of_charge");
        if ($amount < $min_of_charge) {
            return back()->withErrors("لطفا مبلغی بیش از " . $min_of_charge . " ریال وارد نمایید.");
        }
        session([
            "amount" => $amount
        ]);

        return redirect()->route($this->route_path . "show");
    }

    public function show()
    {
        $amount = session("amount");
        if (!$amount) {
            return redirect()->route($this->route_path . "index")->withErrors("لطفا مبلغ را وارد نمایید.");
        }

        $domain = request()->getHost();
        $port = request()->getPort();

        $static_ip = Setting::getStringValue("static_ip");
      //  return $this->extractDomain($domain, $port) == $this->extractDomain($static_ip)?1:2;
        if ($this->extractDomain($domain,$port) != $this->extractDomain($static_ip)) {
            return back()->withErrors("⚠️ توجه: شما از آدرس غیرمجاز وارد شده‌اید، برای دسترسی صحیح و ایمن، لطفاً از دامنه زیر استفاده کنید:" . "<br/>$static_ip");
        }

        return view($this->view_path . "show", compact("amount"));

    }

    public function confirm()
    {
        $domain = request()->getHost();
        $port = request()->getPort();
        $static_ip = Setting::getStringValue("static_ip");

        if ($this->extractDomain($domain,$port) != $this->extractDomain($static_ip)) {
            return back()->withErrors("برای اتصال به درگاه پرداخت از آدرس زیر اقدام نمایید." . $static_ip . "<br/><a href='$static_ip'>$static_ip</a>");
        }
        $amount = session("amount");
        if (!$amount) {
            return redirect()->route($this->route_path . "index")->withErrors("لطفا مبلغ را وارد نمایید.");
        }
        $trans_kind_id = session("trans_kind_id");
        if (!$trans_kind_id) {
            $trans_kind_id = 1;// شارژ حساب
        }

        $payment = Client\ClientPayment::create([
            "user_id" => Auth::id(),
            "client_transaction_type_id" => $trans_kind_id,
            "amount" => $amount,
            "status_id" => 3600001//تایید نشده
        ]);

        return self::confirmPayment($payment); // اتصال به درگاه بانک

    }

    public static function confirmPayment(Client\ClientPayment $payment)
    {
        $result = Client\ClientPayment::CheckConnectionInDeyaco();// در این ای پی ای چک می شود ایا اتصال به اینترنت برقرار است یا خیر
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }
        $result_deyaco = Client\ClientPayment::CreatePaymentInDeyaco($payment->amount, env('APP_NAME'));//این ای پی ای به صفحه دیاکو  برای پرداخت می رود
        if (!$result_deyaco["result"]) {
            return back()->withErrors($result_deyaco["error"]);
        }
        $payment->payment_id_in_deyaco = $result_deyaco["payment_id"];
        $payment->random = $result_deyaco["random"];
        $payment->save();
        return redirect()->to("http://deyaco.ir/accounting/client/buy/confirm_payment/{$payment->payment_id_in_deyaco}");

    }

    public function verify_payment(Request $request)
    {

        $client_payment = Client\ClientPayment::where([
            'payment_id_in_deyaco' => $request->payment_id,
            'random' => $request->random,
        ])->first();
        if (!$client_payment) {
            $is_verify = false;
            $message = "پرداخت شما به درستی تکمیل نشده است لطفا با پشتیبانی تماس بگیرید.";
            return view("accounting.client.buy.verify_payment", compact("client_payment", "is_verify", "message"));
        }
        $client_payment_track_exist = Client\ClientPayment::where([
            'track_id' => $request->track_id,
        ])->first();
        if ($client_payment_track_exist) {
            $is_verify = false;
            $message = 'کد رهگیری تکراری می باشد. لطفا با پشتیبانی تماس بگیرید.';
            return view("accounting.client.buy.verify_payment", compact("client_payment", "is_verify", "message"));

        }
        $client_transaction = Client\ClientTransaction::create([
            "user_id" => $client_payment->user_id,
            "amount" => $client_payment->amount,
            "client_transaction_type_id" => $client_payment->client_transaction_type_id,
            "other_id" => $client_payment->id,
        ]);
        $client_payment->track_id = $request->track_id;
        $client_payment->order_id_in_deyaco = $request->order_id;
        $client_payment->client_transaction_id = $client_transaction->id;
        $client_payment->status_id = ($request->status == 1) ? 3600004 : 3600005;
        $client_payment->save();

        if ($client_payment->client_transaction_type_id == 5) {
            if ($client_payment->order->status_id == 35095) { // در انتظار پرداخت

                $client_payment->order->status_id = 35096; // پرداخت انجام شده
                $client_payment->order->save();

                $message = "شماره تراکنش:" . $request->track_id;
                event(new OrderLogEvent($client_payment->order, 304997, $message, $message, null, $client_payment->order->customer->user_id)); // پرداخت صورت حساب


            }
        }

        self::UpdateCredit(); // بروز رسانی اعتبار حساب

        $is_verify = true;
        return view("accounting.client.buy.verify_payment", compact("client_payment", "is_verify"));

    }


    public static function UpdateCredit()// محاسه اعتبار حساب
    {
        $client_transaction_1 = Client\ClientTransaction::where('client_transaction_type_id', 1)->sum('amount');
        $client_transaction_2 = Client\ClientTransaction::where('client_transaction_type_id', 2)->sum('amount');


        $total_credit = ($client_transaction_1 - $client_transaction_2);
        $setting = Setting::where("key", "credit")->first();
        $setting->integer_value = $total_credit;
        $setting->save();

    }

    // تابعی برای استخراج فقط دامنه
    function extractDomain($value, $port = null)
    {
        // اطمینان از اینکه URL معتبر است
        if (!preg_match('#^https?://#', $value)) {
            $value = 'http://' . $value; // اضافه کردن پروتکل اگر نبود
        }

        $host = parse_url($value, PHP_URL_HOST);


        if (!$port) {
            $port = parse_url($value, PHP_URL_PORT);
        }
        if($port == 80 || $port == 443) {
            $port = "";
        }
        return $host . ($port == "" ? "" : ":" . $port);
    }

}
