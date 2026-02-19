<?php

namespace App\Models\HR\Personal;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalType extends Model
{
    use HasFactory;

    protected $fillable = [
        'caption'
    ];

}
