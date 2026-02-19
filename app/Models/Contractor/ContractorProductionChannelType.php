<?php

namespace App\Models\Contractor;

use App\Models\Production\ProductionChannelType;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ContractorProductionChannelType extends Model {
    use HasFactory;
    use Loggable;

    protected $fillable = [ "contractor_id", "production_channel_type_id" ];
protected $table="contractor_production_channel_type";
    public function contractor(){
        return $this->belongsTo(Contractor::class);
    }

    public function production_channel_type(){
        return $this->belongsTo(ProductionChannelType::class);
    }

}
