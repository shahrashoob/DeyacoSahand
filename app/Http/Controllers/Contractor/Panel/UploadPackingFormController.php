<?php

namespace App\Http\Controllers\Contractor\Panel;

use App\Http\Controllers\Controller;
use App\Imports\ContractorPackingFormImport;
use App\Models\Contractor\ContractorAllocation;
use App\Models\Contractor\ImportContractorPackingForm;
use App\Models\Contractor\MachineAllocationPackingForm;
use App\Models\Form\Packing\PackingForm;
use App\Models\Form\Packing\PackingFormItem;
use App\Models\LineProduct\LotNumber;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\Utility\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class UploadPackingFormController extends Controller
{
    public static $info = [
        "route" => "contractor.panel.upload_packing_form.",
        "view" => "contractor.panel.upload_packing_form.",
        "enable_status" => ["104"],
        "button" => ["caption" => "آپلود بسته بندی ها", "class" => "btn-primary"],

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "contractor.panel.dashboard.";

    //
    public function __construct()
    {
        $this->route_path = UploadPackingFormController::$info["route"];
        $this->view_path = UploadPackingFormController::$info["view"];
    }

    // آپلود فایل
    public function index(ContractorAllocation $contractor_allocation)
    {

        $result = $this->checkPermission($contractor_allocation);
        if ($result != "") {
            return $result;
        }

        $model = [
            "id" => $contractor_allocation->id,
            "name" => "contractor_allocation_packing_form",
            "route" => $this->route_path . "submit",
            "caption" => "آپلود لیست بسته بندی ها برای دستور پیمان " . $contractor_allocation->production->serial()
        ];

        $packing_type_option = Option::get("packing_type", 0, $contractor_allocation->product->goods_kind_id);

        return view($this->route_path . "index", compact("model", "packing_type_option"));

    }

    public function submit(Request $request, ContractorAllocation $contractor_allocation)
    {

        $result = $this->checkPermission($contractor_allocation);
        if ($result != "") {
            return $result;
        }

        $packing_type = PackingType::find($request->packing_type_id);
        if (!$packing_type) {
            return back()->withErrors("نوع بسته بندی معتبر نمی باشد.");
        }


        $IPF = new ContractorPackingFormImport();
        $IPF->contractor_allocation = $contractor_allocation;
        $IPF->packing_type = $packing_type;

        Excel::import($IPF, request()->file('file_uploaded'));

        return redirect()->route($this->route_path . "show_upload", $contractor_allocation);

    }

    public function show_upload(ContractorAllocation $contractor_allocation)
    {

        $result = $this->checkPermission($contractor_allocation);
        if ($result != "") {
            return $result;
        }


        $sub_packing_list = ImportContractorPackingForm::
        where("machine_allocation_id", $contractor_allocation->id)->
        orderBy("id")->
        get();

        foreach ($sub_packing_list as $item) {
            $packing_count = ImportContractorPackingForm::
            where("machine_allocation_id", $contractor_allocation->id)->
            where("packing_form_row", $item->parent_packing_form_row)->
            distinct()->count("parent_packing_form_row");

            if ($packing_count != 1) {
                $item->error = $item->error . "یک بسته بندی فرعی نمی تواند در بیش از یک بسته بندی اصلی وجود داشته باشد." . " <br/>";
                $item->save();
            }

            $carrier_count = count(ImportContractorPackingForm::
            where("machine_allocation_id", $contractor_allocation->id)->
            where("packing_form_carrier_id", $item->packing_form_carrier_id)->
            whereNotNull("packing_form_carrier_id")->
            groupBy("packing_form_row")->
            get());

            if ($carrier_count > 1) {
                $item->error = $item->error . "یک حامل بسته بندی فرعی نمی تواند در بیش از یک بسته بندی وجود داشته باشد." . " <br/>";
                $item->save();
            }


            $carrier_count = count(ImportContractorPackingForm::
            where("machine_allocation_id", $contractor_allocation->id)->
            where("parent_packing_form_carrier_id", $item->parent_packing_form_carrier_id)->
            whereNotNull("parent_packing_form_carrier_id")->
            groupBy("parent_packing_form_row")->
            get());

            if ($carrier_count > 1) {
                $item->error = $item->error . "یک حامل بسته بندی اصلی نمی تواند در بیش از یک بسته بندی وجود داشته باشد." . " <br/>";
                $item->save();
            }
        }


        $list = ImportContractorPackingForm::
        where("machine_allocation_id", $contractor_allocation->id)->
        orderBy("id")->
        paginate(50);

        $error_count = ImportContractorPackingForm::where("error", "!=", "")->count();

        $count = ImportContractorPackingForm::count();;

        $parent_packing_count = count(ImportContractorPackingForm::
        where("machine_allocation_id", $contractor_allocation->id)->
        groupBy("parent_packing_form_row")->
        get());

        $packing_count = count($packing_count = ImportContractorPackingForm::
        where("machine_allocation_id", $contractor_allocation->id)->
        groupBy("packing_form_row")->
        get());

        return view($this->route_path . "show_form", compact("list", "error_count", "contractor_allocation", "count", "packing_count", "parent_packing_count"));
    }

    public function upload_product(ContractorAllocation $contractor_allocation)
    {

        $result = $this->checkPermission($contractor_allocation);
        if ($result != "") {
            return $result;
        }

        // حذف بسته بندی های موجود
        $list = MachineAllocationPackingForm::where([
            "contractor_id" => $contractor_allocation->contractor_id,
            "machine_allocation_id" => $contractor_allocation->id,
            "status_id" => 7007006 // معلق
        ])->get();
        foreach ($list as $contractor_packing_form) {
            foreach ($contractor_packing_form->packing_form->items() as $packing_form_item) {
                $packing_form_item->delete();
            }

            foreach ($contractor_packing_form->packing_form->sub_packing as $sub_packing_form) {
                foreach ($sub_packing_form->packing_form->items() as $packing_form_item) {
                    $packing_form_item->delete();
                }
                $sub_packing_form->packing_form->delete();
                $sub_packing_form->delete();
            }
            $contractor_packing_form->packing_form->delete();
            $contractor_packing_form->delete();
        }

        $packing_list = ImportContractorPackingForm::
        where("machine_allocation_id", $contractor_allocation->id)->
        orderBy("id")->
        groupBy("parent_packing_form_row")->
        get();

        foreach ($packing_list as $packing_row) {

            $packing_form = PackingForm::create([
                "carrier_id" => $packing_row->parent_packing_form_carrier_id,
                "status_id" => 7007006, // معلق
                "packing_type_id" => $packing_row->parent_packing_type_id,
            ]);

            MachineAllocationPackingForm::create([
                "contractor_id" => $contractor_allocation->contractor_id,
                "machine_allocation_id" => $contractor_allocation->id,
                "packing_form_id" => $packing_form->id
            ]);

            ImportContractorPackingForm::
            where("machine_allocation_id", $contractor_allocation->id)->
            orderBy("id")->
            where("parent_packing_form_row", $packing_row->parent_packing_form_row)->
            update(["parent_packing_form_id" => $packing_form->id]);


            $sub_packing_list = ImportContractorPackingForm::
            where("machine_allocation_id", $contractor_allocation->id)->
            where("parent_packing_form_row", $packing_row->parent_packing_form_row)->
            groupBy("packing_form_row")->
            orderBy("id")->
            get();

            if ($packing_row->parent_packing_type->first_packing_type) {

                return redirect()->back()->withErrors("تعریف بسته بندی چند لایه در سامانه تعریف نشده است");
//                foreach ( $sub_packing_list as $sub_packing_row ) {
//                    $sub_packing_form = PackingForm::create( [
//                        "carrier_id"      => $sub_packing_row->packing_form_carrier_id,
//                        "status_id"       => 7007006, // معلق
//                        "packing_type_id" => $sub_packing_row->packing_type_id
//                    ] );
//
//                    PackingFormSubPacking::create( [
//                        "packing_form_id"        => $sub_packing_form->id,
//                        "parent_packing_form_id" => $packing_form->id
//                    ] );
//
//
//                    $packing_form_item_list = ImportContractorPackingForm::
//                    where( "machine_allocation_id", $contractor_allocation->id )->
//                    where( "parent_packing_form_row", $packing_row->parent_packing_form_row )->
//                    where( "packing_form_row", $sub_packing_row->packing_form_row )->
//                    orderBy( "id" )->
//                    get();
//                    foreach ( $packing_form_item_list as $packing_form_item ) {
//                        //لات
//                        $lot_number = LotNumber::where( [
//                            "product_id" => $contractor_allocation->product_id,
//                            "code"       => $packing_form_item->lot_number_code
//                        ] )->first();
//
//                        if ( ! $lot_number ) {
//                            $lot_number = LotNumber::create( [
//                                "product_id" => $contractor_allocation->product_id,
//                                "code"       => $packing_form_item->lot_number_code,
//                                "user_id"    => Auth::id(),
//                            ] );
//                        }
//
//
//                        PackingFormItem::create( [
//                            "packing_form_id" => $sub_packing_form->id,
//                            "final_amount"    => $packing_form_item->amount,
//                            "sub_amount"      => $packing_form_item->sub_amount,
////                    "sub_amount2"      =>$sub_packing->sub_amount2,
//                            "status_id"       => $sub_packing_form->status_id,
//                            "product_id"      => $contractor_allocation->product_id,
//                            "degree_id"       => $packing_form_item->degree_id,
//                            "lot_number_id"   => $lot_number->id,
//                            "band_code"       => 1,
//                        ] );
//                    }
//                }
            } else {

                $packing_form_item_list = ImportContractorPackingForm::
                where("machine_allocation_id", $contractor_allocation->id)->
                where("parent_packing_form_row", $packing_row->parent_packing_form_row)->
                orderBy("id")->
                get();
                foreach ($packing_form_item_list as $packing_form_item) {
                    //لات
                    $lot_number = LotNumber::where([
                        "product_id" => $contractor_allocation->product_id,
                        "code" => $packing_form_item->lot_number_code
                    ])->first();

                    if (!$lot_number) {
                        $lot_number = LotNumber::create([
                            "product_id" => $contractor_allocation->product_id,
                            "code" => $packing_form_item->lot_number_code,
                            "user_id" => Auth::id(),
                        ]);
                    }


                    PackingFormItem::create([
                        "packing_form_id" => $packing_form->id,
                        "final_amount" => $packing_form_item->amount,
                        "sub_amount" => $packing_form_item->sub_amount,
                        "init_sub_amount" => $packing_form_item->sub_amount,
                        "status_id" => $packing_form->status_id,
                        "product_id" => $contractor_allocation->product_id,
                        "degree_id" => $packing_form_item->degree_id,
                        "lot_number_id" => $lot_number->id,
                        "band_code" => 1,
                    ]);
                }
            }
        }


        return redirect()->route("contractor.panel.register_production.index", $contractor_allocation)->with(["success" => "آپلود با موفقیت انجام شد، در صورت تایید، بر روی دکمه ثبت نهایی تولید کلیک نمایید."]);


    }


    public function checkPermission(ContractorAllocation $contractor_allocation)
    {

        $result = DashboardController::checkPermissionConditions($contractor_allocation, RegisterProductionController::$info);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }

        return "";
    }

}
