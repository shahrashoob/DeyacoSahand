<?php

namespace App\Models\Production;

use App\Models\LineProduct\Packing\PackingType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionPackingType extends Model
{
    use HasFactory;
    protected $table="production_packing_type";
    protected $fillable=["production_id","packing_type_id"];

    public function production(){
        return $this->belongsTo(Production::class);
    }
    public function packing_type(){
        return $this->belongsTo(PackingType::class);
    }
}
