<?php

namespace App\Models\Contractor;

use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Product;
use App\Models\Production\Production;
use App\Models\Utility\Status;
use Carbon\Carbon;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractorAllocation extends Model
{
    use HasFactory;
    use Loggable;
    protected $table="machine_allocation";
    protected $fillable=["allocation_id","production_id","contractor_id","product_id","status_id","user_id","allocation_amount","production_start_date"];

    public function contractor() {
        return $this->belongsTo( Contractor::class );
    }
    public function status() {
        return $this->belongsTo( Status::class );
    }

    public function product() {
        return $this->belongsTo( Product::class );
    }

    public function allocation() {
        return $this->belongsTo( Allocation::class );
    }

    public function production() {
        return $this->belongsTo( Production::class );
    }

    public function logs(){
        return $this->hasMany( MachineAllocationLog::class,"machine_allocation_id" );
    }

    public function packing_forms(){
        return $this->hasMany(MachineAllocationPackingForm::class,"machine_allocation_id","id");
    }

    ################### date time
    public function get_create_date() {
        return jdate( Carbon::parse( $this->created_at )->timestamp )->format( 'Y/m/d' );
    }

    public function get_create_time() {
        return jdate( Carbon::parse( $this->created_at )->timestamp )->format( 'H:i' );
    }

    public function get_create_date_and_time() {
        return jdate( Carbon::parse( $this->created_at )->timestamp )->format( 'H:i Y/m/d ' );

    }
    public function getContractorPackingForm(){
      return   MachineAllocationPackingForm::where( [
            "contractor_id"         => $this->contractor_id,
            "machine_allocation_id" => $this->id
        ] )->
        where( "status_id", "!=", "7007006" )->//معلق
        get();
    }
    public function getContractorPackingFormCount(){
      return   MachineAllocationPackingForm::where( [
            "contractor_id"         => $this->contractor_id,
            "machine_allocation_id" => $this->id
        ] )->
        where( "status_id", "!=", "7007006" )->//معلق
        count();
    }
}
