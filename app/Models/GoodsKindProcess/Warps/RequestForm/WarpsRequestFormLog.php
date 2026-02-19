<?php

namespace App\Models\GoodsKindProcess\Warps\RequestForm;

use App\Models\Utility\Status;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarpsRequestFormLog extends Model
{
    use HasFactory;
    protected $table="product_request_form_logs";
    protected $fillable = [
        "product_request_form_id",
        "design_available",
        "it_has_pinning",
        "warps_is_in_warehouse",
        "need_to_convert",
        "status_id",
        "user_id",
        "message+id"
    ];

    public function worker(){
        return $this->belongsTo(Worker::class,"user_id","id");
    }
    public function status(){
        return $this->belongsTo(Status::class);
    }
    public function get_datetime(){
        return jdate( Carbon::parse( $this->created_at )->timestamp )->format( 'H:i Y/m/d ' );
    }
}
