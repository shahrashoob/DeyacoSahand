<?php

namespace App\Imports;

use App\Models\Accounting\Tariff\Tariff;
use App\Models\Customer\NewCustomer;
use App\Models\Order\OrderType;
use App\Models\Order\Permision\OrderPermissionCustomer;
use App\Models\User;
use App\Models\Utility\Address\Province;
use App\Models\Utility\Utility;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\Customer\ChannelType;
use Haruncpi\LaravelUserActivity\Traits\Loggable;


class CustomerImport implements ToCollection
{
    use Loggable;

    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {
        NewCustomer::where("id", ">", 0)->delete();
        $cols = [
            "code" => 0,
            "firstname" => 1,
            "lastname" => 2,
            "caption" => 3,
            "birth_date_shamsi" => 4,
            "detailed_code" => 5,
            "channel_caption" => 6,
            "sub_channel_caption" => 7,
            "gender_id" => 8,
            "customer_type_id" => 9,
            "tariff_id" => 10,
            "cash_off_percent" => 11,
            "bail_amount" => 12,
            "economic_number" => 13,
            "national_code" => 14,
            "national_id" => 14,
            "register_code" => 15,
            "province_id" => 16,
            "order_type_id" => 17,


            "order_permission_type_1" => 18,
            "order_permission_type_4" => 19,
            "order_permission_type_2" => 20,
            "order_permission_type_3" => 21,
            "order_permission_type_5" => 22,
            "order_permission_type_6" => 23,
            "order_permission_type_7" => 24,
        ];

        $i = 0;
        foreach ($rows as $row) {
            $i++;
            if ($i <= 1) {
                continue;
            }
            foreach ($cols as $k => $v) {
                if (!isset($row[$v])) {
                    $row[$v] = 0;
                }
            }


            $error_text = "";
            $channel_id = ChannelType::GetIdFromCaption($row[$cols["channel_caption"]]);

            $customer = NewCustomer::firstOrCreate(
                [
                    "code" => $row[$cols["code"]]
                ]
            );

            $error_text .= $channel_id == -100 ? " نوع کانال به درستی اضافه نشده است" : "";
            $customer->caption = $row[$cols["caption"]];
            $customer->firstname = $row[$cols["firstname"]];
            $customer->lastname = $row[$cols["lastname"]];
            $customer->channel_id = $channel_id;
            $customer->sub_channel_caption = $row[$cols["sub_channel_caption"]];
            $customer->detailed_code = $row[$cols["detailed_code"]];
            $customer->gender_id = $row[$cols["gender_id"]];
            $customer->customer_type_id = $row[$cols["customer_type_id"]];
            $customer->tariff_id = $row[$cols["tariff_id"]];
            $customer->cash_off_percent = $row[$cols["cash_off_percent"]];
            $customer->bail_amount = $row[$cols["bail_amount"]];
            $customer->economic_number = $row[$cols["economic_number"]];
            $customer->national_code = $row[$cols["national_code"]];
            $customer->national_id = $row[$cols["national_id"]];
            $customer->register_code = $row[$cols["register_code"]];

            $birth_date = Utility::ShamsiToMiladi($row[$cols["birth_date_shamsi"]]);
            $error_text .= $birth_date == NULL && $row[$cols["birth_date_shamsi"]]!=0 ? "فرمت تاریخ  تولد نادرست است" . "<br/>" : "";
            $customer->birth_date = $birth_date;

            $error_text .=$customer->gender_id >=1 && $customer->gender_id <=3 ?"":" جنسیت به درستی انتخاب نشده است"."<br/>";
            $error_text .=$customer->customer_type_id==1 || $customer->customer_type_id == 2 ?"":" نوع مشتری به درستی انتخاب نشده است"."<br/>";

            $tariff=Tariff::find($row[$cols["tariff_id"]]);
            if(!$tariff && $row[$cols["tariff_id"]]!=0 ){
                $error_text .=" کد تعرفه  به درستی انتخاب نشده است"."<br/>";
            }

            if(!isset($row[$cols["national_code"]]) || $row[$cols["national_code"]]== 0){
                $error_text .="کد ملی الزامی است، در صورتی که مشتری حقوقی است، کد ملی را برابر شناسه ملی قرار دهید"."<br/>";
            }


            $province = Province::find($row[$cols["province_id"]]);
            if ($province) {
                $customer->province_id = $row[$cols["province_id"]];
            } else {
                $customer->province_id = 9999;
                $error_text.=" ;کد استان نا معتبر است"."<br/>";
            }

            $order_type = OrderType::find($row[$cols["order_type_id"]]);
            if ($order_type) {
                $customer->order_type_id = $row[$cols["order_type_id"]];
            } else {
                $customer->order_type_id = 9999;
                $error_text.=" نوع فروش نامعتبر است"."<br/>";
            }

            $customer->error = $error_text;

            for ($i = 1; $i <= 7; $i++) {
                if ($row[$cols["order_permission_type_" . $i]] == 1) {
                    $c = "order_permission_type_" . $i;
                    $customer->$c = $row[$cols["order_permission_type_" . $i]];
                }
            }
            $customer->save();

        }


    }
}
