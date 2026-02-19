<?php

namespace App\Models\LineProduct\Machine\ProductionChannel;

use App\Models\LineProduct\Machine\MachineModuleType;
use App\Models\LineProduct\Machine\MachineType;
use App\Models\Production\ProductionChannelNextOne;
use App\Models\Production\ProductionChannelType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MachineTypeProductionChannelType extends Model
{
    use HasFactory;

    protected $table = "machine_type_production_channel_type";
    protected $fillable = ["machine_type_id", "production_channel_type_id"];

    public function production_channel_type()
    {
        return $this->belongsTo(ProductionChannelType::class);
    }

    public function production_channel_next_ones()
    {
        // کانال بعدی به ازای هر ایستگاه کاری مشخص می شود.
        return ProductionChannelNextOne::
        where("machine_type_id", $this->machine_type_id)->
            where("production_channel_type_id",$this->production_channel_type_id);
    }

    public function machine_type()
    {
        return $this->belongsTo(MachineType::class);
    }
}
