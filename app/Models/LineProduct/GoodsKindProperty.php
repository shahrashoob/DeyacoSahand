<?php

namespace App\Models\LineProduct;

use App\Models\LineProduct\GoodsKind\GoodsKindPropertyDependentValue;
use App\Models\LineProduct\GoodsKind\GoodsKindPropertyProductType;
use App\Models\Utility\FieldType;
use App\Models\Utility\SpecialUnit;
use App\Models\Utility\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GoodsKindProperty extends Model {
    use HasFactory;
    use SoftDeletes;
    protected $table = "goods_kind_properties";
    protected $fillable = [
        "goods_kind_id",
        "caption",
        "field_type_id",
        "special_unit_id",
        "min_value",
        "max_value",
        "priority_number",
        "parent_id",
        "status_id"
    ];

    public function property_product_type() {
        return $this->hasMany( GoodsKindPropertyProductType::class );
    }

    public static function ExistsCode( $goods_kind_id, $caption, $id = false ) {
        if ( $id ) {
            return GoodsKindProperty::where( "caption", $caption )->where( "id", "!=", $id )->where( "goods_kind_id", $goods_kind_id )->exists();
        }

        return GoodsKindProperty::where( "caption", $caption )->where( "goods_kind_id", $goods_kind_id )->exists();
    }

    public function field_type() {
        return $this->belongsTo( FieldType::class );
    }

    public function parent() {
        return $this->belongsTo( GoodsKindProperty::class, "parent_id", "id" );
    }

    public function special_unit() {
        return $this->belongsTo( SpecialUnit::class );
    }
    public function status() {
        return $this->belongsTo( Status::class );
    }

    public function option() {
        return $this->hasMany( GoodsKindPropertyOption::class );
    }

    public function dependent_values() {
        return $this->hasMany( GoodsKindPropertyDependentValue::class, "goods_kind_property_id" );
    }

    public function dependent_values_html() {
        $html = "";
        $i=0;
        if ( $this->parent->field_type_id == 1 ) {
            foreach ( $this->dependent_values as $item ) {
                $html.=++$i."  - مقدار ".$item->compare." ".($item->parent_value)." و<br/>  ";
            }
        }
        if ( $this->parent->field_type_id == 3 ) {
            foreach ( $this->dependent_values as $item ) {
                $html.=++$i."  - مقدار ".($item->option->caption??"---")."<br/>";
            }
        }
        if ( $this->parent->field_type_id == 5 ) {
            foreach ( $this->dependent_values as $item ) {
                $html.=$item->parent_value==0?"داده نامعتبر؛ لطفا بررسی شود.":
                    ($item->parent_value==1?" انتخاب شده (True)":"انتخاب نشده (False)");

            }
        }
        return $html;
    }

}
