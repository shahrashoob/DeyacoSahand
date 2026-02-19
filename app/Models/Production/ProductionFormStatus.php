<?php

namespace App\Models\Production;

use App\Models\LineProduct\GoodsKind;
use App\Models\Utility\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionFormStatus extends Model
{
    use HasFactory;
    protected $fillable=["status_id","goods_kind_id"];
    protected $table="production_form_status";

    public static function Exists( $caption, GoodsKind $goods_kind, $status_id = false ) {

        $table = ProductionFormStatus::join( "status", "status.id", "=", "status_id" )->where( [
            "caption"       => $caption,
            "goods_kind_id" => $goods_kind->id
        ] );
        if ( $status_id ) {
            return $table->where( "status_id", "!=", $status_id )->exists();
        }

        return $table->exists();
    }

    public function status() {
        return $this->belongsTo( Status::class );
    }
    public function goods_kind() {
        return $this->belongsTo( GoodsKind::class);
    }
}
