<?php

namespace App\Models\Post;

use App\Models\User;
use App\Models\Utility\Document\DocumentType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostDocumentType extends Model
{
    use HasFactory;

    protected $fillable = [
        "document_type_id","post_id",'document_delivery_type'
    ];
    protected $table='post_document_type';

    public function post() {

        return $this->belongsTo(Post::class);
    }
    public function document_type() {

        return $this->belongsTo(DocumentType::class);
    }
}
