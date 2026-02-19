<?php

namespace App\Models\LineProduct\Product\BOM;

use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\ProductRoute;
use App\Models\LineProduct\Station;
use App\Models\LineProduct\StationOperation;
use App\Models\Utility\Status;
use App\Models\Warehouse\Warehouse;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BOM extends Model
{
    use HasFactory;

    protected $table = "bill_of_materials";
    protected $fillable = ["product_id", "product_route_id", "caption", "active_status_id"];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function active_status()
    {
        return $this->belongsTo(Status::class, "active_status_id");
    }

    public function product_route()
    {
        return $this->belongsTo(ProductRoute::class);
    }

    public function items()
    {
        return $this->hasMany(BOMItem::class, "bill_of_material_id");
    }
    public function bom_logs()
    {
        return $this->hasMany(BOMLog::class, "bill_of_material_id");
    }

    public function replaces()
    {
        return $this->hasMany(BOMReplace::class, "bill_of_material_id");
    }

    public function permutations()
    {
        return $this->hasMany(BOMPermutation::class, "bill_of_material_id");
    }

    public static function getStructureBOMItem(BOM $BOM)
    {
        return BOMItem::where([
            "is_structure_product" => 1,
            "bill_of_material_id" => $BOM->id
        ])->first();
    }

    public function code()
    {

        if ($this->code) {
            return $this->code;
        }
        $code = 1 + BOM::where("product_id", $this->product_id)->
            where("id", "<", $this->id)->count();

        $code = Str::of($code)
            ->when($code < 10, function ($string) {
                return Str::of('0')->append($string);
            });
        $code = $this->product_route->code() . $code;
        $this->code = $code;
        $this->save();

        return $code;

    }

    public function fullCaption()
    {
        return $this->caption . "-کد " . $this->code();
    }

    public static function exist($product_id, $caption, $id = null)
    {
        if ($id) {
            return BOM::
            where("product_id", $product_id)->
            where("caption", $caption)->
            where("id", "!=", $id)->
            exists();
        }

        return BOM::
        where("caption", $caption)->
        where("product_id", $product_id)->
        exists();
    }

    public static function GetConsumptionOfProductionChannel($bom, $product_ids = null)
    {
        $value = BOMItem::
        when($bom, function ($query) use ($bom) {
            return $query->where("bill_of_material_id", $bom->id);
        })->
        when($product_ids, function ($query) use ($product_ids) {
            $product_ids[] = -1;
            return $query->whereIn("product_id", $product_ids);
        })->
        selectRaw("max(amount* number * percent_of_use * consumption_percent_of_production_channel/100) as percent")->
        first();

        if ($value->percent == 0) {
            if ($bom) {
                $product = $bom->product;
            } else {
                $product = Product::whereIn("id", $product_ids)->first();
            }

            return [
                "result" => false,
                "error" => " ضریب مصرف کانال تولید برای " . ($product->caption ?? "") .
                    " صفر شده است و امکان ثبت تخصیص وجود ندارد
                    ،<br/> لطفا مقدار ضریب مصرف کانال تولید برای این کالا در بخش BOM اصلاح نمایید." .
                    "و دوباره تلاش کنید."
            ];
        } else {

            return [
                "result" => true,
                "value" => $value->percent
            ];
        }

    }

}
