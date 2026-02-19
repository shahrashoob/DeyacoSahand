<?php

namespace App\Models\Utility\Document;

use App\Models\Post\PostDocumentType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentReceiveStepDocumentType extends Model
{
    use HasFactory;
    protected $fillable = [
        'receive_document_step_id','document_type_id'
    ];
    protected $table = 'document_receive_step_document_types';

    public function receive_document_step() {

        return $this->belongsTo(DocumentReceiveStep::class);
    }
    public function document_type() {

        return $this->belongsTo(DocumentType::class);
    }
}
