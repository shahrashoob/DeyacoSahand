<?php

namespace App\Models\Accounting;

use App\Models\Utility\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CostCenter extends Model
{
    use HasFactory;
    protected $fillable = [ "caption", "code","status_id" ];

    public function fullCaption() {
        return $this->code . " - " . $this->caption;
    }

    public function status() {
        return $this->belongsTo( Status::class );
    }

    public static function ExistsCode( $code, $id =false) {
        if ( $id ) {
            return CostCenter::where( "code", $code )->where( "id", "!=", $id )->exists();
        }

        return CostCenter::where( "code", $code )->exists();
    }
}
