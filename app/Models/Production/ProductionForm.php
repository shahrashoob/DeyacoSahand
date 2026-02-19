<?php

namespace App\Models\Production;

use App\Events\ProductionForm\ProductionFormLogEvent;
use App\Models\Contractor\Contractor;
use App\Models\Form\Packing\PackingFormItem;
use App\Models\GoodsKindProcess\Fabric_Raw\FabricRaw;
use App\Models\GoodsKindProcess\Fabric_Raw\FabricRawTypeOfCut;
use App\Models\GoodsKindProcess\Warps\RequestForm\WarpsRequestFormItem;
use App\Models\LineProduct\Carrier\Carrier;
use App\Models\LineProduct\GoodsKindPropertyValue;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineAllocation;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\LineProduct\Product\Version\ProductVersion;
use App\Models\Utility\Status;
use Carbon\Carbon;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionForm extends Model
{
    use HasFactory;
    use Loggable;

    protected $fillable = [
        "machine_id",
        "production_id",
        "product_id",
        "lot_number_id",
        "carrier_id",
        "amount",
        "sub_amount",
        "install_date",
        "end_date",
        "status_id",
        "start_machine_log_id",
        "packing_type_id",
        "contractor_id",
        "supplier_id",
    ];

    /**
     * @return int[]
     * وضعیت های غیر مجاز که نباید در زمان محاسبه مقدار تولید شده در نظر گرفته شود.
     */
    public static function StatusNotValidForProductionAmount()
    {
        return  [7302003,7302004];
    }
    public function getCode()
    {
        if (isset($this->code)) {
            return $this->code;
        }

        $this->code = "DCPF/" . (1000 + $this->id);
        $this->save();

        return $this->code;
    }

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }

    public function contractor()
    {
        return $this->belongsTo(Contractor::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function carrier()
    {
        return $this->belongsTo(Carrier::class);
    }

    public function packing_type()
    {
        return $this->belongsTo(PackingType::class);
    }

    public function items()
    {
        return $this->hasMany(ProductionFormItem::class);
    }

    public function lot_numbers()
    {
        return $this->hasMany(ProductionFormItemLotNumber::class);
    }

    public function logs()
    {
        return $this->hasMany(ProductionFormLog::class);
    }

    public function start_machine_log()
    {
        return $this->belongsTo(MachineLog::class, "start_machine_log_id", "id");
    }

    public function end_of_machine_log()
    {
        return $this->belongsTo(MachineLog::class, "end_of_machine_log_id", "id");
    }


    public function loading_machine_log()
    {
        return $this->belongsTo(MachineLog::class, "loading_machine_log_id", "id");
    }


    public function in_the_weaving_machine_log()
    {
        return $this->belongsTo(MachineLog::class, "in_the_weaving_machine_log_id", "id");
    }


    public function in_the_finishing_weaving_machine_log()
    {
        return $this->belongsTo(MachineLog::class, "in_the_finishing_weaving_machine_log_id", "id");
    }


    public function extraction_machine_log()
    {
        return $this->belongsTo(MachineLog::class, "extraction_machine_log_id", "id");
    }

    public function fabric_raw_type_of_cut_for_extraction_form()
    {
        return $this->belongsTo(FabricRawTypeOfCut::class, "fabric_raw_type_of_cut_for_extraction_form_id", "id");
    }

    public function fabric_raw_type_of_cut_for_create_form()
    {
        return $this->belongsTo(FabricRawTypeOfCut::class, "fabric_raw_type_of_cut_for_create_form_id", "id");
    }

    public function getOneAmountItem()
    {
        $row = ProductionFormItem::where("production_form_id", $this->id)->first();
        if (isset($row)) {
            return $row->amount;
        }

        return -1;
    }

    public function getAmount()
    {
        return round(ProductionFormItem::where("production_form_id", $this->id)->sum("amount"), 2);
    }

    public function get_datetime()
    {
        return jdate(Carbon::parse($this->created_at)->timestamp)->format('H:i Y/m/d ');

    }

    public static function AddNewForm($machine_id, $carrier_id, $start_machine_log_id, $packing_type_id, $contractor_id = null, $status_id = "7002001", $supplier_id = null)
    {
        $production_form = ProductionForm::create([
            "machine_id" => $machine_id,
            "install_date" => Carbon::now(),
            "carrier_id" => $carrier_id,
            "start_machine_log_id" => $start_machine_log_id,
            "status_id" => $status_id,
            "packing_type_id" => $packing_type_id,
            "contractor_id" => $contractor_id,
            "supplier_id" => $supplier_id
        ]);
        $production_form->getCode();
        event(new  ProductionFormLogEvent(
            $production_form,
            7002001 // ایجاد فرم تولید
        ));
        $production_form->updateExtraAmountForCreateForm();

        return $production_form;
    }

    public function updateExtraAmountForExtraction($fabric_raw_type_of_cut_id)
    {


        $this->fabric_raw_type_of_cut_for_extraction_form_id = $fabric_raw_type_of_cut_id;


        // مشخص کردن مقدار قطب شروع
        $start_machine_log = $this->start_machine_log;

        //اگر پیک شروع ثبت نشده است، پیک شروع را به دست می آورد
        if (!$start_machine_log) {

            if ($this->fabric_raw_type_of_cut_for_create_form_id == 1) { // برش از شانه
                $start_machine_log = $this->loading_machine_log;
            } else {
                $start_machine_log = $this->in_the_weaving_machine_log;
            }
        }

        $this->setStartMachineLog($start_machine_log);


        // مشخص کردن مقدار قطب پایان
        $end_of_machine_log = $this->end_of_machine_log;

        //اگر پیک پایان ثبت نشده است، پیک را به دست می آورد.
        if (!$end_of_machine_log) {
            if ($this->fabric_raw_type_of_cut_for_extraction_form_id == 1 && $this->in_the_finishing_weaving_machine_log_id != null) { // برش از شانه
                $end_of_machine_log = $this->in_the_finishing_weaving_machine_log;
            } else {
                $end_of_machine_log = $this->extraction_machine_log;
            }
        }

        $this->setEndMachineLog($end_of_machine_log);

    }

    public function setStartMachineLog(MachineLog $machine_log)
    {
        $this->start_machine_log_id = $machine_log->id;
        $this->save();

        // تغییر قطب اولین فرم
        $band_number = [];
        foreach ($this->items()->orderBy("id")->get() as $item) {
            if (!isset($band_number[$item->band_code])) {
                $item->setStartMachineLog($machine_log);
            }
            $band_number[$item->band_code] = true; // اولین رکورد باند یک اپدیت شد.
        }
    }

    public function setEndMachineLog(MachineLog $machine_log)
    {
        $this->end_of_machine_log_id = $machine_log->id;
        $this->save();

        // تغییر قطب آخرین فرم
        $band_number = [];
        foreach ($this->items()->orderByDesc("id")->get() as $item) {
            if (!isset($band_number[$item->band_code])) {
                $item->setEndMachineLog($machine_log);
            }
            $band_number[$item->band_code] = true; // اولین رکورد باند یک اپدیت شد.
        }

    }

    public function updateAmount()
    {
        foreach ($this->items as $item) {
            $item->updateItemAmount(true);
        }
    }

    public static function UpdateAmountWithLastContour(ProductionForm $production_form, MachineLog $machine_log)
    {
        $band_cods = []; // به ازای هر باند مقدار آخرین آیتم روی باند را محاسبه می کند و کار به بقیه ندارد.
        foreach ($production_form->items()->orderBy("id", "desc")->get() as $item) {
            if (!isset($band_cods[$item->band_code])) {
                ProductionFormItem::UpdateAmountWithLastContour($item, $machine_log);
                $band_cods[$item->band_code] = $item->band_code;
            }
        }
    }


    public function updateExtraAmountForCreateForm()
    {

        $last_production_form = ProductionForm::
        where("machine_id", $this->machine_id)->
        where("id", "<", $this->id)->
        orderByDesc("id")->
        first();

        $fabric_raw_type_of_cut_id = 1; // برش از شانه
        if (isset($last_production_form)) {
            // اگر برش آخرین فرم ثبت نشده بود، فرض می کنیم، برش از چروک گیر است.
            $fabric_raw_type_of_cut_id =
                $last_production_form->fabric_raw_type_of_cut_for_extraction_form_id ?? 2;
        }

        $this->fabric_raw_type_of_cut_for_create_form_id = $fabric_raw_type_of_cut_id;
        $this->save();

    }

    // این تابع حذف است.
//    public static function ChangeStatusFromTo( Allocation $allocation, $status_from, $status_to ) {
//        $production_form_list = ProductionForm::where( "machine_id", $allocation->machine_id )->
//        where( "status_id", $status_from )->
//        get();
//        foreach ( $production_form_list as $item ) {
//            $item->status_id = $status_to;
//            $item->save();
//            event( new ProductionFormLogEvent( $item, 700206, null, Status::find( $status_from )->caption . "=>" . Status::find( $status_to )->caption ) );
//        }
//    }

    public function ChangeStatus($status_to, $text = "", $event_id = false)
    {

        if (!$event_id) {
            $event_id = $status_to;
        }
        $this->status_id = $status_to;
        $this->save();
        event(new ProductionFormLogEvent($this, $event_id, null, $text));

    }

    public function getLatestItem()
    {

        return $this->items()->orderByDesc("id")->first();
    }

    public static function UpdateVersion(ProductionForm $production_form)
    {
        foreach ($production_form->items as $item) {
            if (!$item->version_code) {
                $machine_allocation = MachineAllocation::
                where([
                    "production_id" => $item->production_id,
                    "allocation_id" => $item->allocation_id,
                ])->first();

                if ($machine_allocation) {
                    $item->version_code = $machine_allocation->version_code;
                    $item->save();
                }
            }
        }
    }


}
