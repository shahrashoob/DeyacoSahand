<?php

namespace App\Models\Warehouse\WarehouseHandling;

use App\Events\Form\PackingLogEvent;
use App\Events\ProductionForm\ProductionFormLogEvent;
use App\Events\Warehouse\PutInWarehouseEvent;
use App\Events\Warehouse\WarehouseHandlingEvent;
use App\Models\Form\Form;
use App\Models\Form\FormItem;
use App\Models\Form\Packing\PackingForm;
use App\Models\Form\Packing\PackingFormItem;
use App\Models\LineProduct\Machine\MachineType;
use App\Models\Production\ProductionFormItem;
use App\Models\User;
use App\Models\Utility\SmartObject;
use App\Models\Utility\Status;
use App\Models\Warehouse\Warehouse;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class WarehouseHandlingGoodsKindTimeLimit extends Model
{
    use HasFactory;

    protected $table = "warehouse_handling_goods_kind_time_limit";
    protected $fillable = [
        "warehouse_type_id",
        "belonging_to_id",
        "goods_kind_id",
        "warehouse_handling_time_limit",
    ];

    public static function GetGoodsKindLimit(MachineType $machineType)
    {
        $list = self::
        where(function ($query) use ($machineType) {
            return $query->where(["warehouse_type_id" => 3, "belonging_to_id" => $machineType->id]);
        })->
        orWhere(function ($query) use ($machineType) {
            return $query->where(["warehouse_type_id" => 4, "belonging_to_id" => $machineType->station_id]);
        })->
        orWhere(function ($query) use ($machineType) {
            return $query->where(["warehouse_type_id" => 5, "belonging_to_id" => $machineType->station->line_id ?? 0]);
        })->get();

        $list_warehouse_type_goods_kind = [];
        foreach ($list as $item) {
            $list_warehouse_type_goods_kind[$item->warehouse_type_id][$item->goods_kind_id][$item->belonging_to_id] =
                $item->warehouse_handling_time_limit;
        }

        return $list_warehouse_type_goods_kind;
    }
}