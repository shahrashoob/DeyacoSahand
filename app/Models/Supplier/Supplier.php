<?php

namespace App\Models\Supplier;

use App\Models\Accounting\CostCenter;
use App\Models\HR\Company\Company;
use App\Models\File\File;
use App\Models\HR\Personal\PersonalType;
use App\Models\LineProduct\Machine\MachineAllocation;
use App\Models\LineProduct\SupplyType;
use App\Models\Post\Post;
use App\Models\Utility\Address\Address;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        "code",
        "caption",
        "supplier_type_id",
        'user_id',
        'personal_type_id',
        "register_code",
        "active_status_id",
        'company_id',
        "cost_center_id",
        "firstname",
        "lastname",
        "national_code",
        "detailed_code",
        "get_packing_form_details",
        'image_id',
        'company_id_in_ic_system',
        "input_form_guarding_require_permission",
        "input_form_loading_require",
        "input_form_quality_control_permission",
        'post_id',
        "start_date_of_contract",
        "end_date_of_contract",
        "exit_form_require_quality_permission",

        "exit_form_require_draft_permission",
        "exit_form_require_draft_permission_post_id",

        "exit_form_require_permission",
        "exit_form_require_permission_post_id",

        "exit_form_loading_require_permission",

        "exit_form_guarding_require_permission",
        "exit_form_guarding_require_permission_post_id",

        "checking_carrier_at_delivery_of_product",

        "can_i_borrow_from_this_supplier",


    ];

    public function image()
    {
        return $this->belongsTo(File::class, "image_id");
    }
    public function user()
    {
        return $this->belongsTo(Worker::class, "user_id");
    }
    public function exit_form_require_draft_permission_post()
    {
        return $this->belongsTo(Post::class, 'exit_form_require_draft_permission_post_id');
    }

    public function exit_form_guarding_require_permission_post()
    {
        return $this->belongsTo(Post::class, 'exit_form_guarding_require_permission_post_id');
    }

    public function exit_form_require_permission_post()
    {
        return $this->belongsTo(Post::class, 'exit_form_require_permission_post_id');
    }

    public function supplier_type()
    {
        return $this->belongsTo(SupplierType::class);
    }

    public function cost_center()
    {
        return $this->belongsTo(CostCenter::class);
    }
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function personal_type()
    {
        return $this->belongsTo(PersonalType::class);
    }
    public function supplier_allocation()
    {
        return $this->hasMany(MachineAllocation::class, "supplier_id");

    }

    public function fullName()
    {
        return $this->firstname . " " . $this->lastname;

    }
    public function get_end_date_of_contract()
    {
        return jdate(Carbon::parse($this->end_date_of_contract)->timestamp)->format('Y/m/d');
    }
    public function fullCaption()
    {
        return $this->caption . "(" . $this->fullname() . ")";

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

    public function getDefaultAddress()
    {
        $supplier_address = SupplierAddress::where(["supplier_id" => $this->id, "is_default" => 1])->first();

        return $supplier_address->address ?? null;
    }

    public function UpdateAddress($request, $is_default)
    {
        $address = $this->getDefaultAddress();
        if (isset($address)) {
            $address->update($request->all());
        } else {

            $address = Address::create($request->all());

            $supplier_address = new SupplierAddress();
            $supplier_address->is_default = $is_default;
            $supplier_address->supplier_id = $this->id;
            $supplier_address->address_id = $address->id;
            $supplier_address->save();
        }
    }


    public function nextStatusForInputForm($current_status_id, $has_general_item)
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
            case 500000700:  // در انتظار دریافت بار (بارگیری)
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

    public function nextStatusForExistForm($current_status_id)
    {

        // با توجه به اولویت های موجود برای برگ خروج و وضعیت جاری، وضعیت بعدی مشخص می شود.

        switch ($current_status_id) {
            case 0: // گرفتن اولین وضعیت فرم خروج
                if ($this->exit_form_require_quality_permission) {
                    return 500000535; //  کنترل کیفیت
                } else if ($this->exit_form_require_draft_permission) {
                    return 500000515; // پیش نویس
                } elseif ($this->exit_form_require_permission) {
                    return 500000520; // نهایی
                } elseif ($this->exit_form_loading_require_permission) {
                    return 500000525; // بارگیری
                } elseif ($this->exit_form_guarding_require_permission) {
                    return 500000530; // نگبانی
                } else {
                    return 500000200; // تایید شده
                    //return 500000500; // پیمانکار(درخواست کننده)
                }
                break;
            case 500000535: // کنترل کیفیت
                if ($this->exit_form_require_draft_permission) {
                    return 500000515; // پیش نویس
                } elseif ($this->exit_form_require_permission) {
                    return 500000520; // نهایی
                } elseif ($this->exit_form_loading_require_permission) {
                    return 500000525; // بارگیری
                } elseif ($this->exit_form_guarding_require_permission) {
                    return 500000530; // نگبانی
                } else {
                    return 500000200; // تایید شده
                    //return 500000500; // پیمانکار(درخواست کننده)
                }
            case 500000515:  // پیش نویس
                if ($this->exit_form_require_permission) {
                    return 500000520; // نهایی
                } elseif ($this->exit_form_loading_require_permission) {
                    return 500000525; // بارگیری
                } elseif ($this->exit_form_guarding_require_permission) {
                    return 500000530; // نگبانی
                } else {
                    return 500000200; // تایید شده
                    //return 500000500; // پیمانکار(درخواست کننده)
                }
                break;
            case 500000520:  // تایید نهایی
                if ($this->exit_form_loading_require_permission) {
                    return 500000525; // بارگیری
                } elseif ($this->exit_form_guarding_require_permission) {
                    return 500000530; // نگبانی
                } else {
                    return 500000200; // تایید شده
                    //return 500000500; // پیمانکار(درخواست کننده)
                }
                break;
            case 500000525:  // بارگیری
                if ($this->exit_form_guarding_require_permission) {
                    return 500000530; // نگبانی
                } else {
                    return 500000200; // تایید شده
                    //return 500000500; // مشتری
                }
                break;
            case 500000530:   // نگبانی
                return 500000200; // تایید شده
                // return 500000500; // پیمانکار(درخواست کننده)
                break;
//            case 500000500:  // پیمانکار(درخواست کننده)
//                return 500000200; // تایید شده
                break;

        }

        1 / 0;

    }
}
