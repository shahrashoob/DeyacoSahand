<?php

namespace App\Models\HR\Selection;

use App\Models\HR\Education\Education;
use App\Models\Post\Post;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostSelectionEducation extends Model
{
    use HasFactory;
    protected $table='post_selection_educations';
    protected $fillable = [
        'education_id',
        'selection_id',
        'post_id',
        'post_selection_setting_id'

    ];
    public function selection() {

        return $this->belongsTo(Selection::class);
    }
    public function education() {

        return $this->belongsTo(Education::class);
    }
    public function post() {

        return $this->belongsTo(Post::class);
    }
    public function post_selection_setting() {
        return $this->belongsTo(SelectionPostSetting::class, 'post_selection_setting_id');

    }

}
