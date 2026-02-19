<?php

namespace App\Models\Utility\Document;

use App\Models\Post\PostDocumentType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentReceiveStep extends Model
{
    use HasFactory;
    protected $fillable = [
        'caption',
    ];
    protected $table = 'document_receive_steps';

    public function post_document_receive_step_confirms()
    {
        return $this->hasMany(PostDocumentType::class, 'document_receive_steps_id');
    }
}
