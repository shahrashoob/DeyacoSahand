<?php

namespace App\Models\LineProduct\Machine;

use App\Models\LineProduct\Degree;
use App\Models\LineProduct\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurrentMachineInputMaterialDegree extends Model
{
    use HasFactory;
    protected $table="current_machine_input_material_degree";
    protected $fillable=[
        "allocation_id",
        "product_id",
        "material_id",
        "degree_id"
    ];
    public function allocation() {
        return $this->belongsTo( Allocation::class );
    }
    public function product() {
        return $this->belongsTo( Product::class );
    }
    public function material() {
        return $this->belongsTo( Product::class ,"material_id");
    }
    public function degree() {
        return $this->belongsTo( Degree::class );
    }

    public static function AddDegreeFromBOM($allocation_id,$product_id,$material_id,Product\BOM\BOMItem $bom_item){

        $degree_id_list = $bom_item->degrees()->pluck( "degree_id" )->toArray();
        foreach ($degree_id_list as $degree_id){
            CurrentMachineInputMaterialDegree::firstOrCreate([
                "allocation_id"=>$allocation_id,
                "product_id"=>$product_id,
                "material_id"=>$material_id,
                "degree_id"=>$degree_id
            ]);
        }
    }
}
