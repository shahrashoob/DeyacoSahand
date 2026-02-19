<?php

namespace App\Models\HR\Personal;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DependentType extends Model
{
    use HasFactory;
    protected $table = 'dependent_types';
    protected $fillable = [
        'caption'
    ];
}
