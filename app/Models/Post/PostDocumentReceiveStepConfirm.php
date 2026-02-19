<?php

namespace App\Models\Post;

use App\Models\Utility\Document\DocumentReceiveStep;
use App\Models\Utility\Document\DocumentType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostDocumentReceiveStepConfirm extends Model
{
    use HasFactory;
    protected $fillable = [
        'receive_document_step_id',"post_id",'confirm_type','is_necessary_to_deliver_document_to_archive',
    ];
    protected $table='post_document_receive_step_confirms';

    public function post() {

        return $this->belongsTo(Post::class);
    }
    public function receive_document_step() {

        return $this->belongsTo(DocumentReceiveStep::class);
    }
}
