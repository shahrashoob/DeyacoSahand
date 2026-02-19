<?php

namespace App\Models\LineProduct\Machine\Allocation\Modification;

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

class MachineAllocationModificationForm extends Model {
    use HasFactory;

    protected $table = "machine_allocation_modification_form";
    protected $fillable = [
            "machine_allocation_modification_id",
            "product_id",
            "input_form_id",
            "output_form_id",
        "input_form_status_id"
    ];


    public function machine_allocation_modification() {
        return $this->belongsTo( MachineAllocationModification::class );
    }

    public function product() {
        return $this->belongsTo( Product::class );
    }

    public function input_form_status() {
        return $this->belongsTo( Status::class,"input_form_status_id" );
    }

    public function input_form() {
        return $this->belongsTo( Form::class ,"input_form_id");
    }
    public function output_form() {
        return $this->belongsTo( Form::class ,"output_form_id");
    }

    public function degree_change_input_form() {
        return $this->belongsTo( Form::class ,"degree_change_input_form_id");
    }
    public function degree_change_output_form() {
        return $this->belongsTo( Form::class ,"degree_change_output_form_id");
    }

    public function amendment_input_form() {
        return $this->belongsTo( Form::class ,"amendment_input_form_id");
    }
    public function amendment_output_form() {
        return $this->belongsTo( Form::class ,"amendment_output_form_id");
    }
    public function change_waste_input_form() {
        return $this->belongsTo( Form::class ,"change_waste_input_form_id");
    }
    public function change_waste_output_form() {
        return $this->belongsTo( Form::class ,"change_waste_output_form_id");
    }
    public function other_correction_input_form() {
        return $this->belongsTo( Form::class ,"other_correction_input_form_id");
    }
    public function other_correction_output_form() {
        return $this->belongsTo( Form::class ,"other_correction_output_form_id");
    }

}
