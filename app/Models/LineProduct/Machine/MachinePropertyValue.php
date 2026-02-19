<?php

namespace App\Models\LineProduct\Machine;

use App\Models\Utility\SpecialUnit;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MachinePropertyValue extends Model
{
    use HasFactory;
    use Loggable;
    protected $table="machine_property_value";
    protected $fillable=["machine_property_id","machine_type_id","value"];

    public function machine_property(){
        return $this->belongsTo(MachineProperty::class);
    }
}
