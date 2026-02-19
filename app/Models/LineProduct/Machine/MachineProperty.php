<?php

namespace App\Models\LineProduct\Machine;

use App\Models\Utility\FieldType;
use App\Models\Utility\SpecialUnit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MachineProperty extends Model
{
    use HasFactory;
    protected $table="machine_properties";
    protected $fillable=["caption","station_id"];
    public function field_type() {
        return $this->belongsTo( FieldType::class );
    }

    public function special_unit() {
        return $this->belongsTo( SpecialUnit::class );
    }
}
