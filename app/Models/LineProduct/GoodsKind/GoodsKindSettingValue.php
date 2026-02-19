<?php

namespace App\Models\LineProduct\GoodsKind;

use App\Models\LineProduct\GoodsKind;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoodsKindSettingValue extends Model
{
    use HasFactory;

    protected $fillable = ["goods_kind_id", "goods_kind_setting_id", "value"];

    public static function getValues(GoodsKind $goods_kind)
    {
        $values = GoodsKindSettingValue::where("goods_kind_id", $goods_kind->id)->
        join("goods_kind_settings", "goods_kind_settings.id", "goods_kind_setting_id")->
        pluck("value", "code")->toArray();

        foreach (GoodsKindSetting::all() as $item) {
            if (!isset($values[$item->code])) {

                if (in_array($item->id, [8, 9])) {
                    $values[$item->code] = [1]; // واحد اصلی
                }
                else {
                    $values[$item->code] = [];
                }
            } else {
                $values[$item->code] = json_decode($values[$item->code], true);
            }
        }

        return $values;
    }

    public static function getArrayValue($goods_kind_id, $code)
    {
        $goods_kind_setting_value = GoodsKindSettingValue::where("goods_kind_id", $goods_kind_id)->
        join("goods_kind_settings", "goods_kind_settings.id", "goods_kind_setting_id")->
        where("code", $code)->first();

        if ($goods_kind_setting_value) {
            return json_decode($goods_kind_setting_value->value, true);
        } else {
            return [-1];
        }

    }

    public static function setValues(GoodsKind $goods_kind, $code, $value)
    {

        $goods_kind_setting_value = GoodsKindSettingValue::where("goods_kind_id", $goods_kind->id)->
        join("goods_kind_settings", "goods_kind_settings.id", "goods_kind_setting_id")->
        where("code", $code)->
        select("goods_kind_setting_values.*")->
        first();
        if ($goods_kind_setting_value) {
            $goods_kind_setting_value->value = json_encode($value);
            $goods_kind_setting_value->save();

        } else {
            $goods_kind_setting = GoodsKindSetting::where("code", $code)->first();
            GoodsKindSettingValue::create([
                "goods_kind_id" => $goods_kind->id,
                "goods_kind_setting_id" => $goods_kind_setting->id,
                "value" => json_encode($value)
            ]);
        }
    }
}
