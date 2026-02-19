<?php

namespace App\Http\Controllers\LineProductStation\Product\ProductCreation;

use App\Events\Product\ProductCreationProcessLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\LineProductStation\ProductController;
use App\Models\File\File;
use App\Models\LineProduct\GoodsKind;
use App\Models\LineProduct\Machine\MachineAllocation;
use App\Models\LineProduct\Packing\PackingTypeLabelPrintingType;

use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\ProductCreation\MethodOfSendingProduct;
use App\Models\LineProduct\Product\ProductCreation\ProductCreationProcess;
use App\Models\Production\Production;
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

class NewFormQuickController extends Controller
{
    //
    public static $info = [
        "route" => "line_product_station.product.product_creation.new_form_quick.",
        "view" => "line_product_station.product.product_creation.new_form_quick.",
        "enable_status" => [""],
        "button" => ["caption" => "تعریف سریع کالای مشابه", "class" => "btn-primary"],
        "button_id" => 5231004 // تایید دریافت نمونه کالا / ثبت درخواست
    ];
    var $view_path;
    var $route_path;
    public $dashboard_path = "line_product_station.product.product_creation.dashboard.";

    public function __construct()
    {
        $this->route_path = self::$info["route"];
        $this->view_path = self::$info["route"];
    }

    public function index()
    {

        if ($this->checkPermission() != "") {
            return $this->checkPermission();
        }

        $list = MachineAllocation::
        join("production_cards", "production_id", "production_cards.id")->
        where([
            "production_type_id" => 2,
        ])->
        whereIn("machine_allocation.status_id", [5310010, 5310040])-> // هر کارت جاری یا رزور
        groupBy("production_id")->
        get();

        $option_list[] = ["id" => 0, "value" => "", "text" => "لطفا یک مورد را انتخاب نمایید."];
        foreach ($list as $item) {
            $option_list[] = ["id" => $item->production_id, "value" => $item->production_id, "text" => "تخصیص " . $item->allocation_id . " - " . $item->product->fullCaption() . " (" . $item->machine->caption . ")"];
        }


        return view($this->view_path . "index", compact("option_list"));
    }

    public function submit(Request $request)
    {

        if ($this->checkPermission() != "") {
            return $this->checkPermission();
        }

        $production = Production::find($request->machine_production_id);
        if (!$production) {
            return back()->withErrors("کالای انتخاب شده نامعتبر است، لطفا یک بار دیگر تلاش کنید.");
        }
        $machine_allocation = MachineAllocation::
        whereIn("machine_allocation.status_id", [5310010, 5310040])-> // هر کارت جاری یا رزور
        where([
            "production_id" => $production->id
        ])->
        first();
        if (!$machine_allocation) {
            return back()->withErrors("تخصیص جاری/رزور برای ماشین یافت نشد، لطفا مجدد تلاش کنید.");
        }
        $substr = substr_count($request->caption, ' ');
        if ($substr > 8) {
            return back()->withErrors("نام کالا حداکثر می تواند دارای 8 کاراکتر  Space باشد");
        }


        // اگر نیاز به تکمیل اطلاعات بعضی از گام ها نیست از آن عبور می کند.
        $break_step_list_all = [
            "consumed_break" => 5231038,
            "property_break" => 5231044,
            "image_break" => 5231045,
        ];

        $break_step = [];
        foreach ($break_step_list_all as $key => $value) {


            if (isset($request->$key) && !$request->$key) {
                $break_step[] = $value;
            }
        }
        if (count($break_step)== count($break_step_list_all)) {
            return back()->withErrors("لطفا حداقل یکی از گام ها جهت تغییر اطلاعات را انتخاب نمایید.");
        }
        $substr = substr_count($request->caption, ' ');
        if ($substr > 8) {
            return back()->withErrors("نام کالا حداکثر می تواند دارای 8 کاراکتر  Space باشد");
        }



        $product = $production->product;
        $data["parent_product_id"] = $product->id;
        $data["parent_product_code"] = $product->code;
        $data["production_id"] = $production->id;
        $data["production_serial"] = $production->serial;
        $data["machine_id"] = $machine_allocation->machine_id;
        $data["machine_code"] = $machine_allocation->machine->code;
        $data["allocation_id"] = $machine_allocation->allocation_id;
        $text = json_encode($data);

        $product_creation_process = ProductCreationProcess::create([
            "caption" => Message::convert_farsi_digits_to_english($request->caption),
            "user_id" => Auth::id(),
            "goods_kind_id" => $product->goods_kind_id,
            "method_of_sending_product_id" => 1, // تحویل حضوری
            "product_service_type_id" => 1, // کالای
            "has_physical_sample" => 1,
            "status_id" => 5231501 // در انتظار ثبت کالای مصرفی (تعریف سریع)
        ]);
        $request["code"] = $product_creation_process->getCode();
        $new_product = ProductController::PostCopyFromOther($request, $product, null);


        event(new ProductCreationProcessLogEvent($product_creation_process, 5231501, $text));


        if (count($break_step) > 0) {
            event(new ProductCreationProcessLogEvent($product_creation_process, 5231610, json_encode($break_step)));
        }


        /********* Next Status ************/
        $product_creation_process->parent_product_id = $product->id;
        $product_creation_process->product_id = $new_product->id;
        //پیش فرض این وضعیت را می گذاریم و سپس در مازول وضعیت بعدی اگر نیاز بود وضعیت بعدی را تغییر می دهیم.
        $product_creation_process->status_id = 5231501; // تعریف کالای مصرفی (تعریف سریع کالا)
        $product_creation_process->save();
        /********* End Next Status **********/

        //کد کالای آزمایشی: رسته کالایی - شناسه کالا
        $new_product->code = $new_product->goods_kind->code . "/" . $new_product->id;
        $new_product->save();


        $result = ProductCreationProcess::GetNextStatusQuick(0, $product_creation_process);
        if (!$result["result"]) {
            return back()->withErrors("طراحی کالا با موفقیت ثبت گردید ولی ماژول بعدی با خطا مواجه شد و ادامه فرایند متوقف گردید."."<br/>".$result["message"]);
        }

        if($result["status_id"]==5231020){ // نمونه آزمایشگاهی
            1/0;
        }
        else{
            /********* Next Status ************/
            $product_creation_process->parent_product_id = $product->id;
            $product_creation_process->product_id = $new_product->id;
            $product_creation_process->status_id = $result["status_id"];
            $product_creation_process->save();
            /********* End Next Status **********/
        }



        return redirect()->route($this->dashboard_path . "view", $product_creation_process)->with(["success" => "ثبت درخواست طراحی با موفقیت ثبت گردید."]);
    }


    public
    static function PostSubmitApi(Request $request)
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
            "caption" => $request->caption,
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

    public
    function posttex_address(ProductCreationProcess $product_creation_process)
    {
        return redirect()->route("dashboard")->with(["success" => "درخواست طراحی شما ثبت گردید، لطفا نمونه کالا را به آدرس شرکت ارسال فرمایید. "]);
    }

    public
    function create_from_exist_product()
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

    public
    function submit_from_exist_product(Request $request)
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

        $product_process_creation = ProductCreationProcess::create([
            "user_id" => Auth::id(),
            "status_id" => $request->status_id,
            "method_of_sending_product_id" => 1,
            "caption" => $product->caption,
            "product_id" => $product->id,
            "has_physical_sample" => 0,
            "goods_kind_id" => $product->goods_kind_id,
            "product_service_type_id" => $product->product_service_type_id,
            "sample_production_id" => null,
            'has_sampling_required' => 0,
        ]);
        $product_process_creation->getCode();
        return redirect()->route($this->dashboard_path . "index")->with(["success" => "یک درخواست طراحی با کد " . $product_process_creation->code . " برای کالا ایجاد گردید."]);
    }

    public
    function checkPermission()
    {
        $post_user = Auth::user()->posts->first();
        if (!$post_user->checkButtonPermission($this->route_path . "index")) {
            return redirect()->route($this->dashboard_path . "index")->withErrors("شما مجوز دسترسی به این ماژول را ندارید.");
        }


    }

}
