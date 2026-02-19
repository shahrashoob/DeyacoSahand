<?php

namespace App\Models\Utility\Financial;

use App\Models\Form\Form;
use App\Models\Utility\Status;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialSoftwareTransferForm extends Model {
    use HasFactory;

    protected $table = "financial_software_transfer_forms";
    protected $fillable = [
        "form_id",
        "financial_software_id",
        "status_id",
        "accounting_document_status_id",
        "warehouse_transaction_status_id",
        "sale_invoice_status_id"
    ];

    public function form() {
        return $this->belongsTo( Form::class );
    }
    public function financial_software() {
        return $this->belongsTo( FinancialSoftware::class );
    }
    public function items() {
        return $this->hasMany( FinancialSoftwareTransferFormItem::class );
    }

    public function status() {
        return $this->belongsTo( Status::class );
    }
    public function accounting_document_status() {
        return $this->belongsTo( Status::class,"accounting_document_status_id" );
    }
    public function warehouse_transaction_status_id() {
        return $this->belongsTo( Status::class,"warehouse_transaction_status" );
    }
    public function sale_invoice_status_id() {
        return $this->belongsTo( Status::class,"sale_invoice_status" );
    }
    public function get_create_date_and_time() {
        return jdate( Carbon::parse( $this->created_at )->timestamp )->format( 'H:i Y/m/d ' );

    }
    public function get_create_date() {
        return jdate( Carbon::parse( $this->created_at )->timestamp )->format( 'Y/m/d' );
    }
    public function getCode() {
        if ( $this->code != "" ) {
            return $this->code;
        }
        $this->code = "DCFF/" . ( $this->id + 1000 );
        $this->save();

        return $this->save();
    }

}
