<?php

namespace App\Http\Controllers\Report;

use App\Exports\Report1003_1Export;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Utility\Script\Script1021Controller;
use App\Models\LineProduct\GoodsKind\GoodsKindClassification;
use App\Models\LineProduct\GoodsKind\GoodsKindClassificationOption;
use App\Models\LineProduct\GoodsKind\GoodsKindPost;
use App\Models\LineProduct\Product;
use App\Models\Order\TransKind;
use App\Models\Utility\Option;
use App\Models\Utility\QueueOfLargeOperation;
use App\Models\Warehouse\Warehouse;
use App\Models\Warehouse\WarehouseProduct;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class Report1003Controller extends Controller
{
    //
    var $view_path = "report/1003/";
    var $route_path = "report.1003.";

    public function index()
    {

        $warehouse_list = Warehouse::getAllowedWarehouse();
        $warehouse_option = Option::get("warehouse", 0, $warehouse_list);
        $product_option = Option::get("product_all");


        // فایل هایی که قبلا دانلود کرده است را حذف می کنیم.
        $list_large_operation_for_delete = QueueOfLargeOperation::where([
            "status_id" => 3500002,
            "large_operation_type_id" => 400,
            "result" => Auth::id()
        ])->
        where("created_at", "<", Carbon::now()->addDay(-1))->
        get();
        foreach ($list_large_operation_for_delete as $item) {

            $data = json_decode($item->data);
            $filePath = 'report1003/' . $data->file_name; // Replace this with the actual path to your file

            // Check if the file exists
            if (Storage::exists($filePath)) {
                Storage::delete($filePath); // Delete the file
            }

            $item->delete();
        }


        $list_large_operation = QueueOfLargeOperation::where([
            "status_id" => 3500002,
            "large_operation_type_id" => 400,
            "result" => Auth::id()
        ])->get();

        $allowed_goods_kind_ids = GoodsKindPost::getAllowedGoodsKindId();
        $goods_kind_option = Option::get("goods_kind", 0, 0, $allowed_goods_kind_ids);
        $classification_option = Option::get("goods_kind_classification_group_option", 0, 0);

        return view($this->view_path . "index", compact("warehouse_option", "product_option", "list_large_operation", "goods_kind_option", "classification_option"));

    }

    public function submit_form(Request $request)
    {


        if (!isset($request->start_date) || !isset($request->end_date)) {
            return back()->withErrors("لطفا تاریخ شروع/تاریخ پایان را به درستی انتخاب نمایید.");
        }
        $start_date = Carbon::parse($request->start_date);
        $end_date = Carbon::parse($request->end_date)->addDay();


        $rows_of_report_count = Report1003Controller::getQuery($request)->
        where("warehouse_product.created_at", ">=", $start_date)->
        where("warehouse_product.created_at", "<", $end_date)->
        selectRaw("count(warehouse_product.id) as count")->pluck("count");

        if (count($rows_of_report_count) > 1000) {
            session(["report_1003_request" => $request->all()]);

            return redirect()->route("report.1003.download_file");
        }

        $export = self::GetExport($request);
        return Excel::download($export, 'warehouse_' . jdate(Carbon::now()->timestamp)->format('Y_m_d') . '.xlsx');

    }

    public static function GetExport($request, $queueOfLargeOperation = null)
    {
        $count_of_calculated = 0;

        $show_zero_inventory = isset($request->show_zero_inventory);
        $show_input_transaction = isset($request->show_zero_input);
        $show_output_transaction = isset($request->show_zero_output);
        $breaking_by_lot_number = isset($request->breaking_by_lot_number);
        $breaking_by_degree = isset($request->breaking_by_degree);
        $breaking_by_packing_item = isset($request->breaking_by_packing_item);
        $breaking_by_transaction = isset($request->breaking_by_transaction);

        $classification_id = $request->classification_id;
        $breaking_by_classification = isset($request->breaking_by_classification);
        $goods_kind_id = $request->goods_kind_id;

        $trans_kind_list = TransKind::get()->keyBy("id");

        $start_date = Carbon::parse($request->start_date);
        $end_date = Carbon::parse($request->end_date)->addDay();

        $query = Report1003Controller::getQuery($request)->
        orderBy("packing_form_item_id");

        $rows_of_report = Report1003Controller::getQuery($request)->
        where("warehouse_product.created_at", ">=", $start_date)->
        where("warehouse_product.created_at", "<", $end_date)->
        addSelect(DB::raw("warehouse_product.id,warehouse_product.created_at,trans_kind,ic,form_item_id,form_id,product_id,lot_number_id ,degree_id, carrier_id ,packing_type_id,packing_form_item_id,warehouse_id"))->
        with("product", "degree", "carrier", "packing_type", "packing_form_item")->
        get();


        $inventory = Report1003Controller::getQuery($request)->
        where("warehouse_product.created_at", "<", $start_date)->
        addSelect(DB::raw("warehouse_product.id, sum(input) - sum(output) as value,product_id,lot_number_id ,degree_id, carrier_id ,packing_type_id,packing_form_item_id,warehouse_id"))->
        when(!$show_zero_inventory, function ($query) {
            $query->having("value", "!=", 0);
        })->
        get();


        $input = Report1003Controller::getQuery($request)->
        where("warehouse_product.created_at", ">=", $start_date)->
        where("warehouse_product.created_at", "<", $end_date)->
        addSelect(DB::raw("warehouse_product.id, sum(input) as value,sum(sub_input) as sub_value,product_id,lot_number_id ,degree_id, carrier_id ,packing_type_id,packing_form_item_id,warehouse_id"))->
        get();

        $output = $query->
        where("warehouse_product.created_at", ">=", $start_date)->
        where("warehouse_product.created_at", "<", $end_date)->
        addSelect(DB::raw("warehouse_product.id, sum(output) as value,sum(sub_output) as sub_value,product_id,lot_number_id ,degree_id, carrier_id ,packing_type_id,packing_form_item_id,warehouse_id"))->
        get();

        $goods_kind_classification_caption = [];
        $goods_kind_classification = [];
        if ($breaking_by_classification) {
            $goods_kind_classification = $query->
            join("goods_kind_classification_product", "goods_kind_classification_product.product_id", "warehouse_product.product_id")->
            where("warehouse_product.created_at", ">=", $start_date)->
            where("goods_kind_classification_id", $classification_id)->
            where("warehouse_product.created_at", "<", $end_date)->
            select("goods_kind_classification_product.product_id", "goods_kind_classification_option_id")->
            pluck("goods_kind_classification_option_id", "product_id")->toArray();


            $goods_kind_classification_caption = GoodsKindClassificationOption::where("goods_kind_classification_id", $classification_id)->pluck("caption", "id")->toArray();
        }


        if ($queueOfLargeOperation) {
            $queueOfLargeOperation->result = "step1";
            $queueOfLargeOperation->save();
        }
        $count_of_calculated++;
        $values = [];

        foreach ($rows_of_report as $item) {
            $key = Report1003Controller::getKey($item, $request);
            $values[$key]["inventory"] = "0";
            $values[$key]["sub_inventory"] = "0";
            $values[$key]["input"] = "0";
            $values[$key]["output"] = "0";
            $values[$key]["sub_input"] = "0";
            $values[$key]["sub_output"] = "0";
            $values[$key]["product"] = $item->product;
            $values[$key]["lot_number"] = $item->lot_number;
            $values[$key]["degree"] = $item->degree;
            $values[$key]["carrier"] = $item->carrier;
            $values[$key]["packing_type"] = $item->packing_type;
            $values[$key]["packing_form_item"] = $item->packing_form_item;
            $values[$key]["warehouse"] = $item->warehouse;
            $values[$key]["classification_caption"] =
                isset($goods_kind_classification[$item->product_id]) &&
                isset($goods_kind_classification_caption[$goods_kind_classification[$item->product_id]]) ?
                    $goods_kind_classification_caption[$goods_kind_classification[$item->product_id]] : "";

            if ($breaking_by_transaction) {
                $values[$key]["created_at"] = jdate(Carbon::parse($item->created_at)->timestamp)->format('Y/m/d H:i ');
                $values[$key]["ic"] = $item->ic;
                $values[$key]["trans_kind"] = $trans_kind_list[$item->trans_kind]->caption;
                $values[$key]["product_request_form_code"] = $item->form_item->product_request_form_item->product_request_form->code ?? "";
                $values[$key]["form_code"] = $item->form->code ?? "";
            }

            $count_of_calculated++;
            if ($count_of_calculated % 1000 == 0 && $queueOfLargeOperation) {
                $queueOfLargeOperation->result = "step2:" . $count_of_calculated;
                $queueOfLargeOperation->save();
            }

        }

        $count_of_calculated = 0;
        foreach ($inventory as $item) {
            $key = Report1003Controller::getKey($item, $request);

            if (isset($values[$key])) {

                $values[$key]["inventory"] = $item->value;
                $values[$key]["sub_inventory"] = $item->sub_value;

            }
            $count_of_calculated++;
            if ($count_of_calculated % 1000 == 0 && $queueOfLargeOperation) {
                $queueOfLargeOperation->result = "step3:" . $count_of_calculated;;
                $queueOfLargeOperation->save();
            }
        }

        $count_of_calculated = 0;
        foreach ($input as $item) {
            $key = Report1003Controller::getKey($item, $request);

            if (isset($values[$key])) {

                $values[$key]["input"] = $item->value;
                $values[$key]["sub_input"] = $item->sub_value;
                $values[$key]["inventory"] += $item->value;
                $values[$key]["sub_inventory"] += $item->sub_value;

            }
            $count_of_calculated++;
            if ($count_of_calculated % 1000 == 0 && $queueOfLargeOperation) {
                $queueOfLargeOperation->result = "step4:" . $count_of_calculated;;
                $queueOfLargeOperation->save();
            }
        }

        $count_of_calculated = 0;
        foreach ($output as $item) {
            $key = Report1003Controller::getKey($item, $request);
            if (isset($values[$key])) {

                $values[$key]["output"] = $item->value;
                $values[$key]["sub_output"] = $item->sub_value;
                $values[$key]["inventory"] -= $item->value;
                $values[$key]["sub_inventory"] -= $item->sub_value;

            }
            $count_of_calculated++;
            if ($count_of_calculated % 1000 == 0 && $queueOfLargeOperation) {
                $queueOfLargeOperation->result = "step5:" . $count_of_calculated;;
                $queueOfLargeOperation->save();
            }

        }

        // Check Permission
        $count_of_calculated = 0;
        foreach ($values as $key => $item) {
            $remove = false;
            if (!$show_zero_inventory && round($item["inventory"], 7) == 0) {

                $remove = true;
            }
            if ($show_input_transaction && !$show_output_transaction && $item["input"] == 0) {
                $remove = true;
            }
            if (!$show_input_transaction && $show_output_transaction && $item["output"] == 0) {
                $remove = true;
            }
            if ($show_input_transaction && $show_output_transaction && $item["input"] == 0 && $item["output"] == 0) {
                $remove = true;
            }

            if ($remove) {
                unset($values[$key]);
            }
            $count_of_calculated++;
            if ($count_of_calculated % 1000 == 0 && $queueOfLargeOperation) {
                $queueOfLargeOperation->result = "step6:" . $count_of_calculated;;
                $queueOfLargeOperation->save();
            }
        }


        if ($queueOfLargeOperation) {
            $queueOfLargeOperation->result = "step7";
            $queueOfLargeOperation->save();
        }
        $export = new Report1003_1Export();
        $export->values = $values;

        $export->breaking_by_lot_number = $breaking_by_lot_number;
        $export->breaking_by_degree = $breaking_by_degree;
        $export->breaking_by_packing_item = $breaking_by_packing_item;
        $export->breaking_by_transaction = $breaking_by_transaction;
        $export->breaking_by_classification = $breaking_by_classification;
//        $cost_center_list = [];
//        return view('report.1003.export',
//            [
//                "values" => $export->values,
//                "breaking_by_lot_number" => $export->breaking_by_lot_number,
//                "breaking_by_degree" => $export->breaking_by_degree,
//                "breaking_by_packing_item" => $export->breaking_by_packing_item,
//                "breaking_by_transaction" => $export->breaking_by_transaction,
//                "breaking_by_classification" => $export->breaking_by_classification,
//                "cost_center_list" => $cost_center_list,
//            ]);

        return $export;
    }

    public function download_file()
    {

        $report_1003_request = session("report_1003_request");

        if (!$report_1003_request) {
            return back()->withErrors("اطلاعات گزارش به درستی وارد نشده است، لطفا یکبار دیگر اقدام کنید.");
        }

        return view($this->view_path . "download_file");

    }

    public function submit_download_file()
    {

        $report_1003_request = session("report_1003_request");

        if (!$report_1003_request) {
            return redirect()->route($this->route_path . "index")->withErrors("اطلاعات گزارش به درستی وارد نشده است، لطفا یکبار دیگر اقدام کنید.");
        }

        $report_1003_request["user_id"] = Auth::id();
        QueueOfLargeOperation::AddToQueue($report_1003_request, 400);
        session(["report_1003_request" => null]);
        new Script1021Controller();

        return redirect()->route($this->route_path . "index")->with(["success" => "درخواست ایجاد گزارش ارسال شد، لطفا 10 دقیقه دیگر جهت دانلود فایل گزارش به این صفحه مراجعه فرمایید."]);


    }

    public function download_report(QueueOfLargeOperation $queueOfLargeOperation)
    {
        return self::DownloadReport($queueOfLargeOperation,'report1003/');

    }

    public static function DownloadReport(QueueOfLargeOperation $queueOfLargeOperation,$path)
    {
        $data = json_decode($queueOfLargeOperation->data);
        if ($data->user_id != Auth::id() || $queueOfLargeOperation->large_operation_type_id != 400) {
            return back()->withErrors("کد درخواست برای دانلود گزارش نامعتبر است.");
        }
        $filePath =$path  . $data->file_name; // Replace this with the actual path to your file

        // Check if the file exists
        if (Storage::exists($filePath)) {
            return response()->streamDownload(function () use ($filePath) {
                echo Storage::get($filePath);
            }, $queueOfLargeOperation->get_created_at("for_file") . '.xlsx');
        } else {
            // Handle the case where the file does not exist
            return back()->withErrors('فایل مورد نظر یافت نشد.');

        }
    }

    public static function getQuery($request, $type = "has_group_by")
    {

        $from_warehouse_id = $request->from_warehouse_id;
        $to_warehouse_id = $request->to_warehouse_id;
        $from_product_id = $request->from_product_id;
        $to_product_id = $request->to_product_id;
        $breaking_by_transaction = isset($request->breaking_by_transaction);

        $breaking_by_lot_number = isset($request->breaking_by_lot_number);
        $breaking_by_degree = isset($request->breaking_by_degree);
        $breaking_by_packing_item = isset($request->breaking_by_packing_item);

        $breaking_by_classification = isset($request->breaking_by_classification);
        $goods_kind_id = $request->goods_kind_id;


        $query = WarehouseProduct::

        when($breaking_by_classification, function ($query) use ($goods_kind_id) {
            return $query->join("products", "products.id", "warehouse_product.product_id")->
            where("goods_kind_id", $goods_kind_id);
        })->
        when($from_warehouse_id != 0, function ($query) use ($from_warehouse_id) {
            $query->where("warehouse_id", ">=", $from_warehouse_id);
        })->
        when($to_warehouse_id != 0, function ($query) use ($to_warehouse_id) {
            $query->where("warehouse_id", "<=", $to_warehouse_id);
        })->

        when($from_product_id, function ($query) use ($from_product_id) {
            $query->where("warehouse_product.product_id", ">=", $from_product_id);
        })->
        when($to_product_id, function ($query) use ($to_product_id) {
            $query->where("warehouse_product.product_id", "<=", $to_product_id);
        })->
        when($type == "has_group_by", function ($query) use ($breaking_by_lot_number, $breaking_by_packing_item, $breaking_by_degree) {

            return $query->
            when(!$breaking_by_lot_number && !$breaking_by_packing_item, function ($query) {
                $query->groupBy("warehouse_product.product_id");
            })->
            when($breaking_by_lot_number, function ($query) {
                $query->groupBy("lot_number_id");
            })->
            when($breaking_by_degree, function ($query) {
                $query->groupBy("degree_id");
            })->
            when($breaking_by_packing_item, function ($query) {
                $query->groupBy("packing_form_item_id");
            });

        })->

        when($breaking_by_transaction, function ($query) {
            $query->groupBy("warehouse_product.id");
        });


        return $query;
    }

    public static function getKey($item, $request)
    {
        $breaking_by_lot_number = isset($request->breaking_by_lot_number);
        $breaking_by_degree = isset($request->breaking_by_degree);
        $breaking_by_packing_item = isset($request->breaking_by_packing_item);
        $breaking_by_transaction = isset($request->breaking_by_transaction);

        $key = $item->product_id;
        if ($breaking_by_lot_number) {
            $key .= "_1_" . $item->lot_number_id;
        }
        if ($breaking_by_degree) {
            $key .= "_2_" . $item->degree_id;
        }
        if ($breaking_by_packing_item) {
            $key .= "_3_" . $item->packing_form_item_id;
        }
        if ($breaking_by_transaction) {
            $key .= "_4_" . $item->id;
        }

        return $key;
    }


}
