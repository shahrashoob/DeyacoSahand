<?php

namespace App\Models\Report\RealTime;

use App\Models\Form\Form;
use App\Models\Form\FormItem;
use App\Models\Form\FormLog;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormForm;
use App\Models\Order\Order;
use App\Models\Warehouse\WarehouseProduct;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RealTimeOrder extends Model
{
    protected $table = "real_time_orders";
    protected $fillable = ["current_date", "date_number", "weight", "total_price", "form_id", "customer_id"];
    use HasFactory;

    public static $list_setting = [
        "allow_show_bar_weigh_in_real_time_dashboard" => 1,
        "allow_show_bar_price_in_real_time_dashboard" => 1,

        "allow_show_line_weight_in_real_time_dashboard" => 1,
        "allow_show_line_price_in_real_time_dashboard" => 1,

        "real_time_top_order_delay_number" => 0,
        "real_time_top_customer_number" => 0,
        "real_time_top_customer_days" => 0,
        "real_time_top_customer_show" => 1,
        "real_time_top_order_delay_show" => 1,
    ];

    /***
     * @param $form_log_id
     * @return integer
     * محاسبه مقدار جدول روند فروش وزنی و ریالی
     */
    public static function ComputeRealTimeOrder($form_log_id)
    {
        $list = FormLog::where("id", ">=", $form_log_id)->
        limit("50")->
        get();

        foreach ($list as $form_log) {
            $form_log_id=$form_log->id;
            $form = $form_log->form;
            if(!$form){
                continue;
            }
            $form_id = $form->id;
            $wp = WarehouseProduct::where("form_id", $form_id)->first();
            if (!$wp) {
                continue;
            }
            $order_ids = ProductRequestFormForm::
            join("product_request_forms", "product_request_forms.id", "product_request_form_id")->
            where("product_request_form_form.form_id", $form_id)->
            whereNotNull("order_id")->
            pluck("order_id")->toArray();

            if (count($order_ids) > 0) {
                $customer_id = Order::whereIn("id", $order_ids)->first()->customer_id;
                $data = FormItem::
                join("forms", "forms.id", "form_id")->
                join('order_factor', function ($join) {
                    $join->on("order_factor.product_id", "=", "form_item.product_id");
                })
                    ->join('products', 'products.id', '=', 'order_factor.product_id')
                    ->whereIn("order_id", $order_ids)
                    ->where("form_id", $form_id)->
                    groupBy("form_item.id")
                    ->selectRaw("sum( form_item.amount)/count(form_item.id)  as sum_weight,products.id , sum( form_item.amount)/count(form_item.id)* fea as sum_price,forms.updated_at")
                    ->get();


                $date_caption = jdate(Carbon::parse($wp->created_at)->timestamp)->format('Ym');
                $real_time_order = RealTimeOrder::firstOrCreate(["form_id" => $form_id], ["date_number" => $date_caption, "current_date" => $wp->created_at, "weight" => 0, "total_price" => 0, "customer_id" => $customer_id]);

                $sum_price = 0;
                $sum_weight = 0;
                foreach ($data as $item) {

                    $sum_weight += $item->sum_weight;
                    $sum_price += $item->sum_price;

                }
                $real_time_order->weight = $sum_weight;
                $real_time_order->total_price = $sum_price;
                $real_time_order->save();
            }


        }

        return $form_log_id;
    }
}
