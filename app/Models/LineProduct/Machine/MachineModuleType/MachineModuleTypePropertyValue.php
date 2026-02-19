<?php

namespace App\Models\LineProduct\Machine\MachineModuleType;

use App\Models\LineProduct\Machine\MachineType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MachineModuleTypePropertyValue extends Model
{
    use HasFactory;

    protected $table = "machine_module_type_property_value";
    protected $fillable = ["machine_type_id", "machine_module_type_id", "machine_module_type_property_id", "value", "text_value"];

    public static function getValue($property_id, $machine_type_id, $type = "value")
    {
        $item = MachineModuleTypePropertyValue::
        where("machine_module_type_property_id", $property_id)->
        where("machine_type_id", $machine_type_id)->
        first();
        if ($item) {
            return $item->$type;
        }

        return "";
    }

    public static function getValues(MachineType $machineType, $type = "value")
    {
        $count = MachineModuleTypeProperty::
        where("machine_module_type_id", $machineType->machine_module_type_id)->
        count();
        $list = MachineModuleTypePropertyValue::
        where("machine_module_type_id", $machineType->machine_module_type_id)->
        where("machine_type_id", $machineType->id)->
        get()->
        keyBy("machine_module_type_property_id");

        if ($count != count($list)) {
            return [
                "result" => false,
                "error" => "با توجه به اینکه تمامی مشخصات برای گروه ماشین " . $machineType->caption . " مشخص نشده است، امکان انجام عملیات برای ماشین مقدور نمی باشد." .
                    "<br/>" . "لطفا با پشتیبانی سامانه تماس بگیرید.".$count." - ".count($list)
            ];
        }

        $list_value = [];
        foreach ($list as $item) {
            if ($item->$type == "") {
                return [
                    "result" => false,
                    "error" => "با توجه به اینکه تمامی مشخصات مازول ماشین برای گروه ماشین " . $machineType->caption . " مشخص نشده است، امکان انجام عملیات برای ماشین مقدور نمی باشد." .
                        "<br/>" . "لطفا با پشتیبانی سامانه تماس بگیرید."
                ];
            }
            $list_value[$item->id] = $item->$type;
        }

//        if ($machineType->machine_module_type_id == 4) {
//
//            if ($list["73030011201"]->value == 1 && $list["73030011202"]->value == 1) {
//                return [
//                    "result" => false,
//                    "error" => "تنظیمات ماژول ماشین برای گروه ماشین ".$machineType->caption." به  درستی انجام نشده است. " .
//                        "<br/>" .
//                        "  اگر تنظیمات  'آیا مقدار فرم تولید با تزریق مواد اولیه تکمیل می شود؟' بله باشد، باید حتما تنظیمات
//                        'آیا در این گروه ماشین، ماژول ثبت تولید وجود دارد؟ ' خیر باشد."
//                ];
//            }
//        }

        return [
            "result" => true,
            "list" => $list
        ];

    }
}
