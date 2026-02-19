<?php

namespace App\Models\Utility\Other;

use App\Models\Customer\Customer;
use App\Models\Utility\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarcodeLink extends Model
{
    use HasFactory;
    protected $fillable=["caption","status_id","barcode_link_type_id","customer_id"];

    public function status(){
       return $this->belongsTo(Status::class);
    }
    public function customer(){
       return $this->belongsTo(Customer::class);
    }
    public function barcode_link_type(){
       return $this->belongsTo(BarcodeLinkType::class);
    }
    public static function ExistsCode( $caption, $id = false ) {
        if ( $id ) {
            return BarcodeLink::where( "caption", $caption )->where( "id", "!=", $id )->exists();
        }

        return BarcodeLink::where( "caption", $caption )->exists();
    }
}
