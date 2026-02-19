<?php

namespace App\Models\HR\Selection;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SelectionType extends Model
{
    use HasFactory;
    protected $fillable = [
        'caption',
    ];
    protected $table='selection_types';
}
