<?php

namespace App\Models\HR\Selection;

use App\Models\HR\Committee\Committee;
use App\Models\HR\Education\Education;
use App\Models\Post\FloatingpostType;
use App\Models\Post\Post;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SelectionSelector extends Model
{
    use HasFactory;

    protected $table = 'selection_selectors';
    protected $fillable = [

        'selection_id',
        'post_selection_id',
        'post_id',
        'committee_id',
        'minimum_percent_of_committee',
        'floating_post_type_id',
        'selection_post_setting_id',
        'confirmation_is_required'
    ];
    public function selections() {

        return $this->belongsTo(Selection::class);
    }
    public function educations() {

        return $this->belongsToMany(Education::class);
    }
    public function selection_type()
    {
        return $this->belongsTo(SelectionType::class);
    }
    public function post() {

        return $this->belongsTo(Post::class);
    }
    public function committee() {

        return $this->belongsTo(    Committee::class);
    }
    public function floating_post_type() {

        return $this->belongsTo(FloatingpostType::class);
    }
    public function post_selection() {

        return $this->belongsTo(Post::class,'post_selection_id');
    }
}
