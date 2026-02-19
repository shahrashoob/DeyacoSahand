<?php

namespace App\Models\HR\Education;

use App\Models\File\File;
use App\Models\HR\Exam\ExamType;
use App\Models\HR\Interview\Interview;
use App\Models\HR\Responsible\ResposibleType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    use HasFactory;

    protected $fillable = [
        "education_type_id",
        "educational_text_file_id",
        "educational_video_file_id",
        "caption",
        "exam_type_id",
        "minimum_score_to_confirm_the_education",


    ];
    protected $table='educations';
    public function interviews() {

        return $this->belongsToMany(Interview::class);
    }
    public function education_type()
    {
        return $this->belongsTo(EducationType::class);
    }
    public function responsible_types() {

        return $this->belongsToMany(ResposibleType::class);
    }
    public function educational_text_file() {

        return $this->belongsTo(File::class);
    }
    public function educational_video_file() {

        return $this->belongsTo(File::class);
    }
    public function exam_type() {

        return $this->belongsTo(ExamType::class);
    }

}
