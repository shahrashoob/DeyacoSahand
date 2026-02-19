<?php

namespace App\Models\LineProduct;

use App\Models\HR\Personal\DependentType;
use App\Models\LineProduct\Packing\DischargeType;
use App\Models\LineProduct\Station\Operation\StationOperationCategory;
use App\Models\LineProduct\Station\Operation\StationOperationType;
use App\Models\LineProduct\Station\Operation\StationSubOperation;
use Database\Seeders\LineProductStation\StationOperationTypeSeeder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class StationOperation extends Model
{
    use HasFactory;

    protected $fillable = [
        "station_id",
        "caption",
        "station_operation_type_id",
        "station_operation_category_id",
        "discharge_type_id" // نوع حرکت مواد اولیه در ماشین FiFo/LiFo
    ];

    public function station()
    {
        return $this->belongsTo(Station::class);
    }

    public function discharge_type()
    {
        return $this->belongsTo(DischargeType::class);
    }

    public function station_operation_category()
    {
        return $this->belongsTo(StationOperationCategory::class, "station_operation_category_id");
    }

    public function station_operation_type()
    {
        return $this->belongsTo(StationOperationType::class);
    }

    public function station_sub_operation()
    {
        return $this->hasMany(StationSubOperation::class);
    }


    public function getCode()
    {

        if ($this->code != "") {
            return $this->code;
        }
        $code_number = StationOperation::where("station_id", $this->station_id)->where("id", "<", $this->id)->count() + 1;
        $code = $string = Str::of($code_number)
            ->when($code_number < 100, function ($string) {
                return Str::of('0')->append($string);
            })
            ->when($code_number < 10, function ($string) {
                return Str::of('0')->append($string);
            });
        $this->code = $this->station->code . "" . $code;
        $this->save();

        return $this->code;
    }

    public static function CheckBath($bath_number, $percent, $number)
    {
        $min = $bath_number - ($bath_number * $percent / 100);
        $max = $bath_number + ($bath_number * $percent / 100);
        $k = 0;
        $check_number = $number;
        while ($k <= $number + 1) {
            if ($check_number >= $min && $check_number <= $max) {
                return [
                    "result" => true,
                    "batch_amount" => $check_number,
                    "k" => $k,
                    "min" => $min,
                    "max" => $max

                ];
            } elseif ($check_number < $min) {
                return [
                    "result" => false,
                    "batch_amount" => $check_number,
                    "k" => $k,
                    "min" => $min,
                    "max" => $max
                ];
            } else {
                $k++;
                $check_number = $number / $k;
            }
        }
        return [
            "result" => false,
            "bath_number"=>$bath_number,
            "number"=>$number,
            "percent" => $percent,
            "batch_amount" => $check_number,
            "k" => $k,
            "min" => $min,
            "max" => $max
        ];
    }
}
