<?php

namespace App\Models\HR\Personal;

use App\Models\Utility\Document\DocumentReceiveStep;
use App\Models\Utility\Document\DocumentReceiveStepDocumentType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicDegreeType extends Model
{
    use HasFactory;
    protected $table = 'academic_degree_types';
    protected $fillable = [
        'caption','receive_document_step_id'
    ];
    public function academic_degree()
    {
        return $this->hasMany(AcademicDegree::class);
    }
    public function document_receive_step() {

        return $this->belongsTo(DocumentReceiveStep::class);
    }
}
