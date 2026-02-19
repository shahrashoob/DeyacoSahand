<?php

namespace App\Models\Utility\Printer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrinterFile extends Model {
    use HasFactory;

    protected $fillable = [ "user_id", "filename", "status_id", "is_landscape", "printer_id","number_of_prints" ];

    public function printer() {
        return $this->belongsTo( Printer::class );
    }
}
