<?php

namespace App\Models\Warehouse\WarehouseHandling;

use App\Models\Form\Packing\PackingForm;
use App\Models\Utility\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseHandlingPackingForm extends Model
{
    use HasFactory;

    protected $table = "warehouse_handling_packing_form";
    protected $fillable = [
        "warehouse_handling_id",
        "packing_form_id",
        "status_id",
        "final_amount",
        "weight",
        "sub_packing_form_number",
        "has_diff_in_amount"
    ];

    public function warehouse_handling()
    {
        return $this->belongsTo(WarehouseHandling::class, "warehouse_handling_id");
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function packing_form()
    {
        return $this->belongsTo(PackingForm::class);
    }

    /**
     * تعداد بسته بندی های داخل انبار گردانی که وضعیت آنها نادرست است.
     * @param WarehouseHandling $warehouseHandling
     */
    public static function GetErrorCount(WarehouseHandling $warehouseHandling)
    {
        $count = WarehouseHandlingPackingForm::where("warehouse_handling_id", $warehouseHandling->id)->
        whereIn("status_id", [
            524000404,
            524000405,
            524000406
        ])->count();

        return $count;
    }
}
