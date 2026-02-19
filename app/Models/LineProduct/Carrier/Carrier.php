<?php

namespace App\Models\LineProduct\Carrier;

use App\Models\Customer\CustomerType;
use App\Models\Utility\Status;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Carrier extends Model
{
    use HasFactory;
    use Loggable;

    protected $fillable = ["code", "carrier_type_id", "status_id"];

    public static function firstOrCreate($carrier_code, $carrier_type_id, $default_status, $product_id,$check_for_define_new_carrier=true)
    {

        if (!is_numeric($carrier_code) || floor($carrier_code) != $carrier_code) {
            return [
                "result" => false,
                "message" => "کد حامل " . $carrier_code . " نامعتبر است کد باید از نوع عدد صحیح باشد.",
            ];
        }
        $carrier = Carrier::where([
            "code" => $carrier_code,
            "carrier_type_id" => $carrier_type_id
        ])->first();

        // اگر نوع فاقد حامل باشد
        if ($carrier_type_id == 9900) {
            $carrier = Carrier::create(
                [
                    "code" => $carrier_code,
                    "carrier_type_id" => $carrier_type_id,
                    "status_id" => $default_status
                ]
            );
            $carrier->log($default_status, 5320101);
        }

        if (isset($carrier) && $carrier->status_id != $default_status) {
            $log_message = "<br/>" . "آخرین وضعیت حامل: " . $carrier->status->caption;
            $log_message .= $carrier->log_message != null ? " - " . $carrier->log_message : "";
            $log_message .= "<br/>" . " کالا(ها)ی موجود در حامل: ";
            foreach ($carrier->product as $item_product) {
                $log_message .= $item_product->product->fullCaption() . " - ";
            }

            return [
                "result" => false,
                "message" => "وضعیت حامل انتخاب شده، " . Status::find($default_status)->caption . " نمی باشد، لطفا حامل دیگری انتخاب کنید." . $log_message,
                "carrier" => $carrier
            ];
        }
        if (!isset($carrier)) {
            $carrier_type = CarrierType::find($carrier_type_id);
            if (!$carrier_type) {
                return [
                    "result" => false,
                    "message" => "نوع حامل در سامانه تعریف نشده است." . $carrier_type_id,
                    "warning" => "نوع حامل با کد $carrier_type_id در سامانه تعریف نشده است."  ,
                ];
            }
            if (!$carrier_type->system_can_define_new_carrier && $check_for_define_new_carrier) {
                return [
                    "result" => false,
                    "message" => "حامل با شماره " . $carrier_code . " در سامانه تعریف نشده است، لطفا با واحد اطلاعات پایه تماس بگیرید.",
                    "warning" => "حامل با شماره " . $carrier_code . " در سامانه تعریف نشده است، لطفا با واحد اطلاعات پایه تماس بگیرید.",
                ];
            }

            $carrier = Carrier::create(
                [
                    "code" => $carrier_code,
                    "carrier_type_id" => $carrier_type_id,
                    "status_id" => $default_status
                ]
            );
        }
        if (isset($product_id)) {

            CarrierProduct::create([
                "product_id" => $product_id,
                "carrier_id" => $carrier->id
            ]);
        }

        if(!$carrier){
            return [
                "result" => false,
                "error" =>"حامل با شماره $carrier_code  در سامانه تعریف نشده است، لطفا شماره حامل را به درست وارد نمایید."
            ];
        }

        return [
            "result" => true,
            "carrier" => $carrier
        ];
    }

    public function addProduct($product_id)
    {

        return CarrierProduct::firstOrCreate([
            "carrier_id" => $this->id,
            "product_id" => $product_id
        ]);
        $this->log($this->status_id, 5320101, $product_id);
    }

    public function firstProductId()
    {

        $cp = CarrierProduct::where("carrier_id", $this->id)->first();

        return $cp ? $cp->product_id : -1;
    }

    public function getCaption()
    {
        return ($this->code) . " (" . ($this->carrier_type->caption ?? "***") . ")";
    }

    public function SetEmpty($user_id = null)
    {
        CarrierProduct::where([
            "carrier_id" => $this->id,
        ])->delete();
        $this->status_id = 5320001;// خالی
        $this->log_message = null;
        $this->save();
        $this->log($this->status_id, 5320109, null, null, null, $user_id);
    }

    public static function StaticSetEmpty($carrier, $user_id = null)
    {
        if ($carrier && $carrier->status_id!= 5320001) {
            $carrier->SetEmpty($user_id);
        }
    }

    public function SetStatus($status_id, $log_message = null, $event_id = null, $product_id = null, $machine_id = null, $contractor_id = null, $user_id = null,$packing_form_id=null)
    {
        if ($status_id == 5320001) {
            $this->SetEmpty($user_id);
        }
        $this->status_id = $status_id;
        if ($log_message != null) {
            $this->log_message = $log_message;
        }

        $this->save();
        $this->log($status_id, $event_id, $product_id, $machine_id, $contractor_id, $user_id,$packing_form_id);
    }

    public function log($status_id, $event_id, $product_id = null, $machine_id = null, $contractor_id = null, $user_id = null,$packing_form_id=null)
    {
        CarrierLog::create([
            "carrier_id" => $this->id,
            "status_id" => $status_id,
            "product_id" => $product_id,
            "machine_id" => $machine_id,
            "contractor_id" => $contractor_id,
            "packing_form_id" => $packing_form_id,
            "user_id" => ($user_id ?? Auth::user()->id),
            "event_id" => $event_id
        ]);
    }

    public function product()
    {
        return $this->hasMany(CarrierProduct::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function carrier_type()
    {
        return $this->belongsTo(CarrierType::class);
    }

}
