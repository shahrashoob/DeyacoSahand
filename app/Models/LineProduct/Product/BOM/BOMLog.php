<?php

namespace App\Models\LineProduct\Product\BOM;

use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\ProductRoute;
use App\Models\LineProduct\Station;
use App\Models\LineProduct\StationOperation;
use App\Models\Production\Production;
use App\Models\Utility\Status;
use App\Models\Warehouse\Warehouse;
use App\Models\Worker;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BOMLog extends Model
{
    use HasFactory;

    protected $table = "bill_of_material_logs";
    protected $fillable = ["product_id", "bill_of_material_id", "production_id", "allocation_id", "user_id", "version"];
    public static $BOMItemCol = [
        "material_id",
        "amount",
        "number",
        "percent_of_use",
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function produciton()
    {
        return $this->belongsTo(Production::class);
    }

    public function allocation()
    {
        return $this->belongsTo(Allocation::class);
    }

    public function worker()
    {
        return $this->belongsTo(Worker::class, "user_id");
    }

    public function items()
    {
        return $this->hasMany(BOMItemLog::class, "bill_of_material_log_id");
    }

    public static function NewLog(BOM $bom, $production_id = null, $allocation_id = null, $user_id = null, $is_first_version = null)
    {

        $version = $bom->bom_logs()->count() + 1;

        // یک بار قبلا ایجاد شده و لازم نیست دوباره اجرا شود.
        if ($is_first_version && $version > 1) {
            return [
                "result" => true,
                "message" => "قبلا یک ورزن ایجاد شده است.",
                "version" => $version,
            ];

        }

//        return self::ChangeBOM($bom);
        // بررسی اینکه هیچ کدام از آیتم ها با آخرین ورزن BOM تفاوتی کرده است یا خیر
        if (!self::ChangeBOM($bom)) {
            return [
                "result" => true,
                "message" => "BOM تغییری نکرده است.",
                "version" => null,
            ];
        }
// پیدا کردن ورژنی که قبلا وجود داشته است.


        $bom_data = $bom->toArray();
        $bom_data["bill_of_material_id"] = $bom->id;
        $bom_data["version"] = $version;
        $bom_data["production_id"] = $production_id;
        $bom_data["allocation_id"] = $allocation_id;
        $bom_data["user_id"] = $user_id;


        $bom_log = BOMLog::create($bom_data);

        foreach ($bom->items as $bom_item) {

            $bom_item_data = $bom_item->toArray();
            $bom_item_data["bill_of_material_id"] = $bom->id;
            $bom_item_data["bill_of_material_log_id"] = $bom_log->id;
            $bom_item_data["bill_of_material_item_id"] = $bom_item->id;

            BOMItemLog::create($bom_item_data);
        }

        return [
            "result" => true,
            "version" => $version,
        ];
    }


    public static function ChangeBOM(BOM $bom, $version = null)
    {

        $bom_log = BOMLog::where("bill_of_material_id", $bom->id)->
        when($version, function ($query, $version) {
            return $query->where("version", $version);
        })->
        orderBy("version", "desc")->
        first();

        if (!$bom_log) {
            return [
                "result" => true,
                "step" => 1
            ];
        }

        $bom_log_item = BOMItemLog::where("bill_of_material_log_id", $bom_log->id)->get()->keyBy("bill_of_material_item_id");
        $bom_log_item_count = count($bom_log_item);
        if ($bom_log_item_count != $bom->items()->count()) {
            return [
                "result" => true,
                "step" => 2
            ];
        }
        foreach ($bom->items as $bom_item) {

            if (!isset($bom_log_item[$bom_item->id])) {
                return [
                    "result" => true,
                    "step" => 3
                ];
            }
            $bom_log_item_confirm = $bom_log_item[$bom_item->id];

            if ($bom_item->amount != $bom_log_item_confirm->amount) {
                return [
                    "result" => true,

                ];
            }
            if ($bom_item->number != $bom_log_item_confirm->number) {
                return [
                    "result" => true,

                ];
            }
            if ($bom_item->percent_of_use != $bom_log_item_confirm->percent_of_use) {
                return [
                    "result" => true,

                ];
            }
        }

        return [
            "result" => false,
        ];
    }

    public static function GetVersion(Product $product, BOM $bom, $production_id = null, $allocation_id = null)
    {
        $bom_change = 0;
        $bom_logs = BOMLog::where("bill_of_material_id", $bom->id)->
        orderBy("version", "desc")->
        get();

        $bom_item_cols = $bom->items()->select(self::$BOMItemCol)->first()->toArray();
        $bom_item_cols = json_encode($bom_item_cols);;
        foreach ($bom_logs as $bom_log) {
            $bom_log_item_cols = $bom_log->items()->select(self::$BOMItemCol)->first()->toArray();

            $bom_log_item_cols = json_encode($bom_log_item_cols);;
            $bom_change = Product\Version\ProductVersion::ChangeVersion($bom_item_cols, $bom_log_item_cols);
            if ($bom_change == 0) {
                return [
                    "result" => true,
                    "type" => "before_version",
                    "version" => $bom_log->version,
                ];
            }
        }

        // هیچ نسخه قبلی نیست باید یک ورژ جدید ایجاد کنیم.
        $result = self::NewLog($bom, null, null, Auth::id());

        $result["type"] = "new_version";
        return $result;
    }


}
