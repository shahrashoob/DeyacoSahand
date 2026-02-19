<?php

namespace App\Models\Utility;

use App\Models\LineProduct\Product;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QueueOfLargeOperation extends Model {


    protected $fillable = [ "large_operation_type_id", "status_id", "data" ];

    public static function AddToQueue( $data ,$large_operation_type_id) {
        QueueOfLargeOperation::create( [
            "large_operation_type_id" => $large_operation_type_id,
            "status_id"               => 3500001,
            "data"                    => json_encode( $data )
        ] );
    }
    public function get_created_at($type=""){
        if($type=="for_file"){
            return jdate(Carbon::parse($this->created_at)->timestamp)->format('H:i Y_m_d ');
        }
        return jdate(Carbon::parse($this->created_at)->timestamp)->format('H:i Y/m/d ');
    }
    public function get_updated_at($type=""){
        return jdate(Carbon::parse($this->updated_at)->timestamp)->format('H:i Y/m/d ');
    }
}
