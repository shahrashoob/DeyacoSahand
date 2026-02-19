<?php

namespace App\Models\Utility\SpecialLicense;

use App\Events\Form\PackingLogEvent;
use App\Events\Machine\MachineLogEvent;
use App\Events\Order\OrderLogEvent;
use App\Events\Product\ProductRequestFormLogEvent;
use App\Events\Utility\SpecialLicenseEvent;
use App\Http\Controllers\GoodsKindProcess\FabricRaw;
use App\Http\Controllers\Utility\Script\Script1023Controller;
use App\Models\Form\Packing\PackingForm;
use App\Models\HR\Employment\Employment;
use App\Models\HR\User\UserAddress;
use App\Models\HR\User\UserEntryLog;
use App\Models\LineProduct\Carrier\CarrierType;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Machine\ProductionChannel\ProductionChannel;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\LineProduct\Packing\PackingTypeLayer;
use App\Models\LineProduct\Product\ProductPackingType;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestForm;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormItem;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormPackingType;
use App\Models\Post\Post;
use App\Models\Utility\JsonDataList;
use App\Models\Utility\Notification\SMSMessage;
use App\Models\Utility\Setting;
use App\Models\Utility\Status;
use App\Models\Utility\Transport\Transport;
use App\Models\Worker;
use App\Notifications\SMSNotification;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class SpecialLicense extends Model
{
    use HasFactory;

    protected $fillable = ["special_license_type_id", "reference_id", "user_id", "status_id", "param1", "param2", "param3", "param4", "param5"];

    public function special_license_type()
    {
        return $this->belongsTo(SpecialLicenseType::class);
    }

    public function special_license_confirmation_post()
    {
        return $this->hasMany(SpecialLicenseConfirmation::class)->whereNull("committee_id");
    }

    public function special_license_confirmation_committee()
    {
        return $this->hasMany(SpecialLicenseConfirmation::class)->whereNotNull("committee_id");
    }

    public function worker()
    {
        return $this->belongsTo(Worker::class, "user_id");
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function logs()
    {
        return $this->hasMany(SpecialLicenseLog::class);
    }

    public function GetReference()
    {
        return $this->special_license_type->GetReference($this->reference_id);
    }

    public function get_create_date_and_time()
    {
        return jdate(Carbon::parse($this->created_at)->timestamp)->format('H:i Y/m/d ');
    }

    public function getCode()
    {
        if ($this->code) {
            return $this->code;
        }
        $this->code = "DCSL/" . (1000 + $this->id);
        $this->save();
        return $this->code;
    }

    public function getDescription()
    {
        $log = $this->logs()->where("event_id", 6040001)->first();
        return $log->message->text ?? "";
    }

    public static function UpdateSpecialLicense($special_license)
    {
        $special_license = SpecialLicense::find($special_license->id);
        $result = self::CheckForPriority($special_license, $special_license->current_priority_number);
        if ($special_license->status_id == 6040001) {// در انتظار تایید مجوز
            switch ($result["type"]) {
                case "reject":
                    $special_license->status_id = 6040003; // عدم تایید
                    $special_license->current_priority_number = 0;
                    $special_license->save();
                    SpecialLicense::SetLogForRefrence($special_license, "reject");
                    break;
                case "confirm":
                    // اگر برای اولویت جاری اوکی است، برای اولویت سطح بالا چک می کنیم، اگر آن هم اوکی بود، درخواست را تایید شده می کنیم.
                    $result_2 = self::CheckForPriority($special_license, $special_license->current_priority_number + 1);
                    if ($result_2["type"] == "confirm") {
                        $special_license->status_id = 6040002; //  تایید
                        $special_license->current_priority_number = 0;
                        $special_license->save();
                        $special_license->ActionAfterConfirm();
                    } else {
                        $special_license->current_priority_number += 1;
                        $special_license->save();

                        self::SendSmsToConfirmation($special_license);
                    }
                    break;
            }
        }
        return $special_license;
    }

    public static function CheckForPriority(SpecialLicense $special_license, $priority_number)
    {
        // اگر حداقل یک پست عدم تایید کرده
        $count_post_reject = $special_license->special_license_confirmation_post()->
        where("priority_number", $priority_number)->
        where("status_id", 6040203)-> //عدم تایید خبره
        count();
        if ($count_post_reject > 0) {
            return [
                "result" => true,
                "type" => "reject"
            ];
        }

        //اگر پست وجود دارد که هنوز اظهار نظر نکرده است، ادامه بده
        $count_post_waiting = $special_license->special_license_confirmation_post()->
        where("priority_number", $priority_number)->
        where("status_id", 6040201)-> // در انتظر تایید
        count();
        if ($count_post_waiting > 0) {
            return [
                "result" => true,
                "type" => "waiting" //هنوز باید خبرگان تایید کنند
            ];
        }

        // بررسی کمیته ها
        $committee_list = $special_license->special_license_confirmation_committee()->
        where("priority_number", $priority_number)->
        get();

        foreach ($committee_list as $item) {

            // به دست آوردن اینکه چند درصد کمیته باید تایید کنند تا نظر کمیته تایید شود
            $type_expert = SpecialLicenseTypeExpert::where([
                "special_license_type_id" => $special_license->special_license_type_id,
                "committee_id" => $item->committee_id
            ])->first();
            if (!$type_expert) {
                1 / 0;
            }
            $percent = $type_expert->min_percent_of_committee;

            $count_post_all = $special_license->special_license_confirmation_committee()->
            where("priority_number", $priority_number)->
            where("committee_id", $item->committee_id)->
            count();
            $count_post_confirm = $special_license->special_license_confirmation_committee()->
            where("priority_number", $priority_number)->
            where("committee_id", $item->committee_id)->
            where("status_id", 6040202)-> // تایید شده
            count();

            if ($count_post_confirm / $count_post_all < $percent / 100) {

                // اگر هنوز فردی هست اظهار نظر نکرده، منتظر باش
                $count_post_waiting = $special_license->special_license_confirmation_committee()->
                where("priority_number", $priority_number)->
                where("committee_id", $item->committee_id)->
                where("status_id", 6040201)-> // در انتظار تایید
                count();
                if ($count_post_waiting > 0) {
                    return [
                        "result" => true,
                        "type" => "waiting" //هنوز باید خبرگان تایید کنند
                    ];
                } else {
                    return [
                        "result" => true,
                        "type" => "reject"
                    ];
                }

            }

        }

        return [
            "result" => true,
            "type" => "confirm"
        ];

    }

    public static function GetLink($special_license_type_id, $reference_id, $link_text, $param1 = null, $param2 = null, $param3 = null, $param4 = null, $param5 = null)
    {
        $url_link = route("utility.special_license.panel.new_special_license.index", [$special_license_type_id, $reference_id, $param1, $param2, $param3, $param4, $param5]);
        return "<a href='$url_link'>$link_text</a>";
    }

    public function getConfirmPostExpertCaption($priority_number)
    {
        $caption = "";
        $list = $this->special_license_confirmation_post()->where("priority_number", $priority_number)->get();
        foreach ($list as $item) {
            $caption .= $item->post->caption . " ,";//. ($item->worker ? " (" . $item->worker->fullname() . ")" : "")
        }
        return $caption;
    }

    public function getConfirmCommitteeExpertCaptions($priority_number)
    {
        $caption = "";
        $list = $this->special_license_confirmation_committee()->where("priority_number", $priority_number)->get();
        $committee = [];
        foreach ($list as $item) {
            if (!isset($committee[$item->committee_id])) {
                $committee[$item->committee_id] = 1;
                $caption .= $item->committee->caption . "<br/>";
            }
            // $caption .= $item->post->caption . ($item->worker ? "(" . $item->worker->fullname() . ")" : "");
        }
        return $caption;
    }

    public function getObject1()
    {
        return $this->special_license_type->getObject1($this->param1);
    }

    public function getObject2()
    {
        return $this->special_license_type->getObject2($this->param2);
    }

    public function getObject3()
    {
        return $this->special_license_type->getObject3($this->param3, $this);
    }

    public function getObject4($var1 = null)
    {
        return $this->special_license_type->getObject4($this->param4, $var1);
    }

    public function ActionAfterConfirm()
    {
        switch ($this->special_license_type_id) {

            case 2://  پایان تولید، کمتر از مقدار کارت تولید
// چون بعد از پایان بافت باید مقدار مجوز تغییر کند.
//                $allocation = $this->GetReference();
//                $new_amount = $this->param1 / $allocation->items()->count();
//                FabricRaw\Jacquard\Machine\ChangeAllocationAmountController::ChangeAllocationAmount($allocation, $new_amount, false, "<br/>مجوز " . $this->code);
                break;
            case 3:// ویرایش تردد

                $reference = $this->GetReference();
                $start_datetime = Carbon::parse($reference->entry_datetime)->addDay(-1);
                $end_datetime = Carbon::parse($reference->exit_datetime)->addDay(1);
                if ($this->param3 == "input") {
                    $reference->entry_datetime = $this->param1;
                    $new_entry_datetime = Carbon::parse($this->param1);
                    if ($new_entry_datetime->lessThan($start_datetime)) {
                        $start_datetime = $new_entry_datetime;
                    }
                } elseif ($this->param3 == "output") {
                    $reference->exit_datetime = $this->param2;
                    $new_exit_datetime = Carbon::parse($this->param2);
                    if ($new_exit_datetime->greaterThan($end_datetime)) {
                        $end_datetime = $new_exit_datetime;
                    }
                }
                $reference->save();
                $worker = $reference->worker;
                Script1023Controller::calculateForDays($worker, $start_datetime, $end_datetime);
                break;
            case 4: // اضافه کردن بسته بندی به درخواست
                $reference = $this->GetReference();
                $product_request_form_id = $reference->id;
                $product_request_form_item_id = $this->param1;
                $product_id = $this->getObject1()->product_id;
                $packing_type_id = $this->param2;
                $list_degree = ProductRequestFormPackingType::where([
                    "product_request_form_id" => $product_request_form_id,
                    "product_request_form_item_id" => $product_request_form_item_id,
                    "product_id" => $product_id,
                ])->
                groupBy("degree_id")->
                get();
                // وقتی یک بسته بندی مجاز می خواهیم اضافه کنیم، تمامی درجه های مجاز کالا را هم اضافه می کنیم.
                foreach ($list_degree as $item) {
                    ProductRequestFormPackingType::create([
                        "product_request_form_id" => $product_request_form_id,
                        "product_request_form_item_id" => $product_request_form_item_id,
                        "product_id" => $product_id,
                        "packing_type_id" => $this->param2,
                        "degree_id" => $item->degree_id
                    ]);
                }

                // بعد از تایید مجوز، بسته بندی به لیست بسته بندی های مجاز کالا اضافه گردد.
                $add_to_product_packing = $this->getObject4();
                if ($add_to_product_packing) {
                    ProductPackingType::create(
                        ["product_id" => $product_id, "packing_type_id" => $packing_type_id]
                    );
                }
                break;
            case 5:
                $transport_item = $this->GetReference();
                $product_request_form = $transport_item->product_request_form;
                $result = SpecialLicenseType::AllowCreate($this->special_license_type, $transport_item);
                if ($result["result"]) {
                    $transport_item->status_id = 6010001; // ثبت موقت
                    $transport_item->save();
                    $message = $transport_item->code . " - " . $this->code;
                    // باز کردن بسته بندی حمل و نقل
                    event(new ProductRequestFormLogEvent($product_request_form, $message, null, 7005020));
                } else {
                    $message = $transport_item->code . " - " . $this->code . "<br/>" .
                        "بعد از تایید مجوز، امکان باز شدن بسته بندی حمل و نقل توسط سامانه امکان پذیر نیست." . "<br/>" .
                        $result["error"] . "<br/>";
                    // باز کردن بسته بندی حمل و نقل
                    event(new ProductRequestFormLogEvent($product_request_form, $message, null, 7005020));
                }
                break;
            case 6: // تکمیل اطلاعات بسته بندی
                $packing_form = $this->GetReference();
                $gross_weight = $this->param5; // وزن ناخالص واقعی
                if ($packing_form->status_id == 7007020) { // اگر در انتظار تکمیل اطلاعات بود
                    $result = FabricRaw\PackingForm\CompleteInformationController::CompleteInformation($packing_form, $gross_weight, false);
                    if ($result["result"]) {
                        $packing_form->status_id = $this->param4; // وضعیت بعدی که تایید شده
                        $packing_form->save();
                        event(new PackingLogEvent($packing_form, "7007027", null, "تکمیل اطلاعات با مجوز " . $this->code));
                    }
                }
                break;
            case 8:
                $product_request_form = $this->GetReference();
                $product_request_form_id = $product_request_form->id;
                $product_request_form_item_id = $this->param1;
                $packing_form = $this->getObject2();
                $packing_form_item = $packing_form->items()->first();
                $product_id = $packing_form_item->product_id;
                $packing_type_id = $packing_form->packing_type_id;
                $degree_id = $packing_form_item->degree_id;
                $warehouse_id = $this->param3;

                // حذف بسته بندی های قبلی
                ProductRequestFormPackingType::
                where("product_request_form_item_id", $product_request_form_item_id)->
                where("product_request_form_id", $product_request_form->id)->
                delete();

                // ایجاد بسته بندی های جدید
                ProductRequestFormPackingType::create([
                    "product_request_form_id" => $product_request_form->id,
                    "product_request_form_item_id" => $product_request_form_item_id,
                    "product_id" => $product_id,
                    "packing_type_id" => $packing_type_id,
                    "degree_id" => $degree_id
                ]);

                ProductRequestFormItem::
                where("id", $product_request_form_item_id)->
                where("product_request_form_id", $product_request_form->id)->
                update(["product_id" => $product_id]);

                $message = "تغییر در درخواست کالا با مجوز " . $this->code;

                $product_request_form->warehouse_id = $warehouse_id;
                $product_request_form->save();
                event(new ProductRequestFormLogEvent($product_request_form, $message, null, 1));

                break;
            case 9:
                $form = $this->GetReference();
                $supplier_id = $this->param1;
                $other["user_id"] = Auth::user()->id;
                $other["form_id"] = $form->id;
                $allocation = $form->allocation;

                $result = ProductRequestForm::newRequest(
                    $allocation, $supplier_id,
                    70, 1, $other,
                    Carbon::now()
                );
                if (!isset($result["product_request_form"]->id)) {
                    SMSMessage::ExceptionError(" ایجاد درخواست خروج از انبار برای مجوز" . $this->code . " ناموفق بوده است. ");
                } else {
                    $this->param5 = $result["product_request_form"]->id;
                }
                $this->save();
                break;
            case 11: // کانال رزور
                $machine = $this->GetReference();
                $production_channel = $this->getObject1();
                $production_channel_new = ProductionChannel::create([
                    "machine_id" => $machine->id,
                    "production_channel_type_id" => $production_channel->production_channel_type->id,
                    "status_id" => 3358002, // کانال رزرو
                    "max_capacity" => $production_channel->production_channel_type->max_capacity,
                    "min_capacity" => $production_channel->production_channel_type->min_capacity,
                    "priority_number" => 9999999
                ]);
                ProductionChannel::UpdatePriorityNumber($machine, $production_channel_new);
                ProductionChannel::UpdateProductionChannel($production_channel_new);
                break;
            case 12:
                //ارسال اطلاعات به ای سی
                $json_data_list = JsonDataList::where('id', $this->param1)->first();
                $json_decode_list = json_decode($json_data_list->data);

                $result_ic = CarrierType::CreateCarrierTypeInIc($json_decode_list, env("APP_NAME"), env("IC_APIKEY"));
                if (!$result_ic["result"]) {
                    $this->status_id = 6040003; // عدم تایید
                    $this->save();
                    event(new SpecialLicenseEvent($this, 6040005, null, null, null, $result_ic["error"]));
                }
                $carrier_type_exist = CarrierType::where('id', $result_ic['carrier_type']['id'])->exists();
                if ($carrier_type_exist) {
                    $this->status_id = 6040003; // عدم تایید
                    $this->save();
                    event(new SpecialLicenseEvent($this, 6040005, null, null, null, 'این نوع حامل  قبلا در سامانه  ثبت شده است.'));
                } else {
                    CarrierType::insert($result_ic['carrier_type']);
                }
                break;
            case 13:
                //ارسال اطلاعات به ای سی
                $json_data_list = JsonDataList::where('id', $this->param1)->first();
                $json_decode_list = json_decode($json_data_list->data);

                $result_ic = PackingType::CreatePackingTypeInIc($json_decode_list, env("APP_NAME"), env("IC_APIKEY"));

                if (!$result_ic["result"]) {
                    $this->status_id = 6040003; // عدم تایید
                    $this->save();
                    event(new SpecialLicenseEvent($this, 6040005, null, null, null, $result_ic["error"]));
                    return;
                }
                $packing_Type_exist = PackingType::where('id', $result_ic['packing_type']['id'])->exists();
                if ($packing_Type_exist) {
                    $this->status_id = 6040003; // عدم تایید
                    $this->save();
                    event(new SpecialLicenseEvent($this, 6040005, null, null, null, "این نوع بسته بندی قبلا در سامانه  ثبت شده است."));
                    return;
                } else {
                    if (isset($result_packing_types["packing_type"]['first_packing_type_id']) && $result_ic["packing_type"]['first_packing_type_id'] != null) {
                        $first_packing_Type_exist = PackingType::where('id', $result_ic["packing_type"]['first_packing_type_id'])->exists();
                        if (!$first_packing_Type_exist) {
                            event(new SpecialLicenseEvent(
                                $this,
                                6040005,
                                null,
                                null,
                                null,
                                "با توجه به اینکه بسته بندی با کد " . $result_ic["packing_type"]['first_packing_type_id'] . " جزء بسته بندی لایه اول بسته بندی " . $result_ic['packing_type']['id'] . " می‌باشد." . "<br/>" .
                                "لطفا ابتدا بسته بندی با کد " . $result_ic["packing_type"]['first_packing_type_id'] . " را از منظومه داده‌ای دریافت نمایید."
                            ));
                        }
                    }
                    PackingType::insert($result_ic["packing_type"]);// افزودن به سامانه
                    foreach ($result_ic["packing_type_layer"] as $item) {
                        PackingTypeLayer::insert($item);
                    }
                }
                break;
            case 14:
                $reference = $this->GetReference();
                $result = Transport::GetPackingFormWhereUnRead($reference, $this->param1, true, $this->param2);
                if (!$result["result"]) {
                    event(new SpecialLicenseEvent($this, 6040002, null, null, null, $result["error"]));
                } else {
                    event(new SpecialLicenseEvent($this, 6040002, null, null, null, implode(', ', $result["unread_packing_forms"])));
                }
                break;
            case 15:
                $order = $this->GetReference();
                $software_name = Setting::getStringValue("software_name");
                $worker = $order->customer->user ?? null;
                $template = "speciallicense15tem1";
                $token = $order->code();
                $token2 = $this->getObject3();
                $token3 = "";
                $token10 = $worker->fullname();
                $token20 = $software_name;


                Notification::send(
                    "00" . ($worker->mobile_country->area_code ?? "98") . $worker->mobile,
                    new SMSNotification($template, $token, $token2, $token3, $token10, $token20)
                );
                break;
            case 16:
                $worker = $this->GetReference();
                $user_entry_log = UserEntryLog::where("user_id", $worker->id)->whereNull("exit_datetime")->first();
                $start_datetime = $this->param1;
                $end_datetime = $this->param2;
                UserEntryLog::create([
                    "user_id" => $worker->id,
                    "user_status_id" => $worker->status_id,
                    "entry_register_user_id" => Auth::id(),
                    "exit_register_user_id" => Auth::id(),
                    "entry_datetime" => $start_datetime,
                    "exit_datetime" => $end_datetime
                ]);
                if ($user_entry_log) {
                    $user_entry_log_array = $user_entry_log->toArray();
                    $user_entry_log->delete();
                    unset($user_entry_log_array["id"]);
                    UserEntryLog::insert($user_entry_log_array);
                }
                Script1023Controller::calculateForDays($worker, $start_datetime, $end_datetime);
                break;

        }
    }

    public static function SetLogForRefrence(SpecialLicense $specialLicense, $type, $text = "")
    {
        $event_code = [
            "register" => [1 => 7005022, 2 => 21, 3 => null, 4 => 7005022, 5 => 7005022, 6 => 7007031, 7 => 7005022, 8 => 7005022, 9 => 0, 10 => 7007031, 11 => 21, 12 => 0, 13 => 0, 14 => 0, 15 => 35098, 16 => null, 17=>7007040],
            "reject" => [1 => 7005023, 2 => 22, 3 => null, 4 => 7005023, 5 => 7005023, 6 => 7007032, 7 => 7005023, 8 => 7005023, 9 => 0, 10 => 7007032, 11 => 22, 12 => 0, 13 => 0, 14 => 0, 15 => 0, 16 => null, 17=>null],
        ];
        $reference = $specialLicense->GetReference();
        $event_id = $event_code[$type][$specialLicense->special_license_type_id];
        $message = $specialLicense->code . "(" . $specialLicense->special_license_type->caption . ")" . ("<br/>" . $text);
        switch ($specialLicense->special_license_type_id) {
            case 1: //   خروج کالا از انبار بیش از حد مجاز
                event(new ProductRequestFormLogEvent($reference, $message, null, $event_id));
                break;
            case 2: // پایان تولید، کمتر از مقدار کارت تولید
                // آخرین وضعیت  قبل از تخصیص ماشین
                $machineLog = MachineLog::create();
                $machineLog->machine_event_type_id = $event_id;
                event(new MachineLogEvent($reference->machine, $machineLog, $message));
                break;
            case 3: // تردد
                break;
            case 4: // اضافه کردن بسته بندی به درخواست خروج از انبار
                event(new ProductRequestFormLogEvent($reference, $message, null, $event_id));
                break;
            case 5:// باز کردن بسته بندی حمل و نقل
                event(new ProductRequestFormLogEvent($reference, $message, null, $event_id));
                break;
            case 6: // تایید وزن واقعی بسته بندی
                event(new PackingLogEvent($reference, $event_id, null, $message));
                break;
            case 7: //  تحویل کالا کمتر از حد مجاز
                event(new ProductRequestFormLogEvent($reference, $message, null, $event_id));
                break;
            case 8: // تغییر در درخواست خروج از انبار
                event(new ProductRequestFormLogEvent($reference, $message, null, $event_id));
                break;
            case 9: // برگشت مواد کالا به تامین کننده
                break;
            case 10: // تایید وزن واقعی بسته بندی
                event(new PackingLogEvent($reference, $event_id, null, $message));
                break;
            case 11:
                // آخرین وضعیت  قبل از تخصیص ماشین
                $machineLog = MachineLog::create();
                $machineLog->machine_event_type_id = $event_id;
                event(new MachineLogEvent($reference, $machineLog, $message));
                break;
            case 12: //درخواست مجوز جدید برای تعریف نوع حامل
                break;
            case 13: //درخواست مجوز جدید برای تعریف نوع بسته بندی
                break;
            case 14: //درخواست مجوز برای مجوز مشاهده بارکد بسته بندی های خوانده نشده در بارگیری
                break;
            case 15:
                event(new OrderLogEvent($reference, $event_id, "", $message, $specialLicense->param1));
                break;
            case 16: //ثبت تردد
                break;
            case 17: //ثبت مجوز
                event(new PackingLogEvent($reference, $event_id, null, $message));
                break;
        }

//    case 1: // 	مجوز خروج کالا از انبار بیش از حد مجاز
//                return ProductRequestForm::find($reference_id);
//            case 2:
//                return Allocation::find($reference_id);
//            case 3:
//                return UserEntryLog::find($reference_id);
//            case 4:
//                return ProductRequestForm::find($reference_id);
//            case 5:
//                return TransportItem::find($reference_id);
//            case 6:
//                return PackingForm::find($reference_id);
//            case 7:// 	مجوز خروج کالا از انبار کمتر از حد مجاز
//                return ProductRequestForm::find($reference_id);
//            case 8: // تغییر در درخواست خروج از انبار
//                return ProductRequestForm::find($reference_id);
//            default:
//                1 / 0;
    }

    public static function ShowOwnerInIc($app_name, $token)
    {

        $settings = [
            'base_uri' => env('IC_URL') . "/api/",
            'headers' => [
            ],
            'query' => [
                'app_name' => $app_name,
                'token' => $token,
            ]
        ];
        $client = new \GuzzleHttp\Client($settings);
        $request = $client->request(
            'POST',
            "show_owner"
        );
        $response = $request->getBody();

        $result = json_decode($response, 1);
        return $result;
    }

    public static function CheckConnection($token)
    {
        try {
            $settings = [
                'base_uri' => env('IC_URL') . "/api/",
                'headers' => [],
                'query' => [
                    'token' => $token,
                ]
            ];
            $client = new \GuzzleHttp\Client($settings);

            $request = $client->request('POST', "check_connection");

            $response = $request->getBody();

            $result = json_decode($response, true);
            return $result;
        } catch (\Exception $e) {
            return [
                "result" => false,
                "error" => "اتصال شما به اینترنت برقرار نمی باشد."
            ];
        }
    }

    public static function SendSmsToConfirmation(SpecialLicense $specialLicense){
        $list = $specialLicense->special_license_confirmation_post()->where("priority_number", $specialLicense->current_priority_number)->
        whereNotIn("post_id",Post::InvalidPost())->
        get();
        foreach ($list as $item) {

            foreach ($item->post->worker as $confirm_worker) {

                Notification::send(
                    "00" . ($confirm_worker->mobile_country->area_code ?? "98") . $confirm_worker->mobile,
                    new SMSNotification("specialLicense_temp1", $specialLicense->code, $specialLicense->special_license_type->caption,null, $specialLicense->worker->fullname(),""));

            }
        }
    }
}
