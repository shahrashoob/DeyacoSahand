<?php

namespace App\Models\LineProduct\Product;

use App\Models\LineProduct\Product;
use App\Models\User;
use App\Models\Utility\Option;
use Carbon\Carbon;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShadeNumber extends Model
{
    use HasFactory;
    use Loggable;
    use SoftDeletes;
    protected $fillable=["product_id", "code"];
    protected $table="shade_numbers";

    public function product(){
        return $this->belongsTo(Product::class);
    }
    public function worker(){
        return $this->belongsTo(User::class,"user_id");
    }

    public static function ExistsCode( $code,$product_id, $id =false) {
        if ( $id ) {
            return ShadeNumber::where( ["product_id"=>$product_id,"code"=>$code ])->where( "id", "!=", $id )->exists();
        }

        return ShadeNumber::where( ["product_id"=>$product_id,"code"=>$code ] )->exists();
    }

    public function getMachineLotEffectiveCode(){
        return Option::getFormatCode($this->machine_lot_effective_code,2);
    }
    public function get_create_date_and_time() {
        return jdate( Carbon::parse( $this->created_at )->timestamp )->format( 'H:i Y/m/d ' );
    }
}
