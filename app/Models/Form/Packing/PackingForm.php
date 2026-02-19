<?php

namespace App\Models\Form\Packing;

use App\Events\Form\PackingLogEvent;
use App\Http\Controllers\GoodsKindProcess\FabricRaw\PackingForm\ChangeInWarehouseController;
use App\Models\Contractor\Contractor;
use App\Models\Customer\Customer;
use App\Models\Form\Form;
use App\Models\Form\FormItem;
use App\Models\LineProduct\Carrier\Carrier;
use App\Models\LineProduct\Degree;
use App\Models\LineProduct\LotNumber;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\LineProduct\Product;
use App\Models\Production\ProductionFormItem;
use App\Models\Supplier\Supplier;
use App\Models\Utility\RealityType;
use App\Models\Utility\Status;
use App\Models\Utility\Transport\TransportItem;
use App\Models\Utility\Transport\TransportPackingForm;
use App\Models\Warehouse\Warehouse;
use App\Models\Warehouse\WarehouseProduct;
use App\Models\Warehouse\WarehouseShelving\WarehouseShelving;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PackingForm extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        "form_id",
        "packing_type_id",
        "carrier_id",
        "status_id",
        "packing_form_parent_id",
        "packing_form_master_id",
        "reality_type_id",
        "weight",
        "gross_weight",
        "sub_packing_form_number",
        'pin1',
        'pin2',
        'tag1',
        'tag2',
        'source_packaging_form_code',
        'destination_packing_form_code',
        'warehouse_shelving_id',
        'applicant_type_id',
        'applicant_id',
    ];

    /***
     * @var int[]  لیست وضعیت بسته بندی هایی که در راه هستند.
     */
    public static $OnTheyWayStatus = [7007002, 7007005, 7007008, 7007009, 7007013, 7007020, 7007023, 7007026];

    public function items()
    {
        return $this->hasMany(PackingFormItem::class);
    }

    public function logs()
    {
        return $this->hasMany(PackingFormLog::class);
    }

    public function packing_form_parent()
    {
        return $this->belongsTo(PackingForm::class, "packing_form_parent_id");

    }

// نام درخواست دهنده
    public
    function applicant()
    {
        // اگر فرم ورود دارد از روی فرم ورود به انبار و نوع تراکنش مالک را تشخیص میدهیم، در غیر این صورت به مالک بسته بندی مراجعه می کنیم.
        if(!$this->applicant_type_id && $this->form_id){
            $this->applicant_type_id=$this->form->applicant_type_id;
            $this->applicant_id=$this->form->applicant_id;
            $this->save();
        }

        switch ($this->applicant_type_id) {
            case 10:
                return $this->belongsTo(Machine::class, "applicant_id");
                break;
            case 20:
                return $this->belongsTo(Contractor::class, "applicant_id");
            case 30:
                return $this->belongsTo(Customer::class, "applicant_id");

            case 60: // تامین کننده (تحویل امانی - قرض)
            case 70: // تامین کننده ( برگشت از خرید)
                return $this->belongsTo(Supplier::class, "applicant_id");
            case 80: // درخواست متفرقه
                return $this->belongsTo(Worker::class, "applicant_id");
        }

        return  $this->belongsTo(Worker::class, "0");
    }
    public function warehouse_shelving()
    {
        return $this->belongsTo(WarehouseShelving::class);

    }

    public function reality_type()
    {
        return $this->belongsTo(RealityType::class);
    }

    public function packing_form_master()
    {
        return $this->belongsTo(PackingForm::class, "packing_form_master_id");

    }

    public function packing_form_contents()
    {
        return $this->hasMany(PackingForm::class, "packing_form_master_id", "id");

    }

    public function getItemCount($type = "")
    {

        switch ($type) {
            case "packing_form_contents":
                return $this->sub_packing_form_number;
                break;
            case "items":
                return $this->items()->count();
                break;
            default:
                $content_count = $this->sub_packing_form_number;
                if ($content_count == 0) {
                    return $this->items()->count();
                } else {
                    return $content_count;
                }
                break;

        }

    }

    public function parent_packing_form_sub_packing()
    {
        return null;// PackingFormSubPacking::where( "packing_form_id", $this->id )->first();

    }

    public function getDegree()
    {
        if (count($this->items) > 0) {
            return $this->items()->first()->degree;
        }
//        if ( count( $this->sub_packing ) > 0 ) {
//            return $this->sub_packing()->first()->packing_form->getDegree();
//        }

        return null;
    }

    public function getTransportPackingForm($type = "")
    {


        $transport_packing_form = TransportPackingForm::where("packing_form_id", $this->id)->first();
        if ($transport_packing_form) {
            switch ($type) {
                case "transport_item_code":
                    return $transport_packing_form->transport_item ? $transport_packing_form->transport_item->code() : "";
            }
        }

        return $transport_packing_form;
    }

    public function getTransportItem($type = "")
    {
        $packing_form = $this->getTransportPackingForm();

        switch ($type) {
            case "code":
                return $packing_form ? $packing_form->transport_item->code() : "";
            case "id":
                return $packing_form ? $packing_form->transport_item->id : "";;
            case "codeNumber":
                return $packing_form ? $packing_form->transport_item->codeNumber() : "";;
            default:
                return $packing_form->transport_item;;


        }


        return null;
    }

    public function getCodeNumber()
    {
        return 1000 + $this->id;
    }

    public function getIdFromCode($id)
    {
        return $id - 1000;
    }

    public function getPin1()
    {
        if ($this->pin1 != null || $this->pin1 != "") {
            return $this->pin1;
        }
        $this->pin1 = self::GetPin1String($this);
        $this->save();

        return $this->pin1;
    }

    public static function GetPin1String(PackingForm $packing_form)
    {
        $app_id = env('APP_ID');
        $random_Str = random_int(1000, 9999);

        $code = Str::of($packing_form->getCodeNumber())->
        when($packing_form->id < 1000000, function ($string) {
            return Str::of('0')->append($string);
        })->
        when($packing_form->id < 100000, function ($string) {
            return Str::of('0')->append($string);
        })->
        when($packing_form->id < 10000, function ($string) {
            return Str::of('0')->append($string);
        })->
        when($packing_form->id < 1000, function ($string) {
            return Str::of('0')->append($string);
        })->
        when($packing_form->id < 100, function ($string) {
            return Str::of('0')->append($string);
        })->
        when($packing_form->id < 10, function ($string) {
            return Str::of('0')->append($string);
        }); // 0000


        $pin1 = "01" . $app_id . $code . $random_Str;
        return $pin1;
    }

    public function warehouse_status()
    {
        return $this->belongsTo(Status::class, "warehouse_status_id");
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function GetWarehouseStatusReferenceCode()
    {
        if ($this->warehouse_status_id == 4203) {
            $packing_form_item = PackingFormItem::where("packing_form_id", $this->id)->first();
            $form_item = FormItem::where(["packing_form_item_id" => $packing_form_item->id ?? ""])->orderByDesc("id")->first();

            return $form_item->form->code ?? "";
        }

        return "";
    }

    public function getStatus()
    {

        if ($this->packing_form_master) {
            return "داخل بسته بندی " . ($this->packing_form_master->code ?? "");
        }
        $caption = $this->status->caption ?? "";
        switch ($this->status_id) {
            case 7007007: // تغییر یافته
                $packing_list = PackingForm::where("packing_form_parent_id", $this->id)->get();
                $caption .= " به ";
                foreach ($packing_list as $item) {
                    $caption .= $item->getCode() . " , ";
                }
                break;
            case 7007003: // تحویل شده به انبار
                $caption = "تحویل شده به " . ($this->warehouse->caption ?? "انبار");

                break;
        }

        return $caption;
    }

    public function carrier()
    {
        return $this->belongsTo(Carrier::class);
    }

    public function packing_type()
    {
        return $this->belongsTo(PackingType::class);
    }

    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    public function sub_packing()
    {
        return null;// $this->hasMany( PackingFormSubPacking::class, "parent_packing_form_id" );
    }

    public function getUnitCaption($type, $caption, $next_text = "")
    {
        $text = "";
        if (count($this->items) > 0) {
            switch ($type) {
                case "unit":
                    $text = $this->items()->first() ? ($this->items()->first()->product->unit->$caption ?? "") : "مقدار ";
                    break;
                case "sub_unit":
                    $text = $this->items()->first() ? ($this->items()->first()->product->sub_unit->$caption ?? "") : "مقدار فرعی ";
                    break;
                case "sub_unit2":
                    $text = $this->items()->first() ? ($this->items()->first()->product->sub_unit2->$caption ?? "") : "مقدار فرعی2 ";
                    break;
            }
        }
//        elseif ( count( $this->sub_packing ) > 0 ) {
//
//            return $this->sub_packing()->first()->packing_form->getUnitCaption( $type, $caption, $text );
//
//        }

        return $text == "" ? "" : $text . " " . $next_text;
    }

    public function getCode($perfix = "DCPK/")
    {

        if ($this->code != null) {
            return $this->code;
        }
        $counter = $this->id + 1000;
        $this->code = $perfix . ($counter);
        $this->save();

        if ($this->random == null) {

            $this->random = Str::random(6);

            // اگر پین 1 ندارد، آن را ایجاد می کند.
            if ($this->pin1 == null || $this->pin1 == "") {
                $this->pin1 = self::GetPin1String($this);
            }

            $this->save();
        }


        return $this->code;
    }

    public function getRandom($pin1 = null)
    {

        if ($this->random == null) {
            $this->random = Str::random(6);
            // اگر پین 1 ندارد، آن را ایجاد می کند.
            if ($this->pin1 == null || $this->pin1 == "") {
                if ($pin1 && $pin1!="") {
                    $pin1=trim($pin1);
                    if (PackingForm::where("pin1", $pin1)->exists()) {
                        $pin1 = self::GetPin1String($this); // اگر پین ست شده بود ولی تکراری بود، تولید می کنیم.
                    }
                } else {
                    // اگر پین ست نشده بود، تولید می کنیم.
                    $pin1 = self::GetPin1String($this, $pin1);
                }

                $this->pin1 = $pin1;
            }
            $this->save();
        }

        return $this->random;
    }

    public function getAmount($type = "amount", $precision = 2, $function = null)
    {
        if ($precision == -1) {
            return $this->items()->sum($type);
        }
        if ($function != null) {
            $sum = $this->items()->selectRaw("sum($function($type)) as sum")->first();
            return round($sum->sum, $precision);
        }

        return round($this->items()->sum($type), $precision);
    }

    public function getAllAmount($type, $precision = 2)
    {
        $amount = $this->getAmount($type, $precision);

        return $amount;
    }

    public function getAmountAfterControl()
    {
        return round($this->items()->sum("amount_after_control"), 2);
    }

    public function getFinalAmount($precision = 6)
    {
        return $this->getAmount($type = "final_amount", $precision);
    }

    public function getSubAmount($precision = 2)
    {
        return $this->getAmount($type = "sub_amount", $precision);
    }

    public function getSubAmount2($precision = 2)
    {
        return $this->getAmount($type = "sub_amount2", $precision);
    }

    public function get_create_date_and_time($format = 'H:i Y/m/d ')
    {
        return jdate(Carbon::parse($this->created_at)->timestamp)->format($format);

    }

    public function get_create_date_en($number = 0, $format = "d/m/Y ")
    {
        if ($number != 0) {
            return Carbon::parse($this->created_at)->addDay($number)->format($format);
        }
        return Carbon::parse($this->created_at)->format($format);

    }

    public function updateAmount()
    {
        // بروز رسانی مقدار سیستم برای بسته بندی هایی که تغییر می کنند.
        $child_packing_form_list = PackingForm::where("packing_form_parent_id", $this->id)->get();
        $child_packing_form_ids = PackingForm::where("packing_form_parent_id", $this->id)->pluck("id")->toArray();
        $packing_form_amount = $this->getAllAmount("amount", -1);
        $child_final_amount = PackingFormItem::whereIn("packing_form_id", $child_packing_form_ids)->sum("final_amount");
        foreach ($child_packing_form_list as $child_packing_form) {

            foreach ($child_packing_form->items as $PFItem) {

                $PFItem->amount = $child_final_amount == 0 ? 0 : $PFItem->final_amount / $child_final_amount * $packing_form_amount;
                $PFItem->save();
            }
        }
    }

    public static function UpdateWeight($packing_form)
    {
        $message = "";
        if (!$packing_form->packing_type) {
            return [
                "result" => true,
                "weight" => 0,
                "carrier_type" => null
            ];
        }
        // به دست آوردن وزن بسته بندی
        $packing_weight_result = PackingType::getWeight($packing_form->packing_type, $packing_form->carrier);
        if (!$packing_weight_result["result"]) {
            return $packing_weight_result;
        }

        if ($packing_form->packing_type->first_packing_type) {
            $sub_packing_weight_result = PackingType::getWeight($packing_form->packing_type->first_packing_type, $packing_form->carrier);
            if (!$sub_packing_weight_result["result"]) {
                return $sub_packing_weight_result;
            }

//            if ( $packing_form->sub_packing_form_number == 0 ) {
//                $message = ( "با توجه به اینکه  " . $packing_form->packing_type->caption . " دارای بسته بندی فرعی می باشد، باید برای بسته بندی های فرعی حداقل یک بسته بندی انتخاب شود." );
//            }
        }


        $packing_type_weight =
            $packing_weight_result["weight"];

        if ($packing_form->packing_type->first_packing_type) {
            $packing_type_weight += $sub_packing_weight_result["weight"] * $packing_form->sub_packing_form_number;
        }

        $weight = 0;

        foreach ($packing_form->items as $item) {
            if ($item->final_amount == 0) {
                $weight = 0; // اگر مقدار نهایی صفر است، یعنی وزن بسته بندی هم صفر است.
            } elseif ($item->product->unit->weight_conversion_rate != 0) {
                $weight += $item->final_amount * $item->product->unit->weight_conversion_rate;
            } elseif ($item->product->sub_unit && $item->product->sub_unit->weight_conversion_rate != 0) {
                $weight += $item->sub_amount * $item->product->sub_unit->weight_conversion_rate;
            } else {
                $weight = -1;
            }
        }

        if ($weight < 0) {
            $message = "وزن خالص نامعتبر است.";
        }


        if ($message != "") {
            return ["result" => false, "error" => $message];
        }

        return [
            "result" => true,
            "weight" => $weight,
            "gross_weight" => $weight + $packing_type_weight,
        ];


    }

    public static function UpdateCratedAtNumber()
    {
        DB::statement("UPDATE `packing_forms` SET `created_at_number`=DATE_FORMAT(packing_forms.created_at,'%Y%m%d') WHERE   created_at_number is NULL");
        DB::statement("UPDATE `packing_forms` SET `created_at_month_number`=DATE_FORMAT(packing_forms.created_at,'%Y%m') WHERE   created_at_month_number is NULL");
    }

    public static function ConfirmToApplicant(PackingForm $packing_form, Form $form, $applicant_type_id)
    {
        $new_status_id = 0;
        $event_id = 7007012;

        switch ($applicant_type_id) {
            case 10:
                $new_status_id = 7007017; // تایید و ثبت ماشین
                break;
            case 20:
                $new_status_id = 7007016; // تایید و ثبت پیمانکار
                break;
            case 30:
                $new_status_id = 7007014; // تایید و ثبت مشتری
                break;
            case 40:
                $new_status_id = 7007003; //تحویل شده به انبار
                break;
            case 60:
                $new_status_id = 70070022; //تحویل شده به تامین کننده
                break;
            case 80:
                $new_status_id = 7007012; //تحویل شده به تامین کننده
                break;
            default:
                1 / 0;
                break;
        }

        if ($packing_form->status_id == $new_status_id) {
            return;
        }

        // اگر بسته بندی ها داخل بسته بندی دیگر است، لازم نیست وضعیت آنها تغییر کند.
        if (!$packing_form->packing_form_master) {
            $packing_form->items()->update(["status_id" => $new_status_id]); // // ثبت و تایید مشتری
            $packing_form->status_id = $new_status_id; // تحویل شده به مشتری
        }

        $packing_form->save();

        event(new PackingLogEvent($packing_form, $event_id, null, "", $form->id));


    }

    public function allowRejectProduct()
    {

        // اگر تغییر یافته است
        if ($this->status_id == 7007007) {
            return false;
        }
        $list = Product\RejectProduct\RejectProductFormItem::
        where("packing_form_id", $this->id)->
        get();
        //اگر در یکی از فرم های مرجوعی وجود دارد
        $count = 0;
        foreach ($list as $item) {
            // کنسل شده | عدم تایید
            if (!in_array($item->reject_product_form->status_id, [7009002, 7009009])) {
                $count++;
            }

        }
        if ($count > 0) {
            return false;
        }

        return true;
    }

    public function initial_shrinkage_percent()
    {
        // درصد جمع شدگی اولیه= 1- مقدار نهایی خودش / مقدار نهایی پدر * 100
        $self_final_amount = $this->getFinalAmount();
        $parent_final_amount = $this->packing_form_parent ?
            $this->packing_form_parent->getFinalAmount() : $this->getFinalAmount();

        $self_amount = $this->getAmount();
        $parent_amount = $this->packing_form_parent ?
            $this->packing_form_parent->getAmount() : $this->getAmount();
        if ($parent_final_amount == 0 || $parent_amount == 0) {
            return "-99999999";
        }
        // متراژ خودش / متراژ پدرش به این دلیل است که ممکن است یک بسته بندی به دو یا چند بسته بندی دیگر تبدیل شود، بنابراین درصد جمع شدگی اولیه آنها با این فرمول اصلاح می شود و برای بقیه هم که در 1 ضرب می شود.
        $parent_final_amount = $parent_final_amount * ($self_amount / $parent_amount);
        if ($parent_final_amount == 0) {
            return "-99999999";
        }

        return round((1 - $self_final_amount / $parent_final_amount) * 100, 2);
    }

    public function final_shrinkage_percent($final_amount = null)
    {
        // درصد جمع شدگی نهایی: 1-متراژ نهایی خودش / متراژ سیستم خودش *100
        $self_final_amount = $final_amount ? $final_amount : $this->getFinalAmount();
        $self_amount = $this->getAmount();
        if ($self_amount == 0) {
            return "-99999999";
        }

        return round((1 - $self_final_amount / $self_amount) * 100, 2);
    }


    /**
     * @param $packing_form_list
     * @param $packing_form
     * @param $count_select
     * گرفتن لیست بسته بندی هایی که بسته بندی فرعی ندارند
     *
     * @return array
     */
    public static function LowestLevelOfPackingFormIds($packing_form_list = [], $packing_form = null, $count_select = [])
    {
        // گرفتن پایین ترین سطح بسته بندی جهت انجام تراکنش انبار
        $lowestLevelPackingFormIds = [];

        if ($packing_form_list != []) {


            foreach ($packing_form_list as $packing_form) {
                // اگر یک بسته بندی master است، باید بسته بندی های فرعی آن انتخاب شوند.
                if ($packing_form->sub_packing_form_number == 0) {
                    $lowestLevelPackingFormIds[] = $packing_form->id;
                } else {
                    // اگر تعداد خاصی از بسته انتخاب شده است.
                    $k = isset($count_select[$packing_form->id]) ? $count_select[$packing_form->id] : 9999999;
                    if ($packing_form->packing_form_contents()->count() == 0) {  // بسته بندی های فرعی ایجاد نشده است
                        $lowestLevelPackingFormIds[] = $packing_form->id;
                    } else {
                        foreach ($packing_form->packing_form_contents()->OrderBy("id")->take($k)->get() as $packing_form_content) {
                            $lowestLevelItem = PackingForm::LowestLevelOfPackingFormIds([], $packing_form_content);
                            foreach ($lowestLevelItem as $loverItem) {
                                $lowestLevelPackingFormIds[] = $loverItem;
                            }
                        }
                    }
                }
            }
        } elseif ($packing_form) {

            if ($packing_form->sub_packing_form_number == 0) {

                $lowestLevelPackingFormIds[$packing_form->id] = $packing_form->id;
            } else {
                // اگر تعداد خاصی از بسته انتخاب شده است.
                $k = isset($count_select[$packing_form->id]) ? $count_select[$packing_form->id] : 9999999;

                if ($packing_form->packing_form_contents()->count() == 0) { // بسته بندی های فرعی ایجاد نشده است
                    $lowestLevelPackingFormIds[$packing_form->id] = $packing_form->id;
                } else {
                    foreach ($packing_form->packing_form_contents()->OrderBy("id")->take($k)->get() as $packing_form_content) {
                        $lowestLevelItem = PackingForm::LowestLevelOfPackingFormIds([], $packing_form_content);
                        foreach ($lowestLevelItem as $loverItem) {
                            $lowestLevelPackingFormIds[$loverItem] = $loverItem;
                        }
                    }
                }
            }

        }

        return $lowestLevelPackingFormIds;

    }

    /**
     * @param $packing_form_list
     * گرفتن لیست بسته بندی هایی که بسته بندی فرعی ندارند و داخل بسته دیگری هم نیستند.
     *
     * @return array
     */
    public static function MasterIsNullPackingForms($packing_form_list = [])
    {
        // گرفتن پایین ترین سطح بسته بندی جهت انجام تراکنش انبار
        $PackingForms = [];

        if ($packing_form_list != []) {


            foreach ($packing_form_list as $packing_form) {
                // اگر یک بسته بندی master نیست، باید بسته بندی  انتخاب شود.
                if (!isset($packing_form->packing_form_master_id)) {
                    $PackingForms[] = $packing_form;
                }
            }
        }

        return $PackingForms;

    }

    /**
     * @param $packing_form_list
     * @param $packing_form
     * گرفتن لیست بسته بندی هایی که بسته بندی فرعی دارند.
     *
     * @return array
     */
    public static function MasterPackingFormIds($packing_form_list = [], $packing_form = null)
    {
        // گرفتن بالاترین ترین سطح بسته بندی
        $masterPackingFormIds = [];

        if ($packing_form_list != []) {


            foreach ($packing_form_list as $packing_form) {

                if (isset($packing_form->packing_form_master_id)) {

                    $masterPackingFormIds[$packing_form->packing_form_master_id] = $packing_form->packing_form_master_id;

//                    foreach ( $packing_form->packing_form_contents as $packing_form_content ) {
//                        $masterItem = PackingForm::MasterPackingFormIds( [], $packing_form_content );
//                        foreach ( $masterItem as $item ) {
//                            $masterPackingFormIds[ $item ] = $item;
//                        }
//                    }
                } else {
                    $masterPackingFormIds[$packing_form->id] = $packing_form->id;
                }
//                if ( $packing_form->packing_form_master ) {
//                    $masterItem = PackingForm::MasterPackingFormIds( [] , $packing_form->packing_form_master );
//                    foreach ( $masterItem as $item ) {
//                        $masterPackingFormIds[ $item ] = $item;
//                    }
//                }
            }
        } elseif ($packing_form) {
            if (isset($packing_form->packing_form_master_id)) {

                $masterPackingFormIds[$packing_form->packing_form_master_id] = $packing_form->packing_form_master_id;

            } else {
                $masterPackingFormIds[$packing_form->id] = $packing_form->id;
            }

//            if (  $packing_form->sub_packing_form_number  > 0 ) {
//
//                $masterPackingFormIds[] = $packing_form->id;
//
//                foreach ( $packing_form->packing_form_contents as $packing_form_content ) {
//                    $masterItem = PackingForm::MasterPackingFormIds( [], $packing_form_content );
//                    foreach ( $masterItem as $item ) {
//                        $masterPackingFormIds[ $item ] = $item;
//                    }
//                }
//            }

        }

        return $masterPackingFormIds;
    }

    /**
     * @param $packing_form_ids
     * @param $pieces_counts
     * @param $packing_type_ids
     * جدا کردن تعدادی از بسته بندی ها از بسته بندی اصلی
     *
     * @return array
     */
    public static function MasterPackingCutIntoPieces($packing_form_ids, $pieces_counts,$pieces_amount, $packing_type_ids)
    {

// این تابع در صورتی کار می کند که تنوع آیتم در بسته بندی 1 باشد.
        if (count($pieces_counts) == 0 && count($pieces_amount) == 0) {
            return [];
        }

        $packing_form_where_cut_in_to_pieces = [];
        $packing_form_where_must_add_to_form = [];


        foreach ($packing_form_ids as $packing_form_id) {

            if (isset($pieces_counts[$packing_form_id]) || isset($pieces_amount[$packing_form_id])) {

                $pieces_count = isset($pieces_counts[$packing_form_id])?$pieces_counts[$packing_form_id]:0;

                $packing_form = PackingForm::find($packing_form_id);
                $packing_form_item = $packing_form->items()->first();
                $packing_form_sub_packing_count = $packing_form->sub_packing_form_number;
                // اگر مقدار مشخص نشده است، مثل قبل عمل می کنیم.
                $new_amount=isset($pieces_amount[$packing_form_id])?
                    $pieces_amount[$packing_form_id]:$packing_form_item->final_amount * $pieces_count;


                // بسته بندی باید حداقل یک آیتم داخل آن باشد.
                // اگر بسته بندی، چند لایت است، باید تعداد بسته بندی فرعی آن مشخص شده باشد و اگر یک لایه است، مقدار بسته بندی جدید باید مشخص شده باشد.
                if ($pieces_count < $packing_form_sub_packing_count || ($packing_form_sub_packing_count ==0 && isset($pieces_amount[$packing_form->id]))) {

                    // یک بسته بندی مشابه بسته بندی اصلی ایجاد می کند
                    $new_master_packing_form = null;
                    if (
                        isset($packing_type_ids[$packing_form_id]) &&
                        $packing_type_ids[$packing_form_id] > 0

                    ) {
                        // ایجاد فرم بسته بندی
                        $new_master_packing_form = PackingForm::create([
                            "packing_type_id" => $packing_type_ids[$packing_form_id],
                            "carrier_id" => null,
                            "status_id" => $packing_form->status_id,
                            "sub_packing_form_number" => $pieces_count,
                            "applicant_type_id" => $packing_form->applicant_type_id??null,
                            "applicant_id" => $packing_form->applicant_id??null,
                        ]);

                        $new_master_packing_form->warehouse_status_id = $packing_form->warehouse_status_id; //موجود در انبار
                        $new_master_packing_form->warehouse_id = $packing_form->warehouse_id;
                        $new_master_packing_form->save();

                        event(new PackingLogEvent($new_master_packing_form, 7007001));

                        // اضافه کردن یک ردیف



                        $new_master_packing_form_item = PackingFormItem::create([
                            "packing_form_id" => $new_master_packing_form->id,
                            "product_id" => $packing_form_item->product_id,
                            "lot_number_id" => $packing_form_item->lot_number_id,
                            "degree_id" => $packing_form_item->degree_id,
                            "amount" => $new_amount,
                            "amount_after_control" =>$new_amount,
                            "final_amount" =>  $new_amount,
                            "sub_amount" =>round( $packing_form_item->sub_amount * $new_amount / $packing_form_item->final_amount,4),
                            "init_sub_amount" =>round( $packing_form_item->sub_amount * $new_amount / $packing_form_item->final_amount,4),
                            "status_id" => 7006003, // بسته بندی شده
                            "band_code" => $packing_form_item->band_code
                        ]);
                        $new_master_packing_form_item->getCode($packing_form_item->band_code, 1);

                        $packing_form_where_cut_in_to_pieces[] = $new_master_packing_form;

                    }


                    // تغییر بسته بندی های فرعی
                    // بسته بندی مجازی ایجاد شده است.
                    if ($packing_form->packing_form_contents()->count() > 0) {
                        $packing_form_where_change_master_id = [-1];
                        // اگر بسته بندی های مجازی آن تولید شده باشد.
                        foreach ($packing_form->packing_form_contents()->take($pieces_count)->orderBy("id")->get() as $sub_packing_form) {
                            $sub_packing_form->packing_form_master_id = $new_master_packing_form->id ?? null;
                            $packing_form_where_change_master_id[] = $sub_packing_form->id;
                            //تغییر بسته بندی اصلی
                            event(new PackingLogEvent($sub_packing_form, 7007016, null, "", null, null, $packing_form->id));

                            if (!$new_master_packing_form) {
                                $sub_packing_form->warehouse_status_id = $packing_form->warehouse_status_id; //موجود در انبار
                                $sub_packing_form->status_id = $packing_form->status_id; // تحویل شده به انبار
                                // اگر بسته بندی اصلی ندارد، یعنی بسته فرعی می شود یک بسته اصلی (از بسته اصلی جدا می شود)
                                $packing_form_where_cut_in_to_pieces[] = $sub_packing_form;

                            }

                            $sub_packing_form->save();

                        }

                        $packing_form->sub_packing_form_number = $packing_form_sub_packing_count - $pieces_count;
                        $packing_form->save();

                        // چون ممکن است، مقدار آیتم های فرعی با هم برابر نباشند، بنابراین مقدار نهایی از نسبت نمی تواند محاسبه شود
                        // و باید یک بار دیگر مقدار محاسبه گردد.
                        $new_master_packing_form_values = PackingFormItem::whereIn("packing_form_id", $packing_form_where_change_master_id)->
                        selectRaw("sum(amount_after_control) as amount_after_control,sum(final_amount) as final_amount, sum(sub_amount) as sub_amount ")->
                        first();
                        // کم کردن مقدار بسته بندی اصلی
                        $packing_form_item->amount_after_control -= $new_master_packing_form_values->amount_after_control;
                        $packing_form_item->final_amount -= $new_master_packing_form_values->final_amount;
                        $packing_form_item->sub_amount -= $new_master_packing_form_values->sub_amount;
                        $packing_form_item->save();

                        // وزن بسته بندی اصلی وقتی بسته های فرعی ایجاد شده اند را بروز می کنیم.
                        $result_weight = PackingForm::UpdateWeight($packing_form);
                        if ($result_weight["result"]) {
                            $packing_form->weight = $result_weight["weight"];;
                            $packing_form->gross_weight = $result_weight["gross_weight"];
                            $packing_form->save();
                        }

                        // ویرایش مقدار بسته بندی
                        $new_master_packing_form_item->amount = $new_master_packing_form_values->final_amount;
                        $new_master_packing_form_item->amount_after_control = $new_master_packing_form_values->amount_after_control;
                        $new_master_packing_form_item->final_amount = $new_master_packing_form_values->final_amount;
                        $new_master_packing_form_item->sub_amount = $new_master_packing_form_values->sub_amount;
                        $new_master_packing_form_item->save();

                        // وزن بسته بندی جدید را وقتی بسته بندی های فرعی ایجاد شده اند بروز می کنیم
                        $result_weight = PackingForm::UpdateWeight($new_master_packing_form);
                        if ($result_weight["result"]) {
                            $new_master_packing_form->weight = $result_weight["weight"];;
                            $new_master_packing_form->gross_weight = $result_weight["gross_weight"];
                            $new_master_packing_form->save();
                        }


                    }
                    else {
                        // بسته بندی های مجازی ایجاد نشده اند، و الان کل بسته بندی تغییر می دهیم.
                        // اگر به صورت بسته بندی مستر خواسته باشد، باد همان مستر را ورود کنیم.
                        if (!$new_master_packing_form) {

                            // ایجاد بسته بندی جدید با نوع بسته بندی بسته های فرعی

                            // ایجاد فرم بسته بندی
                            $new_master_packing_form = PackingForm::create([
                                "packing_type_id" => $packing_form->packing_type_id,
                                "carrier_id" => null,
                                "status_id" => $packing_form->status_id,
                                "sub_packing_form_number" => $pieces_count
                            ]);

                            $new_master_packing_form->warehouse_status_id = $packing_form->warehouse_status_id; //موجود در انبار
                            $new_master_packing_form->warehouse_id = $packing_form->warehouse_id;
                            $new_master_packing_form->save();

                            event(new PackingLogEvent($new_master_packing_form, 7007001));

                            // اضافه کردن یک ردیف

                            $new_master_packing_form_item = PackingFormItem::create([
                                "packing_form_id" => $new_master_packing_form->id,
                                "product_id" => $packing_form_item->product_id,
                                "lot_number_id" => $packing_form_item->lot_number_id,
                                "degree_id" => $packing_form_item->degree_id,
                                "amount" => $new_amount,
                                "amount_after_control" =>$new_amount,
                                "final_amount" =>  $new_amount,
                                "sub_amount" =>round( $packing_form_item->sub_amount * $new_amount / $packing_form_item->final_amount,4),
                                "init_sub_amount" =>round( $packing_form_item->sub_amount * $new_amount / $packing_form_item->final_amount,4),
                                "status_id" => 7006003, // بسته بندی شده
                                "band_code" => $packing_form_item->band_code,
                            ]);
                            $new_master_packing_form_item->getCode($packing_form_item->band_code, 1);

                            $packing_form_where_cut_in_to_pieces[] = $new_master_packing_form;


                        }

                        // ثبت تغییر بسته بندی
                        ChangeInWarehouseController::ExitInputForm(
                            $packing_form,
                            [$new_master_packing_form],
                            1,
                            $packing_form_sub_packing_count - $pieces_count,
                            $new_master_packing_form_item->final_amount,
                            $new_master_packing_form_item->sub_amount,
                        );

                        $packing_form_where_must_add_to_form[] = $new_master_packing_form;


                        // کم کردن مقدار بسته بندی اصلی
                        $packing_form_item->sub_amount -= $packing_form_item->sub_amount * $new_amount / $packing_form_item->final_amount;
                        $packing_form_item->amount_after_control -= $new_amount;
                        $packing_form_item->final_amount -= $new_amount;
                        $packing_form_item->save();

                    }


                    event(new PackingLogEvent($packing_form, 7007015));
                }

            }
        }


        return [
            "packing_form_where_cut_in_to_pieces" => $packing_form_where_cut_in_to_pieces,
            "packing_form_where_must_add_to_form" => $packing_form_where_must_add_to_form
        ];
    }

    /**
     * @param \App\Models\Form\Packing\PackingForm $master_packing_form
     * @param                                      $sub_packing_form_number
     * ایجاد بسته بندی فرعی برای بسته بندی
     *
     * @return array|bool[]
     */
    public static function CreateSubPacking(PackingForm $master_packing_form, $sub_packing_form_number, $status_id)
    {

        if (!$master_packing_form->packing_type->create_sub_packing_form_in_creation) {
            $master_packing_form->sub_packing_form_number = $sub_packing_form_number;
            $master_packing_form->save();

            return ["result" => true, "message" => "تنظیمات ایجاد بسته بندی فرعی غیرفعال است."];
        }

        if (!$master_packing_form->packing_type->first_packing_type_id) {
            return ["result" => false, "error" => "تعریف بسته بندی نادرست است."];
        }

        if ($sub_packing_form_number <= 0) {
            return ["result" => false, "error" => "تعداد بسته بندی فرعی نادرست است."];
        }

        for ($p = 0; $p < $sub_packing_form_number; $p++) {
            $packing_form_list[] = [
                "packing_type_id" => $master_packing_form->packing_type->first_packing_type_id,
                "carrier_id" => null,
                "status_id" => $status_id,
                "packing_form_master_id" => $master_packing_form->id,
                "created_at" => now(),
                "reality_type_id" => 2, // بسته بندی های مجازی
                "weight" => $master_packing_form->weight / $sub_packing_form_number,
                "gross_weight" => $master_packing_form->gross_weight / $sub_packing_form_number,
            ];
        }

        PackingForm::insert($packing_form_list);

        $master_packing_form->sub_packing_form_number = $sub_packing_form_number;
        $master_packing_form->save();


        return ["result" => true];
    }

    public static function LowOffFromPackingForm(PackingFormItem $packing_form_item, $low_of_amount)
    {

        if ($low_of_amount > 0) {

            // تغییر مقدار بسته بندی
            $packing_form_item->sub_amount -=
                $packing_form_item->final_amount == 0 ?
                    $low_of_amount :
                    $low_of_amount *
                    $packing_form_item->sub_amount /
                    $packing_form_item->final_amount;


            $packing_form_item->final_amount -= $low_of_amount;
            $packing_form_item->save();

            // اگر بسته بندی دارای بسته بندی مستر است، باید بسته بندی مستر هم بروز شود.
            if ($packing_form_item->packing_form->packing_form_master) {

                $packing_form_master_item =
                    $packing_form_item->packing_form->packing_form_master->
                    items()->
                    where("product_id", $packing_form_item->product_id)->
                    first();

                $packing_form_master_item->sub_amount -=
                    $packing_form_master_item->sub_amount == 0 ?
                        $low_of_amount :
                        $low_of_amount *
                        $packing_form_master_item->sub_amount /
                        $packing_form_master_item->final_amount;

                $packing_form_master_item->final_amount -= $low_of_amount;

                $packing_form_master_item->save();
            }


        }

    }

    public static function CheckChangePackingIsOK($packing_form)
    {

        if (!$packing_form) {
            return ["result" => true, "warning" => "بسته بندی وجود ندارد. "];
        }
// چک کردن اینکه بسته بندی بعدی از تغییر همه چیز آن درست است.
        $packing_form_child_ids = PackingForm::where("packing_form_parent_id", $packing_form->id)->pluck("id");

        $message = "";
        // بررسی تعداد کالاها
        $packing_form_child_product_ids = PackingFormItem::
        whereIn("packing_form_id", $packing_form_child_ids)->
        groupBy("product_id")->
        orderBy("product_id")->
        pluck("product_id", "product_id")->
        toArray();

        $packing_form_product_id = PackingFormItem::
        where("packing_form_id", $packing_form->id)->
        groupBy("product_id")->
        orderBy("product_id")->
        pluck("product_id", "product_id")->
        toArray();


        if (
            count(array_diff($packing_form_child_product_ids, $packing_form_product_id)) != 0 ||
            count(array_diff($packing_form_product_id, $packing_form_child_product_ids)) != 0
        ) {
            $message = "در تغییر  بسته بندی " . $packing_form->code . " یک ناهنجاری در کالا ها به وجود آمده، لطفا به قید فوریت با واحد پشتیبانی تماس بگیرید. ";

            return ["result" => false, "error" => $message];
        }

        // بررسی تعداد لات ها
        $packing_form_child_lot_number_id = PackingFormItem::
        whereIn("packing_form_id", $packing_form_child_ids)->
        groupBy("lot_number_id")->
        orderBy("product_id")->
        pluck("product_id")->
        toArray();

        $packing_form_lot_number_id = PackingFormItem::
        where("packing_form_id", $packing_form->id)->
        groupBy("lot_number_id")->
        orderBy("product_id")->
        pluck("product_id")->
        toArray();

        if (count(array_diff($packing_form_child_lot_number_id, $packing_form_lot_number_id)) != 0 ||
            count(array_diff($packing_form_lot_number_id, $packing_form_child_lot_number_id)) != 0) {
            $message = "در تغییر  بسته بندی " . $packing_form->code . " یک ناهنجاری در لات ها به وجود آمده، لطفا به قید با واحد پشتیبانی تماس بگیرید.";

            return ["result" => false, "error" => $message];

        }

        if ($message == "") {
            return ["result" => true];
        } else {
            return ["result" => false, "error" => $message];
        }

    }

    /**
     * @param PackingForm $packingForm
     * @param $machine_operation_discharge_type_id
     * @return array
     */
    //با توجه به نوع تخلیه و نوع حرکت مواد تصمیم می گیریم که بسته بندی ها به چه شکلی به فرم تولید اضافه شوند.
    public static function GetFirstStackDisplayOrder(PackingType $packingType, $machine_operation_discharge_type_id)
    {
        $packing_form_discharge_type_id = $packingType->discharge_type_id ?? -1; // پیش فرض FIFo

        if ($packing_form_discharge_type_id == -1) {
            return [
                "result" => false,
                "error" => "نوع تخلیه برای بسته بندی " . $packingType->code . " -" . $packingType->caption . " مشخص نشده است."

            ];
        }
        $normal = [
            "result" => 1,
            "asc_or_desc" => "asc",
        ];

        $inverse = [
            "result" => 1,
            "asc_or_desc" => "desc",
        ];


        switch ($machine_operation_discharge_type_id) {
            case 2: // دقیقا FiFo
            case 4: // تقریبا FiFo

                switch ($packing_form_discharge_type_id) {
                    case 1: // دقیقا LiFo
                    case 3: // تقریبا LiFo
                        return $inverse;
                        break;

                    case 2: // FiFo تقریبا
                    case 4: // FiFo دقیقا
                        return $normal;
                        break;

                    case 5: // درهم
                        return $normal;
                        return [
                            "result" => false,
                            "error" => "این امکان هنوز پیاده سازی نشده است." . " (نوع تخلیه مواد در بسته بندی 1)"
                        ];
                }

                break;

            case 1: // دقیقا لایفو (LiFo)
            case 3: // تقریبا لایفو (LiFo)

                switch ($packing_form_discharge_type_id) {
                    case 1: // دقیقا LiFo
                    case 3: // تقریبا LiFo
                        return $normal;
                        break;

                    case 2: // FiFo تقریبا
                    case 4: // FiFo دقیقا
                        return $inverse;
                        break;

                    case 5: // درهم
                        return [
                            "result" => false,
                            "error" => "این امکان هنوز پیاده سازی نشده است." . " (نوع تخلیه مواد در بسته بندی 4)"
                        ];
                }

                break;

            case 5: // درهم

                return $normal; // فرقی نمی کند که چی باشه
//                return [
//                    "result" => false,
//                    "error" => "این امکان هنوز پیاده سازی نشده است." . " (نوع تخلیه مواد در بسته بندی 2)"
//                ];


                break;
        }


        return [
            "result" => false,
            "error" => "این امکان هنوز پیاده سازی نشده است." . " نوع تخلیه مواد در بسته بنید 3"
        ];
    }

    public static function GetInventoryBuyPackingType(Product $product)
    {
       $inventory= PackingForm::join("packing_form_item", "packing_forms.id", "packing_form_id")->
        where("product_id", $product->id)->
        where("warehouse_status_id", 4201)->
        whereIn("packing_forms.status_id",
            [
                7007003, // تحویل شده به انبار
            ]
        )->
        groupBy("packing_type_id")->
        selectRaw("sum(final_amount) as final_amount, packing_type_id")->
        pluck("final_amount", "packing_type_id")->toArray();

        $packing_type_ids=[];
        foreach ($inventory as $key=>$inventory_item) {
            $packing_type_ids[] = $key;
        }
        $packing_type_ids[]=-1;
        $packing_type_list=PackingType::whereIn("id", $packing_type_ids)->get();

        $packing_type_caption=[];
        foreach ($packing_type_list as $item) {
            $packing_type_caption[] = [
                "caption" => $item->caption,
                "inventory"=>$inventory[$item->id],
            ];
        }
        return [
            "result" => true,
            "packing_type_ids" => $packing_type_ids,
            "inventory" => $inventory,
            "packing_type_list"=>$packing_type_list,
            "packing_type_caption"=>$packing_type_caption,
        ];
    }
}
