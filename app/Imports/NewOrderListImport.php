<?php

namespace App\Imports;

use App\Models\LineProduct\LineProductStation;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\Option;
use App\Models\LineProduct\Product;
use App\Models\Utility\Utility;
use App\Models\Utility\Unit;
use App\Models\Utility\Status;
use App\Models\Utility\Priority;
use App\Models\Utility\Message;
use App\Models\Order\NewOrderList;
use App\Models\Order\Order;
use App\Models\Order\OrderType;
use App\Models\Customer\Customer;
use App\Models\Utility\Call;

class NewOrderListImport implements ToCollection
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {

        NewOrderList::where("id", ">", 0)->delete();
        //
        // excel col =>  product type | tonnage | price | grade | shape | size/size_arz | size_zekhamat
        $cols = [
            "product_code" => 2,
            "series" => 3,
            "order_code" => 4,
            "order_date_shamsi" => 5,
            "wherehouse_code" => 6,
            "wherehouse_id" => 6,
            "order_type" => 7,
            "prefactor_number" => 8,
            "carton" => 11,
            "number_in_carton" => 12,
            "amount" => 13,
            "unit_caption" => 14,
            "customer_code" => 18,
            "order_status" => 20,
            "description_request" => 23,
            "priority_text" => 24,
            "description_sheet" => 25,
        ];

        $i = 0;
        foreach ($rows as $row) {
            $i++;
            if ($i <= 2 ) {
                continue;
            }
            foreach ($cols as $k => $v) {
                if (!isset($row[$v])) {
                    $row[$v] = 0;
                }
            }


            $error_text = "";

            $product = Product::GetIdFromCode($row[$cols["product_code"]]);

            $error_text .= !isset($product) ? "کد محصول یافت نشد" . "<br/>" : "";

            $order_date = Utility::ShamsiToMiladi($row[$cols["order_date_shamsi"]]);
            $error_text .= $order_date == NULL ? "فرمت تاریخ سفارش گذاری نادرست است" . "<br/>" : "";

            $unit_id = Unit::GetIdFromCaption($row[$cols["unit_caption"]]);
            $error_text .= $unit_id == -100 ? "واحد نامعتبر است" . "<br/>" : "";

            $order_status_id = Status::GetIdFromCaption($row[$cols["order_status"]], 100);
            $error_text .= $order_status_id == -100 ? "وضعیت نا معتبر است" . "<br/>" : "";

            $priority_id = Priority::GetIdFromText($row[$cols["priority_text"]]);

            $customer = Customer::where("code", $row[$cols["customer_code"]])->first();

            if (!$customer) {
                $error_text .= "کد مشتری در سامانه تعریف نشده است" . "<br/>";
            }



            $order_type_id = OrderType::GetIdFromCaption($row[$cols["order_type"]]);
            $error_text .= $order_type_id == -100 ? "نوع رخداد نامعتبر است" . "<br/>" : "";

            $error_text .= $product && $product->hasBOM() ? "" : " BOM محصول تعریف نشده است" . "<br/>";



            $call = Call::where("status_id", 3300)->first();

            $NOL = new NewOrderList();
            $NOL->order_code = $row[$cols["order_code"]];
            $NOL->series = $row[$cols["series"]];
            $NOL->order_id = -100;
            $NOL->product_code = $row[$cols["product_code"]];
            $NOL->product_id = $product->id ?? -100;
            $NOL->customer_id = $customer->id ?? 0;
            $NOL->customer_code = $row[$cols["customer_code"]];
            $NOL->carton = $row[$cols["carton"]] == "" ? 0 : $row[$cols["carton"]];
            $NOL->number_in_carton = $row[$cols["number_in_carton"]] == "" ? 0 : $row[$cols["number_in_carton"]];
            $NOL->amount = $row[$cols["amount"]];
            $NOL->priority_text = $row[$cols["priority_text"]];
            $NOL->priority_id = $priority_id;
            $NOL->order_date_shamsi = $row[$cols["order_date_shamsi"]];
            $NOL->order_datetime = $order_date;
            $NOL->order_status = $row[$cols["order_status"]];
            $NOL->order_status_id = $order_status_id;
            $NOL->wherehouse_code = $row[$cols["wherehouse_code"]];
            $NOL->wherehouse_id = $row[$cols["wherehouse_id"]];
            $NOL->unit_caption = $row[$cols["unit_caption"]];
            $NOL->unit_id = $unit_id;
            $NOL->order_type = $row[$cols["order_type"]];
            $NOL->order_type_id = $order_type_id;
            $NOL->order_date_shamsi = $row[$cols["order_date_shamsi"]];
            $NOL->prefactor_number = $row[$cols["prefactor_number"]];
            $NOL->call_id = $call->id;

            $NOL->erp_status_id = ($NOL->order_status_id == 100) ? 305 : 310;
            $NOL->save();


            // Validation
            if ($product != NULL) {

                $error_text .= $product->number_in_carton != $NOL->number_in_carton ?
                    "تعداد در کارتن با ویژگی محصول سازگار نیست " .
                    "تعداد در کارتن لیست محصولات:" . $product->number_in_carton . ", تعداد در کارتن در درخواست ها:" . $NOL->number_in_carton . "<br/>"
                    : "";

                $error_text .= $product->number_in_carton != 0 && $product->number_in_carton * $NOL->carton != $NOL->amount ?
                    "مقدار به درستی محاسبه نشده است" . "<br/>" : "";


                    $error_text .= $product->hasLineProduct() ? "" : " برای محصول خط تولید تعریف نشده است ";

                $error_text .= $product->ValidSelfBOM() ? "" : "با توجه به BOM تعریف شده، نوع تامین محصول نا معتبر است ";

            }


            $error_text .= $NOL->carton < 1 ? "حداقل تعداد کارتن باید 1 باشد" : "";

            $d_sheet = $row[$cols["description_sheet"]];
            if ($d_sheet != "") {
                $msg = new Message();
                $msg->text = $d_sheet;
                $msg->other_id = $NOL->id;
                $msg->message_type_id = 110;
                $msg->save();
                $NOL->description_sheet_id = $msg->id;
            }


            $d_request = $row[$cols["description_request"]];
            if ($d_request != "") {
                $msg = new Message();
                $msg->text = $d_request;
                $msg->other_id = $NOL->id;
                $msg->message_type_id = 100;
                $msg->save();
                $NOL->description_request_id = $msg->id;

            }


            // Validation

            $NOL->error = $error_text;


            $NOL->save();
        }
    }
}
