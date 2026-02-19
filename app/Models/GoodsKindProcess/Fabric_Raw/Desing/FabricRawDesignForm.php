<?php

namespace App\Models\GoodsKindProcess\Fabric_Raw\Desing;

use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Product;
use App\Models\Production\Production;
use App\Models\Utility\Status;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FabricRawDesignForm extends Model {
    use HasFactory;
    use Loggable;
    protected $fillable = [
        "machine_id",
        "design_available",
        "it_has_pinning",
        "warps_is_in_warehouse",
        "need_to_convert",
        "status_id",
        "allocation_id"
    ];
public static $perfix_status_code="7004";

    public function getCode() {
        if ( isset( $this->code ) ) {
            return $this->code;
        }

        $this->code = "EDF/" . ( 1000 + $this->id );
        $this->save();

        return $this->code;
    }


    public function production_list(){
        return $this->hasMany(FabricRawDesignFormProduction::class);
    }
    public function machine(){
        return $this->belongsTo(Machine::class);
    }
    public function allocation(){
        return $this->belongsTo(Allocation::class);
    }
    public function status(){
        return $this->belongsTo(Status::class);
    }
    public function product(){
        return $this->belongsTo(Product::class);
    }

    public function getAnswer($question){
        $answer="در انتظار پاسخ";
        switch ($question){
            case "design_available":
                if(isset($this->design_available)){
                    $answer=$this->design_available?"بله":"خیر";
                }
                break;
            case "it_has_pinning":
                if(isset($this->it_has_pinning)){
                    $answer=$this->it_has_pinning?"بله":"خیر";
                }
                break;
            case "warps_is_in_warehouse":
                if(isset($this->warps_is_in_warehouse)){
                    $answer=$this->warps_is_in_warehouse?"بله":"خیر";
                }
                break;
            case "need_to_convert":
                if(isset($this->need_to_convert)){
                    $answer=$this->need_to_convert?"بله":"خیر";
                }
                break;

        }
        return $answer;
    }

    public static function getDesignFormFromMachine(Machine $machine){

      return  FabricRawDesignForm::where("machine_id",$machine->id)->orderByDesc("allocation_id")->first();

    }
}
