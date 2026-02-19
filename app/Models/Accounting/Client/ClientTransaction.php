<?php

namespace App\Models\Accounting\Client;

use App\Models\Utility\Notification\SMSMessageResult;
use App\Models\Utility\Script\Script;
use App\Models\Utility\Setting;
use App\Models\Worker;
use App\Notifications\SMSNotification;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ClientTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id",
        "amount",
        "client_transaction_type_id",
        "other_id",
        "client_factor_id",
    ];

    public function worker()
    {
        return $this->belongsTo(Worker::class, 'user_id');
    }

    public function client_transaction_type()
    {
        return $this->belongsTo(ClientTransactionType::class, 'client_transaction_type_id');
    }

    public function get_created_at()
    {

        return jdate(Carbon::parse($this->created_at)->timestamp)->format('H:i Y/m/d ');
    }

    public function get_created_at_without_time()
    {

        return jdate(Carbon::parse($this->created_at)->timestamp)->format(' Y/m/d ');
    }

    public function get_track_id()
    {

        if ($this->client_transaction_type_id != 1) {
            return null;
        }
        return $this->client_payment->track_id ?? "";
    }

    public function get_payment_id_in_deyaco()
    {

        if ($this->client_transaction_type_id != 1) {
            return null;
        }
        return $this->client_payment->payment_id_in_deyaco ?? "";
    }

    public function get_order_id_in_deyaco()
    {

        if ($this->client_transaction_type_id != 1) {
            return null;
        }
        return $this->client_payment->order_id_in_deyaco ?? "";
    }

    public function client_payment()
    {
        return $this->belongsTo(ClientPayment::class, "other_id");
    }

    public static function UpdateCredit()// محاسه اعتبار حساب
    {
        $client_transaction_1 = ClientTransaction::whereIn('client_transaction_type_id', [1])->sum('amount');
        $client_transaction_2 = ClientTransaction::whereIn('client_transaction_type_id', [2,3,4,5,6,7])->sum('amount');
        $client_transaction_3 = ClientTransaction::sum('amount');

        if($client_transaction_1+$client_transaction_2!=$client_transaction_3){
         //   Script::SendSmd(Script::find(21),"0"," جدول تراکنش های مالی سامانه بالانس نیست، محاسبه اعتبار حساب با خطا مواجه شده است.");
        }

        // هزینه پیامک هایی که هنوز محاسبه نشده است.
        $new_sms_cost = self::CalculateSmsCost(0, true);

        $total_credit = $client_transaction_1 - $client_transaction_2 - $new_sms_cost;

        $setting = Setting::where("key", "credit")->first();
        $setting->integer_value = $total_credit;
        $setting->save();

    }

    public static function CalculateSmsCost($user_id = 2, $only_cost_with_coefficient = false)
    {    //این تابع برای حساب کردن هزینه پیامک با ضریب می باشد

        // گرفتن مقادیر a و b از تنظیمات
        $a_in_ax_b_of_sms_cost = Setting::getDoubleValue("a_in_ax_b_of_sms_cost");
        $b_in_ax_b_of_sms_cost = Setting::getIntegerValue("b_in_ax_b_of_sms_cost");

        if ($only_cost_with_coefficient) {
            // محاسبه هزینه پیامک هایی که فاکتور نشده است.

            $sms_not_calculated = SMSMessageResult::where('client_transaction_id', null)->
            selectRaw("sum($a_in_ax_b_of_sms_cost * cost + $b_in_ax_b_of_sms_cost) as sum_new")->
            first();

            return $sms_not_calculated->sum_new;
        }

        $sms_message_results = SMSMessageResult::where('client_transaction_id', null)->get();

        if ($sms_message_results->isEmpty()) {
            return;
        }

        $sum_cost_with_coefficient = 0;
        $client_transaction = ClientTransaction::create([
            "user_id" => $user_id,
            "amount" => 0,
            "client_transaction_type_id" => 2, // هزینه پیامک
        ]);
        // محاسبه cost_with_coefficient برای هر آیتم و جمع زدن آن
        foreach ($sms_message_results as $item) {
            $cost_with_coefficient = $a_in_ax_b_of_sms_cost * $item->cost + $b_in_ax_b_of_sms_cost;
            if ($cost_with_coefficient == 0 && $item->cost != 0) {
                1 / 0;
            }
            $item->cost_with_coefficient = $cost_with_coefficient;
            $item->client_transaction_id = $client_transaction->id;
            $item->save();

            $sum_cost_with_coefficient += $cost_with_coefficient;
        }

        $client_transaction->amount = $sum_cost_with_coefficient;
        $client_transaction->save();

        return $sum_cost_with_coefficient;
    }

// تابع محاسبه فاکتور پیامک ها
    public static function SMSCalculateFactor($from_date, $to_date)
    {

        if (!Carbon::now()->greaterThan($to_date)) {
            return [
                "result" => false,
                "error" => 'شما امکان صدور فاکتور را ندارید.'
            ];
        }


        $client_transactions_sum = ClientTransaction::
        whereNull("client_factor_id")->
        where('client_transaction_type_id', 2)->
        sum("amount");

        if ($client_transactions_sum == 0) {
            return [
                "result" => false,
                "error" => 'در صدور فاکتور اشتباهی رخ داده است. لطفا با پشتیبانی تماس بگیرید.'
            ];
        }
        $tax_calculation_percentage=Setting::getIntegerValue('tax_calculation_percentage');
        $client_factor = ClientFactor::create([
            'client_factor_type_id' => 1,
            'caption' => 'شارژ سامانه پیامکی',
            'sum_amount' => 0,
        ]);

        $client_transactions = ClientTransaction::
        whereNull("client_factor_id")->
        where('client_transaction_type_id', 2)->
        get();

        foreach ($client_transactions as $client_transaction) {
            $client_transaction->client_factor_id = $client_factor->id;
            $client_transaction->save();
        }
        $deduction_of_thousand_rials = Setting::getIntegerValue('deduction_of_thousand_rials');//کسر هزار ریال
//            $client_factor->code = 1000 + $client_factor->id;به علت نداشت ای پی ای به بنرامه حسابداری فعلا کحد به صورت دستی وارد می شود.

        $sum_amount = $client_transactions_sum + $deduction_of_thousand_rials;// جمع مبلغ واحد+کسر هزار ریال
        //کسر هزار ریال برای مبلغ واحد
        if ($sum_amount % 1000 != 0) {
            $last_three_digits = $sum_amount % 1000;// گرفتن سه رقم آخر

            $deduction_of_thousand_rials += $last_three_digits; // به‌روزرسانی مقدار کسر
            $setting = Setting::where('key', 'deduction_of_thousand_rials')->first();
            $setting->integer_value = $deduction_of_thousand_rials;
            $setting->save();
            $last_sum_amount = $sum_amount - $last_three_digits; // کم کردن سه رقم اخر
        } else {
            $last_sum_amount = $sum_amount;
        }
        $client_factor->sum_amount = $last_sum_amount;//مبلغ اصلی مبلغ واحد
        $client_factor->tax = $last_sum_amount * ($tax_calculation_percentage/100);// درصد مالیات

        $total_amount = $client_factor->sum_amount + $client_factor->tax ;
        $client_factor->total_amount = $total_amount;// جمع کل مبلغ
        $client_factor->save();

        $client_transaction = ClientTransaction::create([
            "user_id" => 2,
            "amount" => $client_factor->tax,
            "client_transaction_type_id" => 6, // ارزش افزوده فاکتور پیامک ها
            'other_id' => $client_factor->id,
        ]);
    }


}
