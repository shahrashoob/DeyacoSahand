<?php

namespace App\Models\Utility;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecialUnit extends Model
{
    use HasFactory;
    protected $table="special_units";
    protected $fillable=["caption"];
    public static function Exists( $code, $id =false) {
        if ( $id ) {
            return SpecialUnit::where( "caption", $code )->where( "id", "!=", $id )->exists();
        }

        return SpecialUnit::where( "caption", $code )->exists();
    }
}
