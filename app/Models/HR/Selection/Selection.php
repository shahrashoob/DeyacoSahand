<?php

namespace App\Models\HR\Selection;

use App\Models\Post\Post;
use App\Models\Utility\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Selection extends Model
{
    use HasFactory;
    protected $table='selections';

    protected $fillable = [
        'caption',"selection_type_id",'active_status_id'
    ];
    public function selection_selector_post() {

        return $this->hasMany(SelectionSelector::class)->whereNotNull('post_id');
    }
    public function selection_selector_committee() {

        return $this->hasMany(SelectionSelector::class)->whereNotNull('committee_id');
    }
//    public function selection_selector_floating_post_type() {
//
//        return $this->hasMany(SelectionSelector::class)->whereNotNull('floating_post_type_id');
//    }
    public function selection_type() {

        return $this->belongsTo(SelectionType::class);
    }
    public function post() {

        return $this->hasMany(Post::class);
    }
    public function status() {

        return $this->belongsTo(Status::class,'active_status_id');
    }
    public function post_selection_setting() {

        return $this->hasMany(SelectionPostSetting::class);
    }
    public function selection_indicators() {

        return $this->hasMany(SelectionIndicator::class);
    }
    public function active_selection(SelectionIndicator $selection_indicator)
    {
        $selection_indicators = SelectionIndicator::where("selection_id" , $selection_indicator->selection->id)->get();
        $sum_weight = $selection_indicators->sum('weight');
        if ($sum_weight == 100) {
            return 1200;
        } else {
            return 1210;
        }
    }
}
