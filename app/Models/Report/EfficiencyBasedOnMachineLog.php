<?php

namespace App\Models\Report;

use App\Models\LineProduct\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EfficiencyBasedOnMachineLog extends Model
{
    protected $table="efficiency_based_on_machine_logs";
    use HasFactory;
    protected $fillable=[
        "machine_log_id",
        "machine_id",
        "product_id",
        "operator_id" ,
        "machine_allocation_id",
        "log_type_id",
        "theory_contour",
        "operation_contour",
        "time_in_minute",
        "amount",
        "contour_sum_value",
        "created_minute_number",
        "created_hour_number",
        "created_day_number",
    ];
    public $timestamps=false;

    public function product(){
        return $this->belongsTo(Product::class);
    }
}
