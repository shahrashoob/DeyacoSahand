<?php

namespace App\Models\Utility;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DateTime extends Model {
    use HasFactory;

    public static function getDateTimeFromRequest( Request $request, $id ) {
        $id_value   = $id . "_value";
        $time_split = Str::of( $request->$id_value )->split( "/[\s:]+/" );

        $date_time = $request->$id;
        $date      = Str::of( $date_time )->split( "/[\s:]+/" );;
        if ( count( $time_split ) == 4 && count( $date ) == 4 ) {
            $date_time = $date[3] . " " . $time_split[0] . ":" . $time_split[1] . ":" . $time_split[2];
        }

        return Carbon::parse( $date_time );

    }
}
