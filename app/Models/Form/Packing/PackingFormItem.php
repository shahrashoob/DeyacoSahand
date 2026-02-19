<?php

namespace App\Models\Form\Packing;

use App\Models\Accounting\Tariff\ProductTariffLog;
use App\Models\GoodsKindProcess\Fabric_Raw\FabricRawGrading;
use App\Models\LineProduct\Degree;
use App\Models\LineProduct\LotNumber;
use App\Models\LineProduct\Product;
use App\Models\Order\Order;
use App\Models\Production\ProductionFormItem;
use App\Notifications\SMSNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class   PackingFormItem extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "packing_form_item";
    protected $fillable = [
        "packing_form_id",
        "production_form_item_id",
        "production_form_item_lot_number_id",
        "product_id",
        "lot_number_id",
        "degree_id",
        "amount",
        "amount_after_control",
        "final_amount",
        "sub_amount",
        "init_sub_amount",
        "sub_amount2",
        "status_id",
        "band_code",
        "version_code"
    ];

    public function production_form_item()
    {
        return $this->belongsTo(ProductionFormItem::class);
    }

    public function packing_form()
    {
        return $this->belongsTo(PackingForm::class);
    }

    public function fabric_raw_grading()
    {
        return $this->belongsTo(FabricRawGrading::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function lot_number()
    {
        return $this->belongsTo(LotNumber::class);
    }

    public function degree()
    {
        return $this->belongsTo(Degree::class);
    }

    public function getCode($band = null, $section_degree_number = 1, $show_packing_form_code = true)
    {

        if ($this->code == "") {

            $item_before = PackingFormItem::where("packing_form_id", $this->packing_form_id)->where("id", "<", $this->id)->count();
            $section_degree_number = $item_before + 1;
            $this->code = $this->packing_form->getCode() . "/" . $this->band_code . "/" . $section_degree_number;
            $this->save();
        }

        if ($show_packing_form_code) {
            return $this->code;
        } else {
            return str_replace($this->packing_form->getCode(), "EPK/***", $this->code);
        }
    }

    public static function final_shrinkage_percent(PackingFormItem $packing_form_item, $final_amount = null)
    {
        // درصد جمع شدگی نهایی: 1-متراژ نهایی خودش / متراژ سیستم خودش *100
        $self_final_amount = $final_amount ? $final_amount : $packing_form_item->final_amount;
        $self_amount = $packing_form_item->amount;
        if ($self_amount == 0) {
            return "-99999999";
        }

        return round((1 - $self_final_amount / $self_amount) * 100, 2);
    }

    /*
     * گرفتن نام و کد کالای مشتری از تعرفه با توجه به شماره سفارش
     */
    public function getCustomerCodeFromTariff($type = "code", $order_id = null)
    {

        //ابتدا بررسی می کنیم که کد کالا در تغییر بسته بندی سریع تغییر نکرده باشد.
        $product_request_form_code = PackingFormLog::where([
            "packing_form_id" => $this->packing_form_id,
            "event_id" => 7007039 // ثبت شماره درخواست کالا از انبار
        ])->orderBy("id", "desc")->first()->message->text??null;
        if ($order_id) {
            $order = Order::where("id", $order_id)->first();
        } elseif ($product_request_form_code) {
            $product_request_form = Product\ProductRequest\ProductRequestForm::where("code", $product_request_form_code)->first();

            $order = $product_request_form->order;
// اگر شماره سفارش را لاگ کردیم که حتما وجود دارد، اگر وجود نداشت خطا دارد.
            if (!$order) {
                1 / 0;
            }
        } else {

// اگر در لاگ وجود نداشت در کارت تولید نگاه می کنیم و اگر هیچی کدام نبود یعنی کد کالا ندارد.
            $order = $this->production_form_item->production->order ?? null;
            if (!$order) {
                return "";
            }
        }

        $product_tariff = ProductTariffLog::where([
            "tariff_log_id" => $order->tariff_log_id,
            "product_id" => $this->product_id,
            "packing_type_id"=>$this->packing_form->packing_type_id,
        ])->first();
        if ($product_tariff) {
            switch ($type) {
                case "code":
                    return $product_tariff->customer_product_code;
                case "caption":
                    return $product_tariff->customer_product_caption;
                case "code_caption":
                    return [
                        "code" => $product_tariff->customer_product_code,
                        "caption" => $product_tariff->customer_product_caption,
                    ];
            }

            return $product_tariff->customer_product_code . " - " . $product_tariff->customer_product_caption;
        }

        return " ";
    }

    /*
     * گرفتن ردیف در آیتم فرم تولید
     * یعنی بسته بندی چندمین بسته تولید شده می باشد.
     */
    public function GetRowInProductionFormItem()
    {
        $production_form_item=$this->production_form_item;
        if(!$production_form_item){
            return "";
        }
//        $production_form=$production_form_item->production_form;
//        if(!$production_form){
//            return "";
//        }
     //   $production_form_ids=$production_form->items()->pluck("production_form_item.id");
        $production_form_ids=ProductionFormItem::where("allocation_id",$production_form_item->allocation_id)->pluck("production_form_item.id");
        $production_form_ids[]=-1;
        return PackingFormItem::
        whereIn("production_form_item_id", $production_form_ids)->
        whereNotNull("production_form_item_id")->
        where("packing_form_id", "<=", $this->packing_form_id)->
        count();
    }

}
