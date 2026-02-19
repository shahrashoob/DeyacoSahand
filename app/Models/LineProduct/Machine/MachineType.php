<?php

namespace App\Models\LineProduct\Machine;

use App\Models\Accounting\CostCenter;
use App\Models\LineProduct\GoodsKind;
use App\Models\LineProduct\LinePost;
use App\Models\LineProduct\Machine\MachineType\MachineTypeInputAlgorithm;
use App\Models\LineProduct\Machine\ProductionChannel\MachineProductionChannelType;
use App\Models\LineProduct\Machine\ProductionChannel\MachineTypeProductionChannelType;
use App\Models\LineProduct\Station;
use App\Models\Utility\SpecialUnit;
use App\Models\Utility\Status;
use App\Models\Warehouse\Warehouse;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Mpdf\Tag\Option;

class MachineType extends Model
{
    use HasFactory;
    use Loggable;

    protected $fillable = [
        "caption",
        "section_number",
        "position_number",
        "station_id",
        "ic",
        "active_status_id",
        "lot_effective_code",
        "machine_module_type_id",
        "warehouse_max_capacity",
        "warehouse_request_point",
        "machine_type_consumption_type_id",
        "number_of_contour_in_minute",
    ];

    public function machine()
    {
        return $this->hasMany(Machine::class);
    }

    public function machine_type_input_algorhtim()
    {
        return $this->hasMany(MachineTypeInputAlgorithm::class);
    }

    public function machine_module_type()
    {
        return $this->belongsTo(MachineModuleType::class);
    }

    public function production_channel_types()
    {
        return $this->hasMany(MachineTypeProductionChannelType::class);
    }

    public function machines()
    {
        return $this->hasMany(Machine::class);
    }

    public function machine_status()
    {
        return $this->hasMany(MachineStatus::class, "machine_module_type_id", "machine_module_type_id");
    }

    public function machine_fault()
    {
        return $this->hasMany(MachineTypeMachineFault::class);
    }

    public function cost_center()
    {
        return $this->belongsTo(CostCenter::class, "ic");
    }

    public function active_status()
    {
        return $this->belongsTo(Status::class, "active_status_id");
    }

    public function warehouses()
    {
        return $this->hasMany(Warehouse::class, "belonging_to_id")->where("warehouse_type_id", 3);
    }

    public function Station()
    {
        return $this->belongsTo(Station::class);
    }

    public function getContourRatio()
    {
        return $this->get_property_value(9, 0); // ضریب تبدیل
    }

    public function getGoodsKind()
    {
        return GoodsKind::
        join("machine_type_input_band_goods_kind", "goods_kind_id", "goods_kinds.id")->
        join("machine_type_input_bands", "machine_type_input_band_id", "machine_type_input_bands.id")->
        where("machine_type_id", $this->id)->
        select("goods_kinds.*")->get();
    }

    public function machine_production_channel_type()
    {
        return $this->belongsTo(MachineProductionChannelType::class,"id","machine_type_id");
    }
    public function machine_property()
    {
        return MachineProperty::where("station_id", $this->station_id)->orderBy("priority_number")->get();
    }

    public function get_property_value($machine_property_id, $default = "", $type = "normal")
    {
        $property_value = MachinePropertyValue::
        where("machine_property_id", $machine_property_id)->
        where("machine_type_id", $this->id)->
        first();

        if ($property_value && $property_value->machine_property->field_type_id == 3 && $type == "normal") {

            $unit = SpecialUnit::find($property_value->value);
            if ($unit) {
                return $unit->caption;
            }

            return $default;
        } elseif ($property_value) {
            return $property_value->value;
        }

        return $default;
    }

    public function fullCaption()
    {
        return $this->code . " - " . $this->caption;
    }

    public function input_bands()
    {
        return $this->hasMany(MachineTypeInputBand::class);
    }

    public function output_bands()
    {
        return $this->hasMany(MachineTypeOutputBand::class);
    }

    public function getCode()
    {

        if ($this->code != "") {
            return $this->code;
        }
        $code_number = MachineType::where("station_id", $this->station_id)->where("id", "<", $this->id)->count() + 1;
        $code = $string = Str::of($code_number)
            ->when($code_number < 10, function ($string) {
                return Str::of('0')->append($string);
            });
        $this->code = $this->station->code . "" . $code;
        $this->save();

        return $this->code;
    }

    public function getCountStatus($type_status)
    {
        switch ($type_status) {
            case "on_status":
                return Machine::where([
                    "machine_type_id" => $this->id,
                    "active_status_id" => 1200,
                    "on_status_id" => 53001
                ])->count();

        }
    }

    public function getPossibilityOfAllocationMachine($option = false, $id = 0, $Line_product_station = null)
    {

        $query = Machine::
        join("machine_status", "machines.production_status_id", "machine_status.production_status_id")->
        where([
            "machines.machine_type_id" => $this->id,
            "possibility_of_allocation_machine" => 1,
            "machines.active_status_id" => 1200,
            "machine_module_type_id" => $this->machine_module_type_id
        ]);

        $machine_ids = $query->pluck("machines.id");
        $machine_ids[] = -1;

        // ماشین هایی که کانال تولید مجاز آنها با خط محصول برابر است.
        $query2 = MachineProductionChannelType::
        whereIn("machine_id", $machine_ids)->
        where("production_channel_type_id", $Line_product_station->production_channel_type_id);


        if (!$option) {
            $machine_ids_2=$query2->pluck("machine_id");
            $machine_ids_2[]=-1;
            $list = $query->
            whereIn("machines.id", $machine_ids_2)->
            select("machines.id", "caption", "code")->orderBy("code")->get();
            return $list;
        }

        $list = $query2->with("machine")->
        get();
        $options = [];
        $selectedText = "";
        foreach ($list as $item) {
            $option = ["value" => $item->machine->id, "text" => $item->machine->caption];
            if ($id == $item->id) {
                $selectedText = $item->machine->caption;
                $option["selected"] = 1;
            }
            $options[] = $option;
        }

        return [
            "items" => $options,
            "value" => $id,
            "text" => $selectedText,
        ];
    }


    public static function ExistsCaption($code, $id = false)
    {
        if ($id) {
            return MachineType::where("caption", $code)->where("id", "!=", $id)->exists();
        }

        return MachineType::where("caption", $code)->exists();
    }

    public static function GetIdFromCode($code)
    {

        $machine_type = MachineType::where("code", $code)->first();

        return isset($machine_type) ? $machine_type : null;
    }

    public function get_access_level($post_id)
    {
        $result_full = LinePost::
        where(["post_id" => $post_id, "station_id" => $this->station_id, "machine_type_id" => $this->id])->
        whereNull("machine_id")->
        exists();
        if ($result_full) {
            return "full";
        }

        $result_empty = LinePost::
        where(["post_id" => $post_id, "station_id" => $this->station_id, "machine_type_id" => $this->id])->
        exists();
        if (!$result_empty) {
            return "empty";
        }

        return "";
    }


}

