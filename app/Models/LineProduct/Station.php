<?php

namespace App\Models\LineProduct;

use App\Models\Accounting\CostCenter;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineProductProperties;
use App\Models\LineProduct\Machine\MachineProperty;
use App\Models\LineProduct\Machine\MachineType;
use App\Models\LineProduct\Station\Operation\StationStationOperationCategory;
use App\Models\Utility\Status;
use App\Models\Warehouse\Warehouse;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Station extends Model {
    use HasFactory;
    use Loggable;
    protected $fillable = [ "caption", "line_id", "active_status_id","ic" ];

    public static function ExistsCaption( $code, $id = false ) {
        if ( $id ) {
            return Station::where( "code", $code )->where( "id", "!=", $id )->exists();
        }

        return Station::where( "code", $code )->exists();
    }

    public function cost_center() {
        return $this->belongsTo( CostCenter::class, "ic" );
    }
    public function active_status() {
        return $this->belongsTo( Status::class,"active_status_id" );
    }

    public function line() {
        return $this->belongsTo( Line::class );
    }
    public function machine_type() {
        return $this->hasMany( MachineType::class );
    }
    public function machine() {
        return $this->hasMany( Machine::class );
    }

    public function machine_property() {
        return $this->hasMany( MachineProperty::class );
    }
    public function machine_product_property() {
        return $this->hasMany( MachineProductProperties::class );
    }

    public function operations() {
        return $this->hasMany( StationOperation::class );
    }
    public function station_categories() {
        return $this->hasMany( StationStationOperationCategory::class );
    }
    public function getMachineCount() {
        return Machine::where( "station_id", $this->id )->count();
    }
    public function fullCaption() {
        return $this->code . " - " . $this->caption;
    }

    public function warehouses() {
        return $this->hasMany( Warehouse::class ,"belonging_to_id")->where("warehouse_type_id",4);
    }
    public function getCode() {

        if ( $this->code != "" ) {
            return $this->code;
        }
        $code_number = Station::where( "line_id", $this->line_id )->where( "id", "<", $this->id )->count()+1;
        $code        = $string = Str::of( $code_number )
                                    ->when( $code_number < 10, function ( $string ) {
                                        return Str::of( '0' )->append( $string );
                                    } );
        $this->code  = $this->line->code . "" . $code;
        $this->save();

        return $this->code;
    }
    public static function GetIdFromCode( $code ) {

        $station = Station::where( "code", $code )->first();

        return isset( $station ) ? $station : null;
    }
    public function get_access_level($post_id){
        $result_full = LinePost::
        where( [ "post_id" => $post_id, "line_id" =>$this->line_id,"station_id"=>$this->id ] )->
        whereNull( "machine_type_id" )->
        whereNull( "machine_id" )->
        exists();
        if ( $result_full ) {
            return "full";
        }

        $result_empty = LinePost::
        where( [ "post_id" => $post_id, "line_id" =>$this->line_id,"station_id"=>$this->id ] )->
        exists();
        if ( ! $result_empty ) {
            return "empty";
        }

        return "";
    }
}
