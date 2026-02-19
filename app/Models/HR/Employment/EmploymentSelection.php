<?php

namespace App\Models\HR\Employment;

use App\Models\HR\Selection\Selection;
use App\Models\Utility\Status;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmploymentSelection extends Model
{
    use HasFactory;

    protected $fillable = [

        'selection_id',
        'minimum_score_to_confirm_selection',
        'priority_number',
        'status_id',
        'employment_id',
        'coordination_time',
        'score_obtained_to_confirm_selection',
        'user_id',
    ];
    protected $table = 'employment_selection';
    public function selection() {

        return $this->belongsTo(Selection::class,'selection_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class,'status_id');
    }

    public function employment_selection_selectors()
    {
        return $this->hasMany(EmploymentSelectionSelector::class);
    }
    public function employment_selection_indicator_values()
    {
        return $this->hasMany(EmploymentSelectionIndicatorValue::class);
    }

    public function employment()
    {
        return $this->belongsTo(Employment::class);
    }

    public function worker()
    {
        return $this->belongsTo(Worker::class, 'user_id');
    }


    public function get_coordination_time(){
        if($this->coordination_time){
            return jdate(Carbon::parse($this->coordination_time)->timestamp)->format('H:i Y/m/d');
        }
        else{
            return "";
        }
    }

    public function get_coordination_time_date(){
        if($this->coordination_time){
            return jdate(Carbon::parse($this->coordination_time)->timestamp)->format('Y/m/d');
        }
        else{
            return "";
        }
    }
    public function get_coordination_time_time(){
        if($this->coordination_time){
            return jdate(Carbon::parse($this->coordination_time)->timestamp)->format('H:i');
        }
        else{
            return "";
        }
    }




}
