<?php

namespace App\Models\Form;

use App\Events\ProductionCard\ProductionCardLogEvent;
use App\Events\Warehouse\Form\FormLogEvent;
use App\Http\Controllers\Utility\Script\Script1013Controller;
use App\Models\Accounting\CostCenter;
use App\Models\Contractor\Contractor;
use App\Models\Customer\Customer;
use App\Models\Form\Packing\PackingForm;
use App\Models\Form\Packing\PackingFormItem;
use App\Models\GoodsKindProcess\Fabric_Raw\FabricRaw;
use App\Models\LineProduct\Carrier\Carrier;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineAllocation;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormForm;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormItem;
use App\Models\Order\TransKind;
use App\Models\Production\ProductionFormItem;
use App\Models\Production\ProductionLog;
use App\Models\Supplier\Supplier;
use App\Models\Utility\Financial\FinancialSoftwareTransferForm;
use App\Models\Utility\Message;
use App\Models\Warehouse\Warehouse;
use App\Models\Warehouse\WarehouseProduct;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Production\Production;
use App\Models\Utility\Status;
use App\Models\Worker;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class Form extends Model
{
    use HasFactory;

    protected $table = "forms";
    protected $fillable = [
        // "order_id", // remove
        // "order_list_id", // remove
        // "production_card_id", // remove
        "user_id",
        "form_type_id",
        "trans_kind",
        "warehouse_id",
        "ic",
        "status_id",
        "message_id",
        "allocation_id",
        "applicant_type_id",
        "applicant_id",
        "one_time_confirmation",
        "financial_software_status_id"
    ];

    public function items()
    {
        //  return
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function cost_center_caption()
    {
        $ic = CostCenter::where("code", $this->ic)->first();
        return $ic->caption ?? "";
    }

    public function get_create_date()
    {
        return jdate(Carbon::parse($this->created_at)->timestamp)->format('Y/m/d');
    }

    public function get_create_time()
    {
        return jdate(Carbon::parse($this->created_at)->timestamp)->format('H:i');
    }

    public function get_create_date_and_time()
    {
        return jdate(Carbon::parse($this->created_at)->timestamp)->format('H:i Y/m/d ');

    }

    public function getPacingForm()
    {
        return PackingForm::where("form_id", $this->id)->first();
    }

    public function getPackingFormList()
    {
        $packing_form_item_ids = $this->item()->pluck("packing_form_item_id")->toArray();

        return PackingForm::join("packing_form_item", "packing_form_id", "packing_forms.id")->
        whereIn("packing_form_item.id", $packing_form_item_ids)->
        groupBy("packing_form_id")->
        get();
    }

    public function getSoftwareTransferFormStatus($type)
    {
        switch ($type) {
            case 10:

                return $list = FinancialSoftwareTransferForm::

                join("status", "status.id", "accounting_document_status_id")->
                where("form_id", $this->id)->
                groupBy("accounting_document_status_id")->
                selectRaw("count(*) as count,accounting_document_status_id,status.caption ")->get()->keyBy("accounting_document_status_id");
                break;
            case 20:

                return $list = FinancialSoftwareTransferForm::

                join("status", "status.id", "warehouse_transaction_status_id")->
                where("form_id", $this->id)->
                groupBy("warehouse_transaction_status_id")->
                selectRaw("count(*) as count,warehouse_transaction_status_id,status.caption ")->get()->keyBy("warehouse_transaction_status_id");
                break;
            case 30:

                return $list = FinancialSoftwareTransferForm::

                join("status", "status.id", "sale_invoice_status_id")->
                where("form_id", $this->id)->
                groupBy("sale_invoice_status_id")->
                selectRaw("count(*) as count,sale_invoice_status_id,status.caption ")->get()->keyBy("sale_invoice_status_id");
                break;

        }
    }

    public static function CreateFrom($data)
    {

        // وضعیت ثبت در نرم افزار های مالی
        //  $data["financial_software_status_id"] = Script1013Controller::GetInitFormStatus();

        return $form = Form::create($data);


    }

    public function getCode($perfix = "")
    {

        if (in_array($this->form_type_id, [304, 306, 401])) {
            $perfix = "DCRF"; // Receiving form
        }
        if (in_array($this->form_type_id, [0])) {
            $perfix = "DCEF"; // Exit form
        }
        if ($this->code != null || $perfix == "") {
            return $this->code;
        }
        $counter = $this->id + 1000;

        $this->code = $perfix . "/" . ($counter);
        $this->save();

        return $this->code;
    }

    public function getNumberCode()
    {
        $counter = $this->id + 1000;

        return $counter;
    }

    public function getRandom()
    {
        if ($this->random == null) {
            $this->random = Str::random(4);
            $this->save();
        }

        return $this->random;
    }

    public function production()
    {
        return $this->belongsTo(Production::class, "production_card_id", "id");
    }


    public function item()
    {
        return $this->hasMany(FormItem::class, "form_id", "id")->orderBy("product_id");
    }

    public function general_items()
    {
        return $this->hasMany(FormGeneralItem::class, "form_id", "id")->orderBy("product_id");
    }

    public function financial_software_transfer_form()
    {
        return $this->hasMany(FinancialSoftwareTransferForm::class);
    }

    public function paginate($items, $perPage = 5, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);

        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

    public function itemOrderByTransportCode($type = "group_by_packing_form_item", $paginate = false)
    {

        // در این تابع با توجه به نوع نیاز لیست آیتم ها آماده می شوند.


        switch ($type) {
            case "group_by_packing_form_item":
                $list_id_order_by_transport_id = FormItem::
                join("packing_form_item", "packing_form_item.id", "packing_form_item_id")->
                leftJoin("transport_packing_form", "transport_packing_form.packing_form_id", "packing_form_item.packing_form_id")->
                orderBy("transport_packing_form.transport_item_id")->
                orderBy("transport_packing_form.packing_form_id")->
                where("form_item.form_id", $this->id)->
                selectRaw("form_item.id")->
                get();
                $form_items = [];
                foreach ($list_id_order_by_transport_id as $item_row) {
                    foreach ($this->item as $item) {
                        if ($item->id == $item_row->id) {
                            $form_items[] = $item;
                        }
                    }
                }

                return $form_items;
                break;

            case "group_by_packing_form":

                // بسته بندی های اصلی که بسته بندی داخلی ندارند
                $packing_form_ids = FormItem::
                join("packing_form_item", "packing_form_item.id", "packing_form_item_id")->
                join("packing_forms", "packing_forms.id", "packing_form_id")->
                where("form_item.form_id", $this->id)->
                whereNull("packing_form_master_id")->
                pluck("packing_form_id")->
                toArray();
//get();

                $list_id_order_by_transport_id = FormItem::
                join("packing_form_item", "packing_form_item.id", "packing_form_item_id")->
                leftJoin("transport_packing_form", "transport_packing_form.packing_form_id", "packing_form_item.packing_form_id")->
                orderBy("transport_packing_form.transport_item_id")->
                orderBy("transport_packing_form.packing_form_id")->
                where("form_item.form_id", $this->id)->
                whereIn("packing_form_item.packing_form_id", $packing_form_ids)->
                selectRaw("form_item.id")->
                get();

                $list_group = [];
                $packing_form_item_before_read = [];
                // گروه بندی بر اساس کد بسته بندی و درجه آیتم ها
                foreach ($this->item as $item) {
                    $new_packing_form_item = 0;
                    // برای اینکه تعداد بسته بندی هایی که به دو ردیف تحویل شده اند دوبار محاسبه نشود.
                    if (!isset($packing_form_item_before_read[$item->packing_form_item_id])) {
                        $packing_form_item_before_read[$item->packing_form_item_id] = 1;
                        $new_packing_form_item = 1;
                    }
                    if (isset($item->packing_form_item->packing_form_id)) {
                        if (!isset($list_group[$item->packing_form_item->packing_form_id . "_" . $item->degree_id])) {
                            $list_group[$item->packing_form_item->packing_form_id . "_" . $item->degree_id] = $item;
                            $list_group[$item->packing_form_item->packing_form_id . "_" . $item->degree_id]["item_count"] = $new_packing_form_item;
                            $list_group[$item->packing_form_item->packing_form_id . "_" . $item->degree_id]["carrier"] = $item->carrier->code ?? "";
                            $list_group[$item->packing_form_item->packing_form_id . "_" . $item->degree_id]["line_input"] = $item->io_line_code ?? "";
                            $list_group[$item->packing_form_item->packing_form_id . "_" . $item->degree_id]["description"] = $item->product_request_form_item->production->serial ?? "";

                        } else {
                            $list_group[$item->packing_form_item->packing_form_id . "_" . $item->degree_id]->amount += $item->amount;
                            $list_group[$item->packing_form_item->packing_form_id . "_" . $item->degree_id]->sub_amount += $item->sub_amount;
                            $list_group[$item->packing_form_item->packing_form_id . "_" . $item->degree_id]["item_count"] += $new_packing_form_item;
                            $list_group[$item->packing_form_item->packing_form_id . "_" . $item->degree_id]["carrier"] .= ", " . ($item->carrier->code ?? "");
                            $list_group[$item->packing_form_item->packing_form_id . "_" . $item->degree_id]["line_input"] .= ", " . ($item->io_line_code ?? "");
                            $list_group[$item->packing_form_item->packing_form_id . "_" . $item->degree_id]["description"] .=
                                (
                                isset($item->product_request_form_item->production->serial) ?
                                    "<br/> " . $item->product_request_form_item->production->serial :
                                    ""
                                );


                        }
                    }

                }
                $form_items = [];
                foreach ($list_id_order_by_transport_id as $item_row) {
                    foreach ($list_group as $item) {
                        if ($item->id == $item_row->id) {
                            $form_items[] = $item;
                        }
                    }
                }

                if ($paginate) {
                    return $this->paginate($form_items, 10, null, [
                        "path" => route("wh.show_output_packing_form", [
                            $this,
                            $this->random
                        ])
                    ]);
                }

                return $form_items;
                break;

            case "group_by_product":


                $list_id_order_by_transport_id = FormItem::
                leftjoin("packing_form_item", "packing_form_item.id", "packing_form_item_id")->
                leftJoin("transport_packing_form", "transport_packing_form.packing_form_id", "packing_form_item.packing_form_id")->
                orderBy("transport_packing_form.transport_item_id")->
                orderBy("transport_packing_form.packing_form_id")->
                where("form_item.form_id", $this->id)->
                selectRaw("form_item.id")->
                get();

                $list_group = [];
                // گروه بندی بر اساس کد کالا و درجه آیتم ها
                foreach ($this->item as $item) {
                    if (!isset($list_group[$item->product_id . "_" . $item->degree_id])) {
                        $list_group[$item->product_id . "_" . $item->degree_id] = $item;
                        $list_group[$item->product_id . "_" . $item->degree_id]["item_count"] = 1;
                        $list_group[$item->product_id . "_" . $item->degree_id]["carrier"] = $item->carrier->code ?? "";
                        $list_group[$item->product_id . "_" . $item->degree_id]["line_input"] = $item->io_line_code ?? "";
                        $list_group[$item->product_id . "_" . $item->degree_id]["description"] = $item->product_request_form_item->production->serial ?? "";
                    } else {
                        $list_group[$item->product_id . "_" . $item->degree_id]->amount += $item->amount;
                        $list_group[$item->product_id . "_" . $item->degree_id]->sub_amount += $item->sub_amount;
                        $list_group[$item->product_id . "_" . $item->degree_id]["item_count"] += 1;
                        $list_group[$item->product_id . "_" . $item->degree_id]["carrier"] .= ", " . ($item->carrier->code ?? "");
                        $list_group[$item->product_id . "_" . $item->degree_id]["line_input"] .= ", " . ($item->io_line_code ?? "");
                        $list_group[$item->product_id . "_" . $item->degree_id]["description"] .=
                            (isset($item->product_request_form_item->production->serial) ?
                                "<br/> " . $item->product_request_form_item->production->serial :
                                ""
                            );
                    }

                }
                $form_items = [];
                foreach ($list_id_order_by_transport_id as $item_row) {
                    foreach ($list_group as $item) {
                        if ($item->id == $item_row->id) {
                            $form_items[] = $item;
                        }
                    }
                }

                return $form_items;
                break;


            case "group_by_packing_form_item_parent_product":

//                $list_id_order_by_transport_id = FormItem::
//                leftjoin("packing_form_item", "packing_form_item.id", "packing_form_item_id")->
//                leftJoin("transport_packing_form", "transport_packing_form.packing_form_id", "packing_form_item.packing_form_id")->
//                orderBy("transport_packing_form.transport_item_id")->
//                orderBy("transport_packing_form.packing_form_id")->
//                where("form_item.form_id", $this->id)->
//                selectRaw("form_item.id")->
//                get();

                $list_group = [];
                $packing_count_list = [];// چون ممکن است یک بسته بندی دو آیتم داشته باشد، بنابراین باید، روش محاسبه تعداد بسته بندی را روی بسته بندی ها ببریم نه روی آیتم بسته بندی
                $production_parent_list = [];
                // برای برخی از بسته بندی ها ممکن است که مجوز ثبت کرده باشند، و کارت تولید سطح بالای آنها متفاوت باشد
                // لیست مجوزهایی که برای کارت های سطح بالا دریافت می شود.

                // گروه بندی بر اساس کد کالا و درجه - کارت سطح بالا
                foreach ($this->item()->with("packing_form_item")->get() as $item) {
                    $parent_production = $item->product_request_form_item->production ?? null;
                    $parent_product = $parent_production->product ?? null;
                    $parent_product_id = $parent_product->id ?? 0;

                    if (!isset($list_group[$item->product_id . "_" . $item->degree_id . "_" . $parent_product_id])) {
                        $list_group[$item->product_id . "_" . $item->degree_id . "_" . $parent_product_id] = $item;
                        $packing_count_list[$item->product_id . "_" . $item->degree_id . "_" . $parent_product_id] = [];
                        $packing_count_list[$item->product_id . "_" . $item->degree_id . "_" . $parent_product_id][$item->packing_form_item->packing_form_id] = 1;
                        $production_parent_list[$item->product_id . "_" . $item->degree_id . "_" . $parent_product_id][$parent_production->id ?? 0] = $parent_production->serial ?? 0;
                        $list_group[$item->product_id . "_" . $item->degree_id . "_" . $parent_product_id]["carrier"] = $item->carrier->code ?? "";
                        $list_group[$item->product_id . "_" . $item->degree_id . "_" . $parent_product_id]["line_input"] = $item->io_line_code ?? "";
                        $list_group[$item->product_id . "_" . $item->degree_id . "_" . $parent_product_id]["description"] = $item->product_request_form_item->production->serial ?? "";
                        $list_group[$item->product_id . "_" . $item->degree_id . "_" . $parent_product_id]["parent_product"] = $parent_product;
                        $list_group[$item->product_id . "_" . $item->degree_id . "_" . $parent_product_id]["parent_product"] = $parent_product;
                        $list_group[$item->product_id . "_" . $item->degree_id . "_" . $parent_product_id]["parent_production"] = $parent_production;
                        $list_group[$item->product_id . "_" . $item->degree_id . "_" . $parent_product_id]["packing_type_caption"] = $parent_production ? $parent_production->getPackingType("caption_br") : "";


                        $list_group[$item->product_id . "_" . $item->degree_id . "_" . $parent_product_id]["yarn_types"] = FabricRaw::GetYarnType($item->product, 220354); // جنس نخ


                    } else {
                        $list_group[$item->product_id . "_" . $item->degree_id . "_" . $parent_product_id]->amount += $item->amount;
                        $list_group[$item->product_id . "_" . $item->degree_id . "_" . $parent_product_id]->sub_amount += $item->sub_amount;
                        $packing_count_list[$item->product_id . "_" . $item->degree_id . "_" . $parent_product_id][$item->packing_form_item->packing_form_id] = 1;
                        $list_group[$item->product_id . "_" . $item->degree_id . "_" . $parent_product_id]["carrier"] .= ", " . ($item->carrier->code ?? "");
                        $list_group[$item->product_id . "_" . $item->degree_id . "_" . $parent_product_id]["line_input"] .= ", " . ($item->io_line_code ?? "");

                        $list_group[$item->product_id . "_" . $item->degree_id . "_" . $parent_product_id]["description"] .=
                            (isset($item->product_request_form_item->production->serial) ?
                                "<br/> " . $item->product_request_form_item->production->serial :
                                ""
                            );

                        $production_parent_list[$item->product_id . "_" . $item->degree_id . "_" . $parent_product_id][$parent_production->id ?? 0] = $parent_production->serial ?? 0;
                    }

                }

                foreach ($packing_count_list as $key => $packing_count) {
                    $list_group[$key]["item_count"] = count($packing_count);
                    $list_group[$key]["parent_production_list"] = $production_parent_list[$key];

                }
                return $list_group;
                break;
        }


    }


    public function getMasterPackingFrom()
    {
        // لیست همه بسته بندی هایی اصلی که مستر هستند.
        // برای نمایش و پرینت بسته بندی های مستر در  فرم خروج از انبار
        // به دست آوردن لیست بسته بندی های مستر
        $master_packing_form_ids = FormItem::
        join("packing_form_item", "packing_form_item.id", "packing_form_item_id")->
        join("packing_forms", "packing_forms.id", "packing_form_id")->
        where("form_item.form_id", $this->id)->
        whereNotNull("packing_form_master_id")->
        distinct("packing_form_master_id")->
        pluck("packing_form_master_id")->
        toArray();

        return PackingForm::whereIn("id", $master_packing_form_ids)->get();
    }

    public function getPackingFrom()
    {
        // لیست همه بسته بندی هایی اصلی که مستر نیستند.
        $list = PackingForm::
        join("packing_form_item", "packing_form_item.packing_form_id", "packing_forms.id")->
        join("form_item", "form_item.packing_form_item_id", "packing_form_item.id")->
        where("form_item.form_id", $this->id)->
        groupBy("packing_forms.id")->
        whereNull("packing_form_master_id")->
        select("packing_forms.*")->
        get();


        return $list;
    }

    public function getPackingFromCount()
    {
        return count($this->getMasterPackingFrom()) + count($this->getPackingFrom());
    }

    public function getLowerPackingFormItem()
    {

        // برای نمایش و پرینت بسته بندی های پایین ترین سطح در فرم خروج از انبار
        $packing_form_ids = FormItem::
        join("packing_form_item", "packing_form_item.id", "packing_form_item_id")->
        join("packing_forms", "packing_forms.id", "packing_form_id")->
        where("form_item.form_id", $this->id)->
        whereNull("packing_form_master_id")->
        pluck("packing_form_id")->
        toArray();

        return $list_id_order_by_transport_id = FormItem::
        join("packing_form_item", "packing_form_item.id", "packing_form_item_id")->
        leftJoin("transport_packing_form", "transport_packing_form.packing_form_id", "packing_form_item.packing_form_id")->
        orderBy("transport_packing_form.transport_item_id")->
        orderBy("transport_packing_form.packing_form_id")->
        where("form_item.form_id", $this->id)->
        whereIn("packing_form_item.packing_form_id", $packing_form_ids)->
        select("form_item.*")->
        get();
    }

    public function getTransportCount()
    {
        // تعداد بسته بندی های باربریی که در آیتم های فرم در آن وجود دارد

        $list_ids = FormItem::
        join("packing_form_item", "packing_form_item.id", "packing_form_item_id")->
        join("transport_packing_form", "transport_packing_form.packing_form_id", "packing_form_item.packing_form_id")->
        where("form_item.form_id", $this->id)->
        pluck("transport_item_id", "transport_item_id");

        return count($list_ids);

    }

    public function worker()
    {
        return $this->belongsTo(Worker::class, "user_id", "id");
    }

    public function warehouse_product()
    {
        return WarehouseProduct::where("form_id", $this->id)->get();
    }

    /***
     * گرفتن تاریخ تراکنش انبار
     * @return void
     */
    public function get_transaction_time()
    {
        $first_warehouse_product = $this->warehouse_product();
        if (count($first_warehouse_product) > 0) {
            return $first_warehouse_product[0]->get_create_date();
        }
        return null;
    }

    public function getCarrierCation()
    {
        $packing_form = PackingForm::where("form_id", $this->id)->first();
        if (isset($packing_form) && $packing_form->carrier) {
            return $packing_form->carrier->getCaption();
        }

        return "فاقد حامل";
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function financial_software_status()
    {
        return $this->belongsTo(Status::class, "financial_software_status_id");
    }


    public function trans_kind_item()
    {
        return $this->belongsTo(TransKind::class, "trans_kind", "id");
    }

    public function message()
    {
        return $this->belongsTo(Message::class, "message_id", "id");
    }

    public function product_request_form_form()
    {
        return $this->hasMany(ProductRequestFormForm::class, "form_id", "id");
    }

    public function logs()
    {
        return $this->hasMany(FormLog::class);
    }

    public function allocation()
    {
        return $this->belongsTo(Allocation::class);
    }

    /**
     * @return string
     * گرفتن مرجع با توجه به فرم درخواست کالا از انبار
     */
    public function getApplicantCaption()
    {
        $prf = $this->product_request_form_form->first();
        if ($prf->product_request_form->applicant) {
            return $prf->product_request_form->applicant->fullCaption();
        }

        return "***";
    }

    /**
     * @return string
     * گرفتن مرجع با توجه به اطلاعات کالا
     */
    public function getFormApplicationCaption()
    {
        $object = null;
        switch ($this->applicant_type_id) {
            case 10:
                $object = Machine::find($this->applicant_id);
                break;
            case 20:
                $object = Contractor::find($this->applicant_id);
                break;
            case 30:
                $object = Customer::find($this->applicant_id);
                break;
            case 40:// انبارک ماشین
                $object = Warehouse::find($this->applicant_id);
                break;
            case 60: // تامین کننده (تحویل امانی - قرض)
            case 70: // تامین کننده ( برگشت از خرید)
                $object = Supplier::find($this->applicant_id);
                break;
            case$object =
            $object = Worker::find($this->applicant_id);
                break;
        }

        return $object ? $object->fullCaption() : "";
    }

    public function getAllProductRequestFormCodes($type = "code")
    {
        // ممکن است با یک برگ خروج چند درخواست تحویل شود.
        switch ($type) {
            case "code":
                $list = ProductRequestFormForm::where("form_id", $this->id)->get();
                $code = "";
                foreach ($list as $item) {
                    $code .= $item->product_request_form->getCode() . ", ";
                }
                $code = trim($code, ", ");

                return $code;
                break;

            case "first_form_form":
                return ProductRequestFormForm::where("form_id", $this->id)->first();

                break;
        }
        1 / 0;

    }

    public function allow_confirmation_according_applicant()
    {
        $product_request_form_form = ProductRequestFormForm::where("form_id", $this->id)->first();
        if (!$product_request_form_form->product_request_form) {
            return false;
        }
        $product_request_form = $product_request_form_form->product_request_form;

        return $product_request_form->allow_confirmation_according_applicant();
    }

    public function hasWarehouseTransaction()
    {
        return $wp_row_exists = WarehouseProduct::where("form_id", $this->id)->exists();
    }

    public static function ExitFormStatus()
    {
        return [500000500, 500000514, 500000515, 500000520, 500000525, 500000530, 500000535, 500000100, 500000200];
    }

    public static function nextStatusForInputForm(Form $form)
    {
        $has_general_item = $form->general_items()->count() > 0 ? true : false;
        if (!$form->allocation && $form->applicant_type_id && $form->applicant_type_id == 30) {
            // فرم هایی که به عنوان تحویل کالای امانی در فروش ثبت می کنند.
            return Customer::find($form->applicant_id)->nextStatusForInputForm($form->status_id, $has_general_item);
        }
        if ($form->allocation->machine) {
            1 / 0; // برای ماشین پیاده سازی نشده است.
        } elseif ($form->allocation->contractor) {
            return $form->allocation->contractor->nextStatusForInputForm($form->status_id, $has_general_item);

        } elseif ($form->allocation->supplier) {
            return $form->allocation->supplier->nextStatusForInputForm($form->status_id, $has_general_item);

        } elseif ($form->allocation->order) {
            return $form->allocation->order->customer->nextStatusForInputForm($form->status_id, $has_general_item);

        } else {
            1 / 0;
        }
    }

    public function referenceForInputForm()
    {
        if ($this->allocation) {
            if ($this->allocation->contractor) {
                $machine_allocation = $this->allocation->items;
                if (count($machine_allocation) > 0) {
                    return "دستور پیمان " . $machine_allocation->first()->production->serial;
                }

                return "تخصیص شماره" . $this->allocation_id;
            }
            if ($this->allocation->supplier) {

                return "درخواست تامین (شماره تخصیص) " . $this->allocation_id;
            }

        }


        return "";
    }

    /*
     * تعداد بسته بندی هایی که در فرم انبار وجود دارد.
     */
    public static function GetPackingFormNumber(Form $form)
    {
        $sum_packing_form_number_register =
            FormItem::join("packing_form_item", "packing_form_item.id", "packing_form_item_id")->
            join("packing_forms", "packing_forms.id", "packing_form_id")->
            where("form_item.form_id", $form->id)->
            whereNull("packing_form_master_id")->
            groupBy("packing_form_id")->count();

        return $sum_packing_form_number_register;
    }

    /**
     * @return array|void
     * فرم هایی که نیاز به نرمالایز داردند را اینجا نرمالایز می کنیم،
     * خروجی رزرنگ به این صورت است که  مقدار خالص نخ بعد از خاتمه یافته شدن تخصیص باید نرمالایز شود.
     */
    public static function NormalaizeForm()
    {
        $normaliz_forms = Form::where("status_id", 500000455)->first();

        if (!$normaliz_forms) {
            return null;
        }
        // 1 به دست آوردن تخصیص مربوط به فرم تولید تولید
        $production_form_item_ids = FormItem::join("packing_form_item", "packing_form_item.id", "packing_form_item_id")->
        where("form_id", $normaliz_forms->id)->
        pluck("production_form_item_id", "production_form_item_id")->toArray();

        $production_form_items = ProductionFormItem::whereIn("id", $production_form_item_ids)->with("allocation")->get();

        $allocation_ids = [];
        foreach ($production_form_items as $item) {
            if ($item->allocation->status_id != 5310020) {
                return [
                    "result" => false,
                    "error" => "تخصیص  " . $item->allocation->id . "هنوز خاتمه یافته نشده است."
                ];
            }
            $allocation_ids[$item->allocation_id] = $item->allocation_id;
        }
// تخصیص ها را به دست آوردیم، حالا مقدار کل تولید شده هر تخصیص را محاسبه می کنیم.

        $machine_allocations = MachineAllocation::
        whereIn("allocation_id", $allocation_ids)->
        get();

        foreach ($machine_allocations as $machine_allocation) {
            // جمع کل تولید شده
            $production_sum = ProductionFormItem::where(["allocation_id" => $machine_allocation->allocation_id, "production_id" => $machine_allocation->production_id])->sum("final_amount");

            $percent_redouce = $machine_allocation->allocation_amount / $production_sum;

            $production_form_item_ids = ProductionFormItem::where(["allocation_id" => $machine_allocation->allocation_id, "production_id" => $machine_allocation->production_id])->pluck("id", "id")->toArray();
            $list = PackingFormItem::whereIn("production_form_item_id", $production_form_item_ids)->get();

            $form_ids_where_is_ok = []; // لیست فرم هایی که باید وضعیت آنها تغییر کند.
            foreach ($list as $packing_form_item) {

                $new_amount = round($packing_form_item->final_amount * $percent_redouce, 3);

                $packing_form_item->final_amount = $new_amount;
                $packing_form_item->save();

                $form_item = FormItem::where("packing_form_item_id", $packing_form_item->id)->first();

                if (!$form_item) {
                    1 / 0;
                }
                $form_item->amount = $new_amount;
                $form_item->save();

                $form_ids_where_is_ok[$form_item->form_id] = $form_item->form_id;
            }

            $list = PackingFormItem::whereIn("production_form_item_id", $production_form_item_ids)->get();

            foreach ($list as $packing_form_item) {
                $packing_form = $packing_form_item->packing_form;

                $result = PackingForm::UpdateWeight($packing_form);
                if ($result["result"]) {
                    $packing_form->weight = $result["weight"];
                    $packing_form->save();
                }
            }

            $production_form_item_list = ProductionFormItem::where(["allocation_id" => $machine_allocation->allocation_id, "production_id" => $machine_allocation->production_id])->get();
            foreach ($production_form_item_list as $production_form_item) {
                $production_form_item->final_amount = $production_form_item->final_amount * $percent_redouce;
                $production_form_item->amount = $production_form_item->amount * $percent_redouce;
                $production_form_item->save();


                // نرمالایز کردن مقدار آیتم های لات در فرم تولید
               $sum_lot_number_amount= $production_form_item->lot_numbers()->sum("amount");
                if($sum_lot_number_amount>0){
                    foreach ($production_form_item->lot_numbers as $production_form_item_lot_number) {
                        $production_form_item_lot_number->amount=$production_form_item_lot_number->amount *  $production_form_item->amount  / $sum_lot_number_amount;
                        $production_form_item_lot_number->save();

                    }
                }
            }



            Form::whereIn("id", $form_ids_where_is_ok)->update(["status_id" => 500000410]);

            // نرمالایز کردن فرم تولید
            event(new ProductionCardLogEvent($machine_allocation->production,round($percent_redouce,4). " %", 2, 7008011));

        }


    }
}
