<?php

namespace App\Models\QualityControl;

use App\Models\Accounting\CostCenter;
use App\Models\HR\Company\Company;
use App\Models\File\File;
use App\Models\HR\Personal\PersonalType;
use App\Models\LineProduct\Machine\MachineAllocation;
use App\Models\LineProduct\Product\Fault\ProductFault;
use App\Models\LineProduct\Product\Fault\ProductFaultProperties;
use App\Models\LineProduct\Product\Fault\ProductFaultPropertyOption;
use App\Models\LineProduct\SupplyType;
use App\Models\Post\Post;
use App\Models\Utility\Address\Address;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class QualityControlProductFaultPropertyValue extends Model
{
    use HasFactory;

    protected $table = "quality_control_fault_property_value";
    protected $fillable = [
        'quality_control_product_fault_id',
        'packing_form_id',
        'packing_form_item_id',
        'production_id',
        'production_form_id',
        'production_form_item_id',
        'product_id',
        'product_fault_id',
        'product_fault_property_id',
        'value',
    ];

    public function product_fault()
    {
        return $this->belongsTo(ProductFault::class, 'product_fault_id');
    }

    public function product_fault_property()
    {
        return $this->belongsTo(ProductFaultProperties::class);
    }

    public function getValue()
    {
        if($this->product_fault_property->field_type_id == 3){
           $option= ProductFaultPropertyOption::
           where("product_fault_property_id",$this->product_fault_property_id)->
           where("value",$this->value)->first();
           if($option){
               return $option->caption;
           }
        }
        return $this->value;
    }
}