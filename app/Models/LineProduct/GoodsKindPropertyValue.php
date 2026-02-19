<?php

namespace App\Models\LineProduct;

use App\Models\File\File;
use App\Models\Utility\SpecialUnit;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;

class GoodsKindPropertyValue extends Model {
    use HasFactory;
    use Loggable;
    protected $table = "goods_kind_property_values";
    protected $fillable = [ "product_id", "goods_kind_property_id", "value" ];

    public function property() {
        return $this->belongsTo( GoodsKindProperty::class, "goods_kind_property_id", "id" );
    }

    public function product() {
        return $this->belongsTo( Product::class );
    }

    public function special_unit() {
        return $this->belongsTo( SpecialUnit::class );
    }

    public function getValue() {
        if ( ! isset( $this->property ) ) {
            return "";
        }
        $value="";
        switch ( $this->property->field_type_id ) {
            case 3:
            $option = GoodsKindPropertyOption::find( $this->value );
            $value= $option ? $option->caption : "";
            break;
            case 4:
                $file=File::find($this->value);
                $url=isset($file)?route("utility.file.product.show_property",[$this->product,$this->property]):"#";
                $value="  <a href=".$url." >مشاهده</a>";
                break;
            default:
                $value=$this->value." ".($this->property->special_unit->caption??"");
        }


        return $value;
    }
}
