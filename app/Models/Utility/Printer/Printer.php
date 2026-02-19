<?php

namespace App\Models\Utility\Printer;

use App\Models\Utility\SmartObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Printer extends Model {
    use HasFactory;

    protected $fillable = [ "width", "height", "caption", "printer_type_id","password" ];

    public function printer_type() {
        return $this->belongsTo( PrinterType::class );
    }

    public function getCode() {
        $this->code = $this->id;
        $this->save();

        return $this->code;
    }

    public static function ExistsCode( $caption, $id = false ) {
        if ( $id ) {
            return Printer::where( "caption", $caption )->where( "id", "!=", $id )->exists();
        }

        return Printer::where( "caption", $caption )->exists();
    }


}
