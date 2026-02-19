<?php

namespace App\Models\LineProduct\Machine;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurrentMachineInputOutputBandLog extends Model
{
    use HasFactory;
    protected $table="current_machine_input_output_band_log";

    protected $fillable = [
        "production_id",
        "product_id",
        "lot_number_id",
        "machine_type_id",
        "machine_id",
        "input_band_id",
        "input_line_code",
        "band_code",
        "material_id",
        "number",
        "amount",
        "percent_of_use",
        "effect_is_shared",
        "allocation_id"
    ];
}
