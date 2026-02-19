<?php

namespace App\Models\LineProduct\Machine;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MachineLotTmps extends Model
{
    use HasFactory;
    protected $table="machine_lot_tmps";
    protected $fillable=["machine_id"];

    public function clearLot(){
        $this->yarn_id_1=null;
        $this->yarn_id_2=null;
        $this->yarn_id_3=null;
        $this->yarn_id_4=null;
        $this->yarn1_lot_number_id=null;
        $this->yarn2_lot_number_id=null;
        $this->yarn3_lot_number_id=null;
        $this->yarn4_lot_number_id=null;

        $this->save();
    }
}
