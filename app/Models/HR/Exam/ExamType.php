<?php

namespace App\Models\HR\Exam;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamType extends Model
{
    use HasFactory;

    protected $fillable = [
        'caption',
    ];

    protected $table='exam_types';

}
