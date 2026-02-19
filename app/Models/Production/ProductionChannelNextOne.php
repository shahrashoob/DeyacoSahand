<?php

namespace App\Models\Production;

use App\Models\LineProduct\GoodsKind;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionChannelNextOne extends Model
{
    use HasFactory;

    protected $fillable = ["id","machine_type_id", "production_channel_type_id","next_production_channel_type_id","priority_number"];

    public function next_production_channel_type(){
        return $this->belongsTo(ProductionChannelType::class,"next_production_channel_type_id");
    }
    public function production_channel_type(){
        return $this->belongsTo(ProductionChannelType::class,"production_channel_type_id");
    }
}