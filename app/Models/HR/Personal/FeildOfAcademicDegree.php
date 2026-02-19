<?php

namespace App\Models\HR\Personal;

use App\Models\HR\User\UserAcademicDegree;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeildOfAcademicDegree extends Model
{
    use HasFactory;
    use HasFactory;



    protected $fillable = [
        'caption','academic_degree_type_id'
    ];

    protected $table = 'feild_of_academic_degrees';

    public function feild_of_academic_degree()
    {
        return $this->hasMany(UserAcademicDegree::class);
    }
}
