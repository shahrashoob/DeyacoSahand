<?php

namespace App\Models\LineProduct\Machine;

use App\Models\Form\Packing\PackingForm;
use App\Models\LineProduct\LotNumber;
use App\Models\LineProduct\Product;
use App\Models\Production\Production;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurrentMachineInputLog extends Model
{
    use HasFactory;
    protected $fillable = [
        "current_machine_input_id",
        "machine_id",
        "allocation_id",
        "production_id",
        "production_form_id",
        "product_id",
        "material_id",
        "lot_number_id",
        "entry_packing_form_id",
        "packing_form_id",
        "user_id",
        "machine_log_id"
    ];

    public function get_datetime() {
        return jdate( Carbon::parse( $this->created_at )->timestamp )->format( 'H:i Y/m/d ' );

    }

    public function worker() {
        return $this->belongsTo( Worker::class, "user_id", "id" );
    }


    public function production() {
        return $this->belongsTo( Production::class );
    }
    public function product() {
        return $this->belongsTo( Product::class );
    }
    public function material() {
        return $this->belongsTo( Product::class ,"material_id");
    }

    public function packing_form() {
        return $this->belongsTo( PackingForm::class );
    }

    public function entry_packing_form() {
        return $this->belongsTo( PackingForm::class,"entry_packing_form_id" );
    }
    public function lot_number() {
        return $this->belongsTo( LotNumber::class );
    }



}
