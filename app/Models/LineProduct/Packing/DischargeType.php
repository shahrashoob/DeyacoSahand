<?php

namespace App\Models\LineProduct\Packing;

use App\Models\LineProduct\Carrier\CarrierType;
use App\Models\LineProduct\Product;
use App\Models\Utility\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DischargeType extends Model
{
    use HasFactory;

    public static function GetOrder($dischargeTypeId)
    {
        switch ($dischargeTypeId) {
            case 1:
            case 3:
                return "desc";
            case 2:
            case 4:
            case 5:
                return "asc";
        }
    }public static function GetOrderReverse($dischargeTypeId)
    {
        switch ($dischargeTypeId) {
            case 1:
            case 3:
                return "asc";
            case 2:
            case 4:
            case 5:
                return "desc";
        }
    }
}