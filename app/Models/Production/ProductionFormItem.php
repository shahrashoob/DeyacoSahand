<?php

namespace App\Models\Production;

use App\Models\Form\FormItem;
use App\Models\Form\Packing\PackingFormItem;
use App\Models\GoodsKindProcess\Fabric_Raw\FabricRaw;
use App\Models\GoodsKindProcess\Fabric_Raw\FabricRawGrading;
use App\Models\LineProduct\GoodsKind;
use App\Models\LineProduct\LotNumber;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Product;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class ProductionFormItem extends Model
{
    use HasFactory;
    use Loggable;

    protected $table = "production_form_item";
    protected $fillable = [
        "production_form_id",
        "production_id",
        "product_id",
        "forecast_amount",
        "lot_number_id",
        "amount",
        "sub_amount",
        "amount_after_control",
        "final_amount",
        "band_code",
        "status_id",
        "allocation_id",
        "source_production_form_item_id",
        "version_code"
    ];

    public function packing_form_item()
    {
        return $this->belongsTo(PackingFormItem::class);
    }

    public function fabric_grading()
    {
        return $this->hasMany(FabricRawGrading::class);
    }

    public function production()
    {
        return $this->belongsTo(Production::class);
    }

    public function production_form()
    {
        return $this->belongsTo(ProductionForm::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function allocation()
    {
        return $this->belongsTo(Allocation::class);
    }

    public function lot_numbers()
    {
        return $this->hasMany(ProductionFormItemLotNumber::class);
    }

    public function start_machine_log()
    {
        return $this->belongsTo(MachineLog::class, "start_machine_log_id", "id");
    }

    public function end_of_machine_log()
    {
        return $this->belongsTo(MachineLog::class, "end_of_machine_log_id", "id");
    }


    public function getPackingFormItems()
    {
        return PackingFormItem::where([
            "production_form_item_id" => $this->id,
            "band_code" => $this->band_code
        ])->orderByDesc("id")->
        get();
    }

    public function getPackingFormItemsCount()
    {
        return PackingFormItem::where([
            "production_form_item_id" => $this->id,
            "band_code" => $this->band_code
        ])->
        count("id");
    }

    public function getPackingFormItem()
    {
        return PackingFormItem::where([
            "production_form_item_id" => $this->id,
            "band_code" => $this->band_code
        ])->first();
    }

    public static function AddNewItem($allocation_id, $production_form_id, $production_id, $product_id, $band_code, $status_id, $forecast_amount = 0, $version_code = null)
    {
        return $production_form_item = ProductionFormItem::create([
            "allocation_id" => $allocation_id,
            "production_form_id" => $production_form_id,
            "production_id" => $production_id,
            "product_id" => $product_id,
            "band_code" => $band_code,
            "status_id" => $status_id,
            "forecast_amount" => $forecast_amount,
            "version_code" => $version_code
        ]);

    }

    public function AddLotItem($lot_number_id, MachineLog $machine_log)
    {

        $last_lot_item = ProductionFormItemLotNumber::where([
            "production_form_id" => $this->production_form_id,
            "production_form_item_id" => $this->id,
        ])->orderByDesc("id")->first();


        if (isset($last_lot_item) && $last_lot_item->lot_number_id != $lot_number_id) {
            $last_lot_item->end_of_machine_log_id = $machine_log->id;
            $last_lot_item->amount = $last_lot_item->getAmount();
            $last_lot_item->save();

        }

        if (!isset($last_lot_item) || ($last_lot_item->lot_number_id ?? 0) != $lot_number_id) {

            ProductionFormItemLotNumber::firstOrCreate([
                "production_form_id" => $this->production_form_id,
                "production_form_item_id" => $this->id,
                "lot_number_id" => $lot_number_id,
                "start_machine_log_id" => $machine_log->id
            ]);

            return true; // یک لات اضافه شد.
        }

        return false;
    }

    public function updateItemAmount($update_lot_item = false)
    {

        if (!$this->start_machine_log || !$this->end_of_machine_log) {
            return -1;
        }
        $amount = GoodsKind::getAmountFromMachineLog(
            $this->product,
            $this->start_machine_log,
            $this->end_of_machine_log
        );

        $lot_item_count = $this->lot_numbers()->count();
        if ($lot_item_count > 0) {
            $this->amount = $amount;
            $this->save();
        }

        if ($update_lot_item) {
            foreach ($this->lot_numbers as $lot_item) {
                $lot_item->amount = $lot_item->getAmount();
                $lot_item->save();
            }
        }


    }

    /**
     * @param \App\Models\Production\ProductionFormItem $production_form_item
     * @param \App\Models\LineProduct\Machine\MachineLog $machine_log
     * این تابع و تابع UpdateItemAmount مثل هم هستند، فقط در static بودن با هم تفاوت دارند.
     *
     * @return int|void
     */
    public static function UpdateAmountWithLastContour(ProductionFormItem $production_form_item, MachineLog $machine_log)
    {
        if (!$production_form_item->start_machine_log) {
            return -1;
        }

        $amount = GoodsKind::getAmountFromMachineLog(
            $production_form_item->product,
            $production_form_item->start_machine_log,
            $machine_log
        );


        $lot_item = $production_form_item->lot_numbers()->orderByDesc("id")->first();
        if ($lot_item) {
            $lot_item->amount = $lot_item->getAmount($machine_log, -1);
            $lot_item->save();

            // اگر لات دارد مقدار فرم تولید را بروز می کنیم وگر نه که لازم نیست
            $production_form_item->amount = $amount;
            $production_form_item->save();
        }


    }

    public function setStartMachineLog(MachineLog $machine_log)
    {
        $this->start_machine_log_id = $machine_log->id;
        $this->save();

        // تغییر قطب اولین لات
        $lot_number = $this->lot_numbers()->orderBy("id")->first();
        if ($lot_number) {
            $lot_number->start_machine_log_id = $machine_log->id;
            $lot_number->save();
        }
    }

    public function setEndMachineLog(MachineLog $machine_log)
    {

        $this->end_of_machine_log_id = $machine_log->id;
        $this->save();

        // تغییر قطب آخرین لات
        $lot_number = $this->lot_numbers()->orderByDesc("id")->first();
        if ($lot_number) {
            $lot_number->end_of_machine_log_id = $machine_log->id;
            $lot_number->save();
        }
    }

    public function getCode()
    {

        if (!isset($this->code) || $this->code == "") {
            $number = ProductionFormItem::
                where("production_form_id", $this->production_form_id)->
                where("id", "<", $this->id)->
                count() + 1;
            $this->code = $this->production_form->code . "/" . $this->band_code . "/" . $number;
            $this->save();
        }

        return $this->code;
    }


}
