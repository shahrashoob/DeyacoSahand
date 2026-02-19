<?php

namespace App\Models\LineProduct\Product\ProductRequest;

use App\Models\Form\Form;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\Utility\Notification\SMSMessage;
use App\Models\Utility\Script\ScriptLog;
use Aws\Sms\SmsClient;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductRequestFromAllocation extends Model {
    use HasFactory;

    protected $table = "product_request_form_allocation";
    protected $fillable = [
        "product_request_form_id",
        "script_log_id",
        "allocation_id",
        "material_id",
        "priority_number",
        "amount_request",
        "amount_delivered",
        "amount_delivered"
    ];

    public function script_log() {
        return $this->belongsTo( ScriptLog::class );
    }

    public function product_reqeust_form() {
        return $this->belongsTo( ProductRequestForm::class );
    }

    public function product_request_form_remove() {
        if ( $this->product_request_form_id ) {
            $product_request_form_id = $this->product_request_form_id;
        } else {
            $product_request_form_id = $this->script_log->other_id ?? "0";
        }

        return ProductRequestForm::where( [ "id" => $product_request_form_id ] )->first();
    }

    public function allocation() {
        return $this->belongsTo( Allocation::class );
    }


    public function datetime() {
        return jdate( Carbon::parse( $this->created_at )->timestamp )->format( 'H:i:s Y/m/d ' );

    }


    public static function AddMaterialAmount( $allocation_material_list, $reserve_allocation_priority, $log_script, $warehouse_need_to_request, $product_request_form = null ) {

        $sum_amount = [];
        foreach ( $allocation_material_list as $allocation_id => $allocation_materials ) {
            foreach ( $allocation_materials as $material_id => $amount ) {
                if ( ! isset( $sum_amount[ $material_id ] ) ) {
                    $sum_amount[ $material_id ] = 0;
                }
                $sum_amount[ $material_id ] += $amount;
            }
        }

        // اضافه کردن مقدار درخواست به جدول PRFAllocation تا زمانی که درخواست تایید شد، آن را به جدول RFW انتقال دهد.
        foreach ( $allocation_material_list as $allocation_id => $allocation_materials ) {
            foreach ( $allocation_materials as $material_id => $amount ) {

                if (
                    isset( $warehouse_need_to_request[ $material_id ] ) &&
                    isset( $sum_amount[ $material_id ] ) &&
                    $sum_amount[ $material_id ] > 0
                ) {
                    if ( ! isset( $reserve_allocation_priority[ $allocation_id ] ) ) {

                        SMSMessage::ExceptionError("در زمان تخصیص مقدار درخواست مواد اولیه برای تخصیص ".$allocation_id." مقدار اولویت نامعتبر است، ProductRequestFormAllocation ");
                        $reserve_allocation_priority[ $allocation_id ]=1;
                    }
                    ProductRequestFromAllocation::create( [
                        "script_log_id"           => $log_script->id ?? null,
                        "product_request_form_id" => $product_request_form->id ?? null,
                        "allocation_id"           => $allocation_id,
                        "material_id"             => $material_id,
                        "priority_number"         => $reserve_allocation_priority[ $allocation_id ],
                        "amount_request"          => $warehouse_need_to_request[ $material_id ] * $amount / $sum_amount[ $material_id ],
                        "amount_delivered"        => 0,
                    ] );
                }
            }
        }
    }

    public static function UpdateMaterialAmount( ProductRequestForm $product_request_form, Form $form ) {

        $product_amount_list = [];
        foreach ( $form->item as $form_item ) {
            // به ازای هر کالا، مقدار کل را محاسبه می کنیم.
            if ( ! isset( $product_amount_list[ $form_item->product_id ] ) ) {
                $product_amount_list[ $form_item->product_id ] = 0;
            }
            $product_amount_list[ $form_item->product_id ] += $form_item->amount;
        }

        $script_log = ScriptLog::where( [ "script_id" => 7, "other_id" => $product_request_form->id ] )->first();

        // در زمان تایید تحویل مواد اولیه، مقدار مواد اولیه تحویل شده، بروز می شود
        foreach ( $product_amount_list as $product_id => $amount_delivered ) {

            $allocation_product_list = ProductRequestFromAllocation::where( [
                "material_id" => $product_id,
            ] )->
            when( $script_log, function ( $query ) use ( $script_log ) {
                return $query->where( "script_log_id", $script_log->id );
            } )->
            when( ! $script_log, function ( $query ) use ( $product_request_form ) {
                return $query->where( "product_request_form_id", $product_request_form->id );
            } )->
            orderBy( "priority_number" )->
            get();

            foreach ( $allocation_product_list as $item ) {
                if ( $item->amount_request - $item->amount_delivered > 0 && $amount_delivered > 0 ) {
                    $new_delivered          = min( $item->amount_request - $item->amount_delivered, $amount_delivered );
                    $item->amount_delivered += $new_delivered;
                    $item->save();

                    $amount_delivered -= $new_delivered;
                }
            }

            // اگر از مقدار تحویل چیزی باقی مانده بود، به اولین درخواست تحویل می دهیم.
            if ( $amount_delivered > 0 && count( $allocation_product_list ) > 0 ) {
                $allocation_product = ProductRequestFromAllocation::where( [
                    "material_id" => $product_id,
                ] )->
                when( $script_log, function ( $query ) use ( $script_log ) {
                    return $query->where( "script_log_id", $script_log->id );
                } )->
                when( ! $script_log, function ( $query ) use ( $product_request_form ) {
                    return $query->where( "product_request_form_id", $product_request_form->id );
                } )->
                orderByDesc( "priority_number" )->
                first();

                if ( $amount_delivered ) {
                    $allocation_product->amount_delivered += $amount_delivered;
                    $allocation_product->save();
                }
            }

        }

        return [ "result" => true ];
    }

}
