<?php

namespace App\Models\LineProduct\Product\RejectProduct;

use App\Models\Contractor\Contractor;
use App\Models\Customer\Customer;
use App\Models\Form\Form;
use App\Models\Form\FormItem;
use App\Models\Form\Packing\PackingForm;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormItem;
use App\Models\Order\Order;
use App\Models\Utility\Status;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class RejectProductForm extends Model {
    use HasFactory;

    protected $fillable = [
        "code",
        "applicant_type_id",
        "applicant_id",
        "status_id",
        "input_form_id",
        "exit_form_id",
        "order_id",
        "reject_product_reason_type_id"
    ];


    public function exit_form() {
        return $this->belongsTo( Form::class, "exit_form_id" );
    }

    public function input_form() {
        return $this->belongsTo( Form::class, "input_form_id" );
    }

    public function status() {
        return $this->belongsTo( Status::class );
    }

    public function order() {
        return $this->belongsTo( Order::class );
    }

    public function reject_product_reason_type() {
        return $this->belongsTo( RejectProductReasonType::class );
    }

    public function items() {
        return $this->hasMany( RejectProductFormItem::class );
    }

    public function applicant() {
        switch ( $this->applicant_type_id ) {
            case 10:
                return $this->belongsTo( Machine::class, "applicant_id" );
                break;
            case 20:
                return $this->belongsTo( Contractor::class, "applicant_id" );
            case 30:
                return $this->belongsTo( Customer::class, "applicant_id" );
        }
    }

    public function getRandom() {
        if ( $this->random == null ) {
            $this->random = Str::random( 7 );
            $this->save();
        }

        return $this->random;
    }

    public function getCode() {
        if ( isset( $this->code ) ) {
            return $this->code;
        }

        // Reject  Goods Form
        $this->code = "DCRG/" . ( 1000 + $this->id );
        $this->save();

        return $this->code;
    }

    public function get_create_date_and_time() {
        return jdate( Carbon::parse( $this->created_at )->timestamp )->format( 'H:i Y/m/d ' );

    }

    public function nextStatusForInputForm($current_status_id)
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

}
