<?php

namespace App\Models\LineProduct\Station\Operation;

use App\Models\LineProduct\StationOperation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class StationSubOperation extends Model {
    use HasFactory;

    protected $fillable=["caption","station_operation_id"];
    public function station_operation() {
        return $this->belongsTo( StationOperation::class );
    }

    public function getCode() {

        if ( $this->code != "" ) {
            return $this->code;
        }
        $code_number = StationSubOperation::where( "station_operation_id", $this->station_operation_id )->where( "id", "<", $this->id )->count() + 1;
        $code        = $string = Str::of( $code_number )
                                    ->when( $code_number < 100, function ( $string ) {
                                        return Str::of( '0' )->append( $string );
                                    } )
                                    ->when( $code_number < 10, function ( $string ) {
                                        return Str::of( '0' )->append( $string );
                                    } );
        $this->code  = $this->station_operation->code . "" . $code;
        $this->save();

        return $this->code;
    }
}
