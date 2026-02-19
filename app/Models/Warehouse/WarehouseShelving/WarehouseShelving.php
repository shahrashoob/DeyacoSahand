<?php

namespace App\Models\Warehouse\WarehouseShelving;

use App\Models\Form\Packing\PackingFormItem;
use App\Models\Warehouse\Warehouse;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class WarehouseShelving extends Model
{
    use HasFactory;

    protected $table = 'warehouse_shelving';
    protected $fillable = [
        "warehouse_id",
        "caption",
        "code",
        "parent_id",
        "fullCode",
        "fullCaption",
        "warehouse_shelving_line_type_id",
        "warehouse_shelving_line_part",
        "warehouse_shelving_line_status_id",
        "warehouse_shelving_type_id",
        "updown_code",
        "can_product_directly_in_location",
    ];

    public function items()
    {
        return $this->hasMany(WarehouseShelving::class, "parent_id");
    }

    public function warehouse_shelving_type()
    {
        return $this->belongsTo(WarehouseShelvingType::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function get_next_warehouse_shelving_type()
    {
        return WarehouseShelvingType::
        where("id",$this->warehouse_shelving_type_id+1)->first();
    }

    public static function UpdateShelvingCode(WarehouseShelving $warehouseShelving)
    {
        return $warehouseShelving->code ?? "";
    }

    public function fullCode()
    {
        $this->fullCaption();
        return $this->fullCode;
    }

    public function fullCaption()
    {
        return $this->fullCaption ?? "";
    }

    public function parent()
    {
        return $this->belongsTo(WarehouseShelving::class, "parent_id");
    }

    public static function AllowCreateSubLines(WarehouseShelving $warehouseShelving,$request,$is_edit=false)
    {

//        if(!$is_edit && $warehouseShelving->warehouse_shelving_line_status_id == 1200 && $status_id == 1210){
//            return [
//                "result"=>false,
//                "error"=>"در صورتی می توان وضعیت نمایش کد را غیر فعال کنید که وضعیت نمایش کد ".$warehouseShelving->caption." نیز غیر فعال باشد"
//            ];
//        }
        if (!$is_edit && $warehouseShelving->warehouse_shelving_line_type_id == 5) {
            return [
                "result" => false,
                "error" => "امکان ثبت زیر مجموعه برای جایگاه وجود ندارد."
            ];
        }
        if($request->can_product_directly_in_location == 1 && $request->warehouse_shelving_line_status_id == 1210){
            return [
                "result" => false,
                "error" =>"در صورتی که امکان دارد کالا به صورت مستقیم در این محل قرار بگیرد، وضعیت کد سلول باید فعال باشد"
            ];
        }
        return [
            "result" => true
        ];
    }

    public function UpdateFullCode($parent_code = "")
    {
        $code = WarehouseShelving::
            where("parent_id", $this->parent_id)->
            where("warehouse_id", $this->warehouse_id)->
            where("id", "<", $this->id)->count() + 1;
        $this->code = $code;
        $result_coding = WarehouseShelving::getCoding($this);

        if (!$result_coding["result"]) {
            return false;
        }
        $per_code = $result_coding["code"];
        $this->fullCode = $parent_code . $per_code;
        $this->caption=$this->fullCaption = $this->warehouse_shelving_type->caption . " " . $code;
        $this->save();
        return $this;
    }

    public static function UpdateFullCodeFroAllSubLine(WarehouseShelving $warehouseShelving,$parent_code="")
    {
        $warehouseShelving->UpdateFullCode($parent_code);
        $parent_code=$warehouseShelving->fullCode;
        foreach ($warehouseShelving->items as $sub_line) {
            self::UpdateFullCodeFroAllSubLine($sub_line,$parent_code);
        }
        return [
            "result" => true
        ];
    }


    public static function UpdateFullCodeFroAll(WarehouseShelving $warehouseShelving)
    {

        $warehouseShelving_list=WarehouseShelving::
        where("warehouse_id",$warehouseShelving->warehouse_id)->where("parent_id",0)->get();
        foreach ($warehouseShelving_list as $item) {
            self::UpdateFullCodeFroAllSubLine($item);
        }
    }


    public static function getCoding(WarehouseShelving $warehouseShelving)
    {
        $part = $warehouseShelving->warehouse_shelving_line_part;
        $code_number1 = $warehouseShelving->code;
        if ($warehouseShelving->warehouse_shelving_line_status_id == 1210) {
             return [
                "result" => true,
                "code" => ""
            ];;
        }
        switch ($warehouseShelving->warehouse_shelving_line_type_id) {
            case "number":
            case 1:
                $code1 = $string = Str::of($code_number1)
                    ->when($part > 4 && $code_number1 < 10000, function ($string) {
                        return Str::of('0')->append($string);
                    })
                    ->when($part > 3 && $code_number1 < 1000, function ($string) {
                        return Str::of('0')->append($string);
                    })->when($part > 2 && $code_number1 < 100, function ($string) {
                        return Str::of('0')->append($string);
                    })->when($part > 1 && $code_number1 < 10, function ($string) {
                        return Str::of('0')->append($string);
                    });


                return [
                    "result" => true,
                    "code" => $code1
                ];

            case "character":
            case 2:
                if ($code_number1 > 26) {
                    return [
                        "result" => false,
                        "error" => "شناسه کاراکتر انگلیسی باید حداکثر 26 باشد."
                    ];
                }
                $letters = range('A', 'Z');
                $code1 = $letters[$code_number1 - 1];

                return [
                    "result" => true,
                    "code" => $code1
                ];
                break;
        }

        return [
            "result" => false,
            "error" => "مورد مشابه ای یافت نشد"
        ];

    }

//    public static function GetShelvingFullCode($product_ids=[-1]){
//
//        PackingFormItem::join("packing_forms","packing_forms.id","packing_form_id")->
//            whereIn("product_id",$product_ids)->
//            where("packing_forms.status_id",7007003)->
//            where("warehouse_status_id",4201)->pluck
//
//    }

}
