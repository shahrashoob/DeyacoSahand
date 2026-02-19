<?php

namespace App\Models\HR\Selection;

use App\Models\Utility\FieldType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SelectionIndicator extends Model
{
    use HasFactory;
    protected $table='selection_indicators';
    protected $fillable = [
        'caption',
        'selection_id',
        'field_type_id',
        'weight',
        'min_score',


    ];

    public function field_type() {

        return $this->belongsTo(FieldType::class);
    }
    public function selection() {

        return $this->belongsTo(Selection::class);
    }
}
