<?php

namespace App\Models\Production;

use App\Models\Form\Packing\PackingFormItem;
use App\Models\GoodsKindProcess\Fabric_Raw\FabricRaw;
use App\Models\LineProduct\GoodsKind;
use App\Models\LineProduct\LotNumber;
use App\Models\LineProduct\Machine\MachineLog;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionFormItemLotNumber extends Model {
    use HasFactory;
    use Loggable;

    protected $table = "production_form_item_lot_number";
    protected $fillable = [
        "production_form_id",
        "production_form_item_id",
        "lot_number_id",
        "start_machine_log_id",
        "end_of_machine_log_id",
    ];

    public function lot_number() {
        return $this->belongsTo( LotNumber::class, "lot_number_id" );
    }

    public function production_form_item() {
        return $this->belongsTo( ProductionFormItem::class );
    }

    public function start_machine_log() {
        return $this->belongsTo( MachineLog::class, "start_machine_log_id", "id" );
    }
    public function end_of_machine_log() {
        return $this->belongsTo( MachineLog::class, "end_of_machine_log_id", "id" );
    }

    public function getAmount($end_of_machine_log=null) {

        if(!$end_of_machine_log) {
            $end_of_machine_log = $this->end_of_machine_log;
        }
        if ( ! $this->start_machine_log || ! $end_of_machine_log ) {
            return 0;
        }
        $amount = GoodsKind::getAmountFromMachineLog(
            $this->production_form_item->product, $this->start_machine_log, $end_of_machine_log
        );

        return $amount;

    }

    public function getPackingFormItem(){
        return PackingFormItem::where("production_form_item_lot_number_id",$this->id)->first();
    }
}
