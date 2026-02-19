<?php

namespace App\Models\Warehouse\Shelving;

use App\Models\Warehouse\Warehouse;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ShelvingCell extends Model
{
    use HasFactory;

    protected $fillable = [
        "warehouse_id",
        "cell_type_id",

        "line1_number",
        "line1_type",
        "line1_part",
        "line1_status_id",

        "line2_number",
        "line2_type",
        "line2_part",
        "line2_status_id",

        "line3_number",
        "line3_type",
        "line3_part",
        "line3_status_id",

        "line4_number",
        "line4_type",
        "line4_part",
        "line4_status_id",

        "line5_number",
        "line5_type",
        "line5_part",
        "line5_status_id",

    ];

    public static function GetName()
    {
        return [
            "1" => "سالن",
            "2" => "سمت",
            "3" => "ردیف",
            "4" => "ستون",
            "5" => "جایگاه",
        ];
    }

    public static function GetCodeLable(Warehouse $warehouse, $cell_info)
    {
        $code = "";
         $shelving_cells = ShelvingCell::where("warehouse_id", $warehouse->id)->orderBy("id")->firstOrCreate(["warehouse_id" => $warehouse->id]);
        foreach (self::GetName() as $id => $value) {
            switch ($id) {
                case "1":
                    if($shelving_cells->line1_status_id ==1200){

                        $result=self::getCoding($shelving_cells->line1_type,$shelving_cells->line1_part,$cell_info[1]);
                        if(!$result["result"]){
                           return $result;
                        }
                        $code.="-".$result["code"];
                    }
                    break;
                case "2":
                    if($shelving_cells->line2_status_id ==1200){

                        $result=self::getCoding($shelving_cells->line2_type,$shelving_cells->line2_part,$cell_info[2]);
                        if(!$result["result"]){
                            return $result;
                        }
                        $code.="-".$result["code"];
                    }
                    break;
                case "3":
                    if($shelving_cells->line3_status_id ==1200){

                        $result=self::getCoding($shelving_cells->line3_type,$shelving_cells->line3_part,$cell_info[3]);
                        if(!$result["result"]){
                            return $result;
                        }
                        $code.="-".$result["code"];
                    }
                    break;
                case "4":
                    if($shelving_cells->line4_status_id ==1200){

                        $result=self::getCoding($shelving_cells->line4_type,$shelving_cells->line4_part,$cell_info[4]);
                        if(!$result["result"]){
                            return $result;
                        }
                        $code.="-".$result["code"];
                    }
                    break;
            }


        }
        return [
            "result" => true,
            "code" => trim($code,"-"),
        ];
    }
    public static function getCoding($type, $part, $code_number1, $code_number2 = null)
    {
        switch ($type) {
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
                $code2 = "";
                if ($code_number2) {
                    $code2 = $string = Str::of($code_number2)
                        ->when($part > 4 && $code_number1 < 10000, function ($string) {
                            return Str::of('0')->append($string);
                        })
                        ->when($part > 3 && $code_number2 < 1000, function ($string) {
                            return Str::of('0')->append($string);
                        })->when($part > 2 && $code_number2 < 100, function ($string) {
                            return Str::of('0')->append($string);
                        })->when($part > 1 && $code_number2 < 10, function ($string) {
                            return Str::of('0')->append($string);
                        });
                }
                return [
                    "result" => true,
                    "code" => $code1 . "" . $code2
                ];
                break;
            case "character":
            case 2:
                if ($code_number1 > 26 || ($code_number2 && $code_number1 > 26)) {
                    return [
                        "result" => false,
                        "error" => "شناسه کاراکتر انگلیسی باید حداکثر 26 باشد."
                    ];
                }
                $letters = range('A', 'Z');
                $code1 = $letters[$code_number1 - 1];
                $code2 = "";
                if ($code_number2) {
                    $code2 = $letters[$code_number2 - 1];
                }
                return [
                    "result" => true,
                    "code" => $code1 . "" . $code2
                ];
                break;
        }

        return [
            "result" => false,
            "error" => "مورد مشابه ای یافت نشد"
        ];

    }
}
