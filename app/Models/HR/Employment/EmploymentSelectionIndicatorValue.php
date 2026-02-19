<?php

namespace App\Models\HR\Employment;

use App\Models\HR\Selection\Selection;
use App\Models\HR\Selection\SelectionIndicator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmploymentSelectionIndicatorValue extends Model
{
    protected $fillable = [
        'employment_id',
        'selection_id',
        'post_id',
        'value',
        'selection_indicator_id',
        'weight',
        'employment_selection_id',
    ];
    protected $table = 'employment_selection_indicator_values';

    public function selection() {

        return $this->belongsTo(Selection::class,'selection_id');
    }
    public function selection_indicator()
    {
        return $this->belongsTo(SelectionIndicator::class, 'selection_indicator_id',);
    }

}
