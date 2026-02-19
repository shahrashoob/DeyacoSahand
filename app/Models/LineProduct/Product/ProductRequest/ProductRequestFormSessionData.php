<?php

namespace App\Models\LineProduct\Product\ProductRequest;

use App\Models\Form\Packing\PackingForm;
use App\Models\User;
use App\Models\Utility\JsonDataList;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductRequestFormSessionData extends Model
{
    use HasFactory;

    protected $table = "product_request_form_packing_session_data";
    protected $fillable = ["product_request_form_id", "data", "user_id", "other_id", "message_type_id"];

    public function product_request_form()
    {
        return $this->belongsTo(ProductRequestForm::class);
    }

    public static function getData(ProductRequestForm $product_request_form, $check_selected_packing_ids = false, $with_other_requests = false)
    {

        $selected_packing_ids_for_customer = [];
        $participant_request_form_ids_all = [];
        if ($product_request_form->applicant_type_id == 30 && $with_other_requests) {
            // اگر مشتری است، کل انتخاب هایی که برای مشتری انجام شده است را بر می گردانیم برای اینکه بتواند یک خروجی بکشد.
            $session_data_list = ProductRequestFormSessionData::
            rightJoin("product_request_forms", "product_request_forms.id", "product_request_form_packing_session_data.product_request_form_id")->
            where("applicant_type_id", $product_request_form->applicant_type_id)->
            where("applicant_id", $product_request_form->applicant_id)->
            whereIn("product_request_forms.status_id", [7005001, 7005004, 7005008,7005201])->
            selectRaw("product_request_form_packing_session_data.data, product_request_forms.id as product_request_form_id")->
            get();


            foreach ($session_data_list as $session_data_item) {
                if($session_data_item->data) {
                    $data = json_decode($session_data_item->data, true);
                    if (isset($data["selected_packing_ids"])) {
                        // لیست بسته بندی هایی که انتخاب کرده است.
                        $selected_packing_ids_for_customer = array_merge($selected_packing_ids_for_customer, $data["selected_packing_ids"]);
                    }
                    $session_data = $session_data_item;
                }

                $participant_request_form_ids_all[] = $session_data_item->product_request_form_id;



            }

            $participant_request_form_ids_all = array_unique(array_values($participant_request_form_ids_all));

        } else {
            $session_data = ProductRequestFormSessionData::where("product_request_form_id", $product_request_form->id)->first();
        }


        if (isset($session_data->data)) {

            $data = json_decode($session_data->data, true);
            if (!isset($data["selected_packing_ids"])) {
                // لیست بسته بندی هایی که انتخاب کرده است.
                $data["selected_packing_ids"] = [];
            }
            if (!isset($data["count_select"])) {
                // در count_select فقط بسته بندی هایی وجود دارد که بسته بندی فرعی دارند و بخشی از بسته بندی های فرعی انتخاب شده اند.
                $data["count_select"] = [];
            }
            if (!isset($data["amount_select"])) {
                // در amount_select فقط بسته بندی هایی وجود دارد که بخشی از آنها توسط انبار جهت خروج انتخاب شده است..
                $data["amount_select"] = [];
            }
            if (!isset($data["exit_amount_of_packing_form"])) {
                // اگر درخواست کالا از انبارک به انبارک بود، باید به ازای هر بسته بندی که انتخاب می کند، مقدار خارج شده از آن را هم انتخاب کنید
                $data["exit_amount_of_packing_form"] = [];
            }
            // بسته بندی های پیشنهادی
            if (!isset($data["suggested_packing_ids"])) {
                $data["suggested_packing_ids"] = [];
            }

            if (count($selected_packing_ids_for_customer) > 0) {
                if(is_null($data["selected_packing_ids"])){
                    $data["selected_packing_ids"] = [];
                }
                $data["selected_packing_ids"] = array_merge($selected_packing_ids_for_customer, $data["selected_packing_ids"]);
                $keys = array_keys($data["selected_packing_ids"], -1);
                foreach ($keys as $key) {
                    // خود درخواست را از لیست درخواست های همراه حذف می کنیم.
                    unset($data["selected_packing_ids"][$key]);
                }
                $data["selected_packing_ids"] = array_unique(array_values($data["selected_packing_ids"]));
                if (count($data["selected_packing_ids"]) == 0) {
                    $data["selected_packing_ids"][] = -1;
                }

            }

            if (count($participant_request_form_ids_all) > 0) {
                $data["participant_request_form_ids"] = $participant_request_form_ids_all;
            }

            if ($check_selected_packing_ids) {
                // چک کردن اینکه حتما بسته بندی ها در انبار موجود باشند
                $data["selected_packing_ids"][] = -1;

                // فرعی ندارد
                // داخل انبار است
                $selected_packing_ids = PackingForm::
                whereIn("id", $data["selected_packing_ids"])->
                where("warehouse_status_id", 4201)->
                where("status_id", 7007003)->
                pluck("id")->toArray();

                $selected_packing_ids[] = -1;

                // فرعی دارد
                // چون آنهایی که فرعی دارند، فرعی ها داخل انبار هستند و وضعیت انبار اصلی خارج است.
                $selected_packing_ids_master = PackingForm::
                whereIn("id", $data["selected_packing_ids"])->
                whereIn("packing_form_master_id", $selected_packing_ids)->
                where("warehouse_status_id", 4202)->
                where("status_id", 7007003)->
                pluck("id");

                foreach ($selected_packing_ids_master as $id) {
                    $selected_packing_ids[] = $id;
                }

                $data["selected_packing_ids"] = $selected_packing_ids;

                self::setData($product_request_form, $data);
            }


            return $data;
        }

        $data["selected_packing_ids"] = []; // بسته بندی های انتخاب شده
        $data["suggested_packing_ids"] = []; // بسته بندی پیشنهادی
        $data["prf_item_value"] = [];
        $data["prf_item_ids"] = [];
        $data["count_select"] = [];
        $data["amount_select"] = [];

        if (isset($product_request_form->json_data)) {
            $json_data = json_decode($product_request_form->json_data->data, 1);
            if (isset($json_data["suggested_packing_ids"])) {
                $data["suggested_packing_ids"] = $json_data["suggested_packing_ids"];
            }
        }

        return $data;
    }

    public static function setData(ProductRequestForm $product_request_form, $data)
    {

        $session_data = ProductRequestFormSessionData::firstOrCreate(["product_request_form_id" => $product_request_form->id]);
        $session_data->data = $data;
        $session_data->save();
    }

    public static function getDataByUser($user_id, $check_selected_packing_ids = false)
    {
        $session_data = ProductRequestFormSessionData::where("user_id", $user_id)->first();
        if (isset($session_data->data)) {

            $data = json_decode($session_data->data, true);
            if (!isset($data["packing_form_read_ids"])) {
                $data["packing_form_read_ids"] = [];
            }
            return $data;
        }
        $data["packing_form_read_ids"] = []; // بسته بندی های انتخاب شده

        return $data;
    }

    public static function setDataByUser($user_id, $data)
    {
        $session_data = ProductRequestFormSessionData::firstOrCreate(["user_id" => $user_id]);
        $session_data->data = $data;
        $session_data->save();
    }

    public static function removeData(ProductRequestForm $product_request_form, $product_request_form_ids=[])
    {

        $product_request_form_ids[] = $product_request_form->id;

        ProductRequestFormSessionData::whereIn("product_request_form_id", $product_request_form_ids)->delete();

    }

    public static function removeDataByUserId($user_id)
    {
        ProductRequestFormSessionData::where(["user_id" => $user_id])->whereNull("product_request_form_id")->delete();

    }

    public static function getDataByOtherId($user_id, $other_id, $message_type_id, $default_data = [])
    {
        $session_data = ProductRequestFormSessionData::where(["user_id" => $user_id, "other_id" => $other_id, "message_type_id" => $message_type_id])->first();
        if (isset($session_data->data)) {

            $data = json_decode($session_data->data, true);
            return $data;
        }

        if ($default_data == []) {
            1 / 0;
        }
        return $default_data;
    }

    public static function setDataByOtherId($user_id, $other_id, $message_type_id, $data)
    {
        $session_data = ProductRequestFormSessionData::firstOrCreate(["user_id" => $user_id, "other_id" => $other_id, "message_type_id" => $message_type_id]);
        $session_data->data = $data;
        $session_data->save();
        return $session_data;
    }

    public static function removeDataByOtherId($user_id, $other_id, $message_type_id)
    {
        ProductRequestFormSessionData::where(["user_id" => $user_id, "other_id" => $other_id, "message_type_id" => $message_type_id])->delete();

    }
}
