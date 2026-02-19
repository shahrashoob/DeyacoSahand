<?php

namespace App\Models\LineProduct\Packing;

use App\Models\Utility\Printer\Printer;
use App\Models\Worker;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackingTypeLabelPrintingType extends Model
{
    use HasFactory;
    protected $table="packing_type_label_printing_types";

    public function getPrinter(Worker $worker){
        if($this->printer_type_id == 1){
           return Printer::find( $worker->default_printer_id );
        }else{
            return Printer::find( $worker->default_label_printer_id );
        }
    }
}
