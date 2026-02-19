<?php

namespace App\Models\LineProduct\Product\ProductRequest;

use App\Models\Contractor\Contractor;
use App\Models\Customer\Customer;
use App\Models\Warehouse\Warehouse;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductRequestFromImportantStatus extends Model {
    use HasFactory;

    protected $table = "product_request_form_important_status";
    protected $fillable = [
        "product_request_form_id",
        "form_id",

        "applicant_type_id",
        "applicant_id",

        "create_exit_form_at",
        "confirm_draft_form_at",
        "confirm_final_form_at",
        "loading_at",
        "confirm_guarding_at",
        "confirm_applicant_at",
    ];

    public function product_request_form() {
        return $this->belongsTo(ProductRequestForm::class);
    }
    public function applicant() {
        switch ( $this->applicant_type_id ) {
            case 20:
                return $this->belongsTo( Contractor::class, "applicant_id" );
            case 30:
                return $this->belongsTo( Customer::class, "applicant_id" );
            case 40:// انبارک ماشین
                return $this->belongsTo( Warehouse::class, "applicant_id" );
        }

        return $this->belongsTo( Warehouse::class, "applicant_id" )->where( "id", 0 );
    }
}
