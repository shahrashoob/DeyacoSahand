<?php

namespace App\Models\Utility\Transport;

use App\Models\Contractor\Contractor;
use App\Models\Customer\Customer;
use App\Models\Form\FormItem;
use App\Models\Form\Packing\PackingForm;
use App\Models\Form\Packing\PackingFormItem;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormForm;
use App\Models\Order\Order;
use App\Models\Utility\Car\Car;
use App\Models\Utility\Car\CarType;
use App\Models\Utility\Status;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Transport extends Model
{
    use HasFactory;

    protected $fillable = [
        "series",
        "order_code",
        "order_id",
        "user_id",
        "status_id",
        "customer_caption",
        "car_id",
        "transport_type_id"
    ];

    public function worker()
    {
        return $this->belongsTo(Worker::class, "user_id");
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function items()
    {
        return $this->hasMany(TransportItem::class);
    }

    public function transport_forms()
    {
        return $this->hasMany(TransportForm::class);
    }

    public function car()
    {
        return $this->belongsTo(Car::class);
    }

    public function create_datetime()
    {
        return jdate(Carbon::parse($this->created_at)->timestamp)->format('H:i Y/m/d ');

    }

    public function getCode()
    {
        if ($this->code == null) {
            $this->code = "DCBL/" . (1000 + $this->id);// Bill of Loading
            $this->save();
        }
        if ($this->random == null) {
            $this->getRandom();
        }

        return $this->code;
    }

    public function codeNumber()
    {
        return (1000 + $this->id);
    }

    public function getRandom()
    {
        if ($this->random == null) {
            $this->random = Str::random(8);
            $this->save();
        }

        return $this->random;
    }

    public function created_date_time()
    {
        return jdate(Carbon::parse($this->created_at)->timestamp)->format('H:i Y/m/d ');
    }

    public function getAmount($type)
    {
        $packing_form_ids = TransportPackingForm::where("transport_id", $this->id)->pluck("packing_form_id")->toArray();

        return PackingFormItem::whereIn("packing_form_id", $packing_form_ids)->sum($type);
    }

    public static function getWeight(Transport $transport, $group_by = "none")
    {
        switch ($group_by) {
            case "none":
                // همه بسته بندی هایی که در بار وجود دارد.
                $packing_form_ids = FormItem::
                join("transport_form", "transport_form.form_id", "form_item.form_id")->
                join("packing_form_item", "packing_form_item.id", "packing_form_item_id")->
                join("packing_forms", "packing_forms.id", "packing_form_id")->
                where("transport_id", $transport->id)->
                distinct("packing_form_id")->
                pluck("packing_forms.id")->
                toArray();

                $wh = PackingForm::whereIn("id", $packing_form_ids)->selectRaw("round(sum(weight),4) as weight,round(sum(gross_weight),4) as gross_weight")->first();

                return ["result" => true, "weight" => $wh->weight, "gross_weight" => $wh->gross_weight];
                break;
            case "product":
                // همه بسته بندی هایی که در بار وجود دارد.
                $packing_form_ids = FormItem::
                join("transport_form", "transport_form.form_id", "form_item.form_id")->
                join("packing_form_item", "packing_form_item.id", "packing_form_item_id")->
                join("packing_forms", "packing_forms.id", "packing_form_id")->
                where("transport_id", $transport->id)->
                distinct("packing_form_id")->
                pluck("packing_forms.id")->
                toArray();

                // gross_weight
                $product_weight = PackingForm::
                join("packing_form_item", "packing_forms.id", "packing_form_id")->
                whereIn("packing_forms.id", $packing_form_ids)->
                selectRaw("round(sum((weight)),4) as weight,round(sum((gross_weight)),4) as gross_weight,product_id,count(distinct(packing_forms.id)) as packing_count")->
                groupBy("product_id")->
                get()->
                keyBy("product_id");

                $wh = PackingForm::whereIn("id", $packing_form_ids)->selectRaw("round(sum(weight),4) as weight,round(sum(gross_weight),4) as gross_weight")->first();

                return ["result" => true, "product_weight" => $product_weight, "gross_weight" => $wh->gross_weight];
                break;
        }

    }

    public static function getPackingFromCount(Transport $transport, $forms_ids)
    {
        return FormItem::join("packing_form_item", "packing_form_item.id", "packing_form_item_id")->
        whereIn("form_id", $forms_ids)->
        groupBy("packing_form_id")->
        get()->count();
    }


    /**
     * @return array|\Illuminate\Http\RedirectResponse|void
     * برای بار همه فرم ها باهم خروج می خورند
     */
    public static function CallApiAddInputFormForTransport(Transport $transport, $first_form)
    {

        // از آخرین فرم خروج دریافت می کنیم.
        $product_request_form_form = ProductRequestFormForm::join("product_request_forms", "product_request_forms.id", "product_request_form_id")->
        where("product_request_form_form.form_id", $first_form->id)->
        select("product_request_form_form.id", "product_request_form_id")->
        first();

        switch ($product_request_form_form->product_request_form->applicant_type_id) {
            case 20: //پیمانکاران
                // ارسال درخواست برای پیمانکارانی که سامانه دارند.
                // چون بارگیری است، همه فرم های خروج با هم ثبت می شوند.
                $contractor = Contractor::find($product_request_form_form->product_request_form->applicant_id);
                if (!$contractor) {
                    return [
                        "result" => false,
                        "error" => "شناسه پیمانکار در درخواست نامعتبر است."
                    ];
                }
                $result_call_api_input = Contractor::CallApiAddInputFormForContractor($contractor, $product_request_form_form->product_request_form, null, $transport);
                if (!$result_call_api_input["result"]) {
                    return $result_call_api_input;
                }
                break;
            case 30: // مشتری
                // ارسال درخواست برای پیمانکارانی که سامانه دارند.
                // چون بارگیری است، همه فرم های خروج با هم ثبت می شوند.
                $customer = Customer::find($product_request_form_form->product_request_form->applicant_id);
                if (!$customer) {
                    return [
                        "result" => false,
                        "error" => "شناسه مشتری در درخواست نامعتبر است."
                    ];
                }
                $result_call_api_input = Customer::CallApiAddInputFormForCustomer($customer, $product_request_form_form->product_request_form, null, $transport);
                if (!$result_call_api_input["result"]) {
                    return $result_call_api_input;
                }
                break;

        }


        return [
            "result" => true,
            "error" => "پیمانکار/مشتری نیست و نیاز به ثبت ندارد"
        ];

    }

    /**
     * @param Transport $transport
     * @param $product_id
     * @return array
     * دری یک بار، لیست بسته بندی هایی که خوانده نشده از یک کالای مشخص را برمی گرداند.
     */
    public static function GetPackingFormWhereUnRead(Transport $transport, $product_id, $allow_change_status,$max_count)
    {
        if ($transport->packing_form_data == "") {
            return [
                "result" => false,
                "error" => "اطلاعات بسته بندی ها یافت نشد."
            ];
        }
        $unread_packing_forms = [];
        $packing_form_codes = json_decode($transport->packing_form_data, 1);
        $count=0;
        foreach ($packing_form_codes as $packing_form_code => $value) {

            if ($value == 1) { // خوانده شده است.
                continue;
            }

            $code_exists = PackingForm::join("packing_form_item", "packing_forms.id", "packing_form_id")->
            where([
                "packing_forms.id" => $packing_form_code,
                "product_id" => $product_id
            ])->
            select("packing_forms.code")->
            first();

            if ($code_exists && $count<$max_count) {
                $unread_packing_forms[] = $code_exists->code;
                $count++;
                // وضعیت بسته بندی را خوانده شده می گذاریم.
                if ($allow_change_status) {
                    $packing_form_codes[$packing_form_code] = 1;
                }
            }
        }
        if (count($unread_packing_forms) == 0) {
            return [
                "result" => false,
                "error" => "همه بسته بندی های کالا با شناسه $product_id خوانده شده است. "
            ];
        }
        if ($allow_change_status) {

            $transport->packing_form_data = json_encode($packing_form_codes);
            $transport->save();
        }
        return [
            "result" => true,
            "unread_packing_forms" => $unread_packing_forms
        ];

    }
}
