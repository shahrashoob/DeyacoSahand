<?php

namespace App\Models\LineProduct\Machine;

use App\Models\Utility\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MachineTypeInputBand extends Model {
    use HasFactory;
    protected $table = "machine_type_input_bands";
    protected $fillable = [
        "machine_type_id",
        "code",
        "caption",
        "active_status_id",
        "input_line_number",
        "can_used_material_with_different_lot_per_production_card"
    ];

    public function machine_type() {
        return $this->belongsTo( MachineType::class );
    }
    public function goods_kinds(){
        return $this->hasMany(MachineTypeInputBandGoodsKind::class);
    }
    public function active_status(){
        return $this->belongsTo(Status::class,"active_status_id");
    }
    public function has_product_type_permission($packing_type_id){
        return MachineTypeInputBandPackingType::where(["machine_type_input_band_id"=>$this->id,"packing_type_id"=>$packing_type_id])->exists();
    }
}
