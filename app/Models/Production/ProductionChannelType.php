<?php

namespace App\Models\Production;

use App\Models\LineProduct\GoodsKind;
use App\Models\LineProduct\Machine\MachineType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionChannelType extends Model {
    use HasFactory;

    protected $fillable = [ "id", "caption","color","min_capacity","max_capacity","max_number_of_sequences", "goods_kind_id","production_channel_category_id" ];

    public function goods_kind() {
        return $this->belongsTo( GoodsKind::class );
    }
    public function production_channel_category() {
        return $this->belongsTo( ProductionChannelCategory::class );
    }
    public function get_production_channel_next_ones(MachineType $machineType) {
        return ProductionChannelNextOne::
        where(["machine_type_id"=>$machineType->id,"production_channel_type_id"=>$this->id])->
         orderBy("priority_number")   ->
        get();
    }
    public function get_production_channel_before_ones(MachineType $machineType) {
        return ProductionChannelNextOne::
        where(["machine_type_id"=>$machineType->id,"next_production_channel_type_id"=>$this->id])->
         orderBy("priority_number")   ->
        get();
    }

    public static function ExistsCaption( $caption, $id = false ) {
        if ( $id ) {
            return ProductionChannelType::where( "caption", $caption )->where( "id", "!=", $id )->exists();
        }

        return ProductionChannelType::where( "caption", $caption )->exists();
    }

}
