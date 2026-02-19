<?php

namespace App\Models\Utility;


use App\Http\Controllers\Utility\Script\Script1025Controller;
use App\Models\Accounting\Account;
use App\Models\Accounting\Bank\Bank;
use App\Models\Accounting\CheckDeliveryTimeType;
use App\Models\Accounting\Contract\ClauseArticle;
use App\Models\Accounting\Contract\ClauseType;
use App\Models\Accounting\Contract\Contract;
use App\Models\Accounting\Contract\ContractKeyword;
use App\Models\Accounting\Contract\ContractType;
use App\Models\Accounting\CostCenter;
use App\Models\Accounting\FinancialOperation\FinancialOperationPattern;
use App\Models\Accounting\FinancialOperation\FinancialOperationPatternItemType;
use App\Models\Accounting\FinancialOperation\FinancialOperationPatternType;
use App\Models\Accounting\HeadOfCheckType;
use App\Models\Accounting\PaymentMethod;
use App\Models\Accounting\SellingType;
use App\Models\Accounting\Tariff\ProductTariff;
use App\Models\Accounting\Tariff\Tariff;
use App\Models\Contractor\Contractor;
use App\Models\Contractor\ContractorOperation;
use App\Models\Contractor\ContractorProductionChannelType;
use App\Models\Contractor\ContractorSupplyType;
use App\Models\Customer\ChannelType;
use App\Models\Customer\Customer;
use App\Models\Customer\CustomerType;
use App\Models\GoodsKindProcess\Fabric_Raw\FabricRawTypeOfCut;
use App\Models\HR\Agent\AgentType;
use App\Models\HR\Committee\Committee;
use App\Models\HR\Company\Company;
use App\Models\HR\Education\Education;
use App\Models\HR\Education\EducationType;
use App\Models\HR\Evaluation\EvaluationCompletionType;
use App\Models\HR\Evaluation\EvaluationIndicator;
use App\Models\HR\Exam\ExamType;
use App\Models\HR\Interview\Interview;
use App\Models\HR\Interview\InterviewType;
use App\Models\HR\LeaveOvertime;
use App\Models\HR\Personal\AcademicDegreeType;
use App\Models\HR\Personal\DependentType;
use App\Models\HR\Personal\FeildOfAcademicDegree;
use App\Models\HR\Personal\Gender;
use App\Models\HR\Personal\MilitaryInformation;
use App\Models\HR\Personal\Nationality;
use App\Models\HR\Personal\PersonalType;
use App\Models\HR\Selection\Selection;
use App\Models\HR\Selection\SelectionType;
use App\Models\HR\Shift\Shift;
use App\Models\HR\Shift\ShiftDeliveryModule;
use App\Models\HR\Shift\ShiftWork;
use App\Models\HR\Shift\ShiftWorkGroupType;
use App\Models\HR\User\CooperationType;
use App\Models\HR\User\UserDevice;
use App\Models\LineProduct\Carrier\CarrierGroup;
use App\Models\LineProduct\Carrier\CarrierType;
use App\Models\LineProduct\Degree;
use App\Models\LineProduct\DegreeType;
use App\Models\LineProduct\GoodsKind;
use App\Models\LineProduct\GoodsKindProperty;
use App\Models\LineProduct\GoodsKindPropertyOption;
use App\Models\LineProduct\GoodsType;
use App\Models\LineProduct\Line;
use App\Models\LineProduct\LineProductStation;
use App\Models\LineProduct\LotNumber;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\Fault\MachineFault;
use App\Models\LineProduct\Machine\Fault\MachineFaultSign;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineEventType;
use App\Models\LineProduct\Machine\MachineModuleType;
use App\Models\LineProduct\Machine\MachineProductPropertyOption;
use App\Models\LineProduct\Machine\MachineType;
use App\Models\LineProduct\Machine\MachineTypeCalculationMethod;
use App\Models\LineProduct\Machine\MachineTypeConsumptionType;
use App\Models\LineProduct\Machine\MachineTypeOutputBandPackingType;
use App\Models\LineProduct\Machine\ProductionChannel\MachineTypeProductionChannel;
use App\Models\LineProduct\Machine\ProductionChannel\MachineTypeProductionChannelType;
use App\Models\LineProduct\Machine\RawMaterialRequestAlgorithmType;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\LineProduct\Packing\PackingTypeLabelPrintingType;
use App\Models\LineProduct\Packing\PackingUnitDisplayType;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\ProductWarehouseStorageType;
use App\Models\LineProduct\ProductType;
use App\Models\LineProduct\Reservoir\ReservoirType;
use App\Models\LineProduct\Packing\DischargeType;
use App\Models\LineProduct\Station;
use App\Models\LineProduct\StationOperation;
use App\Models\LineProduct\SupplyType;
use App\Models\Order\OppKind;
use App\Models\Order\OrderType;
use App\Models\Order\TransKind;
use App\Models\Post\FloatingPostType;
use App\Models\Post\OrganizationCategory;
use App\Models\Post\Post;
use App\Models\Post\PostCooperationType;
use App\Models\Post\PostUser;
use App\Models\Production\ProductionAlgorithmType;
use App\Models\Production\ProductionChannelCategory;
use App\Models\Production\ProductionChannelType;
use App\Models\Production\ProductionMethod;
use App\Models\Production\ProductionType;
use App\Models\Production\ProductionWaitingStatus;
use App\Models\SoftwareSystem\SoftwareSystem;
use App\Models\Supplier\Supplier;
use App\Models\Supplier\SupplierType;
use App\Models\Utility\Address\Country;
use App\Models\Utility\Address\Posttex;
use App\Models\Utility\Address\Province;
use App\Models\Utility\Algorithm\Algorithm;
use App\Models\Utility\Algorithm\AlgorithmType;
use App\Models\Utility\Car\CarType;
use App\Models\Utility\Car\DeliveryPointType;
use App\Models\Utility\Car\ShippingMethod;
use App\Models\Utility\Holdding\Factory;
use App\Models\Utility\Menu\Button;
use App\Models\Utility\OfficeAutomation\OfficeAutomationToDoType;
use App\Models\Utility\Printer\PrinterType;
use App\Models\Utility\Printer\PrinterUnitDisplayType;
use App\Models\Utility\SpecialLicense\SpecialLicense;
use App\Models\Utility\SpecialLicense\SpecialLicenseType;
use App\Models\Utility\Unit\UnitOfMeasureType;
use App\Models\Utility\Unit\UnitType;
use App\Models\Warehouse\Warehouse;
use App\Models\Warehouse\WarehouseShelving\WarehouseShelving;
use App\Models\Warehouse\WarehouseShelving\WarehouseShelvingType;
use App\Models\Warehouse\WarehouseStorageType;
use App\Models\Worker;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use function Couchbase\basicEncoderV1;


class Option extends Model
{
    //
    public static $Connection = "mysql";

    /**
     * این تابع برای تست برنامه نویسی استفاده شده است،
     * برای اینکه وقتی نمی دانیم مقدار گزینه چیست، به جای تکست شناسه و نوع گروه را می گذاریم.
     * @param $id
     * @param $caption
     * @param $local_file
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Translation\Translator|string|null
     */
    public static function local($id, $caption, $local_file)
    {
        if (\session("locale")) {

            return __("option/" . $local_file . "." . $id);
        }

        return $caption;
    }

    public function __construct()
    {
//        $this->route_path = "customer_group.tmp.product_creation.";
//        $this->view_path = "customer.tmp.product_creation.";
    }

    public static function get($model, $id = 0, $type = 0, $list = [], $default_value = "", $invalid_list = [], $select_defult_if_count_is_one=false)
    {
        $options = null;
        if ($id == 0 && $type != 2) {
            $options[] = ["id" => "0", "caption" => "لطفا یک مورد را انتخاب کنید", "selected" => 1, "value" => ""];
        }
        if ($type == 2) {
            $options[] = ["id" => "0", "text" => "همه موارد", "selected" => 1, "value" => "0"];
        }
        switch ($model) {

            case "worker":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا یک نفر را انتخاب کنید", "value" => 0];
                $selectedText = "";
                $list_worker = Worker::
                when($type, function ($query) use ($type) {
                    return $query->where("cooperation_type_id", $type);
                })->
                when(count($list) > 0, function ($query) use ($list) {
                    return $query->whereIn("id", $list);
                })->
                get();
                foreach ($list_worker as $item) {
                    $option = ["value" => $item->id, "text" => $item->firstname . " " . $item->lastname];
                    if ($id == $item->id ) {
                        $selectedText = $item->firstname . " " . $item->lastname;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "id" => "worker_id",
                    "label" => "شاغل",
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "worker_in_ids":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا یک نفر را انتخاب کنید", "value" => 0];
                $selectedText = "";
                $list[] = -1;
                $list = Worker::
                whereIn("id", $list)->
                get();
                foreach ($list as $item) {
                    $option = ["value" => $item->id, "text" => $item->firstname . " " . $item->lastname];
                    if ($id == $item->id) {
                        $selectedText = $item->firstname . " " . $item->lastname;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "id" => "worker_id",
                    "label" => "شاغل",
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "worker_cooperation_type":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا یک نفر را انتخاب کنید", "value" => 0];
                $selectedText = "";

                $post_cooperation_type = PostCooperationType::
                where("post_id", $type)->
                pluck("cooperation_type_id")->
                toArray();

                foreach (Worker::whereIn("cooperation_type_id", $post_cooperation_type)->get() as $item) {
                    $option = ["value" => $item->id, "text" => $item->firstname . " " . $item->lastname];
                    if ($id == $item->id) {
                        $selectedText = $item->firstname . " " . $item->lastname;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "id" => "worker_id",
                    "label" => "شاغل",
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "station":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا یک ایستگاه کاری را انتخاب کنید", "value" => 0];
                $selectedText = "";
                $list = Station::where("line_id", $type)->
                where("active_status_id", 1200)->get();
                foreach ($list as $item) {
                    $option = ["value" => $item->id, "text" => $item->code . " - " . $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->code . "-" . $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "station_bom":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا یک ایستگاه کاری را انتخاب کنید", "value" => ""];
                $selectedText = "";
                $list = LineProductStation::where("product_route_id", $type)->pluck("station_id");
                foreach (Station::whereIn("id", $list)->get() as $item) {
                    $option = ["value" => $item->id, "text" => $item->code . " - " . $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->code . "-" . $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "station_operation_type":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا یک نوع عملیات را انتخاب کنید", "value" => 0];
                $selectedText = "";
                $list = Station\Operation\StationOperationType::get();
                foreach ($list as $item) {
                    $option = ["value" => $item->id, "text" => $item->code . " - " . $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->code . "-" . $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "station_operation_category":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا دسته عملیات را انتخاب کنید", "value" => 0];
                $selectedText = "";
                $list = Station\Operation\StationStationOperationCategory::
                where("station_id", $type)->
                get();
                foreach ($list as $item) {
                    $option = ["value" => $item->station_operation_category->id, "text" => $item->station_operation_category->caption];
                    if ($id == $item->station_operation_category->id) {
                        $selectedText = $item->station_operation_category->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "machine":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا یک  دستگاه را انتخاب کنید", "value" => 0];
                $selectedText = "";
                if (count($list) == 0) {
                    $list = Machine::all();
                }
                foreach ($list as $item) {
                    $option = ["value" => $item->id, "text" => $item->code . " - " . $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->code . "-" . $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "machine_module_type":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا یک  گروه ماژول را انتخاب کنید", "value" => 0];
                $selectedText = "";
                $list = MachineModuleType::                get();
                foreach ($list as $item) {
                    $option = ["value" => $item->id, "text" => $item->code . " - " . $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->code . "-" . $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "machine_type":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا یک گروه ماشین را انتخاب کنید", "value" => 0];
                $selectedText = "";
                $list = MachineType::
                where("station_id", $type)->
                where("active_status_id", 1200)->
                get();
                foreach ($list as $item) {
                    $option = ["value" => $item->id, "text" => $item->code . " - " . $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->code . "-" . $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "machine_fault":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا نوع نقص ماشین را انتخاب کنید", "value" => ""];
                $selectedText = "";
                $list = MachineFault::get();

                foreach ($list as $item) {

                    $option = [
                        "value" => $item->id,
                        "text" => $item->code . "-" . $item->caption
                    ];
                    if ($id == $item->id) {
                        $selectedText = $item->code . "-" . $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;

                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "machine_type_consumption_type":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا روش ثبت تراکنش مصرف را انتخاب کنید", "value" => 0];
                $selectedText = "";
                $list = MachineTypeConsumptionType::
                get();
                foreach ($list as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "machine_type_production_channel":
                $options = null;
                $options[] = [
                    "id" => "0",
                    "text" => "لطفا کانال تولید گروه ماشین را انتخاب کنید",
                    "value" => 0
                ];
                $selectedText = "";
                $list = ProductionChannelType::
                orderBy("goods_kind_id")->
                get();


                foreach ($list as $item) {
                    $option = [
                        "value" => $item->id,
                        "text" => $item->caption . " (" . ($item->goods_kind->caption ?? "***") . ")"
                    ];
//                    if ( in_array( $item->id, $list_current_production ) ) {
//                        $selectedText       = $item->caption;
//                        $option["selected"] = 1;
//                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "machine_product_property_option":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا یک  مورد را انتخاب کنید", "value" => 0];
                $selectedText = "";
                $list = MachineProductPropertyOption::
                where("machine_product_property_id", $type)->
                get();
                foreach ($list as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "machine_type_goods_kind":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا یک  گروه ماشین را انتخاب کنید", "value" => 0];
                $selectedText = "";
                $list = MachineType::
                join("stations", "station_id", "stations.id")->
                join("lines", "line_id", "lines.id")->
                where("goods_kind_id", $type)->
                where("machine_types.active_status_id", 1200)->
                select("machine_types.code", "machine_types.id", "machine_types.caption")->
                get();
                foreach ($list as $item) {
                    $option = ["value" => $item->id, "text" => $item->code . " - " . $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->code . "-" . $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "line":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا یک خط را انتخاب کنید", "value" => 0];
                $selectedText = "";

                $list_items = Line::
                when(count($list) != 0, function ($query) use ($list) {
                    return $query->whereIn("id", $list);
                })->
                where("active_status_id", 1200)->
                get();
                foreach ($list_items as $item) {
                    $option = ["value" => $item->id, "text" => $item->code . " - " . $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->code . "-" . $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "line_select_multiple":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا یک خط را انتخاب کنید", "value" => 0];
                $selectedText = "";
                foreach (Line::where("active_status_id", 1200)->get() as $item) {
                    $option = ["value" => $item->id, "text" => $item->code . " - " . $item->caption];
                    if (in_array($item->id, $id)) {
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "line_goods_kind":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا یک خط را انتخاب کنید", "value" => 0];
                $selectedText = "";
                foreach (Line::where("lines.active_status_id", 1200)->where("goods_kind_id", $type)->get() as $item) {
                    $option = ["value" => $item->id, "text" => $item->code . " - " . $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->code . "-" . $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "line_product":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا یک خط را انتخاب کنید", "value" => 0];
                $selectedText = "";
                foreach (LineProductStation::where("product_id", $type)->get() as $item) {
                    $option = [
                        "value" => $item->line->id,
                        "text" => $item->line->code . " - " . $item->line->caption
                    ];
                    if ($id == $item->id) {
                        $selectedText = $item->line->code . "-" . $item->line->caption;
                        $option["selected"] = 1;
                        $id = $item->line->id;
                    }
                    $options[] = $option;
                }

                return [
                    "id" => "line_id",
                    "label" => "خط تولید",
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "tariff_together_product":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا یک کالا را انتخاب کنید", "value" => 0];
                $selectedText = "";
                foreach (Product::whereIn("id", $list)->get() as $item) {
                    $option = [
                        "value" => $item->id,
                        "text" => $item->fullCaption()
                    ];
                    $options[] = $option;
                }

                return [
                    "id" => "line_id",
                    "label" => "تعرفه گذاری کالای همراه",
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "degree":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا یک درجه را انتخاب کنید", "value" => $default_value];
                $selectedText = "";
                $list_degree=(Degree::where("goods_kind_id", $type)->where("active_status_id", 1200)->get());
                foreach($list_degree as $item) {
                    $option = [
                        "value" => $item->id,
                        "text" => $item->code . " - " . $item->caption
                    ];
                    if ($id == $item->id||((count($list_degree)==1)&& $select_defult_if_count_is_one)) 
                        {
                       
                        $selectedText = $item->code . "-" . $item->caption;
                        $option["selected"] = 1;
                        $id = $item->id;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "product_lot_number":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا یک شماره همبافت(شید) را انتخاب کنید", "value" => 0];
                $selectedText = "";
                foreach (LotNumber::where("product_id", $type)->get() as $item) {
                    $option = [
                        "value" => $item->id,
                        "text" => $item->code . " - " . $item->caption
                    ];
                    if ($id == $item->id) {
                        $selectedText = $item->code . "-" . $item->caption;
                        $option["selected"] = 1;
                        $id = $item->id;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "warehouse":
                $options = null;
                $options[] = [
                    "id" => "",
                    "text" => $default_value != "" ? $default_value : "لطفا یک انبار را انتخاب کنید",
                    "value" => ""
                ];
                $selectedText = "";
                $warehouse_list = Warehouse::
                when(count($list) > 0, function ($query) use ($list) {
                    return $query->whereIn("warehouse_type_id", $list);
                })->
                when(is_array($type), function ($query) use ($type) {

                    return $query->whereIn("id", $type);
                })->
                get();
               

                foreach ($warehouse_list as $item) {

                    if ($item->warehouse_type_id > 1 ) {
                        $belonging_to = $item->GetBelongingToObject();
                        if (!$belonging_to || ($belonging_to && $belonging_to->active_status_id == 1210)) {
                            continue;
                        }
                    }


                    $option = ["value" => $item->id, "text" => $item->code . " - " . $item->caption];
                    
                    if ($id == $item->id) {
                        $selectedText = $item->code . "-" . $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "id" => "warehouse_id",
                    "label" => "انبارها ",
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "warehouse_delivery":
                $options = null;
                $options[] = [
                    "id" => "",
                    "text" => $default_value != "" ? $default_value : "لطفا یک انبار را انتخاب کنید",
                    "value" => ""
                ];
                $selectedText = "";

                $list = json_decode($list, true);
                $goods_kind_id = $list[0];
                $bom_goods_kind_id = $list[1];
                $list_default = GoodsKind\GoodsKindSettingValue::getArrayValue($goods_kind_id, "default_bom_warehouse_ids_" . $bom_goods_kind_id);
                $list_default[] = -1;
                $warehouse_list = Warehouse::
                whereIn("id", $list_default)->
                get();

                foreach ($warehouse_list as $item) {

                    $option = ["value" => $item->id, "text" => $item->code . " - " . $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->code . "-" . $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "warehouse_select_multiple":
                $options = null;
                $options[] = [
                    "id" => "",
                    "text" => $default_value != "" ? $default_value : "لطفا انبار های مجاز را انتخاب کنید",
                    "value" => ""
                ];
                $selectedText = "";
                $warehouse_list = Warehouse::
                when(count($list) > 0, function ($query) use ($list) {
                    return $query->whereIn("warehouse_type_id", $list);
                })->
                get();
                // در تنظیمات پیش فرض رسته کالایی باید انبارک ماشین را هم (انبارک شناور) بتوان انتخاب کرد.
                $option = ["value" => "-100", "text" => "* - انبارک ماشین "];
                if (in_array(-100, $id)) {
                    $option["selected"] = 1;
                }
                $options[] = $option;
                foreach ($warehouse_list as $item) {

                    if ($item->warehouse_type_id > 1) {
                        $belonging_to = $item->GetBelongingToObject();
                        if (!$belonging_to || ($belonging_to && $belonging_to->active_status_id == 1210)) {
                            continue;
                        }
                    }


                    $option = ["value" => $item->id, "text" => $item->code . " - " . $item->caption];
                    if (in_array($item->id, $id)) {
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "warehouse_consume":
                $options = null;
                $options[] = ["id" => "", "text" => "لطفا یک انبار را انتخاب کنید", "value" => ""];


                $list = json_decode($list, true);
                $goods_kind_id = $list[0];
                $bom_goods_kind_id = $list[1];
                $list_default = GoodsKind\GoodsKindSettingValue::getArrayValue($goods_kind_id, $type . $bom_goods_kind_id);
                $list_default[] = -1;
                $warehouse_list = Warehouse::
                whereIn("id", $list_default)->
                whereNotIn("warehouse_type_id", [1, 2])->
                get();
                $selectedText = "";
                foreach ($warehouse_list as $item) {
                    $option = ["value" => $item->id, "text" => $item->code . " - " . $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->code . "-" . $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }
                // -100 یعنی انبارک ماشین
                if (in_array(-100, $list_default)) {
                    $option_machine = ["text" => "*" . " - " . "انبارک ماشین", "value" => -100]; // انبارک های ماشین
                    if ($id == -1) {
                        $selectedText = "انبارک ماشین";
                        $option_machine["selected"] = 1;
                    }
                    $options[] = $option_machine;
                }

                return [
                    "id" => "warehouse_id",
                    "label" => "انبارها ",
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "warehouse_storage_types":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا نوع انبارش کالا را انتخاب نمایید.", "value" => ""];
                $selectedText = "";
                foreach (WarehouseStorageType::get() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "degree_warehouse":
                $options = null;
                $options[] = ["id" => "", "text" => "لطفا یک انبار را انتخاب کنید", "value" => ""];
                $selectedText = "";
                $list_option = Warehouse::join("degrees", "warehouses.id", "warehouse_id")->
                select("warehouses.*")->
                where("degrees.id", $type ?? 0)->
                get();
                foreach ($list_option as $item) {
                    $option = ["value" => $item->id, "text" => $item->code . " - " . $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->code . "-" . $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "id" => "warehouse_id",
                    "label" => "انبارها ",
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "post_warehouse":
                $options = null;
                $options[] = ["id" => "", "text" => "لطفا یک انبار را انتخاب کنید", "value" => "0"];
                $selectedText = "";
                foreach (Warehouse::whereIn("id", $list)->get() as $item) {
                    $option = ["value" => $item->id, "text" => $item->code . " - " . $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->code . "-" . $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "id" => "warehouse_id",
                    "label" => "انبارها ",
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "currency":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا واحد پول را انتخاب کنید", "value" => 0];
                $selectedText = "";
                foreach (Currency::all() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;


                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [

                    "id" => "currency_id",
                    "label" => "واحد پول",
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "debt_or_supply":
                $options = null;
                $selectedText = "";
                $list = [
                    ["id" => 1, "caption" => "خرید"],
                    ["id" => 2, "caption" => "قرض"]
                ];
                foreach ($list as $item) {
                    $option = ["value" => $item["id"], "text" => $item["caption"]];
                    if ($id == $item["id"]) {
                        $selectedText = $item["caption"];
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "supply_type":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا نوع تامین را انتخاب کنید", "value" => 0];
                $selectedText = "";
                foreach (SupplyType::all() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->code . "-" . $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "leave_overtime_type":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا نوع مرخصی را انتخاب کنید", "value" => 0];
                $selectedText = "";
                foreach (LeaveOvertime\LeaveOvertimeType::where("leave_overtime_group_id", $type)->get() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->code . "-" . $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "payment_method":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا  روش پرداخت را انتخاب کنید", "value" => ""];
                $selectedText = "";
                foreach (PaymentMethod::all() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;


                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [

                    "id" => "payment_method",
                    "label" => "روش پرداخت ",
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "reject_product_reason_type":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا  علت مرجوعی را مشخص کنید", "value" => ""];
                $selectedText = "";
                foreach (Product\RejectProduct\RejectProductReasonType::all() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;


                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "tariff":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا تعرفه را انتخاب کنید", "value" => 0];
                $selectedText = "";
                foreach (Tariff::all() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [

                    "id" => "tariff_id",
                    "label" => "تعرفه ",
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "discharge_type":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا نوع تخلیه (یا حرکت مواد) را انتخاب کنید", "value" => 0];
                $selectedText = "";
                $list_item = DischargeType::when(count($list) > 0, function ($query) use ($list) {
                    return $query->whereIn("id", $list);
                })->get();
                foreach ($list_item as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "label_packing_type_option":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا قالب را انتخاب کنید", "value" => 0];
                $selectedText = "";
                $list = PackingTypeLabelPrintingType::
                when($type, function ($query) use ($type) {
                    return $query->where("is_for_packing_forms", $type);
                })->
                orderBy("priority_number")->
                get();
                foreach ($list as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "label_packing_type_list_option":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا قالب را انتخاب کنید", "value" => 0];
                $selectedText = "";
                $list_paking = PackingTypeLabelPrintingType::

                whereIn("id", $type)->

                orderBy("priority_number")->
                get();
                foreach ($list_paking as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if (in_array($item->id, $list)) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "printer_unit_display_type":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا نوع نمایش را انتخاب کنید", "value" => 0];
                $selectedText = "";
                foreach (PrinterUnitDisplayType::all() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "status":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا وضعیت  را انتخاب کنید", "value" => 0];
                $selectedText = "";
                foreach (Status::where("status_type_id", $type)->get() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "status_in_ids":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا وضعیت  را انتخاب کنید", "value" => 0];
                $selectedText = "";
                foreach (Status::whereIn("id", $list)->get() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "status_exit_form":
                // وضعیت های تاییده برگ خروج از انبار
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا وضعیت  را انتخاب کنید", "value" => 0];
                $selectedText = "";
                if (count($list) == 0) {
                    $list = [
                        500000500,
                        500000515,
                        500000520,
                        500000525,
                        500000530,
                        500000535
                    ];
                }
                foreach (
                    Status::whereIn("id", $list)->get() as $item
                ) {
                    $caption = Str::of($item->caption)->trim("در")->trim()->trim("انتظار");
                    $option = ["value" => $item->id, "text" => $caption];
                    if ($id == $item->id) {
                        $selectedText = $caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "status_permission":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا وضعیت  را انتخاب کنید", "value" => 0];
                $selectedText = "";
                foreach (Status::where("status_type_id", $type)->whereIn("id", $list)->get() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "active_status":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا وضعیت  را انتخاب کنید", "value" => 0];
                $selectedText = "";
                foreach (Status::where("status_type_id", 1100)->get() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "waiting_status":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا وضعیت  را انتخاب کنید", "value" => 0];
                $selectedText = "";
                $table = ProductionWaitingStatus::join("status", "status.id", "=", "status_id")->where([
                    "goods_kind_id" => $type
                ]);
                foreach ($table->get() as $item) {
                    $option = ["value" => $item->status_id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "machine_status":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا وضعیت  را انتخاب کنید", "value" => 0];
                $selectedText = "";
                $table = Status::join("machine_status", "status.id", "=", "status_id")->where([
                    "station_id" => $type
                ]);
                foreach ($table->get() as $item) {
                    $option = ["value" => $item->status_id, "text" => $item->getCaption()];
                    if ($id == $item->id) {
                        $selectedText = $item->getCaption();
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "machine_status_for_filter":
                $options = null;
                $options[] = ["id" => "0", "text" => "همه موارد", "value" => 0];
                $selectedText = "";

                $list = MachineType::join("machine_status", "machine_status.machine_module_type_id", "machine_types.machine_module_type_id")->
                join("status", "status.id", "production_status_id")->
                where("machine_types.active_status_id", 1200)->
                select("status.*")->
                distinct("status.id")->
                get();


                foreach ($list as $item) {


                    $caption = $item->caption;

                    $option = [
                        "value" => $item->id,
                        "text" => $caption
                    ];
                    if ($id == $item->id) {
                        $selectedText = $caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "marital_status":
                $options = null;
                $options[] = [
                    "id" => "0",
                    "text" => Option::local("0", "لطفا وضعیت تاهل خود را انتخاب کنید", "marital_status"),
                    "value" => 0
                ];
                $selectedText = "";
                foreach (Status::where('status_type_id', 650)->get() as $item) {
                    $option = ["value" => $item->id, "text" => Option::local($item->id, $item->caption, "marital_status")];
                    if ($id == $item->id) {
                        $selectedText = Option::local($item->id, $item->caption, "marital_status");
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "production_waiting_status":
                $options = null;
                $options[] = ["id" => "0", "text" => "همه موارد", "value" => 0];
                $selectedText = "";
                $master_status = Status::find(500);
                if (is_array($type)) {
                    $status_list_query = Status::whereIn("status_type_id", $type);
                } else {
                    $status_list_query = Status::where("status_type_id", $type);
                }
               if($list!=null) {
                   $list[] = -1;
                   $status_list_query = $status_list_query->whereIn("id", $list);
               }
                $status_type_caption_show = $status_list_query->distinct("status_type_id")->count() > 1;

                foreach ($status_list_query->get() as $item) {

                    $status_type_caption = $status_type_caption_show ? Str::replace("کارت تولید", "-", $item->status_type->caption) : "";
                    $status_type_caption = trim($status_type_caption);

                    $caption = $master_status->caption . " (" . $item->caption . $status_type_caption . ")";
                    if (in_array($item->id, [7001004, 7201004, 7301004])) {
                        $caption = $item->caption . $status_type_caption;
                    }
                    $option = [
                        "value" => $item->id,
                        "text" => $caption
                    ];
                    if ($id == $item->id) {
                        $selectedText = $caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "production_form_status":

                $options = null;
                $options[] = ["id" => "0", "text" => "همه موارد", "value" => 0];
                $selectedText = "";

                if (is_array($type)) {
                    $status_list_query = Status::whereIn("status_type_id", $type);
                } else {
                    $status_list_query = Status::where("status_type_i", $type);
                }
                $list[] = -1;
                $status_list_query = $status_list_query->whereIn("id", $list);

                $status_type_caption_show = $status_list_query->distinct("status_type_id")->count() > 1;

                foreach ($status_list_query->get() as $item) {

                    $status_type_caption = $status_type_caption_show ? Str::replace("کارت تولید", "-", $item->status_type->caption) : "";
                    $status_type_caption = trim($status_type_caption);

                    $option = [
                        "value" => $item->id,
                        "text" => $item->caption . " (" . $status_type_caption . ")"
                    ];
                    if ($id == $item->id) {
                        $selectedText = $item->caption . " (" . $status_type_caption . ")";
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "contractor_allocation_waiting_status":
                $options = null;
                $options[] = ["id" => "0", "text" => "همه موارد", "value" => 0];
                $selectedText = "";
                $master_status = Status::find(500);
                foreach (Status::where("status_type_id", $type)->get() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "machine_event_type_in_ids":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا نوع رویداد  را انتخاب کنید", "value" => 0];
                $selectedText = "";
                foreach (MachineEventType::whereIn("id", $list)->get() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "event_type_in_ids":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا نوع رویداد  را انتخاب کنید", "value" => 0];
                $selectedText = "";
                foreach (Event::where("status_type_id", $type)->get() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "contractor_supply_types":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا یک مورد را انتخاب نمایید.", "value" => ""];
                $selectedText = "";
                foreach (ContractorSupplyType::where("status_id", 1200)->get() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "software_system":
                $options = null;
                $options[] = ["id" => "0", "text" => "فاقد نرم افزار جامع", "value" => "0"];
                $selectedText = "";
                foreach (SoftwareSystem::get() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "production_channel_type":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا یک مورد را انتخاب نمایید.", "value" => ""];
                $selectedText = "";

                $list_production = ProductionChannelType::
                when(count($list) > 0, function ($query) use ($list) {
                    return $query->whereIn("id", $list);
                })->
                orderBy("production_channel_category_id")->get();


                foreach ($list_production as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption . "(" . ($item->production_channel_category->caption ?? "") . ")"];
                    if ($id == $item->id) {
                        $selectedText = $item->caption . "(" . ($item->production_channel_category->caption) . ")";
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "production_channel_category":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا یک مورد را انتخاب نمایید.", "value" => ""];
                $selectedText = "";
                foreach (ProductionChannelCategory::get() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "station_production_channel_type":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا یک مورد را انتخاب نمایید.", "value" => ""];
                $selectedText = "";
                $list = ProductionChannelType::
                orderBy("production_channel_category_id")->get();
                foreach ($list as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "machine_type_production_channel_type":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا یک مورد را انتخاب نمایید.", "value" => ""];
                $selectedText = "";
                $list = MachineTypeProductionChannelType::where("machine_type_id", $type)->get();
                foreach ($list as $item) {
                    $option = [
                        "value" => $item->production_channel_type->id,
                        "text" => $item->production_channel_type->caption
                    ];
                    if ($id == $item->production_channel_type->id) {
                        $selectedText = $item->production_channel_type->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "contractor_production_channel_type":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا یک مورد را انتخاب نمایید.", "value" => ""];
                $selectedText = "";
                $list = ContractorProductionChannelType::where("contractor_id", $type)->get();
                if ($type == -1) {
                    $list = ContractorProductionChannelType::get();
                }
                foreach ($list as $item) {
                    $option = [
                        "value" => $item->production_channel_type->id,
                        "text" => $item->production_channel_type->caption
                    ];
                    if ($id == $item->production_channel_type->id) {
                        $selectedText = $item->production_channel_type->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "accompanying_product_select":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا یک کالا را انتخاب نمایید.", "value" => ""];
                $selectedText = "";
                // در انتظار قیمت گذاری
                $list = Product\ProductCreation\ProductCreationProcess::
                whereIn("status_id", [5231029, 5231028])->
                where("product_id", "!=", $type)->
                get();
                foreach ($list as $item) {
                    $option = [
                        "value" => $item->product_id,
                        "text" => $item->product->caption . " (" . $item->code . ")"
                    ];
                    if ($id == $item->product_id) {
                        $selectedText = $item->product->caption . " (" . $item->code . ")";
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "order_waiting_status":
                $options = null;
                $options[] = ["id" => "0", "text" => "همه موارد", "value" => 0];
                $selectedText = "";

                foreach (
                    Status::whereIn("id", $type)->whereIn("status_type_id", [
                        350,
                        351,
                        4600
                    ])->get() as $item
                ) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "factory":
                $options = null;
                $options[] = ["id" => "0", "text" => "همه کارخانه ها", "value" => 0];
                $selectedText = "";
                foreach (Factory::all() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [

                    "id" => "factory_id",
                    "label" => "کارخانه ",
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "factory_tariff":
                1 / 0; // حذف شده
                break;

            case "product_type":
                $options = null;
                $options[] = ["id" => "0", "text" => "همه گروه های کالایی ", "value" => 0];
                $selectedText = "";
                foreach (ProductType::where("goods_kind_id", $type)->get() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [

                    "id" => "product_group_id",
                    "label" => "گروه کالایی ",
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "goods_type":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا نوع کالا را مشخص کنید. ", "value" => 0];
                $selectedText = "";
                $goods_kind_id = $type;
                if ($default_value != "") {

                    $list_default = GoodsKind\GoodsKindSettingValue::getArrayValue($goods_kind_id, $default_value);
                    $list_default[] = -1;
                    $list = GoodsType::
                    whereIn("id", $list_default)->
                    get();
                } else {
                    $list = GoodsType::get();
                }
                foreach ($list as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "goods_type_select_multiple":
                $options = null;
                $options[] = ["id" => "0", "text" => "نوع کالا ", "value" => 0];
                $selectedText = "";
                foreach (GoodsType::get() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if (in_array($item->id, $id)) {
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "goods_kind":
                $options = null;
                $options[] = [
                    "id" => "0",
                    "text" => Option::local(0, "لطفا  رسته کالایی را انتخاب کنید ", "goods_kind"),
                    "value" => ""
                ];
                $selectedText = "";
                $list_option = GoodsKind::
                where("active_status_id", 1200)->
                when(count($list) > 0, function ($query) use ($list) {
                    return $query->whereIn("id", $list);
                })->
                get();
                foreach ($list_option as $item) {
                    $option = [
                        "value" => $item->id,
                        "text" => Option::local($item->id, $item->caption, "goods_kind")
                    ];
                    if ($id == $item->id) {
                        $selectedText = Option::local($item->id, $item->caption, "goods_kind");
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];

                break;
            case "goods_kind_select_multiple":
                $options = null;
                $options[] = [
                    "id" => "0",
                    "text" => Option::local(0, "لطفا  رسته کالا های مجاز را انتخاب کنید ", "goods_kind"),
                    "value" => ""
                ];
                $selectedText = "";
                $list_option = GoodsKind::where("active_status_id", 1200)->get();
                foreach ($list_option as $item) {
                    $option = [
                        "value" => $item->id,
                        "text" => Option::local($item->id, $item->caption, "goods_kind")
                    ];
                    if (in_array($item->id, $id)) {
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];

                break;

            case "main_property_by_goods_kind":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا یک مورد را انتخاب کنید", "value" => 0];
                $selectedText = "";
                $list = GoodsKindProperty::
                where(
                    [
                        "goods_kind_id" => $type,
                        "status_id" => 1200
                    ])->get();
                foreach ($list as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "get_property_by_goods_kind":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا یک مورد را انتخاب کنید", "value" => 0];
                $selectedText = "";
                $list = GoodsKindProperty::on(Option::$Connection)->
                where(
                    [
                        "goods_kind_id" => $type,
                        "status_id" => 1200
                    ])->get();
                foreach ($list as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "goods_kind_property_option":
                $options = null;
                // $options[]    = [ "id" => "0", "text" => "لطفا یک مورد را انتخاب کنید", "value" => 0 ];
                $selectedText = "";
                $list = GoodsKindPropertyOption::where(
                    [
                        "goods_kind_property_id" => $type,
                        "enabled" => 1
                    ])->get();
                foreach ($list as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "goods_kind_property_option_type5":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا یک مورد را انتخاب کنید", "value" => ""];
                $selectedText = "";
                $list = [
                    ["id" => 1, "caption" => "دارد/صحیح/True"],
                    ["id" => -1, "caption" => "ندارد/اشتباه/False"]
                ];
                foreach ($list as $item) {
                    $option = ["value" => $item["id"], "text" => $item["caption"]];
                    if ($id == $item["id"]) {
                        $selectedText = $item["caption"];
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "goods_kind_dependent_property":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا یک مورد را انتخاب کنید", "value" => 0];
                $selectedText = "";
                $list_item = GoodsKindProperty::
                where("id", "!=", $type)->
                where("status_id", 1200)-> // فعال
                where("goods_kind_id", $list)->
                whereIn("field_type_id", [1, 3, 5])->
                get();
                foreach ($list_item as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "goods_kind_classification_option":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا یک مورد را انتخاب کنید", "value" => 0];
                $selectedText = "";
                $list_item = GoodsKind\GoodsKindClassificationOption::
                where("goods_kind_classification_id", $type)->
                get();
                foreach ($list_item as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "goods_kind_classification_group_option":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا یک مورد را انتخاب کنید", "value" => 0];
                $selectedText = "";
                $list_item = GoodsKind\GoodsKindClassification::
                where("goods_kind_id", $type)->
                get();
                foreach ($list_item as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "compare_number":
                $options = null;
                $selectedText = "";
                $list_item = [
                    ["id" => "1", "text" => "=", "value" => "="],
                    ["id" => "2", "text" => "!=", "value" => "!="],
                    ["id" => "3", "text" => ">", "value" => ">"],
                    ["id" => "4", "text" => "<", "value" => "<"],
                    ["id" => "5", "text" => "=>", "value" => "=>"],
                    ["id" => "6", "text" => "<=", "value" => "<="],
                ];
                foreach ($list_item as $item) {
                    $option = ["value" => $item["value"], "text" => $item["value"]];
                    if ($id == $item["value"]) {
                        $selectedText = $item["text"];
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "product_from_list":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا یک کالا را انتخاب کنید ", "value" => $default_value];
                $selectedText = "";
                foreach ($list as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption . " - (" . ($item->code) . ")"];
                    if ($id == $item->id) {
                        $selectedText = $item->caption . " - (" . ($item->code) . ")";
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "tariff_product_group_by_packing_type":
                $options = null;
                $selectedText = "";
                $tariff_product_list_query = ProductTariff::where([
                    "tariff_id" => $type["tariff_id"],
                    "product_id" => $type["product_id"],
                ]);
                foreach (ProductTariff::GroupItemList() as $item) {
                    $tariff_product_list_query = $tariff_product_list_query->where($item, $type[$item]);
                }
                $tariff_product_list_query = $tariff_product_list_query->groupBy("packing_type_id");

                $tariff_product_list = $tariff_product_list_query->get();
                foreach ($tariff_product_list as $item) {
                    $option = ["value" => $item->packing_type_id, "text" => $item->packing_type->caption];
                    if (in_array($item->id, $type["selected_list"]) || $type["selected_list"] == []) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "product_type_tariff":
                $options = null;
                $options[] = ["id" => "0", "text" => "همه گروه های کالایی ", "value" => 0];
                $selectedText = "";
                $productTypeIds = ProductTariff::join("products", "product_id", "products.id")->
                where("tariff_id", $type)->
                where("active_status_id", 1200)->
                where("possibility_of_sale", 1)->
                groupBy("product_type_id")->pluck("product_type_id");
                foreach (ProductType::whereIn("id", $productTypeIds)->get() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "goods_kind_tariff":
                $options = null;
                $options[] = ["id" => "0", "text" => "همه رسته های کالایی ", "value" => 0];
                $selectedText = "";
                $goodsKindIds = ProductTariff::join("products", "product_id", "products.id")->
                where("tariff_id", $type)->
                where("active_status_id", 1200)->
                where("possibility_of_sale", 1)->
                groupBy("goods_kind_id")->pluck("goods_kind_id");
                foreach (GoodsKind::whereIn("id", $goodsKindIds)->get() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "product_goods_1":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا یک کالا را انتخاب کنید ", "value" => ""];
                $selectedText = "";
                foreach (Product::where("goods_type_id", 1)->get() as $item) {
                    $option = ["value" => $item->id, "text" => $item->code . " - " . $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->code . " - " . $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [

                    "id" => "product_id",
                    "label" => "نوع کالا  ",
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "product_by_goods_kind":
                $goods_kind = GoodsKind::find($type);
                $options = null;
                $options[] = [
                    "id" => "0",
                    "text" => "لطفا یک " . $goods_kind->caption . " را انتخاب کنید ",
                    "value" => ""
                ];
                $selectedText = "";
                foreach (Product::where("goods_kind_id", $type)->get() as $item) {
                    $option = ["value" => $item->id, "text" => $item->code . " - " . $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->code . " - " . $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [

                    "id" => "product_id",
                    "label" => "نوع کالا  ",
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "product_all":
                $options = null;
                $options[] = [
                    "id" => "0",
                    "text" => $default_value ?? "لطفا یک کالا را انتخاب کنید ",
                    "value" => ""
                ];
                $selectedText = "";
                $goods_kind_ids = $type;
                $list_product = Product::
                where("active_status_id", 1200)->
                when($type != 0, function ($query) use ($type) {
                    return $query->whereIn("goods_kind_id", $type);
                })->
                when(count($list) == 0, function ($query) {
                    return $query->where("product_service_type_id", 1);
                })->
                get();
                foreach ($list_product as $item) {
                    $option = ["value" => $item->id, "text" => $item->code . " - " . $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->code . " - " . $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [

                    "id" => "product_id",
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "product_list":
                $options = null;
                $options[] = [
                    "id" => "0",
                    "text" => $default_value ?? "لطفا یک کالا را انتخاب کنید ",
                    "value" => ""
                ];
                $selectedText = "";
                $product_list = Product::whereIn("id", $list)->get();
                foreach ($product_list as $item) {
                    $option = ["value" => $item->id, "text" => $item->code . " - " . $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->code . " - " . $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [

                    "id" => "product_id",
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "product_consumed_waste":
                $options = null;
                $options[] = [
                    "id" => "0",
                    "text" => $default_value == "" ? "لطفا یک ضایعات را انتخاب کنید " : $default_value,
                    "value" => ""
                ];
                $selectedText = "";

                $list = Product::
                join("product_waste", "product_waste.waste_id", "products.id")->
                where("product_id", $type)->
                select("products.*")->get();

                foreach ($list as $item) {
                    $option = ["value" => $item->id, "text" => $item->code . " - " . $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->code . " - " . $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [

                    "id" => "product_id",
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "consumed_product":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا یک کالا را انتخاب کنید ", "value" => ""];
                $selectedText = "";
                foreach (Product\ConsumedProduct\ConsumedProduct::where("product_id", $type)->get() as $item) {
                    $option = [
                        "value" => $item->material_id,
                        "text" => $item->material->code . " - " . $item->material->caption
                    ];
                    if ($id == $item->material_id) {
                        $selectedText = $item->material->code . " - " . $item->material->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                    $last_item = $item;
                }

                $product = Product::find($type);


                return [

                    "id" => "product_id",
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];

                break;

            case "warehouse_storage_type_product":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا یک کالا را انتخاب کنید ", "value" => ""];
                $selectedText = "";
                foreach (ProductWarehouseStorageType::where("warehouse_storage_type_id", $type)->with("product")->get() as $item) {
                    $option = [
                        "value" => $item->product_id,
                        "text" => $item->product->code . " - " . $item->product->caption
                    ];
                    if ($id == $item->product_id) {
                        $selectedText = $item->product->code . " - " . $item->product->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                    $last_item = $item;
                }

                $product = Product::find($type);


                return [

                    "id" => "product_id",
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];

                break;

            case "modification_consumed_status":
                $options = null;
                $options[] = ["id" => "0", "text" => "وضعیت مصرف بسته بندی ", "value" => ""];
                $selectedText = "";

                $option = [
                    "value" => 6021101,
                    "text" => "مصرف نشده (کاملا سالم)"
                ];

                $options[] = $option;
                $option = [
                    "value" => 6021102,
                    "text" => "بخشی مصرف شده",
                    "selected" => 1
                ];

                $options[] = $option;


                return [

                    "id" => "product_id",
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "dependent_on_material":
                $options = null;
                $bom = Product\BOM\BOM::find($type);
                $options[] = ["id" => "0", "text" => "وابسته به کالای اصلی " . $bom->product->fullCaption(), "value" => $bom->product_id];
                if ($id == 0) {
                    $options[0]["selected"] = 1;
                }
                $selectedText = "";
                $items = $bom->items()->groupBy("material_id")->get();
                foreach ($items as $item) {
                    $option = [
                        "value" => $item->material_id,
                        "text" => "وابسته به " . $item->material->code . " - " . $item->material->caption
                    ];
                    if ($id == $item->material_id) {
                        $selectedText = $item->material->code . " - " . $item->material->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [

                    "id" => "product_id",
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "bill_of_material_entering_type":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا روش  ورود مواد را مشخص نمایید ", "value" => ""];
                $selectedText = "";
                foreach (Product\BOM\BOMItemEnteringType::get() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [

                    "id" => "product_id",
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "bill_of_material_dependency_type":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا نوع وابستگی مواد را مشخص نمایید ", "value" => ""];
                $selectedText = "";
                foreach (Product\BOM\BOMDependencyType::get() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [

                    "id" => "product_id",
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "product_supply":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا یک کالا را انتخاب کنید ", "value" => ""];
                $selectedText = "";
                foreach (Product::where("product_service_type_id", 1)->where("supply_type_id", $type)->get() as $item) {
                    $option = ["value" => $item->id, "text" => $item->code . " - " . $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->code . " - " . $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [

                    "id" => "product_id",
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;


            case "service_all":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا یک سرویس را انتخاب کنید ", "value" => ""];
                $selectedText = "";
                foreach (Product::where("product_service_type_id", 2)->get() as $item) {
                    $option = ["value" => $item->id, "text" => $item->code . " - " . $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->code . " - " . $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [

                    "id" => "product_id",
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "channel_type":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا یک کانال مشتری انتخاب کنید", "value" => ""];
                $selectedText = "";
                foreach (ChannelType::all() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [

                    "id" => "channel_type_id",
                    "label" => "گروه مشتریان ",
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "customer":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا یک  مشتری انتخاب کنید", "value" => ""];
                $selectedText = "";
                foreach (Customer::whereNull("parent_id")->where('status_id' , 1200)->get() as $item) {
                    $option = ["value" => $item->id, "text" => $item->code . " - " . $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->code . " - " . $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [

                    "id" => "customer_id",
                    "label" => " مشتریان ",
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "product_active":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا یک محصول را انتخاب کنید", "value" => ""];
                $selectedText = "";

                //
                $products = Product::where("active_status_id", 1200)->
                when(isset($list["supply_type_ids"]), function ($query) use ($list) {
                    return $query->whereIn("supply_type_id", $list["supply_type_ids"]);
                })->
                get();
                foreach ($products as $item) {
                    $option = [
                        "value" => $item->id,
                        "text" => $item->code . " - " . $item->caption . " (" . ($item->unit->bach_caption ?? "") . ")"
                    ];
                    if ($id == $item->id) {
                        $selectedText = $item->code . " - " . $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [

                    "id" => "product_id",
                    "label" => " محصولات ",
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;


            case "product_record_entry_into_warehouse_manually":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا یک محصول را انتخاب کنید", "value" => ""];
                $selectedText = "";
                //
                $list = Product::join("goods_kinds", "goods_kind_id", "goods_kinds.id")->
                where("record_entry_into_warehouse_manually", 1)->
                where("products.active_status_id", 1200)->
                select("products.*")->
                get();
                foreach ($list as $item) {
                    $option = [
                        "value" => $item->id,
                        "text" => $item->code . " - " . $item->caption . " (" . ($item->unit->bach_caption ?? "") . ")"
                    ];
                    if ($id == $item->id) {
                        $selectedText = $item->code . " - " . $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [

                    "id" => "product_id",
                    "label" => " محصولات ",
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;


            case "product_service_active":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا یک خدمت را انتخاب کنید", "value" => ""];
                $selectedText = "";

                //
                $products = Product::where("active_status_id", 1200)->
                where("product_service_type_id", 2)->
                get();
                foreach ($products as $item) {
                    $option = [
                        "value" => $item->id,
                        "text" => $item->code . " - " . $item->caption . " (" . ($item->unit->bach_caption ?? "") . ")"
                    ];
                    if ($id == $item->id) {
                        $selectedText = $item->code . " - " . $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [

                    "id" => "product_id",
                    "label" => " محصولات ",
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "opp_kind":
                $options = null;
                $options[] = ["id" => "", "text" => "لطفا یک طرف انتخاب کنید", "value" => ""];
                $selectedText = "";
                foreach (OppKind::all() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "trans_kind":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا یک نوع تراکنش انتخاب کنید", "value" => ""];
                $selectedText = "";
                // where("entry_type_id",$type)->
                foreach (TransKind::where("entry_type_id", $type)->orderBy("nosa_code")->get() as $item) {
                    $option = [
                        "value" => $item->id == 0 ? 100 : $item->id,
                        "text" => $item->entry_type->caption . " => " . $item->caption
                    ];
                    if ($id == $item->id) {
                        $selectedText = $item->entry_type->caption . " => " . $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "unit":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا یک واحد انتخاب کنید", "value" => ""];
                $selectedText = "";
                $goods_kind_id = $type;

                if ($default_value != "") {

                    $list_default = GoodsKind\GoodsKindSettingValue::getArrayValue($goods_kind_id, $default_value);
                    $list_default[] = -1;
                    $list = Unit::
                    whereIn("id", $list_default)->
                    get();
                } else {
                    $list = Unit::all();
                }

                foreach ($list as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }

                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "unit_type":
                $options = null;
                $selectedText = "";
                $unit_types = UnitType::
                where("active_status_id", 1200)->
                when(count($list) > 0, function ($query) use ($list) {
                    return $query->whereIn("id", $list);
                })->
                get();


                foreach ($unit_types as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }

                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "unit_of_measure_type":
                $options = null;
                $selectedText = "";
                $valid_ids = GoodsKind\GoodsKindSettingValue::getArrayValue($type, $list);
                $unit_of_measure_types = UnitOfMeasureType::whereIn("id", $valid_ids)->
                get();

                foreach ($unit_of_measure_types as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }

                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "unit_of_measure_type_multiple":
                $options = null;
                $selectedText = "";
                $unit_of_measure_types = UnitOfMeasureType::
                when(count($list) > 0, function ($query) use ($list) {
                    return $query->whereIn("id", $list);
                })->
                get();

                foreach ($unit_of_measure_types as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if (in_array($item->id, $id)) {
                        $option["selected"] = 1;
                    }

                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "unit_select_multiple":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا واحدهای مجاز را انتخاب کنید", "value" => ""];
                $selectedText = "";
                foreach (Unit::all() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if (in_array($item->id, $id)) {
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "delivery_unit_bom":
                $product = Product::find($type);
                if (!$product) {
                    return [
                        "items" => $options,
                        "value" => 0,
                        "text" => "",
                    ];
                }
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا یک واحد انتخاب کنید", "value" => ""];
                $selectedText = "";
                $list = [];
                $list[] = ["caption" => $product->unit->caption, "value" => $product->unit->id];
                if ($product->sub_unit) {
                    $list[] = ["caption" => $product->sub_unit->caption, "value" => $product->sub_unit->id];
                }
                if ($product->sub_unit2) {
                    $list[] = ["caption" => $product->sub_unit2->caption, "value" => $product->sub_unit2->id];
                }
                foreach ($list as $item) {
                    $option = ["value" => $item["value"], "text" => $item["caption"]];
                    if ($id == $item["value"]) {
                        $selectedText = $item["caption"];
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "result_owner_ic":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا یک مالک را انتخاب کنید", "value" => "0"];
                $selectedText = "";
                $result_owner_ic = SpecialLicense::ShowOwnerInIc(env("APP_NAME"), env("IC_APIKEY"));
                foreach ($result_owner_ic["application"] as $item) {
                    $option = ["value" => $item['id'], "text" => $item["name"]];
                    if ($id == $item['id']) {
                        $selectedText = $item["name"];
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "carrier_group":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا یک گروه حامل را انتخاب کنید", "value" => "0"];
                $selectedText = "";
                foreach (CarrierGroup::all() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "province":
                $options = null;
                $options[] = [
                    "id" => "0",
                    "text" => Option::local(0, "لطفا یک استان انتخاب کنید", "province"),
                    "value" => ""
                ];
                $selectedText = "";
                foreach (Province::all() as $item) {
                    $option = [
                        "value" => $item->id,
                        "text" => Option::local($item->id, $item->caption, "province")
                    ];
                    if ($id == $item->id) {
                        $selectedText = Option::local($item->id, $item->caption, "province");
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;


            case "posttex_province":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا یک استان انتخاب کنید", "value" => ""];
                $selectedText = "";
                foreach (Posttex\Province::get_all() as $item) {
                    $option = ["value" => $item->stateId, "text" => $item->stateName];
                    if ($id == $item->stateId) {
                        $selectedText = $item->stateName;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "posttex_city":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا یک استان انتخاب کنید", "value" => ""];
                $selectedText = "";
                foreach (Posttex\City::get_all($type) as $item) {
                    $option = ["value" => $item->townId, "text" => $item->townName];
                    if ($id == $item->townId) {
                        $selectedText = $item->townName;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "agent_type":
                $options = null;
                $options[] = [
                    "id" => "0",
                    "text" => Option::local(0, "لطفا نوع ارتباط را انتخاب کنید", "agent_type"),
                    "value" => ""
                ];
                $selectedText = "";
                foreach (AgentType::all() as $item) {
                    $option = ["value" => $item->id, "text" => Option::local($item->id, $item->caption, "agent_type")];
                    if ($id == $item->id) {
                        $selectedText = Option::local($item->id, $item->caption, "agent_type");
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "feild_of_academic_degree":
                $options = null;
                $options[] = [
                    "id" => "0",
                    "text" => Option::local(0, "لطفا رشته تحصیلات را انتخاب کنید", "feild_of_academic_degree"),
                    "value" => ""
                ];
                $selectedText = "";
                foreach (FeildOfAcademicDegree::where('academic_degree_type_id', $type)->get() as $item) {
                    $option = ["value" => $item->id, "text" => Option::local($item->id, $item->caption, "feild_of_academic_degree")];
                    if ($id == $item->id) {
                        $selectedText = Option::local($item->id, $item->caption, "feild_of_academic_degree");
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "academic_degree_type":
                $options = null;
                $options[] = [
                    "id" => "0",
                    "text" => Option::local(0, "لطفا میزان تحصیلات را انتخاب کنید", "academic_degree_type"),
                    "value" => ""
                ];
                $selectedText = "";
                foreach (AcademicDegreeType::all() as $item) {
                    $option = ["value" => $item->id, "text" => Option::local($item->id, $item->caption, "academic_degree_type")];
                    if ($id == $item->id) {
                        $selectedText = Option::local($item->id, $item->caption, "academic_degree_type");
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "gender":
                $options = null;
                $options[] = [
                    "id" => "0",
                    "text" => Option::local(0, "لطفا جنسیت را انتخاب کنید", "gender"),
                    "value" => ""
                ];
                $selectedText = "";
                foreach (Gender::all() as $item) {
                    $option = ["value" => $item->id, "text" => Option::local($item->id, $item->caption, "gender")];
                    if ($id == $item->id) {
                        $selectedText = Option::local($item->id, $item->caption, "gender");
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "military":
                $options = null;
                $options[] = [
                    "id" => "0",
                    "text" => Option::local(0, "لطفا وضعیت سربازی را انتخاب کنید", "military"),
                    "value" => ""
                ];
                $selectedText = "";
                foreach (MilitaryInformation::all() as $item) {
                    $option = ["value" => $item->id, "text" => Option::local($item->id, $item->caption, "military")];
                    if ($id == $item->id) {
                        $selectedText = Option::local($item->id, $item->caption, "military");
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "dependent_type":
                $options = null;
                $options[] = [
                    "id" => "0",
                    "text" => Option::local(0, "لطفا نوع ارتباط فرد را انتخاب کنید", "dependent_type"),
                    "value" => ""
                ];
                $selectedText = "";
                foreach (DependentType::all() as $item) {
                    $option = ["value" => $item->id, "text" => Option::local($item->id, $item->caption, "dependent_type")];
                    if ($id == $item->id) {
                        $selectedText = Option::local($item->id, $item->caption, "dependent_type");
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "customer_type":
                $options = null;
                $options[] = [
                    "id" => "0",
                    "text" => Option::local(0, "لطفا نوع مشتری را انتخاب کنید", "customer_type"),
                    "value" => ""
                ];
                $selectedText = "";
                foreach (CustomerType::all() as $item) {
                    $option = [
                        "value" => $item->id,
                        "text" => Option::local($item->id, $item->caption, "customer_type")
                    ];
                    if ($id == $item->id) {
                        $selectedText = Option::local($item->id, $item->caption, "customer_type");
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "priority":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا اولویت  را انتخاب کنید", "value" => $default_value];
                $selectedText = "";
                $type = $type == 0 ? 1 : $type;
                foreach (Priority::where("priority_type_id", $type)->get() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "type_of_sale_of_product":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا نوع فروش کالا را انتخاب کنید", "value" => ""];
                $selectedText = "";
                $type_of_product_list = Product\TypeOfSaleProduct\TypeOfSaleOfProduct::
                when(count($list) > 0, function ($query) use ($list) {
                    return $query->whereIn("id", $list);
                })->
                get();
                foreach ($type_of_product_list as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "product_service_type":
                $options = null;
                $options[] = [
                    "id" => "0",
                    "text" => Option::local(0, "لطفا نوع کالا / خدمت را انتخاب کنید", "product_service_type"),
                    "value" => ""
                ];
                $selectedText = "";
                foreach (Product\ProductServiceType::all() as $item) {
                    $option = [
                        "value" => $item->id,
                        "text" => Option::local($item->id, $item->caption, "product_service_type")
                    ];
                    if ($id == $item->id) {
                        $selectedText = Option::local($item->id, $item->caption, "product_service_type");
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;


            case "product_fault_sign":
                $options = null;
                $options[] = [
                    "id" => "0",
                    "text" => "لطفا یک نمود بیرونی نقص کالا را انتخاب نمایید.",
                    "value" => ""
                ];
                $selectedText = "";
                foreach (Product\Fault\ProductFaultSign::get() as $item) {
                    $option = [
                        "value" => $item->id,
                        "text" => Option::local($item->id, $item->caption, "product_service_type")
                    ];
                    if (in_array($item->id, $list)) {
                        $selectedText = Option::local($item->id, $item->caption, "product_service_type");
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "product_fault_property":
                $options = null;
                $options[] = [
                    "id" => "0",
                    "text" => "لطفا مشخصه(های) نقص را انتخاب نمایید.",
                    "value" => ""
                ];
                $selectedText = "";
                foreach (Product\Fault\ProductFaultProperties::get() as $item) {
                    $option = [
                        "value" => $item->id,
                        "text" => Option::local($item->id, $item->caption, "product_service_type")
                    ];
                    if (in_array($item->id, $list)) {
                        $selectedText = Option::local($item->id, $item->caption, "product_service_type");
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "product_fault_property_options":
                $options = null;
                $options[] = [
                    "id" => "0",
                    "text" => "لطفا گزینه را انتخاب نمایید.",
                    "value" => ""
                ];
                $selectedText = "";
                foreach (Product\Fault\ProductFaultPropertyOption::where("product_fault_property_id", $type)->get() as $item) {
                    $option = [
                        "value" => $item->value,
                        "text" => $item->caption
                    ];
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "machine_fault_sign":
                $options = null;
                $options[] = [
                    "id" => "0",
                    "text" => "لطفا یک نمود بیرونی نقص ماشین را انتخاب نمایید.",
                    "value" => ""
                ];
                $selectedText = "";
                foreach (MachineFaultSign::get() as $item) {
                    $option = [
                        "value" => $item->id,
                        "text" => Option::local($item->id, $item->caption, "product_service_type")
                    ];
                    if (in_array($item->id, $list)) {
                        $selectedText = Option::local($item->id, $item->caption, "product_service_type");
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "order_type":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا نوع فروش انتخاب کنید", "value" => ""];
                $selectedText = "";
                foreach (OrderType::all() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "contracts":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا یک قرارداد را انتخاب کنید", "value" => $default_value];
                $selectedText = "";
                foreach (Contract::where('active_status_id', 1200)->get() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "contract_types":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا نوع قرارداد را انتخاب کنید", "value" => $default_value];
                $selectedText = "";
                foreach (ContractType:: all() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
            case "contract_keyword":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا کلمه کلیدی را انتخاب کنید", "value" => $default_value];
                $selectedText = "";
                foreach (ContractKeyword:: all() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "clause_types":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا ماده قراداد را انتخاب کنید", "value" => $default_value];
                $selectedText = "";
                foreach (ClauseType:: all() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "clause_article":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا بند قراداد را انتخاب کنید", "value" => $default_value];
                $selectedText = "";
                foreach (ClauseArticle:: all() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "service_caption":
                $user_device = UserDevice::find($id);
                $device_type_id = $user_device ? $user_device->device_type_id : null;

                $options[] = ["id" => "0", "text" => "لطفا یک گزینه را انتخاب کنید", "value" => "لطفا یک گزینه را انتخاب کنید"];
                $selectedText = "";

                if ($device_type_id == 1) {
                    $devices = [
                        (object)["id" => 1, "caption" => "تلفن همراه خودم (1)"],
                        (object)["id" => 2, "caption" => "تلفن همراه خودم (2)"],
                        (object)["id" => 3, "caption" => "تلفن همراه شرکت (1)"],
                        (object)["id" => 4, "caption" => "تلفن همراه شرکت (2)"],
                        (object)["id" => 5, "caption" => "تلفن همراه شرکت (3)"],
                    ];
                } elseif ($device_type_id == 3) {
                    $devices = [
                        (object)["id" => 1, "caption" => "سیستم شرکت (1)"],
                        (object)["id" => 2, "caption" => "سیستم شرکت (2)"],
                        (object)["id" => 3, "caption" => "سیستم شرکت (3)"],
                        (object)["id" => 4, "caption" => "سیستم خودم (1)"],
                        (object)["id" => 5, "caption" => "سیستم خودم (2)"],
                    ];
                } elseif ($device_type_id == 2) {
                    $devices = [
                        (object)["id" => 1, "caption" => "تبلت شرکت (1)"],
                        (object)["id" => 2, "caption" => "تبلت شرکت (2)"],
                        (object)["id" => 3, "caption" => "تبلت شرکت (3)"],
                        (object)["id" => 4, "caption" => "تبلت خودم (1)"],
                        (object)["id" => 5, "caption" => "تبلت خودم (2)"],
                    ];
                } else {
                    $devices = [
                        (object)["id" => 1, "caption" => "دستگاه نوع دیگر 1"],
                        (object)["id" => 2, "caption" => "دستگاه نوع دیگر 2"]
                    ];
                }

                foreach ($devices as $item) {
                    $option = ["value" => $item->caption, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $selectedText,
                    "text" => $selectedText,
                ];
                break;
            case "bank_names":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا یک بانک را انتخاب کنید", "value" => $default_value];
                $selectedText = "";
                foreach (Bank::where('is_bank_allowed_to_choose', 1)->get() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "posts":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا یک پست را انتخاب کنید", "value" => $default_value];
                $selectedText = "";
                foreach (Post::whereNotIn("id", Post::InvalidPost($invalid_list))->where("active_status_id", 1200)->get() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "posts_multi_select":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا یک یا چند پست را انتخاب کنید", "value" => $default_value];
                $selectedText = "";
                foreach (Post::whereNotIn("id", Post::InvalidPost())->where("active_status_id", 1200)->get() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if (in_array($item->id, $list)) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }


                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "company":
                $options = null;
                $options[] = ["id" => "1", "text" => "لطفا یک شرکت را انتخاب کنید", "value" => ""];
                $selectedText = "";
                foreach (Company::all() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "empty_posts":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا یک پست را انتخاب کنید", "value" => $default_value];
                $selectedText = "";
                $cooperation_type_id = $type;

                // نوع همکاری هایی که در پست ها وجود دارد.
                $cooperation_type_list = PostCooperationType::
                where("cooperation_type_id", $cooperation_type_id)->
                pluck("post_id", "post_id")->
                toArray();
                $cooperation_type_list[] = -1;

                $list = Post::
                join('shifts', "shift_id", "shifts.id")->
                leftJoin("post_user", "post_id", "posts.id")->
                whereNotIn("posts.id", Post::InvalidPost())->
                where("posts.active_status_id", 1200)->
                whereIn("posts.id", $cooperation_type_list)->
                selectRaw("posts.id,posts.caption,count(post_user.id) as post_user_count,max_person_number_in_shift_work * number_of_shift_work as max")->
                groupBy("posts.id")->
                get();

                foreach ($list as $item) {
                    if ($item->post_user_count < $item->max) {
                        $option = ["value" => $item->id, "text" => $item->caption];
                        if ($id == $item->id) {
                            $selectedText = $item->caption;
                            $option["selected"] = 1;
                        }
                        $options[] = $option;
                    }
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "post_user":
                $options = null;
                //$options[]    = [ "id" => "0", "text" => "لطفا یک پست را انتخاب کنید", "value" => $default_value ];
                $option[] = [];
                $selectedText = "";
                $list = PostUser::join("users", "user_id", "users.id")->
                groupBy("post_id")->
                groupBy("user_id")->
                where("cooperation_type_id", "!=", 3)-> // مشتری را نمی توان مستقیم اضافه کرد.
                select("post_user.id", "post_id", "user_id")->
                get();
                foreach ($list as $item) {
                    $option = [
                        "value" => $item->id,
                        "text" => $item->worker->fullName() . " (" . $item->post->caption . ")"
                    ];
                    if ($id == $item->id) {
                        $selectedText = $item->worker->fullName() . " (" . $item->post->caption . ")";
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;


            case "car_types":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا نوع وسیله نقلیه را انتخاب کنید", "value" => ""];
                $selectedText = "";
                foreach (CarType::all() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "car_type_with_weight_volume":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا نوع وسیله نقلیه را انتخاب کنید", "value" => ""];
                $selectedText = "";
                $car_type_list = CarType::
                where("min_weight", "<", $list["weight"])->
                where("max_weight", ">", $list["weight"])->
                where("max_volume", ">", $list["volume"])->
                get();

                foreach ($car_type_list as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "doors":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا درب را انتخاب کنید", "value" => ""];
                $selectedText = "";
                foreach (Door::all() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "organization_category":
                $options = null;
                $options[] = ["id" => "1", "text" => "لطفا نقش را انتخاب کنید", "value" => ""];
                $selectedText = "";
                foreach (OrganizationCategory::all() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "port_parent_diff_child":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا پست مافوق را انتخاب کنید", "value" => ""];
                $selectedText = "";
                foreach (Post::all() as $item) {

                    $option = ["value" => $item->id, "text" => $item->caption];

                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;

                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "cost_center":
                $options = null;
                $options[] = ["id" => "", "text" => "لطفا یک مرکز هزینه را انتخاب کنید", "value" => ""];
                $selectedText = "";
                foreach (CostCenter::all() as $item) {
                    $option = ["value" => $item->id, "text" => $item->code . " - " . $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->code . "-" . $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "machine_type_calculation_method":
                $options = null;
                $selectedText = "";
                foreach (MachineTypeCalculationMethod::all() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "raw_material_request_algorithm_type":
                $options = null;
                $options[] = ["id" => "", "text" => "لطفا یک الگوریتم  را انتخاب کنید", "value" => ""];
                $selectedText = "";
                foreach (RawMaterialRequestAlgorithmType::all() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "smart_object_post":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا یک شیء را انتخاب کنید.", "value" => ""];
                $selectedText = "";
                $list_smart_object = SmartObject::where("smart_object_type_id", $type)->
                when($list != [], function ($query) use ($list) {
                    return $query->whereIn("id", $list);
                })->
                get();
                foreach ($list_smart_object as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($item->id == $id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "smart_object":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا یک شیء را انتخاب کنید.", "value" => ""];
                $selectedText = "";
                foreach (SmartObject::whereIn("smart_object_type_id", $list)->get() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "smart_object_multiple":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا یک شیء را انتخاب کنید.", "value" => ""];
                $selectedText = "";
                foreach (SmartObject::where("smart_object_type_id", $type)->get() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if (in_array($item->id, $id)) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "smart_object_type":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا نوع شیء را انتخاب کنید.", "value" => ""];
                $selectedText = "";
                foreach (SmartObjectType::all() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;


            case "field_type":
                $options = null;
                $selectedText = "";
                foreach (FieldType::all() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "special_unit":
                $options = null;
                $options[] = ["id" => "0", "text" => "فاقد واحد", "value" => "0"];
                $selectedText = "فاقد واحد";
                foreach (SpecialUnit::where("special_unit_type_id", $type == 0 ? 1 : $type)->get() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "production_algorithm_type":

                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا اولویت سفارش را انتخاب کنید", "value" => ""];
                $selectedText = "";
                foreach (ProductionAlgorithmType::all() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "barcode_algorithm":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا نوع الگوریتم بارکد را انتخاب کنید", "value" => ""];
                $selectedText = "";
                foreach (Algorithm::where("algorithm_type_id", 400)->get() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "production_type":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا اولویت  را انتخاب کنید", "value" => $default_value];
                $selectedText = "";
                $type = $type == 0 ? 1 : $type;
                foreach (ProductionType::all() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "shift_work":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا یک گروه شیفت را انتخاب کنید", "value" => ""];
                $selectedText = "";
                $type = $type == 0 ? 3 : $type;
                foreach (ShiftWork::where("id", "<=", $type ?? 3)->get() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "shift":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا یک شیفت را انتخاب کنید", "value" => ""];
                $selectedText = "";
                $list_shift = Shift::
                where("active_status_id", 1200)->
                when($list != [], function ($query) use ($list) {
                    return $query->whereIn("number_of_shift_work", $list);
                })->
                get();
                foreach ($list_shift as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "shift_delivery_module":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا یک مازول تحویل شیفت را انتخاب کنید", "value" => "0"];
                $selectedText = "";
                foreach (ShiftDeliveryModule::get() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "country":
                $options = null;
                $options[] = [
                    "id" => "0",
                    "text" => Option::local("0", "لطفا یک کشور را انتخاب کنید", "country"),
                    "value" => 0
                ];
                $selectedText = "";
                foreach (Country::get() as $item) {
                    $option = ["value" => $item->id, "text" => Option::local($item->id, $item->caption, "country")];
                    if ($id == $item->id) {
                        $selectedText = Option::local($item->id, $item->caption, "country");
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "cooperation_type":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا نوع همکاری را انتخاب کنید", "value" => ""];
                $selectedText = "";
                foreach (CooperationType::whereIn("id", $list)->orderBy("priority_number")->get() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "degree_type":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا نوع درجه را انتخاب کنید", "value" => ""];
                $selectedText = "";
                foreach (DegreeType::all() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "head_of_check_type":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا زمان تحویل را مشخص کنید", "value" => ""];
                $selectedText = "";
                foreach (HeadOfCheckType::all() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "check_delivery_time_type":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا راس چک ها را مشخص کنید", "value" => ""];
                $selectedText = "";
                foreach (CheckDeliveryTimeType::all() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "carrier_type":
                $options = null;
                $options[] = ["id" => "0", "text" => "فاقد حامل", "value" => "0"];
                $selectedText = "";
                foreach (CarrierType::orderBy("carrier_group_id")->orderBy("id")->get() as $item) {
                    $option = ["value" => $item->id, "text" => $item->id . " - " . $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->id . " - " . $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "packing_type":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا نوع بسته بندی را انتخاب کنید", "value" => ""];
                $selectedText = "";
                $list_packing = $type == 0 ?
                    PackingType::where("active_status_id", 1200)->get() :
                    PackingType::
                    join("goods_kind_packing_type", "packing_type_id", "packing_types.id")->
                    select("packing_types.id", "packing_types.caption")->
                    where("goods_kind_id", $type)->
                    where("packing_types.active_status_id", 1200)->
                    select("packing_types.*")->
                    get();
                foreach ($list_packing as $item) {
                    if (count($list) == 0 || in_array($item->layers()->count(), $list)) {
                        $option = ["value" => $item->id, "text" => $item->code . " - " . $item->caption];
                        if ($id == $item->id ) {
                            $selectedText = $item->code . " - " . $item->caption;
                            $option["selected"] = 1;
                        }
                        $options[] = $option;
                    }
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "packing_type_by_list":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا نوع بسته بندی را انتخاب کنید", "value" => ""];
                $selectedText = "";
                $list_packing =
                    PackingType::
                    whereIn("id", $list)->
                    get();
                foreach ($list_packing as $item) {
                    $option = ["value" => $item->id, "text" => $item->code . " - " . $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->code . " - " . $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;

                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "production_packing_type":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا نوع بسته بندی را انتخاب کنید", "value" => ""];
                $selectedText = "";

                foreach ($list as $item) {

                    $option = [
                        "value" => $item->packing_type->id,
                        "text" => $item->packing_type->code . " - " . $item->packing_type->caption
                    ];
                    if ($id == $item->packing_type->id) {
                        $selectedText = $item->packing_type->code . " - " . $item->packing_type->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }


                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "packing_type_extraction_separately":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا نوع بسته بندی را انتخاب کنید", "value" => ""];
                $selectedText = "";
                $list_packing = $type == 0 ?
                    PackingType::all() :
                    PackingType::
                    join("goods_kind_packing_type", "packing_type_id", "packing_types.id")->
                    select("packing_types.id", "packing_types.caption")->
                    where("goods_kind_id", $type)->
                    where("it_is_possible_extract_production_form_separately", 1)->
                    get();
                foreach ($list_packing as $item) {
                    if (count($list) == 0 || in_array($item->layers()->count(), $list)) {
                        $option = ["value" => $item->id, "text" => $item->caption];
                        if ($id == $item->id) {
                            $selectedText = $item->caption;
                            $option["selected"] = 1;
                        }
                        $options[] = $option;
                    }
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "packing_type_product":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا نوع بسته بندی را انتخاب کنید", "value" => ""];
                $selectedText = "";
                $list_packing = Product\ProductPackingType::where("product_id", $type)->where("product_id", ">", "0")->get();

                foreach ($list_packing as $item) {

                    $option = [
                        "value" => $item->packing_type_id,
                        "text" => $item->packing_type->code . "-" . $item->packing_type->caption
                    ];
                    if ($id == $item->packing_type_id|| ((count($list_packing)==1)&& $select_defult_if_count_is_one )) {
                        $selectedText = $item->packing_type->code . "-" . $item->packing_type->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;

                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "warehouse_storage_type_option":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا نوع انبارش کالا را انتخاب کنید", "value" => ""];
                $selectedText = "";
                $list = ProductWarehouseStorageType::where("product_id", $type)->pluck("warehouse_storage_type_id")->toArray();

                $list_packing = WarehouseStorageType::
                when(count($list) > 0, function ($query) use ($list) {
                    return $query->whereIn("id", $list);
                })->
                get();

                
                foreach ($list_packing as $item) {

                    $option = [
                        "value" => $item->id,
                        "text" => $item->caption
                    ];
                    if ($id == $item->id||((count($list_packing)==1)&& $select_defult_if_count_is_one)) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;

                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "product_fault":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا نوع نقص کالا را انتخاب کنید", "value" => ""];
                $selectedText = "";
                $list = Product\Fault\ProductFault::get();

                foreach ($list as $item) {

                    $option = [
                        "value" => $item->id,
                        "text" => $item->code . "-" . $item->caption
                    ];
                    if ($id == $item->id) {
                        $selectedText = $item->code . "-" . $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;

                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;


            case "packing_type_from_output_band":
                $options = null;
                // $options[]        = [ "id" => "0", "text" => "لطفا نوع بسته بندی را انتخاب کنید", "value" => "" ];
                $selectedText = "";
                $packing_type_ids = MachineTypeOutputBandPackingType::
                join("machine_type_output_bands", "machine_type_output_band_id", "machine_type_output_bands.id")->
                where("active_status_id", 1200)->
                where("machine_type_output_bands.machine_type_id", $type)->
                when(count($list) > 0, function ($query) use ($list) {
                    return $query->whereIn("goods_kind_id", $list);
                })->
                pluck("packing_type_id")->toArray();

                $list = PackingType::whereIn("id", $packing_type_ids)->get();

                foreach ($list as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id || count($list) == 1) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "carrier_type_goods_kind":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا نوع حامل را انتخاب کنید", "value" => ""];
                $selectedText = "";
                foreach (GoodsKind::find($type)->carrier_type as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "station_operation":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا نوع عملیات را انتخاب کنید", "value" => ""];
                $selectedText = "";
                foreach (StationOperation::where("station_id", $type)->where("station_operation_type_id", "!=", 0)->get() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "station_operation_bom":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا نوع عملیات را انتخاب کنید", "value" => ""];
                $selectedText = "";

                // گرفتن همه عملیات هایی که در مسیر تعریف شده
                $operation_ids = LineProductStation::
                where("product_route_id", $list)->
                where("station_id", $type)->
                pluck("station_operation_id")->
                toArray();

                $operation_ids[] = 0;


                $list_station = StationOperation::whereIn("id", $operation_ids)->get();

                foreach ($list_station as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "station_sub_operation_bom":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا نوع عملیات فرعی را انتخاب کنید", "value" => ""];
                $selectedText = "";

                // گرفتن همه عملیات هایی فرعی

                $list_station_sub_operation = Station\Operation\StationSubOperation::
                where("station_operation_id", $type)->
                get();

                foreach ($list_station_sub_operation as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "fabric_raw_type_of_cut":
                $options = null;

                $selectedText = "";
                foreach (FabricRawTypeOfCut::get() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "production_methods":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا کد روش تولید را انتخاب کنید", "value" => "0"];

                $selectedText = "";
                foreach (ProductionMethod::get() as $item) {
                    $option = ["value" => $item->id, "text" => $item->getCode()];
                    if ($id == $item->id) {
                        $selectedText = $item->getCode();
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;


            case "contractor":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا پیمانکار  را انتخاب کنید", "value" => ""];
                $selectedText = "";
                foreach (Contractor::where("active_status_id", 1200)->get() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "contractor_operation":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا نوع عملیات پیمانکار  را انتخاب کنید", "value" => ""];
                $selectedText = "";
                foreach (ContractorOperation::where("contractor_id", $type)->get() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;


            case "supplier":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا تامین کننده  را انتخاب کنید", "value" => ""];
                $selectedText = "";
                $list_supplier =
                    Supplier::where("active_status_id", 1200)->
                    get();
                foreach ($list_supplier as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "supplier_product":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا تامین کننده  را انتخاب کنید", "value" => ""];
                $selectedText = "";
                $list_supplier =
                    Supplier::
                    join("line_product_station", "supplier_id", "suppliers.id")->
                    join("product_routes", "product_routes.id", "product_route_id")->
                    where("product_routes.active_status_id", 1200)->
                    where("product_routes.product_id", $type)->
                    select("suppliers.*")->
                    get();

                foreach ($list_supplier as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

                case "customer_product":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا مشتری  را انتخاب کنید", "value" => ""];
                $selectedText = "";
                $list_customer =
                    Customer::
                    join("line_product_station", "customer_id", "customers.id")->
                    join("product_routes", "product_routes.id", "product_route_id")->
                    where("product_routes.active_status_id", 1200)->
                    where("product_routes.product_id", $type)->
                    select("customers.*")->
                    get();

                foreach ($list_customer as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

                case "product_customer_line_product_station":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا محصول را انتخاب کنید", "value" => ""];
                $selectedText = "";
                $list_product =
                   Customer::
                    join("line_product_station", "customer_id", "customers.id")->
                    join("product_routes", "product_routes.id", "product_route_id")->
                    join("products", "line_product_station.product_id", "products.id")->
                    where("product_routes.active_status_id", 1200)->
                    where("line_product_station.customer_id", $type)->
                    select("products.*")->
                    get();


                foreach ($list_product as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "supplier_type":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا نوع تامین کننده  را انتخاب کنید", "value" => ""];
                $selectedText = "";
                foreach (SupplierType::get() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "absorption_type":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا نوع جذب را انتخاب کنید", "value" => ""];
                $selectedText = "";
                foreach (SellingType::get() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;


            case "selling_type":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا نوع فروش  را انتخاب کنید", "value" => ""];
                $selectedText = "";
                foreach (SellingType::get() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;


            case "office_automation_to_do_type":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا نوع  را انتخاب کنید", "value" => ""];
                $selectedText = "";
                $list[] = 0;
                foreach (OfficeAutomationToDoType::whereNotIn("id", $list)->get() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;


            case "printers":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا  پرینتر  را انتخاب کنید", "value" => ""];
                $selectedText = "";
                foreach (\App\Models\Utility\Printer\Printer::where("printer_type_id", $type)->get() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption . " (کد  " . $item->code . ")"];
                    if ($id == $item->id) {
                        $selectedText = $item->caption . " (کد " . $item->code . ")";
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "printer_type":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا نوع پرینتر را انتخاب کنید", "value" => ""];
                $selectedText = "";
                foreach (PrinterType::get() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "print_number":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا تعداد پرینت را انتخاب کنید", "value" => ""];
                $selectedText = "";
                for ($k = 1; $k <= 5; $k++) {
                    $option = ["value" => $k, "text" => $k . " پرینت "];
                    if ($id == $k) {
                        $selectedText = $k . " پرینت ";
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
                break;

            case "committee":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا کمیته را انتخاب کنید", "value" => 0];
                $selectedText = "";

                $list_items = Committee::
                where("active_status_id", 1200)->
                get();
                foreach ($list_items as $item) {
                    $option = ["value" => $item->id, "text" => $item->code . " - " . $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->code . "-" . $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "special_license_type":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا نوع مجوز را انتخاب کنید", "value" => 0];
                $selectedText = "";

                $list_items = SpecialLicenseType::
                get();
                foreach ($list_items as $item) {
                    $option = ["value" => $item->id, "text" => $item->code . " - " . $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->code . "-" . $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "smart_option":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا یک شیء هوشمند را انتخاب کنید", "value" => 0];
                $selectedText = "";

                $list_items = SmartObject::
                get();
                foreach ($list_items as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "floating_post":
                $options = null;
                $selectedText = "";

                $list_items = FloatingPostType::all();

                foreach ($list_items as $item) {
                    $option = ["value" => $item["id"], "text" => $item["caption"]];
                    if ($id == $item["id"]) {
                        $selectedText = $item["caption"];
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "financial_operation_pattern_type":
                $options = null;
                $selectedText = "";

                $list_items = FinancialOperationPatternType::all();
                $options[] = ["id" => "0", "text" => "لطفا نوع الگوی عملیات مالی را انتخاب کنید", "value" => 0];
                foreach ($list_items as $item) {
                    $option = ["value" => $item["id"], "text" => $item["caption"]];
                    if ($id == $item["id"]) {
                        $selectedText = $item["caption"];
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "financial_operation_pattern":
                $options = null;
                $selectedText = "";

                $list_items = FinancialOperationPattern::
                where("financial_operation_pattern_type_id", $type)->
                get();
                $options[] = ["id" => "0", "text" => "لطفا نوع الگوی عملیات مالی را انتخاب کنید", "value" => 0];

                foreach ($list_items as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item["id"]) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "financial_operation_pattern_item_types":
                $options = null;
                $selectedText = "";

                $list_items = FinancialOperationPatternItemType::                get();
                $options[] = ["id" => "0", "text" => "لطفا نوع ارتباط حساب با الگوی مالی را انتخاب کنید", "value" => 0];

                foreach ($list_items as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item["id"]) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "account":
                $options = null;
                $selectedText = "";


                $account_ids = Account:: whereNotNull("parent_id")->pluck("parent_id")->toArray();
                $list_items = Account::
                orderBy("full_code")->
                whereNotNull("parent_id")->
                whereNotIn("id", $account_ids)->
                get();

                foreach ($list_items as $item) {
                    $option = ["value" => $item["id"], "text" => $item->fullCaption()];
                    if ($id == $item["id"]) {
                        $selectedText = $item->fullCaption();
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "shift_work_group_type_multi":
                $options = null;
                $selectedText = "";
                $list = ShiftWorkGroupType::get();
                foreach ($list as $item) {
                    $option = ["value" => $item["id"], "text" => $item->caption];
                    if (in_array($item["id"], $id)) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "button_list":

                $options = null;
                $selectedText = "";
                $options[] = ["id" => "0", "text" => "لطفا یک مورد را انتخاب کنید", "value" => ""];

                $list_items = Button::whereIn("id", $list)->get();

                foreach ($list_items as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item["id"]) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "evaluation_indicator":
                $options = null;
                $options[] = [
                    "id" => "0",
                    "text" => Option::local(0, "لطفا شاخص را انتخاب کنید", "evaluation_indicator"),
                    "value" => ""
                ];
                $selectedText = "";
                foreach (EvaluationIndicator::all() as $item) {
                    $option = ["value" => $item->id, "text" => Option::local($item->id, $item->caption, "evaluation_indicator")];
                    if ($id == $item->id) {
                        $selectedText = Option::local($item->id, $item->caption, "evaluation_indicator");
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "evaluation_completion_type":
                $options = null;
                $options[] = [
                    "id" => "0",
                    "text" => Option::local(0, "لطفا نوع تکمیل را انتخاب کنید", "evaluation_completion_type"),
                    "value" => ""
                ];
                $selectedText = "";
                foreach (EvaluationCompletionType::all() as $item) {
                    $option = ["value" => $item->id, "text" => Option::local($item->id, $item->caption, "evaluation_completion_type")];
                    if ($id == $item->id) {
                        $selectedText = Option::local($item->id, $item->caption, "evaluation_completion_type");
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
            case "education_type":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا گزینه مورد نظر خود را انتخاب کنید", "value" => 0];
                $selectedText = "";
                foreach (EducationType::all() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "exam_type":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا گزینه مورد نظر خود را انتخاب کنید", "value" => 0];
                $selectedText = "";
                foreach (ExamType::all() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "selection_type":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا گزینه مورد نظر را انتخاب کنید", "value" => 0];
                $selectedText = "";
                foreach (SelectionType::all() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "selection_active":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا یک گزینش انتخاب کنید", "value" => $default_value];
                $selectedText = "";
                foreach (Selection::where('active_status_id', 1200)->get() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "post_selectionsetting":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا یک گزینش را انتخاب کنید", "value" => $default_value];
                $selectedText = "";
                foreach (Selection:: all() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "personal_type":
                $options = null;
                $options[] = [
                    "id" => "0",
                    "text" => Option::local(0, "لطفا نوع فرد را انتخاب کنید", "personal_type"),
                    "value" => ""
                ];
                $selectedText = "";
                foreach (PersonalType::all() as $item) {
                    $option = [
                        "value" => $item->id,
                        "text" => Option::local($item->id, $item->caption, "personal_type")
                    ];
                    if ($id == $item->id) {
                        $selectedText = Option::local($item->id, $item->caption, "personal_type");
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "nationality":
                $options = null;
                $options[] = [
                    "id" => "0",
                    "text" => Option::local("0", "لطفا ملیت خود را انتخاب کنید", "country"),
                    "value" => 0
                ];
                $selectedText = "";
                foreach (Nationality::get() as $item) {
                    $option = ["value" => $item->id, "text" => Option::local($item->id, $item->caption, "country")];
                    if ($id == $item->id) {
                        $selectedText = Option::local($item->id, $item->caption, "country");
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "education":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا یک آموزش را انتخاب کنید", "value" => $default_value];
                $selectedText = "";
                foreach (Education:: all() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "algorithm":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا یک الگوریتم را انتخاب کنید", "value" => $default_value];
                $selectedText = "";
                foreach (Algorithm:: where("algorithm_type_id", $type)->get() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "reservoir_type":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا نوع مخزن را انتخاب نمایید.", "value" => ""];
                $selectedText = "";
                foreach (ReservoirType::get() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "report_break":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا نوع تفکیک گزارش(ها) را مشخص نمایید", "value" => ""];
                $selectedText = "";
                $list = [
                    ["id" => 1, "caption" => "به تفکیک روز"],
                    ["id" => 2, "caption" => "به تفکیک ماه"],
                ];
                foreach ($list as $item) {
                    $option = ["value" => $item["id"], "text" => $item["caption"]];
                    if ($id == $item["id"]) {
                        $selectedText = $item["caption"];
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "auto_exit_option_algorithm":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا نوع الگوریتم هوشمند را انتخاب نمایید", "value" => ""];
                $selectedText = "";
                $list = Script1025Controller::$AlgorithmList;
                foreach ($list as $key => $item) {
                    $option = ["value" => $key, "text" => $item];
                    if ($id == $key) {
                        $selectedText = $item;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;
            case "warehouse_shelving_option_list":
                $options = null;

                $selectedText = "";
                $warehouse_shelving_list = WarehouseShelving::
                where("warehouse_shelving_line_status_id", 1200)->
                where("can_product_directly_in_location", 1)->
                with("warehouse")->
                orderBy("id")->
                get();

                $option_list = [];
                foreach ($warehouse_shelving_list as $item) {
                    $option = ["value" => $item->id, "text" => $item->fullCode()];

                    if (in_array($item->id, $list["warehouse_shelving_ids"])) {

                        $option["selected"] = 1;
                    }
                    $option_list[$item->warehouse_id][] = $option;
                }

                return [
                    "items" => $option_list,
                    "value" => "",

                ];
                break;

            case "warehouse_shelving_option_type_list":
                $options = null;

                $selectedText = "";
                $warehouse_shelving_type_list = WarehouseShelvingType::
                orderBy("id")->
                get();

                $option_list = [];
                foreach ($list["warehouse_ids_where_have_shelving"] as $warehouse_id) {
                    foreach ($warehouse_shelving_type_list as $item) {
                        $option = ["value" => $item->id, "text" => "همه " . $item->caption . " ها"];

                        if (isset($list["warehouse_shelving_type_ids"][$warehouse_id]) && in_array($item->id, $list["warehouse_shelving_type_ids"][$warehouse_id])) {

                            $option["selected"] = 1;
                        }
                        $option_list[$warehouse_id][] = $option;
                    }
                }
                return [
                    "items" => $option_list,
                    "value" => "",

                ];
                break;


            case "product_request_form_type":
                $options = null;
                $options[] = ["id" => "0", "text" => "لطفا نوع را انتخاب کنید", "value" => $default_value];
                $selectedText = "";
                foreach (Product\ProductRequest\ProductRequestFormType::get() as $item) {
                    $option = ["value" => $item->id, "text" => $item->caption];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "machine_allocation_list":
                $selectedText = "";
                foreach ($list as $item) {
                    $option = ["value" => $item->id, "text" => $item->production->serial()];

                    if ($item->id == $id) {

                        $option["selected"] = 1;
                        $selectedText = $item->production->serial();
                    }
                    $option_list[] = $option;
                }

                return [
                    "items" => $option_list,
                    "value" => "",
                    "text" => $selectedText,

                ];
                break;

            case "shipping_methods":
                $options = null;
                $options[] = [
                    "id" => "0",
                    "text" => "لطفا نوع ارسال بار را انتخاب کنید",
                    "value" => ""
                ];
                $selectedText = "";
                foreach (ShippingMethod::all() as $item) {
                    $option = [
                        "value" => $item->id,
                        "text" =>  $item->caption
                    ];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
                break;

            case "delivery_point_type":
                $options = null;
                $options[] = [
                    "id" => "0",
                    "text" => "لطفا محل تحویل بار را انتخاب کنید",
                    "value" => ""
                ];
                $selectedText = "";
                foreach (DeliveryPointType::all() as $item) {
                    $option = [
                        "value" => $item->id,
                        "text" =>  $item->caption
                    ];
                    if ($id == $item->id) {
                        $selectedText = $item->caption;
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];
 break;

            case "machine_production_channel_type":
                $options = null;
                $options[] = [
                    "id" => "0",
                    "text" => "",
                    "value" => ""
                ];
                $selectedText = "";
                foreach (ChannelType::all() as $item) {
                    $option = [
                        "value" => $item->id,
                        "text" => $item->caption
                    ];
                    if ($id == $item->id) {
                        $selectedText = Option::local($item->id, $item->caption, "goods_kind");
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "items" => $options,
                    "value" => $id,
                    "text" => $selectedText,
                ];                break;

        }

        return $options;


    }

    public static function OrderBy($type, $value = "")
    {
        switch ($type) {
            case "production":
                $options = null;
                $options[] = ["value" => "", "text" => ""];
                $selectedText = "";
                $items = [
                    ["value" => "serial__asc", "text" => "سریال تولید صعودی"],
                    ["value" => "products.code__asc", "text" => "کد محصول صعودی"],
                    ["value" => "products.caption__asc", "text" => "نام محصول صعودی"],
                    ["value" => "production_cards.number__asc", "text" => "تعداد صعودی"],
                    ["value" => "production_cards.created_at__asc", "text" => "تاریخ ابلاغ صعودی "],
                    ["value" => "production_cards.updated_at__desc", "text" => "آخرین ویرایش نزولی"],
                    // ["value"=>"production_cards.status_id__asc","text"=>"وضعیت ابلاغ صعودی "],

                    ["value" => "serial__desc", "text" => "سریال تولید نزولی"],
                    ["value" => "products.code__desc", "text" => "کد محصول نزولی"],
                    ["value" => "products.caption__desc", "text" => "نام محصول نزولی"],
                    ["value" => "production_cards.number__desc", "text" => "تعداد نزولی"],
                    ["value" => "production_cards.created_at__desc", "text" => "تاریخ ابلاغ نزولی "],
                ];
                foreach ($items as $item) {
                    $option = ["value" => $item["value"], "text" => $item["text"]];
                    if ($value == $item["value"]) {
                        $selectedText = $item["text"];
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "id" => "order_by",
                    "label" => "مرتب سازی ",
                    "items" => $options,
                    "value" => $value,
                    "text" => $selectedText,
                ];
                break;
            case "production_form":
                $options = null;
                $options[] = ["value" => "", "text" => ""];
                $selectedText = "";
                $items = [
                    ["value" => "production_forms.code__asc", "text" => "کد فرم  صعودی"],
                    ["value" => "carrier_id__asc", "text" => "کد حامل صعودی"],
                    ["value" => "machine_id__asc", "text" => "ماشین صعودی"],
                    ["value" => "production_forms.created_at__asc", "text" => "تاریخ ایجاد صعودی"],

                    ["value" => "production_forms.code__desc", "text" => "کد فرم  نزولی"],
                    ["value" => "carrier_id__desc", "text" => "کد حامل نزولی"],
                    ["value" => "machine_id__desc", "text" => "ماشین نزولی"],
                    ["value" => "production_forms.created_at__desc", "text" => "تاریخ ایجاد نزولی"],

                ];
                foreach ($items as $item) {
                    $option = ["value" => $item["value"], "text" => $item["text"]];
                    if ($value == $item["value"]) {
                        $selectedText = $item["text"];
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "id" => "order_by",
                    "label" => "مرتب سازی ",
                    "items" => $options,
                    "value" => $value,
                    "text" => $selectedText,
                ];
                break;
            case "fabric_raw_design_form":
                $options = null;
                $options[] = ["value" => "", "text" => ""];
                $selectedText = "";
                $items = [
                    ["value" => "code__asc", "text" => "کد فرم  صعودی"],
                    ["value" => "machine_id__asc", "text" => "ماشین صعودی"],
                    ["value" => "created_at__asc", "text" => "تاریخ ایجاد صعودی"],

                    ["value" => "code__desc", "text" => "کد فرم  نزولی"],
                    ["value" => "machine_id__desc", "text" => "ماشین نزولی"],
                    ["value" => "created_at__desc", "text" => "تاریخ ایجاد نزولی"],

                ];
                foreach ($items as $item) {
                    $option = ["value" => $item["value"], "text" => $item["text"]];
                    if ($value == $item["value"]) {
                        $selectedText = $item["text"];
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "id" => "order_by",
                    "label" => "مرتب سازی ",
                    "items" => $options,
                    "value" => $value,
                    "text" => $selectedText,
                ];
                break;
            case "orders":
                $options = null;
                $options[] = ["value" => "", "text" => ""];
                $selectedText = "";
                $items = [
                    ["value" => "orders.order_datetime__desc", "text" => "تاریخ ثبت سفارش نزولی"],
                    ["value" => "orders.created_at__desc", "text" => "تاریخ ایجاد نزولی"],
                    ["value" => "orders.code__asc", "text" => "کد سفارش صعودی"],
                    ["value" => "customer_id__asc", "text" => "نام مرکز صعودی"],
                    ["value" => "status_id__asc", "text" => "وضعیت صعودی"],
                    ["value" => "priority_id__asc", "text" => "اولویت سفارش صعودی "],

                    ["value" => "orders.code__desc", "text" => "کد سفارش نزولی"],
                    ["value" => "customer_id__desc", "text" => "نام مرکز نزولی"],
                    ["value" => "status_id__desc", "text" => "وضعیت نزولی"],
                    ["value" => "priority_id__desc", "text" => "اولویت سفارش نزولی "],

                ];
                foreach ($items as $item) {
                    $option = ["value" => $item["value"], "text" => $item["text"]];
                    if ($value == $item["value"]) {
                        $selectedText = $item["text"];
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "id" => "order_by",
                    "label" => "مرتب سازی ",
                    "items" => $options,
                    "value" => $value,
                    "text" => $selectedText,
                ];
                break;

            case "customer":
                $options = null;
                $options[] = ["value" => "", "text" => ""];
                $selectedText = "";
                $items = [
                    ["value" => "caption__asc", "text" => "نام مرکز صعودی"],

                    ["value" => "caption__desc", "text" => "نام مرکز نزولی"],

                ];
                foreach ($items as $item) {
                    $option = ["value" => $item["value"], "text" => $item["text"]];
                    if ($value == $item["value"]) {
                        $selectedText = $item["text"];
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "id" => "order_by",
                    "label" => "مرتب سازی ",
                    "items" => $options,
                    "value" => $value,
                    "text" => $selectedText,
                ];
                break;
            case "customer_buy":
                $options = null;
                $options[] = ["value" => "", "text" => ""];
                $selectedText = "";
                $items = [
                    ["value" => "products.code__asc", "text" => "کد محصول صعودی"],
                    ["value" => "products.code__desc", "text" => "کد محصول نزولی"],
                    ["value" => "fea__asc", "text" => "قیمت صعودی"],
                    ["value" => "fea__desc", "text" => "قیمت نزولی"],

                ];
                foreach ($items as $item) {
                    $option = ["value" => $item["value"], "text" => $item["text"]];
                    if ($value == $item["value"]) {
                        $selectedText = $item["text"];
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "id" => "order_by",
                    "label" => "مرتب سازی ",
                    "items" => $options,
                    "value" => $value,
                    "text" => $selectedText,
                ];
                break;
            case "maintenance":
                $options = null;
                $options[] = ["value" => "", "text" => ""];
                $selectedText = "";
                $items = [
                    ["value" => "code__asc", "text" => "شماره درخواست صعودی"],

                    ["value" => "code__desc", "text" => "شماره درخواست نزولی"],

                ];
                foreach ($items as $item) {
                    $option = ["value" => $item["value"], "text" => $item["text"]];
                    if ($value == $item["value"]) {
                        $selectedText = $item["text"];
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "id" => "order_by",
                    "label" => "مرتب سازی ",
                    "items" => $options,
                    "value" => $value,
                    "text" => $selectedText,
                ];
                break;
            case "warehouse_input":
                $options = null;
                $options[] = ["value" => "", "text" => ""];
                $selectedText = "";
                $items = [
                    ["value" => "forms.created_at__asc", "text" => "تاریخ ایجاد صعودی"],
                    ["value" => "forms.updated_at__asc", "text" => "آخرین ویرایش صعودی"],
                    ["value" => "warehouse_id__asc", "text" => "انبار صعودی"],

                    ["value" => "forms.created_at__desc", "text" => "تاریخ ایجاد نزولی"],
                    ["value" => "forms.updated_at__desc", "text" => "آخرین ویرایش نزولی"],
                    ["value" => "warehouse_id__desc", "text" => "انبار نزولی"],

                ];
                foreach ($items as $item) {
                    $option = ["value" => $item["value"], "text" => $item["text"]];
                    if ($value == $item["value"]) {
                        $selectedText = $item["text"];
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "id" => "order_by",
                    "label" => "مرتب سازی ",
                    "items" => $options,
                    "value" => $value,
                    "text" => $selectedText,
                ];
                break;
            case "warehouse_output":
                $options = null;
                $options[] = ["value" => "", "text" => ""];
                $selectedText = "";
                $items = [
                    ["value" => "product_request_forms.created_at__asc", "text" => "تاریخ ایجاد صعودی"],
                    ["value" => "product_request_forms.updated_at__asc", "text" => "آخرین ویرایش صعودی"],
                    ["value" => "warehouse_id__asc", "text" => "انبار صعودی"],

                    ["value" => "product_request_forms.created_at__desc", "text" => "تاریخ ایجاد نزولی"],
                    ["value" => "product_request_forms.updated_at__desc", "text" => "آخرین ویرایش نزولی"],
                    ["value" => "warehouse_id__desc", "text" => "انبار نزولی"],

                ];
                foreach ($items as $item) {
                    $option = ["value" => $item["value"], "text" => $item["text"]];
                    if ($value == $item["value"]) {
                        $selectedText = $item["text"];
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "id" => "order_by",
                    "label" => "مرتب سازی ",
                    "items" => $options,
                    "value" => $value,
                    "text" => $selectedText,
                ];
                break;
            case "order_by_warehouse":
                $options = null;
                $options[] = ["value" => "", "text" => ""];
                $selectedText = "";
                $items = [
                    ["value" => "created_at__asc", "text" => "تاریخ ایجاد صعودی"],
                    ["value" => "updated_at__asc", "text" => "آخرین ویرایش صعودی"],
                    ["value" => "id__asc", "text" => "کد انبار صعودی"],
                    ["value" => "warehouse_type_id__asc", "text" => "نوع انبار صعودی"],

                    ["value" => "created_at__desc", "text" => "تاریخ ایجاد نزولی"],
                    ["value" => "updated_at__desc", "text" => "آخرین ویرایش نزولی"],
                    ["value" => "id__desc", "text" => "کد انبار نزولی"],
                    ["value" => "warehouse_type_id__desc", "text" => "نوع انبار نزولی"],

                ];
                foreach ($items as $item) {
                    $option = ["value" => $item["value"], "text" => $item["text"]];
                    if ($value == $item["value"]) {
                        $selectedText = $item["text"];
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "id" => "order_by",
                    "label" => "مرتب سازی ",
                    "items" => $options,
                    "value" => $value,
                    "text" => $selectedText,
                ];
                break;
            case "waiting_packing":
                $options = null;
                $options[] = ["value" => "", "text" => ""];
                $selectedText = "";
                $items = [
                    ["value" => "fabric_raw_grading.code__asc", "text" => "شماره ردیف صعودی"],
                    ["value" => "fabric_raw_grading.product_id__asc", "text" => "کد کالا صعودی"],

                    ["value" => "fabric_raw_grading.code__desc", "text" => "شماره ردیف  نزولی"],
                    ["value" => "fabric_raw_grading.product_id__desc", "text" => "کد کالا  نزولی"],

                ];
                foreach ($items as $item) {
                    $option = ["value" => $item["value"], "text" => $item["text"]];
                    if ($value == $item["value"]) {
                        $selectedText = $item["text"];
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "id" => "order_by",
                    "label" => "مرتب سازی ",
                    "items" => $options,
                    "value" => $value,
                    "text" => $selectedText,
                ];
                break;
            case "contractor_allocation":
                $options = null;
                $options[] = ["value" => "", "text" => ""];
                $selectedText = "";
                $items = [
                    ["value" => "serial__asc", "text" => "سریال دستور پیمان صعودی"],
                    ["value" => "production_cards.id__asc", "text" => "تاریخ ایجاد پیمان صعودی"],

                    ["value" => "serial__desc", "text" => "سریال دستور پیمان نزولی"],
                    ["value" => "production_cards.id__desc", "text" => "تاریخ ایجاد پیمان نزولی"],

                ];
                foreach ($items as $item) {
                    $option = ["value" => $item["value"], "text" => $item["text"]];
                    if ($value == $item["value"]) {
                        $selectedText = $item["text"];
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "id" => "order_by",
                    "label" => "مرتب سازی ",
                    "items" => $options,
                    "value" => $value,
                    "text" => $selectedText,
                ];
                break;
            case "public":
                $options = null;
                $options[] = ["value" => "", "text" => ""];
                $selectedText = "";
                $items = [
                    ["value" => "caption__asc", "text" => "عنوان صعودی"],

                    ["value" => "caption__desc", "text" => "عنوان نزولی"],

                ];
                foreach ($items as $item) {
                    $option = ["value" => $item["value"], "text" => $item["text"]];
                    if ($value == $item["value"]) {
                        $selectedText = $item["text"];
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "id" => "order_by",
                    "label" => "مرتب سازی ",
                    "items" => $options,
                    "value" => $value,
                    "text" => $selectedText,
                ];
                break;

            case "special_license":
                $options = null;
                $options[] = ["value" => "", "text" => ""];
                $selectedText = "";
                $items = [
                    ["value" => "special_licenses.created_at__asc", "text" => "تاریخ صعودی"],

                    ["value" => "special_licenses.created_at__desc", "text" => "تاریخ نزولی"],

                ];
                foreach ($items as $item) {
                    $option = ["value" => $item["value"], "text" => $item["text"]];
                    if ($value == $item["value"]) {
                        $selectedText = $item["text"];
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "id" => "order_by",
                    "label" => "مرتب سازی ",
                    "items" => $options,
                    "value" => $value,
                    "text" => $selectedText,
                ];
                break;

            case "supplier":
            case "contractor":
                $options = null;
                $options[] = ["value" => "", "text" => ""];
                $selectedText = "";
                $items = [
                    ["value" => "caption__asc", "text" => "نام صعودی"],

                    ["value" => "caption__desc", "text" => "نام نزولی"],

                ];
                foreach ($items as $item) {
                    $option = ["value" => $item["value"], "text" => $item["text"]];
                    if ($value == $item["value"]) {
                        $selectedText = $item["text"];
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "id" => "order_by",
                    "label" => "مرتب سازی ",
                    "items" => $options,
                    "value" => $value,
                    "text" => $selectedText,
                ];
                break;

            case "product_list":
                $options = null;
                $options[] = ["value" => "", "text" => ""];
                $selectedText = "";
                $items = [
                    ["value" => "caption__asc", "text" => "عنوان صعودی"],
                    ["value" => "code__asc", "text" => "کد کالا صعودی"],

                    ["value" => "caption__desc", "text" => "عنوان نزولی"],
                    ["value" => "code__desc", "text" => "کد کالا نزولی"],

                ];
                foreach ($items as $item) {
                    $option = ["value" => $item["value"], "text" => $item["text"]];
                    if ($value == $item["value"]) {
                        $selectedText = $item["text"];
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "id" => "order_by",
                    "label" => "مرتب سازی ",
                    "items" => $options,
                    "value" => $value,
                    "text" => $selectedText,
                ];
                break;

            case "office_automation":
                $options = null;
                $options[] = ["value" => "", "text" => ""];
                $selectedText = "";
                $items = [
                    ["value" => "caption__asc", "text" => "عنوان صعودی"],
                    ["value" => "id__asc", "text" => "کد صعودی"],
                    ["value" => "priority_id__asc", "text" => "اولویت صعودی"],
                    ["value" => "created_at__asc", "text" => "تاریخ ایجاد صعودی"],
                    ["value" => "end_datetime__asc", "text" => "تاریخ پایان صعودی"],

                    ["value" => "caption__desc", "text" => "عنوان نزولی"],
                    ["value" => "id__desc", "text" => "کد نزولی"],
                    ["value" => "priority_id__desc", "text" => "اولویت نزولی"],
                    ["value" => "created_at__desc", "text" => "تاریخ ایجاد نزولی"],
                    ["value" => "end_datetime__desc", "text" => "تاریخ پایان نزولی"],

                ];
                foreach ($items as $item) {
                    $option = ["value" => $item["value"], "text" => $item["text"]];
                    if ($value == $item["value"]) {
                        $selectedText = $item["text"];
                        $option["selected"] = 1;
                    }
                    $options[] = $option;
                }

                return [
                    "id" => "order_by",
                    "label" => "مرتب سازی ",
                    "items" => $options,
                    "value" => $value,
                    "text" => $selectedText,
                ];
                break;

        }
    }


    public static function stepInfo($type, $step = 1)
    {
        switch ($type) {
            case "call_steps":
                return [
                    "titles" => [
                        ["title" => " شروع فراخوانی "],
                        ["title" => " آپلود گزارش نوسا"],
                        ["title" => " آپلود لیست موجودی انبار"],
                        ["title" => " اجرای فراخوانی"],
                    ],
                    "step" => $step
                ];
                break;


        }
    }

    public static function getFormatCode($code, $number_count)
    {
        $code += 0;

        return $string = Str::of($code)->
        when($code < 1000 && $number_count >= 4, function ($string) {
            return Str::of('0')->append($string);
        })->
        when($code < 100 && $number_count >= 3, function ($string) {
            return Str::of('0')->append($string);
        })->
        when($code < 10 && $number_count >= 2, function ($string) {
            return Str::of('0')->append($string);
        });
    }
}
