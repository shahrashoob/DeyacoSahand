<?php

namespace App\Http\Controllers\LineProductStation\Product\ProductCreation;

use App\Events\Product\ProductCreationProcessLogEvent;
use App\Http\Controllers\Controller;
use App\Models\File\File;
use App\Models\LineProduct\GoodsKind;
use App\Models\LineProduct\Packing\PackingTypeLabelPrintingType;

use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\ProductCreation\MethodOfSendingProduct;
use App\Models\LineProduct\Product\ProductCreation\ProductCreationProcess;
use App\Models\Utility\Message;
use App\Models\Utility\Option;
use App\Models\Utility\Setting;
use App\Models\Utility\Status;
use App\Models\Worker;
use App\Notifications\SMSNotification;
use Database\Seeders\LineProductStation\ProductCreationProcessSeeder;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Stream;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class NewFormController extends Controller
{
    //
    public static $info = [
        "route" => "line_product_station.product.product_creation.new_form.",
        "view" => "line_product_station.product.product_creation.new_form.",
        "enable_status" => [""],
        "button" => ["caption" => "ثبت درخواست طراحی", "class" => "btn-primary"],
        "button_id" => 5231004
    ];
    var $view_path;
    var $route_path;
    public $dashboard_path = "line_product_station.product.product_creation.dashboard.";

    public function __construct()
    {
        $this->route_path = NewFormController::$info["route"];
        $this->view_path = NewFormController::$info["route"];
    }

    public function index($product_creation_process_id = false)
    {

        if ($this->checkPermission() != "") {
            return $this->checkPermission();
        }
        $method_of_sending_product_list = MethodOfSendingProduct::get();
        $product_service_type_option = Option::get("product_service_type", 1);
        $goods_kind_option = Option::get("goods_kind");
        $good_kinds = GoodsKind::all();
        $good_kind_with_sampling = [];

        foreach ($good_kinds as $good_kind) {
            $good_kind_with_sampling[$good_kind->id] = $good_kind->has_sampling_required_in_product_creation;
        }


        return view($this->view_path . "index", compact("method_of_sending_product_list", "good_kind_with_sampling", "goods_kind_option", "product_service_type_option", "product_creation_process_id"));
    }

    public function submit(Request $request)
    {

        if ($this->checkPermission() != "") {
            return $this->checkPermission();
        }
        $validator = $request->validate([
            'image_file' => 'max:' . (1024 * 10),
        ]);
//        if (!isset($request->image_file)) {
//            return back()->withErrors("لطفا تصویر کالا را بارگذاری نمایید.");
//        }

        if ($request->caption == "" || ProductCreationProcess::Exists($request->caption, false, "caption")) {
            return back()->withErrors("نام پیشنهادی تکراری/ نامعتبر است");
        }
        $validator = $request->validate([
            'image_file' => 'max:' . (1024 * 10),
        ]);
        $substr = substr_count($request->caption, ' ');
        if ($substr > 8) {
            return back()->withErrors("نام کالا حداکثر می تواند دارای 8 کاراکتر  Space باشد");
        }


        if ($request->product_service_type_id == 2) {
            return self::CreateServiceInProductCreation($request, $this->dashboard_path);
        } else {
            return self::CreateProductInProductCreation($request, $this->route_path, $this->dashboard_path);
        }
    }

    public static function PostSubmitApi(Request $request)
    {
        $software_name = Setting::getStringValue("software_name");
        if (!$request->user() || !$request->user()->currentAccessToken()) {
            return response()->json([
                'result' => false,
                'massage_type' => "error",
                'message' => "لطفا قبل از ثبت درخواست وارد $software_name  شوید (توکن اعتبار سنجی نامعتبر است.) "
            ]);
        }
//        if (!$request->hasFile('image_file')) {
//            return response()->json([
//                'result' => false,
//                'massage_type' => "error",
//                'message' => 'تصویری برای بارگزاری ارسال نشده است.'
//            ]);
//        }


        if ($request->caption == "" || ProductCreationProcess::Exists($request->caption, false, "caption")) {

            return response()->json([
                'result' => false,
                'massage_type' => "error",
                'message' => "نام پیشنهادی برای ثبت در $software_name   تکراری/ نامعتبر است"
            ]);
        }
        $substr = substr_count($request->caption, ' ');
        if ($substr > 8) {
            return back()->withErrors(
                "نام پیشنهادی برای ثبت $software_name   " .
                " حداکثر می تواند دارای 8 کاراکتر  Space باشد");
        }

        $product_creation_process = ProductCreationProcess::create([
            "caption" => Message::convert_farsi_digits_to_english($request->caption),
            "user_id" => Auth::id(),
            "goods_kind_id" => $request->goods_kind_id,
            "method_of_sending_product_id" => $request->method_of_sending_product_id,
            "product_service_type_id" => $request->product_service_type_id,
            "has_physical_sample" => $request->has_physical_sample,
            "status_id" => $request->has_physical_sample ? 5231001 : 5231002,
        ]);

        if ($request->hasFile('image_file')) {
            $file = File::uploadFile($request->file('image_file'), $product_creation_process->id . "_" . rand(1000, 9000) . ".png", 42, "upload/product_creation/", true);

            $product_creation_process->file_id = $file->id;
            $product_creation_process->save();
        }

        $worker = Worker::find(Auth::id());
        $token = $product_creation_process->getCode();
        $token2 = null;
        $token3 = null;
        $token10 = $worker->fullname();
        Notification::send("00" . ($worker->mobile_country->area_code ?? "98") . $worker->mobile,
            new SMSNotification("productcreationprocessnew", $token, $token2, $token3, $token10));


        return response()->json([
            'result' => true,
            'massage_type' => "success",
            "code" => $product_creation_process->getCode(),
            'message' => 'یک درخواست طراحی کالا با کد ' . $product_creation_process->getCode() . " در " . $software_name . " ایجاد گردید."
        ]);
    }

    public function posttex_address(ProductCreationProcess $product_creation_process)
    {
        return redirect()->route("dashboard")->with(["success" => "درخواست طراحی شما ثبت گردید، لطفا نمونه کالا را به آدرس شرکت ارسال فرمایید. "]);
    }

    public function create_from_exist_product()
    {
        $list = Product::leftJoin("product_creation_processes", "product_id", "products.id")->
        whereNull("product_id")->
        select("products.*")->
        get();
        $product_option = Option::get("product_from_list", 0, 0, $list);

        $status_ids = Status::
        where("status_type_id", 5231)->
        whereNotIn("id", [5231201, 5231001, 5231004])->
        pluck("id")->
        toArray();

        $status_option = Option::get("status_permission", 0, 5231, $status_ids);

        return view($this->view_path . "create_from_exist_product", compact("product_option", "status_option"));
    }

    public function submit_from_exist_product(Request $request)
    {

        $product_process_creation = ProductCreationProcess::where("product_id", $request->product_id)->first();
        if ($product_process_creation) {
            return back()->withErrors("با توجه به اینکه درخواست طراحی کالا " . $product_process_creation->code . " برای کالای انتخاب شده وجود دارد، امکان ثبت مجدد درخواست وجود ندارد.");
        }

        $product = Product::find($request->product_id);
        if (!$product) {
            return back()->withErrors("لطفا یک کالا انتخاب نمایید.");
        }
        if ($product->product_service_type_id == 2) {
            return back()->withErrors("امکان ثبت درخواست طراحی برای خدمات وجود ندارد.");
        }

        //چک کردن رسته کالایی مجاز برای درخواست طراحی
         $product_craetion_process_priority = Product\ProductCreation\ProductCreationProcessPriority::
        where("goods_kind_id", $product->goods_kind_id)->
        where("next_status_id", $request->status_id)->
        first();
        if (!$product_craetion_process_priority) {
            $status=Status::find($request->status_id);
            $goods_kind=GoodsKind::find($product->goods_kind_id);
            return back()->withErrors("وضعیت " . $status->caption . " برای رسته کالایی ".$goods_kind->caption." مجاز نمی باشد.");
        }
        $product_process_creation = ProductCreationProcess::create([
            "user_id" => Auth::id(),
            "status_id" => $request->status_id,
            "method_of_sending_product_id" => 1,
            "caption" => Message::convert_farsi_digits_to_english($product->caption),
            "product_id" => $product->id,
            "has_physical_sample" => 0,
            "goods_kind_id" => $product->goods_kind_id,
            "product_service_type_id" => $product->product_service_type_id,
            "sample_production_id" => null,
            'has_sampling_required' => 0,
        ]);
        $product_process_creation->getCode();
        event(new ProductCreationProcessLogEvent($product_process_creation, 5231601));
        return redirect()->route($this->dashboard_path . "index")->with(["success" => "یک درخواست طراحی با کد " . $product_process_creation->code . " برای کالا ایجاد گردید."]);
    }

    public function checkPermission()
    {
        $post_user = Auth::user()->posts->first();
        if (!$post_user->checkButtonPermission($this->route_path . "index")) {
            return redirect()->route($this->dashboard_path . "index")->withErrors("شما مجوز دسترسی به این ماژول را ندارید.");
        }


    }

    public static function CreateProductInProductCreation($request, $route_path, $dashboard_path)
    {
        $product_creation_process = ProductCreationProcess::create([
            "caption" => Message::convert_farsi_digits_to_english($request->caption),
            "user_id" => Auth::id(),
            "goods_kind_id" => $request->goods_kind_id,
            "method_of_sending_product_id" => $request->method_of_sending_product_id,
            "product_service_type_id" => $request->product_service_type_id,
            "has_physical_sample" => $request->has_physical_sample,
            "status_id" => $request->has_physical_sample ?
                5231001 : // در انتظار ارسال نمونه پارچه
                5231002 // در انتظار تکمیل اطلاعات پایه
        ]);

        if ($request->image_file) {
            $file = File::uploadFile($request->file('image_file'), $product_creation_process->id . "_" . rand(1000, 9000) . ".png", 42, "upload/product_creation/", true);

            $product_creation_process->file_id = $file->id;
            $product_creation_process->save();
        }
        if ($product_creation_process->goods_kind->has_sampling_required_in_product_creation == 1) {
            $product_creation_process->has_sampling_required = $request->has_sampling_required ? 1 : 0;
            $product_creation_process->save();
        }
        if ($product_creation_process->method_of_sending_product_id == 2) {
            $product_creation_process->status_id = 5231999; // معلق
            $product_creation_process->save();

            return redirect()->route($route_path . "posttex_address", $product_creation_process);

        } else {
            event(new ProductCreationProcessLogEvent($product_creation_process, 5231001));

        }

        // اگر نمونه فیزیکی نداشت، وضعیت بعدی را از روی تنظیمات طراحی می خواند.
        if (!$request->has_physical_sample) {
            /********* Next Status ************/
            $result_next_status = ProductCreationProcess::GetNextStatus(self::$info["button_id"], $product_creation_process);
            if (!$result_next_status["result"]) {
                return back()->withErrors($result_next_status["error"]);
            }
            $product_creation_process->status_id = $result_next_status["status_id"];
            $product_creation_process->save();
            /********* End Next Status **********/
        }

        // در صورتی که درخواست فرم طراحی کالا از طریق مرحله کالای مصرفی ارسال شده است.
        $product_creation_process_in_consumed = ProductCreationProcess::find($request->product_creation_process_id);
        if ($product_creation_process_in_consumed) {

            Product\ConsumedProduct\ConsumedProduct::create([
                "product_id" => $product_creation_process_in_consumed->product->id,
                "material_id" => null,
                "product_creation_process_id" => $product_creation_process->id,
                "status_id" => 3400002,// در حال تعریف کالای مصرفی
            ]);
            $route_path = ConsumedProductController::$info["route"] . "index";
            $product_creation_process->getCode();
            return redirect()->route($route_path, $product_creation_process_in_consumed)->with(["success" => "درخواست طراحی کالای مصرفی جدید ثبت گردید."]);
        }
        if ($request->has_physical_sample) {
            return redirect()->route($dashboard_path . "show_print_form", $product_creation_process)->with(["success" => "یک درخواست با موفقیت ثبت گردید، لطفا نمونه کالا را به واحد طراحی کالا تحویل/ ارسال نمایید."]);
        } else {
            return redirect()->route($dashboard_path . "view", $product_creation_process)->with(["success" => "یک درخواست با موفقیت ثبت گردید، "]);

        }
    }

    public static function CreateServiceInProductCreation($request, $dashboard_path)
    {
        $product_creation_process = ProductCreationProcess::create([
            "caption" => Message::convert_farsi_digits_to_english($request->caption),
            "user_id" => Auth::id(),
            "product_service_type_id" => $request->product_service_type_id,
            "has_physical_sample" => 0,
            "status_id" => 5231301,//در انتظار تکمیل خدمت
        ]);
        $product_creation_process->getCode();
        $product_creation_process->save();
        event(new ProductCreationProcessLogEvent($product_creation_process, 5231001));
        return redirect()->route($dashboard_path . "view", $product_creation_process)->with(["success" => "یک درخواست با موفقیت ثبت گردید، "]);
    }
}
