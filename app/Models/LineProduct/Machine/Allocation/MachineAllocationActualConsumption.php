<?php

namespace App\Models\LineProduct\Machine\Allocation;

use App\Models\LineProduct\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MachineAllocationActualConsumption extends Model {
    use HasFactory;

    protected $table = "machine_allocation_actual_consumption";
    protected $fillable = [
        "allocation_id",
        "product_id",
        "material_id",
        "predictive_amount",
        "actual_amount",
        "change_degree_amount",
        "waste_amount",
        "calculated_amount_till_now",
        "cost_of_one_unit"
    ];

    public function material() {
        return $this->belongsTo( Product::class, "material_id" );
    }
}
