<?php

namespace App\Models\LineProduct;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewBOM extends Model
{
    use HasFactory;
    protected $table="new_bill_of_material";
    public function degrees(){
        return $this->hasMany(NewBOMDegree::class,"new_bill_of_material_id");
    }
}
