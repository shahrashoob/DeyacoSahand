<?php

namespace App\Models\Utility\SpecialLicense;

use App\Models\Post\FloatingPostType;
use App\Models\Post\Post;
use App\Models\HR\Committee\Committee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecialLicenseTypeExpert extends Model
{
    use HasFactory;

    protected $fillable = ["special_license_type_id", "post_id", "committee_id", "priority_number","floating_post_type_id","min_percent_of_committee"];
    public function special_license_type(){
        return $this->belongsTo(SpecialLicenseType::class);
    }
    public function post(){
        return $this->belongsTo(Post::class);
    }
    public function committee(){
        return $this->belongsTo(Committee::class);
    }
    public function floating_post_type(){
        return $this->belongsTo(FloatingPostType::class);
    }
}
