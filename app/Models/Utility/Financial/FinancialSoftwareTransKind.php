<?php

namespace App\Models\Utility\Financial;

use App\Models\Order\TransKind;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialSoftwareTransKind extends Model {
    use HasFactory;

    protected $table = "financial_software_trans_kind";
    protected $fillable = [
        "financial_software_id",
        "trans_kind_id",
        "warehouse_id",
        "has_accounting_document",
        "has_warehouse_transaction",
        "has_sale_invoice",
        "has_group_by_product",
        "inv_kind_code",
        "inv_series",
        "dept_code",
        "delivery_cond_code",
        "payment_method_code",
        "sales_center_code",
        "deb_side",
        "calc_state"
    ];

    public function financial_software() {
        return $this->belongsTo( FinancialSoftware::class );
    }
    public function trans_kind() {
        return $this->belongsTo( TransKind::class ,"trans_kind_id");
    }


}
