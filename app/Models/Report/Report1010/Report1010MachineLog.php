<?php

namespace App\Models\Report\Report1010;

use App\Models\LineProduct\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report1010MachineLog extends Model
{
    protected $table="report_1010_machine_logs";
    use HasFactory;
    protected $fillable=[
        "machine_log_id",
        "machine_id",
        "product_id",
        "operator_id" ,
        "created_at",
        "theory_contour",
        "operation_contour",
        "time_in_minute",
        "amount",
        "machine_allocation_id"
    ];
    public $timestamps=false;

    public function product(){
        return $this->belongsTo(Product::class);
    }
}
