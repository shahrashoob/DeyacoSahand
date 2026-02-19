<?php

namespace App\Models\Utility\Document;

use App\Models\Post\Post;
use App\Models\Post\PostDocumentType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
    use HasFactory;
    protected $fillable = [
        'caption','nationality_id'
    ];
    protected $table = 'document_types';

    public function post_document_type()
    {
        return $this->hasMany(PostDocumentType::class, 'document_type_id');
    }

}
