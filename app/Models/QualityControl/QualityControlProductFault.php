<?php

namespace App\Models\QualityControl;

use App\Models\Accounting\CostCenter;
use App\Models\Form\Packing\PackingForm;
use App\Models\Form\Packing\PackingFormItem;
use App\Models\HR\Company\Company;
use App\Models\File\File;
use App\Models\HR\Personal\PersonalType;
use App\Models\LineProduct\Machine\MachineAllocation;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\SupplyType;
use App\Models\Post\Post;
use App\Models\Production\ProductionForm;
use App\Models\Production\ProductionFormItem;
use App\Models\Utility\Address\Address;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class QualityControlProductFault extends Model
{
    use HasFactory;

    protected $table = "quality_control_product_fault";
    protected $fillable = [
        'packing_form_id',
        'packing_form_item_id',
        'production_id',
        'production_form_id',
        'production_form_item_id',
        'product_id',
        "band_code",
        'product_fault_id',
        'start_point',
        'end_point',
        'point',
        'fault_is_fixed',
        "parent_quality_control_product_fault_id"
    ];

    public function create_datetime()
    {
        return jdate(Carbon::parse($this->created_at)->timestamp)->format('H:i Y/m/d ');

    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function packing_form()
    {
        return $this->belongsTo(PackingForm::class);
    }

    public function packing_form_item()
    {
        return $this->belongsTo(PackingFormItem::class);
    }

    public function product_fault()
    {
        return $this->belongsTo(Product\Fault\ProductFault::class);
    }

    public function production_form()
    {
        return $this->belongsTo(ProductionForm::class);
    }

    public function production_form_item()
    {
        return $this->belongsTo(ProductionFormItem::class);
    }

    public function product_fault_propery_values(){
        return $this->hasMany(QualityControlProductFaultPropertyValue::class,"quality_control_product_fault_id");
    }

    public function get_operator_fullname()
    {
        $result = QualityControlPackingForm::GetOperator($this->packing_form, $this->point ? $this->point : ($this->start_point));
        if ($result["result"]) {
            return $result["operator_fullname"];
        }
        return null;
    }
}