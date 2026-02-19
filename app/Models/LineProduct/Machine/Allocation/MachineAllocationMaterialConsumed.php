<?php

namespace App\Models\LineProduct\Machine\Allocation;

use App\Models\Form\Form;
use App\Models\Form\Packing\PackingForm;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Product;
use App\Models\Utility\Status;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MachineAllocationMaterialConsumed extends Model {
    use HasFactory;

    protected $table = "machine_allocation_material_consumed";
    protected $fillable = [
        "allocation_id",
        "machine_id",
        "start_machine_log_id",
        "end_machine_log_id",
        "status_id",
        "amount_production"
    ];

    public function allocation() {
        return $this->belongsTo( Allocation::class );
    }

    public function machine() {
        return $this->belongsTo( Machine::class );
    }

    public function status() {
        return $this->belongsTo( Status::class );
    }

    public function product() {
        return $this->belongsTo( Product::class );
    }

    public function packing_form() {
        return $this->belongsTo( PackingForm::class );
    }

    public function start_machine_log() {
        return $this->belongsTo( MachineLog::class, "start_machine_log_id" );
    }
    public function forms() {
        return $this->belongsToMany( Form::class,"machine_allocation_material_consumed_form" );
    }

    public function end_machine_log() {
        return $this->belongsTo( MachineLog::class, "end_machine_log_id" );
    }
    public function get_create_date() {
        return jdate( Carbon::parse( $this->created_at )->timestamp )->format( 'H:i Y/m/d ' );
    }
    public function get_updated_date() {
        return jdate( Carbon::parse( $this->updated_at )->timestamp )->format( 'H:i Y/m/d ' );
    }


    public static function registerNewConsumed( $allocation, $machine, $start_machine_log, $end_machine_log,$amount_production=null ) {
        if (  $machine->check_inventory_for_allocation ) {
            MachineAllocationMaterialConsumed::create( [
                "allocation_id"        => $allocation->id??null,
                "machine_id"           => $machine->id,
                "start_machine_log_id" => $start_machine_log->id??null,
                "end_machine_log_id"   => $end_machine_log->id??null,
                "status_id"            => 6020001,//در انتظار ثبت مصرف
                "amount_production"=>$amount_production
            ] );
        }
    }
}
