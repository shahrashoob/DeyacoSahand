<?php

namespace App\Models\HR\Responsible;

use App\Models\HR\Education\Education;
use App\Models\HR\Interview\Interview;
use Database\Seeders\Responible\ResposibleTypeSeeder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EducationResposibleType extends Model
{
    use HasFactory;
    protected $table='education_responsible_types';
    protected $fillable = [

        'responsible_type_id',
        'education_id',
        'post_id'
    ];
    public function responsible_types() {

        return $this->belongsTo(Resposible_Type::class);
    }
    public function educations() {

        return $this->belongsTo(Education::class);
    }

}
