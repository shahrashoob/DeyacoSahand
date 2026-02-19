<?php

namespace App\Models\Form;

use App\Models\Customer\Customer;
use App\Models\LineProduct\Degree;
use App\Models\LineProduct\LotNumber;
use App\Models\LineProduct\Machine\MachineAllocation;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\LineProduct\Product;
use App\Models\Order\Order;
use App\Models\Production\ProductionFormItem;
use App\Models\Warehouse\WarehouseStorageType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormGeneralItem extends Model
{
    use HasFactory;
    protected $table = "form_general_item";
    protected $fillable = [
        "form_id",
        "order_id",
        "production_form_item_id",
        "allocation_id",
        "machine_allocation_id",
        "product_id",
        "degree_id",
        "lot_number_id",
        "packing_type_id",
        "warehouse_storage_type_id",
        "status_id",
        "packing_form_number",
        "amount",
        "sub_amount",
        "price",
        "tax_price",
        "total_price_with_tax",
        "price_registration_status_id",
        "kg_in_meter",
        'customer_id'
    ];

    public function form() {
        return $this->belongsTo( Form::class );
    }

    public function order() {
        return $this->belongsTo( Order::class );
    }
    public function customer() {
        return $this->belongsTo( Customer::class );
    }
    public function production_form_item() {
        return $this->belongsTo( ProductionFormItem::class );
    }
    public function product() {
        return $this->belongsTo( Product::class );
    }
    public function degree() {
        return $this->belongsTo( Degree::class );
    }
    public function lot_number() {
        return $this->belongsTo( LotNumber::class );
    }
    public function packing_type() {
        return $this->belongsTo( PackingType::class );
    }
    public function machine_allocation() {
        return $this->belongsTo( MachineAllocation::class );
    }
    public function warehouse_storage_type() {
        return $this->belongsTo( WarehouseStorageType::class );
    }
    public function GetPackingFormCaption() {
        // return $this->packing_type_id ;
       return $this->warehouse_storage_type_id==2?$this->packing_type->fullCaption():$this->warehouse_storage_type->caption;
    }

    public function form_general_item_packing_form()
    {
        return $this->hasMany(FormGeneralItemPackingForm::class);
    }
}
