<?php

namespace App\Models\HR\Employment;

use App\Models\HR\Committee\Committee;
use App\Models\Post\Post;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmploymentSelectionSelector extends Model
{
    use HasFactory;
    protected $fillable = [
        'post_id',
        'selection_id',
        'minimum_percent_of_committee',
        'committee_id',
        'employment_id',
        'employment_selection_id',

    ];
    protected $table = 'employment_selection_selector';


    public function post() {

        return $this->belongsTo(Post::class);
    }

    public function committee() {

        return $this->belongsTo(Committee::class);
    }
}
