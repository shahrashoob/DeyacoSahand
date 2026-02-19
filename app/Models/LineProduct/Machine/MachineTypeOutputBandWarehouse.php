<?php

namespace App\Models\LineProduct\Machine;

use App\Models\Utility\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MachineTypeOutputBandWarehouse extends Model
{
    protected $table = "machine_type_output_band_warehouses";
    protected $fillable = [
        "machine_type_id",
        "goods_kind_id",
        "degree_id",
        "warehouse_id",
        "quality_control_warehouse_id"
    ];
}