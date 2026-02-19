<?php

namespace App\Http\Controllers\LineProductStation\Product\ProductCreation;

use App\Events\Product\ProductCreationProcessLogEvent;
use App\Http\Controllers\Controller;
use App\Models\Customer\Customer;
use App\Models\File\File;
use App\Models\LineProduct\Packing\PackingTypeLabelPrintingType;
use App\Models\LineProduct\Product\ProductCreation\MethodOfSendingProduct;
use App\Models\LineProduct\Product\ProductCreation\ProductCreationProcess;
use App\Models\LineProduct\Product\ProductCreation\ProductCreationProcessLog;
use App\Models\LineProduct\Product\ProductCreation\ProductCreationProcessPriority;
use App\Models\Post\PostStatus;
use App\Models\Utility\Option;
use App\Models\Utility\Pdf;
use App\Models\Utility\Printer\PrinterFile;
use App\Models\Utility\Setting;
use App\Models\Utility\Status;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    //
    public static $perfix_production_status_code = "5231";
    public $view_path = "line_product_station.product.product_creation.dashboard.";
    public $route_path = "line_product_station.product.product_creation.dashboard.";

    public function index(Request $request)
    {

        $worker = Worker::find(Auth::id());
        $post_user = $worker->posts->first();
        $allowed_status_ids = PostStatus::getAllowedStatus(1, 5231);
        $allowed_status_ids_all = $allowed_status_ids;
        if ($request->waiting_status_id != 0 && !in_array($request->waiting_status_id, $allowed_status_ids)) {
            return back()->withErrors("شما اجازه دسترسی به مشاهده درخواست طراحی کالا با وضعیت انتخاب شده را ندارید");
        }
        if ($request->isMethod('post')) {
            $search = $request->search;
            $waiting_status_id = $request->waiting_status_id;
            $event_id = $request->event_id;
            $user_id = $request->user_id;
            $start_date = $request->start_date;
            $end_date = $request->end_date;


        } else {
            $search = session("search_product");
            $waiting_status_id = session("waiting_status_id_product");
            $event_id = session("event_id_product");
            $user_id = session("user_id_product");
            $start_date = session("start_date_product");
            $end_date = session("end_date_product");

        }
        session([
            "search_product" => $search,
            "waiting_status_id_product" => $waiting_status_id,
            "event_id_product" => $event_id,
            "user_id_product" => $user_id,
            "start_date_product" => $start_date,
            "end_date_product" => $end_date,
        ]);
        // search
        if ($waiting_status_id != 0) {
            $allowed_status_ids = [];
            $allowed_status_ids[] = $waiting_status_id;
        }
        $list = ProductCreationProcess::
        leftJoin('products', 'products.id', 'product_id')->
        join('users', 'users.id', 'user_id')->

        when($search != "", function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where("product_creation_processes.code", "like", "%{$search}%")
                    ->orWhere("product_creation_processes.caption", "like", "%{$search}%")
                    ->orWhere("products.code", "like", "%{$search}%")
                    ->orWhere("products.caption", "like", "%{$search}%")
                    ->orWhere("users.firstname", "like", "%{$search}%")
                    ->orWhere("users.lastname", "like", "%{$search}%");
            });
        })->
        // در صورتی که به داشبورد طراحی کالا دسترسی ندارد، فقط درخواست های خودش را ببیند.
        when(!$post_user->getMenuPermission($worker, 2101), function ($query) use ($worker) {
            return $query->where("user_id", $worker->id);
        })->
        when($event_id != 0 || $user_id != 0 || $start_date != 0 || $end_date != 0, function ($query) use ($event_id, $user_id, $start_date, $end_date) {
            return $query->join('product_creation_process_logs', 'product_creation_process_logs.product_creation_process_id', 'product_creation_processes.id')
                ->when($event_id != 0, function ($query) use ($event_id) {
                    return $query->where("product_creation_process_logs.event_id", $event_id);
                })
                ->when($user_id != 0, function ($query) use ($user_id) {
                    return $query->where("product_creation_process_logs.user_id", $user_id);
                })
                ->when($start_date != 0, function ($query) use ($start_date) {
                    return $query->where("product_creation_process_logs.created_at", ">=", $start_date);
                })
                ->when($end_date != 0, function ($query) use ($end_date) {
                    return $query->where("product_creation_process_logs.created_at", "<=", $end_date);
                });

        })->

        whereIn("product_creation_processes.status_id", $allowed_status_ids)->
        select("product_creation_processes.*")->
        with(["product", "status", "worker", "status"])->
        orderBy("product_creation_processes.id", "desc")->
        groupBy('product_creation_processes.id')->
        paginate();
        $worker_list = ProductCreationProcessLog::groupBy("user_id")->pluck('user_id');
        $waiting_status_option = Option::get("status_in_ids", $waiting_status_id, null, $allowed_status_ids_all);
        $event_option = Option::get("event_type_in_ids", $event_id, 5231);
        $worker_option = Option::get("worker", $user_id, null, $worker_list);
        return view($this->view_path . "index", compact("list", 'search', 'waiting_status_option', 'start_date', 'end_date', 'event_option', 'worker_option'));
    }

    public function view(ProductCreationProcess $product_creation_process)
    {

        $worker = Worker::find(Auth::id());
        $customer = Customer::where("user_id", $worker->id)->first();
        $controller_info = DashboardController::get_controller_info();
        $special_condition = DashboardController::enable_special_condition($product_creation_process);
        $button_list = ProductCreationProcessPriority::
        where("goods_kind_id", $product_creation_process->goods_kind_id)->
        pluck('description', 'button_id');

        if (
            $product_creation_process->product &&
            $product_creation_process->product->goods_type_id == 1 &&
            $product_creation_process->product->goods_kind_id != $product_creation_process->goods_kind_id
        ) {
            return back()->withErrors(" در طراحی کالا ناهنجاری اطلاعات یافت شده، لطفا با کارشناس اطلاعات پایه تماس بگیرید." . "<br/>" .
                "رسته کالایی طراحی کالا " . $product_creation_process->goods_kind->caption . " می باشد در حالی که رسته کالایی کالا " . $product_creation_process->product->goods_kind->caption . " می باشد.");
        }

        $before_status_list = self::GetBeforeStatus($product_creation_process);
        $route_path = $this->route_path;
        return view($this->view_path . "view", compact("product_creation_process", 'special_condition', "worker", "customer", "controller_info", 'button_list', "before_status_list", "route_path"));

    }

    public function log(ProductCreationProcess $product_creation_process)
    {

        $list = ProductCreationProcessLog::where("product_creation_process_id", $product_creation_process->id)->orderByDesc("id")->paginate(30);

        return view($this->view_path . "log", compact("product_creation_process", "list"));

    }

    public function go_to_before_step(ProductCreationProcess $product_creation_process, Status $status)
    {
        $before_status_list = self::GetBeforeStatus($product_creation_process);
        if (!in_array($status->id, array_keys($before_status_list))) {
            return back()->withErrors("وضعیت انتخاب شده جزء وضعیت های مجاز نمی باشد.");
        }
        if ($product_creation_process->status_id == $status->id) {
            return back()->withErrors("درخواست طراحی در وضعیت انتخاب شده قرار دارد.");
        }
        $product_creation_process->status_id = $status->id;
        $product_creation_process->save();
        event(new ProductCreationProcessLogEvent($product_creation_process, 5231039));

        return back()->with(["success" => "تغییر وضعیت درخواست طراحی با موفقیت انجام شد."]);
    }

    public static function GetBeforeStatus(ProductCreationProcess $product_creation_process)
    {
        $before_status_list = [];
        if ($product_creation_process->status_id != 5231201) {
            $before_status_list =
                ProductCreationProcessLog::
                join("status", "status_id", "status.id")->
                where("product_creation_process_id", $product_creation_process->id)->
                whereNotIn("status_id", [5231001, 5231004, 5231201, $product_creation_process->status_id])-> // در انتظار ارسال نمونه کالا - در انتظار ثبت اطلاعات پستی - طراحی تکمیل شده است.
                pluck("status.caption", "status_id")->toArray();
        }
        return $before_status_list;
    }

    public function show_print_form(ProductCreationProcess $product_creation_process)
    {
        $worker = Worker::find(Auth::id());
        $customer = Customer::where("user_id", $worker->id)->first();
        $product_creation_unit_address = Setting::getStringValue("product_creation_unit_address");
        $product_creation_unit_phone = Setting::getStringValue("product_creation_unit_phone");

        return view($this->view_path . "show_print_form", compact("product_creation_process", "worker", "customer", "product_creation_unit_address", "product_creation_unit_phone"));

    }

    public function print(ProductCreationProcess $product_creation_process)
    {


        $current_worker = Worker::find(Auth::id());
        if (!$current_worker->default_printer_id) {
            return redirect()->route("utility.printer.select_default_printer")->withErrors("لطفا پرینتر پیش فرض را انتخاب نمایید.");
        }

        $print_file = PrinterFile::create([
            "user_id" => $current_worker->id,
            "filename" => $product_creation_process->getCode() . ".pdf",
            "status_id" => 305001, // در انتظار دانلود
            "is_landscape" => 0,
            "printer_id" => $current_worker->default_printer_id
        ]);
        $label_printer_size = PackingTypeLabelPrintingType::find(3);
        Pdf::labelPrinter(DashboardController::getPdfFile($product_creation_process, $this->view_path),
            "P",
            $product_creation_process->getCode() . ".pdf", [

                $label_printer_size->long,
                $label_printer_size->width,
            ],
            $print_file
        );

        return back()->with(["success" => "پرینت  فرم درخواست طراحی کالا برای چاپ به پرینتر " . $current_worker->default_printer_id . " ارسال شد."]);
    }

    public function download(ProductCreationProcess $product_creation_process)
    {

        $label_printer_size = PackingTypeLabelPrintingType::find(3);

        Pdf::createAsHtml(DashboardController::getPdfFile($product_creation_process, $this->view_path),
            "P",
            $product_creation_process->getCode() . ".pdf", "A5"
        );
    }

    public static function getPdfFile(ProductCreationProcess $product_creation_process, $view_path)
    {

        $software_name = Setting::getStringValue("software_name");
        $customer = Customer::where("user_id", $product_creation_process->user_id)->first();
        $product_creation_unit_address = Setting::getStringValue("product_creation_unit_address");
        $product_creation_unit_phone = Setting::getStringValue("product_creation_unit_phone");
        $html[0] = view($view_path . "print._head")->render();
        $html[0] .= view($view_path . "print._print_info", compact("product_creation_unit_phone", "product_creation_unit_address", "product_creation_process", "software_name", "customer"))->render() . $html[0];
        $html[0] .= view($view_path . "print._footer")->render();

        return $html;
    }

    public function checkPermission(ProductCreationProcess $product_creation_process)
    {
        $result = DashboardController::checkPermissionConditions($product_creation_process);
        if (!$result["result"]) {
            $message = \Session::get('success');
            if (isset($message)) {
                return redirect()->route($this->route_path . "index")->with(["success" => $message]);
            }

            return redirect()->route($this->route_path . "index")->withErrors($result["message"]);
        }

    }

    public static function checkPermissionConditions(ProductCreationProcess $product_creation_process, $info = false)
    {

        if ($info != false) {
            foreach ($info["enable_status"] as &$value) {
                $value = DashboardController::$perfix_production_status_code . $value;
            }
            unset($value);
            if (!in_array($product_creation_process->status_id, $info["enable_status"])) {
                return [
                    "result" => false,
                    "message" => "وضعیت فرم طراحی جهت عملیات نامعتبر است",
                    "error_type" => "for_form_status"
                ];
            }

            $post_user = Auth::user()->posts->first();
            if (!$post_user->checkButtonPermission($info["route"] . "index")) {
                return [
                    "result" => false,
                    "message" => "دسترسی  عملیات برای شما تعریف نشده است",
                ];
            }
        }

        return [
            "result" => true,
        ];

    }


    public static function get_controller_info($type = "")
    {
        // ProductCreationProcessSeeder
        $controller_info = [
            "001" => NewFormController::$info,
            "002" => BasicInformationRegistrationController::$info,

            "004" => ReceiveProductSampleController::$info,
            "005" => SaleController::$info,
            "006" => WarehouseController::$info,
            "007" => ClassificationController::$info,
            "008" => PropertyController::$info,
//            "009" => ,Empty
            "010" => ConsumedProductController::$info,
            "011" => ProductRouteController::$info,
            "012" => ProductRoutePropertyController::$info,
            "013" => BOMController::$info,
            "014" => BOMPermutationController::$info,
            "015" => ReplaceProductController::$info,
            "016" => WasteController::$info,
            "017" => MaterialFlowController::$info,
            "018" => LotNumberController::$info,
            "019" => ShadeNumberController::$info,
            "020" => PackingTypeController::$info,
            "021" => ActualCostController::$info, // بهای تمام شده
            "022" => SampleProductionOrderController::$info,  // دستور تولید نمونه
            "023" => SampleEndOfProductionController::$info,  // پایان تولید نمونه
            "024" => SampleInitialApprovalController::$info,
            "025" => SampleFinalApprovalController::$info,
            "026" => SampleSendToCustomer::$info,
//            "26"=> تایید توسط مشتری
            "027" => SampleApprovalByCustomerController::$info,
            "028" => ProductImageController::$info, // تصویر کالا
            "029" => RegisterInFinancialSoftwareController::$info, // تصویر کالا

            "030" => AddTariffRowsController::$info, // تعرفه گذاری
            "031" => PricingController::$info, // قیمت گذاری

            "032" => Step1DesignController::$info, //گام یک
            "033" => Step2DesignController::$info, // گام 2
            "034" => Step3DesignController::$info, // گام3
            "035" => Step4DesignController::$info, // گام4
            "036" => Step5DesignController::$info, // گام5


            "037" => NewFormQuickController::$info,
            "038" => ConsumedProductQuickController::$info,
            "044" => PropertyQuickController::$info,
            "045" => ProductImageQuickController::$info,

            "039" => BasicInformationServiceController::$info, // تکمیل اطلاعات خدمت
            "040" => RegisterServiceInFinancialSoftwareController::$info,//ثبت اطلاعات خدمت در نرم افزار مالی
            "041" => ConfirmFinalServiceController::$info,//تایدد اطلاعات خدمت
            "042" => QualityControlController::$info,//کنترل کیفیت
            "043" => PlaningController::$info,// برنامه ریزی


        ];

        return $controller_info;
    }

    public static function enable_special_condition(ProductCreationProcess $product_creation_process)
    {


        $result = [];

        $result["030"] = $product_creation_process->product->possibility_of_sale ?? 0;


        return $result;


    }

    public static function get_controller_info_for_next($type = "")
    {
        // ProductCreationProcessSeeder
        $controller_info = [
            "002" => BasicInformationRegistrationController::$info,
            "004" => ReceiveProductSampleController::$info,
            "005" => SaleController::$info,
            "006" => WarehouseController::$info,
            "007" => ClassificationController::$info,
            "008" => PropertyController::$info,
//            "009" => ,Empty

            "010" => ConsumedProductController::$info,
            "011" => ProductRouteController::$info,
            "012" => ProductRoutePropertyController::$info,
            "013" => BOMController::$info,
            "014" => BOMPermutationController::$info,
            "015" => ReplaceProductController::$info,
            "016" => WasteController::$info,
            "017" => MaterialFlowController::$info,
            "018" => LotNumberController::$info,
            "019" => ShadeNumberController::$info,
            "020" => PackingTypeController::$info,
            "021" => ActualCostController::$info, // بهای تمام شده
            "022" => SampleProductionOrderController::$info,  // دستور تولید نمونه
            "023" => SampleEndOfProductionController::$info,  // پایان تولید نمونه
            "024" => SampleInitialApprovalController::$info,
            "025" => SampleFinalApprovalController::$info,
            "026" => SampleSendToCustomer::$info,
            "027" => SampleApprovalByCustomerController::$info,
            "028" => ProductImageController::$info,
            "029" => RegisterInFinancialSoftwareController::$info,

            "030" => AddTariffRowsController::$info, // تعرفه گذاری
            "031" => PricingController::$info, // قیمت گذاری
            "032" => Step1DesignController::$info, //گام یک
            "033" => Step2DesignController::$info, // گام 2
            "034" => Step3DesignController::$info, // گام3
            "035" => Step4DesignController::$info, // گام4
            "036" => Step5DesignController::$info, // گام5
            "042" => QualityControlController::$info, // کنترل کیفیت
            "043" => PlaningController::$info, // کنترل کیفیت

        ];

        return $controller_info;
    }
}
