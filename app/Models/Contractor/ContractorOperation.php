<?php

namespace App\Models\Contractor;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ContractorOperation extends Model {
    use HasFactory;
    use Loggable;

    protected $fillable = [ "contractor_id", "caption", "code" ];

    public function contractor(){
        return $this->belongsTo(Contractor::class);
    }
    public function getCode() {

        if ( $this->code != "" ) {
            return $this->code;
        }
        $code_number = ContractorOperation::where( "contractor_id", $this->contractor_id )->where( "id", "<", $this->id )->count()+1;
        $code        = $string = Str::of( $code_number )
                                    ->when( $code_number < 100, function ( $string ) {
                                        return Str::of( '0' )->append( $string );
                                    } )
                                    ->when( $code_number < 10, function ( $string ) {
                                        return Str::of( '0' )->append( $string );
                                    } );
        $this->code  = $this->contractor->code . "" . $code;
        $this->save();

        return $this->code;
    }
}
