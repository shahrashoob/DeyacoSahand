<?php

namespace App\Models\Contractor;

use App\Models\Accounting\CostCenter;
use App\Models\HR\Company\Company;
use App\Models\Customer\CustomerAddress;
use App\Models\File\File;
use App\Models\HR\Personal\PersonalType;
use App\Models\LineProduct\LineProductStation;
use App\Models\Warehouse\Warehouse;
use App\Models\LineProduct\Machine\CurrentMachineInput;
use App\Models\LineProduct\Machine\MachineAllocation;
use App\Models\LineProduct\Product\BOM\BOM;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestForm;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormForm;
use App\Models\Post\Post;
use App\Models\Production\ProductionFormItem;
use App\Models\SoftwareSystem\SoftwareSystem;
use App\Models\Utility\Address\Address;
use App\Models\Utility\Address\Province;
use App\Models\Utility\Algorithm\Algorithm;
use App\Models\Utility\Setting;
use App\Models\Utility\Transport\Transport;
use App\Models\Worker;
use Carbon\Carbon;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Contractor\MachineAllocationActualConsumption;


class Contractor extends Model
{
    use HasFactory;
    use Loggable;


    protected $fillable = [
        "code",
        "caption",
        "post_id",
        "register_code",
        "active_status_id",
        "cost_center_id",
        "personal_type_id",
        "firstname",
        "lastname",
        "national_code",
        "minimum_time_required_to_start_coordination",
        "start_of_work_time",
        "end_of_work_time",
        'software_system_id',
        'api_url',
        'api_username',
        'api_password',
        'api_key',
        "start_date_of_contract",
        'end_date_of_contract',
        'image_id',
        'company_id_in_ic_system',
        "exit_form_require_draft_permission",
        "exit_form_require_draft_permission_post_id",

        "exit_form_require_permission",
        "exit_form_require_permission_post_id",

        "exit_form_guarding_require_permission",
        "exit_form_guarding_require_permission_post_id",

        "exit_form_loading_require_permission",

        "checking_form_not_delivered_at_register_production",

        "get_packing_form_details",

        "input_form_guarding_require_permission",
        "input_form_loading_require",
        "input_form_quality_control_permission",

        "checking_carrier_at_delivery_of_product",
        "it_is_coordination_for_sending",
        "show_packing_forms_in_warehouse",
        'company_id',
        'detailed_code',
        "sent_address_place_type_of_transport",
        "duration_of_default_of_product",
        "is_order_registration_date_chosen_by_contractor",

        "barcode_algorithm_id"

    ];

    public function warehouse()
    {
    return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public static function getWarehouse($contractor)
    {
    $contractor = is_object($contractor) ? $contractor : self::find($contractor);
    
    if (!$contractor) {
        return null;
    }
    $lastId = Warehouse::latest()->first()->id;
    $newId = $lastId + 1;
    if (!$contractor->warehouse_id) {
        $warehouse = Warehouse::create([
            'code'=>'DCC'.$newId,
            'caption'=>'انبارک'.$contractor->caption,
            'warehouse_type_id' => 6,
            'belonging_to_id'=> $contractor->id,
        ]);
        
        $contractor->warehouse_id = $warehouse->id;
        $contractor->save();

        return $warehouse;
    }
    return Warehouse::find($contractor->warehouse_id);
    }

    public function image()
    {
        return $this->belongsTo(File::class, "image_id");
    }
    public function province()
    {
        return $this->belongsTo(Province::class);
    }
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function barcode_algorithm()
    {
        return $this->belongsTo(Algorithm::class,"barcode_algorithm_id");
    }
    public function worker()
    {
        return $this->belongsTo(Worker::class,"user_id");
    }
    public function personal_type()
    {
        return $this->belongsTo(PersonalType::class);
    }
    public function getDefaultAddress()
    {
        $contractor_address = ContractorAddress::where(["contractor_id" => $this->id, "is_default" => 1])->first();

        return $contractor_address->address ?? null;
    }

    public function exit_form_require_draft_permission_post() {
        return $this->belongsTo( Post::class ,'exit_form_require_draft_permission_post_id');
    }
    public function exit_form_guarding_require_permission_post() {
        return $this->belongsTo( Post::class ,'exit_form_guarding_require_permission_post_id');
    }
    public function exit_form_require_permission_post() {
        return $this->belongsTo( Post::class ,'exit_form_require_permission_post_id');
    }

    public function operations()
    {
        return $this->hasMany(ContractorOperation::class);
    }

    public function property()
    {
        return $this->hasMany(ContractorPropertyValue::class, "contractor_id");
    }

    public function software_system()
    {
        return $this->belongsTo(SoftwareSystem::class);
    }

    public function cost_center()
    {
        return $this->belongsTo(CostCenter::class);
    }

    public function contractor_allocation()
    {
        return $this->hasMany(ContractorAllocation::class, "contractor_id");

    }
    public function get_end_date_of_contract()
    {
        return jdate(Carbon::parse($this->end_date_of_contract)->timestamp)->format('Y/m/d');
    }
    public function fullName()
    {
        return $this->firstname . " " . $this->lastname;

    }

    public function fullCaption()
    {
        return $this->caption . "(" . $this->fullname() . ")";

    }

    public function UpdateAddress($request, $is_default)
    {
        $address = $this->getDefaultAddress();
        if (isset($address)) {
            $address->update($request->all());
        } else {

            $address = Address::create($request->all());

            $contractor_address = new ContractorAddress();
            $contractor_address->is_default = $is_default;
            $contractor_address->contractor_id = $this->id;
            $contractor_address->address_id = $address->id;
            $contractor_address->save();
        }
    }

    public function getCode()
    {

        if ($this->code != "") {
            return $this->code;
        }
        $code_number = $this->id;
        $code = Str::of($code_number)
            ->when($code_number < 1000, function ($string) {
                return Str::of('0')->append($string);
            })
            ->when($code_number < 100, function ($string) {
                return Str::of('0')->append($string);
            })
            ->when($code_number < 10, function ($string) {
                return Str::of('0')->append($string);
            });
        $this->code = $code;
        $this->save();

        return $this->code;
    }

    public function getIC()
    {
        // تابغی همه درخواست دهنده های باید داشته باشند.
        return $this->cost_center->code;
    }

    public function get_property_value($contractor_property_id)
    {
        $property_value = ContractorPropertyValue::where(
            [
                "contractor_property_id" => $contractor_property_id,
                "contractor_id" => $this->id
            ]
        )->first();
        if ($property_value) {
            return $property_value->value;
        }

        return null;
    }

    public function nextStatusForExistForm($current_status_id)
    {

        // با توجه به اولویت های موجود برای برگ خروج و وضعیت جاری، وضعیت بعدی مشخص می شود.

        switch ($current_status_id) {
            case 0: // گرفتن اولین وضعیت فرم خروج
                if ($this->exit_form_require_draft_permission) {
                    return 500000515; // پیش نویس
                } elseif ($this->exit_form_require_permission) {
                    return 500000520; // نهایی
                } elseif ($this->exit_form_loading_require_permission) {
                    return 500000525; // بارگیری
                } elseif ($this->exit_form_guarding_require_permission) {
                    return 500000530; // نگبانی
                } else {
                    return 500000500; // پیمانکار(درخواست کننده)
                }
                break;
            case 500000515:  // پیش نویس
                if ($this->exit_form_require_permission) {
                    return 500000520; // نهایی
                } elseif ($this->exit_form_loading_require_permission) {
                    return 500000525; // بارگیری
                } elseif ($this->exit_form_guarding_require_permission) {
                    return 500000530; // نگبانی
                } else {
                    return 500000500; // پیمانکار(درخواست کننده)
                }
                break;
            case 500000520:  // تایید نهایی
                if ($this->exit_form_loading_require_permission) {
                    return 500000525; // بارگیری
                } elseif ($this->exit_form_guarding_require_permission) {
                    return 500000530; // نگبانی
                } else {
                    return 500000500; // پیمانکار(درخواست کننده)
                }
                break;
            case 500000525:  // بارگیری
                if ($this->exit_form_guarding_require_permission) {
                    return 500000530; // نگبانی
                } else {
                    return 500000500; // مشتری
                }
                break;
            case 500000530:   // نگبانی
                return 500000500; // پیمانکار(درخواست کننده)
                break;
            case 500000500:  // پیمانکار(درخواست کننده)
                return 500000200; // تایید شده
                break;

        }

        1 / 0;

    }

    public function nextStatusForInputForm($current_status_id, $has_general_item = true)
    {

        // با توجه به اولویت های موجود برای فرم ورود و وضعیت جاری، وضعیت بعدی مشخص می شود.

        switch ($current_status_id) {
            case 0: // گرفتن اولین وضعیت فرم ورود
                if ($this->input_form_guarding_require_permission) {
                    return 500000710; // در انتظار تایید نگهبانی
                } elseif ($this->input_form_quality_control_permission) {
                    return 500000535; // در انتظار تایید کنترل کیفیت
                } else {
                    return $has_general_item ? 500000430 : 500000410; // در انتظار تکمیل اطلاعات
                }
                break;
            case 500000710:  // در انتظار تایید نگهبانی
                if ($this->input_form_quality_control_permission) {
                    return 500000535; // در انتظار تایید کنترل کیفیت
                } else {
                    return $has_general_item ? 500000430 : 500000410; // در انتظار تکمیل اطلاعات
                }
                break;
            case 500000535:  // در انتظار تایید کنترل کیفیت
                return $has_general_item ? 500000430 : 500000410; // در انتظار تکمیل اطلاعات
                break;

        }

        1 / 0;

    }

    public static function CalculateActualCost(MachineAllocation $machine_allocation)
    {

        // محاسبه BOM کالا
        $line_product_station = LineProductStation::
        whereNotNull("contractor_operation_id")->
        where("contractor_id", $machine_allocation->contractor_id)->
        where("product_id", $machine_allocation->product_id)->
        first();

        // گرفتن اولین BOM
        $bom = BOM::where("product_route_id", $line_product_station->product_route_id)->first();

        $material_sent_amount = [];
        // گرفتن فرم های تحویل مواد اولیه
        $product_request_form_list = ProductRequestForm::where("allocation_id", $machine_allocation->allocation_id)->orderByDesc("id")->get();
        foreach ($product_request_form_list as $product_request_form) {
            foreach ($product_request_form->items as $product_request_form_item) {
                if (!isset($material_sent_amount[$product_request_form_item->product_id])) {
                    $material_sent_amount[$product_request_form_item->product_id] = 0;
                }
                $material_sent_amount[$product_request_form_item->product_id] += $product_request_form_item->amount_sent;

            }
        }

        $product_amount = ProductionFormItem::where("allocation_id", $machine_allocation->allocation_id)->sum("final_amount");

        $allocation_amount = $machine_allocation->allocation->getAllocationAmount();

        if ($product_amount == 0) {
            // ارسال پیامک خطا
        } else {

            //حذف محاسبات قبلی در صورت وجود
            CurrentMachineInput::where("allocation_id", $machine_allocation->allocation_id)->delete();

//محاسبه مصرف واقعی

            foreach ($bom->items as $bom_item) {
                if (!isset($material_sent_amount[$bom_item->material_id])) {
                    continue;
                }

                $amount_required = CurrentMachineInput::getConsumedAmount(
                    $bom_item->amount,
                    $bom_item->number,
                    $bom_item->percent_of_use
                );

//                    return
//                        [
//                            "allocation_id" => $machine_allocation->allocation_id,
//                            "product_id" => $machine_allocation->product_id,
//                            "material_id" => $material_id,
//                            "predictive_amount" => $value,
//                            "actual_amount" => $material_sent_amount[$product_request_form_item->product_id] / $product_amount,
//                            "change_degree_amount"=>0,
//                            "waste_amount"=>0,
//                            "calculated_amount_till_now"=>$allocation_amount
//                        ];

                // در زمان تخصیص باید ورودی های تخصیص را درست کنیم و شماره پیمانکار آن را مشخص نماییم.
                $has_current_machine_input = CurrentMachineInput::where("allocation_id", $machine_allocation->allocation_id)->first();
                if (!$has_current_machine_input) {
                    CurrentMachineInput::CreateOrUpdate(
                        1,
                        1,
                        $bom_item->material_id,
                        $bom_item->amount,
                        $amount_required,
                        $bom_item->percent_of_use,
                        $bom_item->material->goods_kind_id,
                        $machine_allocation->allocation_id,
                        0,
                        $bom_item->product_id,
                        $machine_allocation->production_id,
                        1,
                        $bom_item->number,
                        null,
                        null,
                        $bom_item->productive_consume_warehouse_id,
                        $bom_item->warehouse_id,
                        $bom_item,
                        1
                    );
                }

                MachineAllocationActualConsumption::updateOrCreate([
                        "allocation_id" => $machine_allocation->allocation_id,
                        "product_id" => $machine_allocation->product_id,
                        "material_id" => $bom_item->material_id
                    ]
                    ,
                    [
                        "predictive_amount" => $amount_required,
                        "actual_amount" => $material_sent_amount[$product_request_form_item->product_id] / $product_amount,
                        "change_degree_amount" => 0,
                        "waste_amount" => 0,
                        "calculated_amount_till_now" => $allocation_amount
                    ]);
            }
        }
    }

    /**
     * صدا زدن API برای پیمانکاران
     * @param Contractor $contractor
     * @param ProductRequestForm $productRequestForm
     * @param Form $exit_form
     * @return array
     */
    public static function CallApiAddInputFormForContractor(Contractor $contractor, ProductRequestForm $productRequestForm, $exit_form, $transport)
    {

        if (!$contractor->software_system_id) {
            return [
                "result" => true,
                "message" => "پیمانکار سامانه ندارد."
            ];
        }
        $exit_form_list_caption = "";
        if ($exit_form) {
            $next_status_id = $productRequestForm->nextStatusForExistForm($exit_form->status_id);
            $exit_form_list_caption = $exit_form->code;
        } else {
            $t_form = $transport->transport_forms()->first();
            if (!$t_form) {
                return [
                    "result" => true,
                    "message" => "هیچ برگ خروجی در بار با شماره " . $transport->code . " وجود ندارد."
                ];
            }
            foreach ($transport->transport_forms as $t_form_item) {
                $exit_form_list_caption .= $t_form_item->form->code.", ";
            }
            $next_status_id = $productRequestForm->nextStatusForExistForm($t_form->form->status_id);
        }
        if ($next_status_id != 500000500) { // در انتظار تایید درخواست کننده
            return [
                "result" => true,
                "message" => "نیاز به ثبت نمی باشد."
            ];
        }

        $result_api_login = SoftwareSystem::Login($contractor->software_system, $contractor->api_url, $contractor->api_username, $contractor->api_password, $contractor->api_key);
        if (!$result_api_login["result"]) {
            return [
                "result" => false,
                "error" => $result_api_login["message"]
            ];
        }

        $message = " ثبت برگ خروج از انبار  " . $exit_form_list_caption . " در " . Setting::getStringValue("software_name");
        $result_api_add_input_form_for_contractor = SoftwareSystem::CallAddInputFormForContractor(
            $contractor->software_system,
            $contractor->api_url,
            $result_api_login["token"],
            $productRequestForm,
            $exit_form,
            $transport,
            $message

        );
        if (!$result_api_add_input_form_for_contractor["result"]) {
            SoftwareSystem::Logout(
                $contractor->software_system,
                $contractor->api_url,
                $result_api_login["token"]
            );
            return [
                "result" => false,
                "error" => "خطای " . $contractor->software_system->caption . " در " . $contractor->caption . ": <br/>" . $result_api_add_input_form_for_contractor["error"] . "<br/>"
            ];
        }

        // اگر دارای نرم افزار جامع باشد و به خطا نخورده باشد، باید خارج شود.
        SoftwareSystem::Logout(
            $contractor->software_system,
            $contractor->api_url,
            $result_api_login["token"]
        );

        return [
            "result" => true,
            "message" => "عملیات ایجاد فرم ورود برای پیمانکار موفقیت آمیز بود."
        ];
    }

}
