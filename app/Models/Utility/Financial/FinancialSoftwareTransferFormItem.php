<?php

namespace App\Models\Utility\Financial;

use App\Models\Form\Form;
use App\Models\Form\FormItem;
use App\Models\Utility\Status;
use App\Models\Warehouse\WarehouseProduct;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialSoftwareTransferFormItem extends Model {
    use HasFactory;

    protected $table = "financial_software_transfer_form_item";
    protected $fillable = [
        "form_item_id",
        "financial_software_transfer_form_id",
        "warehouse_product_id",
        "amount",
    ];

    public function form_item(){
        return $this->belongsTo(FormItem::class);
    }
    public function financial_software_transfer_form(){
        return $this->belongsTo(FinancialSoftwareTransferForm::class);
    }
    public function warehouse_product(){
        return $this->belongsTo(WarehouseProduct::class,"warehouse_product_id");
    }

}
