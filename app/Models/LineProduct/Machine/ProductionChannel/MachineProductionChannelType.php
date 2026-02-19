<?php

namespace App\Models\LineProduct\Machine\ProductionChannel;

use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineModuleType;
use App\Models\LineProduct\Machine\MachineType;
use App\Models\Production\ProductionChannelNextOne;
use App\Models\Production\ProductionChannelType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MachineProductionChannelType extends Model
{
    use HasFactory;

    protected $table = "machine_production_channel_types";
    protected $fillable = ["production_channel_type_id","machine_id","machine_type_id"];

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }
    public function production_channel_type()
    {
        return $this->belongsTo(ProductionChannelType::class);
    }
}
