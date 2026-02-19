<?php

namespace App\Models\Utility\SpecialLicense;

use App\Models\Contractor\Contractor;
use App\Models\Form\Form;
use App\Models\Form\FormItem;
use App\Models\Form\Packing\PackingForm;
use App\Models\LineProduct\Carrier\CarrierGroup;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\ProductionChannel\ProductionChannel;
use App\Models\LineProduct\Packing\DischargeType;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestForm;
use App\Models\HR\User\UserEntryLog;
use App\Models\Order\Order;
use App\Models\Production\Production;
use App\Models\Production\ProductionChannelType;
use App\Models\Supplier\Supplier;
use App\Models\Utility\JsonDataList;
use App\Models\Utility\Setting;
use App\Models\Utility\Status;
use App\Models\Utility\Transport\Transport;
use App\Models\Utility\Transport\TransportItem;
use App\Models\Utility\Unit;
use App\Models\Warehouse\Warehouse;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecialLicenseType extends Model
{
    use HasFactory;

    protected $fillable = [
        'caption',
        'code',
        'description',
        'back_route',
        'active_status_id',
        'sms_status_id',
    ];
    protected $table = 'special_license_types';

    public function special_license_type_expert()
    {
        return $this->hasMany(SpecialLicenseTypeExpert::class);
    }

    public function active_status()
    {
        return $this->belongsTo(Status::class, 'active_status_id');
    }


    public function sms_status()
    {
        return $this->belongsTo(Status::class, 'sms_status_id');
    }

    public function special_license_type_expert_post()
    {
        return $this->hasMany(SpecialLicenseTypeExpert::class)->whereNotNull("post_id");
    }

    public function special_license_type_expert_floating_post()
    {
        return $this->hasMany(SpecialLicenseTypeExpert::class)->whereNotNull("floating_post_type_id");
    }

    public static function GetSpecialLicenseTypeExpertFloatingPost(SpecialLicenseType $special_license_type)
    {
        return SpecialLicenseTypeExpert::where("special_license_type_id", $special_license_type->id)->
        whereNotNull("floating_post_type_id")->get();
    }

    public function special_license_type_expert_committee()
    {
        return $this->hasMany(SpecialLicenseTypeExpert::class)->whereNotNull("committee_id");
    }

    public function getPostCaptions($priority_number = null)
    {
        $caption = "";
        $list = $this->special_license_type_expert_post()->
        when($priority_number, function ($query) use ($priority_number) {
            return $query->where("priority_number", $priority_number);
        })->
        get();
        foreach ($list as $item) {
            $caption .= $item->post->caption . ",";
        }
        return $caption;
    }

    public function getCommitteeCaptions($priority_number)
    {
        $caption = "";
        $list = $this->special_license_type_expert_committee()->
        when($priority_number, function ($query) use ($priority_number) {
            return $query->where("priority_number", $priority_number);
        })->
        get();
        foreach ($list as $item) {
            $caption .= $item->committee->caption . ",";
        }
        return $caption;
    }

    public function getFloatingPostTypeCaptions($priority_number = null)
    {
        $caption = "";
        $list = $this->special_license_type_expert_floating_post()->
        when($priority_number, function ($query) use ($priority_number) {
            return $query->where("priority_number", $priority_number);
        })->
        get();
        foreach ($list as $item) {
            $caption .= $item->floating_post_type->caption . ",";
        }
        return $caption;
    }

    public function GetReference($reference_id)
    {
        switch ($this->id) {
            case 1: // 	مجوز خروج کالا از انبار بیش از حد مجاز
                return ProductRequestForm::find($reference_id);
            case 2:
                return Allocation::find($reference_id);
            case 3:
                return UserEntryLog::find($reference_id);
            case 4:
                return ProductRequestForm::find($reference_id);
            case 5:
                return TransportItem::find($reference_id);
            case 6:
                return PackingForm::find($reference_id);
            case 7:// 	مجوز خروج کالا از انبار کمتر از حد مجاز
                return ProductRequestForm::find($reference_id);
            case 8: // تغییر در درخواست خروج از انبار
                return ProductRequestForm::find($reference_id);
            case 9: // مجوز برگشت کالا به تامین کننده
                return Form::find($reference_id);
            case 10: // مجوز جمع شدگی
                return PackingForm::find($reference_id);
            case 11: // مجوز ایجاد کانال رزور
                return Machine::find($reference_id);
            case 12: // درخواست مجوز برای تعریف نوع حامل جدید
                return true;//با توجه به اینکه مرجع ندارد.
            case 13: // درخواست مجوز برای تعریف نوع بسته بندی جدید
                return true;//با توجه به اینکه مرجع ندارد.
            case 14: //مجوز مشاهده بارکد بسته بندی های خوانده نشده در بارگیری
                return Transport::find($reference_id);
            case 15: // مجوز مرجوعی
                return Order::find($reference_id);
            case 16: // مجوز تردد
                return Worker::find($reference_id);
            case 17: // مجوز تخصیص سریع پیمانکاران
                return PackingForm::find($reference_id);
            default:
                1 / 0;

        }
    }

    public function getObject1($param1)
    {
        switch ($this->id) {
            case 1:
                return Product::find($param1);
            case 3:
                return jdate(Carbon::parse($param1)->timestamp)->format("Y/m/d - %A - H:i:s ");
            case 4:
            case 8:
                return Product\ProductRequest\ProductRequestFormItem::find($param1);
            case 7:
                return Product::find($param1);
            case 9: // مجوز برگشت کالا به تامین کننده
                return Supplier::find($param1);
            case 10:
                $json_data_list = JsonDataList::find($param1);
                if (!$json_data_list) {
                    return [];
                }
                $product_shrinkage_info = json_decode($json_data_list->data, true);
                $product_ids = [];
                foreach ($product_shrinkage_info as $item) {
                    $product_ids[] = $item["product_id"];
                }
                $product_ids[] = -1;
                $product_list = Product::whereIn("id", $product_ids)->get()->keyBy("id");
                return [
                    "product_list" => $product_list,
                    "product_shrinkage_info" => $product_shrinkage_info
                ];
            case 11:
                return ProductionChannel::find($param1);
            case 12:
                $json_data_list = JsonDataList::where('id', $param1)->first();
                if (!$json_data_list) {
                    return null;
                }
                $json_decode_list = json_decode($json_data_list->data);
                $unit = Unit::where('id', $json_decode_list->unit_id)->first();
                $carrier_group = CarrierGroup::where('id', $json_decode_list->carrier_group_id)->first();
                return [
                    "unit" => $unit,
                    "carrier_group" => $carrier_group,
                    "json_decode_list" => $json_decode_list
                ];
            case 13:
                $json_data_list = JsonDataList::where('id', $param1)->first();
                if (!$json_data_list) {
                    return null;
                }
                $json_decode_list = json_decode($json_data_list->data);
                $active_status = Status::where('id', $json_decode_list->active_status_id)->first();
                $discharge_type = DischargeType::where('id', $json_decode_list->discharge_type_id)->first();
                return [
                    "json_decode_list" => $json_decode_list,
                    'active_status' => $active_status,
                    'discharge_type' => $discharge_type,
                ];
            case 14:
                return Product::find($param1);
            case 15:
                return Form::find($param1);
            case 16:
                return jdate(Carbon::parse($param1)->timestamp)->format("Y/m/d - %A - H:i:s ");
                break;
            case 17: // مجوز تخصیص سریع پیمانکاران
                return Production::find($param1);
        }
        return null;
    }

    public function getObject2($param2)
    {
        switch ($this->id) {
            case 3:
                return jdate(Carbon::parse($param2)->timestamp)->format("Y/m/d - %A - H:i:s ");
            case 4:
                return PackingType::find($param2);
            case 8:
                return PackingForm::find($param2);
            case 14:
                return $param2;
            case 15:
                return $param2;
            case 16:
                return jdate(Carbon::parse($param2)->timestamp)->format("Y/m/d - %A - H:i:s ");
                break;
            case 17: // مجوز تخصیص سریع پیمانکاران
                return Production::find($param2);

        }
        return null;
    }

    public function getObject3($param3, $special_license = null)
    {
        switch ($this->id) {
            case 8:
                return Warehouse::find($param3);
            case 15:
                return jdate(Carbon::parse($special_license->created_at ?? null)->addDay($param3))->format('H:i Y/m/d ');
            case 17: // مجوز تخصیص سریع پیمانکاران
                return Contractor::find($param3);
        }
        return null;
    }

    public function getObject4($param4, $var1 = null)
    {
        switch ($this->id) {
            case 3:
                if (!$var1) {
                    $var1 = "Y/m/d - %A - H:i:s ";
                }
                return jdate(Carbon::parse($param4)->timestamp)->format($var1);

            case 17:

                return Contractor::find($param4);

        }
        return null;
    }

    public function getBackUrl($reference, $param1="",$param2="",$param3="",$param4="",$param5="")
    {
        switch ($this->id) {
            case 1:
                return route("wh.out.dashboard.view", $reference->id);
            case 2:
                return route("production.machine.index");
            case 3:
                return route("hr.personal.current_user");
            case 4:
                return route("wh.out.dashboard.view", $reference->id);
            case 5:
                return route("wh.out.dashboard.view", $reference->product_request_form_id);
            case 6:
                return route("fabric_raw.packing_form.index");
            case 7:
                return route("wh.out.dashboard.view", $reference->id);
            case 8:
                return route("wh.out.dashboard.view", $reference->id);
            case 9: // مجوز برگشت کالا به تامین کننده
                return route("wh.dashboard.show_form", $reference->id);
            case 10:
                return route("fabric_raw.packing_form.index");
            case 11:
                return route("production.machine.index");
            case 12://درخواست مجوز برای تعریف نوع حامل
                return route("line_product_station.carrier.carrier_type.index");
            case 13://درخواست مجوز برای تعریف نوع بسته بندی
                return route("line_product_station.packing.packing_type.index");
            case 14://درخواست مجوز برای تعریف نوع بسته بندی
                return route("utility.transport.loading.load_registration.show_transport", $reference->id);
            case 15:// مجوز مرجوعی
                return route("sales.dashboard.view_order", $reference->id);
            case 16: // مجوز ثبت تردد
                return route("hr.personal.current_user");
            case 17: // مجوز تغییر بسته بندی سریع
                return route("contractor.admin.contractor_allocation_quick.show_selected_packing_by_packing_form",[$param3,$param4]);
            default:
                1 / 0;
        }
    }

    /**
     * آیا اجازه ایجاد مجوز وجود دارد.
     * @param SpecialLicenseType $specialLicenseType
     * @param $reference
     * @return array|true[]
     */
    public static function AllowCreate(SpecialLicenseType $specialLicenseType, $reference)
    {
        switch ($specialLicenseType->id) {
            case 3:
                $entry_log = $reference;
                $specialLicense3 = SpecialLicense::where([
                        "user_id" => $entry_log->user_id,
                    ])->
                    whereIn("special_license_type_id", [3, 16])->
                    whereNotIn("status_id", [6040002, 6040003])->
                    first();
                if ($specialLicense3) {
                    return [
                        "result" => false,
                        "error" => "با توجه به اینکه مجوز شماره " . $specialLicense3->code . " در انتظار تایید می باشد، امکان ثبت درخواست وجود ندارد."
                    ];
                }
                break;
            case 5: // باز کردن عدل بندی
                $transport_item = $reference;
                $packing_form_ids =
                    $transport_item->transport_packing_list()->
                    pluck("packing_form_id")->
                    toArray();
                $packing_form_ids[] = -1;
                $error_form = "";
                // هیچ کدام از بسته بندی های داخل عدل نباید در فرم تایید شده یا در انتظار تایید باشند.
                $list = FormItem::
                join("packing_form_item", "packing_form_item.id", "packing_form_item_id")->
                join("forms", "forms.id", "form_item.form_id")->
                whereIn("packing_form_id", $packing_form_ids)->
                where("forms.status_id", "!=", 500000100)->//عدم تایید
                where("form_type_id", 0)->//برگ خروج باشد
                groupBy("form_id")->
                select("form_item.*")->
                get();

                foreach ($list as $item) {
                    $error_form .= $item->form->code . ", ";
                }
                if ($error_form != "") {
                    $error = "با توجه به اینکه عدل " . $reference->code . " در برگ  خروج " . $error_form . "  قرار دارد، امکان باز کردن عدل وجود ندارد.";
                    $error .= "<br/>" . "جهت بازکردن عدل فرم خروج آن باید عدم تایید گردد." . "<br/>";

                    return [
                        "result" => false,
                        "error" => $error
                    ];
                }


                break;
            case 6:
                $packing_form = $reference;
                if ($packing_form->status_id != 7007020) { // تکمیل اطلاعات بسته بندی
                    return [
                        "result" => false,
                        "error" => "وضعیت بسته بندی جهت ثبت مجوز تکیمل اطلاعات معتبر نمی باشد."
                    ];
                }
                break;
            case 9: // مجوز برگشت کالا به تامین کننده
                $form = $reference;
                if ($form->status_id != 500000200) {
                    return [
                        "result" => false,
                        "error" => "وضعیت فرم ورود به انبار " . $form->code . " نامعتبر است."
                    ];
                }
                if (!in_array($form->trans_kind, [100, 1])) {
                    return [
                        "result" => false,
                        "error" => "با توجه به اینکه نوع رخداد خرید خارجی/خرید داخلی نمی باشد، امکان ثبت مجوز وجود ندارد."
                    ];
                }
                if ($form->item()->count() <= 0) {
                    return [
                        "result" => false,
                        "error" => "هیچ اطلاعاتی برای آیتم های فرم ورود به انبار وجود ندارد، لطفا با پشتیبانی تماس بگیرید." . $form->item()->count()
                    ];
                }
                break;
            case 16:
                $worker = $reference;
                $specialLicense3 = SpecialLicense::where([
                        "user_id" => $worker->id,
                    ])->
                    whereIn("special_license_type_id", [3, 16])->
                    whereNotIn("status_id", [6040002, 6040003])->
                    first();
                if ($specialLicense3) {
                    return [
                        "result" => false,
                        "error" => "با توجه به اینکه مجوز شماره " . $specialLicense3->code . " در انتظار تایید می باشد، امکان ثبت درخواست وجود ندارد."
                    ];
                }
                break;

            case 17:
                break;

        }
        return [
            "result" => true
        ];
    }

}
