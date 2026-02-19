<?php

namespace App\Models\LineProduct\Machine\RequestRawMaterialAlgorithm;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\GoodsKind;
use App\Models\LineProduct\Machine\CurrentMachineInput;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineAllocation;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Machine\MachineModuleType;
use App\Models\LineProduct\Machine\MachineTypeInputBandGoodsKind;
use App\Models\LineProduct\Product;
use App\Models\Production\ProductionFormItem;
use App\Models\HR\Shift\Shift;
use App\Models\Utility\Script\Script;
use App\Models\Warehouse\Warehouse;
use Carbon\Carbon;

class Algorithm2WithMinMaxCapacity extends Controller
{
    //
    public static function RequestForMachine(Machine $machine, Script $script, $special_goods_kind_ids, $user_id, $emergency_time, $production_type_id, $other_data = [])
    {

        $warehouse_list_ids = [-1];
        $allocation_amount_remaining = [];
        $allocation_amount_remaining_minute = [];
        $degree_id_list = [];
        $goods_kind_id_list = [];
        $allocation_bands = [];
        $contour = 0;

        $log_data = [];
        $log_data["emergency_time"] = $emergency_time;
        $log_data["algorithm"] = "Algorithm2WithMinMaxCapacity";
        $log_data["machine"]["id"] = $machine->id;
        $log_data["machine"]["caption"] = $machine->caption;
        $reserve_allocation_priority = [];


        // محاسبه مقدار باقی مانده کارت تولید جاری
        $current_allocation = $machine->getCurrentAllocation();

        // اگر مقدار تخصیص صفر باشد، یعنی تغییر مقدار تخصیص داده باشند، کارت جاری را در نظر نمی گیریم.
        if ($current_allocation && $current_allocation->getAllocationAmount() <= 0) {
            $current_allocation = null;
        }
        if ($current_allocation) {
            $reserve_allocation_priority[$current_allocation->id] = $current_allocation->priority_number;
        }
        // اگر فقط برای یک تخصیص خاص قرار است که درخواست ارسال شود.
        if ($current_allocation && isset($other_data["allocation_id"]) && $other_data["allocation_id"] != $current_allocation->id) {
            $current_allocation=null;
        }

        $log_data["current_allocation_id"] = $current_allocation->id ?? "";

        if ($current_allocation) {
            $current_first_machine_allocation = $current_allocation->items()->first();
            $current_production = $current_first_machine_allocation->production;
            $log_data["current_production"] = $current_production->serial ?? "";
            $allocation_bands[$current_allocation->id] = $current_allocation->items()->count();
            if (!$current_production) {
                $log_data["message"] = "تخصیص جاری وجود دارد ولی کارت تولید جاری یافت نشد.";
                $log = AlgorithmFunction::log($script, 0, 41003, $contour, $log_data, $user_id, $machine->id);

                return ["result" => false, "log_data" => $log_data, "log" => $log];
            }

            $check_list_machine_log_start = MachineModuleType::getChecklist($machine->machine_type->machine_module_type_id, "start_machine_event_for_production");


            $machine_log_in_start_production = MachineLog::where("machine_id", $machine->id)->
            whereIn("machine_event_type_id", $check_list_machine_log_start)->
            orderByDesc("id")->first();

            $machine_log_last = MachineLog::getLastLogWithContour($machine);


            $amount_of_produced =
                GoodsKind::getAmountFromMachineLog($current_production->product, $machine_log_in_start_production, $machine_log_last)
                * $current_allocation->items()->count();

            // مقدار باقی مانده از کارت تولید جاری با توجه به کنتور ماشین
            $allocation_amount_remaining[$current_allocation->id] = $current_allocation->getAllocationAmount() - $amount_of_produced;
            // مدت زمان تئوری باقی ماده از کارت تولید جاری
            $allocation_amount_remaining_minute[$current_allocation->id] =
                $current_allocation->predict_of_production_time_theory * $allocation_amount_remaining[$current_allocation->id]
                / $current_allocation->getAllocationAmount();

            // محاسبه مقدار کالا از روی مقدار پیک در هر رسته کالایی

            $warehouse_list_ids = array_merge($warehouse_list_ids, AlgorithmFunction::getWarehouseListFromBOM($current_production->product, $machine));

        }

// بررسی کارت های رزور ماشین
        $log_data["reserve_allocation"] = [];

        foreach ($machine->ReserveAllocation()->get() as $reserve_allocation) {

            // اگر فقط برای یک تخصیص خاص قرار است که درخواست ارسال شود.
            if (isset($other_data["allocation_id"]) && $other_data["allocation_id"] != $reserve_allocation->id) {
               continue;
            }
            $reserve_first_machine_allocation = $reserve_allocation->items()->first();
            $reserve_production = $reserve_first_machine_allocation->production;
            // مقدار باقی مانده از کارت تولید رزور با توجه به کنتور ماشین
            $allocation_amount_remaining[$reserve_allocation->id] = $reserve_first_machine_allocation->allocation->getAllocationAmount();
            // مدت زمان تئوری باقی ماده از کارت تولید رزور
            $allocation_amount_remaining_minute[$reserve_allocation->id] = $reserve_allocation->predict_of_production_time_theory;

            $warehouse_list_ids = array_merge($warehouse_list_ids, AlgorithmFunction::getWarehouseListFromBOM($reserve_production->product, $machine));

            $log_data["reserve_allocation"][$reserve_allocation->id] = $reserve_production->serial ?? "";
            $allocation_bands[$reserve_allocation->id] = $reserve_allocation->items()->count();
            $reserve_allocation_priority[$reserve_allocation->id] = $reserve_allocation->priority_number;
        }

        $log_data["allocation_amount_remaining"] = $allocation_amount_remaining;
        $log_data["allocation_amount_remaining_minute"] = $allocation_amount_remaining_minute;
        $log_data["allocation_bands"] = $allocation_bands;

        // محاسبه مدت زمان کاری انبار
        $warehouse_list = Warehouse::whereIn("id", $warehouse_list_ids)->get();
        foreach ($warehouse_list as $warehouse) {
            // اگر شیفت کاری برای انبار تعریف نشده است، خطا بدهد.
            if (!$warehouse->shift) {
                $log_data["message"] = "شیفت کاری برای " . $warehouse->caption . " مشخص نشده است.";
                $log = AlgorithmFunction::log($script, 0, 41003, $contour, $log_data, $user_id, $machine->id);

                return ["result" => false, "log_data" => $log_data, "log" => $log];
            }


            $result = Shift::GetEndTimeOfWork($warehouse->shift, $machine->machine_type->warehouse_max_capacity, null, $emergency_time);
            if ($result["result"]) {
                $warehouse->working_end_datetime = $result["datetime"];
                $warehouse->time_remaining_in_minute = $result["time_remaining_in_minute"];
            } elseif (isset($result["message"])) {
                $log_data["message"] = $result["message"];
                $log = AlgorithmFunction::log($script, 0, 41003, $contour, $log_data, $user_id, $machine->id);

                return ["result" => true, "log_data" => $log_data, "warning" => $log_data["message"], "log" => $log];
            } else {
                $log_data["message"] = $result["error"];
                $log = AlgorithmFunction::log($script, 0, 41003, $contour, $log_data, $user_id, $machine->id);

                return ["result" => false, "log_data" => $log_data, "log" => $log];
            }

            // بررسی نقطه سفارش انبارک
            $result_request_order = Shift::GetEndTimeOfWork($warehouse->shift, $machine->machine_type->warehouse_request_point, null, $emergency_time);
            if ($result_request_order["result"]) {
                $warehouse->warehouse_request_point_in_minute = $result_request_order["time_remaining_in_minute"];
            } elseif (isset($result_request_order["message"])) {
                $log_data["message"] = $result_request_order["message"];
                $log = AlgorithmFunction::log($script, 0, 41003, $contour, $log_data, $user_id, $machine->id);

                return ["result" => true, "log_data" => $log_data, "warning" => $log_data["message"], "log" => $log];
            } else {
                $log_data["message"] = $result_request_order["error"];
                $log = AlgorithmFunction::log($script, 0, 41003, $contour, $log_data, $user_id, $machine->id);

                return ["result" => false, "log_data" => $log_data, "log" => $log];
            }


            $log_data["warehouse"][$warehouse->id]["time_remaining_in_minute"] = $result["time_remaining_in_minute"];
            $log_data["warehouse"][$warehouse->id]["warehouse_max_capacity"] = $machine->machine_type->warehouse_max_capacity;
            $log_data["warehouse"][$warehouse->id]["now_datetime"] = jdate(Carbon::now()->timestamp)->format('H:i Y/m/d ');
            $log_data["warehouse"][$warehouse->id]["end_working_time"] = $result["datetime_persian"];

            $log_data["warehouse"][$warehouse->id]["warehouse_request_point_in_minute"] = $result_request_order["time_remaining_in_minute"];
            $log_data["warehouse"][$warehouse->id]["warehouse_request_point"] = $machine->machine_type->warehouse_request_point;
            $log_data["warehouse"][$warehouse->id]["warehouse_request_point_end_working_time"] = $result_request_order["datetime_persian"];

        }

//return $warehouse_list;
        // محاسبه مقداری از هر کارت که باید درخواست به انبار آن زده شود.
        $allocation_warehouse_request_list = [];
        $allocation_material_list = [];
        $allocation_production_form_item = [];

        // مقدار تولید شده هر تخصیص
        foreach ($warehouse_list as $warehouse) {
            $allocation_warehouse_request_list[$warehouse->id] = [];
            foreach ($allocation_amount_remaining_minute as $allocation_id => $minute) {
                $allocation_production_form_item[$allocation_id] = ProductionFormItem::
                where("allocation_id", $allocation_id)->sum("production_form_item.amount");
            }
        }
        $log_data["allocation_production_form_item"] = $allocation_production_form_item;


        foreach ($warehouse_list as $warehouse) {
            $allocation_warehouse_request_list[$warehouse->id] = [];
            foreach ($allocation_amount_remaining_minute as $allocation_id => $minute) {

                if ($warehouse->time_remaining_in_minute > $minute) {
                    // به اندازه کل تخصیص، درخواست می دهد.
                    $warehouse->time_remaining_in_minute = $warehouse->time_remaining_in_minute - $minute;
                    $production_amount_for_request = $allocation_amount_remaining[$allocation_id];
                } elseif ($warehouse->time_remaining_in_minute <= 0) {
                    // هیچ مقدار برای این تخصیص اختصاص نمی دهد.
                    $production_amount_for_request = 0;
                } else {
                    // نمی تواند به اندازه کل تخصیص درخواست بدهد، بنابراین مقداری که می تواند با توجه به زمان به دست می آورد و درخواست می دهد.
                    // در اینجا مقدار تایم انبار صفر می شود.
                    $production_amount_for_request = $warehouse->time_remaining_in_minute * $allocation_amount_remaining[$allocation_id]
                        / $minute;


                    $warehouse->time_remaining_in_minute = $warehouse->time_remaining_in_minute - $minute; // این مقدار صفر می شود.

                }


                // مقدار P= مقدار تولید شده از قبل + مقداری که در 12 ساعت آینده می تواند تولید کند.
                $production_amount_for_request += $allocation_production_form_item[$allocation_id];

                $allocation_warehouse_request_list[$warehouse->id][$allocation_id] = $production_amount_for_request;

                if ($warehouse->time_remaining_in_minute < 0) {
                    $warehouse->time_remaining_in_minute = 0;
                }
            }
        }

        $log_data["allocation_warehouse_request_list+sum(production_form_item)"] = $allocation_warehouse_request_list;


        // محاسبه مقداری از هر کارت که باید درخواست به انبار آن زده شود.
        $allocation_warehouse_request_list_request_point = [];

        foreach ($warehouse_list as $warehouse) {
            $allocation_warehouse_request_list_request_point[$warehouse->id] = [];
            foreach ($allocation_amount_remaining_minute as $allocation_id => $minute) {

                if ($warehouse->warehouse_request_point_in_minute > $minute) {
                    // به اندازه کل تخصیص، درخواست می دهد.
                    $warehouse->warehouse_request_point_in_minute -= $minute;
                    $production_amount_for_request_request_point = $allocation_amount_remaining[$allocation_id];
                } elseif ($warehouse->warehouse_request_point_in_minute <= 0) {
                    // هیچ مقدار برای این تخصیص اختصاص نمی دهد.
                    $production_amount_for_request_request_point = 0;
                } else {
                    // نمی تواند به اندازه کل تخصیص درخواست بدهد، بنابراین مقداری که می تواند با توجه به زمان به دست می آورد و درخواست می دهد.
                    // در اینجا مقدار تایم انبار صفر می شود.

                    ///  return $minute." ===".$warehouse->warehouse_request_point_in_minute;
                    $production_amount_for_request_request_point = $warehouse->warehouse_request_point_in_minute * $allocation_amount_remaining[$allocation_id]
                        / $minute;
                    $warehouse->warehouse_request_point_in_minute -= $minute; // این مقدار صفر می شود.
                }


                if ($warehouse->warehouse_request_point_in_minute < 0) {
                    $warehouse->warehouse_request_point_in_minute = 0;
                }
                // مقدار P= مقدار تولید شده از قبل + مقداری که در 12 ساعت آینده می تواند تولید کند.
                $production_amount_for_request_request_point += $allocation_production_form_item[$allocation_id];
                $allocation_warehouse_request_list_request_point[$warehouse->id][$allocation_id] = $production_amount_for_request_request_point;
            }
        }

        $log_data["allocation_warehouse_request_list_request_point+sum(production_form_item)"] = $allocation_warehouse_request_list_request_point;

        // محاسبه مقدار مورد نیاز برای درخواست به انبارها
        // محاسبه مقداری از هر کارت در نقطه سفارش
        $warehouse_need_to_request = [];
        $warehouse_need_to_request_point = [];
        $warehouse_material_packing_count = [];
        $material_waste_prediction_list = [];
        $warehouse_need_to_target_warehouse_request = [];
        foreach ($warehouse_list as $warehouse) {
            $warehouse_need_to_request[$warehouse->id] = [];
            $warehouse_need_to_request_point[$warehouse->id] = [];
            $warehouse_need_to_target_warehouse_request[$warehouse->id] = [];

            foreach ($allocation_warehouse_request_list[$warehouse->id] as $allocation_id => $production_amount_for_request) {

                $machine_allocation_list = MachineAllocation::where("allocation_id", $allocation_id)->
                groupBy("production_id")->
                get();

                foreach ($machine_allocation_list as $machine_allocation_item) {
                    $bom = $machine_allocation_item->product->get_first_bom_from_route($machine);
                    if (!$bom) {
                        $log_data["message"] = " BOM برای " . ($machine_allocation_item->product->caption ?? "" . ($machine_allocation_item->product_id ?? "")) . "یافت نشد.";
                        $log = AlgorithmFunction::log($script, 0, 41003, $contour, $log_data, $user_id, $machine->id);

                        return ["result" => false, "log_data" => $log_data, "log" => $log];
                    }

//                $material_ids = CurrentMachineInput::where( "allocation_id", $allocation_id )->
//                distinct( "material_id" )->
//                pluck( "material_id" )->
//                toArray();
                    $current_machine_inputs = CurrentMachineInput::where("allocation_id", $allocation_id)->
                    where("production_id", $machine_allocation_item->production_id)->
                    groupBy("material_id")->
                    groupBy("consume_warehouse_id")->
                    get();


                    foreach ($current_machine_inputs as $current_machine_input) {

                        $material = $current_machine_input->material;
                        // اگر ارسال درخواست در رسته کالایی فعال است، آن را به لیست کالا - رسته کالایی اضافه می کند.
                        if (in_array($material->goods_kind_id, $special_goods_kind_ids)) {


// لیست درجه هایی که می تواند تحویل بگیرد.
                            $bom_item = $bom->
                            items()->
                            where("warehouse_id", $warehouse->id)->
                            where("material_id", $material->id)->first();

                            //مقدار ضریب ضایعات برای ماده اولیه
                            $material_waste_prediction = $bom_item->waste_prediction ?? 0;

                            if ($material_waste_prediction != 0) {
                                $material_waste_prediction_list[$warehouse->id][$allocation_id][$material->id] = $material_waste_prediction;
                            }

                            if (!$bom_item) {

                                // اگر ردیف BOM یافت نشد، احتمالا کالای جایگزین است و باید در جابگزین دنبال آن باشیم.
                                $bom_replace = Product\BOM\BOMReplace::
                                where([
                                    "bill_of_material_id" => $bom->id,
                                    "replace_product_id" => $material->id
                                ])->first();

                                $material_waste_prediction = $bom_replace->waste_prediction ?? 0;

                                if ($material_waste_prediction != 0) {
                                    $material_waste_prediction_list[$warehouse->id][$allocation_id][$material->id] = $material_waste_prediction;
                                }

                                $bom_item = $bom->
                                items()->
                                where("warehouse_id", $warehouse->id)->
                                where("material_id", $bom_replace->material_id ?? 0)->
                                first();

                            }

                            if (!$bom_item) {
                                // چون همه ردیف های ووردی جاری ماشین را به ازای همه انبار ها بررسی می کند، اگر انبار ماده اولیه با انبار در حال بررسی یکی نباشد، نمی تواند ردیف BOM را به دست آورد، بنابراین به  الگوریتم ادامه می دهد.
                                continue;
                            }

                            $material_amount_result = CurrentMachineInput::where(
                                ["allocation_id" => $allocation_id,
                                    "production_id" => $current_machine_input->production_id
                                ]
                            )->
                            where("material_id", $material->id)->
                            selectRaw("sum(amount*number*percent_of_use/100) as amount")->first();
                            if (!$material_amount_result) {

                                $log_data["message"] = "برای کالا با شناسه:" . $material->id . " هیچ رکوردی در ورودی های ماشین یافت نشد.";
                                $log = AlgorithmFunction::log($script, 0, 41003, $contour, $log_data, $user_id, $machine->id);

                                return ["result" => false, "log_data" => $log_data, "log" => $log];
                            }
                            $material_amount = $material_amount_result->amount;
                            // مقدار ظرفیت
                            if (!isset($warehouse_need_to_request[$warehouse->id][$material->id])) {
                                $warehouse_need_to_request[$warehouse->id][$material->id] = 0;
                            }

                            if (!isset($warehouse_need_to_target_warehouse_request[$warehouse->id][$current_machine_input->consume_warehouse_id][$material->id])) {
                                $warehouse_need_to_target_warehouse_request[$warehouse->id][$current_machine_input->consume_warehouse_id][$material->id] = 0;
                            }

                            // به دست آوردن مقدار مصرف
                            $amount_need_for_material_id =
                                $material_amount *

                                $allocation_warehouse_request_list[$warehouse->id][$allocation_id]
                                *
                                (1 + $material_waste_prediction / 100);

                            if ($current_machine_input->production->production_type_id != $production_type_id) {
                                $amount_need_for_material_id = 0;
                            }

                            $warehouse_need_to_request[$warehouse->id][$material->id] += $amount_need_for_material_id;

                            $warehouse_need_to_target_warehouse_request
                            [$warehouse->id][$current_machine_input->consume_warehouse_id][$material->id] +=
                                $amount_need_for_material_id;

                            // به ازای هر تخصیص مقدار مورد نیاز مواد اولیه درخواست شده مشخص می شود.
                            if ($amount_need_for_material_id > 0) {
                                $allocation_material_list[$allocation_id][$material->id] = $amount_need_for_material_id;
                            }

                            // مقدار نقطه سفارش
                            if (!isset($warehouse_need_to_request_point[$warehouse->id][$material->id])) {
                                $warehouse_need_to_request_point[$warehouse->id][$material->id] = 0;
                            }


                            // به دست آوردن مقدار مصرف در پوینت
                            $amount_need_for_material_id_point =
                                $material_amount *

                                $allocation_warehouse_request_list_request_point[$warehouse->id][$allocation_id]

                                *
                                (1 + $material_waste_prediction / 100);

                            if ($current_machine_input->production->production_type_id != $production_type_id) {
                                //  return $current_machine_input->production;
                                $amount_need_for_material_id_point = 0;
                            }

                            $warehouse_need_to_request_point[$warehouse->id][$material->id] +=
                                $amount_need_for_material_id_point;


                            // به دست آوردن تعداد بسته بندی مورد نیاز
                            $material_packing_count = AlgorithmFunction::PackingCount($allocation_id, $material);

                            $warehouse_material_packing_count[$warehouse->id][$current_machine_input->consume_warehouse_id][$material->id] =
                                isset($warehouse_material_packing_count[$warehouse->id][$current_machine_input->consume_warehouse_id][$material->id]) ?
                                    $warehouse_material_packing_count[$warehouse->id][$current_machine_input->consume_warehouse_id][$material->id] : 0;

                            // اگر کارت جاری 2 ورودی ماشین  از کالای material_id است، بنابراین باید حداقل دو بسته بندی درخواست دهیم.
                            $warehouse_material_packing_count[$warehouse->id][$current_machine_input->consume_warehouse_id][$material->id] =
                                max(
                                    $material_packing_count,
                                    $warehouse_material_packing_count[$warehouse->id][$current_machine_input->consume_warehouse_id][$material->id]
                                );
                            if ($current_machine_input->production->production_type_id != $production_type_id) {
                                $warehouse_material_packing_count[$warehouse->id][$current_machine_input->consume_warehouse_id][$material->id] = 0;

                            }

                            $bom_item_degrees = $bom_item->degrees;

                            if (count($bom_item_degrees) > 0) {
                                // اگر قبلا درجه ها برای این کالا مشخص نشده
                                $degree_id_list_material_id = $bom_item->degrees()->pluck("degree_id")->toArray();
                                if (!isset($degree_id_list[$warehouse->id][$material->id])) {
                                    $degree_id_list[$warehouse->id][$material->id] = $degree_id_list_material_id;
                                } else {
                                    // قبلا یک یا چند درجه برای کالا مشخص شده، بنابراین درجه های جدید باید در صورت وجود نداشتن اضافه شوند.
                                    foreach ($degree_id_list_material_id as $degree_id_item_material) {
                                        if (!in_array($degree_id_item_material, $degree_id_list[$warehouse->id][$material->id])) {
                                            $degree_id_list[$warehouse->id][$material->id][] = $degree_id_item_material;
                                        }
                                    }
                                }
                            } else {

                                $log_data["message"] = "درجه برای ردیف BOM کالای " . ($machine_allocation->product->caption ?? "" . ($machine_allocation->product_id ?? "")) . "تعریف نشده است.";
                                $log = AlgorithmFunction::log($script, 0, 41003, $contour, $log_data, $user_id, $machine->id);

                                return ["result" => false, "log_data" => $log_data, "log" => $log];

                            }

                            $goods_kind_id_list[$material->id] = $bom_item->material->goods_kind_id;

                        }

                    }


                }
            }

        }

        // اگر مقدار یک کالا صفر بود، آن ردیف را حذف می کنیم.
        foreach ($warehouse_list as $warehouse) {
            foreach ($allocation_warehouse_request_list[$warehouse->id] as $allocation_id => $production_amount_for_request) {

                $current_machine_inputs = CurrentMachineInput::where("allocation_id", $allocation_id)->
                groupBy("material_id")->
                groupBy("consume_warehouse_id")->
                get();

                foreach ($current_machine_inputs as $current_machine_input) {

                    // اگر مقدار یک کالا صفر بود، آن ردیف را حذف می کنیم.
                    if (isset($warehouse_need_to_request[$warehouse->id][$current_machine_input->material_id]) && $warehouse_need_to_request[$warehouse->id][$current_machine_input->material_id] == 0) {
                        unset($warehouse_need_to_request[$warehouse->id][$current_machine_input->material_id]);
                    }


                    if (isset($warehouse_need_to_target_warehouse_request[$warehouse->id][$current_machine_input->consume_warehouse_id][$current_machine_input->material_id]) &&
                        $warehouse_need_to_target_warehouse_request[$warehouse->id][$current_machine_input->consume_warehouse_id][$current_machine_input->material_id]
                        == 0
                    ) {
                        unset($warehouse_need_to_target_warehouse_request[$warehouse->id][$current_machine_input->consume_warehouse_id][$current_machine_input->material_id]);
                    }
                }
            }
        }

        $log_data["allocation_material_list"] = $allocation_material_list;
        $log_data["material_waste_prediction_list"] = $material_waste_prediction_list;


        // به دست آوردن مقدار تحویل شده به ازای هر تخصیص
        $allocation_material_amount_delivered = [];


        // به ازای هر انبار موجودی کالای مورد درخواست را بررسی می کنیم اگر مقدار موجودی به اندازه درخواست نباشد، ما به تفاوت را درخواست می دهیم.
        foreach ($warehouse_list as $warehouse) {

            foreach ($warehouse_need_to_request[$warehouse->id] as $material_id => $amount_need) {

                //محاسبه مقدار مواد اولیه تحویل شده به ازای همه تخصیص ها
                $allocation_material_amount_delivered[$material_id] =
                    Product\ProductRequest\ProductRequestFromAllocation::
                    whereIn("allocation_id", array_keys($reserve_allocation_priority))->
                    where("material_id", $material_id)->
                    sum("amount_delivered");

                $warehouse_need_to_request[$warehouse->id][$material_id] -= $allocation_material_amount_delivered[$material_id];
                $warehouse_need_to_request_point[$warehouse->id][$material_id] -= $allocation_material_amount_delivered[$material_id];
            }
        }

        $log_data["allocation_material_amount_delivered"] = $allocation_material_amount_delivered;
        $log_data["warehouse_need_to_request-amount_delivered"] = $warehouse_need_to_request;
        $log_data["warehouse_need_to_request_point-amount_delivered"] = $warehouse_need_to_request_point;
        $log_data["warehouse_material_packing_count"] = $warehouse_material_packing_count;


        foreach ($warehouse_list as $warehouse) {
            // اگر جمع مقدار مورد نیاز حداقل یک سطر بزرگتر از 0 است ، پس باید یک درخواست به انبار زده شود.
            foreach ($warehouse_need_to_request_point[$warehouse->id] as $product_id => $item_request_amount) {
                if ($item_request_amount <= 0) {

                    unset($warehouse_need_to_request_point[$warehouse->id][$product_id]);
                    // اگر قرار شده که درخواست ارسال کند، به ازای همه مواردی که کم داشته درخواست می فرستد.
                    if (isset($warehouse_need_to_request[$warehouse->id][$product_id]) && $warehouse_need_to_request[$warehouse->id][$product_id] <= 0) {
                        unset($warehouse_need_to_request[$warehouse->id][$product_id]);
                    }

                }
            }

        }

        // ساختن material_list برای خروجی الگوریتم
        $material_list_for_result = [];
        foreach ($warehouse_need_to_target_warehouse_request as $warehouse_id => $material_consume_warehouse) {
            $material_list_for_result[$warehouse_id] = [];
            foreach ($material_consume_warehouse as $consume_warehouse_id => $material_list) {
                foreach ($material_list as $material_id => $amount) {
                    if (isset($warehouse_need_to_request[$warehouse_id][$material_id])) {
                        $amount_st = $warehouse_need_to_request[$warehouse_id][$material_id];
                        if (!isset($material_list_for_result[$warehouse_id][$consume_warehouse_id][$material_id])) {
                            $material_list_for_result[$warehouse_id][$consume_warehouse_id][$material_id] = 0;
                        }
                        $material_list_for_result[$warehouse_id][$consume_warehouse_id][$material_id] += $amount_st;
                        unset($warehouse_need_to_request[$warehouse_id][$material_id]);
                    }
                }
            }
        }


        $result = [];

        $result["allocation_id"] = isset($other_data["allocation_id"])?$other_data["allocation_id"]:null;
        $result["material_list"] = $material_list_for_result;
        $result["material_list_for_sampling"] = [];
        $result["degree_id_list"] = $degree_id_list;
        $result["warehouse_material_packing_count"] = $warehouse_material_packing_count;
        $result["goods_kind_id_list"] = $goods_kind_id_list;
        $result["reserve_allocation_priority"] = $reserve_allocation_priority;
        $result["log_data"] = $log_data;
        $result["result"] = true;

        return $result;


    }

}
