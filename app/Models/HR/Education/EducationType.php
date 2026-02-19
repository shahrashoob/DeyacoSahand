<?php

namespace App\Models\HR\Education;

use App\Models\HR\Interview\Interview;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EducationType extends Model
{
    use HasFactory;
    protected $fillable = [
        'caption',
    ];
    protected $table='education_types';

}
