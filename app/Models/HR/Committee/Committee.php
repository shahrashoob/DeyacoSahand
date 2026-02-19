<?php

namespace App\Models\HR\Committee;

use App\Models\Post\Post;
use App\Models\Utility\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Committee extends Model
{
    use HasFactory;

    protected $fillable = ["caption","active_status_id"];

    public function committee_post()
    {
        return $this->hasMany(CommitteePost::class);
    }
    public function active_status()
    {
        return $this->belongsTo(Status::class,"active_status_id");
    }
    public static function ExistsCode( $caption, $id =false) {
        if ( $id ) {
            return Committee::where( "caption", $caption )->where( "id", "!=", $id )->exists();
        }

        return Committee::where( "caption", $caption )->exists();
    }
    public function getCode() {

        if ( $this->code != "" ) {
            return $this->code;
        }

        $code        = 1000+$this->id;
        $this->code  = $code;
        $this->save();

        return $this->code;
    }
}
