<?php

namespace App\Models\Purchase;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\Utility\Status;

class PurchaseOrder extends Model
{
    use HasFactory;
    use Loggable;

    public function code(){
        return $this->id+1000;
    }
    public function get_created_date(){
        return jdate( Carbon::parse($this->created_at)->timestamp)->format(' Y/m/d');
    }

    public function status(){
        return $this->belongsTo( Status::class,"status_id","id" );
    }

    public function orderProduct(){
        return $this->hasMany(PurchaseOrderProduct::class,"perchase_order_id");
    }
}
