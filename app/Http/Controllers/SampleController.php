<?php

namespace App\Http\Controllers;

use App\Events\Warehouse\Form\FormLogEvent;
use App\Events\Warehouse\PutInWarehouseEvent;
use App\Events\Warps\WarpsAvailableEvent;
use App\Http\Controllers\GoodsKindProcess\Fabric\FinishingMachine\Machine\DashboardController;
use App\Http\Controllers\GoodsKindProcess\FabricRaw\Jacquard\Machine\BeginChangeWarpsController;
use App\Http\Controllers\GoodsKindProcess\FabricRaw\Jacquard\Machine\ChangeAllocationAmountController;
use App\Http\Controllers\GoodsKindProcess\FabricRaw\Jacquard\Machine\EndOfProductionCardTextureController;
use App\Http\Controllers\GoodsKindProcess\FabricRaw\Jacquard\ProductionCard\MachineAllocationController;
use App\Http\Controllers\GoodsKindProcess\FabricRaw\PackingForm\ChangePackingQuickController;
use App\Http\Controllers\GoodsKindProcess\General\Machine\GeneralMaterialReturnToWarehouseController;
use App\Http\Controllers\GoodsKindProcess\General\ProductionCard\GeneralProductionChannelController;
use App\Http\Controllers\HR\Employment\Register\StartController;
use App\Http\Controllers\HR\Personal\AbsenceController;
use App\Http\Controllers\HR\Personal\ShiftWorkDayController;
use App\Http\Controllers\Production\PublicModule\RegisterProductionController;
use App\Http\Controllers\Sales\ProductRequestPermissionController;
use App\Http\Controllers\Utility\Script\Script1001Controller;
use App\Http\Controllers\Utility\Script\Script1003Controller;
use App\Http\Controllers\Utility\Script\Script1004Controller;
use App\Http\Controllers\Utility\Script\Script1005Controller;
use App\Http\Controllers\Utility\Script\Script1006Controller;
use App\Http\Controllers\Utility\Script\Script1007Controller;
use App\Http\Controllers\Utility\Script\Script1008Controller;
use App\Http\Controllers\Utility\Script\Script1009Controller;
use App\Http\Controllers\Utility\Script\Script1010Controller;
use App\Http\Controllers\Utility\Script\Script1011Controller;
use App\Http\Controllers\Utility\Script\Script1012Controller;
use App\Http\Controllers\Utility\Script\Script1013Controller;
use App\Http\Controllers\Utility\Script\Script1014Controller;
use App\Http\Controllers\Utility\Script\Script1015Controller;
use App\Http\Controllers\Utility\Script\Script1016Controller;
use App\Http\Controllers\Utility\Script\Script1017Controller;
use App\Http\Controllers\Utility\Script\Script1021Controller;
use App\Http\Controllers\Utility\Script\Script1022Controller;
use App\Http\Controllers\Utility\Script\Script1023Controller;
use App\Http\Controllers\Utility\Script\Script1025Controller;
use App\Http\Controllers\Utility\Script\Script1026Controller;
use App\Http\Controllers\Utility\Script\Script1030Controller;
use App\Http\Controllers\Warehouse\Out\DeliveryController;
use App\Http\Controllers\Warehouse\Out\ExitFormController;
use App\Models\Accounting\Client\ClientFactor;
use App\Models\Accounting\Client\ClientTransaction;
use App\Models\Accounting\CostCenter;
use App\Models\Accounting\Tariff\ProductTariff;
use App\Models\Accounting\Tariff\ProductTariffLog;
use App\Models\Accounting\Tariff\Tariff;
use App\Models\Accounting\Tariff\TariffLog;
use App\Models\Contractor\Contractor;
use App\Models\Contractor\MachineAllocationPackingForm;
use App\Models\Form\Form;
use App\Models\Form\FormGeneralItem;
use App\Models\Form\FormItem;
use App\Models\Form\FormLog;
use App\Models\Form\Packing\PackingForm;
use App\Models\Form\Packing\PackingFormItem;
use App\Models\Form\Packing\PackingFormItemImportantStatus;
use App\Models\GoodsKindProcess\Fabric_Raw\FabricRaw;
use App\Models\GoodsKindProcess\Warps\RequestForm\WarpsRequestForm;
use App\Models\GoodsKindProcess\Warps\Warps;
use App\Models\HR\Company\Company;
use App\Models\HR\Employment\Employment;
use App\Models\HR\LeaveOvertime\LeaveOvertime;
use App\Models\HR\LeaveOvertime\LeaveOvertimeConfirmation;
use App\Models\HR\LeaveOvertime\LeaveRemainder;
use App\Models\HR\Selection\SelectionPostSetting;
use App\Models\HR\Shift\DailyShiftOperation;
use App\Models\HR\User\UserBankAccount;
use App\Models\HR\User\UserDependent;
use App\Models\HR\User\UserDevice;
use App\Models\HR\User\UserEntryLog;
use App\Models\HR\User\UserJobInformation;
use App\Models\HR\User\UserOperation;
use App\Models\LineProduct\Degree;
use App\Models\LineProduct\GoodsKind;
use App\Models\LineProduct\GoodsKindPropertyValue;
use App\Models\LineProduct\LineProductStation;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\Allocation\AllocationBrand;
use App\Models\LineProduct\Machine\Allocation\AllocationDoffs;
use App\Models\LineProduct\Machine\Allocation\MachineAllocationMaterialConsumed;
use App\Models\LineProduct\Machine\Allocation\Modification\MachineAllocationModificationForm;
use App\Models\LineProduct\Machine\CurrentMachineInput;
use App\Models\LineProduct\Machine\CurrentMachineInputLog;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineAllocation;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Machine\MachineModuleType;
use App\Models\LineProduct\Machine\MachineModuleType\MachineModuleTypePropertyValue;
use App\Models\LineProduct\Machine\MachineProductPropertyValue;
use App\Models\LineProduct\Machine\MachinePropertyValue;
use App\Models\LineProduct\Machine\MachineType;
use App\Models\LineProduct\Machine\MachineTypeInputBandPackingType;
use App\Models\LineProduct\Machine\ProductionChannel\MachineProductionChannelType;
use App\Models\LineProduct\Machine\ProductionChannel\ProductionChannel;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\LineProduct\Packing\PackingTypeLabelPrintingType;
use App\Models\LineProduct\Product\BOM\BOM;
use App\Models\LineProduct\Product\BOM\BOMDegree;
use App\Models\LineProduct\Product\BOM\BOMItem;
use App\Models\LineProduct\Product\BOM\BOMPermutation;
use App\Models\LineProduct\Product\BOM\BOMPermutationItem;
use App\Models\LineProduct\Product\BOM\BOMReplace;
use App\Models\LineProduct\Product\MaterialFlow;
use App\Models\LineProduct\Product\ProductCreation\ProductCreationProcess;
use App\Models\LineProduct\Product\ProductCreation\ProductCreationProcessLog;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestForm;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormForm;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormItem;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormSessionData;
use App\Models\LineProduct\Product\ProductRoute;
use App\Models\Order\OrderFactor;
use App\Models\Order\OrderListPackingType;
use App\Models\Order\TransKind;
use App\Models\Post\Evaluation\PostEvaluation;
use App\Models\Post\Post;
use App\Models\Post\PostUser;
use App\Models\Production\ProductionChannelType;
use App\Models\Production\ProductionDetailsReport;
use App\Models\Production\ProductionForm;
use App\Models\Production\ProductionFormItem;
use App\Models\Production\ProductionPackingType;
use App\Models\QualityControl\QualityControlProductFault;
use App\Models\Report\RealTime\RealTimeOrder;
use App\Models\Report\Report1010\Report1010MachineLog;
use App\Models\Supplier\Supplier;
use App\Models\User;
use App\Models\Utility\Address\Address;
use App\Models\Utility\Address\Posttex\Posttex;
use App\Models\Utility\Algorithm\ProductionAllocation\Algorithm4ProductionAllocationToContractorByDownstreamTermination;
use App\Models\Utility\Catalog;
use App\Models\Utility\Financial\FinancialSoftware;
use App\Models\Utility\Financial\FinancialSoftwareTransferForm;
use App\Models\Utility\Financial\FinancialSoftwareTransKind;
use App\Models\Utility\Financial\FinancialSoftwareTransKindLog;
use App\Models\Utility\JsonDataList;
use App\Models\Utility\OfficeAutomation\OfficeAutomationToDoList;
use App\Models\Utility\OfficeAutomation\OfficeAutomationWork;
use App\Models\Utility\Printer\Printer;
use App\Models\Utility\Printer\PrinterFile;
use App\Models\Utility\QueueOfLargeOperation;
use App\Models\Utility\Script\Script;
use App\Models\Utility\Setting;
use App\Models\Utility\SpecialLicense\SpecialLicense;
use App\Models\Utility\Transport\Transport;
use App\Models\Utility\Transport\TransportItem;
use App\Models\Utility\Transport\TransportPackingForm;
use App\Models\Warehouse\Pallet\Pallet;
use App\Models\Warehouse\Pallet\PalletItem;
use App\Models\Warehouse\Shelving\Shelving;
use App\Models\Warehouse\Warehouse;
use App\Models\Warehouse\WarehouseProduct;
use App\Models\Warehouse\WarehouseShelving\WarehouseShelving;
use App\Models\Warehouse\WarehouseShelving\WarehouseShelvingProduct;
use App\Notifications\SMSNotification;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;
use Jenssegers\Agent\Agent;
use Kavenegar\KavenegarApi;
use Morilog\Jalali\CalendarUtils;
use Mpdf\Strict;
use Mpdf\Tag\Em;
use Mtownsend\XmlToArray\XmlToArray;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Spatie\ArrayToXml\ArrayToXml;
use Verta;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Option;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Line;
use App\Models\Utility\Utility;
use App\Models\Utility\Unit;
use App\Models\Utility\Status;
use App\Models\Utility\Priority;
use App\Models\Utility\Message;
use App\Models\Order\NewOrderList;
use App\Models\Order\Order;
use App\Models\Order\OrderList;
use App\Models\Order\OrderType;
use App\Models\Customer\Customer;
use App\Models\Production\ExtraProduction;
use App\Models\Production\ProductionDateTime;
use App\Models\Production\ProductionWorker;
use App\Models\Production\Production;
use App\Models\Worker;
use App\Models\TrmOrder;
use App\Models\TrmProductionCard;
use App\Models\TrmProductionDate;
use App\Models\TrmRFW;
use App\Models\TrmOrderList;
use App\Models\Order\RequstFromWarehouse;
use Ammont\Finglify\Finglify;
use Illuminate\Support\Facades\Http;

# UPDATE request_from_warehouse INNER JOIN production_cards on production_cards.id=request_from_warehouse.production_card_id set request_from_warehouse.status_id=386001 where production_cards.status_id in (510,520,530)
class SampleController extends Controller
{



    public function operation()
    {
        $worker = Worker::find(408);
        $date = "2025-08-13";
        return Script1023Controller::calculate($worker, $date);

        $date = Carbon::now()->addDay($x)->format("Y-m-d");
        for ($k = $x; $k <= $y; $k++) {
            Script1023Controller::handle(-$k);
            echo -$k;
        }
        return "OK";
//
//     return   ClientTransaction::UpdateCredit();

    }


    public static function getAllSellingAmount2($customer_id, $setting_types = null, $invalid_order_id = null, $product_id = null)
    {
        // براساس نوع فاکتور (رسمی، غیررسمی)
        $status_list = Setting::getStringValue($setting_types ?? "sale_formal_status_list");
        $status_list = json_decode($status_list, true);

        $warehouse_status = [];
        if (in_array(35090, $status_list)) { // تایید برگ خروج
            $status_list = array_diff($status_list, [35090]);
            $status_list[] = 7005008; // در انتظار تایید برگ خروج (انبار)
        }
        if (in_array(35040, $status_list)) { // ارسال ناقص
            $status_list = array_diff($status_list, [35040]);
            $status_list[] = 7005004; // در انتظار ارسال باقی مانده درخواست
        }


        $list = Order::join("order_factor", "orders.id", "order_id")->
        when($customer_id, function ($query) use ($customer_id) {
            return $query->where("orders.customer_id", $customer_id);
        })->
        when($invalid_order_id, function ($query) use ($invalid_order_id) {
            return $query->where("orders.id", "!=", $invalid_order_id);
        })->
        when($product_id, function ($query) use ($product_id) {
            return $query->where("order_factor.product_id", $product_id);
        })->
        whereIn("orders.status_id", $status_list)->
        selectRaw("sum(total_price_with_tax) as total_price_with_tax,sum(carton* number_in_carton) as amount")->
        first();

        return $list_warehouse = Product\ProductRequest\ProductRequestForm::join("product_request_form_item", "product_request_forms.id", "product_request_form_id")->
        where("applicant_type_id", 30)->
        when($product_id, function ($query) use ($product_id) {
            return $query->where("product_id", $product_id);
        })->
        whereIn("product_request_forms.status_id", $status_list)->

        get();


        $amount_list["total_selling_type"] = $list["total_price_with_tax"];
        $amount_list["sum_amount_order"] = $list["amount"];
        $amount_list["sum_amount_remaining"] = $list_warehouse["amount_remaining"];
        $amount_list["sum_order_and_warehouse_amount"] = $list["amount"] + $list_warehouse["amount_remaining"];


        $final_amount = PackingForm::join("packing_form_item", "packing_forms.id", "packing_form_id")->
        where("product_id", $product_id)->
        where("warehouse_status_id", 4201)->
        whereIn("packing_forms.status_id",
            [
                7007002, // در انتظار تایید انبار
                7007005, // در انتظار تحویل به انبار
                7007026, // در انتظار کنترل کیفیت
            ]
        )->sum("final_amount");


        $amount_list["sum_packing_forms"] = $final_amount;


        $production_sum = Production::where("status_id", "!=", 520)->
        when($product_id, function ($query) use ($product_id) {
            return $query->where("product_id", $product_id);
        })->sum("number");


        $production_form_item_sum = Production::
        join("production_form_item", "production_cards.id", "production_form_item.production_id")->
        where("production_cards.status_id", "!=", 520)->
        when($product_id, function ($query) use ($product_id) {
            return $query->where("production_form_item.product_id", $product_id);
        })->sum("final_amount");

        $amount_list["production_sum"] = $production_sum;
        $amount_list["production_form_item_sum"] = $production_form_item_sum;
        $amount_list["production_form_item_sum_production_sum"] = $production_sum - $production_form_item_sum < 0 ?
            0 : $production_sum - $production_form_item_sum;

        return $amount_list;
    }

    public static function GetOtherCustomerPermission($type, $order, $product_ids = [], $product_request_form_ids = [])
    {
        switch ($type) {
            case 1:
                return ProductRequestForm::join("product_request_form_item", "product_request_forms.id", "=", "product_request_form_id")->
                whereIn("product_request_forms.status_id", self::$product_request_forms_status_list)->
                whereIn("product_id", $product_ids)->
                where("applicant_id", "!=", $order->customer_id)->
                where("applicant_type_id", 30)->
                selectRaw("sum(amount_remaining) as amount, product_id")->
                groupBy("product_id")->pluck("amount", "product_id");
                break;
            case 2:
                $list["list"] = ProductRequestForm::join("product_request_form_item", "product_request_forms.id", "=", "product_request_form_id")->
                whereIn("product_request_forms.status_id", self::$product_request_forms_status_list)->
                where("product_id", $product_ids[0])->
                where("applicant_id", "!=", $order->customer_id)->
                where("applicant_type_id", 30)->
                selectRaw("product_request_forms.code as prf_code,product_request_forms.id as prf_id, product_request_form_item.id as prf_item_id, order_id , amount_request, amount_sent,amount_remaining, applicant_id")->
                with("order", "order.customer")->
                paginate(50);

                $list["customers"] = Customer::pluck("caption", "id")->toArray();

                return $list;
                break;

            case 3: // لیست درخواست های
                $list["list"] = ProductRequestForm::join("product_request_form_item", "product_request_forms.id", "=", "product_request_form_id")->
                whereIn("product_request_forms.status_id", self::$product_request_forms_status_list)->
                where("product_id", $product_ids[0])->
                whereIn("product_request_forms.id", $product_request_form_ids)->
                selectRaw("product_request_forms.code as prf_code,product_request_forms.id as prf_id, product_request_form_item.id as prf_item_id, order_id , amount_request, amount_sent,amount_remaining, applicant_id")->
                with("order", "order.customer")->
                paginate(50);


                return $list;
                break;
        }

        return null;

    }

    public static function GetCurrentDelivery($product_ids = [], $product_request_form_ids = [])
    {

        // کل درحال تحویل ها
// وضعیت هایی که در آن برگ خروج کشیده شده است.
        //$status_were_transaction_not_ok = ProductRequestForm::Get_CurrentExistFromForDashboard(30);
        $status_were_transaction_not_ok = [500000514, 500000515, 500000520, 500000525, 500000530, 500000500];


        return ProductRequestFormForm::join("forms", "forms.id", "product_request_form_form.form_id")->
        join("form_item", "form_item.form_id", "product_request_form_form.form_id")->
        whereIn("forms.status_id", $status_were_transaction_not_ok)->

        when(count($product_ids) > 0, function ($query) use ($product_ids) {
            return $query->whereIn("form_item.product_id", $product_ids);
        })->
        when(count($product_request_form_ids) > 0, function ($query) use ($product_request_form_ids) {
            return $query->whereIn("product_request_form_id", $product_request_form_ids);
        })->

        selectRaw("product_id,product_request_form_id, sum(amount) as amount")->
        groupBy("product_request_form_id", "product_id")->
        get();
    }

    public static function GetProductDataByProduct(Order $order, $order_list_ids = [])
    {
        $product_request_form_items = OrderList::
        join("orders", "orders.id", "order_list.order_id")->
        leftJoin("product_request_form_item", 'product_request_form_item.order_list_id', 'order_list.id')->
        when(count($order_list_ids) > 0, function ($query) use ($order_list_ids) {
            $query->whereIn("order_list.id", $order_list_ids);
        })->
        where("orders.customer_id", $order->customer_id)->
        whereIn("orders.status_id", [35030, 35040, 35090])->
        selectRaw(
            "orders.id as order_id  ,order_list.id as order_list_id,product_request_form_item.product_id,
            orders.code as order_code,orders.series as order_series,
            sum(product_request_form_item.amount_remaining) as amount_remaining,sum(product_request_form_item.amount_sent) as amount_sent,sum(product_request_form_item.amount_request) as amount_request, order_list.amount as order_amount
            ")->
        groupBy("product_request_form_item.product_id")->
        with("product")->
        orderBy("amount_remaining", "desc")->
        orderBy("order_list.product_id", "desc")->
        orderBy("orders.id", "desc")->
        paginate(max(count($order_list_ids), 30));

        if (count($product_request_form_items) == 0) {
            return [
                "result" => false,
                "error" => "هنوز درخواست کالا از انبار برای سفارش های مشتری ایجاد نشده است ویا تمامی سفارش ها ارسال شده اند."
            ];
        }
        $product_ids = [];
        $permission_amount = []; // مقدار مجوز حهت خروج
        $order_list_product = [];
        foreach ($product_request_form_items as $product_request_form_item) {
            $product_ids[] = $product_request_form_item->product_id;
            $order_list_product  [$product_request_form_item->order_list_id] = $product_request_form_item->product_id;
        }

        $inventory_list = WarehouseProduct::getProductInventoryList($product_ids);

        return [
            "result" => true,
            "product_request_form_items" => $product_request_form_items,
            "permission_amount" => $permission_amount,
            "inventory_list" => $inventory_list,
        ];
    }

    public static function GetProductData(Order $order, $order_list_ids = [], $does_sales_set_permission_for_inventory_on_the_way = 0, $method_of_calculating_active_inventory_on_sales = 0)
    {

        $product_request_form_items = OrderList::
        join("orders", "orders.id", "order_list.order_id")->
        leftJoin("product_request_form_item", 'product_request_form_item.order_list_id', 'order_list.id')->
        when(count($order_list_ids) > 0, function ($query) use ($order_list_ids) {
            $query->whereIn("order_list.id", $order_list_ids);
        })->
        where("orders.customer_id", $order->customer_id)->
        whereIn("orders.status_id", self::$order_status_list)->
        // whereNotIn("product_request_forms.status_id", [7005006])->
        selectRaw(
            "order_list.order_id as order_id  ,order_list.id as order_list_id,order_list.product_id,product_request_form_id,orders.order_datetime,
            orders.code as order_code,orders.series as order_series,
            sum(product_request_form_item.amount_remaining) as amount_remaining,sum(product_request_form_item.amount_sent) as amount_sent,sum(product_request_form_item.amount_request) as amount_request, order_list.amount as order_amount
            ")->
        groupBy("orders.id", "order_list.product_id")->
        with("product")->
        orderBy("orders.order_datetime", "desc")->
        orderBy("amount_remaining", "asc")->
        orderBy("order_list.product_id", "desc")->
        paginate(max(count($order_list_ids), 50)); //

        if (count($product_request_form_items) == 0) {
            return [
                "result" => false,
                "error" => "هنوز درخواست کالا از انبار برای سفارش های مشتری ایجاد نشده است ویا تمامی سفارش ها ارسال شده اند."
            ];
        }
        $product_ids = [];
        $permission_amount = []; // مقدار مجوز حهت خروج
        $order_list_product = [];
        $order_ids_count = []; // تعداد سفارش هایی که در صفحه نمایش داده می شود (جهت مایش تیک)
        foreach ($product_request_form_items as $product_request_form_item) {
            $product_ids[] = $product_request_form_item->product_id;
            $order_list_product  [$product_request_form_item->order_list_id] = $product_request_form_item->product_id;


        }
        $product_ids[] = -1;


        // به دست آوردن مقدار بسته بندی ها با توجه به نوع بسته بندی
        $order_list_ids = array_keys($order_list_product);
        $order_list_ids[] = -1;
        $order_list_packing_types = OrderListPackingType::
        whereIn("order_list_id", $order_list_ids)->
        select("order_list_id", "packing_type_id")->
        with("packing_type")->
        get();
        $order_list_packing_type_list = [];
        $order_list_packing_type_cation_list = [];

        $packing_type_list = [];
        foreach ($order_list_packing_types as $order_list_packing_type) {
            $order_list_packing_type_list[$order_list_packing_type->order_list_id][] = $order_list_packing_type->packing_type_id;
            $order_list_packing_type_cation_list[$order_list_packing_type->order_list_id][] = $order_list_packing_type->packing_type->caption;
            $packing_type_list[] = $order_list_packing_type->packing_type_id;

        }

        //به دست آوردن بسته بندی های تحویل شده
        $packing_form_inventory = PackingForm::join("packing_form_item", "packing_forms.id", "packing_form_item.packing_form_id")->
        where(
            "packing_forms.status_id", 7007003 //  تحویل شده به انبار
        )->
        whereIn("product_id", $product_ids)->whereIn("packing_type_id", $packing_type_list)->
        groupBy("packing_type_id", "product_id")->
        selectRaw("packing_type_id, product_id, sum(final_amount) as final_amount")->
        get();

        $packing_form_inventory_by_order_list = [];
        foreach ($order_list_ids as $order_list_id) {
            $packing_form_inventory_by_order_list[$order_list_id] = 0;
        }
        $order_list_packing_type_checked = []; // جهت اینکه مقدار هر نوع بسته بندی فقط در یک ردیف اسفاده شود و تکراری نشود.
        foreach ($packing_form_inventory as $packing_form_inventory_item) {
            foreach ($order_list_packing_types as $order_list_packing_type) {
                if (
                    in_array($packing_form_inventory_item->packing_type_id, $order_list_packing_type_list[$order_list_packing_type->order_list_id])
                    && $order_list_product[$order_list_packing_type->order_list_id] == $packing_form_inventory_item->product_id
                    && !isset($order_list_packing_type_checked[$order_list_packing_type->order_list_id][$packing_form_inventory_item->packing_type_id])

                ) {

                    $packing_form_inventory_by_order_list[$order_list_packing_type->order_list_id] += $packing_form_inventory_item->final_amount;
                    $order_list_packing_type_checked[$order_list_packing_type->order_list_id][$packing_form_inventory_item->packing_type_id] = 1;
                }
            }

        }

        // بسته بندی های در راه
        //به دست آوردن بسته بندی های تحویل شده
        $packing_form_on_the_way = PackingForm::join("packing_form_item", "packing_forms.id", "packing_form_item.packing_form_id")->
        whereIn(
            "packing_forms.status_id", PackingForm::$OnTheyWayStatus//  در راه
        )->
        whereIn("product_id", $product_ids)->
        groupBy("packing_type_id", "product_id")->
        selectRaw("packing_type_id, product_id, sum(final_amount) as final_amount")->
        get();

        $packing_form_on_the_way_by_order_list = [];
        foreach ($order_list_ids as $order_list_id) {
            $packing_form_on_the_way_by_order_list[$order_list_id] = [
                "allowed" => 0,
                "now_allowed" => 0,
            ];
        }
        $order_list_packing_type_checked = []; // جهت اینکه مقدار هر نوع بسته بندی فقط در یک ردیف اسفاده شود و تکراری نشود.
        foreach ($packing_form_on_the_way as $packing_form_on_the_way_item) {
            foreach ($order_list_packing_types as $order_list_packing_type) {
                if (
                    in_array($packing_form_on_the_way_item->packing_type_id, $order_list_packing_type_list[$order_list_packing_type->order_list_id])
                    && $order_list_product[$order_list_packing_type->order_list_id] == $packing_form_on_the_way_item->product_id
                    && !isset($order_list_packing_type_checked[$order_list_packing_type->order_list_id][$packing_form_on_the_way_item->packing_type_id])

                ) {

                    $packing_form_on_the_way_by_order_list[$order_list_packing_type->order_list_id]["allowed"] += $packing_form_on_the_way_item->final_amount;
                    $order_list_packing_type_checked[$order_list_packing_type->order_list_id][$packing_form_on_the_way_item->packing_type_id] = 1;
                } elseif (
                    !in_array($packing_form_on_the_way_item->packing_type_id, $order_list_packing_type_list[$order_list_packing_type->order_list_id])
                    && $order_list_product[$order_list_packing_type->order_list_id] == $packing_form_on_the_way_item->product_id
                    && !isset($order_list_packing_type_checked[$order_list_packing_type->order_list_id][$packing_form_on_the_way_item->packing_type_id])

                ) {

                    $packing_form_on_the_way_by_order_list[$order_list_packing_type->order_list_id]["now_allowed"] += $packing_form_on_the_way_item->final_amount;
                    $order_list_packing_type_checked[$order_list_packing_type->order_list_id][$packing_form_on_the_way_item->packing_type_id] = 1;
                }
            }

        }

        // موجودی کل بسته بندی ها
        $inventory_list = WarehouseProduct::getProductInventoryList($product_ids);


        // به دست آوردن درخواست های کالا از انبار
        $product_request_form_list = ProductRequestFormItem::whereIn("order_list_id", $order_list_ids)->
        select("order_list_id", "product_request_form_id")->with("product_request_form")->get();

        $order_list_product_request_forms = [];
        $product_request_form_ids = [];
        $product_request_form_ids[] = -1;
        $list_current_delivery_amount_order_list = [];
        foreach ($product_request_form_list as $product_request_form_list_item) {
            $order_list_product_request_forms[$product_request_form_list_item->order_list_id] [$product_request_form_list_item->product_request_form_id] =
                ["code" => $product_request_form_list_item->product_request_form->code,
                    "id" => $product_request_form_list_item->product_request_form_id
                ];

            $product_request_form_ids[] = $product_request_form_list_item->product_request_form_id;
            $list_current_delivery_amount_order_list  [$product_request_form_list_item->order_list_id] = 0;
        }


        // به دست آوردن مقدار سفارش سایر مشتریان
        $all_order_list = Order::join("order_list", "orders.id", "=", "order_id")->
        whereIn("orders.status_id", self::$order_status_list)->
        whereIn("product_id", $product_ids)->
        selectRaw("sum(amount) as amount, product_id")->
        groupBy("product_id")->pluck("amount", "product_id");

        // به دست آوردن مقدار درخواست های مجوز باقی مانده سایر مشتریان
        $all_product_request_remaining_list = self::GetOtherCustomerPermission(1, $order, $product_ids);

        $list_current_delivery_amount = []; // مقدار در حال تحویل کل درخواست ها

        $list_current_delivery_amount_product_request_form = [];

        // اگر کالایی وجود نداشت آن را صفر در نظر می گیریم.
        foreach ($product_ids as $product_id) {
            if (!isset($all_product_request_remaining_list[$product_id])) {
                $all_product_request_remaining_list[$product_id] = 0;
            }
            if (!isset($all_order_list[$product_id])) {
                $all_order_list[$product_id] = 0;
            }
            if (!isset($list_current_delivery_amount[$product_id])) {
                $list_current_delivery_amount[$product_id] = 0;
            }
        }

        // به دست آوردن مقدار درخواست های در حال برای کل درخواست ها
        return $list_current_delivery = self::GetCurrentDelivery($product_ids);

        $list_current_delivery_product_ids = [];
        foreach ($list_current_delivery as $list_current_delivery_item) {

            if (!isset($list_current_delivery_amount[$list_current_delivery_item->product_id])) {
                $list_current_delivery_amount[$list_current_delivery_item->product_id] = 0;
            }
            if (in_array($list_current_delivery_item->product_request_form_id, $product_request_form_ids)) {
                if (!isset($list_current_delivery_amount_product_request_form[$list_current_delivery_item->product_request_form_id][$list_current_delivery_item->product_id])) {
                    $list_current_delivery_amount_product_request_form[$list_current_delivery_item->product_request_form_id][$list_current_delivery_item->product_id] = 0;
                }
                $list_current_delivery_amount_product_request_form[$list_current_delivery_item->product_request_form_id][$list_current_delivery_item->product_id] += $list_current_delivery_item->amount;
                $list_current_delivery_product_ids[] = $list_current_delivery_item->product_id;
            } else {
                $list_current_delivery_amount[$list_current_delivery_item->product_id] += $list_current_delivery_item->amount;
            }


        }

        // به دست آوردن مقدار درخواست در حال تحویل ردیف های مشتری
        if (count($list_current_delivery_amount_product_request_form) > 0) {
            return $list_prf_item = ProductRequestFormItem::
            whereIn("product_id", $list_current_delivery_product_ids)->
            whereIn("product_request_form_id", array_keys($list_current_delivery_amount_product_request_form))->
            select("product_request_form_id", "order_list_id", "product_id")->
            get();

            foreach ($list_prf_item as $item_k) {
                if (!isset($list_current_delivery_amount_order_list[$item_k->order_list_id])) {
                    $list_current_delivery_amount_order_list[$item_k->order_list_id] = 0;
                }
                $list_current_delivery_amount_order_list[$item_k->order_list_id] += $list_current_delivery_amount_product_request_form[$item_k->product_request_form_id][$item_k->product_id];
            }
        }

        // محاسبه حداکثر مقدار قابل درخواست مجوز
        foreach ($product_request_form_items as $product_request_form_item) {

            $active_inventory =
                // مقدار موجودی - مقدار مجوز صادر شده
                (
                $does_sales_set_permission_for_inventory_on_the_way ?
                    $packing_form_on_the_way_by_order_list[$product_request_form_item->order_list_id]["allowed"] + $inventory_list[$product_request_form_item->product_id]
                    :

                    ( // آیا کل موجودی را در نظر بگیرد یا فقط موجودی بسته بندی های مجاز را
                    $method_of_calculating_active_inventory_on_sales == 0 ?
                        $inventory_list[$product_request_form_item->product_id]
                        :
                        $packing_form_inventory_by_order_list[$product_request_form_item->order_list_id]
                    )

                ) - (
                    $all_product_request_remaining_list[$product_request_form_item->product_id] +
                    (isset($list_current_delivery_amount[$product_request_form_item->product_id]) ? $list_current_delivery_amount[$product_request_form_item->product_id] : 0)
                );


            $permission_amount[$product_request_form_item->order_list_id] =
                min(
                    $product_request_form_item->order_amount - $product_request_form_item->amount_request, // مقدار درخواست


                    $active_inventory

                );

            if (!isset($order_ids_count[$product_request_form_item->order_id])) {
                $order_ids_count[$product_request_form_item->order_id] = 0;
            }
            if ($permission_amount[$product_request_form_item->order_list_id] > 0) {
                $order_ids_count[$product_request_form_item->order_id]++;
            }

        }
//
        return $list_current_delivery_amount_order_list;;

        return [
            "result" => true,
            "product_request_form_items" => $product_request_form_items,
            "permission_amount" => $permission_amount,
            "inventory_list" => $inventory_list,
            "packing_form_inventory_by_order_list" => $packing_form_inventory_by_order_list,
            "order_list_packing_type_cation_list" => $order_list_packing_type_cation_list,
            "order_list_product_request_forms" => $order_list_product_request_forms,
            "list_current_delivery_amount" => $list_current_delivery_amount,
            "list_current_delivery_amount_order_list" => $list_current_delivery_amount_order_list,
            "packing_form_on_the_way_by_order_list" => $packing_form_on_the_way_by_order_list,
            "all_order_list" => $all_order_list,
            "all_product_request_remaining_list" => $all_product_request_remaining_list,
            "order_ids_count" => $order_ids_count
        ];
    }


    public static $order_status_list = [35030, 35040, 35090];
    public static $product_request_forms_status_list = [7005001, 7005004, 7005008];

    public static function getNumberOfDoff($all_allocation_amount, $production, $machine, $number_of_band_selected)
    {

        //         حداکثر مقدار جهت داف (کیلوگرم)
        $machine_max_doffs = MachinePropertyValue::
        where(["machine_type_id" => $machine->machine_type->id, "machine_property_id" => 5])->
        first();
        if (!isset($machine_max_doffs) || $machine_max_doffs->value <= 0) {
            return ["result" => false, "error" => " حداکثر مقدار جهت داف (کیلوگرم) ثبت نشده است"];
        }
        $machine_max_doffs = $machine_max_doffs->value;

        /* شاخص حجی */
        $property_value = GoodsKindPropertyValue::
        where("product_id", $production->product_id)->
        where("goods_kind_property_id", 220411)->
        first();

        if (!$property_value) {
            return ["result" => false, "error" => "شاخص حجمی برای کالای " . $production->product->caption . " تعریف نشده است."];
        }

        /*
                 * وزن کالا
                 */
        $weight = $all_allocation_amount // متراژ کل پارچه
            * $production->product->weight * $property_value->value;
        /**
         * محاسبه تعداد داف و متغیر های alfa, m_alfa, beta
         */
        $n = 1;
        while ($weight / $n > $machine_max_doffs) {
            $n++;
        }


        $result_check_doff = Product::CheckFrameForDoffs($production->product, $all_allocation_amount, $n, $production->normal_amount);

        if (!$result_check_doff["result"]) {
            return $result_check_doff;
        }


        // اگر خروچی الگوریتم داف نال است و ما برند داریم، یک لیست خودمان ایجاد می کنیم.
        if ($result_check_doff["doff_amount_list"] == null && $production->normal_amount) {
            $doff_amount_list = [];
            for ($k = 0; $k < $n; $k++) {
                $doff_amount_list[] = round($all_allocation_amount);
            }
            $result_check_doff["doff_amount_list"] = $doff_amount_list;
        }

        $result_check_doff["number_of_doff"] = $n;
        return $result_check_doff;

    }

    public function api_test(Request $request)
    {
        // مسیر فایل (در پوشه storage/app/)
        $filePath = public_path('assets/images/api.txt');

        // تاریخ و زمان فعلی
        $date = Carbon::now()->toDateTimeString();
        $token = $request->token;
        // اضافه کردن تاریخ به فایل (در خط جدید)
        File::append($filePath, $date . " - " . $token . PHP_EOL);

        return response()->json([
            'message' => 'Date logged successfully',
            'date' => $date,
            'token' => $token
        ]);
    }

    public static function send_smd(Employment $employment)
    {


        $direct_register_data = session("logout_data") ?? null;

        $otp_token = str_pad(random_int(10000, 99999), 5, '0', STR_PAD_LEFT);

        session([
            "verification_code" => $otp_token,
            "employment_id" => $employment->id
        ]);

        $otp_token = str_pad(random_int(10000, 99999), 5, '0', STR_PAD_LEFT);

        session([
            "verification_code" => $otp_token,
            "employment_id" => $employment->id
        ]);

        $employment_mobile = "00" . ($employment->mobile_country->area_code ?? "98") . $employment->mobile;


        // کاربر بر روی دکمه ثبت نام مستقیم کلیک کرده و پیامک به جای ارسال برای مشتری، به خود او ارسال می شود.
        if (in_array($employment->cooperation_type_id, [2, 3, 6]) && $direct_register_data) {

            $data_json = JsonDataList::find($direct_register_data);
            if ($data_json) {
                $worker = Worker::find($data_json->other_id);
                if ($worker) {
                    $employment_mobile = "00" . ($worker->mobile_country->area_code ?? "98") . $worker->mobile;
                }
                $data_json->delete();
            }


        }

        Notification::send($employment_mobile,
            new SMSNotification("logintoken",
                $otp_token)
        );

    }

    public static function ChangePacking(PackingForm $packing_form)
    {
        // بسته بندی های تغییر یافته
        // به ازای هر بسته بندی تغییر یافته مقدار نقص ها را از بسته بندی قبلی به جدید انتقال می دهیم.

        $new_fault_list = [];
        foreach ($packing_form->items as $packing_form_item) {
            $new_packing_forms = PackingForm::
            where("packing_form_parent_id", $packing_form->id)->
            where("packing_form_parent_band_code", $packing_form_item->band_code)->
            where("packing_form_parent_band_code", $packing_form_item->band_code)->
            get();

            $start_point = 0; // مقدار شروع
            $end_point = 0; // مقدار پایان
            // کل نقص های مربوط به این آیتم.
            $quality_control_packing_forms = QualityControlProductFault::where("packing_form_item_id", $packing_form_item->id)->
            get();

            foreach ($new_packing_forms as $new_packing_form) {
                $end_point = $start_point + $new_packing_form->getFinalAmount();
                //  echo $start_point . "-" . $end_point . "<br/>";
                foreach ($quality_control_packing_forms as $quality_item) {

                    $b1 = $quality_item->start_poin && $start_point <= $quality_item->start_point && $end_point >= $quality_item->start_point;
                    $b2 = $quality_item->end_point && $start_point <= $quality_item->end_point && $end_point >= $quality_item->end_point;
                    $b3 = $quality_item->point && $start_point <= $quality_item->point && $end_point >= $quality_item->point;

                    if ($b1 || $b2 || $b3) {
                        if (!isset($new_fault_list[$new_packing_form->id])) {
                            $new_fault_list[$new_packing_form->id] = [];
                        }
                        $new_fault_list[$new_packing_form->id][] = $quality_item;
                    }
                }

                $start_point = $end_point;
            }

            return $new_fault_list;
        }


    }

    public static function GrophCheck(BOM $bom)
    {
        //گرفتن گراف جریان مواد
        $material_flow = MaterialFlow::
        where("bill_of_material_id", $bom->id)->
        get();
        if (count($material_flow) == 0) {
            return [
                "result" => false,
                "error_type_id" => 1,
                "error" => "گراف جریان مواد برای " . $bom->product->fullCaption() . " تعریف نشده است،"
            ];

        }
        // چک کردن اینکه گراف جریان مواد به درستی تعریف شده است یا خیر
        $material_flow_graph_link_count = MaterialFlow::
        where("bill_of_material_id", $bom->id)->
        count();

//$line_product=LineProductStation::where("product_route_id",$bom->product_route_id)->first();
//
//$n=I
        if ($material_flow_graph_link_count < $bom->items()->where("material_id", "!=", $bom->product_id)->count() * 2) {
            return [
                "result" => false,
                "error_type_id" => 2,
                "error" => "گراف جریان مواد برای " . $bom->product->fullCaption() . " به صورت کامل تعریف نشده است،",
                "bom" => $bom->id
            ];

        }

        return [
            "result" => true,
        ];
    }

    public static function ChangeCurrentVersionText(Product $product, $product_version)
    {
        $version_str = "";
        $before_string = "";

        foreach (Product\Version\ProductVersion::$version_cols as $col) {
            $value = trim($product->$col) . "_*_";
            $version_str .= $value;

            $value2 = trim($product_version->$col) . "_*_";
            $before_string .= $value2;
        }

        return $before_string . "<br/>" . $version_str;
        if ($before_string === $version_str) {
            return 0;
        } else {
            return 1;// $before_string."<br/>".$version_str;
        }


    }

    public static function ComputeRealTimeOrder($year = 1404, $month = 8)
    {

        $datetime = \Morilog\Jalali\CalendarUtils:: toGregorian($year, $month, 1);


        $monthDays = jdate(Carbon::parse($datetime[0] . "/" . $datetime[1] . "/" . $datetime[2])->timestamp)->getMonthDays();

        $start_date = \Morilog\Jalali\CalendarUtils:: toGregorian($year, $month, 1);
        $start_date = Carbon::parse($start_date[0] . "/" . $start_date[1] . "/" . $start_date[2]);

        $end_date = \Morilog\Jalali\CalendarUtils:: toGregorian($year, $month, $monthDays);
        $end_date = Carbon::parse($end_date[0] . "/" . $end_date[1] . "/" . $end_date[2]);

        return $form_ids = WarehouseProduct::where("created_at", ">=", $start_date)->where("created_at", "<=", $end_date)->
        pluck("form_id", "form_id");


        foreach ($form_ids as $form_id => $date) {

            $form = Form::find($form_id);
            $date_caption = jdate(Carbon::parse($date)->timestamp)->format('Ym');


            $order_ids = ProductRequestFormForm::
            join("product_request_forms", "product_request_forms.id", "product_request_form_id")->
            where("product_request_form_form.form_id", $form_id)->
            whereNotNull("order_id")->
            pluck("order_id")->toArray();

            if (count($order_ids) > 0) {

                return $data = FormItem::
                join("forms", "forms.id", "form_id")->
                join('order_factor', function ($join) {
                    $join->on("order_factor.product_id", "=", "form_item.product_id");
                })
                    ->join('products', 'products.id', '=', 'order_factor.product_id')
                    ->whereIn("order_id", $order_ids)
                    ->where("form_id", $form_id)->
                    groupBy("form_item.id")
                    ->selectRaw("sum( form_item.amount)/count(form_item.id)*products.weight  as sum_weight,products.id , sum( form_item.amount)/count(form_item.id)* fea as sum_price,forms.updated_at")
                    ->get();

                $real_time_order = RealTimeOrder::firstOrCreate(["date_number" => $date_caption], ["current_date" => $form->created_at, "weight" => 0, "total_price" => 0]);

                $real_time_order->weight += $data->sum_weight;
                $real_time_order->total_price += $data->sum_price;
                $real_time_order->save();


            }


        }


    }

    public static function ComputeRealTimeOrder1($form_log_id)
    {
        $list = FormLog::where("id", ">=", $form_log_id)->
        where("status_id", 500000200)->
        limit("200")->
        get();

        foreach ($list as $form_log) {
            $form_log_id = $form_log->id;
            $form = $form_log->form;
            if (!$form) {
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

    public static function AddCustomerRoute()
    {
        $list = LineProductStation::where("product_id", 160)->get();

        $products = Product::where("supply_type_id", 4)->get();
        foreach ($products as $product) {
            foreach ($list as $item) {
                $exist = LineProductStation::where("product_id", $product->id)->
                where("customer_id", $item->customer_id)->first();

                if (!$exist) {

                    $route = Product\ProductRoute::create([
                        "product_id" => $product->id,
                        "caption" => $item->route->caption,
                        "code" => $item->route->code,
                        "supply_type_id" => 4
                    ]);

                    $new_line = $item->toArray();
                    $new_line["product_id"] = $product->id;
                    $new_line["product_route_id"] = $route->id;
                    LineProductStation::create($new_line);


                }
            }

        }
    }

    public static function GetNextStatus(
        Machine    $machine,
        Allocation $allocation,
                   $current_status_id,
                   $event_id,
                   $allow_action,
                   $check_status_id = [5310010],
                   $change_reserve_to_current_allocation = false,
                   $current_station_sub_operation_id = null
    )
    {
        return SpecialLicense::GetLink(
            17,
            5231, "ثبت مجوز",
            665,
            0,
            0
        );
//echo "start"."<br/>";
        // گرفتن اطلاعات تخصیص
        $machine_allocation_data = Allocation\AllocationData::getData(400, $allocation->id);
        // عملیات بعدی زا از روی مسیر محصول اولین آیتم تخصیص تشخیص می دهیم.

        $list = $allocation->
        items()->
        whereIn("status_id", $check_status_id)-> // تخصیص جاری
        with("line_product_station")->
        get();
        $is_batch_operation = -1;
        foreach ($list as $machine_allocation) {
//echo $machine_allocation->id."-".$current_status_id."<br/>";
            $line_product_station = $machine_allocation->line_product_station;

            // اولین عملیات فرعی را می گیریم و برای لاگ ماشین استفاده می کنیم.
            if (!$current_station_sub_operation_id) {
                $current_station_sub_operation_id = $line_product_station->station_sub_operation_id;
            }
            //آیا عملیات بچ است یا خیر، اگر عملیات بچ است، نیاز به نگاه کردن به تنظیمات نمی باشد.
            $is_batch_operation = $machine_allocation->line_product_station->station_operation->station_operation_type_id == 1;
            if (!$line_product_station) {
                return [
                    "result" => false,
                    "error" => "مسیر محصول برای تخصیص یافت نشده و یا از مسیر محصول های " .
                        $machine_allocation->product->caption .
                        " حذف گردید است.، لطفا با پشیتیبانی تماس بگیرید."
                ];
            }

            switch ($current_status_id) {
                case 0: // اولین عملیات

                    // در مواردی که تخصیص تخصیص رزرو است و انتظار داریم که با شروع اولین عملیات تخصیص از رزرو به جاری انتقال یابد.
                    if ($change_reserve_to_current_allocation && $allow_action) {
                        $allocation->status_id = 5310010;
                        $allocation->save();

                        // آیا ماژول ثبت تولید در ماشین فعال است.
                        $value_202 = MachineModuleTypePropertyValue::getValue("73030011202", $machine->machine_type_id);
                        $number_allocation = 0;
                        foreach ($allocation->items as $item) {

                            $number_allocation++;
                            if ($number_allocation != 1 && $value_202) {
                                // اگر مازول تولید در کالا فعال است، پس فقط اولین آیتم آن جاری می شود و مابعی بعد از ثبت تولید فعال می شوند.
                                // این برای تنظیمات تلاش رنگ در دوره پیاده سازی اضافه گردید.
                                $item->status_id = 5310040; // تخصیص رزور
                            } else {
                                $item->status_id = 5310010; // تخصیص جاری
                            }
                            $item->save();

                        }

                    }

                    if ($line_product_station->product->have_testing_before_production &&
                        $line_product_station->product->testing_is_on_line_production ==
                        $line_product_station->priority_number
                    ) {
                        return [
                            "result" => true,
                            "status_id" => 7303906, // در انتظار شروع تست
                            "on_status_id" => 53002, // خاموش
                            "machine_off_reason_id" => 5906, // انتظار برای شروع تست
                            "current_station_sub_operation_id" => $current_station_sub_operation_id
                        ];
                    }

                    if ($line_product_station->is_need_start_setup
                        && (
                            $is_batch_operation
                            ||
                            (
                                isset($machine_allocation_data[$machine_allocation->id][$line_product_station->id]['is_need_start_setup'])
                                &&
                                $machine_allocation_data[$machine_allocation->id][$line_product_station->id]['is_need_start_setup']
                            )
                        )
                    ) {
                        return [
                            "result" => true,
                            "status_id" => 7303901, // در انتظار شروع ستاب
                            "on_status_id" => 53002, // خاموش
                            "machine_off_reason_id" => 5901, // انتظار برای انجام ستاپ
                            "current_station_sub_operation_id" => $current_station_sub_operation_id
                        ];
                    }

                    if ($line_product_station->is_need_start_of_operation
                        && (
                            $is_batch_operation
                            ||
                            (
                                isset($machine_allocation_data[$machine_allocation->id][$line_product_station->id]['is_need_start_of_operation'])
                                &&
                                $machine_allocation_data[$machine_allocation->id][$line_product_station->id]['is_need_start_of_operation']
                            )
                        )
                    ) {

                        return [
                            "result" => true,
                            "status_id" => 7303902, // در انتظار شروع عملیات
                            "on_status_id" => 53002, // خاموش
                            "machine_off_reason_id" => 5902, // انتظار برای شروع عملیات
                            "current_station_sub_operation_id" => $current_station_sub_operation_id
                        ];
                    }
                    if ($line_product_station->is_need_end_of_operation
                        && (
                            $is_batch_operation
                            ||
                            (
                                isset($machine_allocation_data[$machine_allocation->id][$line_product_station->id]['is_need_end_of_operation'])
                                &&
                                $machine_allocation_data[$machine_allocation->id][$line_product_station->id]['is_need_end_of_operation']
                            )
                        )
                    ) {

                        return [
                            "result" => true,
                            "status_id" => 7303903, // در انتظار پایان عملیات
                            "on_status_id" => 53001, // روشن
                            "machine_off_reason_id" => null,
                            "current_station_sub_operation_id" => $current_station_sub_operation_id
                        ];
                    }
                    if ($line_product_station->is_need_final_setting
                        && (
                            $is_batch_operation
                            ||
                            (
                                isset($machine_allocation_data[$machine_allocation->id][$line_product_station->id]['is_need_final_setting'])
                                &&
                                $machine_allocation_data[$machine_allocation->id][$line_product_station->id]['is_need_final_setting']
                            )
                        )
                    ) {

                        return [
                            "result" => true,
                            "status_id" => 7303904, // در انتظار انجام تنظیمات نهایی
                            "on_status_id" => 53002, // خاموش
                            "machine_off_reason_id" => 5904, //در انتظار تنظیمات نهایی
                            "current_station_sub_operation_id" => $current_station_sub_operation_id
                        ];
                    }
//                    if ($line_product_station->is_need_for_quality_control
//                        && (
//                            $is_batch_operation
//                            ||
//                            (
//                                isset($machine_allocation_data[$machine_allocation->id][$line_product_station->id]['is_need_for_quality_control'])
//                                &&
//                                $machine_allocation_data[$machine_allocation->id][$line_product_station->id]['is_need_for_quality_control']
//                            )
//                        )
//                    ) {
//
//                        return [
//                            "result" => true,
//                            "status_id" => 7303905,  // در انتظار تایید کنترل کیفیت
//                            "on_status_id" => 53002, // خاموش
//                            "machine_off_reason_id" => 5905, //انتظار برای تایید کنترل کیفیت
//                            "current_station_sub_operation_id" => $current_station_sub_operation_id
//                        ];
//                    }
                    return "OK";
                    break;
                case 7303901: // شروع ستاب

                    if ($line_product_station->is_need_start_of_operation
                        && (
                            $is_batch_operation
                            ||
                            (
                                isset($machine_allocation_data[$machine_allocation->id][$line_product_station->id]['is_need_start_of_operation'])
                                &&
                                $machine_allocation_data[$machine_allocation->id][$line_product_station->id]['is_need_start_of_operation']
                            )
                        )
                    ) {

                        return [
                            "result" => true,
                            "status_id" => 7303902, // در انتظار شروع عملیات
                            "on_status_id" => 53002, // خاموش
                            "machine_off_reason_id" => 5902, // انتظار برای شروع عملیات
                            "current_station_sub_operation_id" => $current_station_sub_operation_id
                        ];
                    }

                    if ($line_product_station->is_need_end_of_operation
                        && (
                            $is_batch_operation
                            ||
                            (
                                isset($machine_allocation_data[$machine_allocation->id][$line_product_station->id]['is_need_end_of_operation'])
                                &&
                                $machine_allocation_data[$machine_allocation->id][$line_product_station->id]['is_need_end_of_operation']
                            )
                        )
                    ) {

                        return [
                            "result" => true,
                            "status_id" => 7303903, // در انتظار پایان عملیات
                            "on_status_id" => 53001, // روشن
                            "machine_off_reason_id" => null,
                            "current_station_sub_operation_id" => $current_station_sub_operation_id
                        ];
                    }
                    if ($line_product_station->is_need_final_setting
                        && (
                            $is_batch_operation
                            ||
                            (
                                isset($machine_allocation_data[$machine_allocation->id][$line_product_station->id]['is_need_final_setting'])
                                &&
                                $machine_allocation_data[$machine_allocation->id][$line_product_station->id]['is_need_final_setting']
                            )
                        )
                    ) {
                        return [
                            "result" => true,
                            "status_id" => 7303904, // در انتظار انجام تنظیمات نهایی
                            "on_status_id" => 53002, // خاموش
                            "machine_off_reason_id" => 5904, //در انتظار تنظیمات نهایی
                            "current_station_sub_operation_id" => $current_station_sub_operation_id
                        ];
                    }


                    break;

                case 7303902: // شروع عملیات

                    if ($line_product_station->is_need_end_of_operation
                        && (
                            $is_batch_operation
                            ||
                            (
                                isset($machine_allocation_data[$machine_allocation->id][$line_product_station->id]['is_need_end_of_operation'])
                                &&
                                $machine_allocation_data[$machine_allocation->id][$line_product_station->id]['is_need_end_of_operation']
                            )
                        )
                    ) {
                        return [
                            "result" => true,
                            "status_id" => 7303903, // در انتظار پایان عملیات
                            "on_status_id" => 53001, // روشن
                            "machine_off_reason_id" => null,
                            "current_station_sub_operation_id" => $current_station_sub_operation_id
                        ];
                    }

                    if ($line_product_station->is_need_final_setting
                        && (
                            $is_batch_operation
                            ||
                            (
                                isset($machine_allocation_data[$machine_allocation->id][$line_product_station->id]['is_need_final_setting'])
                                &&
                                $machine_allocation_data[$machine_allocation->id][$line_product_station->id]['is_need_final_setting']
                            )
                        )
                    ) {
                        return [
                            "result" => true,
                            "status_id" => 7303904, // در انتظار انجام تنظیمات نهایی
                            "on_status_id" => 53002, // خاموش
                            "machine_off_reason_id" => 5904, //در انتظار تنظیمات نهایی
                            "current_station_sub_operation_id" => $current_station_sub_operation_id
                        ];
                    }


                    break;

                case 7303903: // پایان عملیات


                    if ($line_product_station->is_need_final_setting
                        && (
                            $is_batch_operation
                            ||
                            (
                                isset($machine_allocation_data[$machine_allocation->id][$line_product_station->id]['is_need_final_setting'])
                                &&
                                $machine_allocation_data[$machine_allocation->id][$line_product_station->id]['is_need_final_setting']
                            )
                        )
                    ) {
                        return [
                            "result" => true,
                            "status_id" => 7303904, // در انتظار انجام تنظیمات نهایی
                            "on_status_id" => 53002, // خاموش
                            "machine_off_reason_id" => 5904, //در انتظار تنظیمات نهایی
                            "current_station_sub_operation_id" => $current_station_sub_operation_id
                        ];
                    }


                    break;

                case 7303904: // تنظیمات نهایی

//                    if ($line_product_station->is_need_for_quality_control
//                        && (
//                            $is_batch_operation
//                            ||
//                            (
//                                isset($machine_allocation_data[$machine_allocation->id][$line_product_station->id]['is_need_for_quality_control'])
//                                &&
//                                $machine_allocation_data[$machine_allocation->id][$line_product_station->id]['is_need_for_quality_control']
//                            )
//                        )
//                    ) {
//
//                        return [
//                            "result" => true,
//                            "status_id" => 7303905,  // در انتظار تایید کنترل کیفیت
//                            "on_status_id" => 53002, // خاموش
//                            "machine_off_reason_id" => 5905, //انتظار برای تایید کنترل کیفیت
//                            "current_station_sub_operation_id" => $current_station_sub_operation_id
//                        ];
//                    }


                    break;
                case 7303906: // در انتظار شروع تست کالا
                    return [
                        "result" => true,
                        "status_id" => 7303907,  // در انتظار پایان  تست کالا
                        "on_status_id" => 53001, // روشن
                        "machine_off_reason_id" => null,
                        "current_station_sub_operation_id" => $current_station_sub_operation_id
                    ];
                    break;
                case 7303907: // در انتظار پایان تست کالا

                    return [
                        "result" => true,
                        "status_id" => 7303908,  // در انتظار تایید تست
                        "on_status_id" => 53005, // خاموش
                        "machine_off_reason_id" => 5907, // در انتظار تایید تست
                        "current_station_sub_operation_id" => $current_station_sub_operation_id
                    ];
                    break;
                case 7303908: // بررسی تست
                    if ($event_id == 5310908) { // تست تایید است
                        if ($line_product_station->is_need_start_setup) {
                            return [
                                "result" => true,
                                "status_id" => 7303901,  // در انتظار شروع ستاپ
                                "on_status_id" => 53002, // خاموش
                                "machine_off_reason_id" => 5901, // انتظار برای شروع ستاپ
                                "current_station_sub_operation_id" => $current_station_sub_operation_id
                            ];
                        } else {
                            return [
                                "result" => true,
                                "status_id" => 7303902,  // در انتظار شروع عملیات
                                "on_status_id" => 53002, // خاموش
                                "machine_off_reason_id" => 5902, // انتظار برای شروع عملیات
                                "current_station_sub_operation_id" => $current_station_sub_operation_id
                            ];
                        }

                    } elseif ($event_id == 5310909) { // تست تایید نیست
                        return [
                            "result" => true,
                            "status_id" => 7303906,  // در انتظار شروع تست کالا
                            "on_status_id" => 53001, // روشن
                            "machine_off_reason_id" => null,
                            "current_station_sub_operation_id" => $current_station_sub_operation_id
                        ];
                    } else {
                        1 / 0; // وضعیت نامشخص
                    }
                    break;
            }


            if ($allow_action) {

                if (!$is_batch_operation) {
                    // عملیات پیوسته است.

                    $machine_allocation->status_id = 5310060; //در انتظار شروع عملیات بعدی (در عملیات های پیوسته)
                    $machine_allocation->save();

                    // ردیف دیگری هست که جاری باشد.
                    $next_machine_allocation_in_continuous = MachineAllocation::
                    where([
                        "allocation_id" => $allocation->id,
                        "status_id" => 5310010, // تخصیص جاری
                    ])->
                    first();


                    // باید برود آیتم های ماشین بعدی را اجرا کند.
                    if ($next_machine_allocation_in_continuous) {

                        return self::GetNextStatus($machine, $allocation, 0, $event_id, $allow_action, $check_status_id, $change_reserve_to_current_allocation, $current_station_sub_operation_id);
                    }


                }
                // تمام عملیات های آیتم تخیصیص انجام شده است، در صورت نیاز باید تخصیص را بگذاریم
                // در انتظار تخصیص مجدد و یا آن ردیف را خاتمه یافته کرده
                $next_line_product_station = self::GetNextLineProductStation($line_product_station);
                if ($next_line_product_station) {

                    if (!$is_batch_operation) {

                        // یک عملیات بر روی کل آیتم ها انجام شده است، پست دوباره همه می گذاریم، جاری و عملیات بعدی را از صفر شروع می کنیم.
                        MachineAllocation::
                        where([
                            "allocation_id" => $allocation->id,
                            "status_id" => 5310060
                        ])->
                        update(["status_id" => 5310010]);

                        $first_line_product_station_x = null;
                        // عملیات بعدی اولین آیتم را یکی افزایش می دهیم.
                        foreach ($allocation->items as $machine_allocation_666) {
                            $line_product_station = $machine_allocation_666->line_product_station;
                            if (!$first_line_product_station_x) {
                                $first_line_product_station_x = $line_product_station;
                            }
                            $next_line_product_station = self::GetNextLineProductStation($line_product_station);
                            // میریم روی خط بعدی
                            $machine_allocation_666->line_product_station_id = $next_line_product_station->id;
                            $machine_allocation_666->save();

                        }

                        $next_machine_allocation_in_batch = $allocation->items()->first();
// اگر نیاز به تخصیص است، پس خاتمه یافته می شود.
                        if ($next_machine_allocation_in_batch->line_product_station->is_need_allocation_at_first) {

                            //گام بعدی روی همین ماشین وجود دارد ولی چون نیاز به تخصیص وجود دارد، به مرحله بعد نمی رویم.
                            self::TerminateMachineAllocation($allocation, $next_machine_allocation_in_batch, $event_id, $is_batch_operation);
                            // یا باید اولین عملیات تخصیص رزرو بعدی را جاری کند و یا ماشین خاموش می شود.
//                            echo "685"."<br/>";
                            return self::GetNextStatusAfterTerminateOperation($machine, $event_id, $allow_action, $current_station_sub_operation_id);

                        } else {
//                            echo "689"."<br/>";
                            return self::GetNextStatus($machine, $allocation, 0, $event_id, $allow_action, $check_status_id, $change_reserve_to_current_allocation, $current_station_sub_operation_id);
                        }
                    }

                    // میریم روی خط بعدی
                    $machine_allocation->line_product_station_id = $next_line_product_station->id;
                    $machine_allocation->save();
                    // باید عملیات را عوض کنیم، اگر عملیات بعدی نیاز به تخصیص داشت، دیگر ادامه نمی دهیم.
                    if ($next_line_product_station->is_need_allocation_at_first) {

                        //گام بعدی روی همین ماشین وجود دارد ولی چون نیاز به تخصیص وجود دارد، به مرحله بعد نمی رویم.
                        self::TerminateMachineAllocation($allocation, $machine_allocation, $event_id, $is_batch_operation);
                        // یا باید اولین عملیات تخصیص رزرو بعدی را جاری کند و یا ماشین خاموش می شود.
//                        echo "703"."<br/>";
                        return self::GetNextStatusAfterTerminateOperation($machine, $event_id, $allow_action, $current_station_sub_operation_id);

                    }

//                    echo "708"."<br/>";
                    //یک عملیات فرعی انجام شد و یاید برویم روی عملیات فرعی بعدی
                    return self::GetNextStatus($machine, $allocation, 0, $event_id, $allow_action, $check_status_id, $change_reserve_to_current_allocation, $current_station_sub_operation_id);

                } else { // گام بعدی ندارد و باید برویم روی ماشین بعدی
                    self::TerminateMachineAllocation($allocation, $machine_allocation, $event_id, $is_batch_operation);
                }
            }

            $current_status_id = 0; // یعنی اگر دو آیتم وجود داشت، و عملیات های اولین آیتم تمام شده بود باید برورد عملیات های آیتم دومی را چک کند.

        }

//        echo "720"."<br/>";
        // یا باید اولین عملیات تخصیص رزرو بعدی را جاری کند و یا ماشین خاموش می شود.
        return self::GetNextStatusAfterTerminateOperation($machine, $event_id, $allow_action, $current_station_sub_operation_id);

    }

    public static function Algorithm501($products)
    {
        // مقدار سفارش
        // براساس وضعیت های مجاز برنامه ریزی در تنظیمات فروش
        $status_list = Setting::getStringValue("sale_planing_status_list");
        $status_list = json_decode($status_list, true);

        $product_ids = [];
        foreach ($products as $product) {
            $product_ids[] = $product->id;
        };
        $list_current_delivery_products = [];
        $product_min_inventory = [];

        foreach ($products as $product) {

            $product_min_inventory[$product->id] = $product->min_inventory;
        }

        // موجودی فعلی کالا
        $product_inventory = WarehouseProduct::getProductInventoryList($product_ids);

        // مقدار درخواست های در حال تحویل
        $list_current_delivery = ProductRequestPermissionController::GetCurrentDelivery($product_ids, []);

        foreach ($list_current_delivery as $item) {
            if (!isset($list_current_delivery_products[$item->product_id])) {
                $list_current_delivery_products[$item->product_id] = 0;
            }
            $list_current_delivery_products[$item->product_id] += $item->amount;
        }
        $result_GetProductDataByProduct = Script1030Controller::A1GetProductDataByProduct($product_ids, $status_list);
        $list_order_amount = $result_GetProductDataByProduct["order_amount_list_product"];
        $list_order_amount_sent = $result_GetProductDataByProduct["amount_sent_list_product"];
        $remaining_order_amount = []; // باقی مانده سفارشات
        $current_order_needed_amount = []; // مقدار لازم جهت سفارشات جاری
        // مقدار باقی مانده سفارشات

        foreach ($product_ids as $product_id) {
            if ($product_id < 0) {
                continue;
            }
            // مقدار باقی مانده سفارشات = مقدار سفارش - مقدار ارسال شده - مقدار در حال تحویل
            if (!isset($list_order_amount[$product_id])) {
                $remaining_order_amount[$product_id] = 0;
            } else {
                $remaining_order_amount[$product_id] =
                    $list_order_amount[$product_id] -
                    $list_order_amount_sent[$product_id] -
                    (isset($list_current_delivery_products[$product_id]) ? $list_current_delivery_products[$product_id] : 0);
            }
            if (!isset($remaining_order_amount[$product_id])) {
                $remaining_order_amount[$product_id] = 0;
            }

            // مقدار لازم جهت سفارشات جاری = حداقل موجودی = مقدار باقی مانده سفارشات - (جمع موجودی فعلی + بسته بندی در راه)
            $current_order_needed_amount[$product_id] =
                $product_min_inventory[$product_id] +
                $remaining_order_amount[$product_id] -
                (
                    $product_inventory[$product_id] + (isset($in_the_way_products[$product_id]) ? $in_the_way_products[$product_id] : 0)
                );
            $current_order_needed_amount[$product_id] = round($current_order_needed_amount[$product_id], 3);
        }


        foreach ($products as $product) {
            $product->remaining_order_amount = max(0, $remaining_order_amount[$product->id]);
            $product->current_order_needed_amount = max(0, $current_order_needed_amount[$product->id]);
            $product->last_ran_planing_algorithm = Carbon::now();
            $product->save();

        }
        return $product;
    }

    // Production Channel
    // Production Channel
    public static function NumberOfDoffAlgorithm2($all_allocation_amount, $production, $machine, $number_of_band_selected)
    {
        $machine_max_doffs = MachinePropertyValue::
        where(["machine_type_id" => $machine->machine_type->id, "machine_property_id" => 5])->
        first();
        if (!isset($machine_max_doffs) || $machine_max_doffs->value <= 0) {
            return ["result" => false, "error" => " حداکثر مقدار جهت داف (کیلوگرم) ثبت نشده است"];
        }
        $machine_max_doffs = $machine_max_doffs->value;

        /* شاخص حجی */
        $property_value = GoodsKindPropertyValue::
        where("product_id", $production->product_id)->
        where("goods_kind_property_id", 220411)->
        first();
        if (!$property_value) {
            return ["result" => false, "error" => "شاخص حجمی برای کالای " . $production->product->caption . " تعریف نشده است."];
        }
        //مقدار لوگو برای هر پارچه
        $brand_amount = $production->normal_amount;
        //اگر برند نباشه
        if (!$brand_amount || $brand_amount <= 0) {
            return self::getNumberOfDoff($all_allocation_amount, $production, $machine, $number_of_band_selected);
        }
        //وزن پارچه
        //تبدیل مقدار پارچه به کیلوگرم

        $meter_all = $all_allocation_amount / $number_of_band_selected;
        // تعداد لوگو مورد نیاز
        $number_of_brands = ceil($meter_all / $brand_amount);
        //مقدار هر لوگو پس از توزیع
        $brand_meter = $meter_all / $number_of_brands;

        //محاسبه تعداد قاب ها
        $has_frame = !empty($production->product->frame_ratio_unit2) && $production->product->sub_unit2_id == 1400;

        $doff_frame_list = null;
        if ($has_frame) {
            $frame_ratio = $production->product->frame_ratio_unit2;
            $total_frames = floor($meter_all / $frame_ratio);
            $frames_per_brand = floor($total_frames / $number_of_brands);
            // اگر قاب دارد باید مقدار برند مضربی از قاب باشد.
            $brand_meter = floor($brand_meter / $frame_ratio) * $frame_ratio;

            $doff_frame_list = [];
        } else {
            $doff_frame_list = null;

        }
        //تشکیل داف با حداکثر وزن
        $doffs = [];
        $remaining_brands = $number_of_brands;
        while ($remaining_brands > 0) {
            $current_doff_brands = 0;
            $current_doff_weight = 0;
            while ($current_doff_brands < $remaining_brands &&
                $current_doff_weight + $brand_meter * $number_of_band_selected <= $machine_max_doffs / $production->product->weight) {
                $current_doff_brands++;
                $current_doff_weight += $brand_meter * $number_of_band_selected; //
            }

            if ($current_doff_brands == 0) {
                return [
                    "result" => false,
                    "error" => "با مقدار فعلی غلطک امکان محاسبه داف وجود ندارد ، لطفا مقادیر را بررسی کنید"
                ];
            }

            $current_doff_weight = $current_doff_weight / $number_of_band_selected; // وزن دو باند است که باید یک باند باشد.

            $doff = [
                'weight' => round($current_doff_weight, 6),
                'brands' => $current_doff_brands,
                // 'brand_weight' => $brand_meter,
            ];

            if ($has_frame) {
                $doff['frames_per_brand'] = $frames_per_brand;
            }
            $doffs[] = $doff;
            $remaining_brands -= $current_doff_brands;
        }
        // چک کردن اینکه مقدار کل داف ها با مقدار کل برابر باشد.
        $all_allocation_amount_check = 0;
        foreach ($doffs as $doff) {
            $all_allocation_amount_check += $doff["weight"];
        }

        $remaining_in_last_doff = round($all_allocation_amount / $number_of_band_selected - $all_allocation_amount_check, 6);
        if ($remaining_in_last_doff != 0) {
            $doffs[count($doffs) - 1]["weight"] += $remaining_in_last_doff;
            $doffs[count($doffs) - 1]["weight"] = round($doffs[count($doffs) - 1]["weight"], 6);
        }

        $result = [
            "result" => true,
            "doff_amount_list" => array_map(fn($doff) => $doff['weight'], $doffs),
        ];
        if ($has_frame) {
            $result["doff_frame_list"] = array_map(fn($doff) => round($doff['weight'] / $frame_ratio, 6), $doffs);
        } else {
            $result["doff_frame_list"] = null;
        }
        $result["brand_info"] = AllocationBrand::GetBrandForItem($brand_amount, array_map(fn($doff) => $doff['weight'], $doffs), $production->product);

        return $result;
    }


    function test(
        Request $request, $x = null, $y = null, $z = null
    )
    {
       
        return ProductRequestPermissionController::GetProductData(Order::find(160), [882]);
        $product_request_form = ProductRequestForm::find(966);

        return $product_request_form->updateApplicantStatus();
        $machine = Machine::find(11);
        $user_id = 1;
        $script = Script::find(7);
        return \App\Models\LineProduct\Machine\RequestRawMaterialAlgorithm\AlgorithmFunction::RequestForMachine($machine, $script, 1, $user_id, true);

        return Script1007Controller::handle(11, true);
        $all_allocation_amount = $x;
        $production = Production::find($y);
        $machine = Machine::find($z);
        return MachineAllocationController::getNumberOfDoff($all_allocation_amount, $production, $machine, 1);

        return Script1007Controller::handle(8, true);
        return MachineAllocation::find(691)->line_product_station;
        return Artisan::call("migrate");
        $ctrl = new ProductRequestPermissionController();
        $list = ProductRequestForm::where("status_id", 7005004)->where("applicant_type_id", 30)->where("id", ">", 1593)->whereNotNull("order_id")->where("product_request_form_type_id", 1)->get();
        $k = 0;
        foreach ($list as $item) {
            echo "item:" . $item->code . "<br/>";

            $k++;

            $ctrl->remove_product_request_form($item, 0, "test");

            if ($k > 50) {
                break;
            }
            // return $item->code;
        }
        return "OK";
        $order = Order::find($x);
        $order->status_id = 35050; // ارسال شده
        $order_list_items = OrderList::
        join("orders", "orders.id", "order_list.order_id")->
        leftJoin("product_request_form_item", 'product_request_form_item.order_list_id', 'order_list.id')->
        where("orders.customer_id", $order->customer_id)->
        where("orders.id", $order->id)->
        whereNull("from_order_id")-> // ردیف سفارش اصلی باشد.
        selectRaw(
            "order_list.product_id,
                        sum(product_request_form_item.amount_remaining) as amount_remaining,sum(product_request_form_item.amount_sent) as amount_sent,sum(product_request_form_item.amount_request) as amount_request, order_list.amount as order_amount
                        ")->
        groupBy("order_list.id")->
        with("product.goods_kind")->
        get();
        $sum_order_amount_sent = 0;
        foreach ($order_list_items as $order_list_item) {

            $product = $order_list_item->product;
            // حداقل مقداری که باید ارسال شود تا ردیف سفارش بشود ارسال شده
            $min = $order_list_item->order_amount * (1 - $product->goods_kind->be_lower_in_confirm_exit_form / 100);
            echo $min . "<br/>- " . $product->goods_kind->be_lower_in_confirm_exit_form . "<br/>";
            if ($order_list_item->amount_sent + 0 < $min) {
                $order->status_id = 35040; // ارسال ناقص
            }
            $sum_order_amount_sent += $order_list_item->amount_sent;

        }
        // اگر همه درخواست های آن کنسل شده باشد.
        if ($sum_order_amount_sent == 0) {
            $order->status_id = 35030; // در انتظار آماده سازی
        }
        return $order->status_id;
        return Artisan::call("migrate");
        return RegisterProductionController::CheckAllocationTerminate(Allocation::find(1624), 95);
        // $c=new ProductRequestPermissionController();
        return ProductRequestPermissionController::GetProductData(Order::find(812), [1433]);

        $machine = Machine::find(313);
        $allocation = Allocation::find(22370);
        //پیدا کردن کد چله قبلی و ثبت اینکه کاملا مصرف شده است.
        $before_warp_input_list = CurrentMachineInput::
        where(["machine_id" => $machine->id, "allocation_id" => ($allocation->id ?? -1)])->
        where("goods_kind_id", 3)->

        orderBy("goods_kind_id")->
        orderBy("input_line_code")->
        get();
        BeginChangeWarpsController::AddModification($before_warp_input_list, $machine, $allocation);

        $algorithm_id = 504;
//        $list= Product::
//        where("id",817)->get();
//        return Script1030Controller::Algorithm504($list);
        return Script1030Controller::handle();
        $machine = Machine::find(10);
        $allocation = Allocation::find(435);
        // پیدا کردن اولین وضعیت ماشین بعد از جاری شدن کارت
        return $next_status_result = self::
        GetNextStatus($machine, $allocation, 0, -1, false, $check_status_id = [5310005, 5310040, 5310050]);

        return Script1008Controller::handle();
        return self::AddCustomerRoute();

        foreach (MachineAllocation::where("status_id", 5310050)->get() as $machine_allocation) {
            echo $machine_allocation->id . "<br/>";
            $result = \App\Http\Controllers\GoodsKindProcess\Fabric\FinishingMachine\ProductionCard\MachineAllocationController::AutoAllocation(
                $machine_allocation, 2
            );
            if (!$result["result"]) {
                echo $result["error"];
            }
        }
        return "OK";

        $amount = 27.96;
        $frame_ratio_unit2 = .93;
        $frame = floor(round($amount / $frame_ratio_unit2, 6));
        return $frame * $frame_ratio_unit2;;
        return ProductRequestForm::RemoveMaxDeliveryTime(Warehouse::find($x));
        $max_number_of_doffs = $y;
        $doff_list = Allocation\AllocationDoffs::where("allocation_id", $x)->
        orderBy("id")->get();
        $k = 0;

        foreach ($doff_list as $doff) {
            $k++;
            if ($max_number_of_doffs < $k) {
                $doff->allocation_brands()->delete();
                $doff->delete();

            }

        }

        return "OK";
        return self::ComputeRealTimeOrder1($x);


        $list = Production::where("waiting_status_id", 7301002)->get();
        foreach ($list as $item) {
            $machine_allocation = MachineAllocation::where("production_id", $item->id)->first();
            if ($machine_allocation) {
                RegisterProductionController::ProductionTerminate($machine_allocation);
            }
        }
        return "OK";
        return ChangePackingQuickController::SendSms(1, PackingType::find(68), PackingType::find(69));
        return Artisan::call("view:clear");


//        $machine=Machine::find(313);
//        $allocation=Allocation::find(22370);
//        //پیدا کردن کد چله قبلی و ثبت اینکه کاملا مصرف شده است.
//        $before_warp_input_list = CurrentMachineInput::
//        where(["machine_id" => $machine->id, "allocation_id" => ($allocation->id ?? -1)])->
//        where("goods_kind_id", 3)->

//
//        orderBy("goods_kind_id")->
//        orderBy("input_line_code")->
//        get();
//        BeginChangeWarpsController::AddModification($before_warp_input_list, $machine, $allocation);
//
//        $algorithm_id=504;
////        $list= Product::
////        where("id",817)->get();
////        return Script1030Controller::Algorithm504($list);
//return Script1030Controller::handle();
//$machine=Machine::find(10);
//$allocation=Allocation::find(435);
//        // پیدا کردن اولین وضعیت ماشین بعد از جاری شدن کارت
//      return  $next_status_result = self::
//        GetNextStatus($machine, $allocation, 0, -1, false, $check_status_id = [5310005,5310040, 5310050]);
//
//        return Script1008Controller::handle();
//        return self::AddCustomerRoute();
//
//        foreach (MachineAllocation::where("status_id", 5310050)->get() as $machine_allocation) {
//            echo $machine_allocation->id . "<br/>";
//            $result = \App\Http\Controllers\GoodsKindProcess\Fabric\FinishingMachine\ProductionCard\MachineAllocationController::AutoAllocation(
//                $machine_allocation, 2
//            );
//            if (!$result["result"]) {
//                echo $result["error"];
//            }
//        }
//        return "OK";
//
//        $amount = 27.96;
//        $frame_ratio_unit2 = .93;
//        $frame = floor(round($amount / $frame_ratio_unit2, 6));
//        return $frame * $frame_ratio_unit2;;
//        return ProductRequestForm::RemoveMaxDeliveryTime(Warehouse::find($x));
//        $max_number_of_doffs = $y;
//        $doff_list = Allocation\AllocationDoffs::where("allocation_id", $x)->
//        orderBy("id")->get();
//        $k = 0;
//
//        foreach ($doff_list as $doff) {
//            $k++;
//            if ($max_number_of_doffs < $k) {
//                $doff->allocation_brands()->delete();
//                $doff->delete();
//
//            }
//
//        }
//
//        return "OK";
//        return self::ComputeRealTimeOrder1($x);
//
//
//        $list = Production::where("waiting_status_id", 7301002)->get();
//        foreach ($list as $item) {
//            $machine_allocation = MachineAllocation::where("production_id", $item->id)->first();
//            if ($machine_allocation) {
//                RegisterProductionController::ProductionTerminate($machine_allocation);
//            }
//        }
//        return "OK";
//        return ChangePackingQuickController::SendSms(1, PackingType::find(68), PackingType::find(69));
//        return Artisan::call("view:clear");

//
        $list = Product\ConsumedProduct\ConsumedProduct::join("products", "products.id", "consumed_products.material_id")->select("consumed_products.*")->where("goods_kind_id", 2)->get();
        foreach ($list as $item) {
            $item->in_ordering_customer_can_choose = 1;
            $item->save();
        }


        // حل مشکل وابستگی نخ
        $list = Product::where("goods_kind_id", 2)->where("id", ">", 400)->get();
        foreach ($list as $product) {
            // حل مشکل گراف
            foreach ($product->route()->where("active_status_id", 1200)->get() as $route) {

                foreach ($route->bom as $bom) {
                    foreach ($bom->product_route->line_product_station()->groupBy("machine_type_id")->get() as $line_product_station) {
                        $result = Product\MaterialFlow::AddOneToOneGraph($product, $bom, $line_product_station->machine_type);
                    }
                }
            }
        }

        // حل مشکل وابستگی نخ
//        $list = Product::where("goods_kind_id", 2)->where("id" , ">", 400)->get();
//        foreach ($list as $product) {
//
//            $list_bom = BOMItem::join("products", "products.id", "material_id")->where("product_id", $product->id)->where("goods_kind_id", 2)->select("bill_of_material_item.*")->get();
//            foreach ($list_bom as $item) {
//                BOMItem::where("id", $item->id)->update(["dependent_on_material_id" => $product->id]);
//
//            }
//
//        }
        //54555

//        $list = Product::where("goods_kind_id", 2)->where("id" , ">", 400)->get();
//        foreach ($list as $product) {
//
//            $list_bom = BOMItem::join("products", "products.id", "material_id")->where("product_id", $product->id)->where("goods_kind_id", 10)->select("bill_of_material_item.*")->get();
//            foreach ($list_bom as $item) {
//                $bom_item_mm = BOMItem::join("products", "products.id", "material_id")->where("product_id", $product->id)->where("goods_kind_id", 2)->select("bill_of_material_item.*")->first();
//                BOMItem::where("id", $item->id)->update(["dependent_on_material_id" => $bom_item_mm->material_id]);
//
//            }
//
//        }
        return "OK";
        return SpecialLicense::SendSmsToConfirmation(SpecialLicense::find(613));
        $allowed_status_ids = [
            500,
            7001001, 7001002,
            7201001, 7201002,
            7301001, 7201002, 7301005,
        ];
        return Production::whereIn("waiting_status_id", $allowed_status_ids)->where("status_id", 500)->selectRaw("sum(number), count(id)")->get();
        return MachineAllocation::whereIn("status_id", [5310010, 5310040])->sum("allocation_amount");

        $order_status_ids = [35030, 35040, 35090, 304040, 304050, 304060, 304070, 304075, 304080];
        // 1- به دست آوردن لیست سفارش ها در x روز گذشته که وضعیت آنها در انتظار آماده سازی و ارسال ناقص، تایید برگ خروج باشد
        $order_ids = Order::
        whereIn("orders.status_id", $order_status_ids)-> // از تایید پیش فاکتور به بعد
        pluck("id")->toArray();
        $order_ids[] = -1;
        return $production_amount = Production:: whereIn("production_cards.order_id", $order_ids)->
        join("products", "production_cards.product_id", "products.id")->
        join('order_factor', function ($join) {
            $join->on("order_factor.order_id", "=", "production_cards.order_id");
            $join->on("order_factor.product_id", "=", "production_cards.product_id");
        })->
        whereNull("parent_production_id")->
        selectRaw("sum(number * weight ) as sum_amount,sum(number * fea) as sum_price_amount ")->
        first();

        $order_ids = ProductRequestFormForm::
        join("product_request_forms", "product_request_forms.id", "product_request_form_id")->
        where("product_request_form_form.form_id", $form_id)->
        whereNotNull("order_id")->
        pluck("order_id")->toArray();


        if (count($order_ids) > 0) {

            return $data = FormItem::
            join("forms", "forms.id", "form_id")->
            join('order_factor', function ($join) {
                $join->on("order_factor.product_id", "=", "form_item.product_id");
            })
                ->join('products', 'products.id', '=', 'order_factor.product_id')
                ->whereIn("order_id", $order_ids)
                ->where("form_id", $form_id)
                ->select("form_id", "products.id", "order_id")
                // ->groupby("form_item.id")
                // ->selectRaw("sum(products.weight * form_item.amount) as sum_weight , sum(fea * form_item.amount) as sum_price,forms.updated_at,products.id")
                ->get();
        }

        return "OK";

        foreach ($list as $item) {

            // $liproduct = LineProductStation::where("product_id",$item[0])->first();
//            if ($liproduct) {
//
//               $liproduct->contractor_operation_id=$item[0];
//               $liproduct->save();
//
//            }

            $gkv = GoodsKindPropertyValue::where("product_id", $item[0])->
            where("goods_kind_property_id", 220336)->first();
            if (!$gkv) {
                GoodsKindPropertyValue::create([
                    "product_id" => $item[0],
                    "goods_kind_property_id" => 220336,
                    "value" => $item[1]
                ]);
            } else {
                $gkv->value = $item[1];
                $gkv->save();
            }


        }
        return "OK";

        return MachineProductPropertyValue::GetCaption(Machine::find(16), Product::find(303), LineProductStation::find(1445));

        return "OK";
        return Script1007Controller::handle(11);
        return $old_requests = ProductRequestForm::
        join("product_request_form_item", "product_request_forms.id", "product_request_form_id")->
        join("products", "products.id", "product_id")->
        where([
            "product_request_forms.applicant_type_id" => 40,
            "product_request_forms.applicant_id" => 15,
            "product_request_forms.warehouse_id" => 6
        ])->
        whereNotIn("product_request_forms.status_id", [7005002, 7005009])-> // خاتمه یافته , تحویل شده
        where("goods_kind_id", 2)->
        groupBy("product_request_forms.id")->
        select("product_request_forms.*")->
        get();

        return "OK";
        $list_id_order_by_transport_id = FormItem::
        leftjoin("packing_form_item", "packing_form_item.id", "packing_form_item_id")->
        leftJoin("transport_packing_form", "transport_packing_form.packing_form_id", "packing_form_item.packing_form_id")->
        orderBy("transport_packing_form.transport_item_id")->
        orderBy("transport_packing_form.packing_form_id")->
        where("form_item.form_id", 3925)->
        selectRaw("form_item.id,packing_form_item.packing_form_id")->
        get();
        $list_group = [];
        $packing_count_list = [];
        // گروه بندی بر اساس کد کالا و درجه - کارت سطح بالا
        foreach (Form::find(3925)->item()->with("packing_form_item")->get() as $item) {
            $parent_production = $item->product_request_form_item->production ?? null;
            $parent_product = $parent_production->product ?? null;
            $parent_product_id = $parent_product->id ?? 0;

            if (!isset($list_group[$item->product_id . "_" . $item->degree_id . "_" . $parent_product_id])) {

                $list_group[$item->product_id . "_" . $item->degree_id . "_" . $parent_product_id] = $item;
                $packing_count_list[$item->product_id . "_" . $item->degree_id . "_" . $parent_product_id] = [];
                $packing_count_list[$item->product_id . "_" . $item->degree_id . "_" . $parent_product_id][$item->packing_form_item->packing_form_id] = 1;
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
            }

        }

        foreach ($packing_count_list as $key => $packing_count) {
            $list_group[$key]["item_count"] = count($packing_count);
        }
        return $list_group;
        return Script1009Controller::handle();
        $allocation_item = MachineAllocation::find(120);
        return $bom_material_item =
            \App\Http\Controllers\GoodsKindProcess\Fabric\FinishingMachine\ProductionCard\MachineAllocationController::
            MaterialFlow(Allocation::find(106), Machine::find(16));

        $product = Product::find(318);
        $product_version = Product\Version\ProductVersion::find(983);
        return self::ChangeCurrentVersionText($product, $product_version);

        $new_allocation_amount = 108;
        $production = Production::find($x);
        $number_of_band_selected = 2;
        $machine = Machine::find(23);
        return $result_doff = self::getNumberOfDoff($new_allocation_amount, $production, $machine, $number_of_band_selected);
//
//        return "OK";
//
//        $form = Form::find(827);
//        foreach ($form->item as $item) {
//            echo $item->packing_form_item->packing_form->pin1 . "<br/>";
//        }
//
//        return "ok";
//        // تغییر مشخصات کالا
//        $list = [
////            ["01/11/0200503","تدی"],
//
//        ];
//        foreach ($list as $item) {
//            $product = Product::where("code", $item[0])->first();
//            if ($product) {
//                $value = GoodsKindPropertyValue::where("product_id", $product->id)->
//                where("goods_kind_property_id", 220555)->first();
//                if ($value) {
//                    $value->value = $item[1];
//
//                    $value->save();
//                } else {
//                    GoodsKindPropertyValue::create([
//                        "product_id" => $product->id,
//                        "goods_kind_property_id" => 220555,
//                        "value" => $item[1]
//                    ]);
//                }
//
//            } else {
//                echo $item[0] . "<br/>";
//                //  return $item;
//            }
//        }
//        return "OK";
//        $k = 1;
//        $products = Product::where("goods_kind_id", 4)->get();
//        foreach ($products as $product) {
//            foreach ($product->bom as $bom) {
//                $resut = self::GrophCheck($bom);
//
//                if (!$resut["result"]) {
//                    echo $product->fullCaption() . " =>" . $resut["error_type_id"] . "<br/>";
//                    $line_product_station = LineProductStation::where("product_id", $product->id)->where("product_route_id", $bom->product_route_id)->first();
//                    Product\MaterialFlow::AddOneToOneGraph($product, $bom, $line_product_station->machine_type);
//                    $k++;
//                    if ($k > 30) {
//                        return "OK";
//                    }
//                    //  echo $product->fullCaption()." =>".$resut["error_type_id"]."<br/>";
//                    //  return "OK";
//                }
//            }
//        }
//        return "OK";
//        return self::ChangePacking(PackingForm::find(5055));
//        return FabricRaw::GetYarnType(Product::find(339), 220354);
//        return Form::find(3925)->itemOrderByTransportCode("group_by_packing_form_item_parent_production");
//
//        return
//        return self::send_smd(Employment::find(132));
//        return SMSNotification::Test();
//        //  return  $rlt = Employment::SendSmsForCustomer(Employment::find(130))?1:2;


//        $list = [
//            [1022, 201],
//            [1028, 201],
//            [1214, 204],
//            [1265, 198],
//            [1266, 204],
//            [1363, 207],
//            [1364, 201],
//            [1365, 203],
//            [1366, 198],
//            [1367, 178],
//            [1370, 199],
//            [1373, 201],
//            [1374, 198],
//            [1382, 198],
//            [1383, 207],
//            [1384, 203],
//            [1385, 206],
//            [1386, 198],
//            [1387, 206],
//            [1388, 201],
//            [1389, 207],
//            [1390, 206],
//            [1391, 169],
//            [1392, 177],
//            [1393, 178],
//            [1395, 207],
//            [1396, 177],
//            [1401, 201],
//            [1405, 206],
//            [1406, 203],
//            [1407, 198],
//            [1408, 206],
//            [1409, 198],
//            [1411, 198],
//            [1413, 198],
//            [1414, 202],
//            [1415, 201],
//            [1416, 203],
//            [1417, 207],
//            [1418, 206],
//            [1419, 198],
//            [1420, 206],
//            [1421, 207],
//            [1422, 206],
//            [1423, 207],
//            [1424, 201],
//            [1425, 198],
//            [1426, 206],
//            [1428, 207],
//            [1435, 178],
//            [1438, 177],
//            [1442, 177],
//            [1443, 198],
//            [1444, 206],
//            [1446, 177],
//            [1449, 177],
//            [1450, 177],
//            [1452, 177],
//            [1454, 198],
//            [1455, 201],
//            [1463, 177],
//            [1469, 178],
//            [1476, 178],
//            [1477, 178],
//
//
//        ];
//        $not_exist = [];
//        foreach ($list as $item) {
//
//            $product = Product::where("id", $item[0])->first();
//            if (!$product) {
//                $not_exist[] = $item;
//                continue;
//            }
//
//            $liproduct = LineProductStation::where("product_id", $product->id)->first();
//            if (!$liproduct) {
//                $not_exist[] = $item;
//                continue;
//            }
//
//            $liproduct->production_channel_type_id = $item[1];
//
//            $liproduct->save();
//
//
//        }
//
//        return json_encode($not_exist);
////     return   GoodsKind::UpdateProperty(GoodsKind::find(5),496);
        //        return "OK";
////        $product=Product::find(7);
////        $new_product=Product::find(8);
////        foreach ($product->route as $route_item) {
////            $wast_list=Product\Waste\ProductWaste::where("product_route_id", $route_item->id)->get();
////            $wast_list = $wast_list->toArray();
////            $wast_list["product_id"] = $new_product->id;
////            $new_line_product_station["product_route_id"] = $new_route->id;
////            Product\Waste\ProductWaste::create($new_line_product_station);
////        }
////        return "OK";
////        $productCreationProcess=ProductCreationProcess::find(1321);
////       return $productCreationProcess->product->product_versions;
////      return  Product\Version\ProductVersion::GetVersion($productCreationProcess->product,false);
//        $production_list = Production::whereNull("normal_amount")->where("id", ">", 540)->get();
//        foreach ($production_list as $production) {
//            $product = $production->product;
//            $normal_amount = 40 / $product->weight;
//
//            if ($product->frame_ratio_unit2 && $product->sub_unit2_id == 1400) {
//                $normal_amount = round($normal_amount / $product->frame_ratio_unit2) * $product->frame_ratio_unit2;
//            }
//            $production->normal_amount = $normal_amount;
//
//            $production->save();
//
//        }
//        return "OK";
//        $product = Product::find(4);
//
//        return Product\Version\ProductVersion::GetVersion($product);
//
//        $list = Product::whereNull("sub_unit2_id")->
//        whereIn("goods_kind_id", [4, 5])->
//        whereNotNull("frame_ratio_unit2")->select("id", "caption", "sub_unit2_id", "frame_ratio_unit2")->get();
//        foreach ($list as $product) {
//            $product->frame_ratio_unit2 = null;
//            $product->save();
//        }
//
//        return count($list);
//
//

//
//        return $list;
//        Product::rightjoin("consumed_products", "products.id", "product_id");
//        Artisan::call("view:clear");
////        return    $session_data = Product\ProductRequest\ProductRequestFormSessionData::
////        getData(ProductRequestForm::find(1611),false,1);
////        $product_request_form=ProductRequestForm::find(1611);
////      return  $session_data_list = ProductRequestFormSessionData::
////        rightJoin("product_request_forms", "product_request_forms.id", "product_request_form_packing_session_data.product_request_form_id")->
////        where("applicant_type_id", $product_request_form->applicant_type_id)->
////        where("applicant_id", $product_request_form->applicant_id)->
////        whereIn("product_request_forms.status_id", [7005001, 7005004, 7005008])->
////        select("product_request_form_packing_session_data.data", "product_request_forms.id")->
////        get();
////        $product=Product::find(186);
////
//        $product = Product::find(63);
//        return Product\BOM\BOMLog::GetVersion($product, $product->bom()->first());
//        return Product\Version\ProductVersion::GetVersion($product);
//        $list = Product::get();
//        foreach ($list as $product) {
//            $product->caption = Message::convert_farsi_digits_to_english($product->caption);
//            $product->save();
//////
//        }
        // چک کردن تخصیصی ها

        $list = Allocation::where("id", ">", 550)->whereIn("status_id", [5310020, 5310040])->get();
        foreach ($list as $allocation) {
            $allocation_amount = MachineAllocation::where("allocation_id", $allocation->id)->sum("allocation_amount");

            $sum_doff = AllocationDoffs::where("allocation_id", $allocation->id)->sum("amount_of_each_doffs");

            $sum_brand = AllocationBrand::where("allocation_id", $allocation->id)->sum("amount_of_brand");

            if ($allocation_amount != $sum_doff || $sum_brand != $sum_brand) {
                if ($sum_doff == 0) {
                    echo $allocation->id . "=>" . $allocation_amount . "-" . $sum_doff . "-" . $sum_brand . "<br/>";
                } elseif ($sum_doff != 0 && $allocation_amount / $sum_doff == 2) {
                    echo $allocation->id . "=>" . $allocation_amount . "-" . $sum_doff . "-" . $sum_brand . "<br/>";
                } else {
                    return $allocation->id . "=>" . $allocation_amount . "-" . $sum_doff . "-" . $sum_brand;
                }
            };
        }

        return


            $production_list = Production::where("id", 972)->get();
        foreach ($production_list as $production) {
            $normal_amount = null;
            if ($production->parent_production) {
                $packing_type = ProductionPackingType::where("production_id", $production->parent_production_id)->first();

                if ($packing_type->packing_type && $packing_type->packing_type->normal_amount) {
                    $normal_amount = $packing_type->packing_type->normal_amount;
                }
            } else {
                $packing_type = ProductionPackingType::where("production_id", $production->id)->first();
                if ($packing_type->packing_type && $packing_type->packing_type->normal_amount) {
                    $normal_amount = $packing_type->packing_type->normal_amount;
                }
            }

            $product = $production->product;
            $normal_amount = $normal_amount / $product->weight;

            if ($product->frame_ratio_unit2 && $product->sub_unit2_id == 1400) {
                $normal_amount = round($normal_amount / $product->frame_ratio_unit2) * $product->frame_ratio_unit2;
            }
            return $production->normal_amount = $normal_amount;

            return $production;
        }


        return "OK";
        $all_allocation_amount = 172.32;
        $production = Production::find(391);
        $machine = Machine::find(13);
        return MachineAllocationController::getNumberOfDoff($all_allocation_amount, $production, $machine, 2);


        $production = Production::find(398);
        $machine = Machine::find(13);
        $allocation_amount = 216;
        $result_doff = self::getNumberOfDoff($allocation_amount, $production, $machine);
        return $result_doff;
        return "OK";
        $list = ProductRequestForm::where("status_id", 7005002)->where("id", ">", $x)->whereNotNull("order_id")->get();
        $k = 0;
        foreach ($list as $item) {
            echo "item:" . $item->code . "<br/>";

            $k++;

            $item->updateApplicantStatus();
            if ($k > 100) {
                break;
            }

        }
        return $k;

        return RegisterProductionController::FinalRegisterWithDetails($machine_allocation, $check_between = true, $terminate_production_form = true);

        return Allocation\AllocationBrand::CheckBrandForBrand(Product::find(292), 33, 3, 'weight');
        //  $classified_absence_from_regular_working_hours = Setting::getIntegerValue("classified_absence_from_regular_working_hours");
        return PackingType::find(356)->fullCaption(5);
        $worker = Worker::find(443);
        $date = "2025-09-07";

        return $result = DailyShiftOperation::CalculateForDay($worker, $date);
        return $operation = UserOperation::CalculateFromDailyShiftOperation($worker, $date, $result["intervals"], $classified_absence_from_regular_working_hours);
        $operation->save();
        return true;

        return $current_final_and_sub_amounts = PackingFormItem::whereIn("packing_form_id", $packing_form_ids)->
        selectRaw("sum(final_amount) as final_amount,sum(sub_amount) as sub_amount,production_form_item_id")->
        groupBy("production_form_item_id")->
        get()->
        keyBy("production_form_item_id");
        return floor(round(26.4 / 2.64, 6));
        $product = Product::find(627);
        return Product::CheckFrameFroDoffs($product, 79.2, 3);

        return "OK";
        //////////////////// صفر کردن سفارش ها
//        $ctrl = new ProductRequestPermissionController();

//        $list = ProductRequestForm::where("status_id", 7005008)->whereNotNull("order_id")->where("product_request_form_type_id", 1)->get();
//        $k = 0;
//        foreach ($list as $item) {
//            echo "item:" . $item->code . "<br/>";
//
//            $k++;
//
//            $ctrl->remove_product_request_form($item, 0, "test");
//
//            if ($k > 50) {
//                break;
//            }
        echo "<br/>";
        echo "<br/>";
        echo "<br/>";
        echo "<br/>";


        return "OK";

        return Carbon::create(2025, 3, 21, '08', '00');
        return $first_day_in_year = CalendarUtils::toGregorian(1404, 1, 1);
        $order = Order::find(2028);
        return ProductRequestForm::newRequest(
            $order,
            $order->customer_id,
            30,
            1,
            null,
            $order->delivery_datetime,
            ""
        );

        $product_ids = [1431];
        // به دست آوردن مقدار سفارش سایر مشتریان
        $all_order_list = Order::join("order_list", "orders.id", "=", "order_id")->
        whereIn("orders.status_id", self::$order_status_list)->
        whereIn("product_id", $product_ids)->
        select("amount", "product_id", "order_id")->pluck("amount", "order_id")->toArray();

        // به دست آوردن مقدار درخواست های مجوز سایر مشتریان
        $all_product_request_list = ProductRequestForm::join("product_request_form_item", "product_request_forms.id", "=", "product_request_form_id")->
        whereIn("product_request_forms.status_id", self::$product_request_forms_status_list)->
        whereIn("product_id", $product_ids)->
        // selectRaw("sum(amount_request) as amount, product_id")->
        select("amount_request", "product_id", "order_id")->pluck("amount_request", "order_id")->toArray();

        $sum = 0;
//        foreach ($all_order_list as $order_id=>$amount) {
////            if(!isset($all_product_request_list[$order_id])) {
////                echo $order_id." not exist".$amount;
////                $sum+=$amount;
////            }
//            if(isset($all_product_request_list[$order_id]) && $amount != $all_product_request_list[$order_id]) {
//                                echo $order_id." not equl ".$amount;
//                $sum+=$amount;
//            }
//        }


        foreach ($all_product_request_list as $order_id => $amount_request) {
            if (!isset($all_order_list[$order_id])) {
                echo $order_id . " not exist" . $amount_request;
                $sum += $amount_request;
            }

            if (isset($all_order_list[$order_id]) && $amount_request != $all_order_list[$order_id]) {
                echo $order_id . " not equl " . $amount_request;
                $sum += $amount_request;
            }
        }
        echo "sum=$sum";
        return "OK";
        $order = Order::find($x);
// ابتدا باید چک کنیم که به اندازه کل سفارش، درخواست خروج از انبار صادر شده باشد
        $order->status_id = 35050; // ارسال شده

        $order_list_items = OrderList::
        join("orders", "orders.id", "order_list.order_id")->
        leftJoin("product_request_form_item", 'product_request_form_item.order_list_id', 'order_list.id')->
        where("orders.customer_id", $order->customer_id)->
        where("orders.id", $order->id)->
        selectRaw(
            "order_list.product_id,
                        sum(product_request_form_item.amount_remaining) as amount_remaining,sum(product_request_form_item.amount_sent) as amount_sent,sum(product_request_form_item.amount_request) as amount_request, sum(order_list.amount) as order_amount
                        ")->
        with("product.goods_kind")->
        get();
        foreach ($order_list_items as $order_list_item) {

            $product = $order_list_item->product;
            // حداقل مقداری که باید ارسال شود تا ردیف سفارش بشود ارسال شده
            $min = $order_list_item->order_amount * (1 - $product->goods_kind->be_lower_in_confirm_exit_form / 100);
            if ($order_list_item->amount_sent < $min) {
                $order->status_id = 35040; // ارسال ناقص
            }

        }
        return $order->status_id;

        return self::GetProductData(Order::find(2000));
// ارسال درخواست کالا از انبار
        return ProductRequestForm::newRequest(
            $order,
            $order->customer_id,
            30,
            1,
            null,
            $order->delivery_datetime,
            ""
        );

        return Artisan::call("migrate");
        $all_allocation_amount = 224;
        $production = Production::find(229);
        $machine = Machine::find(12);
        return MachineAllocationController::getNumberOfDoff($all_allocation_amount, $production, $machine);
        return SMSNotification::Test();
        $date = Carbon::now()->addDay($x)->format("Y-m-d");
        for ($k = $x; $k <= $y; $k++) {
            Script1023Controller::handle(-$k);
            echo -$k;
        }
        return "OK";
        $order_list = OrderList::find($x);

        return $list = self::getAllSellingAmount2(null, "sale_planing_status_list", $order_list->order_id, $order_list->product_id);
        $customer_id = $order_list->customer_id;
        $product_id = $order_list->product_id;
        $invalid_order_id = $order_list->order_id;
        $status_list = [];
// براساس نوع فاکتور (رسمی، غیررسمی)
        $status_list = Setting::getStringValue($setting_types ?? "sale_formal_status_list");
        $status_list = json_decode($status_list, true);

        $warehouse_status = [];
        if (in_array(35090, $status_list)) { // تایید برگ خروج
            $status_list = array_diff($status_list, [35090]);
            $status_list[] = 7005008; // در انتظار تایید برگ خروج (انبار)
        }
        if (in_array(35040, $status_list)) { // ارسال ناقص
            $status_list = array_diff($status_list, [35040]);
            $status_list[] = 7005004; // در انتظار ارسال باقی مانده درخواست
        }

        return $list = Order::join("order_factor", "orders.id", "order_id")->
        when($customer_id, function ($query) use ($customer_id) {
            return $query->where("orders.customer_id", $customer_id);
        })->
        when($invalid_order_id, function ($query) use ($invalid_order_id) {
            return $query->where("orders.id", "!=", $invalid_order_id);
        })->
        when($product_id, function ($query) use ($product_id) {
            return $query->where("order_factor.product_id", $product_id);
        })->
        whereIn("orders.status_id", $status_list)->
        selectRaw("sum(total_price_with_tax) as total_price_with_tax,sum(carton* number_in_carton) as amount")->
        first();
        $product_id = 161;
        $degree_id = null;
        return $query = OrderList::
        when($product_id, function ($query) use ($product_id) {
            $query->where("product_id", $product_id);
        })->
        when($degree_id, function ($query) use ($degree_id) {
            $query->where("degree_id", $degree_id);
        })->
        whereNull("from_order_id")->
        whereIn("erp_status_id", [360, 340, 305])->get();
// return  Artisan::call("view:clear");

        return Artisan::call("migrate");
        return Artisan::call("storage:link");;
        $order = Order::find($x);
        $product_ids = $order->orderList()->pluck("product_id")->toArray();
        if (count($product_ids) == 0) {
            return "error 1";
            return back()->with("سبد خرید خالی است، لطفا یکبار دیگر تلاش کنید.");
        }

        $product_tariff_log_count = ProductTariffLog::whereIn("product_id", $product_ids)->
        where("tariff_log_id", $order->tariff_log_id)->pluck("product_id")->count();

        if ($product_tariff_log_count != count($product_ids)) {

            return back()->with("سبد خرید با توجه به تعرفه نامعتبر است، لطفا یکبار دیگر تلاش کنید.");
        }
        return "OK";

        $packing_form = PackingForm::find(71064);
        $result = PackingForm::UpdateWeight($packing_form);
        $packing_form->weight = $result["weight"];
        $packing_form->gross_weight = $result["gross_weight"];
        $packing_form->save();
        return "OK";
        $machine = Machine::find(9);
        return $reserve_after_current_allocation = $machine->ReserveAllocation(false, $production_type_id = 2, $orderByPriority = "Desc")->first();
        $carbon_end_datetime = Carbon::now();
        $carbon_start_datetime = Carbon::now();
        $worker = Worker::find(115);
        return $leaves_post_user_ids = LeaveOvertime::
        join("leave_overtime_types", "leave_overtime_types.id", "leave_overtime_type_id")->
        join("leave_overtime_confirmation", "leave_overtimes.id", "leave_overtime_id")->
        whereIn("leave_overtime_group_id", [1, 3, 4, 5])->//مرخصی ,ماموریت, جایگزینی - غیبت
        whereIn("leave_overtimes.status_id", [4630003, 4630007, 4630006])->
        where("replace_user_id", $worker->id)->
        where("start_datetime", "<", $carbon_end_datetime->addMinute(-($allow_earlier_and_delay_for_post->allowed_earlier_time_for_entry ?? 1)))->
        where("end_datetime", ">", $carbon_start_datetime->addMinute(-($allow_earlier_and_delay_for_post->allowed_delay_time_for_exit ?? 0)))->
        pluck("post_user_id")->
        toArray();
        $post_user_list = PostUser::getCurrentPostByShiftWorkAndLeaveOvertime("post_user_object", $worker);

        $packing_form = PackingForm::find(2033);
        $jsn_data_list = JsonDataList::where(
            [
                "other_id" => $packing_form->id,
                "message_type_id" => 370,
            ])->first();

        return $qc_data = json_decode($jsn_data_list->data, true);
        return SMSNotification::Test();
        return PackingFormItem::where("packing_form_id", 46484)->groupBy("product_id")->selectRaw("sum(final_amount) as final_amount, product_id")->pluck("final_amount", "product_id");
        return Script1016Controller::ScriptStatus();
        $product_request_form = ProductRequestForm::find(3);
        $product_request_form->product_request_form_packing_types()->

        whereIn("product_id", [30])->
        pluck("packing_type_id")->toArray();
        $current_packing_type_ids = [355];
        $packing_type_ids = [356];
        $message = "";
        foreach ($current_packing_type_ids as $current_packing_type_id) {

            if (!in_array($current_packing_type_id, $packing_type_ids)) {

                $current_packing_type = PackingType::find($current_packing_type_id);

                $message = "نوع بسته بندی " . "456" . "  معتبر نمی باشد، " .
                    "<br/>لطفا نوع بسته بندی را به یکی از انواع زیر تغییر دهید:" . "<br/>";

                $packing_type_list = PackingType::whereIn("id", $packing_type_ids)->get();

                foreach ($packing_type_list as $item) {
                    // $message .= $item->caption . "<br/>";
                    $message .= print_r($packing_type_ids);
                }
            }

            return [
                "result" => false,
                "error" => $message
            ];
        }
        return [
            "result" => true,
            "error" => ""
        ];

        $date = Carbon::now()->addDay($x)->format("Y-m-d");
        for ($k = $x; $k <= $y; $k++) {
            Script1023Controller::handle(-$k);
            echo -$k;
        }
        return "OK";

        $pallet = Pallet::find(1002);
        return $pallet->isCorrectNumberOfPackingForSetToWarehouse(2) ? 1 : 2;
        $exit_status_id = Setting::getStringValue("customer_exit_form_status_id");

        $product_request_form_item = ProductRequestFormItem::find(6);
        return $status_were_transaction_not_ok = $product_request_form_item->product_request_form->getCurrentExistFromForDashboard();


        return $sum_amount = ProductRequestFormForm::
        join("forms", "forms.id", "product_request_form_form.form_id")->
        join("form_item", "form_item.form_id", "forms.id")->
        where("product_request_form_item_id", $product_request_form_item->id)->
        where("product_request_form_id", $product_request_form_item->product_request_form_id)->
        where("product_id", $product_request_form_item->product_id)->
        whereIn("forms.status_id", $status_were_transaction_not_ok)->
        sum("amount");

        $mpdf = new \Mpdf\Mpdf();
        $html = '<img src="' . public_path('assets/images/7.jpg') . '">';
        $mpdf->WriteHTML($html);
        $mpdf->Output();

        return $warps_request_form = ProductRequestForm:: // تعداد درخواست های ماژول پایان نیافته
        join("product_request_form_item", "product_request_form_id", "product_request_forms.id")->
        where(["product_id" => $product_id])->
        where("applicant_type_id", $applicant_type_id)->
        when($product_request_form_id, function ($query) use ($product_request_form_id) {
            return $query->where("product_request_form_id", "<", $product_request_form_id);
        })->
        whereNotIn("status_id", [
            7005002,//تحویل شده
            7005006, // کنسل شده
            7005009 // خاتمه یافته
        ])->
        get();

        return "($input_count - $output_count) - $warps_request_form";
        event(new WarpsAvailableEvent(Product::find(78), 1));
        return;
        $list = [

            "09130656899",
        ];;

        foreach ($list as $item) {
            $software_name = Setting::getStringValue("software_name");
            $template = "invalidsms";
            $token = "باتشکر";
            $token2 = jdate(Carbon::now()->timestamp)->format('H:i Y/m/d ');
            $token3 = "5646";
            $token10 = $software_name;
            $token20 = "تست پیامک";


            \Illuminate\Support\Facades\Notification::send(
                $item,
                new SMSNotification($template, $token, $token2, $token3, $token10, $token20, true)
            );
        }
        return "OK";
        SMSNotification::Test();
        $order = Order::find(12);
        return $address = $order->customer->getDefaultAddress();
        $domain = request()->getHost();
        $static_ip = Setting::getStringValue("static_ip");
        return $this->extractDomain($domain) . "----" . $this->extractDomain($static_ip);
        return $domain = request()->getHost();
        return Artisan::call("migrate");
//return ClientTransaction::UpdateCredit();
        $date = Carbon::now()->addDay($x)->format("Y-m-d");
        for ($k = $x; $k <= $y; $k++) {
            Script1023Controller::handle(-$k);
            echo -$k;
        }
        return "OK";
//
//     return   ClientTransaction::UpdateCredit();
        $worker = Worker::find(11);
        $date = "2025-07-09";
        return Script1023Controller::calculate($worker, $date);
        return $result = DailyShiftOperation::CalculateForDay($worker, $date);
        if (!$result["result"]) {
            Script::SendSmd(Script::find(23), $worker->id, $result["error"]);
            return false;
        }
        return true;
        $classified_absence_from_regular_working_hours = Setting::getIntegerValue("classified_absence_from_regular_working_hours");

        $operation = UserOperation::CalculateFromDailyShiftOperation($worker, $date, $result["intervals"], $classified_absence_from_regular_working_hours);
        $operation->save();
        return true;
        $product_request_form_item = ProductRequestFormItem::find(4251);

        $all_status_list = ProductRequestForm::getFormWaitingStatusList();

// وضعیت هایی که در آن تراکنش انبار ثبت شده است.
        $status_were_transaction_ok = $product_request_form_item->product_request_form->getValidStatusForConformForm();

// لیست وضعیت هایی که تراکنش انبار ثبت نشده است.
        foreach ($status_were_transaction_ok as $status_id) {
            // return $status_id;
            if (($key = array_search($status_id, $all_status_list)) !== false) {
                unset($all_status_list[$key]);
            }
        }
        return $all_status_list;
        return $product = Product::where("code", "1")->first();
        return Artisan::call("view:clear");
        return Script1013Controller::SaleInvoiceForTest(Form::find(5747), FinancialSoftwareTransKind::find(9), FinancialSoftwareTransferForm::find(6601));

        $allocation_or_order = Order::find(1322);
        return $product_tariff = ProductTariffLog::where("tariff_log_id", $allocation_or_order->tariff_log_id)->pluck("warehouse_id", "product_id")->toArray();
        return Artisan::call("view:clear");
        return Artisan::call("migrate");
        $carbon_start_datetime = Carbon::now();
        $carbon_end_datetime = Carbon::now();
        return $allow_earlier_and_delay_for_post =
            PostUser::join("posts", "posts.id", "post_id")->
            where("user_id", 118)->
            selectRaw("max(allowed_earlier_time_for_entry) as allowed_earlier_time_for_entry, max(allowed_delay_time_for_exit) as allowed_delay_time_for_exit")->
            first();
        return $leaves_post_user_ids = LeaveOvertime::
        join("leave_overtime_types", "leave_overtime_types.id", "leave_overtime_type_id")->
        join("leave_overtime_confirmation", "leave_overtimes.id", "leave_overtime_id")->
        whereIn("leave_overtime_group_id", [1, 3, 4, 5])->//مرخصی ,ماموریت, جایگزینی - غیبت
        whereIn("leave_overtimes.status_id", [4630003, 4630007, 4630006])->
        where("replace_user_id", 118)->
        where("start_datetime", "<", $carbon_end_datetime->addMinute(-($allow_earlier_and_delay_for_post->allowed_earlier_time_for_entry ?? 1)))->
        where("end_datetime", ">", $carbon_start_datetime->addMinute(-($allow_earlier_and_delay_for_post->allowed_delay_time_for_exit ?? 0)))->
        pluck("post_user_id")->
        toArray();
        $year = null;
        return $leave_reminder = LeaveRemainder::where("user_id", 6)->
        when($year, function ($query) use ($year) {
            return $query->where("year", $year);
        })->
        when(!$year, function ($query) use ($year) {
            return $query->
            where("start_date", "<=", Carbon::now())->
            where("end_date", ">=", Carbon::now());
        })->
        first();
        return Script1023Controller::handle($request);
// تعداد مرخصی/ماموریت های اضطراری در یک سال مالی
        $financial_year = Setting::FinancialYear();
        $start_date_time = $financial_year["start_date_time"];
        $last_date_time = $financial_year["last_date_time"];

        return LeaveOvertime::join("leave_overtime_types", "leave_overtime_types.id", "leave_overtime_type_id")->
        where([
            "user_id" => 1,
            "leave_overtime_group_id" => 2,
            "register_after_tacking" => 1
        ])->
        where("start_datetime", ">", $start_date_time)->
        where("start_datetime", "<=", $last_date_time)->
        whereNotIn("status_id", [4630004, 4630005, 4630008,])->
        where("start_datetime", ">=", Carbon::now()->addYear(-1))->
        count();
// return WarehouseShelving::find($x)->warehouse_shelving_type->id;
///return  WarehouseShelving::UpdateFullCodeFroAllSubLine( WarehouseShelving::find($x));
        return (WarehouseShelving::find($x)->UpdateFullCode());
        $products = Product::where("id", ">=", $x)->where("id", "<=", $y)->get();
        foreach ($products as $product) {
            $product->caption = $this->convert_farsi_digits_to_english($product->caption);
            $product->code = $this->convert_farsi_digits_to_english($product->code);

            $product->save();

        }

        return "OK";
        $date = Carbon::now()->addDay($x)->format("Y-m-d");
        for ($k = $x; $k <= $y; $k++) {
            Script1023Controller::handle(-$k);
            echo -$k;
        }
        return "OK";

        return Script1025Controller::handle($request);
        return "OK";
        $prf = ProductRequestForm::find(7);
        return $prf->getTranKind();

        $traffic_notification_for_post_ids = PostUser::join("posts", "posts.id", "post_id")->
        where("user_id", 48)->
        whereNotNull("traffic_notification_for_post_id")->
        pluck("traffic_notification_for_post_id")->toArray();
        $supervisor = Post::whereIn("id", $traffic_notification_for_post_ids)->get();


        return Artisan::call("migrate");
        $products = Product::where("id", ">=", $x)->where("id", "<=", $y)->get();
        foreach ($products as $product) {
            $product->caption = $this->convert_farsi_digits_to_english($product->caption);
            $product->code = $this->convert_farsi_digits_to_english($product->code);

            $product->save();

        }

        return "OK";

        $text = "پارچه پیراهنی تکمیل شده چهاخانه کد۸۰۲۹۳ طوسی,ابی";
        $converted_text = $this->convert_farsi_digits_to_english($text);

        return $converted_text; // نتیجه: در این متن چند عدد فارسی 120 و 35 است.
        $l = QueueOfLargeOperation::find(13);
        return RegisterProductionController::CopyPackingFrom($l);
//  return     $packing_type_weight_result = PackingType::getWeight( PackingType::find(74) );
//  return Script1023Controller::handle(-$x);
//        $worker=Worker::find(113);
//      return  AbsenceController::CreateNewAbsence($worker, Carbon::now(), null,true);
//       return $date = Carbon::now()->addDay(-30)->format("Y-m-d");
////        return $this->testNosa();
// return Script1008Controller::handle();

        $worker = Worker::find(309);
        $date = "2025-04-$x";
        return Script1023Controller::calculate($worker, $date);
        $transport = Transport::find(1599);
        $packing_form_ids = FormItem::
        join("transport_form", "transport_form.form_id", "form_item.form_id")->
        join("packing_form_item", "packing_form_item.id", "packing_form_item_id")->
        join("packing_forms", "packing_forms.id", "packing_form_id")->
        where("transport_id", $transport->id)->
        distinct("packing_form_id")->
        pluck("packing_forms.id")->
        toArray();

        return $product_weight = PackingForm::
        join("packing_form_item", "packing_forms.id", "packing_form_id")->
        whereIn("packing_forms.id", $packing_form_ids)->
        selectRaw("round(sum(weight),4) as weight,round(sum(gross_weight),4) as gross_weight,product_id,count(distinct(packing_forms.id)) as packing_count")->

        get();

        foreach (Product::all() as $product) {
            $product->caption = "کالای " . $product->id;
            $product->save();

        }
        return Artisan::call("storage:link");;
// return Artisan::call("db:seed");
        return Artisan::call("migrate");
        return Script1025Controller::handle();

        return Shelving::getCoding('number', 2, 999);
        return Script1008Controller::handle();

        $leave_overtime = LeaveOvertime::find(5734);

        return Script1023Controller::calculateForDays($leave_overtime->worker, $leave_overtime->start_datetime, $leave_overtime->end_datetime);
        Artisan::call("storage:link");
        return 1;
        $forms = Form::where("id", "<=", 3152)->
        where("status_id", 500000500)->
        paginate(40);
        foreach ($forms as $form) {

            $result = ExitFormController::ConfirmApplicant($form);

            if ($result["result"]) {

                event(new FormLogEvent($form));

            } else {
                return $form->code . "=>" . $result["error"];
            }
            echo "<br/>" . $form->code;
        }
        return "OK";
        return $result = SpecialLicense::CheckConnection(env("IC_APIKEY"));// در این ای پی ای چک می شود ایا اتصال به اینترنت برقرار است یا خیر

        return Script1013Controller::handle();
        return Artisan::call("migrate");
        return Script1021Controller::RemovePackingTypeFromProductRequestForm(10);
        return Script1025Controller::handle();
        return Artisan::call("migrate");
        $worker = Worker::find(1);
        $last_user_entry = UserEntryLog::where("user_id", $worker->id)->orderByDesc("id")->first();

        $shift_work_query = ShiftWorkDayController::GetDateWorkQuery($worker, 0, 0);
        $shift_work_query->where("start_datetime", ">=", Carbon::parse($last_user_entry->entry_datetime)->format("Y/m/d"))->
        where("end_datetime", "<=", Carbon::parse($last_user_entry->entry_datetime)->addDay()->format("Y/m/d"))->
        orderBy("start_datetime", "desc");
        $shift_work = $shift_work_query->first();
// باید تاریخ خروجی که ثبت می کنیم از تاریخ ورود بزرگتر باشد
        $input_datetime = Carbon::parse($last_user_entry->entry_datetime);
        $output_datetime = Carbon::parse($shift_work->end_datetime);

        if ($shift_work && $output_datetime->greaterThan($input_datetime)) {
            return "OK" . $input_datetime->format("Y/m/d H:i:s") . "--" . $output_datetime->format("Y/m/d H:i:s");
        } else {
            return "false";
        }

//  return Carbon::now()->addDay(-11);
        foreach (LeaveRemainder::all() as $leave) {
            $worker = Worker::find($leave->user_id);

            $re = LeaveRemainder::UpdateLeaveReminder($worker, 1, Carbon::now()->addDay(-11));
            if ($re["result"]) {

            } else {
                return "error";
            }
        }
        return "OK";
        return Script1021Controller::handle();
        $client = new \GuzzleHttp\Client();
        $request = $client->post('http://service.termehdastjerdi.ir:14400/api/accounting/client/buy/verify_payment_api', array());
        $response = $request->getBody();
        $result = json_decode($response, 1);
        return $result;
        return "OK";
//   $order=Order::find(12);
// return $order->orderFactor()->join("products","products.id","product_id")->orderBy("property1_caption")->select("order_factor.*")->get();
        return GoodsKind::UpdateProperty(GoodsKind::find(4), null, true);
        return Script1023Controller::handle();
////        $date = Carbon::now()->addDay($x)->format("Y-m-d");
////        $worker = Worker::find($y);
//        $log=UserOperation::find($x);
//        $date = Carbon::parse($log->current_date)->format("Y-m-d");
////      return  $result = DailyShiftOperation::CalculateForDay($worker, $date);
//        return Script1023Controller::calculate($log->worker, $date);
//
//

        foreach (LeaveRemainder::all() as $leave) {
            $worker = Worker::find($leave->user_id);
            $re = LeaveRemainder::UpdateLeaveReminder($worker, 1);
            if ($re["result"]) {

            } else {
                return "error";
            }
        }
        return "OK";
        return Artisan::call("db:seed");
        return LeaveRemainder::UpdateLeaveReminder(Worker::find(221));

// return Artisan::call("migrate");
//  return   Script1016Controller::UpdateSupportStatus();
//        $production_form=ProductionForm::find(11420);
//     return   ProductionForm::UpdateAmountWithLastContour($production_form,MachineLog::find(178086));
//
//        return $production_form->updateAmount();
// return Script1025Controller::handle();
//  return  $result_weight = PackingForm::UpdateWeight(PackingForm::find(20));
//  return Script1013Controller::handle();
//        return self::SetToWarehouse(Transport::find($x));
//        $worker = Worker::find(213);
//        $last_user_entry = UserEntryLog::where("id", 9307)->first();
//        $shift_work_query = ShiftWorkDayController::GetDateWorkQuery($worker, 0, 0);
//        $shift_work_query->where("start_datetime", ">=", Carbon::parse($last_user_entry->entry_datetime)->format("Y/m/d"))->
//        where("end_datetime", "<=", Carbon::parse($last_user_entry->entry_datetime)->addDay()->format("Y/m/d"))->
//        orderBy("start_datetime", "desc");
//
//        return $shift_work = $shift_work_query->first();
//        if ($shift_work) {
//            $exit_datetime = $shift_work->end_datetime;
//        }
//
//
//        $form_item=FormItem::find(13723);
//        $order=Order::find(730);
//      return  self::GetPriceFromFormItem($form_item,$order);
        $classified_absence_from_regular_working_hours = Setting::getIntegerValue("classified_absence_from_regular_working_hours");
//        return Artisan::call("migrate");
        for ($x = 1; $x <= 36; $x++) {
            $date = Carbon::now()->addDay(-$x)->format("Y-m-d");
            $worker = Worker::find($y);
//      return  $result = DailyShiftOperation::CalculateForDay($worker, $date);
            Script1023Controller::calculate($worker, $date);
        }
        return "ok";
        $date = Carbon::now()->addDay($x)->format("Y-m-d");
        $worker = Worker::find($y);
//      return  $result = DailyShiftOperation::CalculateForDay($worker, $date);
        return Script1023Controller::calculate($worker, $date);
//  $date = $uo->current_date;

// return   Script1023Controller::calculate(Worker::find(212), $date);

// برای تمامی افردا که نوع همکاری آنها کارمند تمام وقت است و حداقل یک پست در سازمان دارند محاسبه می کنیم.
        $workers = Worker::
        whereIn("cooperation_type_id", 1)->
        whereNotIn("status_id", [4620006, 4620007, 4620009])->
        get();
        foreach ($workers as $worker) {

            Script1023Controller::calculate($worker, $date);

        }
        return "OK";
        return Script1023Controller::calculate($worker, $date);

        return Script1021Controller::handle();
        return Artisan::call("migrate");


        $machine = Machine::where("id", ">", 0)->first();
        $machine->ReserveAllocation()->orderBy("priority_number")->get();

        $channelId = env("Tara_ContractId");
        $mobile = "09130656899";
        $accountNumber = "00118393";

//return  $RSASignature = Utility::GetRSASignature($accountNumber . "," . $mobile . "," . $channelId);


        return $result_login = \App\Http\Controllers\Accounting\WelfareService\Tara\Client\DashboardController::Login();

        return \App\Http\Controllers\Accounting\WelfareService\Tara\Client\DashboardController::GetPurchase(Worker::find(1), "eyJhbGciOiJIUzUxMiJ9.eyJzdWIiOiIyNzIwOCIsImlhdCI6MTcyODc5OTU0OCwiZXhwIjoxNzQ0MzUxNTQ4LCJkZXZpY2UiOiJ0YWd0YXBfZmFsbGFoIiwicm9sZSI6IlJPTEVfUEFZLUlOSVQifQ.-tN35yLCjld15yTbUe65LsZYbv0m8k6uEW6Os2ngZhHpt-FFYZGLbai1QHib-JpNKDJSYq0AqaWYC9IrScfegQ");

        1 / 0;

        return $this->testNosa();
        return SMSNotification::Test();
        return Utility::GetRSASignature("879,09130656899,4420207817,00118393");
        $sing = new SignController();
        return $sing->signValue($request);
// return Carbon::yesterday()->format('Y-m-d 00:00:00');
        return Production::where("created_at", ">", Carbon::yesterday()->addDay()->format('Y-m-d'))->get();
        $form = Form::find(78708);
        return $list = FormItem::
// join("packing_form_item", "packing_form_item.packing_form_id", "packing_forms.id")->
//  join("form_item", "form_item.packing_form_item_id", "packing_form_item.id")->
        where("form_item.form_id", $form->id)->
// groupBy("packing_forms.id")->
// whereNull("packing_form_master_id")->
// select("packing_forms.*")->
        get();
        return $form->getPackingFrom();
        return Script1021Controller::handle();
        return ClientTransaction::UpdateCredit();
        return Artisan::call("migrate");
        return $list_confirmed_count = ProductRequestFormForm::join("forms", "form_id", "forms.id")->
        where("product_request_form_id", 19208)->
        where("forms.status_id", 500000200)-> // تایید شده
        count();

        $machine = Machine::find(7);
        $allocation = Allocation::find(10);
        return $next_status_result = \App\Http\Controllers\GoodsKindProcess\Fabric\FinishingMachine\Machine\DashboardController::
        GetNextStatus($machine, $allocation, 0, -1, false, $check_status_id = [5310005, 5310050]);

        SMSNotification::Test();
        $this->getAlgorithm();
        return "OK";
        return Script1012Controller::handle();
        $sp = SpecialLicense::find(85);
        return $sp->ActionAfterConfirm();
        return $allow_earlier_and_delay_for_post =
            PostUser::join("posts", "posts.id", "post_id")->
            where("user_id", 194)->
            selectRaw("max(allowed_earlier_time_for_entry) as earlier_time_for_entry, max(allowed_delay_time_for_exit) as allowed_delay_time_for_exit")->
            first();

        session([
            "bearer_token" => Auth::user()->createToken("API TOKEN", ['*'], Carbon::now()->addMinute(2))->plainTextToken
        ]);
        $user = Auth::user();
        return session("bearer_token");
        return $request->bearerToken();
        $order = Order::find(1239);

        $allSellingAmount = $order->customer->getAllSellingAmount();
        $informal_percent = $allSellingAmount["selling_type"][2]["percent"];

//
        $order->selling_type_id = 2;// غیر رسمی
        if ($informal_percent > $order->customer->percent_max_informal_purchase) {
            $order->selling_type_id = 1; //رسمی
        }
// بروز رسانی درصد افزایش قیمت در خرید های غیررسمی
        return $order->selling_type_id;


        return $prices_without_packing_type = Product\Pricing\ProductPricing::selectRaw('CONCAT(product_id, "_") as col, price')->
        whereNull('packing_type_id')->
        pluck('price', 'col')->
        toArray();
        return view("test");


        return self::getPackingInWarehouse(ProductRequestForm::find(8931));
        return EndOfProductionCardTextureController::has_requirement_for_doffs(Machine::find(317));
        $employment = Employment::find(2);

        return $result = Worker::CreatePersonalInIc($employment->worker, env("IC_APIKEY"));
        return Employment::AddUserToPost($employment);
        return $no_started_employment_selection = $employment->employment_selections()->
        whereIn("status_id", [4640101, 4640102, 4640103])-> // شروع نشده/هماهنگی/ در انتظار انجام
        where('priority_number', '>=', $employment->current_priority_number)->
        orderBy('priority_number')->
        first();

        $sl = SpecialLicense::find(56);
        return $sl->ActionAfterConfirm();

//  Artisan::call("migrate");
        return "OK";
        return Order::GetPriceFromFormItem(FormItem::find(71867), Order::find(118));
        return $jalaliDate = "1399/08/06";
        return $georgianCarbonDate = \Morilog\Jalali\Jalalian::fromFormat('Y/m/d', $jalaliDate)->toCarbon();
        return SelectionPostSetting::
        join("selections", "selections.id", "selection_post_settings.selection_id")->
        leftJoin("selection_selectors", "selection_selectors.post_id", "selection_post_settings.post_id")->
        where("selection_post_settings.post_id", 1000)->
        groupBy("selection_post_settings.selection_id")->
        selectRaw("count(selection_selectors.id) as count,selections.caption")->
        get();
        1 / 0;
//        Artisan::call("migrate");
        return "OK";


        $material_ids = [-1];
        return $open_request_count = ProductRequestForm::
        join("product_request_form_item", "product_request_forms.id", "product_request_form_id")->
        where("applicant_type_id", 40)->
        where("applicant_id", 11)->
        whereIn("product_id", [$material_ids])->
        whereIn("product_request_forms.status_id", [7005001, 7005003, 7005004, 7005008])->
        count();
        $start_datetime = Carbon::parse("2024-01-08 18:01:10");
//تا پایان روز
        $end_datetime = (Carbon::parse($start_datetime)->addHour(24))->format("Y-m-d H:i:s");
        $worker = Worker::find(179);
// شیفت کاری امروز را به دست می آوریم و پاره وقتی که زمان پایان آن از همه بزرگتر است را انتخاب می کنیم.
        return $shift_work_query = ShiftWorkDayController::GetDateWorkQuery($worker, 0, 0)->
        where("start_datetime", ">=", Carbon::parse($start_datetime->format("Y-m-d")))->
        where("end_datetime", "<=", $end_datetime)->
        orderByDesc("end_datetime")->
        first();
        if (!$shift_work_query) {
            return [
                "result" => true,
                "message" => "برای امروز زمان کاری وجود ندارد."
            ];
        }
        $end_datetime = $shift_work_query->end_datetime;

        return "OK";
        Artisan::call("migrate");
        return "OK";
        $date = $user_operation->current_date;
        $operation = Script1023Controller::calculate($worker, $date);
        $result = DailyShiftOperation::CalculateForDay($worker, $date);
        $intervals = $result["intervals"];
        return view("hr.personal.shift_work_day.calculator_log", compact("intervals", "worker", "date", "operation"));


// تنظیمات
        $text = "سلام دنیا"; // متن فارسی برای ترجمه
        $targetLanguage = "en"; // زبان مقصد (در اینجا انگلیسی)

// ترجمه متن با استفاده از Google Translate API
        $apiKey = "YOUR_API_KEY"; // کلید API شما
        $url = "https://translation.googleapis.com/language/translate/v2?key=$apiKey";
        $data = [
            'q' => $text,
            'target' => $targetLanguage
        ];

        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data)
            ]
        ];

        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        if ($result === FALSE) { /* Handle error */
        }

// نمایش نتیجه
        $translation = json_decode($result, true);
        echo "متن اصلی: $text <br>";
        echo "ترجمه به انگلیسی: " . $translation['data']['translations'][0]['translatedText'];

        return "OK";

        $production_list = Production::join("products", "products.id", "product_id")->
        where("goods_kind_id", 5)->
        where("production_cards.waiting_status_id", 7008001)-> // در انتظار تخصیص پیمانکار
        orderByDesc("production_cards.id")->
        select("production_cards.*")->
        get();
        $log_algorithm  ["algorithm_id"] = 4;
        $log_algorithm["all_production_count"] = count($production_list);
        $log_algorithm["production_list_where_is_ok"] = 0;
        $log_algorithm["goods_kind_id"] = 0;
        $log_algorithm["production"] = [];

        return $result_setting_type1 = Algorithm4ProductionAllocationToContractorByDownstreamTermination::GetProductionBySettingType1($production_list);
        1 / 0;
        if ($x + $y + $z == 1500) {
            //  return $resutl = Script1026Controller::handle();
        }
        1 / 0;
        foreach ($resutl["production_list_where_is_ok"] as $item) {
            echo $item->serial . "<br/>";
        }
        return "OK";
        $packing_form_ids = [1000];
        return $packing_form_list = PackingForm::
        whereIn("id", $packing_form_ids)->
        get()->
        keyBy("id");

        $product_request_form = ProductRequestForm::find(7347);

        return $product_request_form->getIC();
//        return $this->is_base64("X3N0YXJ0X2NvZGVfMHhhZTI1OGRmZWU4YmJfZW5X2NvZGVf");
//        return $decoded_data = base64_decode();
//        echo $decoded_data;
//        $string = 'Hello, World!';
//        $hex = bin2hex($string);
//        // return $hex; // 48656c6c6f2c20576f726c6421
//        return $data = $this->my_encrypt("ali8812583", $hex);
//        return $this->my_decrypt("BgsDQbxa6r6un/oFARUI48PylbVVVnUk3Akpkc7FEi4=", $hex);
//
//
//        return Script1026Controller::handle(2);
        1 / 0;
        return
            Algorithm4ProductionAllocationToContractorByDownstreamTermination::handel(GoodsKind::find(5), Script::find(26), 2);

        $order = Order::find($x);
        $form = Form::find($y);
        $result_factor = Order::GetFactorFromExitFrom($order, $form);

// اگر تیک سرجمع برای فاکتور خورده است، به ازای هر کالا - بسته بندی
// اگر درجه های متفاوت داشت و قیمت آنها مساوی بود، ردیف ها را یکی می کنیم.

        $list_check = [];// لیست چک کردن آیتم ها
        $result_factor_group = [];
        foreach ($result_factor["form_factor"] as $main_key => $from_factor_item) {
            if (!isset($list_check[$main_key])) {
                // این آیتم جدید است و هنوز چیز مشابه ای برای آن وجود ندارد.
                $result_factor_group[$main_key] = $from_factor_item;
                $list_check[$main_key] = true;


                foreach ($result_factor["form_factor"] as $other_key => $from_factor_item_other) {
                    if (!isset($list_check[$other_key]) &&
                        $from_factor_item_other["item"]->product_id == $from_factor_item["item"]->product_id &&
                        $from_factor_item_other["fea"] == $from_factor_item["fea"]

                    ) {
                        // اگر قیمت کالا، کد کالا و کد بسته بندی یکی بود، ولی درجه ها متفاوت بود، لیست را یکی می کنیم.

                        $result_factor_group[$main_key]["amount"] += $from_factor_item_other["amount"];
                        $result_factor_group[$main_key]["price"] += $from_factor_item_other["price"];
                        $result_factor_group[$main_key]["total_off_price"] += $from_factor_item_other["total_off_price"];
                        $result_factor_group[$main_key]["total_price"] += $from_factor_item_other["total_price"];
                        $result_factor_group[$main_key]["tax_price"] += $from_factor_item_other["tax_price"];
                        $result_factor_group[$main_key]["total_price_with_tax"] += $from_factor_item_other["total_price_with_tax"];

                        $list_check[$other_key] = true;

                    }
                }
            }

        }

        return $result_factor["form_factor"] = $result_factor_group;

        return $result_factor;

        return "OK";
        return "OK";

        echo phpinfo();
//        $finglify = new Finglify();
//     return   Finglify::trans('سیبسیب');
//        return Order::find(671)->code();
//        $bom=BOM::find(305);
//      return  Product\BOM\BOMPermutation::GetSPCodeFromMaterialId($bom,[491,483,483,1403,407]);
//        $form_general_item = FormGeneralItem::find(18);
//        return $machine_allocation_actual_cost = Allocation\MachineAllocationActualCost::createFromFormGeneralItem($form_general_item);

//        Artisan::call("migrate");
//        return "OK";
//       return SmartObject::getContour(SmartObject::find(1));
//      return  $before_order=Order::
//        where("series",2)->max("code");
//return "[«";
//        return Script1016Controller::handle();
//        $form = Form::find(28520);
//        $packing_form_item_id_where_put_in_warehouse = WarehouseProduct::where("form_id", $form->id)->pluck("packing_form_item_id")->toArray();
//        return $packing_form_id_where_put_in_warehouse = PackingFormItem::whereIn("id", $packing_form_item_id_where_put_in_warehouse)->pluck("packing_form_id", "packing_form_id")->toArray();
//
//
//        $new_service = [
//            "serviceId" => 723,
//            "GoodsType" => "نمونه پارچه",
//            "ApproximateValue" => 500000,
//
//            "Weight" => 100,
//            "insuranceName" => "غرامت تا سقف 300 هزار تومان",
//            "CartonSizeName" => 21087,//به کارتن نیاز ندارم
//            "NeedCarton" => false,//به کارتن نیاز ندارم
//
//            "Sender_FristName" => "علیرضا",
//            "Sender_LastName" => "حسینی اربندآبادی",
//            "Sender_mobile" => "09130023474",
//            "Sender_StateId" => "1",
//            "Sender_townId" => "585",
//
//            "Sender_City" => "تهران",
//            "Sender_PostCode" => "8919718588",
//            "Sender_Address" => "تهران - میدان آزادی کنار باقالی فروشی",
//            "Sender_Email" => "jalayegh@gmail.com",
//
//            "Reciver_FristName" => "علیرضا",
//            "Reciver_LastName" => "جلایق",
//            "Reciver_mobile" => "09130656899",
//            "Reciver_StateId" => "1",
//            "Reciver_townId" => "585",
//
//            "Reciver_City" => "یزد",
//            "Reciver_PostCode" => "8919718588",
//            "Reciver_Address" => "یزد - بلوار نواب صفوی کوچه قدس",
//            "Reciver_Email" => "jalayegh@gmail.com",
//
//            "IsCOD" => 1,
//            "HasAccessToPrinter" => true,
//
//            "boxType" => "نمی دانم",
//            "Count" => 1,
//            "orderSource" => 13,
//            "refrenceNo" => 100,
//
//            "SenderLat" => "54.36398612",
//            "SenderLon" => "31.88789443",
//
//            "ReciverLat" => "54.35581611",
//            "ReciverLon" => "31.88680126",
//
//            "IpAddress" => "109.125.144.51",
//
//
//        ];
//        $client = Posttex::client();
//        $response = $client->request('post', 'checkout/newOrder',
//            [
//                'headers' => ["token" => Posttex::getToken(),],
//                'query' => $new_service
//            ]);
//        $body = $response->getBody();
//
//        return $body;
//
//
//        $ip = Setting::getStringValue("smart_object_server_ip");
//        $port = Setting::getStringValue("smart_object_server_port");
//        $client = new \GuzzleHttp\Client();
//        $request = $client->get("$ip:$port/get_smart_object_weight/" . 1);
//        return $response = $request->getBody();
//
//        return SpecialLicense::find(16)->ActionAfterConfirm();
//        $agent = new Agent();
//        echo "platform " . $agent->platform() . "<br/>";
//        echo "browser " . $agent->browser() . "<br/>";
//        echo "version " . $version = $agent->version($agent->browser()) . "<br/>";
//        echo "version platform " . $version = $agent->version($agent->platform()) . "<br/>";
//
//        echo $_SERVER['HTTP_USER_AGENT'] . "<<br>";
//        return self::DeviceInfo();
//
//        $date = "2023-11-23";
//        $worker = Worker::find(127);
//        $operation = Script1023Controller::calculate($worker, $date);
//        $result = DailyShiftOperation::CalculateForDay($worker, $date);
//        $intervals = $result["intervals"];
//        return view("hr.personal.shift_work_day.calculator_log", compact("intervals", "worker", "date", "operation"));
//
//        1 / 0;
//        $machine_allocation_modification_form_ids = [
//            890,
//        ];
//
//        $list2 = [];
//        $machine_allocation_modification_form_list =
//            MachineAllocationModificationForm::whereIn("id", $machine_allocation_modification_form_ids)->
//            orderByDesc("id")->
//            get();
//        foreach ($machine_allocation_modification_form_list as $item) {
//            $before_item = MachineAllocationModificationForm::join("machine_allocation_modifications", "machine_allocation_modifications.id", "machine_allocation_modification_id")->
//            where("machine_id", $item->machine_allocation_modification->machine_id)->
//            where("machine_allocation_modification_form.id", "<", $item->id)->
//            where("product_id", $item->product_id)->
//            first();
//            if ($before_item) {
//                $list2[] = $item;
//            }
//        }
//        $k = 1;
//        foreach ($list2 as $machine_allocation_modification_form) {
//            $k++;
//            if ($k > 15) {
//                return "OKKK";
//            }
//            // محاسبه مصرف واقعی و تغییر درجه داده شده و ضایعات مواد اولیه
//            $result_actual_consumption = self::ActualConsumption($machine_allocation_modification_form->machine_allocation_modification, $machine_allocation_modification_form);
//
//            if (!$result_actual_consumption["result"]) {
//
//                return "error for " . $machine_allocation_modification_form->id;
//            } else {
//
//                $machine_allocation_modification_form->actual_consumption_status_id = 6021302; //مصرف واقعی محاسبه شد..
//                $machine_allocation_modification_form->save();
//            }
//
//            $result_actual_consumption["log"]["message"] = isset($result_actual_consumption["error"]) ? $result_actual_consumption["error"] : "";
//            $result_actual_consumption["log"]["result"] = $result_actual_consumption["result"];
//            $jsn_data_list = JsonDataList::create([
//                "other_id" => $machine_allocation_modification_form->id,
//                "message_type_id" => 250,
//                "data" => json_encode($result_actual_consumption["log"])
//            ]);
//
//            $machine_allocation_modification_form->json_data_id = $jsn_data_list->id;
//            $machine_allocation_modification_form->save();
//
//            echo $machine_allocation_modification_form->id;
//        }
//        return "OK";
//        return Carrier::StaticSetEmpty(Carrier::find(735), 4);
//        return "";//   $date = Carbon::now()->addDay(-$x)->format("Y-m-d");
//        $worker = Worker::find(118);
//        return $operation = Script1023Controller::calculate($worker, "2023-11-02");
//        echo $_SERVER['HTTP_USER_AGENT'] . "\n\n";
//
//        $browser = get_browser($_SERVER['HTTP_USER_AGENT'], true);
//        print_r($browser);
//        return Script1023Controller::handle();
//        $date = Carbon::now()->addDay(-$x)->format("Y-m-d");
//        $worker = Worker::find($y);
//        return $operation = Script1023Controller::calculate($worker, $date);
//        return SpecialLicense::find(34)->ActionAfterConfirm();
//        return $log_list = MachineAllocationLog::where([
//            "event_id" => 5310109 // خاتمه یافته شدن
//        ])->
//        whereNotNull("contractor_id")->
//        where("id", ">", 9288)->
//        orderBy("id")->
//        take(100)->get();
//        foreach ($log_list as $item) {
//
//            echo $item->id . "=>" . ($item->machine_allocation_id ?? "***") . "<br/>";
//
//        }
//        return "OK";
//        1 / 0;
//        $date = Carbon::now()->addDay(-$x)->format("Y-m-d");
//        $worker = Worker::find($y);
//        return $operation = Script1023Controller::calculate($worker, $date);
//
//        return Str::random(50);;
//        return $machine_allocation_modification_form = MachineAllocationModificationForm::find(1015);
////        $machine_allocation_modification = MachineAllocationModification::find(468);
////      return  $calculated_actual_consumption = Allocation\MachineAllocationActualConsumption::
////        join("allocations", "allocation_id", "allocations.id")->
////        whereIn("allocation_id", [])->
////        where("material_id", $machine_allocation_modification_form->product_id)->
////        groupBy("allocation_id")->
////        selectRaw("sum(calculated_amount_till_now) as sum_amount,allocation_id")->
////        pluck("sum_amount", "allocation_id")->
////        toArray();
////
////        $result_actual_consumption = Script1012Controller::ActualConsumption($machine_allocation_modification_form->machine_allocation_modification, $machine_allocation_modification_form);
////        $result_actual_consumption["log"]["message"] = isset($result_actual_consumption["error"]) ? $result_actual_consumption["error"] : "";
////        $result_actual_consumption["log"]["result"] = $result_actual_consumption["result"];
////        $jsn_data_list = JsonDataList::create([
////            "other_id" => $machine_allocation_modification_form->id,
////            "message_type_id" => 250,
////            "data" => json_encode($result_actual_consumption["log"])
////        ]);
//
//        $machine_allocation_modification_form->json_data_id = $jsn_data_list->id;
//        $machine_allocation_modification_form->save();
//
//        return "OK";
//
//
//        $packing_form = PackingForm::find(60287);
//        foreach ($packing_form->items as $packing_form_item) {
//            // به ازای هر کالا تخصیص یک ردیف در نظر می گیریم.
//            if (isset($packing_form_item->production_form_item->allocation_id) &&
//                !isset($machine_allocation_consumption[$packing_form_item->production_form_item->allocation_id][$packing_form_item->product_id])) {
//
//                $machine_allocation_consumption["material"]
//                [$packing_form_item->production_form_item->allocation_id]
//                [$packing_form_item->product_id]
//                    = CurrentMachineInput::
//                where("allocation_id", $packing_form_item->production_form_item->allocation_id)->
//                groupBy("material_id")->
//                selectRaw("current_machine_inputs.*,sum(number*amount*percent_of_use/100) as predictive_amount")->
//                get()->keyBy("material_id");
//
//
//                $machine_allocation_consumption["allocation"]
//                [$packing_form_item->production_form_item->allocation_id]
//                [$packing_form_item->product_id] =
//                    MachineAllocationActualConsumption::
//                    where("allocation_id", $packing_form_item->production_form_item->allocation_id)->
//                    get()->keyBy("material_id");
//
//                $machine_allocation_consumption["product"][$packing_form_item->product_id] = $packing_form_item->product;
//            }
//        }
//
//        return $machine_allocation_consumption;
//        return MachineAllocationActualConsumption::
//        where("allocation_id", 5825)->
//        get()->keyBy("material_id");
//        $date = Carbon::now()->addDay(-$x)->format("Y-m-d");
//        $worker = Worker::find($y);
//        return $operation = Script1023Controller::calculate($worker, $date);
//
//        return $result_weight = PackingForm::UpdateWeight(PackingForm::find(59087));
//        if ($result_weight["result"]) {
//            $sub_packing_form->weight = $result_weight["weight"];;
//            $sub_packing_form->gross_weight = $result_weight["gross_weight"];
//            $sub_packing_form->save();
//        }
//        $date = Carbon::now()->addDay(-$x)->format("Y-m-d");
//        $worker = Worker::find($y);
//
//        return $operation = Script1023Controller::calculate($worker, $date);
//
//        return Script1017Controller::handle();
//        return $result_check_max = self::CheckProductionTerminate(MachineAllocation::find(8212), "check_max", 0);
//
//
//        return $production_amount = ProductionFormItem::
//        where("allocation_id", 8768)->
//        sum("final_amount");
//        return $general_item_amount = FormGeneralItem::
//        join("forms", "forms.id", "form_id")->
//        where([
//            "machine_allocation_id" => 8212,
//        ])->
//        whereIn("form_general_item.status_id", [5002003, 5002002])->
//        where("forms.status_id", "!=", 500000100)-> // عدم تایید ها را حذف می کنیم.
//        sum("form_general_item.amount");
//        return Report1010MachineLog::where("id", ">", 0)->count();
////        return "OK";
////        $warps_is_in_warehouse = Warps::warpsExistInWarehouse(null, ProductRequestForm::find(6052));
//        return AbsenceController::CancelRequest(Worker::find(111), Carbon::now());
//        return SpecialLicense::UpdateSpecialLicense(SpecialLicense::find(36));
//      return  SpecialLicenseType::AllowCreate(SpecialLicenseType::find(5), TransportItem::find(3313));
//        return Script1008Controller::handle();
//        $leave_list = LeaveOvertime::join("leave_overtime_types", "leave_overtime_types.id", "leave_overtime_type_id")->
//        whereIn("status_id", [4630003, 4630006])->
//        where("leave_overtime_group_id", 1)->
//        where("start_datetime", "<=", Carbon::now())->
//        where("end_datetime", ">=", Carbon::now())->
//        where("user_id", 109)->
//        select("leave_overtimes.*")->
//        get();
//        foreach ($leave_list as $leave_item) {
//            $leave_item->status_id = 4630007; //انجام شده
//            $leave_item->save();
//            echo $leave_item->id;
//        }
//        return "OK";
//        return Script1025Controller::UpdateLeaveStatus(Worker::find(109));
//        return Script1013Controller::CheckFroError(Script::find(13));
//        $post_id = Setting::getStringValue("send_sms_in_create_allocation_machine_to_post_id1");
//
//        $post = Post::find($post_id);
//        if (!$post) {
//            return;
//        }
//        return $users = PostUser::getCurrentUserByShiftWorkAndLeaveOvertimeByPostId("worker", $post_id);
//
//        return "OK";
//
//        return $special_license = SpecialLicense::UpdateSpecialLicense(SpecialLicense::find(31));
//        return Script1023Controller::handle(-$x);
//        return FormGeneralItem::find(13)->amount == 1843.90;
//   return   Allocation::find(5958)->before_allocation(1);
//    return    Allocation::getDifferentTowAllocation( Allocation::find(5872), Allocation::find(5958), "Fabric_Raw" );
//return Script1025Controller::handle($x);
//        $date = Carbon::now()->addDay(-$x)->format("Y-m-d");
//        $worker = Worker::find(100);
//        $result = DailyShiftOperation::CalculateForDay($worker, $date);
//        $intervals = $result["intervals"];
//      //return  UserOperation::CalculateFromDailyShiftOperation( $worker, $date, $result["intervals"] );
//        $operation = Script1023Controller::calculate($worker, $date);
//
//        return view("hr.personal.shift_work_day.calculator_log", compact("intervals", "worker", "date", "operation"));
//        return "OK";
//
//        return FormItem::where([
//            "id" => 0,
//            "product_id" => 0,
//            "packing_type_id" => 0,
//            "degree_id" => 0,
//        ])->
//        sum("amoun");
//        $financial_software_trans_kind = FinancialSoftwareTransKind::find(108);
//        $financial_software_form = FinancialSoftwareTransferForm::find(1166);
//        $form = Form::find(1655);
//        return Script1013Controller::SaleInvoice($form, $financial_software_trans_kind, $financial_software_form);
//        return OldSeeder\MessageTypeSeeder::class;
//        return Script1024Controller::handle();;
//        $date = Carbon::now()->addDay(-$x)->format("Y-m-d");
//        $worker = Worker::find(100);
//        return Script1023Controller::calculate($worker, $date);
//
//        return $sum_2 = NewShiftWorkDay::
//        where("shift_id", 4)->
//        where("shift_work_id", 1)->
//        where("month", ">", 0)->
//        whereNotIn("work_day_type_id", [2, 4])->
//        where("month", 6)->
//        get();
//
//        1 / 0;
//        $production = Production::find(4951);
//        return $line_production = LineProductStation::where("product_id", $production->product_id)->first();
//        return
//        $jalaliDate = "1399/08/06";
//return Jalalian::fromFormat('Y/m/d', $jalaliDate)->toCarbon();
//
//        return Script1008Controller::handle();
//   return Script1012Controller::handle();
//     $list=   Product::where("code","")->
//        orWhere("code","01/01/04/01/1")->
//        orWhere("code","01/01/04/01/2")->
//        orWhere("code","01/01/04/01/3")->
//        orWhere("code","01/01/04/01/4")->
//        orWhere("code","01/01/04/03")->
//        orWhere("code","01/01/04/05")->
//        orWhere("code","01/01/04/06")->
//        orWhere("code","01/01/04/08")->
//        orWhere("code","01/01/04/09")->
//        orWhere("code","01/01/04/11")->
//        orWhere("code","01/01/04/20")->
//        orWhere("code","01/01/04/21")->
//        orWhere("code","01/01/04/22")->
//        orWhere("code","01/01/04/23")->
//        orWhere("code","01/01/04/26")->
//        orWhere("code","01/01/04/27")->
//        orWhere("code","01/01/04/28")->
//        orWhere("code","01/01/04/53")->
//        orWhere("code","01/01/04/56")->
//        orWhere("code","01/01/04/57")->
//        orWhere("code","01/01/04/61")->
//        orWhere("code","01/01/04/64")->
//        orWhere("code","01/01/04/65")->
//        orWhere("code","01/01/04/66")->
//            pluck("id")->toArray();
//
//   $product_ids= BOMItem::whereIn("material_id",$list)->
//
//         pluck("product_id")->toArray();
//
//   $list= LineProductStation::whereIn("product_id",$product_ids)->
//        where("machine_type_id",31)->
//   get();
//    foreach ($list as $item){
//        $item->status_id=1210;
//        $item->save();
//    }
//return "[«";

//return     $sum_production_form_item_amount = ProductionFormItem::where( "allocation_id", 4368 )->get();

//        foreach (MachineAllocationModification::all() as $item) {
//            $item->warehouse_id = $item->machine->warehouse_id;
//            $item->save();
//        }
//        return "OK V1.6.22";
//        foreach ( MachineAllocationModification::all() as $item ) {
//            $item->warehouse_id = $item->machine->warehouse_id;
//            $item->save();
//        }


//        $machine_allocation_modification=MachineAllocationModification::find(97);
// return   $machine_allocation_modification->packing_forms()->groupBy( "product_id" )->get();
//  return Script1012Controller::handle();
// $warehouse = Warehouse::find( 9 );

// return Warehouse::warehouse_handling_need( $warehouse, Product::find( 1403 ) );
        $dbName = "_AccXP_arman1402";


        $n = 123.456;


        return $this->remove_discount($n, 2);
        $packing_form = PackingForm::find(55740);
        $result_weight = PackingForm::UpdateWeight($packing_form);;
        if ($result_weight["result"]) {

            $packing_form->weight = $result_weight["weight"];;
            $packing_form->gross_weight = $result_weight["gross_weight"];
            $packing_form->save();
        }

        return $packing_form;

        Script1023Controller::handle(-69);
        Script1023Controller::handle(-68);
        Script1023Controller::handle(-67);
        Script1023Controller::handle(-66);
        Script1023Controller::handle(-65);
        Script1023Controller::handle(-64);
        Script1023Controller::handle(-63);
        Script1023Controller::handle(-62);

        return Script1023Controller::handle(-61);

//return 840/60;
        $date = "2023-07-15";
        $worker = Worker::find(112);
        $intervals = DailyShiftOperation::CalculateForDay($worker, $date);

//        $t1                     = new DailyShiftOperation( 1690641000 ,1690659000 );
//        $t1->start = 1690641000;
//        $t1->end = 1690659000;
//        $t1->shift_work_day_id = 12345;

// return     $intervals = DailyShiftOperation::BreakIntervals( $intervals, $t1 );
//

//  UserOperation::CalculateFromDailyShiftOperation($worker,$date,$intervals);


        $operation = UserOperation::where("user_id", $worker->id)->
        where("current_date", $date)->first();
        $date = jdate(Carbon::parse($date)->timestamp)->format('Y/m/d');

        return view("hr.personal.daily_shift_operation.log", compact("worker", "intervals", "date", "operation"));


        $age = array("0" => 20, "1" => 14, "2" => 45, "-5" => 35);
        ksort($age);
// print_r($age);
// return 0;
        $t1 = new TimeInterval (0, 24);
        $t1->min = 0;
        $t1->max = 24;
        $intervals[] = $t1;


        $t1 = new TimeInterval (1690641000, 1690659000);

        $t1->shift_work_day_id = 1;

        $intervals = TimeInterval::BreakIntervals($intervals, $t1);

        $t1 = new TimeInterval (8, 16);
        $t1->shift_work_type_id = 2;

        $intervals = TimeInterval::BreakIntervals($intervals, $t1);
        $t1 = new TimeInterval (16, 24);
        $t1->shift_work_type_id = 3;

        $intervals = TimeInterval::BreakIntervals($intervals, $t1);


        $intervals = TimeInterval::BreakIntervals($intervals, $t1);
        $t1 = new TimeInterval (7, 15);
        $t1->shift_work_day_id = 19141;


        $intervals = TimeInterval::BreakIntervals($intervals, $t1);
        $t1 = new TimeInterval (5, 20);
        $t1->leave_id = 362514;

        return $intervals = TimeInterval::BreakIntervals($intervals, $t1);

        return "OK";

        return TimeInterval::ClearInterval($intervals);

        return $intervals = $this->BreakIntervals($intervals, new TimeInterval (125, 175));
        $worker = Worker::find(1);
        $user_entry = UserEntryLog::where("user_id", $worker->id)->orderByDesc("id")->first();

        return Carbon::now()->timestamp;

        return Script1012Controller::handle();

        return Contractor::find(1)->nextStatusForInputForm(0, $has_general_item = true);

//        $form_general_item=FormGeneralItem::find(9);
//     return   $form_general_item->machine_allocation->production_id;
        return Script1008Controller::handle();

        return $post_ids = self::getMachineListWhereWorkerIsOperator(Worker::find(Auth::id()), "count", "post_ids", "leaves_user_ids");


        $waiting_for_actual_consumption_list = MachineAllocationModificationForm::
        join("machine_allocation_modifications", "machine_allocation_modifications.id", "machine_allocation_modification_id")->
        where([
            "input_form_status_id" => 6021202, // پردازش انجام شده
            "actual_consumption_status_id" => 6021301, //در انتظار محاسبه مصرف واقعی
            "machine_allocation_modifications.status_id" => 6021003 // خاتمه یافته
        ])->
        select("machine_allocation_modification_form.*")->
        get();


        foreach ($waiting_for_actual_consumption_list as $machine_allocation_modification_form) {
//return $machine_allocation_modification_form;
            // اگر برای برگشت فرم ورود به انبار وجود دارد، باید فرم تایید شده باشد یا اصلا فرم ورود وجود نداشته باشد.
            if (
                ($machine_allocation_modification_form->input_form && $machine_allocation_modification_form->input_form->status_id == 500000200)
                || !$machine_allocation_modification_form->input_form
            ) {

                $correction_form_input = null;
                $correction_form_output = null;

                // بررسی درست بودن تراکنش هایی اصلاحی و اینکه مقدار موجودی انبارک صفر شده باشد.
                // اگر موردی بود که مشکل داشت، مصرف های واقعی را هم تا زمان درست شدن، بررسی نمی کند.
                $inventory_packing_form_ids = WarehouseProduct::getProductInventoryList(
                    [$machine_allocation_modification_form->product_id],
                    null,
                    $machine_allocation_modification_form->machine_allocation_modification->machine->warehouse_id ?? 0,
                    null,
                    null,
                    10,
                    "packing_form_item_id"
                );

                $all_packing_form_item_inventory_is_zero = true;

                // آیا در زمان محاسبه مصرف واقعی چک شود که موجودی همه بسته بندی های کالا در انبارک کاملا صفر شده باشد،
                if ($machine_allocation_modification_form->machine_allocation_modification->check_inventory_for_calculate_actual_consumption) {

                    // بررسی اینکه موجودی انبارک برای کالای موجود در فرم کاملا صفر شده باشد.
                    foreach ($inventory_packing_form_ids as $packing_form_item_id => $inventory) {

                        // هر چند ساعت یک بار چک می کند اگر درست نشده، پیامک ارسال می کند.

                        if ($inventory == 0) {
                            // مشکلی نیست ادامه بده
                            continue;
                        } else if (abs($inventory) <= 0.00001) {


                            // به دلیل رند شدن این اتفاق اوفتاده است و باید بسته بندی آن را پیدا کنیم و تراکنش اصلاحی متفرقه بزنیم.
                            $trans_kind = TransKind::find(
                                $inventory > 0 ?
                                    11 : // خروج متفرقه
                                    5 //  ورود متفرقه
                            );


                            $packing_form_item = PackingFormItem::find($packing_form_item_id);
                            if (!$packing_form_item) {
                                $message = "آیتم بسته بندی با شناسه" . $packing_form_item_id . " در دیتابیس وجود ندار.";
                                $all_packing_form_item_inventory_is_zero = false;
                            }


                        } else {
                            $packing_form_item = PackingFormItem::find($packing_form_item_id);

                            return $packing_form_item->code . "--$inventory";

                        }

                    }
                }

                // اگر جایی باید تراکنش اصلاحی متفرقه ثبت شود، همه در این فرم ها ثبت می شوند.
                if (isset($correction_form_input)) {
                    event(new PutInWarehouseEvent($correction_form_input, null, null, false));

                    $machine_allocation_modification_form->other_correction_input_form_id = $correction_form_input->id;

                }
                if (isset($correction_form_output)) {
                    event(new PutInWarehouseEvent($correction_form_output, null, null, false));

                    $machine_allocation_modification_form->other_correction_output_form_id = $correction_form_output->id;

                }


                if (!$all_packing_form_item_inventory_is_zero) {
                    //  اگر مقدار نهایی حداقل یکی از بسته بندی ها غیر صفر است، مقدار واقعی محاسبه نشود و ادامه دهد.
                    continue;
                }

//                // محاسبه مصرف واقعی و تغییر درجه داده شده و ضایعات مواد اولیه
//                $result_actual_consumption = self::ActualConsumption( $machine_allocation_modification_form->machine_allocation_modification, $machine_allocation_modification_form );
//
//                if ( ! $result_actual_consumption["result"] ) {
//                    $message = $result_actual_consumption["error"];
//                    if ( self::TimeForSms() ) {
//                        Script::SendSmd( $script, $machine_allocation_modification_form->machine_allocation_modification->id, $message, self::$user_id );
//                    }
//                } else {
//
//                    $machine_allocation_modification_form->actual_consumption_status_id = 6021302; //مصرف واقعی محاسبه شد..
//                    $machine_allocation_modification_form->save();
//                }
//
//                $result_actual_consumption["log"]["message"]        = isset( $result_actual_consumption["error"] ) ? $result_actual_consumption["error"] : "";
//                $result_actual_consumption["log"]["result"]         = $result_actual_consumption["result"];
//                $jsn_data_list                                      = JsonDataList::create( [
//                    "other_id"        => $machine_allocation_modification_form->id,
//                    "message_type_id" => 250,
//                    "data"            => json_encode( $result_actual_consumption["log"] )
//                ] );
//                $machine_allocation_modification_form->json_data_id = $jsn_data_list->id;
//                $machine_allocation_modification_form->save();


            } else {

            }
        }

        return "PL";

//        $list = PackingFormItem::
//        join( "packing_forms", "packing_form_id", "packing_forms.id" )->
//        where( [
//            "packing_forms.status_id" => 7007019,
//        ] )->
//        where( "final_amount", ">", 0 )->
//        select( "packing_form_item.*" )->
//        get();
//
//        foreach ( $list as $item ) {
//            $item->final_amount = 0;
//            $item->save();
//        }


        $list_zero = PackingForm::where([
            "status_id" => 7007019,
        ])->
        where("weight", "!=", 0)->
        get();
        foreach ($list_zero as $packing_form) {
            $result = PackingForm::UpdateWeight($packing_form);
            if ($result["result"]) {
                $packing_form->weight = $result["weight"];
                $packing_form->gross_weight = $result["gross_weight"];
                $packing_form->save();
            }

        }

        return "OK";

        return Script1007Controller::handle(315, true);

        return session()->forget("amount");
        $leave_overtime = LeaveOvertime::find(533);
        $list = LeaveOvertimeConfirmation::
        where([
            "leave_overtime_id" => $leave_overtime->id,
            "status_id" => 4630002 // در انتظار تایید مافوق
        ])->
        get();

        $replace_count = LeaveOvertimeConfirmation::where([
            "leave_overtime_id" => $leave_overtime->id,
            "status_id" => 4630001 // در انتظار تایید جانشین
        ])->
        count();
// اگر حداقل یک جانشین وجود دارد که تایید نشده است، پیامک برای مدیر ارسال نشود.
        if ($replace_count > 0) {
            return 5;
        }

        return "OK";

        return PackingForm::join("packing_form_item", "packing_forms.id", "packing_form_id")->
        where("status_id", 7007019)->
        where("final_amount", "!=", 0)->get();


        return Script1012Controller::handle();

        return ProductionChannel::UpdateProductionChannel(ProductionChannel::find(34));

        return $current_time = Carbon::now()->timestamp;
        $packing_form_item_ids = FormItem::
        where("form_id", 11289)->
        pluck("packing_form_item_id")->
        toArray();

        $packing_form_item_ids[] = -1;

        return PackingFormItemImportantStatus::whereIn("packing_form_item_i", $packing_form_item_ids)->
        update(["exit_form_id" => 11289]);

        $data[54988]["consumed_status_id"] = 6021103;
        $packing_form_ids = [54988];

        return GeneralMaterialReturnToWarehouseController::AddNewModificationForPacking(Machine::find(319), $packing_form_ids, $data, Allocation::find(5219));


        return Script1012Controller::handle();
        $inventory_packing_form_ids = WarehouseProduct::getProductInventoryList(
            [514],
            null,
            32,
            null,
            null,
            10,
            "packing_form_item_id"
        );

        foreach ($inventory_packing_form_ids as $packing_form_item_id => $inventory) {

            // هر چند ساعت یک بار چک می کند اگر درست نشده، پیامک ارسال می کند.

            if ($inventory == 0) {
                // مشکلی نیست ادامه بده
                continue;
            } else if (abs($inventory) <= 0.00001) {


                // به دلیل رند شدن این اتفاق اوفتاده است و باید بسته بندی آن را پیدا کنیم و تراکنش اصلاحی متفرقه بزنیم.
                $trans_kind = TransKind::find(
                    $inventory > 0 ?
                        11 : // خروج متفرقه
                        5 //  ورود متفرقه
                );

                echo $inventory . "<br/>";


            }
        }

        return "OK";

        return Script1022Controller::handle();
        $machine = Machine::find(319);
        $allocation = Allocation::find(4551);
        $before_warp_input_list = CurrentMachineInput::
        where(["machine_id" => $machine->id, "allocation_id" => ($allocation->id ?? -1)])->
        where("goods_kind_id", 3)->
        orderBy("goods_kind_id")->
        orderBy("input_line_code")->
        get();

        return BeginChangeWarpsController::AddModification($before_warp_input_list, $machine, $allocation);


        return $result_weight = PackingForm::UpdateWeight(PackingForm::find(51312));
        $request["token"] = "3|GtR3f8ZuUpvpGp5dqlXgn8rBRiuNyLOy6vX99K20";

        return $request->user()->currentAccessToken() ? "ok" : "error";
        $user = \App\Models\User::find(1);

        return $user->createToken("API TOKEN")->plainTextToken;
//        $list = WarehouseProduct::where( [
//            "trans_kind"                   => 32,
//            "financial_software_status_id" => 5103950
//        ] )->
//        groupBy( "form_id" )->
//        get();
//        foreach ( $list as $item ) {
//            $item->form->trans_kind = 32;
//            $item->form->save();
//            echo $item->form->code . "<br/>";
//        }
//
//        return "OK";

        return Script1022Controller::handle();

        return $list_classification = Product::
        leftJoin("goods_kind_classification_product", "products.id", "goods_kind_classification_product.product_id")->
        where("goods_kind_id", 5)->
        whereNull("goods_kind_classification_option_id")->
        select("products.caption", "products.code", "products.id")->
        get();

        return Script1013Controller::Review();


        return "OK";
// انتقال اطلاعات  مصرف واقعی
        $list = CurrentMachineInput::whereNotNull("actual_amount")->get();
        foreach ($list as $item) {

            $current_machine_input_list = CurrentMachineInput::where([
                "allocation_id" => $item->allocation_id,
                "material_id" => $item->material_id
            ])->get();

            $sum_amount_required = 0;
            $actual_amount = 0;
            $change_degree_amount = 0;
            foreach ($current_machine_input_list as $current_machine_input) {
                $sum_amount_required += CurrentMachineInput::getConsumedAmount(
                    $current_machine_input->amount,
                    $current_machine_input->number,
                    $current_machine_input->percent_of_use
                );

                $actual_amount += $current_machine_input->actual_amount;
                $change_degree_amount += $current_machine_input->change_degree_amount;

            }
            if (Allocation\MachineAllocationActualConsumption::where([
                "allocation_id" => $item->allocation_id,
                "product_id" => $item->product_id,
                "material_id" => $item->material_id,
            ])->first()) {


                Allocation\MachineAllocationActualConsumption::
                where([
                    "allocation_id" => $item->allocation_id,
                    "product_id" => $item->product_id,
                    "material_id" => $item->material_id,
                ])->
                update([
                    "allocation_id" => $item->allocation_id,
                    "product_id" => $item->product_id,
                    "material_id" => $item->material_id,
                    "predictive_amount" => $sum_amount_required,
                    "actual_amount" => $actual_amount,
                    "change_degree_amount" => $change_degree_amount,
                    "waste_amount" => $item->waste_amount,
                    "calculated_amount_till_now" => $item->allocation->getAllocationAmount()
                ]);
            }
        }

        return "OK";

//        for ( $k = 1; $k < 30; $k ++ ) {
//            $production_channel = ProductionChannel::find( $k );
//            ProductionChannel::UpdateProductionChannel( $production_channel, null, null, true );
//        }
//
//        return $k;
//        foreach ( Allocation\MachineAllocationProductionChannel::get() as $item){
//            $allocation_item=$item->allocation->items()->first();
//            $item->amount=$allocation_item->allocation_amount- $item->amount;
//            $item->save();
//        }

//
//        $machine       = Machine::find( 307 );
//        $warps_request = ProductRequestForm:: getLatestRequestForm( $machine->warehouse_id, 40, 3 );
//        $i             = 1;
//        foreach ( $warps_request->items as $item ) {
//
//            /********************/
//
//            $result                  = Warps::getRemainingAmountOfWarps( $warps_request, $item, null, $item->product_id ?? null );
//            $warps_amount_list[ $i ] = $result;
//            $i ++;
//        }
//
//        return $warps_amount_list;
// V1019
//پارچه خام
//        $product_ids = Product::where( "goods_kind_id", 4 )->pluck( "id", "id" )->toArray();
//
//        BOMItem::join( "products", "products.id", "material_id" )->
//        whereIn( "product_id", $product_ids )->
//        where( "goods_kind_id", 3 )->
//        update( [ "consumption_percent_of_production_channel" => 1 ] );
//
//        // چله
//        $product_ids = Product::where( "goods_kind_id", 3 )->pluck( "id", "id" )->toArray();
//
//        BOMItem::join( "products", "products.id", "material_id" )->
//        whereIn( "product_id", $product_ids )->
//        where( "goods_kind_id", 2 )->
//        update( [ "consumption_percent_of_production_channel" => 1 ] );


//        return "ok";
        foreach (Machine::all() as $machine) {
            if ($machine->warehouse) {
                $machine->warehouse->belonging_to_id = $machine->id;
                $machine->warehouse->save();
            }
        }


        return "OK";
//        foreach ( Machine::where( "active_status_id", 1200 )->get() as $machine ) {
//            $allocation = $machine->getCurrentAllocation();
//            if ( $allocation ) {
//                $allocation_item = $allocation->items()->first();
//                $result          = ProductionChannel::CheckProductionChannelForAllocation( $machine, $allocation_item->production, 1 );
//                echo "<br/>machine:" . $machine->number_code . "=>";
//                if ( ! $result["result"] ) {
//                    if ( isset( $result["error"] ) ) {
//
//                        echo $result["error"];
//
//                    } else {
//                        echo "machine:" . $machine->number_code;
//                        $production_channel_type = $result["production_channel_type"];
//
//                        // به صورت اتوماتیک یک کانال ایجاد می کنیم.
//                        GeneralProductionChannelController:: create_production_channel( $machine, $production_channel_type, $production_channel_type->min_capacity, $production_channel_type->max_capacity,true );
//                    }
//                }
//                echo "<br/>";
//            }
//        }
////
//        return "OK";

        $warps_request_list = [];
        $report_list = [];
        foreach (Machine::where("active_status_id", 1200)->get() as $machine) {
            $warps_request = ProductRequestForm:: getLatestRequestForm($machine->id, 10);
            if ($warps_request) {
                $i = 1;
                // بررسی مقدار باقی مانده چله با توجه به قطب ها
                foreach ($warps_request->items as $item) {

                    $allocation = $machine->getCurrentAllocation();
                    if (!$allocation) {
                        $allocation = Allocation::
                        where("machine_id", $machine->id)->
                        where("status_id", 5310020)->orderByDesc("id")->first();;

                    }
                    $allocation_item = $allocation->items()->first();
                    // چله کارت درحال رزرو را به دست می آوریم و مقدار باقی مانده را با آن مقایسه می کنیم.
                    /*********************************/
                    $current_warps_bom = BOMItem::join("products", "material_id", "products.id")->
                    where("product_id", $allocation_item->production->product_id)->
                    where("goods_kind_id", 3)->
                    orderBy("material_id")->
                    first();
                    /********************/

                    $result = self::getRemainingAmountOfWarps($warps_request, $item, null, $current_warps_bom->material_id ?? null);
                    $warps_amount_list[$i] = $result;
                    $i++;
                    $warps_count = $warps_request->items->count();
                    $warps_request_list[$machine->id] = $warps_amount_list;
                    $warps_request_list[$machine->id]["packing_form_code"] = PackingFormItem::where("id", $warps_amount_list[1]["packing_form_item_id"])->first()->packing_form;
                    $report_list[] = [
                        "machine_code" => $machine->number_code,
                        "machine_caption" => $machine->caption,
                        "amount" => $warps_amount_list[1]["input_amount"],
                        "woven_amount" => $warps_amount_list[1]["consumed_amount"],
                        "packing_form" => $warps_request_list[$machine->id]["packing_form_code"]->code,
                        "carrier_code" => $warps_request_list[$machine->id]["packing_form_code"]->carrier->code,
                        "carrier_caption" => $warps_request_list[$machine->id]["packing_form_code"]->carrier->carrier_type->caption,
                        "product_code" => $warps_request_list[$machine->id]["packing_form_code"]->items()->first()->product->code,
                        "product_caption" => $warps_request_list[$machine->id]["packing_form_code"]->items()->first()->product->caption,
                        "lot" => $warps_request_list[$machine->id]["packing_form_code"]->items()->first()->lot_number->code,
                    ];
                }

            }
        }

        return $report_list;

        return "OK";

        return Script1007Controller::handle(320);
        $machine = Machine::find(320);

//        return Warps::updateCarrierInCurrentInputOutputBand( $machine, "updateProductionChannel" );
//        foreach ( Machine::where( "active_status_id", 1200 )->get() as $machine ) {
//            $allocations = $machine->ReserveAllocation()->get();
//            echo "<br/>machine:" . $machine->number_code . "=>";
//            foreach ( $allocations as $allocation ) {
//                if ( $allocation ) {
//                    $allocation_item = $allocation->items()->first();
//                    $result          = ProductionChannel::CheckProductionChannelForAllocation( $machine, $allocation_item->production, 1 );
//
//                    if ( ! $result["result"] ) {
//                        if ( isset( $result["error"] ) ) {
//
//                            echo "<br/>" . $result["error"];
//
//                        }
//                    }
//                    echo "<br/>";
//                }
//            }
//        }
//
//        return "OK";
        foreach (Machine::where("active_status_id", 1200)->get() as $machine) {
            $allocation = $machine->getCurrentAllocation();
            if ($allocation) {
                $allocation_item = $allocation->items()->first();
                $result = ProductionChannel::CheckProductionChannelForAllocation($machine, $allocation_item->production, 1);
                echo "<br/>machine:" . $machine->number_code . "=>";
                if (!$result["result"]) {
                    if (isset($result["error"])) {

                        echo $result["error"];

                    } else {
                        echo "machine:" . $machine->number_code;
                        $production_channel_type = $result["production_channel_type"];

                        // به صورت اتوماتیک یک کانال ایجاد می کنیم.
                        GeneralProductionChannelController:: create_production_channel($machine, $production_channel_type, $production_channel_type->min_capacity, $production_channel_type->max_capacity);
                    }
                }
                echo "<br/>";
            }
        }

        return "OK";

        $list = CurrentMachineInput::whereNull("consume_warehouse_id")->get();
        foreach ($list as $item) {

            $item->consume_warehouse_id = $item->machine->warehouse_id;
            $item->save();
        }

        return "OK";

        return Script1012Controller::handle();

        $allocation = Allocation::find(4749);
        $machine = Machine::find(319);
        $now_packing_forms = CurrentMachineInput::where("allocation_id", 4749)->
        where("goods_kind_id", 3)->
        pluck("packing_form_id")->
        toArray();

        $packing_form_ids = CurrentMachineInputLog::
        join("products", "products.id", "material_id")->
        join("packing_forms", "packing_forms.id", "entry_packing_form_id")->
        where([
            "allocation_id" => $allocation->id,
            "goods_kind_id" => 3,
        ])->
        whereNotIn("entry_packing_form_id", $now_packing_forms)->
        where([
            "warehouse_status_id" => 4201,
            "packing_forms.status_id" => 7007003,
            "packing_forms.warehouse_id" => $machine->warehouse_id,
        ])->
        groupBy("current_machine_input_id")->
        orderByDesc("current_machine_input_logs.id")->
        pluck("entry_packing_form_id", "current_machine_input_id");


        foreach ($packing_form_ids as $packing_form_id) {
            $data[$packing_form_id]["consumed_status_id"] = 6021103;
        }

        return GeneralMaterialReturnToWarehouseController::AddNewModificationForPacking($machine, $packing_form_ids, $data, $allocation);


        return $form_ids = WarehouseProduct::where([
            "financial_software_status_id" => 5103200 // در انتظار بررسی
        ])->
        where("created_at", "<", Carbon::now()->addMinute(-1))->
        groupBy("form_id")->
        distinct("form_id")->
        pluck("form_id")->
        toArray();

        return $allocations_consumed = ProductionFormItem::whereIn("allocation_id", [3280, 3230])->
        groupBy("allocation_id")->
        selectRaw("sum(final_amount) as final_amount,allocation_id")->
        pluck("final_amount", "allocation_id")->
        toArray();

        return Script1006Controller::handle();

        return ProductRequestFormForm::find(3126)->form->warehouse->warehouse_type_id;
// V1.6.19
        foreach (Warehouse::where("warehouse_type_id", 2)->get() as $w) {
            $machine = Machine::where("warehouse_id", $w->id)->first();
            if (!$machine) {
                return $w;
            }
            $w->belonging_to_id = $machine->id;
            $w->save();
        }
        event(new WarpsAvailableEvent(Product::find(2435)));

        return
            $product_request_form = ProductRequestForm::find(3044);

        return $warps_is_in_warehouse = Warps::warpsExistInWarehouse(null, $product_request_form);


        if (RateLimiter::tooManyAttempts('send-message:' . Auth::id(), $perMinute = 20)) {
            $seconds = RateLimiter::availableIn('send-message:' . Auth::id());

            return 'You may try again in ' . $seconds . ' seconds.';
        }

        return RateLimiter::hit('send-message:' . Auth::id());

        $warps_request_list = [];
        $report_list = [];
        foreach (Machine::where("active_status_id", 1200)->get() as $machine) {
            $warps_request = ProductRequestForm:: getLatestRequestForm($machine->id, 10);
            if ($warps_request) {
                $i = 1;
                // بررسی مقدار باقی مانده چله با توجه به قطب ها
                foreach ($warps_request->items as $item) {

                    $allocation = $machine->getCurrentAllocation();
                    if (!$allocation) {
                        $allocation = Allocation::
                        where("machine_id", $machine->id)->
                        where("status_id", 5310020)->orderByDesc("id")->first();;

                    }
                    $allocation_item = $allocation->items()->first();
                    // چله کارت درحال رزرو را به دست می آوریم و مقدار باقی مانده را با آن مقایسه می کنیم.
                    /*********************************/
                    $current_warps_bom = BOMItem::join("products", "material_id", "products.id")->
                    where("product_id", $allocation_item->production->product_id)->
                    where("goods_kind_id", 3)->
                    orderBy("material_id")->
                    first();
                    /********************/

                    $result = self::getRemainingAmountOfWarps($warps_request, $item, null, $current_warps_bom->material_id ?? null);
                    $warps_amount_list[$i] = $result;
                    $i++;
                    $warps_count = $warps_request->items->count();
                    $warps_request_list[$machine->id] = $warps_amount_list;
                    $warps_request_list[$machine->id]["packing_form_code"] = PackingFormItem::where("id", $warps_amount_list[1]["packing_form_item_id"])->first()->packing_form->code;
                    $report_list[] = [
                        "machine_code" => $machine->number_code,
                        "machine_caption" => $machine->caption,
                        "amount" => $warps_amount_list[1]["input_amount"],
                        "woven_amount" => $warps_amount_list[1]["consumed_amount"],
                        "packing_form" => $warps_request_list[$machine->id]["packing_form_code"]
                    ];
                }

            }
        }

        return json_encode($report_list);
        foreach (Machine::where("active_status_id", 1200)->get() as $machine) {
            $allocations = $machine->ReserveAllocation()->get();
            echo "<br/>machine:" . $machine->number_code . "=>";
            foreach ($allocations as $allocation) {
                if ($allocation) {
                    $allocation_item = $allocation->items()->first();
                    $result = ProductionChannel::CheckProductionChannelForAllocation($machine, $allocation_item->production, 1);

                    if (!$result["result"]) {
                        if (isset($result["error"])) {

                            echo "<br/>" . $result["error"];

                        }
                    }
                    echo "<br/>";
                }
            }
        }

        return "OK";
        foreach (Machine::where("active_status_id", 1200)->get() as $machine) {
            $allocation = $machine->getCurrentAllocation();
            if ($allocation) {
                $allocation_item = $allocation->items()->first();
                $result = ProductionChannel::CheckProductionChannelForAllocation($machine, $allocation_item->production, 1);
                echo "<br/>machine:" . $machine->number_code . "=>";
                if (!$result["result"]) {
                    if (isset($result["error"])) {

                        echo $result["error"];

                    } else {
                        echo "machine:" . $machine->number_code;
                        $production_channel_type = $result["production_channel_type"];

                        // به صورت اتوماتیک یک کانال ایجاد می کنیم.
                        GeneralProductionChannelController:: create_production_channel($machine, $production_channel_type, $production_channel_type->min_capacity, $production_channel_type->max_capacity);
                    }
                }
                echo "<br/>";
            }
        }

        return "OK";

        return Script1021Controller::handle();

        $post_users = PostUser::getCurrentPostByShiftWorkAndLeaveOvertime("post_user_object");

        return $navs = \Auth::user()->getNavBars();
        $post_user = PostUser::where("user_id", 100)->first();
        if (!$post_user) {
            return redirect("dashboard")->withErrors("برای شما هیچ پستی در سیستم تعریف نشده است، لطفا با پشتیبانی تماس بگیرید");
        }

        return $post_user->getMenuList($post_user->worker);

        $machine = Machine::find(310);

        return EndOfProductionCardTextureController::has_requirement_for_doffs($machine);

        return $machine->ReserveAllocation(false, false, "Acs", array_keys($list["allocation_illegal"]))->get();

        $new_machine = Machine::find(305);
        $directory_namespace = $new_machine->machine_type->machine_module_type->directory_namespace . "\ProductionCard\MachineAllocationController";

        $machine_allocation_controller = new $directory_namespace();

        return $machine_allocation_controller->shoulder_width_id;

// به دست آوردن کانالی که قرار است تخصیص را به آن اختصاص دهیم.
//  return round(25*2/12-4.16666667,10);
        return abs(-1 * round(3.333333342681044e-8, 7)) > .0000000001 ? 1 : -1;
        $reserve_allocation = Allocation::find(4638);

        return ProductionChannel::GetSameChannel(Machine::find(310));
//        $packing_form_final_amount_list_pre = PackingFormItem::
//        join( "packing_forms", "packing_form_id", "packing_forms.id" )->
//        whereIn( "packing_form_id", [51779,
//         51775,
//         51778,
//         51779,
//         51776,
//         50162,
//         51777] )->
//        groupBy( "packing_form_id", "product_id", "degree_id" )->
//        addSelect( DB::raw( "packing_form_id,sub_packing_form_number, product_id ,degree_id,sum(final_amount) as final_amount" ) )->
//        get();
//
//        $packing_form_final_amount_list = [];
//        foreach ( $packing_form_final_amount_list_pre as $item ) {
//
//            // ممکن است کل یک بسته بندی انتخاب نشده باشد، در این صورت باید نسبت بگیریم مثلا اگر 4 بسته از یک پالت 200 تایی انتخاب شود، مقدار انتخاب شده برابر است با
//            // final_amount * 4 / 200
//
//            $number_of_selected = isset( $count_select[ $item->packing_form_id ] ) ? $count_select[ $item->packing_form_id ] : $item->sub_packing_form_number;
//
//            $final_amount =
//                $item->sub_packing_form_number == 0 ?
//                    $item->final_amount :
//                    $item->final_amount * $number_of_selected / $item->sub_packing_form_number;
////            if ( $item->product_id == 1403 ) {
////                echo $item . "<br/>";
////            }
//            if ( ! isset( $packing_form_final_amount_list[ $item->product_id ][ $item->degree_id ] ) ) {
//                $packing_form_final_amount_list[ $item->product_id ][ $item->degree_id ]      = 0;
//                $packing_form_final_amount_remaining[ $item->product_id ][ $item->degree_id ] = 0;
//            }
//            $packing_form_final_amount_list[ $item->product_id ][ $item->degree_id ] += $final_amount;;
//            $packing_form_final_amount_remaining[ $item->product_id ][ $item->degree_id ] += $final_amount;
//
//        }
//return $packing_form_final_amount_list;
//return round(67.9999995,6);
//        return Script1007Controller::handle($x);
//        $software_name = Setting::getStringValue( "software_name" );
//        $template      = "scriptexecution";
//        $token         = 1254;
//        $token2        = jdate( Carbon::now()->timestamp )->format( 'H:i Y/m/d ' );
//        $token3        = "5646";
//        $token10       = $software_name;
//        $token20       = "تست پیامک";
//
//
//        Notification::send(
//            "09130656899",
//            new SMSNotification( $template, $token, $token2, $token3, $token10, $token20 )
//        );
//
//        return "[«";


//
//     $list=   FormItem::
//        join("packing_form_item","packing_form_item.id","packing_form_item_id")->
//        where("form_id",$x)->pluck("packing_form_id")->toArray();
//
//    $ff= ProductRequestFormForm::where("form_id",$x)->first();
//    $wid=$ff->product_request_form->applicant_id;
//  foreach (  PackingForm::whereIn("id",$list)->get() as $packing_form){
//      $packing_form->warehouse_status_id=4201;
//      $packing_form->warehouse_id=$wid;
//      $packing_form->status_id=7007003;
//      $packing_form->save();
//  }
//  return "OK";
//   return Script1009Controller::handle( $x );

//   return   $result = SmartObject::getContour( SmartObject::find(1) );;
//  return self::FabricRawForecast( Script::find( 1 ) );

//  return Product::CheckWeight(Product::find($x),BOM::find($y));
//        return "OK";
//  return Script1007Controller::handle(310,true);
// New Version
//         بروز رسانی فرم های مصرف در جدول machine_allocation_material_consumed
//        $list = MachineAllocationMaterialConsumed::whereNotNull( "form_id_delete" )->get();
//        foreach ( $list as $item ) {
//            if ( !Allocation\MachineAllocationMaterialConsumedFrom::where( [
//                "machine_allocation_material_consumed_id" => $item->id,
//                "form_id"                                 => $item->form_id_delete,
//            ] )->first()
//            ) {
//                Allocation\MachineAllocationMaterialConsumedFrom::create( [
//                    "machine_allocation_material_consumed_id" => $item->id,
//                    "form_id"                                 => $item->form_id_delete,
//                ] );
//            }
//        }
//
//       return "OK";

//        $list = CurrentMachineInput::whereNull( "consume_warehouse_id" )->get();
//        foreach ( $list as $item ) {
//
//            $item->consume_warehouse_id = $item->machine->warehouse_id;
//            $item->save();
//        }
//
//        return "OK";

// بروز رسانی انبارک مصرف برای BOM پارچه خام

//        $product_ids = Product::where( "goods_kind_id", 4 )->pluck( "id" )->toArray();
//        BOMItem::
//        join( "products", "material_id", "products.id" )->
//        where( "goods_kind_id", 2 )->
//        whereIn( "product_id", $product_ids )->
//        update( [
//            "sampling_consume_warehouse_type_id" => 4,
//            "sampling_consume_warehouse_id"      => 54
//        ] );
//
//        return BOMItem::join( "products", "material_id", "products.id" )->
//        where( "goods_kind_id", 2 )->paginate();
//
//
//        $material_info = BOMItem::where( [ "bill_of_material_id" => 2698 ] )->
//        where( "material_id", 481 )->
//        selectRaw( "
//                       amount,
//                       percent_of_use,
//                       count(id) as number,
//                      sum(amount * percent_of_use * number /100) as amount_required,
//                      min(input_line_code) as input_line_code_from,
//                      max(input_line_code) as input_line_code_to,
//                      productive_consume_warehouse_type_id,
//                      productive_consume_warehouse_id,
//                      sampling_consume_warehouse_type_id,
//                      sampling_consume_warehouse_id"
//        )->
//        first();
//
//        return $consume_warehouse_id = BOMItem::getConsumeWarehouseId( $material_info, Production::find( 3443 ), Machine::find( 305 ) );
//        $diff_in_minutes = 40;
//
//        return $diff_in_minutes % 40 == 0;
//
//        $list = [ 15622, 16866, 16979, 16989, 20478, 24935, 25095, 25312, 50215, 50216, 50217, 50218 ];
//
//        $list = PackingForm::whereIn( "id", $list )->get();
//        foreach ( $list as $packing_form ) {
//            $result = PackingForm::UpdateWeight( $packing_form );
//            if ( $result["result"] ) {
//                $packing_form->weight       = $result["weight"];
//                $packing_form->gross_weight = $result["gross_weight"];
//                $packing_form->save();
//            }
//        }
//
//        return "OK";
//        $x=null;
//        $x[]=-1;
//        return $x;
//return Script1012Controller::handle();
// بررسی مقدار بسته بندی های اصلی و فرعی
        $packing_form_master_ids = PackingForm::whereNotNull("packing_form_master_id")->groupBy("packing_form_master_id")->orderBy("packing_form_master_id")->pluck("packing_form_master_id")->toArray();


        $list_master = PackingFormItem::whereIn("packing_form_id", $packing_form_master_ids)->groupBy("packing_form_id")->
        selectRaw("sum(final_amount) as amount,packing_form_id")->
        pluck("amount", "packing_form_id")->
        toArray();


        $packing_form_ids = PackingForm::whereNotNull("packing_form_master_id")->groupBy("id")->orderBy("id")->pluck("id")->toArray();

        $list_subpacking =

            PackingFormItem::whereIn("packing_form_id", $packing_form_ids)->
            join("packing_forms", "packing_forms.id", "packing_form_id")->
            groupBy("packing_form_master_id")->
            selectRaw("sum(final_amount) as amount,packing_form_master_id")->
            pluck("amount", "packing_form_master_id")->
            toArray();

        foreach ($list_master as $master_packing_form_id => $final_amount) {
            if (round($final_amount, 10) != round($list_subpacking[$master_packing_form_id], 10)) {
                echo $master_packing_form_id . "=>" . $final_amount . "|" . $list_subpacking[$master_packing_form_id] . "<br/>";
                $pfitem = PackingFormItem::where("packing_form_id", $master_packing_form_id)->first();
                $pfitem->final_amount = $list_subpacking[$master_packing_form_id];
                $pfitem->save();

                $packing_form = PackingForm::find($master_packing_form_id);
                $result = PackingForm::UpdateWeight($packing_form);
                if ($result["result"]) {
//                    $packing_form->weight       = $result["weight"];
//                    $packing_form->gross_weight = $result["gross_weight"];
//                    $packing_form->save();
                }


            }
        }

        return "sdfsdf";

        return 1;
// بروز رسانی مقدار درصد ضایعات
        BOMItem::where("material_id", $x)->update(["waste_prediction" => $y]);
        BOMReplace::where("replace_product_id", $x)->update(["waste_prediction" => $y]);

        return BOMItem::where("material_id", $x)->get();


        return Script1013Controller::handle();
        foreach (Machine::where("active_status_id", 1200)->get() as $machine) {

            $checklist = MachineModuleType::getChecklist($machine->machine_type->machine_module_type_id, "current_production_form_status_with_reserve");


        }

        return "OK";

        return Script1011Controller::handle();

        return redirect()->route("dashboard")->withErrors($result_check["error"]);

        return MachineAllocation::groupBy("machine_id")->get();

        return $material_info = BOMItem::where(["bill_of_material_id" => 2705])->
        where("material_id", 491)->
        selectRaw("
                       amount,
                       percent_of_use,
                       count(id) as number,
                      sum(amount * percent_of_use * number /100) as amount_required,
                      min(input_line_code) as input_line_code_from,
                      max(input_line_code) as input_line_code_to")->
        first();

// بروز رسانی مقدار بسته بندی ها
        $packing_form_master_ids = PackingForm::whereNotNull("packing_form_master_id")->groupBy("packing_form_master_id")->orderBy("packing_form_master_id")->pluck("packing_form_master_id")->toArray();

        $list = PackingForm::
        whereIn("id", $packing_form_master_ids)->
        where("warehouse_id", 1)->
        where("warehouse_status_id", 4201)->
        where("status_id", 7007003)->
        get();


        foreach ($list as $packing_form) {


            $amount = 0;
            foreach ($packing_form->packing_form_contents as $sub_packing_form) {
                $amount += $sub_packing_form->getFinalAmount();
            }

            $p_amount = $packing_form->getFinalAmount();
            if ($p_amount != $amount) {
                echo $packing_form->code . "=>" . $p_amount . ":" . $amount . "<br/>";

                $pfi = $packing_form->items()->first();
                $pfi->final_amount = $amount;
                $pfi->save();


                // return $packing_form;
            }

        }

        return "OK";

        $packing_form = PackingForm::find(20725);
        $result_weight = PackingForm::UpdateWeight($packing_form);
        if ($result_weight["result"]) {
            $packing_form->weight = $result_weight["weight"];;
            $packing_form->gross_weight = $result_weight["gross_weight"];
            $packing_form->save();
        }

        return $packing_form;
        $sub_packing_ids = $packing_form->packing_form_contents()->pluck("id")->toArray();
        $amount_list = PackingFormItem::whereIn("id", $sub_packing_ids)->selectRaw("sum(final_amount) as final_amount,sum(sub_amount) as sub_amount")->first();
        $final_amount = $amount_list->final_amount;
        $sub_amount = $amount_list->sub_amount;

        return $final_amount;
        $k = 0;
        foreach (Product\BOM\BOMPermutation::groupBy("bill_of_material_id")->get() as $permutation) {
            echo "k=" . $k . "<br/>";
            $k++;
            if ($k < $x) {
                continue;
            }
            if ($k > $y) {
                break;
            }

            $bom = BOM::find($permutation->bill_of_material_id);
            echo $permutation->product_id . "<br/>";
            $product = Product::find($bom->product_id ?? 0);
            if ($bom && $product) {
                Product\BOM\BOMPermutation::CreateBOMMood($bom);
            } else {
                echo $permutation->product_id . "error" . "<br/>";
            }

        }

        return "OK";

        $sub_packing_ids = PackingForm::find(20725)->packing_form_contents()->pluck("id")->toArray();
        $amount = PackingFormItem::whereIn("id", $sub_packing_ids)->selectRaw("sum(final_amount) as final_amount,sum(sub_amount) as sub_amount")->get();

        return $amount;

        return Script1007Controller::handle(305, true);

        $product = Product::find($x);
        $bom = $product->bom()->first();
        $items = $bom->items()->
        join("products", "material_id", "products.id")->
        orderBy("goods_kind_id")->
        select("bill_of_material_item.id", "material_id", "input_line_code", "goods_kind_id")->
        get();
        $category = 0;

        Product\MaterialFlow::where([
            "product_id" => $product->id,
            "bill_of_material_id" => $bom->id,
        ])->delete();

// ایجاد نود به ازای هر ورودی
        foreach ($items as $bom_item) {
            Product\MaterialFlow::create([
                "product_id" => $product->id,
                "bill_of_material_id" => $bom->id,
                "bill_of_material_item_id" => $bom_item->id,
                "machine_type_id" => 30,
                "band_code" => 1
            ]);
        }

        return "OK";
        $input = array("a", "b", "c", "d", "e");
        $ma = MachineAllocation::where("allocation_id", 4174)->first();

//  return  $output = array_slice($input, 0,2);      // returns "c", "d", and "e"
        return Script1007Controller::RequestForMachineIfNeed($ma, "2023-05-25 06:41:55");

        return 0;


//        $list = PackingFormItem::join( "packing_forms", "packing_forms.id", "packing_form_id" )->
//        where( "warehouse_id", 2 )->
//        where( "packing_forms.status_id", 7007003 )->
//        whereIn( "packing_form_item.id", $pfi_id )->
//        get();
//
//        foreach ( $list as $item ) {
//
//
//            $item->final_amount         = round( $item->final_amount+0.0000001, 6 );
//            $item->amount               = round( $item->amount+0.0000001, 6 );
//            $item->amount_after_control = round( $item->amount_after_control+0.0000001, 6 );
//            $item->sub_amount           = round( $item->sub_amount+0.0000001, 6 );
//            $item->save();
//
//        }


        $list = PackingFormItem::join("packing_forms", "packing_forms.id", "packing_form_id")->
        where("warehouse_id", 1)->
        where("packing_forms.status_id", 7007003)->
        whereIn("packing_form_item.id", [50307])->
        select("packing_form_item.*")->
        get();


        $packing_form_item_ids = PackingFormItem::join("packing_forms", "packing_forms.id", "packing_form_id")->
        where("warehouse_id", 1)->
        where("packing_forms.status_id", 7007003)->
        select("packing_form_item.*")->
        pluck("packing_form_item.id")->toArray();

        $inventory_p = WarehouseProduct::whereIn("packing_form_item_id", $packing_form_item_ids)->
        where("output", 0)->orderBy("id")->
        groupBy("packing_form_item_id")->
        pluck("input", "packing_form_item_id");


//        $count = WarehouseProduct::whereIn( "packing_form_item_id", $packing_form_item_ids )->
//        where( "output", 0 )->orderBy( "id" )->
//        selectRaw( "count(packing_form_item_id) as count,packing_form_item_id" )->
//        groupBy( "packing_form_item_id" )->
//        pluck( "count", "packing_form_item_id" );

        $inventory = [];
        foreach ($list as $item) {
            $item_inventory["id"] = $item->id;
            $item_inventory["code"] = $item->code;
            $item_inventory["packing_form_item_amount"] = $item->final_amount;


            $item_inventory["warehouse_input_amount"] = isset($inventory_p[$item->id]) ? $inventory_p[$item->id] : -10000;


            $item_inventory["warehouse_input_count"] = isset($count[$item->id]) ? $count[$item->id] : -10000;;

            return $inventory[] = $item_inventory;

            if ($item_inventory["packing_form_item_amount"] != $item_inventory["warehouse_input_amount"] && $item_inventory["warehouse_input_amount"] != -10000) {
                $item->final_amount = round($item->final_amount, 6);
                $item->save();
                echo $item . "<br/>";
            }
        }


        return $inventory;

        return "OK";

        return $amount_required_list = CurrentMachineInput::
        join("machines", "machine_id", "machines.id")->
        join("allocations", "allocations.id", "allocation_id")->
        whereIn("allocations.status_id", [5310040, 5310010])-> // تخصیص رزرو/ جاری
        whereIn("material_id", [520])->
        where("machines.check_inventory_for_allocation", 1)->// فقط ماشین هایی که تیک دارند
        select("amount_required", "production_id", "current_machine_inputs.id")->
        get();

        return Script1007Controller::handle(310);

        return PackingForm::join("packing_form_item", "packing_forms.id", "packing_form_item.id")->
        join("products", "products.id", "product_id")->
        where("goods_kind_id", 2)->
        selectRaw("packing_forms.weight,sum(final_amount),packing_forms.code")->
        groupBy("packing_forms.id")->
        get();

        1 / 0;

        return Script1009Controller::handle();

        return $machine_allocation_material_consumed->start_machine_log_id;

        return PackingFormItem::where("production_id", 72)->
        join("production_form_item", "production_form_item.id", "production_form_item_id")->
        groupBy("production_form_item.id")->orderBy("production_form_item.id")->
        selectRaw("sum(packing_form_item.final_amount) as amount , production_form_item.code")->
        get();

        return "OK";

        return Script1017Controller::handle();

        return "OK";

        return Script1011Controller::handle();
        event(new PutInWarehouseEvent($form_output));

        return Product\MaterialFlow::where("product_id", $x)->delete();


//return LotNumber::selectRaw("product_id, count(id)")-> groupBy("product_id","code")->get();
        return FabricRaw::getLotNumber(Allocation::find(3775), Machine::find(305), 1, Product::find(847));

        return Script1016Controller::handle(329);

        return WarehouseProduct::where("id", ">", 0)->where("warehouse_id", 5)->groupBy("trans_kind")->select("trans_kind")->get();
        $script = Script::find(15);

        return Script::SendSmd($script, 0, " در درخواست خروج با شناسه " . ($product_request_form_form->product_request_form_id ?? 0) . " به دلیل نداشتن درخواست هیچ تراکنشی ثبت نگردید.", 4);


        return Script1013Controller::handle(329);
        1 / 0;
        $form = Form::find(797);
        $k = 0;
        foreach ($form->item as $form_item) {
            $count = WarehouseProduct::where("form_item_id", $form_item->id)->count();
            if ($count > 1) {
                $wp = WarehouseProduct::where("form_item_id", $form_item->id)->first();
                $wp->delete();
                echo "delete" . $wp->id . "<br/>" . $k;
                $k++;
            }
        }

        return "OK";

        return Script1015Controller::handle();
        $ids = [

            150
            ,
            151
            ,
            152
            ,
            153
            ,
            154
            ,
            155
            ,
            156
            ,
            157
            ,
            158
            ,
            159
            ,
            160
            ,
            161
            ,
            162
            ,
            163
            ,
            164
            ,
            165
            ,
            166
            ,
            167
            ,
            168
            ,
            169
            ,
            170
            ,
            171
            ,
            172
            ,
            173
        ];

        $NewPackingFrom = PackingType::whereIn("id", $ids)->pluck("id", "first_packing_type_id")->toArray();

        foreach (Product::where("goods_kind_id", 2)->get() as $product) {
            $packing_types = $product->packing_types;
            echo "product_id=" . $product->id . "<br/>";
            foreach ($packing_types as $packing_type) {
                if (in_array($packing_type->id, array_keys($NewPackingFrom))) {
                    $new_packing_type_id = $NewPackingFrom[$packing_type->id];
                    if (
                        !Product\ProductPackingType::where([
                            "product_id" => $product->id,
                            "packing_type_id" => $new_packing_type_id
                        ])->exists()
                    ) {
                        Product\ProductPackingType::create([
                            "product_id" => $product->id,
                            "packing_type_id" => $new_packing_type_id
                        ]);
                    }
                }
            }
        }

        return "OK";

        return Script1014Controller::handle(Allocation::find(3593), true);

        return Script1012Controller::handle();
        $waiting_for_actual_consumption_list = MachineAllocationModificationForm::
        join("machine_allocation_modifications", "machine_allocation_modifications.id", "machine_allocation_modification_id")->
        where([
            "input_form_status_id" => 6021202, // پردازش انجام شده
//            "actual_consumption_status_id"               => 6021301, //در انتظار محاسبه مصرف واقعی
            "machine_allocation_modifications.status_id" => 6021003 // خاتمه یافته
        ])->
        select("machine_allocation_modification_form.*")->
        get();

        foreach ($waiting_for_actual_consumption_list as $machine_allocation_modification_form) {
            if ($machine_allocation_modification_form->input_form && $machine_allocation_modification_form->input_form->status_id == 500000200) {
                if ($machine_allocation_modification_form->id == $x) {
                    // محاسبه مصرف واقعی و تغییر درجه داده شده و ضایعات مواد اولیه
                    $machine_allocation_modification_form->actual_consumption_status_id = 6021302; //عدم امکان محاسبه، چون فرم ورود تایید نشده است.
                    $machine_allocation_modification_form->save();
                }
            } else {
                $machine_allocation_modification_form->actual_consumption_status_id = 6021303; //عدم امکان محاسبه، چون فرم ورود تایید نشده است.
                $machine_allocation_modification_form->save();
            }
        }

        return "OK";

        return Product::CheckWeight(Product::find(2778), BOM::find(2481));

        return $material_info = BOMItem::where(["bill_of_material_id" => 2650])->
//  where("material_id",$material->id)->
        selectRaw("
                      sum(amount) as amount,
                      sum(amount * number * percent_of_use /100) as amount_required,
                      avg(percent_of_use) as percent_of_use ,
                      count(id) as number,
                      min(input_line_code) as input_line_code_from,
                      max(input_line_code) as input_line_code_to")->
        first();


// return Script1007Controller::GetEndTimeOfWork( 1, 12, null, false );

        $production_form = $machine->getCurrentProductionForm();;
        $last_machine_log = MachineLog::getLastLogWithContour($machine);

        return ProductionForm::UpdateAmountWithLastContour($production_form, $last_machine_log);
        $product = Product::find(783);
        if ($product->sub_unit && (

                ($product->unit->weight_conversion_rate != 0 && $product->sub_unit_id == 1100) ||
                ($product->sub_unit->weight_conversion_rate != 0 && $product->unit_id == 1100)
            )
        ) {
            return "OK";
        }

        return "NM";
        $bom = BOM::find(2554);
        $weight = 0;
        foreach ($bom->items as $bom_item) {
            $x = CurrentMachineInput::getConsumedAmount($bom_item->amount, $bom_item->number, $bom_item->percent_of_use) *
                $bom_item->material->weight;
            echo $bom_item->amount . "," . $bom_item->number . "," . $bom_item->percent_of_use . "<" . $x . "=" . $bom_item->material->id . "<br/>";
            $weight +=
                CurrentMachineInput::getConsumedAmount($bom_item->amount, $bom_item->number, $bom_item->percent_of_use) *
                $bom_item->material->weight;


        }
        $product = Product::find(2826);
        if ($product->weight + 1 > $weight + 1) {
            return [
                "result" => false,
                "error" => $product->weight . "--" . $weight . "=>" . ($product->weight + 0 > $weight ? "sdf" : "erro")
            ];
        }


        if ($product->weight + 1 < $weight * (1 - $product->goods_kind->error_rate_in_checking_bom_weight / 100) + 1) {
            return [
                "result" => false,
                "error" => "65464"
            ];

        }

        return $weight;

        foreach ($packing_forms as $packing_form) {
            $packing_form_item = $packing_form->items()->first();
            $wp = WarehouseProduct::find(67102)->toArray();

            $wp["input"] = 0;
            $wp["output"] = .42;
            $wp["packing_form_item_id"] = $packing_form_item->id;

            WarehouseProduct::create($wp);
        }

        foreach (Form::find(6300)->item as $item) {
            $item->packing_form_item->packing_form->warehouse_id = 5;
            $item->packing_form_item->packing_form->warehouse_status_id = 4201;
            $item->packing_form_item->packing_form->save();
        }

        return Product::CheckWeight(Product::find(2679), BOM::find(2323));

        return Script1001Controller::handle();

        return WarehouseProduct::where("warehouse_id", 5)->groupBy("trans_kind")->select("trans_kind")->get();

        return Script1001Controller::handle();


        $allocation = Allocation::find(586);
        $send_product_request_form_by_robot =
            CurrentMachineInput::
            join("machine_type_input_band_goods_kind", "machine_type_input_band_id", "input_band_id")->
            where("send_product_request_form_by_robot", 1)->
            where("allocation_id", $allocation->id)->
            groupBy("material_id")->
            pluck("send_product_request_form_by_robot", "material_id");

        return Script1005Controller::handle();
//return   SmartObject::find(1)->getContour();

        $client = new \GuzzleHttp\Client();
        $request = $client->get('http://127.0.0.1:5000/get_smart_object_weight/1');// Url of your choosing

        return $response = $request->getBody();

//return MachineAllocationModification::find(7);

        return $result = (new KavenegarApi(env("KAVENEGAR_APIKEY")))->VerifyLookup(
            "09130656899",
            "125452",
            "125452",
            "125452",
            "scriptexecution",
            null
        );

        $software_name = Setting::getStringValue("software_name");
        $template = "scriptexecution";
        $token = 1254;
        $token2 = jdate(Carbon::now()->timestamp)->format('H:i Y/m/d ');
        $token3 = "5646";
        $token10 = $software_name;
        $token20 = "تست پیامک";


        Notification::send(
            "09130656899",
            new SMSNotification($template, $token, $token2, $token3, $token10, $token20)
        );

        return "[«";

//return Script1007Controller::handle();

        $machine_allocation = MachineAllocation::where("allocation_id", 3436)->first();

        return Script1007Controller::RequestForMachineIfNeed($machine_allocation, $machine_allocation->allocation->predict_of_production_start_date_theory);

        return Script1007Controller::handle(317);

        return "OK";


        return Script1008Controller::handle();
// بروزرسانی مقادیر فرم تولید
        $production_form = Machine:: find(317)->getCurrentProductionForm();
        $last_machine_log = MachineLog::getLastLogWithContour(Machine:: find(317));

        return ProductionForm::UpdateAmountWithLastContour($production_form, $last_machine_log);

        return FabricRaw::getCurrentLot(Allocation::find(3317), $order_desc = false, $only_current_allocation = true);

        return Script1007Controller::handle();

        return MachineAllocationMaterialConsumed::registerNewConsumed(null, Machine::find(317), null, null);

// return $inventory = WarehouseProduct::getProductInventoryList( [ 479, 608, 491 ] );;

//        $list=FormItem::where("form_id","4850")->get();
//        foreach ($list as $item){
//            $item->description=str_replace("نقل و انتقال بدون افزایش بها" , " ",$item->description);
//             $item->save();
//        }
        return "OK";


        return $this->getLatestId("PRINTSERVER", 1);

        return Script1007Controller::handle();

        return Form::find(3533)->trans_kind_item;
        $allocation = Allocation::find(3255);
        $warps_machine_input = CurrentMachineInput::where(
            [
                "allocation_id" => $allocation->id,
                "goods_kind_id" => 3
            ])->
        get();

        $warps_is_in_warehouse = true;

        foreach ($warps_machine_input as $input) {

            //  $warps_count_in_warehouse = Warps::warpsCountInWarehouse( $input->material_id, $allocation->id );
            $warps_is_in_warehouse = $warps_is_in_warehouse && $warps_count_in_warehouse >= $input->number;
        }

        return $warps_is_in_warehouse;

//        return Warps::warpsExistInWarehouse( Allocation::find( 3256 ) );

//  return WarehouseProduct::getProductInventoryList( [ 2435 ] );

        return $removed_print = PrinterFile::
        where(["status_id" => 305001,])->
        where("created_at", "<", Carbon::now()->addDay(-3))->
        first();

        $last_check_id = 0;
        $printer_id = 1;
        while (1) {
            $printer_file = PrinterFile::
            where(["status_id" => 305001, "printer_id" => $printer_id])->
            when($last_check_id > 0, function ($query) use ($last_check_id) {
                return $query->where("id", "<", $last_check_id);
            })->
            first();

            if (!$printer_file) {
                break;
            }
            $last_check_id = $printer_file->id;
            if ($printer_file && File::exists(public_path() . '/printer_files/' . ($printer_file->id ?? 0) . '.pdf')) {
                return $printer_file->id;
            }
        }

        return "0ok";

        return file_exists(public_path() . '/printer_files/' . (24635) . '.pdf');

        return WarpsRequestForm::newRequest(Allocation::find(3253), Machine::find(330), 1);

        return Script1007Controller::handle();
//        return Script1007Controller::GetEndTimeOfWork(1,24);
        $production_form = ProductionForm::find(2268);

        return $production_form->updateAmount();

//v 1.6.9
//        foreach ( PackingForm::whereNotNull( "packing_form_master_id" )->orderByDesc("id")->get() as $packing_form ) {
//            if ( $packing_form->packing_form_master->sub_packing_form_number == 0 ) {
//                $packing_form->packing_form_master->sub_packing_form_number = $packing_form->packing_form_master->packing_form_contents()->count();
//                $packing_form->packing_form_master->save();
//            }
//        }
//
//        return "OK";

//        try {
//            $opts = array(
//                'http' => array(
//                    'user_agent' => 'PHPSoapClient'
//                )
//            );
//            $context = stream_context_create($opts);
//
//            $wsdlUrl = 'http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl';
//            $soapClientOptions = array(
//                'stream_context' => $context,
//                'cache_wsdl' => WSDL_CACHE_NONE
//            );
//
//            $client = new \SoapClient($wsdlUrl, $soapClientOptions);
//
//            $checkVatParameters = array(
//                'countryCode' => 'DK',
//                'vatNumber' => '47458714'
//            );
//
//            $result = $client->checkVat($checkVatParameters);
//            print_r($result);
//        }
//        catch(Exception $e) {
//            echo $e->getMessage();
//        }
//        return "OK";
////        return view( "test" );

        $dbName = "_AccXP_arman1402";

        $userName = "api_users";
        $password = "Q77Lan35@Yz";
//$methodName = "WS_AddDet";


//$methodName = "WS_AddInvArt";
        $methodName = "WS_AddSalesArt";

//        $data =
//            "<_Art  InvDocClass='1'  FA_Date='1402/02/11' Desc='تست خروج' ODesc='تست Description' DeptCode='1'  InvKindCode='205' InvSeries='1' InvCenterCode='04/123' StockCode='4' ImpId='1004' >\n" .
//            "  <_Trans Desc='شرح عملیات خروج' ODesc='Description' InvTransKind='11'  InvCenterCode='04/123' StockCode='4' InvKind='1' MatCode='50/37/012'  InvAmount='100' />\n" .
//            "</_Art>";


//        $data =
//            "<_Art  InvDocClass='0'  FA_Date='1402/02/11' Desc='تست شرح ورود' ODesc='تست Description' DeptCode='1'  InvKindCode='101' InvSeries='1' InvCenterCode='04/123' StockCode='5' ImpId='1003' >\n".
//            "  <_Trans Desc='شرح عملیات' ODesc='Description' InvTransKind='5'  InvCenterCode='04/123' StockCode='5' InvKind='1' MatCode='40/123/0122'  InvAmount='100' />\n".
//            "</_Art>";


//        $methodName = "WS_GetMatRemain";
//
//        $data =
//            "<_Params  AllStocks='1' >\n".
//            "  <_Dept  Code='01/03/01/423' />\n".
//            "</_Params>";

// فاکتور فروش
        $data =
            "<_Art SalesDocKind='0'   FA_Date='1402/02/11' Desc='تست فاکتور فروش' ODesc='تست فاکتور Description' DeptCode='1'  SalesSeries='1' CustCode='125142' DebSide='0' SalesKindCode='1'  ImpId='1004' >\n" .
            "  <_Trans SalesKind='5' Desc='شرح عملیات خروج' ODesc='Description'  MatCode='50/37/012' SalesUnitPrice='10' SalesPrice='20' SalesDirDis='0' SalesDirFare='0' SalesDirTax='0' InvAmount='100' />\n" .
            "</_Art>";


        $params = array(
            'ADBName' => $dbName,
            'AData' => $data
        );

        $options = array(
            'login' => $userName,
            'password' => $password
        );


        $srvUri = "http://192.168.180.220:8080" . '/AccXPSOAP/AccXPSOAPServer.dll/wsdl/IAccXPSOAPSrvDM';

        $client = new \SoapClient($srvUri, $options);

        $callResult = $client->__soapCall($methodName, $params);
        $array = XmlToArray::convert($callResult);

        return $array["@attributes"];

        return $result = ArrayToXml::convert($array);

        return WarehouseProduct::
        groupBy("packing_form_item_id")->
        selectRaw("sum(input-output),id")->
        get();


        return "OK";


        $production_form = ProductionForm::find(2260);

        return $production_form->updateAmount();

        return ProductionForm::UpdateAmountWithLastContour($production_form, $last_machine_log);

        return Artisan::call("script:s1010");

        return Script1010Controller::handle();
        $list = PackingForm::whereNull("packing_form_master_id")->get();
        foreach ($list as $item) {

            $i_item_item = $item->items()->first();
            $i_item_item->amount = $item->weight;
            $i_item_item->final_amount = $item->weight;
            $i_item_item->save();
            $item = PackingForm::find($item->id);
            $amount = $item->getFinalAmount();
            $c = $item->packing_form_contents()->count();
            foreach ($item->packing_form_contents as $sub_item) {
                $sub_item_item = $sub_item->items()->first();
                $sub_item_item->amount = $amount / $c;
                $sub_item_item->amount_after_control = $amount / $c;
                $sub_item_item->final_amount = $amount / $c;
                $sub_item_item->save();
            }
        }

        return "OK";
//        $office_automation_action = OfficeAutomationAction::orderByDesc( "id" )->first();
//        $token                    = $office_automation_action->office_automation_to_do_list->getCode();
//        $token2                   = $office_automation_action->office_automation_to_do_list->worker->fullName();
//        $token3                   = "_APP_NAME_/DCWT/" . $office_automation_action->office_automation_work->id . "/" .
//                                    $office_automation_action->office_automation_work->random . "/";
//        $token10                  = $office_automation_action->worker->cooperation_type->caption2 . ".گرامی." . $office_automation_action->worker->fullName();
//        $token20                  = $office_automation_action->office_automation_to_do_list->caption;
//
//        $worker = $office_automation_action->worker;
//      return  $token3 = \Illuminate\Support\Str::replace( "_APP_NAME_", env("APP_NAME"), $token3 );
//        Notification::send(
//            "009130656899",
//            new SMSNotification( "officeautomationconfirm", $token, $token2, $token3, $token10, $token20 )
//        );
//
//return
//        $script = Script::find( 1 );
//
//        return Script1001Controller::FabricRawForecast( $script );
//
//        return "OK";
// return $this->callServer();

//        foreach ( PackingForm::where( "status_id", "7007018" )->get() as $packing_form ) {
//            if ( $packing_form->packing_form_master ) {
//                $packing_form->status_id = $packing_form->packing_form_master->status_id;
//                $packing_form->save();
//            }
//        }

// return "OK";
        foreach (Production::whereNotNull("packing_type_id_remove")->get() as $item) {
            $row = ProductionPackingType::where([
                "production_id" => $item->id,
                "packing_type_id" => $item->packing_type_id_remove
            ])->first();
            if (!$row) {
                ProductionPackingType::create([
                    "production_id" => $item->id,
                    "packing_type_id" => $item->packing_type_id_remove
                ]);
            }
        }
//
//         return "OK";
// V 1.6.9
        foreach (OrderFactor::get() as $item) {
            $row = OrderListPackingType::where([
                "order_list_id" => $item->order_list_id,
                "packing_type_id" => $item->packing_type_id_remove
            ])->first();
            if (!$row) {
                OrderListPackingType::create([
                    "order_list_id" => $item->order_list_id,
                    "packing_type_id" => $item->packing_type_id_remove,
                    "order_id" => $item->order_id
                ]);
            }
        }
//
//        return "OK";
// V 1.6.8
        foreach (Product\ProductRequest\ProductRequestFormPackingType::where("degree_id", 0)->get() as $item) {

            if ($item->product_request_form_item) {
                $item->degree_id = $item->product_request_form_item->degree_id;
                $item->save();
            }
        }

        return "OK";
//
//        foreach ( Machine::where("active_status_id",1200)->get() as $machine ) {
//            Warehouse::SetMachineWarehouse( $machine );
//        }
//
//        return "OK 1.6.8";
//        return Script1008Controller::handle();

//    return    Script1007Controller::rejectWaitingForm( 1, 20, Script::find( 7 ), Machine::find( 305 ),2 );
//      return  $first_shift_work_day = ShiftWorkDay::
//        where( [ "shift_id" => 1 ] )->
//        where( "start_datetime", "<=", Carbon::now() )->
//        where( "end_datetime", ">=", Carbon::now() )->
//        first();

        return Script1007Controller::handle();
//        $list=WarehouseProduct::where("warehouse_id",1)->where("form_id",">=","3716")->get();
//        foreach ($list as $item){
//            if(round($item->form->items() - $item->form_item->amount,4)!=0){
//                $a=["form_item"=>$item->form_item,"wp"=>$item];
//                return $a;
//                echo $item->id."- ".$item->input ."!=". $item->form_item->amount."-- ".round($item->input - $item->form_item->amount,10)."<br/>";
//            }
//        }


        return Carbon::now()->format("H:i:s Y/m/d");

        return Script1008Controller::handle();
//        return view("test");
//        Auth::loginUsingId( 2);
//        Auth::login(User::find(2) ); // دستیار دیجیتال
//        $client      = Posttex::client();
//        $response    = $client->request( 'post', 'checkout/newOrder',
//            [
//                'headers' => [ "token" => Posttex::getToken(), ],
//                'query'   => 0
//            ] );
//        $body        = $response->getBody();

        return 1;
        foreach (Product\ProductRequest\ProductRequestFormPackingType::where("degree_id", 0)->get() as $item) {

            if ($item->product_request_form_item) {
                $item->degree_id = $item->product_request_form_item->degree_id;
                $item->save();
            }
        }


        $new_service = [
            "serviceId" => 723,
            "GoodsType" => "نمونه پارچه",
            "ApproximateValue" => 500000,

            "Weight" => 100,
            "insuranceName" => "غرامت تا سقف 300 هزار تومان",
            "CartonSizeName" => 21087,//به کارتن نیاز ندارم
            "NeedCarton" => false,//به کارتن نیاز ندارم

            "Sender_FristName" => "علیرضا",
            "Sender_LastName" => "حسینی اربندآبادی",
            "Sender_mobile" => "09130023474",
            "Sender_StateId" => "1",
            "Sender_townId" => "585",

            "Sender_City" => "تهران",
            "Sender_PostCode" => "8919718588",
            "Sender_Address" => "تهران - میدان آزادی کنار باقالی فروشی",
            "Sender_Email" => "jalayegh@gmail.com",

            "Reciver_FristName" => "علیرضا",
            "Reciver_LastName" => "جلایق",
            "Reciver_mobile" => "09130656899",
            "Reciver_StateId" => "1",
            "Reciver_townId" => "585",

            "Reciver_City" => "یزد",
            "Reciver_PostCode" => "8919718588",
            "Reciver_Address" => "یزد - بلوار نواب صفوی کوچه قدس",
            "Reciver_Email" => "jalayegh@gmail.com",

            "IsCOD" => 1,
            "HasAccessToPrinter" => true,

            "boxType" => "نمی دانم",
            "Count" => 1,
            "orderSource" => 13,
            "refrenceNo" => 100,

            "SenderLat" => "54.36398612",
            "SenderLon" => "31.88789443",

            "ReciverLat" => "54.35581611",
            "ReciverLon" => "31.88680126",

            "IpAddress" => "109.125.144.51",


        ];
        $client = Posttex::client();
        $response = $client->request('post', 'checkout/newOrder',
            [
                'headers' => ["token" => Posttex::getToken(),],
                'query' => $new_service
            ]);
        $body = $response->getBody();

        return $body;

        return Carbon::now()->format("H:i Y/m/d");

        return Script1007Controller::handle();
//
//     return Script1007Controller::GetEndTimeOfWork( 5, 11 );


        foreach (Machine::all() as $machine) {
            Warehouse::SetMachineWarehouse($machine, 2);
        }

        return $allocation_item = MachineAllocation::find(3595);

        FabricRaw::ProductionTerminated($allocation_item);

        $sum_allocation_amount = MachineAllocation::
        where("production_id", $allocation_item->production_id)->
        whereIn("status_id", [5310010, 5310020, 5310040])->
        sum("allocation_amount");

        return $count_doffs = MachineAllocation::
        where("production_id", $allocation_item->production_id)->
        whereIn("status_id", [5310010, 5310020, 5310040])->
        selectRaw("sum(max_number_of_doffs)- sum(number_of_doffs_done) as count")->
        first()["count"] == 0 ? 1 : 2;


        $packing_forms = PackingForm::whereIn("id", [7102, 7103, 7099])->get();

        return PackingForm::MasterPackingFormIds($packing_forms);

        $allocation_item = MachineAllocation::find(3314);
        $sum_allocation_amount = MachineAllocation::
        where("production_id", $allocation_item->production_id)->
        whereIn("status_id", [5310010, 5310020, 5310040])->
        sum("allocation_amount");

        $count_doffs = MachineAllocation::
        where("production_id", $allocation_item->production_id)->
        whereIn("status_id", [5310010, 5310020, 5310040])->
        selectRaw("sum(max_number_of_doffs)- sum(number_of_doffs_done) as count")->
        first();

        return $count_doffs["count"];

        $machine_input_output_band_goods_kinds = CurrentMachineInput::
        where([
            "allocation_id" => 1624,
            "machine_id" => 310,
        ])->groupBy("goods_kind_id")->pluck("goods_kind_id")->toArray();

        return $machine_input_output_band = CurrentMachineInput::
        where([
            "allocation_id" => 1624,
            "machine_id" => 310,
            "goods_kind_id" => 2
        ])->
        groupBy("material_id")->
        select("*")->
        selectRaw("count(material_id) input_line_count")->
        get();

//return Machine::find(312)->getCurrentAllocation()->items()->get()[0]->production;
        return FabricRaw:: ChangeLot(Machine::find(305)->getCurrentAllocation(), 7002011);

// گزاف جریان
        return
            $bom = Product\BOM\BOM::find(1988);
        $product = Product::find(1828);
//        Product\MaterialFlow::where([
//            "product_id"=>407,
//            "bill_of_material_id"=>$bom->id,
//        ])->
//        where("machine_type_id","!=",30)->
//        delete();
        $items = $bom->items()->
        join("products", "material_id", "products.id")->
        orderBy("goods_kind_id")->
        select("bill_of_material_item.id", "material_id", "input_line_code", "goods_kind_id")->
        get();
        $category = 0;
// ایجاد نود به ازای هر ورودی
        foreach ($items as $bom_item) {
            Product\MaterialFlow::create([
                "product_id" => $product->id,
                "bill_of_material_id" => $bom->id,
                "bill_of_material_item_id" => $bom_item->id,
                "machine_type_id" => 30,
                "band_code" => 1
            ]);
        }

        return $x % 100;

        return EndOfProductionCardTextureController::has_requirement_for_doffs(Machine::find(310));


        return DashboardController::getCurrentSelectedToExist(ProductRequestFormItem::find(1887));

        return Machine::find(306)->getFirstReserveAllocation()->items;

        return $this->getJsonFromTable("countries");
        $worker = Worker::find(161);

        return $current_post_users = PostUser::getCurrentPostByShiftWorkAndLeaveOvertime("post_user_object", $worker);

        return $post_ids = PostUser::getCurrentPostByShiftWorkAndLeaveOvertime("post_ids", $worker);
        $allowed_machine_ids = Line::getAllowedMachine($post_ids);

        return $machine_log_ids = MachineLog::
        whereIn("machine_id", $allowed_machine_ids)->
        groupBy("machine_id")->
        selectRaw("max(id) as id")->
        pluck("id");

        return $machine_logs = MachineLog::
        whereIn("id", $machine_log_ids)->
        where("operator_id", $worker->id)->
        where("machine_event_type_d", "!=", 650)-> // تحویل شیفت های تایید نشده
        pluck("machine_id")->toArray();


        return Machine::whereIn("id", $machine_logs)->get();

//        foreach ( PackingForm::where( "status_id", 7007003 )->whereNull( "warehouse_id" )->where( "id", ">", $x )->where( "id", "<", $y )->get() as $packing_form ) {
//            $item = $packing_form->items()->first();
//            echo "pfid=" . $packing_form->id . "<br/>";
//            if ( $item ) {
//
//                echo "itemid=" . $item->id . "<br/>";
//                $wp = WarehouseProduct::where( "packing_form_item_id", $item->id )->where( "output", 0 )->first();
//
//                if ( $wp ) {
//                    echo "wpid=" . $wp->id . "<br/>";
//                    $packing_form->warehouse_id = $wp->warehouse_id;
//                    $packing_form->save();
//                }
//            }
//        }

        return "OK";

//return $warps_is_in_warehouse = Warps::warpsExistInWarehouse( Allocation::find(1917) );
        return DashboardController::productCountInWarehouse(553, 1917);
//        foreach ( Post::all() as $item ) {
//            $m=new MenuPost();
//            $m->post_id=$item->id;
//            $m->menu_id=2601;
//            $m->save();
//
//        }

        return 1;

        return FabricRaw::getLotNumber(Allocation::find(1847), Machine::find(309), 0, Product::find(1764));

        return FabricRaw::ChangeLot(Allocation::find(1498));


        return Machine::find(314)->getCurrentProductionForm();

        return intval(850000.53);
        $product_request_form = ProductRequestForm::find(96);

        return $product_request_form->updateExistFormStatusForm(Form::find(2067));

//
//        foreach ( Product\BOM\BOMItem::where( "id", ">=", $x )->where( "id", "<", $y )->get() as $item ) {
//
//            if ( isset( $item->station_operation ) && count( $item->station_operation->station_sub_operation ) > 0 ) {
//                $item->station_sub_operation_id = $item->station_operation->station_sub_operation()->first()->id;
//                $item->save();
//            }
//
//            $consumed_product = Product\ConsumedProduct::where( [
//                "product_id"  => $item->product_id,
//                "material_id" => $item->material_id
//            ] )->get();
//            if ( count( $consumed_product ) == 0 ) {
//                Product\ConsumedProduct::create( [
//                    "product_id"  => $item->product_id,
//                    "material_id" => $item->material_id
//                ] );
//            } elseif ( count( $consumed_product ) > 1 ) {
//                Product\ConsumedProduct::where( [
//                    "product_id"  => $item->product_id,
//                    "material_id" => $item->material_id
//                ] )->first()->delete();
//            }
//        }

//        foreach (Product::all() as $product){
//            if($product->possibility_of_sale){
//                if(!Product\TypeOfSaleProduct\TypeOfSaleProductProduct::where("product_id",$product->id)->first()){
//                    Product\TypeOfSaleProduct\TypeOfSaleProductProduct::create(
//                       [ "product_id"=>$product->id,
//                           "type_of_sale_of_product_id"=>1
//                    ]);
//                }
//            }
//        }

        return "Ok";

        return FabricRaw::ChangeLot(Allocation::find(1656), false, 1336);

        return ProductRequestForm::find(612)->updateApplicantStatus(false, null);

        return number_format(48.5, 2);

        return $navs = \Auth::user()->getNavBars();

        return view("test");
        foreach (ProductRequestFormItem::whereNull("degree_id")->get() as $item) {
            $item->degree_id = $item->product->getMasterDegree();
            $item->save();
        }

        $packing_form = PackingForm::find(6200);

        return $packing_form_degree_ids = $packing_form->items()->pluck("degree_id", "product_id")->toArray();

        return Script1006Controller::handle();


        if (count($list) == 0) {
            return 0;
        }

        $efficiency = [];
        foreach ($list as $item) {
            $efficiency[$item->station_id]["amount"] = round($item->amount);
            $efficiency[$item->station_id]["efficiency"] = $item->theory_contour != 0 ? round($item->operation_contour / $item->theory_contour * 100) : 0;
            $efficiency[$item->station_id]["product"] = Product::find($item->product_id);
        }

        return $efficiency;

        return "OK";
        $order = Order::find(196);

// ارسال درخواست کالا از انبار
        return ProductRequestForm::newRequest(
            $order,
            $order->customer_id,
            30,
            1,
            null,
            $order->delivery_datetime,
            ""
        );

        return
            $worker = Worker::find(114);

        return $post_users = PostUser::getCurrentPostByShiftWorkAndLeaveOvertime("post_user_object");

        return $worker->getNavBars($worker);


        foreach (Worker::all() as $worker) {
            $worker->default_label_printer_id = $worker->default_printer_id;
            $worker->save();
        }

        return "OK";

        return PostUser::getCurrentUserByShiftWorkAndLeaveOvertimeByPostId("worker", 2037);
        $allocation = Allocation::find(1438);

        return FabricRaw:: ChangeLot($allocation, false, 1140);

        return Form::find(1741)->itemOrderByTransportCode($type = "group_by_packing_form");
        foreach (Form::find(1741)->item as $item) {
            echo $item->packing_form_item->packing_form_id . "_" . $item->degree_id . "<br/>";
        }

        return $list_id_order_by_transport_id = FormItem::
        join("packing_form_item", "packing_form_item.id", "packing_form_item_id")->
        leftJoin("transport_packing_form", "transport_packing_form.packing_form_id", "packing_form_item.packing_form_id")->
        orderBy("transport_packing_form.transport_item_id")->
        orderBy("transport_packing_form.packing_form_id")->
        where("form_item.form_id", 1741)->
        selectRaw("form_item.id")->
        get();


        foreach (TransportItem::get() as $item) {
            $item->code();
        }

        return ProductRequestFormForm::
        join("forms", "forms.id", "product_request_form_form.form_id")->
        join("form_item", "form_item.form_id", "forms.id")->
        join("packing_form_item", "form_item.packing_form_item_id", "packing_form_item.id")->
        selectRaw("distinct(packing_form_id) as count")->first();
//        foreach (TransportItem::get() as $item){
//            $item->code();
//        }
        $product_request_form = ProductRequestForm::find($x);
        $packing_list = DeliveryController::getPackingInWarehouse($product_request_form, null, 1);

        if (count($packing_list) == 0) {
            return back()->withErrors("هیچ بسته بندی  برای تحویل در انبار موجود نمی باشد.");
        }

        $packing_ids = [];
        foreach ($packing_list as $item) {
            $packing_ids[] = $item->id;
        }

        $packing_list_data_raw = PackingFormItem::
        join("products", "products.id", "product_id")->
        join("units", "units.id", "unit_id")->
        whereIn("packing_form_id", $packing_ids)->
        selectRaw("packing_form_id,sum(amount) as sum_amount, count(packing_form_item.id) as count_item, count(distinct(product_id)) as count_product_id, products.caption as product_caption, units.caption as unit_caption")->
        groupBy("packing_form_id")->
        get();
        $packing_list_data = [];
        foreach ($packing_list_data_raw as $item) {
            $packing_list_data[$item->packing_form_id]["sum_amount"] = $item->sum_amount;
            $packing_list_data[$item->packing_form_id]["count_item"] = $item->count_item;
            $packing_list_data[$item->packing_form_id]["count_product_id"] = $item->count_product_id;
            $packing_list_data[$item->packing_form_id]["product_caption"] = $item->product_caption;
            $packing_list_data[$item->packing_form_id]["unit_caption"] = $item->unit_caption;
        }

        return $packing_list_data;
        $packing_list_transport_item = TransportPackingForm::
        join("transport_item", "transport_item_id", "transport_item.id")->
        whereIn("packing_form_id", $packing_ids)->
        pluck("code", "packing_form_id")->toArray();

        return $packing_list_transport_item["4915"];

        return Printer::where("id", $x)->update(["width" => $y, "height" => $z]);

        $form = Form::find(1666);

        return $form->itemOrderByTransportCode(true);

        $get_price = [
            "serviceId" => 723,

            "weight" => 100,
            "insuranceName" => "غرامت تا سقف 300 هزار تومان",
            "packingDimension" => [
                "length" => 30,
                "width" => 22,
                "height" => 1
            ],
            "senderCityId" => 585,
            "receiverCityId" => 261,
            "goodsValue" => 500000,
            "printBill" => true,
            "printLogo" => false,
            "needCartoon" => false,
            "isCod" => true,
            "sendSms" => true,

        ];


        $new_service = [
            "serviceId" => 723,
            "GoodsType" => "نمونه پارچه",
            "ApproximateValue" => 500000,

            "Weight" => 100,
            "insuranceName" => "غرامت تا سقف 300 هزار تومان",
            "CartonSizeName" => 21087,//به کارتن نیاز ندارم
            "NeedCarton" => false,//به کارتن نیاز ندارم

            "Sender_FristName" => "علیرضا",
            "Sender_LastName" => "حسینی اربندآبادی",
            "Sender_mobile" => "09130023474",
            "Sender_StateId" => "1",
            "Sender_townId" => "585",

            "Sender_City" => "تهران",
            "Sender_PostCode" => "8919718588",
            "Sender_Address" => "تهران - میدان آزادی کنار باقالی فروشی",
            "Sender_Email" => "jalayegh@gmail.com",

            "Reciver_FristName" => "علیرضا",
            "Reciver_LastName" => "جلایق",
            "Reciver_mobile" => "09130656899",
            "Reciver_StateId" => "1",
            "Reciver_townId" => "585",

            "Reciver_City" => "یزد",
            "Reciver_PostCode" => "8919718588",
            "Reciver_Address" => "یزد - بلوار نواب صفوی کوچه قدس",
            "Reciver_Email" => "jalayegh@gmail.com",

            "IsCOD" => 1,
            "HasAccessToPrinter" => true,

            "boxType" => "نمی دانم",
            "Count" => 1,
            "orderSource" => 13,
            "refrenceNo" => 100,

            "SenderLat" => "54.36398612",
            "SenderLon" => "31.88789443",

            "ReciverLat" => "54.35581611",
            "ReciverLon" => "31.88680126",

            "IpAddress" => "109.125.144.51",


        ];
        $client = Posttex::client();
        $response = $client->request('post', 'checkout/newOrder',
            [
                'headers' => ["token" => Posttex::getToken(),],
                'query' => $new_service
            ]);
        $body = $response->getBody();

        return $body;


        $response = $client->request('get', 'town/getTowns',
            [
                'headers' => ["token" => Posttex::getToken(),],
                'query' => []
            ]);

        return $body = \GuzzleHttp\json_decode($response->getBody());

// Send a request to https://foo.com/root
        $response = $client->request('GET', '/root');

        return "false;";

        return Script1006Controller::handle();
        $start = Carbon::now()->format("Y/m/d H:m:s");
        echo $start . "<br/>.";

        $start_time_minute = Carbon::parse($start)->format("Y/m/d H:m:00");
        echo $start_time_minute . "<br/>..";
        $start_time_minute = Carbon::parse($start)->addMinute()->format("Y/m/d H:m:00");
        echo $diff = Carbon::parse($start_time_minute)->diffInSeconds($start) . "<br/>";

        echo Carbon::parse($start_time_minute)->addSecond(60)->format("Y/m/d H:m:00") . "<br/>";

        echo 60 - (int)$diff;

        return "";


        return Script1006Controller::handle();

        $worker = Worker::find(112);

// بررسی پست های جانشین مرخصی
        return PostUser::getCurrentPostByShiftWorkAndLeaveOvertime("post_ids", $worker);

        return $worker->posts;

        return $leaves_post_user_ids = LeaveOvertime::
        join("leave_overtime_types", "leave_overtime_types.id", "leave_overtime_type_id")->
        join("leave_overtime_confirmation", "leave_overtimes.id", "leave_overtime_id")->
        where("leave_overtime_group_id", 1)->//مرخصی
        whereIn("leave_overtimes.status_id", [4630003, 4630007, 4630006])->
        where("replace_user_id", $worker->id)->
        where("start_datetime", "<", now())->
        where("end_datetime", ">", now())->
        pluck("post_user_id")->
        toArray();

        return TransportPackingForm::pluck("packing_form_id")->toArray();

        return PackingForm::
        join("packing_form_item", "packing_forms.id", "packing_form_item.packing_form_id")->
        join("products", "product_id", "products.id")->

        whereNotIn("packing_form_id", [5])->
        toSql();


        return Script1006Controller::handle();

        return Hash::make(123);

        return $navs = \Auth::user()->getNavBars();
//      return  Product\BOM\BOMItem::where("material_id",490)->groupBy("product_id")->pluck("product_id")->toArray();
//        Artisan::call( "composer dump-autoload" );
//
//     return   ProductRequestForm::where("applicant_type_id",20)->groupBy("allocation_id")->
//        addSelect( DB::raw( "count(id) as count, id,allocation_id" ) )->having("count",">","1")->get();
//
//        return "OK";
//        foreach ( Machine::get() as $machine ) {
//
//            $production_forms = ProductionForm::where( "machine_id", $machine->id )->
//            where( "status_id", 7002006 )->
//            orderByDesc( "id" )->get();
//            foreach ( $production_forms as $production_form ) {
//                $item = $production_form->items()->orderByDesc( "id" )->first();
//
//                $log = MachineLog::
//                where( "machine_id", $machine->id )->
//                where( "id", "<", $item->end_of_machine_log_id )->
//                where( "machine_event_type_id", 520 )->
//                orderByDesc( "id" )->first();
//
//
//                if ( $log ) {
//                    $log->allocation_id = $item->allocation_id;
//                    $log->save();
//                }
//
//            }
//        }

        return Script1005Controller::handle();
        foreach (Report1010MachineLog::all() as $item) {
            $tarakom_pod = GoodsKindPropertyValue::where("product_id", $item->product_id)->whereIn("goods_kind_property_id", [
                220381 // تراکم نهایی پود (تئوری)
            ])->sum("value");

            $item->amount = $tarakom_pod > 0 ? ($item->operation_contour) / ($tarakom_pod * 100) : -1;
            $item->save();

        }

        return "OK";
//  Report1010MachineLog::where("id",">",0)->delete();
//  return Script1005Controller::handle(7000);

        $machine = Machine::find(310);

        foreach (Machine::get() as $machine) {

            $production_forms = ProductionForm::where("machine_id", $machine->id)->
            where("status_id", 7002006)->
            orderByDesc("id")->get();
            foreach ($production_forms as $production_form) {
                $item = $production_form->items()->orderByDesc("id")->first();

                $log = MachineLog::
                where("machine_id", $machine->id)->
                where("id", "<", $item->end_of_machine_log_id)->
                where("machine_event_type_id", 520)->
                orderByDesc("id")->first();


                if ($log) {
                    $log->allocation_id = $item->allocation_id;
                    $log->save();
                }

            }
        }

        return "ok";

        return FabricRaw:: ChangeLot($allocation, false, 767);

//        Warps::updateCarrierInCurrentInputOutputBand( $machine, "setCurrentCarrier", $allocation );

        return "OK";

        return $machine->getFirstReserveAllocation()->items()->first()->amount_of_each_doffs;

        return "OK";
//        Warps::updateCarrierInCurrentInputOutputBand(Machine::find(306));
//        FabricRaw:: ChangeLot( Allocation::find(992) );
//        return "OK";

        foreach (ProductRequestForm::where("status_id", 7005008)->get() as $product_request_form) {
            //تغییر وضعیت فرم های درخواست
            $status_id = 7005002; // تحویل (ارسال) شده

            $form_list = $product_request_form->getExistFormForConfirmList();
            if (count($form_list) > 1) { // بعد از اینجا وضعیت فرم تعییر می کند
                // اگر فرم دیگری برای تایید وجود دارد؟
                $status_id = 7005004;
            } else {
                foreach ($product_request_form->items as $prf_item) {

                    $product = $prf_item->product;

                    $min = $prf_item->amount_request * $product->goods_kind->be_lower_in_confirm_exit_form / 100;
                    if ($prf_item->amount_remaining > $min) {
                        $status_id = 7005008; // در انتظار تحویل (ارسال) بافی مانده کالا
                    }

                }
            }
            $product_request_form->status_id = $status_id;
            $product_request_form->save();
        }

        return "OK";
//        // Initialize an URL to the variable
//        $url = "http://192.168.180.190/login";
//
//// Use get_headers() function
//        $headers = @get_headers($url);
//
//// Use condition to check the existence of URL
//        if($headers && strpos( $headers[0], '200')) {
//            $status = "URL Exist";
//        }
//        else {
//            $status = "URL Doesn't Exist";
//        }

// Display result
//  return $status;

        return $is_static_ip = Setting::where("key", "static_ip")->where("string_value", "like", "%" . $_SERVER['HTTP_HOST'] . "%")->count() . "sdf";

        return "OK";
//$text="مدیر عامل شرکت حریر نام کویر";
//foreach ()
//        return substr_count(, ' ');
        return Script1004Controller::handle();

        return Product::
        join("packing_form_item", "products.id", "packing_form_item.product_id")->
        join("packing_forms", "packing_forms.id", "packing_form_id")->
        join("packing_types", "packing_types.id", "packing_type_id")->
        join("degrees", "degrees.id", "degree_id")->
        join("lot_numbers", "lot_numbers.id", "lot_number_id")->
        select("packing_form_item.product_id", "packing_type_id", "degree_id", "lot_number_id")->
        addSelect(DB::raw("SUM(final_amount) as inventory , SUM(sub_amount)  as sub_inventory,packing_form_item.product_id, degrees.caption,lot_numbers.code,packing_types.caption as pc"))->
        whereNull("packing_forms.deleted_at")->
        where("packing_forms.status_id", 7007003)-> //تحویل شده به انبار
        where("packing_forms.warehouse_status_id", 4201)-> // موجود در انبار
        groupBy("packing_form_item.product_id", "packing_type_id", "degree_id", "lot_number_id")->
        where("packing_form_item.product_id", 734)->
        get()->toArray();

        return 1;

        foreach (WarehouseProduct::get() as $item) {
//            if($item->id==885){
//              return  $form_item=FormItem::where([
//                    "form_id"=>$item->form_id,
//                    "product_i"=>$item->product_id,
//                    "packing_form_item_id"=>$item->packing_form_item_id,
//                    "degree_id"       => $item->degree_id,
//                    "lot_number_id"   => $item->lot_number_id,
//                ])->first();
//                return $item;
//            }
            if ($item->form_item_id != 0) {
                $form_item = FormItem::where([
                    "form_id" => $item->form_id,
                    "product_id" => $item->product_id,
                    "packing_form_item_id" => $item->packing_form_item_id,
                    "degree_id" => $item->degree_id,
                    "lot_number_id" => $item->lot_number_id,
                ])->first();


                if ($form_item) {
                    $form_item->description = $item->getDesc();
                    $form_item->save();

                    $item->form_item_id = $form_item->id;
                    $item->save();
                }
            }
        }


        Script1003Controller::handle();

        return;
//تغییر وضعیت فرم های درخواست
        $prf = ProductRequestForm::find(101);
        $status_id = 7005002; // تحویل (ارسال) شده
        foreach ($prf->items as $prf_item) {

            $product = $prf_item->product;

            $min = $prf_item->amount_request * $product->goods_kind->be_lower_in_confirm_exit_form / 100;
            if ($prf_item->amount_remaining > $min) {
                return $min . " " . $prf_item->amount_remaining;
                $status_id = 7005008; // در انتظار تحویل (ارسال) بافی مانده کالا
            }

        }

        return $status_id;

        foreach (WarehouseProduct::where("output", 0)->get() as $item) {

            if ($item->packing_type_id == 0) {
                echo $item->id . "<br/>";
                $item->packing_type_id = $item->packing_form_item->packing_form->packing_type_id ?? -1;
                $item->save();
            }
        }

        return "OK";
///  return LotNumber::find(1127)->getPropertyValue(1,"value");
//  echo DNS1D::getBarcodeSVG( '4445645656', 'C39', 1.3, 60 );;;
//   $worker=Worker::find(1);
//  Notification::send( "00" . ( $worker->country->area_code ?? "98" ) . $worker->mobile, new SMSNotification( "resetpass", $worker->fullname() ) );

        $machine = Machine::find(315);
        $allocation = Allocation::find(569);

//         $product_request_form = ProductRequestForm::getLatestRequestForm( $machine->id, 10 )->forms[0]->form->item;
//        $current_input_list = CurrentMachineInput::
//        where( [ "machine_id" => $machine->id, "allocation_id" => $allocation->id, "goods_kind_id" => 3 ] )->
//        orderBy( "input_line_code" )->
//        get();

//        Warps::updateCarrierInCurrentInputOutputBand( Machine::find( 315 ), "setCurrentCarrier", Allocation::find( 569 ) );

        return FabricRaw:: ChangeLot(Allocation::find(569));
//       return $printers = Printing::printers();
//        $printJob = Printing::newPrintTask()
//                            ->printer(0)
//                            ->file('test.pdf')
//                            ->send();
//return "OK";


//        foreach ($printers as $printer) {
//            echo $printer->name();
//        }
//
//       return Printing::printers();

//  event( new WarpsAvailableEvent(Product::find(524) ) );
//   return Warps::warpsCountInWarehouse( 407, 381 );

//        return Warps::warpsExistInWarehouse( Allocation::find( 381 ) );


        return DashboardController::getRemainingAmountOfWarps(ProductRequestForm::find(40), ProductRequestFormItem::find(456));

        return FabricRaw:: ChangeLot(Allocation::find(331));


        return "OK";
        $list = Product\ProductRequest\ProductRequestForm::whereNull("applicant_type_id")->get();
        foreach ($list as $item) {
            $item->applicant_id = $item->machine_id;
            $item->applicant_type_id = 10;
            $item->save();
        }


        return QrCode::generate(asset("assets/images/favicon.ico"));

//   return 1;
//  return Warps::warpsExistInWarehouse( Allocation::find(273) );
//return FabricRaw::getLotNumber(Allocation::find(280),Machine::find(318),1 ,Product::find(522));

// ProductionForm::find(42)->setEndMachineLog(MachineLog::find(6008));
        return ProductionForm::find(42)->updateAmount();

        return FabricRaw::ChangeLot(Allocation::find(279), false, false);
//event( new WarpsAvailableEvent( Product::find(524) ) );

//        $allocation = Machine::find(316)->getCurrentAllocation();
//
//        // پیدا کردن کد چله
//     return   Warps::warpsCountInWarehouse( 407, $allocation->id );
//
//       return FabricRaw::ChangeLot(Allocation::find(263),false,false);
//        $production_formitem=ProductionFormItem::where("id","<",14)->get();
//        // بروز رسانی مقدار فرم ها
//        foreach ( $production_formitem as $item ) {
//            $item->updateItemAmount( true, false,true );
//        }
        return;
//        return FabricRaw::getLotNumber(
//            Allocation::find( 250 ),
//            Machine::find( 316 ),
//            1,
//            Product::find( 508 )
//        );

        return FabricRaw:: ChangeLot(Allocation::find(250), false, 9);

        return $warps_is_in_warehouse = Warps::findWarpsIdFromMachine(450, Allocation::find(234));

// return   Notification::send( "09130656899", new SMSNotification( "addpost","مدیر سیستم", null,null,"علیرضا جلایق","کارخانه حریر نام یزد" ) );

        return Hash::make("User@159357");
// return    GoodsKindProperty::find(220239)->property_product_type[0]->product_type;
//        $first = Product::on("mysql2")->selectRaw("1 as c ,caption, id")
//                   ->where( 'id', ">", 1 )->where( "id", "<", 5 );
//
//        $users =  Product::on("mysql")->selectRaw("2 as c , caption, id")
//                   ->where( 'id', ">", 3 )->where( "id", "<", 7 )
//                   ->union( $first )
//                   ->get();
//
//        return $users;
// return  Warps::updateCarrierInCurrentInputOutputBand( Machine::find(219), "setEmpty" )?1:2;

//  return Allocation::getDifferentTowAllocation( Allocation::find( 178 ), Allocation::find( 213 ), "Fabric_Raw" );


//        $list = Carrier::whereIn( "carrier_type_id", [ 1, 101 ] )->get();
//        foreach ( $list as $item ) {
//            if ( $item->status_id == 5320002 && $item->product->count() == 0 ) {
//
//                $wp = WarehouseProduct::where( [
//                    "carrier_id" => $item->id,
//                    "output"     => 0
//                ] )->orderByDesc( "id" )->first();
//              //  if ( ( $item->firstProductId() != ( $wp->product_id ?? - 2 ) ) ) {
//                    echo $item->id . " code:" . $item->code . "  product id:" . $item->firstProductId() . "," . ( $wp->product_id ?? - 2 ) . "=>" . ( $item->firstProductId() == ( $wp->product_id ?? - 2 ) ? "OK" : "Error" ) . "<br/>";
//               // }
//            }
//        }

//$item=WarpsRequestFormItem::find(295);
//
//        $carrier_list = Carrier::join( "carrier_product", "carrier_id", "carriers.id" )->
//        where( [
//            "product_id" => 408,
//            "status_id"  => 5320002
//        ] )->pluck( "carrier_id" );
//
//       return $lot_number_list = WarehouseProduct::where( [ "product_id" => 408 ] )->
//        whereIn( "carrier_id", $carrier_list )->
//        whereIn("packing_type_id",  [2])->
//        where( "output", 0 )->
//        orderBy( "lot_number_id" )->
//        orderByDesc( "id" )->
//        take( 10000 )->
//        get();


//        $list = MachineLog::whereNotNull( "contour_1_value" )->get();
//        foreach ( $list as $item ) {
//            $item->contour_sum_value = $item->contour_1_value + $item->contour_2_value + $item->contour_3_value;
//            $item->save();
//        }
//
//        $list = Machine::get();
//        foreach ( $list as $item ) {
//            Warps::updateCarrierInCurrentInputOutputBand( $item );
//        }

//        return MachineLog::select( 'id', 'machine_id', 'created_at' )
//                         ->selectRaw( 'LEAD(created_at,1) OVER (
//                                        PARTITION BY machine_id
//                                        ORDER BY id ) nextOrderDate' )
//                         ->get();
    }

    public
    function f23Pallet()
    {
        // پیدا کردن بسته بندی داخل پالت
        $list = FormItem::where("form_id", 983)->where("product_id", 122)->get();
        foreach ($list as $item) {

            $x = PalletItem::where("pallet_id", 1508)->
            where("packing_form_id", $item->packing_form_item->packing_form_id)->
            first();
            if (!$x) {
                echo $item->packing_form_item->packing_form->code . "<br/>";
            }
        }
    }

    public
    function FabricWithChannel()
    {
        // لیست پارچه های خام همراه با کانال تولید و ...
        $list = [];

        foreach (Product::where("goods_kind_id", 5)->get() as $product) {
            $product_value = [];
            $product_value["fabric_id"] = $product->id;
            $product_value["fabric_code"] = $product->code;
            $product_value["fabric_caption"] = $product->caption;
            $product_value["product_property"] = Message::convert_farsi_digits_to_english($product->getPropertyValue(220337, "value"));
            $product_value["product_property_takmil"] = Message::convert_farsi_digits_to_english($product->getPropertyValue(220343, "caption"));
            $product_value["color"] = Message::convert_farsi_digits_to_english($product->getPropertyValue(220338, "caption"));
            $product_value["color_az"] = Message::convert_farsi_digits_to_english($product->getPropertyValue(220336, "caption"));

            $row_product = $product->consumed_product()->first();
            $row_product = $row_product->material ?? null;
            $product_value["raw_product_code"] = "";
            $product_value["raw_product_caption"] = "";
            $product_value["raw_product_property"] = "";
            if ($row_product) {
                $product_value["raw_product_code"] = $row_product->code;
                $product_value["raw_product_caption"] = $row_product->caption;
                $product_value["raw_product_property"] = Message::convert_farsi_digits_to_english($row_product->getPropertyValue(220219, "value"));
            }

            $liproduct = LineProductStation::where("product_id", $product->id)->first();
            if ($liproduct) {
                $product_value["production_channel_type_id"] = $liproduct->production_channel_type->caption ?? "";
                $product_value["contractor_operation"] = $liproduct->contractor_operation->caption ?? "";
            }
            $list[] = $product_value;

        }

        return $list;
    }

    public
    function PackingFormWithShelf()
    {
        // لیست بسته بندی های نخ با جایگاه
        $list = PackingFormItem::
        join("packing_forms", "packing_forms.id", "packing_form_id")->
        join("products", "products.id", "product_id")->
        where("goods_kind_id", 2)->

        selectRaw("products.code as pcode, products.caption as pcaption, packing_forms.code as code, final_amount , packing_form_id,warehouse_shelving_id")->
        with("packing_form")->
        get();

        $packing_forms = [];
        foreach ($list as $item) {
            $packing_forms[] = [
                "packing_form_id" => $item->packing_form_id,
                "code" => $item->code,
                "final_amount" => $item->final_amount,
                "product" => $item->pcode,
                "product_caption" => $item->pcaption,
                "warehouse_caption" => $item->packing_form->warehouse->caption ?? "",
                "status" => $item->packing_form->status->caption ?? "",
                "warehouse_shelving_id" => $item->warehouse_shelving_id,
            ];
        }

        return $packing_forms;
    }

    public
    function func1()
    {
        // تابعی که نام پارچه تکمیل شده و چله و مشخصات کالایی را در یک فایل اکسل آماده می کند.
        $list = Product::where("goods_kind_id", 5)->get();
        $list_product = [];
        foreach ($list as $product) {
            $bom_item = BOMItem::where("product_id", $product->id)->first();
            if (!$bom_item) {
                continue;
            }
            $line_product_station = LineProductStation::where("product_id", $product->id)->first();

            $warps = BOMItem::where("product_id", $bom_item->material_id)->
            join("products", "products.id", "=", "material_id")->
            where("goods_kind_id", 3)->first();

            $liproduct = LineProductStation::where("product_id", $product->id)->first();


            if (!$warps || !$warps->material) {
                $list_product[] = [
                    "fabric_code" => $product->code,
                    "fabric_caption" => $product->caption,
                    "fabric_raw_code" => $bom_item->material->code,
                    "fabric_raw_caption" => $bom_item->material->caption,
                    "warps_code" => "",
                    "warps_caption" => "",
                    "operation" => $line_product_station->contractor_operation->caption ?? "",
                    "color" => $product->getPropertyValue(220338, "value"),
                    "sabc" => $product->getPropertyValue(220555, "value"),
                    "product_id" => $product->id,
                    "production_channel_type_id" => $liproduct->production_channel_type_id ?? "",
                ];
                continue;
            }
            $warps = $warps->material;

            $list_product[] = [
                "fabric_code" => $product->code,
                "fabric_caption" => $product->caption,
                "fabric_raw_code" => $bom_item->material->code,
                "fabric_raw_caption" => $bom_item->material->caption,
                "warps_code" => $warps->code,
                "warps_caption" => $warps->caption,
                "operation" => $line_product_station->contractor_operation->caption ?? "",
                "color" => $product->getPropertyValue(220338, "value"),
                "sabc" => $product->getPropertyValue(220555, "value"),
                "product_id" => $product->id,
                "production_channel_type_id" => $liproduct->production_channel_type_id ?? "",
            ];


        }
        return $list_product;


    }

    public
    function catalog_index()
    {
        return view("website.catalog.index");
    }

    public
    function catalog_submit(Request $request)
    {

        if (!$request->mobile) {
            return back()->withErrors("لطفا شماره موبایل را وارد نمایید.");
        }
        Catalog::create($request->all());

        $template = "catalogtemplate";
        $token = "1";


        $result = (new KavenegarApi(env("KAVENEGAR_APIKEY")))->VerifyLookup(
            $request->mobile,
            "12345",
            "",
            "",
            $template,

        );

        return back()->with(["success" => "سرور ارجمند " . $request->fullname . "<br/>" . "لینک دریافت کاتالوگ برای  شما ارسال گردید."]);
    }

    public
    function getAlgorithm()
    {
        $alphabet = ["A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z"];
        $raport = " 5B 5A 3B 3A 4B 2A 1B  2B 5A 3B 2B 5A 3B   2B 5A 3B 2A 5B 3A 8B 3A 19B 9A 11B 3A 6B 3A 5B 4A 4B 4A 4B 4A 4B 4A 4B 4A 4B 6A 3B 6A 4B 12A 4B 9A 3B 10A 5B 8A";
        $raport = str_replace(" ", "", $raport);
        $raport_split = [];
        $k = 0;
        $j = 0;
        $alpha = "";
        while (strlen($raport) > 0 && $k < 26) {


            $split = explode($alphabet[$k], $raport, 2);

            if (preg_match("/[a-z]/i", $split[0])) {
                $k++;
                continue;
            }
            for ($i = 0; $i < $split[0] + 0; $i++) {
                $raport_split[$j] = $alphabet[$k];
                $j++;
                // echo $alphabet[$k] . "<br/>";
            }
            $raport = $split[1];
            $k = 0;
        }
        print_r($raport_split);
        return $raport_split;
    }

    public
    function tara()
    {
        function signMessage($privateKey, $message)
        {
            // Ensure the private key is in the correct format (also handles PEM format)
            $privateKeyResource = openssl_pkey_get_private($privateKey);

            if ($privateKeyResource === false) {
                throw new Exception('Invalid private key.');
            }

            // Generate a signature
            $signature = '';
            if (!openssl_sign($message, $signature, $privateKeyResource, OPENSSL_ALGO_SHA256)) {
                throw new Exception('Failed to sign message.');
            }

            // Return the signature in a binary format, or you can Base64 encode it if needed
            return $signature;
        }

// Example usage
        try {
            $pemPrivateKey = "-----BEGIN PRIVATE KEY-----\nMIG..."; // Your PEM private key here
            $message = "This is the message to sign";

            $signature = signMessage($pemPrivateKey, $message);
            echo "Signature generated successfully.\n";

            // Optionally, you can encode the signature in base64 to make it easier to handle
            $base64Signature = base64_encode($signature);
            echo "Base64 Encoded Signature: $base64Signature\n";
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
        function readPrivateKey($prvKey)
        {
            // Remove header and footer if they exist
            $prvKey = preg_replace('/-----BEGIN PRIVATE KEY-----/', '', $prvKey);
            $prvKey = preg_replace('/-----END PRIVATE KEY-----/', '', $prvKey);

            // Remove any whitespace from the key
            $prvKey = str_replace("\n", '', $prvKey);

            // Create the private key in PEM format
            $pemKey = "-----BEGIN PRIVATE KEY-----\n" . chunk_split($prvKey, 64, "\n") . "-----END PRIVATE KEY-----\n";

            // Get the private key resource
            $privateKey = openssl_pkey_get_private($pemKey);

            if ($privateKey === false) {
                throw new Exception('Invalid private key');
            }

            return $privateKey;
        }

// Example usage
        try {
            $pemPrivateKey = "-----BEGIN PRIVATE KEY-----\nMIG..."; // Your PEM private key here
            $privateKey = readPrivateKey($pemPrivateKey);
            echo "Private key imported successfully.";
            // Use the private key for further processing (e.g., signing, decryption, etc.)
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
// Load private key from a PEM file
        $privateKeyFile = 'path/to/your/privatekey.pem';
        $privateKey = file_get_contents($privateKeyFile);

        if ($privateKey === false) {
            die('Unable to read private key file');
        }

// Get the private key resource
        $privateKeyResource = openssl_pkey_get_private($privateKey);
        if ($privateKeyResource === false) {
            die('Failed to load private key: ' . openssl_error_string());
        }

// Data to be signed
        $data = "This is a message to be signed.";

// Sign the data
        if (openssl_sign($data, $signature, $privateKeyResource, OPENSSL_ALGO_SHA256)) {
            // Output the signature
            $signatureBase64 = base64_encode($signature);
            echo "Signature: " . $signatureBase64;
        } else {
            echo 'Error signing data: ' . openssl_error_string();
        }

// Clean up
        openssl_free_key($privateKeyResource);

    }

    public
    function SetToWarehouse(Transport $transport)
    {


        foreach ($transport->items as $transportItem) {

            foreach ($transportItem->transport_packing_list as $item) {

                if ($item->packing_form->status_id == 7007002) {

                    \App\Http\Controllers\Warehouse\DashboardController::SubmitNewPackingToWarehouse($item->packing_form, $transportItem->user_id);
                }
            }
        }
    }


    function extractDomain($value)
    {
        // اطمینان از اینکه URL معتبر است
        if (!preg_match('#^https?://#', $value)) {
            $value = 'http://' . $value; // اضافه کردن پروتکل اگر نبود
        }

        $host = parse_url($value, PHP_URL_HOST);
        return $host;
    }

    public
    function submit_test()
    {
    }

    public
    function trans_test()
    {
        DB::transaction(function () {
            $form = Form::find(208);
            $form->code = 1;
            $form->save();

            $form = Form::find(207);
            $form->code = 21;
            $form->save();
        });
    }

    public
    static function DeviceInfo()
    {

        $agent = new Agent();
        if ($agent->isMobile()) {
            $deviceType = 'تلفن همراه';
            $deviceBrand = $agent->device();
            $deviceModel = $agent->version($deviceBrand);
            $deviceType = "(" . $deviceBrand . " " . $deviceModel . ")" . $deviceType;
        } elseif ($agent->isTablet()) {
            $deviceType = 'تبلت';
        } else {
            $deviceType = 'دسکتاب';
            $browser = $agent->browser();
            $version = $agent->version($browser);
            $deviceType = $deviceType . " (" . $browser . " " . $version . " - " . $agent->platform() . ")";
        }
        return $deviceType;
    }

    public
    function callServer()
    {
        phpinfo();
        $params = array('uri' => 'soap/public/server');
        $server = new \SoapServer(null, $params);
        $server->setClass('App\Server');
        $server->handle();
    }

    public
    static function getCurrentSelectedToExist(
        ProductRequestFormItem $product_request_form_item
    )
    {


        $packing_forms = PackingForm::whereIn("id", [7099]);

        return PackingForm::LowestLevelOfPackingForm($packing_forms);
        // محاسبه مقدار انتخاب شده جهت خروج
        $session_data = ProductRequestFormSessionData::
        getData($product_request_form_item->product_request_form);

        $selected_packing_ids = $session_data["selected_packing_ids"];
        if (!$selected_packing_ids) {
            $selected_packing_ids[] = -1;
        }
        $packing_type_ids = $product_request_form_item->product_request_form_packing_types()->pluck("packing_type_id")->toArray();

        $value = [];

        return $value["amount"] = PackingFormItem::join("packing_forms", "packing_forms.id", "packing_form_id")->
        whereIn("packing_type_id", $packing_type_ids)->
        where(["product_id" => $product_request_form_item->product_id])->
        //where( [ "degree_id" => $product_request_form_item->degree_id ] )->
        whereIn("packing_form_id", $selected_packing_ids)->
//        addSelect( DB::raw( "sum(final_amount) as amount" ) )->
        get();

        $value["count"] = count(PackingFormItem::
        where(["product_id" => $product_request_form_item->product_id])->
        //where( [ "degree_id" => $product_request_form_item->degree_id ] )->
        whereIn("packing_form_id", $selected_packing_ids)->
        groupBy("packing_form_id")->
        select("packing_form_id")->
        get());

        return $value;

        //addSelect( DB::raw( "count(packing_form_id) as count" ) )->
    }

    public
    function testNosa()
    {

        $dbName = "_AccXP_1403";

        $userName = "api_user";
        $password = "P@ssw0rd321";
        //$methodName = "WS_AddDet";


        //$methodName = "WS_AddInvArt";
        $methodName = "WS_AddSalesArt";

//        $data =
//            "<_Art  InvDocClass='1'  FA_Date='1402/02/11' Desc='تست خروج' ODesc='تست Description' DeptCode='1'  InvKindCode='205' InvSeries='1' InvCenterCode='04/123' StockCode='4' ImpId='1004' >\n" .
//            "  <_Trans Desc='شرح عملیات خروج' ODesc='Description' InvTransKind='11'  InvCenterCode='04/123' StockCode='4' InvKind='1' MatCode='50/37/012'  InvAmount='100' />\n" .
//            "</_Art>";


        $data =
            "<_Art  InvDocClass='0'  FA_Date='1403/02/11' Desc='تست شرح ورود' ODesc='تست Description' DeptCode='1'  InvKindCode='3' InvSeries='1' InvCenterCode='06/0004' StockCode='5' ImpId='1003' >\n" .
            "  <_Trans Desc='شرح عملیات' ODesc='Description' InvTransKind='5'  InvCenterCode='06/0004' StockCode='9' InvKind='1' MatCode='25/0003/093501'  InvAmount='100' />\n" .
            "</_Art>";


//        $methodName = "WS_GetMatRemain";
//
//        $data =
//            "<_Params  AllStocks='1' >\n".
//            "  <_Dept  Code='01/03/01/423' />\n".
//            "</_Params>";

        $price = 425393000 + 1648;
        // فاکتور فروش
//        $data =
//            "<_Art SalesDocKind='0'   FA_Date='1403/09/22' Desc='تست فاکتور فروش' ODesc='تست فاکتور Description' DeptCode='1' DeliveryCondCode='1' PaymentMethodCode='1' SalesSeries='2' CustCode='02/0054' DebSide='0'  SalesKindCode='1' SalesCenterCode='1'  ImpId='3' CalcState='1' >\n" .
//            "  <_Trans SalesKind='5' Desc='شرح عملیات فروش' ODesc='Description' StockCode='5' MatCode='1547' SalesUnitPrice='425393000' SalesPrice='" . $price . "' SalesDirDis='1648' SalesDirFare='0' SalesDirTax='38285370' Amount='1'  />\n" .
//            "  <_Trans SalesKind='0'  AccCode='113/03/001'  />\n" .
//            "  <_Trans SalesKind='4'  AccCode='210/06/003'  />\n" .
//            "  <_Trans SalesKind='2'  AccCode='532/002/02'  />\n" .
//            "</_Art>";


        $params = array(
            'ADBName' => $dbName,
            'AData' => $data
        );

        $options = array(
            'login' => $userName,
            'password' => $password
        );


        $srvUri = "http://192.168.1.22:8085" . '/AccXPSOAP/AccXPSOAPServer.dll/wsdl/IAccXPSOAPSrvDM';

        $client = new \SoapClient($srvUri, $options);

        $callResult = $client->__soapCall($methodName, $params);
        $array = XmlToArray::convert($callResult);

        return $array["@attributes"];
    }

    public
    function getJsonFromTable(
        $table, $caption = "caption"
    )
    {

        $list = DB::table($table)->get();
        $json = [];
        foreach ($list as $item) {
            $json[$item->id] = $item->$caption;
        }

        return $json;
    }

    private
    function setEnv(
        $key, $value
    )
    {
        echo file_put_contents(app()->environmentFilePath() . ".test", str_replace(
            $key . '=' . env($key),
            $key . '=' . $value,
            file_get_contents(app()->environmentFilePath() . ".test")
        ));
    }

//
// https://codedthemes.com/demos/admin-templates/datta-able/bootstrap/default/animation.html#!
    public
    function testRFW($from)
    {

        $list = Order::where("id", "<", $from)->get();
        foreach ($list as $item) {

            echo "<br/>" . $item->id;
            $item->id;

            $order_item = OrderList::where("order_id", $item->id)->first();

            if (isset($order_item)) {
                $item->description_sheet_id = $order_item->description_sheet_id;
                $item->save();
            }

            // $customer=Customer::where("code","like","0".$item->customer_code)->first();

            // if(!$customer){
            // // echo "error customer_id?".$item->id;
            //     $item->customer_id=0;
            //     $item->save();
            // }
            // else{
            //     $item->customer_id=$customer->id;
            //     $item->save();
            // }


            //    $newOrderList=TrmOrderList::where("id",$item->id)->first()->toArray();
// if($newOrderList->production_card_code){
//     echo $newOrderList->production_card_code;
// }
//                $item->production_card_code=$newOrderList->production_card_code??"";
//                $item->save();

            //  $item->update($newOrderList);
            echo "<br/>" . $item->id;

        }

    }

    public
    function testRFW5()
    {

        $list = Production::get();

        foreach ($list as $item) {

            $order = Order::where("id", $item->order_id)->first();

            if ($item->series == 0 && isset($order)) {
                echo 1;
                $item_order = Order::where("code", "like", $order->code)->where("series", 1)->first();
                if ($item_order) {
                    $item->order_id = $item_order->id;
                    echo $item->code . "<br/>";
                    $item->save();
                }

            }


        }

    }

    public
    function update_pc_id($from, $to)
    {
        $list = RequstFromWarehouse::where("id", ">=", $from)->where("id", "<", $to)->get();
        foreach ($list as $item) {

            $rfw = Production::where("serial", "like", $item->production_card_series)->first();

            if (!$rfw) {
                echo $item->production_card_series;
            } else {
                $item->production_card_id = $rfw->id;
            }

            $item->save();

        }
    }

    public
    function testRFW1()
    {
        // $list=TrmRFW::get();

        foreach ($list as $item) {

            // $customer=Customer::where("code","like",Str::of($item->customer_code)->trim())->first();

            //     if(!$customer){
            //        // echo "error customer_id?".$item->id;
            //         $item->customer_id=0;
            //         $item->save();
            //     }
            //     else{
            //         $item->customer_id=$customer->id;
            //         $item->save();
            //     }

            // if($item->order_code!="0"){
            //         $order=Order::firstOrCreate(["code"=>$item->order_code,"series"=>$item->order_series??0]);

            //         $item->order_id=$order->id;
            //         $item->save();

            //     }

            // $product=Product::GetIdFromCode(Str::of($item->product_code)->trim());
            // if(!isset($product)){
            //     echo $item->product_code."<br/>";

            // }
            // else{
            //      $item->product_id=$product->id;
            //      $item->save();
            // }

            // $material=Product::GetIdFromCode(Str::of($item->material_code)->trim());
            // if(!isset($material)){
            //     echo $item->material_code."<br/>";

            // }
            // else{
            //      $item->material_id=$material->id;
            //      $item->save();
            // }
        }

    }

    function my_encrypt($data, $passphrase)
    {
        $secret_key = hex2bin($passphrase);
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $encrypted_64 = openssl_encrypt($data, 'aes-256-cbc', $secret_key, 0, $iv);
        $iv_64 = base64_encode($iv);

        $json["iv"] = $iv_64;
        $json["data"] = $encrypted_64;
        return base64_encode(json_encode($json));
    }

    function my_decrypt($data, $passphrase)
    {
        $secret_key = hex2bin($passphrase);
        $json = json_decode(base64_decode($data));
        $iv = base64_decode($json->{'iv'});
        $encrypted_64 = $json->{'data'};
        $data_encrypted = base64_decode($encrypted_64);
        $decrypted = openssl_decrypt($data_encrypted, 'aes-256-cbc', $secret_key, OPENSSL_RAW_DATA, $iv);
        return $decrypted;
    }

    function is_base64($s)
    {
        // Check if there are valid base64 characters
        if (!preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $s)) return false;

        // Decode the string in strict mode and check the results
        $decoded = base64_decode($s, true);
        if (false === $decoded) return false;


        $string = base64_decode($s, true);
        if (!Str::contains($string, ["_start_code_"])) {
            return false;
        }

        if (!Str::contains($string, ["_end_code_"])) {
            return false;
        }
        $string = str_replace('_start_code_', '', $string);
        $string = str_replace('_end_code_', '', $string);
        return $string;

    }


//     public function testProductDate(){
//         TrmProductionDate::get();

//         foreach($list as $item){

//                 // $customer=Customer::where("code","like",Str::of($item->customer_code)->trim())->first();

//         //     if(!$customer){
//         //        // echo "error customer_id?".$item->id;
//         //         $item->customer_id=0;
//         //         $item->save();
//         //     }
//         //     else{
//         //         $item->customer_id=$customer->id;
//         //         $item->save();
//         //     }

//         // if($item->order_code!="0"){
//         //         $order=Order::firstOrCreate(["code"=>$item->order_code,"series"=>$item->order_series??0]);

//         //         $item->order_id=$order->id;
//         //         $item->save();

//         //     }

//         $product=Product::GetIdFromCode(Str::of($item->product_code)->trim());
//         if(!isset($product)){
//             echo "error product_id ?".$item->id;

//         }
//         else{
//              $item->product_id=$product->id;
//              $item->save();
//         }


//     }
//     public function verta($shamshi_str="1399/09/13"){
//         $shamsi=Str::of($shamshi_str)->explode("/");
//        $milidi= Verta::getGregorian($shamsi[0],$shamsi[1],$shamsi[2]);
//        return Carbon::create($milidi[0],$milidi[1],$milidi[2]);
//     }
//     public function test(){
//         return "5";
//     }

//     public function testProductCode(){

//        $list= TrmProductionCard:: get();
//        foreach($list as $item){


//         // $customer=Customer::where("code","like",Str::of($item->customer_code)->trim())->first();

//         //     if(!$customer){
//         //        // echo "error customer_id?".$item->id;
//         //         $item->customer_id=0;
//         //         $item->save();
//         //     }
//         //     else{
//         //         $item->customer_id=$customer->id;
//         //         $item->save();
//         //     }

//         // if($item->order_code!="0"){
//         //         $order=Order::firstOrCreate(["code"=>$item->order_code,"series"=>$item->order_series??0]);

//         //         $item->order_id=$order->id;
//         //         $item->save();

//         //     }

//                 $product=Product::GetIdFromCode(Str::of($item->product_code)->trim());
//         if(!isset($product)){
//             echo "error product_id ?".$item->id;

//         }
//         else{
//              $item->product_id=$product->id;
//              $item->save();
//         }

//         // $line=Line::GetIdFromCode(Str::of($item->line_code)->trim());
//         // if(!isset($line)){
//         //     echo "".$item->line_code."<br/>";

//         // }
//         // else{
//         //      $item->line_id=$line->id;
//         //      $item->save();
//         // }


//     }

//     return "OK";
// }

//     public function testOrderList(){
//         //
//      $list= TrmOrder::where("id","<",1000)-> get()->toArray();
//   // OrderList::insert($list);


//             // $error="";
//             // $customer=Customer::where("code","like",$item->customer_code)->first();

//             // if(!$customer){
//             //     $error.= "error customer_id?".$item->id;
//             // }
//             //  $item->customer_id=$customer->id;

//             $order=Order::firstOrCreate(["code"=>$item->order_code,"series"=>$item->order_series]);

//            // $order->order_datetime=$item->order_datetime;
//            // $order->priority_id= $item->priority_id;
//             $order->customer_id= $customer->id??0;

//     //         $item->order_id=$order->id;
//     //        $production_card=Production::where("serial","like",$item->production_card_code)->firstOrCreate(["serial"=>$item->production_card_code]);

//     //        $item->production_card_id=$production_card->id;

//     //       $product=Product::GetIdFromCode($item->product_code);
//     //       if(!isset($product)){
//     //         $error.= "error product_id ?".$item->id;


//     //     }
//     //     else{
//     //          $item->product_id=$product->id;
//     //     }

//     //    echo "<br/>".$item->id;
//     //         $item->error=$error;
//     //     $item->save();


//         }
//        //  return DB::table("tem_orderlist")->where("error","!=","")->get();

//     public function uploadOrderList(){

//         $list= DB::table("tem_orderlist")->get()->toArray();

//         OrderList::insert($list);

//     }


//     // public function uploadProductionCard(){


//     //     Worker::
//     // }


}
