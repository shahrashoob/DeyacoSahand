<?php

namespace App\Models\LineProduct\Machine;

use App\Models\Utility\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MachineTypeOutputBand extends Model
{
    use HasFactory;

    protected $table = "machine_type_output_bands";
    protected $fillable = [
        "machine_type_id",
        "code",
        "caption",
        "active_status_id",
        "output_line_number",
        "doff_algorithm_id"
    ];
    public function machine_type() {
        return $this->belongsTo( MachineType::class );
    }
    public function goods_kinds(){
        return $this->hasMany(MachineTypeOutputBandGoodsKind::class);
    }
    public function active_status(){
        return $this->belongsTo(Status::class,"active_status_id");
    }
    public function has_product_type_permission($packing_type_id){

        return MachineTypeOutputBandPackingType::where(["machine_type_output_band_id"=>$this->id,"packing_type_id"=>$packing_type_id])->exists();
    }
    public static function get_number_band($goods_kind_id){

      $output_band=  MachineTypeOutputBand::join("machine_type_output_band_goods_kind","machine_type_output_band_id","machine_type_output_bands.id")->
            where("goods_kind_id",$goods_kind_id)->first();

          return $output_band->output_line_number??false;

    }
}
