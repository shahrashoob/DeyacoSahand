<?php

namespace App\Models\HR\Selection;

use App\Models\Post\Post;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SelectionPostSetting extends Model
{
    use HasFactory;
    protected $table='selection_post_settings';
    protected $fillable = [
        'post_id',
        'selection_id',
        'minimum_score_to_confirm_selection',
        'priority_number',
    ];
    public function selection() {

        return $this->belongsTo(Selection::class,'selection_id');
    }
    public function post() {

        return $this->belongsTo(Post::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * پیش نیاز های مصاحبه
     */
    public function post_selection_education() {

        return $this->hasMany(PostSelectionEducation::class,"post_selection_setting_id");
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * پست های سازمانی و کمیته های ارزیاب
     */
    public function selection_selectors() {

        return $this->hasMany(SelectionSelector::class,"selection_post_setting_id");
    }
    public function selection_selector_posts() {

        return $this->hasMany(SelectionSelector::class,"selection_post_setting_id")->whereNotNull("post_selection_id");
    }
    public function selection_selector_committees() {

        return $this->hasMany(SelectionSelector::class,"selection_post_setting_id")->whereNotNull("committee_id");
    }

}
