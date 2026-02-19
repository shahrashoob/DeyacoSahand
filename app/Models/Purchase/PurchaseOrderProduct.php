<?php

namespace App\Models\Purchase;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\LineProduct\Product;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Utility\Status;

class PurchaseOrderProduct extends Model
{
    use HasFactory;
    protected $table="purchase_order_product";

    
    public function product(){
        return $this->belongsTo( Product::class,"product_id","id" );

    }
    public function perchaseOrder(){
        return $this->belongsTo( PurchaseOrder::class,"perchase_order_id","id" );

    }
    
    public function get_created_date(){
        return jdate( Carbon::parse($this->created_at)->timestamp)->format(' Y/m/d');
    }
    
    public function status(){
        return $this->belongsTo( Status::class,"status_id","id" );
    }
    public function code(){

        if(!$this->code){
//return $this->perchaseOrder->id??"45";
            $number_all=PurchaseOrderProduct::where("perchase_order_id",$this->perchase_order_id)->
                            where("id","<",$this->id)->count();


                $number=Str::of($number_all)
            ->when($number_all<10, function ($string) {
                return Str::of('00')->append($string);
            })->when($number_all>=10 && $number_all < 100, function ($string) {
                return Str::of('0')->append($string);
            })->when($number_all>=1000, function ($string) {
                return Str::of('OF')->append($string);
            });
            $this->code=$this->perchaseOrder->code().   $number;
            $this->save();

        }
            return $this->code;
    }
}
