<?php

namespace App\Models\LineProduct\Product\ProductCreation;

use App\Models\File\File;
use App\Models\LineProduct\GoodsKind;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\ConsumedProduct\ConsumedProduct;
use App\Models\LineProduct\Product\ProductServiceType;
use App\Models\Post\PostUser;
use App\Models\Production\Production;
use App\Models\Utility\Setting;
use App\Models\Utility\Status;
use App\Models\Worker;
use App\Notifications\SMSNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\LineProductStation\Product\ProductCreation;
use Illuminate\Support\Facades\Notification;

class ProductCreationProcess extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id",
        "status_id",
        "method_of_sending_product_id",
        "caption",
        "has_physical_sample",
        "goods_kind_id",
        "product_service_type_id",
        "sample_production_id",
        'has_sampling_required',
        'product_id'
    ];


    public function worker()
    {
        return $this->belongsTo(Worker::class, "user_id", "id");
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function product_service_type()
    {
        return $this->belongsTo(ProductServiceType::class);
    }

    public function goods_kind()
    {
        return $this->belongsTo(GoodsKind::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function sample_production()
    {
        return $this->belongsTo(Production::class, "sample_production_id");
    }

    public function image()
    {
        return $this->belongsTo(File::class, "file_id");
    }

    public function getCode()
    {
        if ($this->code != null) {
            return $this->code;
        }
        $this->code = "DCPD/" . ($this->id + 1000); // DC Product Design
        $this->save();

        return $this->code;
    }

    public static function Exists($value, $id = false, $col = 'code')
    {

        if ($id) {
            return ProductCreationProcess::where($col, $value)->where("id", "!=", $id)->exists();
        }

        return ProductCreationProcess::where($col, $value)->exists();
    }

    public static function GetNextStatus($button_id, ProductCreationProcess $productCreationProcess, $allow_active_product = true, $kk = 0)
    {
//echo $kk.":$button_id<br/>";

        if ($kk > 50) {
            return [
                "result" => false,
                "error" => "وضعیت بعدی طراحی کالا (" . ($priority->button_id ?? "") . ") مشخص نشده است، لطفا با پشتیبانی تماس بگیرید." . "<br/> کد خطا: 1005"
            ];
        }
        if ($productCreationProcess->product_service_type_id == 2) // اگر خدمت است به صورت ثابت وضعیت ها عوض می شوند
            switch ($productCreationProcess->status_id) {
                case 5231301:
                    return [
                        "result" => true,
                        "status_id" => 5231302
                    ];
                    break;
                case 5231302:
                    return [
                        "result" => true,
                        "status_id" => 5231303
                    ];
                    break;
                case 5231303:
                    // فعال کردن خدمت
                    if ($allow_active_product) {
                        self::EndOfCreationProcess($productCreationProcess, true);
                    }
                    return [
                        "result" => true,
                        "status_id" => 5231201
                    ];
                    break;
                default:
                    return [
                        "result" => false,
                        "error" => "وضعیت بعدی طراحی کالا مشخص نشده است، لطفا با پشتیبانی تماس بگیرید."
                    ];
            }
        $k = 0;

        // گرفتن لیست گام هایی که نیاز ندارند
        $break_list = [];
        $break_list_log = ProductCreationProcessLog::where(["product_creation_process_id" => $productCreationProcess->id, "event_id" => 5231610])->first();
        if ($break_list_log) {
            $break_list = json_decode($break_list_log->message->text);
        }

        while ($k < 1000) {

            $k++;
            $priority = Product\ProductCreation\ProductCreationProcessPriority::
            where("button_id", $button_id)->
            where("goods_kind_id", $productCreationProcess->goods_kind_id)->
            first();

            if (!$priority || !$priority->next_status_id) {
                return [
                    "result" => false,
                    "error" => "وضعیت بعدی طراحی کالا (" . ($priority->button_id ?? "") . ") مشخص نشده است، لطفا با پشتیبانی تماس بگیرید."
                ];
            }


            // در صورتی که کالا نیاز به نمونه گیری ندارد، باید از گام های نمونه گیری عبور کند.
            $sampling_status = [5231020, 5231021, 5231022, 5231023, 5231024, 5231025,];

// از گام های زیر باید عبور کند و نیاز به تکمیل اطلاعات نیست.
            if (in_array($priority->next_status_id, $break_list)) {

                $controller_info = ProductCreation\DashboardController::get_controller_info_for_next();
                foreach ($controller_info as $name => $info)
                    // اگر ماژول در چند وضعیت فعال است، فقط اولین وضعیت ملاک است و وضعیت های دیگر مهم نیست.
                    if (\Illuminate\Support\Str::substr($priority->next_status_id, -3) == $info["enable_status"][0]
                    ) {
                        $button_id = $info["button_id"];
                        return self:: GetNextStatus($button_id, $productCreationProcess, $allow_active_product, ++$kk);
                    }
            } else if (in_array($priority->next_status_id, $sampling_status) && !$productCreationProcess->has_sampling_required) {
                $controller_info = ProductCreation\DashboardController::get_controller_info_for_next();
                foreach ($controller_info as $name => $info)

                    // اگر ماژول در چند وضعیت فعال است، فقط اولین وضعیت ملاک است و وضعیت های دیگر مهم نیست.
                    if (\Illuminate\Support\Str::substr($priority->next_status_id, -3) == $info["enable_status"][0]
                    ) {

                        $button_id = $info["button_id"];

                        break;
                    }

            } else {
                // در صورتی که کالا قابلیت فروش ندارد، باید از گام مربوط به فروش  عبور کند.
                $sale_status = [5231028, 5231029];
                if (
                    isset($productCreationProcess->product) &&
                    !$productCreationProcess->product->possibility_of_sale && // قابلیت فروش ندارد
                    in_array($priority->next_status_id, $sale_status)) {

                    $controller_info = ProductCreation\DashboardController::get_controller_info_for_next();
                    foreach ($controller_info as $name => $info)
                        // اگر ماژول در چند وضعیت فعال است، فقط اولین وضعیت ملاک است و وضعیت های دیگر مهم نیست.
                        if (\Illuminate\Support\Str::substr($priority->next_status_id, -3) == $info["enable_status"][0]
                        ) {
                            $button_id = $info["button_id"];
                            break;
                        }

                } else {
                    break;
                }
            }


        }
        if ($k > 1000) {
            return [
                "result" => false,
                "error" => "وضعیت بعدی طراحی کالا مشخص نشده است، لطفا با پشتیبانی تماس بگیرید (کد 1001)."
            ];
        }

        // فعال کردن کالا و تغییر کالاهای مصرفی در انتظار طراحی
        if ($allow_active_product && $priority->next_status_id == 5231201) {

            self::EndOfCreationProcess($productCreationProcess, true);
        }

        if ($allow_active_product) {
            self::SmsProductCreationPost($productCreationProcess, $priority->next_status_id);
        }
        return [
            "result" => true,
            "status_id" => $priority->next_status_id
        ];
    }

    public static function GetNextStatusQuick($button_id, ProductCreationProcess $productCreationProcess)
    {

        // کالای مصرفی 038
        // مشخصات کالا 044
        // تصویر 045

        $break_step_list_all = [
            "consumed_break" => 5231038,
            "property_break" => 5231044,
            "image_break" => 5231045,
        ];

        // گرفتن لیست گام هایی که نیاز ندارند
        $break_list = [];
        $break_list_log = ProductCreationProcessLog::where(["product_creation_process_id" => $productCreationProcess->id, "event_id" => 5231610])->first();
        if ($break_list_log) {
            $break_list = json_decode($break_list_log->message->text);
        }

        switch ($button_id) {
            case 0:
                if (in_array(5231038, $break_list)) {
                    if (in_array(5231044, $break_list)) {

                        if (in_array(5231045, $break_list)) {
                            return [
                                "result" => true,
                                "status_id" => 5231020, // در حال تولید نمونه آزمایشگاهی
                                "message" => "صدور کارت تولید و تخصیص به ماشین"
                            ];
                        } else {
                            return [
                                "result" => true,
                                "status_id" => 5231503
                            ];
                        }
                    } else {

                        return [
                            "result" => true,
                            "status_id" => 5231502
                        ];
                    }
                } else {
                    return [
                        "result" => true,
                        "status_id" => 5231501
                    ];
                }
                break;
            case 5231038: // اطلاعات کالای مصرفی
                if (in_array(5231044, $break_list)) {

                    if (in_array(5231045, $break_list)) {
                        return [
                            "result" => true,
                            "status_id" => 5231020 // در حال تولید نمونه آزمایشگاهی
                        ];
                    } else {
                        return [
                            "result" => true,
                            "status_id" => 5231503
                        ];
                    }
                } else {

                    return [
                        "result" => true,
                        "status_id" => 5231502
                    ];
                }
                break;
            case 5231044: // مشخصات کالا
                if (in_array(5231045, $break_list)) {
                    return [
                        "result" => true,
                        "status_id" => 5231020 // در حال تولید نمونه آزمایشگاهی
                    ];
                } else {
                    return [
                        "result" => true,
                        "status_id" => 5231503
                    ];
                }
                break;
            case 5231045:
                return [
                    "result" => true,
                    "status_id" => 5231020 // در حال تولید نمونه آزمایشگاهی
                ];
                break;


        }
        return [
            "result" => false,
            "error" => "وضعیت بعدی درخواست طراحی سریع کالا قابل محاسبه نمی باشد، لطفا با واحد پشتیبانی تماس بگیرید."
        ];
    }

    public static function EndOfCreationProcess(ProductCreationProcess $productCreationProcess, $check_active_status)
    {

        if ($check_active_status) {
            Product\Version\ProductVersion::GetVersion($productCreationProcess->product,false);
            if ($productCreationProcess->product->active_status_id == 1200) {
                // قبلا فعال شده است.
                return true;
            }
            // طراحی تکمیل شده است.
            $productCreationProcess->product->active_status_id = 1200;
            $productCreationProcess->product->save();
        }

        // لیست کالاهای در انتظار طراحی.
        $consumed_product_list = ConsumedProduct::
        where("product_creation_process_id", $productCreationProcess->id)->
        where("status_id", 3400002)-> // در حال طراحی کالای مصرفی
        get();
        foreach ($consumed_product_list as $item) {

            $item->status_id = 3400001; // طراحی انجام شده است.
            $item->material_id = $item->product_creation_process->product_id;
            $item->save();

        }


    }

    public static function SmsProductCreationPost($productCreationProcess, $next_status_id)
    {
        $product_creation_priority = Product\ProductCreation\ProductCreationProcessPriority::
        where("goods_kind_id", $productCreationProcess->goods_kind_id)->
        where("next_status_id", $next_status_id)->
        whereNotNull('post_id')->
        first();
        if (!$product_creation_priority) {
            return true;
        }
        $post_users = PostUser::where('post_id', $product_creation_priority->post_id)->get();

        $token = "_APP_NAME_";
        $token2 = $productCreationProcess->code;
        $token3 = Setting::getStringValue('software_name');
        $token20 = Status::find($next_status_id)->caption ?? "";
        foreach ($post_users as $item) {
            $token10 = $item->worker->fullname("with_gender_2");
            Notification::send("00" . ($item->worker->mobile_country->area_code ?? "98") . $item->worker->mobile,
                new SMSNotification("productproccesstemplate1",
                    $token,
                    $token2,
                    $token3,
                    $token10,
                    $token20));
        }

    }
}
