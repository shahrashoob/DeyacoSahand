<?php

namespace App\Http\Controllers\Warehouse\Out;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Sales\ConfirmationOfDemandsFormController;
use App\Http\Controllers\Sales\ConfirmationOfDraftFormController;
use App\Http\Controllers\Sales\ConfirmationOfFinancialUnitController;
use App\Models\Form\Form;
use App\Models\Form\FormItem;
use App\Models\LineProduct\Machine\MachineAllocation;
use App\Models\LineProduct\Packing\PackingTypeLabelPrintingType;
use App\Models\LineProduct\Product\ConsumedProduct\ConsumedProduct;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestForm;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormForm;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormItem;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormLog;
use App\Models\Supplier\Supplier;
use App\Models\Utility\Pdf;
use App\Models\Utility\Printer\Printer;
use App\Models\Utility\Printer\PrinterFile;
use App\Models\Utility\Setting;
use App\Models\Worker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\F;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExitFormController extends Controller
{
    //  warehouse/out/exit_form
    public static $view_path = "warehouse.out.exit_form.";
    public static $route_path = "wh.out.exit_form.";

    public function DCEF_QR(Form $form, $key,$route_back="")
    {
        //  return $form;
        if ($form->random != $key) {
            return back()->withErrors("لینک فرم خروج از انبار معتبر نمی باشد.");
        }

        $form_item = $form->item()->first();
        if (!$form_item) {
            return back()->withErrors("هیچ ردیفی برای فرم خروج وجود ندارد، لطفا با پشتیبانی تماس بگیرید.");
        }
        switch ($route_back) {
            case "wh.out.dashboard.index":
                $route_back = "wh.out.dashboard.index";
                break;
            default:
                $route_back = "dashboard";
        }

        return view(ExitFormController::$view_path . "qr.index", compact("form","route_back"));
    }

    public function submit_QR(Request $request, Form $form)
    {

        $post_user = Auth::user()->posts->first();

        $confirm_type = $request->confirm_type;

        if ($confirm_type == "confirm") { // تایید فرم
            switch ($form->status_id) {

                case 500000514:  //تایید وصول مطالبات (واحد فروش)

                    $form_item = $form->item()->first();
                    $product_request_form = $form_item->product_request_form_item->product_request_form;

                    switch ($product_request_form->applicant_type_id) {
                        case 30: // مشتری
                            if (!$product_request_form->order) {
                                return back()->withErrors("سفارش مربوط به فرم خروج یافت نشد، لطفا با پشتیبانی تماس بگیرید.");
                            }
                            $controller = new ConfirmationOfDemandsFormController();

                            return $controller->confirm_exist_form($request, $product_request_form->order, $form);
                            break;
                    }


                    break;

                case 500000515:  //تایید پیش نویس فرم (واحد مالی)

                    $form_item = $form->item()->first();
                    $product_request_form = $form_item->product_request_form_item->product_request_form;

                    switch ($product_request_form->applicant_type_id) {
                        case 20: // پیمانکار

                            $controller = new \App\Http\Controllers\Contractor\Admin\ConfirmationOfDraftFormController();
                            $machine_allocation = MachineAllocation::where("allocation_id", $product_request_form->allocation_id)->first();
                            if (!$machine_allocation) {
                                return back()->withErrors("تخصیص پیمانکار مرتبط با فرم یافت نشد، لطفا با پشتیبانی تماس بگیرید.");
                            }

                            return $controller->confirm_exist_form($request, $machine_allocation, $form);

                            break;
                        case 30: // مشتری
                            if (!$product_request_form->order) {
                                return back()->withErrors("سفارش مربوط به فرم خروج یافت نشد، لطفا با پشتیبانی تماس بگیرید.");
                            }
                            $controller = new ConfirmationOfDraftFormController();

                            return $controller->confirm_exist_form($request, $product_request_form->order, $form);
                            break;

                        case 60: // تامین کننده (قرض)

                            $controller = new \App\Http\Controllers\Supplier\Admin\ConfirmationOfDraftFormController();
                            $machine_allocation = MachineAllocation::where("allocation_id", $product_request_form->allocation_id)->first();
                            if (!$machine_allocation) {
                                return back()->withErrors("تخصیص تامین کننده مرتبط با فرم یافت نشد، لطفا با پشتیبانی تماس بگیرید.");
                            }

                            return $controller->confirm_exist_form($request, $machine_allocation->supplier_id, $form, $product_request_form->applicant_type_id);

                            break;
                        case 70: // تامین کننده - برگشت از خرید
                            $controller = new \App\Http\Controllers\Supplier\Admin\ConfirmationOfDraftFormController();
                            $supplier = Supplier::where("id", $product_request_form->applicant_id)->first();
                            if (!$supplier) {
                                return back()->withErrors(" تامین کننده مرتبط با فرم یافت نشد، لطفا با پشتیبانی تماس بگیرید.");
                            }

                            return $controller->confirm_exist_form($request, $supplier->id, $form, $product_request_form->applicant_type_id);

                            break;
                    }


                    break;

                case 500000520:  //تایید واحد مالی

                    $form_item = $form->item()->first();
                    $product_request_form = $form_item->product_request_form_item->product_request_form;

                    switch ($product_request_form->applicant_type_id) {
                        case 20: // پیمانکار

                            $controller = new \App\Http\Controllers\Contractor\Admin\ConfirmationOfFinancialUnitController();
                            $machine_allocation = MachineAllocation::where("allocation_id", $product_request_form->allocation_id)->first();
                            if (!$machine_allocation) {
                                return back()->withErrors("تخصیص پیمانکار مرتبط با فرم یافت نشد، لطفا با پشتیبانی تماس بگیرید.");
                            }

                            return $controller->confirm_exist_form($request, $machine_allocation, $form);

                            break;
                        case 30: // مشتری

                            if (!$product_request_form->order) {
                                return back()->withErrors("سفارش مربوط به فرم خروج یافت نشد، لطفا با پشتیبانی تماس بگیرید.");
                            }
                            $controller = new ConfirmationOfFinancialUnitController();

                            return $controller->confirm_exist_form($request, $product_request_form->order, $form);
                        case 60: // تامین کننده (قرض)

                            $controller = new \App\Http\Controllers\Supplier\Admin\ConfirmationOfFinancialUnitController();
                            $machine_allocation = MachineAllocation::where("allocation_id", $product_request_form->allocation_id)->first();
                            if (!$machine_allocation) {
                                return back()->withErrors("تخصیص پیمانکار مرتبط با فرم یافت نشد، لطفا با پشتیبانی تماس بگیرید.");
                            }

                            return $controller->confirm_exist_form($request, $machine_allocation, $form);

                            break;

                        case 70: // تامین کننده - برگشت از خرید
                            $controller = new \App\Http\Controllers\Supplier\Admin\ConfirmationOfFinancialUnitController();
                            $supplier = Supplier::where("id", $product_request_form->applicant_id)->first();
                            if (!$supplier) {
                                return back()->withErrors(" تامین کننده مرتبط با فرم یافت نشد، لطفا با پشتیبانی تماس بگیرید.");
                            }

                            return $controller->confirm_exist_form($request, $supplier->id, $form, $product_request_form->applicant_type_id);

                            break;

                    }


                    break;

                case 500000525:  //تایید بارگیری

                    $controllerLoading = new \App\Http\Controllers\Utility\Transport\Loading\DashboardController();

                    return $controllerLoading->confirm_exist_form($request, $form);
                    break;

                case 500000530: // در انتظار تایید خروج توسط نگهبانی

                    $controllerGarding = new \App\Http\Controllers\Garding\DashboardController();

                    return $controllerGarding->confirm_exist_form($request, $form);
                    break;

                case 500000535: // کنترل کیفیت
                    $controllerQuality = new \App\Http\Controllers\QualityControl\OutputForm\ConfirmQualityController();

                    return $controllerQuality->confirm_exist_form($request, $form);
                    break;

                case 500000500: // تایید دریافت کالا

                    if (!$form->allow_confirmation_according_applicant()) {
                        return back()->withErrors("شما به عملیات مورد نظر دسترسی ندارید");
                    }
                    // تایید درخواست دهنده
                    $result = self::ConfirmApplicant($form);
                    if ($result["result"]) {
                        return back()->with(["success" => "برگه خروج از انبار با موفقیت تایید شد."]);
                    } else {
                        return back()->withErrors($result["error"]);
                    }
                    break;
            }
        } elseif ($confirm_type == "reject") { // عدم تایید فرم
            $form_item = $form->item()->first();
            $product_request_form = $form_item->product_request_form_item->product_request_form;
            if ($product_request_form) {
                $result = $product_request_form->rejectRequest($form);
                // در این تابع وضعیت جدید فرم ثبت می شود.

                if ($result["result"]) {
                    return back()->with(["success" => "عدم تایید برگ خروج با موفقیت ثبت گردید."]);
                } else {
                    return back()->withErrors($result["error"]);
                }
            }
        }

        return back()->withErrors("تایید فرم با خطایی مواجه شده است، لطفا با پشتیبانی تماس بگیرید.");

    }

    /**
     * @param Form $form
     * @return array
     * تایید درخواست دهنده فرم
     */
    public static function ConfirmApplicant(Form $form, $message = "")
    {
        $form_item = $form->item()->first();
        $product_request_form = $form_item->product_request_form_item->product_request_form;

        // در صورت مجاز بودن فرم تایید و تراکنش انبار ثبت شود.
        $result = $product_request_form->checkIfValidConfirmRequest($form->id, $message);

        // در این تابع وضعیت جدید فرم ثبت می شود.
        $product_request_form->updateExistFormStatusForm($form, 7005002); // تایید دریافت کالا

        return $result;

    }

    public function show_output_packing_form(Form $form, $key)
    {
        //  return $form;
        if ($form->random != $key) {
            return back()->withErrors("لینک فرم خروج از انبار معتبر نمی باشد.");
        }

        $form_item = $form->item()->first();
        if (!$form_item) {
            return back()->withErrors("هیچ ردیفی برای فرم خروج وجود ندارد، لطفا با پشتیبانی تماس بگیرید.");
        }

        return view(ExitFormController::$view_path . "qr.show_output_packing_form", compact("form"));
    }

    public function download(ProductRequestForm $product_request_form, Form $form, PackingTypeLabelPrintingType $packing_type_label_printing_type, $print_type = "product")
    {


        $result = DashboardController::check_permission($product_request_form);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }
        $exists = ProductRequestFormForm::
        where("form_id", $form->id)->
        where("product_request_form_id", $product_request_form->id)->
        first();
        if (!$exists) {
            return back()->withErrors("اطلاعات فرم خروج نادرست است.");
        }
        $worker = Worker::find(Auth::user()->id);
        if (!$worker->default_printer_id) {
            return redirect()->route("utility.printer.select_default_printer")->withErrors("لطفا پرینتر پیش فرض را انتخاب نمایید.");
        }


        $result = $this->create_pdf_file($form, $worker, "download", $packing_type_label_printing_type, 1, $print_type);

        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }

        Pdf::labelPrinter($result["html"],
            $packing_type_label_printing_type->orientation,
            $form->code, [
                $packing_type_label_printing_type->width,
                $packing_type_label_printing_type->long
            ]
        );


    }

    public function print(ProductRequestForm $product_request_form, Form $form, PackingTypeLabelPrintingType $packing_type_label_printing_type, $print_number = 1, $print_type = "product_and_packing_form")
    {


        $result = DashboardController::check_permission($product_request_form);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }
        $exists = ProductRequestFormForm::
        where("form_id", $form->id)->
        where("product_request_form_id", $product_request_form->id)->
        first();
        if (!$exists) {
            return back()->withErrors("اطلاعات فرم خروج نادرست است.");
        }
        $worker = Worker::find(Auth::user()->id);
        if (!$worker->default_printer_id) {
            return redirect()->route("utility.printer.select_default_printer")->withErrors("لطفا پرینتر پیش فرض را انتخاب نمایید.");
        }
        if (!$packing_type_label_printing_type) {
            $packing_type_label_printing_type = PackingTypeLabelPrintingType::find(1);
        }

        $printer = $packing_type_label_printing_type->getPrinter($worker);

        $result = $this->create_pdf_file($form, $worker, "print", $packing_type_label_printing_type, $print_number, $print_type);

        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }
        if ($packing_type_label_printing_type->size != "") {

            Pdf::createAsHtml($result["html"], $packing_type_label_printing_type->orientation, $form->code, $packing_type_label_printing_type->size, "", $result["print_file"]);
        } else {

            Pdf::labelPrinter($result["html"],
                $packing_type_label_printing_type->orientation,
                $form->code, [
                    $packing_type_label_printing_type->width,
                    $packing_type_label_printing_type->long
                ],
                $result["print_file"]
            );
        }

        return back()->with(["success" => "جهت دریافت لیبل پرینت شده، به محل پرینتر " . $printer->caption . " مراجعه فرمایید."]);


    }

// پرینت برگ خروج بدون بسته بندی
    public function download_with_out_request_form(Form $form, PackingTypeLabelPrintingType $packing_type_label_printing_type, $print_type = "product")
    {


        $worker = Worker::find(Auth::user()->id);
        if (!$worker->default_printer_id) {
            return redirect()->route("utility.printer.select_default_printer")->withErrors("لطفا پرینتر پیش فرض را انتخاب نمایید.");
        }


        $result = $this->create_pdf_file($form, $worker, "download", $packing_type_label_printing_type, 1, $print_type);

        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }

        Pdf::labelPrinter($result["html"],
            $packing_type_label_printing_type->orientation,
            $form->code, [
                $packing_type_label_printing_type->width,
                $packing_type_label_printing_type->long
            ]
        );


    }

    public function print_with_out_request_form(Form $form, PackingTypeLabelPrintingType $packing_type_label_printing_type, $print_number = 1, $print_type = "product_and_packing_form")
    {


        $worker = Worker::find(Auth::user()->id);
        if (!$worker->default_printer_id) {
            return redirect()->route("utility.printer.select_default_printer")->withErrors("لطفا پرینتر پیش فرض را انتخاب نمایید.");
        }
        if (!$packing_type_label_printing_type) {
            $packing_type_label_printing_type = PackingTypeLabelPrintingType::find(1);
        }

        $printer = $packing_type_label_printing_type->getPrinter($worker);

        $result = $this->create_pdf_file($form, $worker, "print", $packing_type_label_printing_type, $print_number, $print_type);

        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }
        if ($packing_type_label_printing_type->size != "") {

            Pdf::createAsHtml($result["html"], $packing_type_label_printing_type->orientation, $form->code, $packing_type_label_printing_type->size, "", $result["print_file"]);
        } else {

            Pdf::labelPrinter($result["html"],
                $packing_type_label_printing_type->orientation,
                $form->code, [
                    $packing_type_label_printing_type->width,
                    $packing_type_label_printing_type->long
                ],
                $result["print_file"]
            );
        }

        return back()->with(["success" => "جهت دریافت لیبل پرینت شده، به محل پرینتر " . $printer->caption . " مراجعه فرمایید."]);


    }

    public static function create_pdf_file(Form $form, $worker, $type, $packing_type_label_printing_type, $number_of_prints = 1, $print_type = "product_and_packing_form")
    {

        $static_ip = "https://deyaco.ir/" . env("APP_NAME");
        $local_ip = url("");

        $url = route("DCEF_QR", [$form, $form->getRandom()]);

        $url = \Illuminate\Support\Str::replace($local_ip, $static_ip, $url);

        $form_item = $form->item()->first();
        if (!$form_item) {
            return ["html" => "", "print_file" => null];
        }

        //محاسبه تعداد بسته بندی
        $product_packing_form_count = FormItem::
        join("packing_form_item", "packing_form_item_id", "packing_form_item.id")->
        groupBy("form_item.product_id")->
        groupBy("form_item.degree_id")->
        selectRaw("count(distinct(packing_form_id)) as packing_form_count,form_item.product_id,form_item.degree_id")->
        where("form_id", $form->id)->
        get()->keyBy(function ($item) {
            return $item->product_id . "_" . $item->degree_id;
        });

        $packing_form = $form_item->packing_form_item->packing_form??null;
        $product_request_form = $form_item->product_request_form_item->product_request_form??null;

        if($print_type!="product" && (count($product_packing_form_count) == 0 || !$packing_form)){
            return [
                "result" => false,
                "error" => "با توجه به اینکه نوع انبارش کالاهای داخل برگ خروج از نوع بسته بندی نمی باشد، امکان دریافت برگ خروج به تفکیک بسته بندی وجود ندارد، لطفا برگ خروج به تفکیک کالا را دریافت کنید."
            ];
        }

        // به دست آوردن لیست رویدادهای فرم، به صورتی که ایدی هر رویداد فقط یک بار در این لیست وجود دارد و با همان آی دی هم فراخوانی می شوند.
        $product_request_form_logs_list = ProductRequestFormLog::where(
            [
                "product_request_form_id" => $product_request_form->id??0,
                "form_id" => $form->id
            ])->
        orderBy("id")->
        get();
        $product_request_form_logs=[];
        foreach ($product_request_form_logs_list as $item) {
            $product_request_form_logs[$item->event_id] = $item;
        }

        $sum_amount = $form->item()->sum("amount");
        $sum_sub_amount = $form->item()->sum("sub_amount");


        $qr = QrCode::size(100)->generate($url);

        $view_path = ExitFormController::$view_path . "print.template" . $packing_type_label_printing_type->id;
        $software_name = Setting::getStringValue("software_name");

        $page_number = 0;



        if ($print_type == "product_and_packing_form" || $print_type == "product") {
            // Page 1
            $html[$page_number] = view(ExitFormController::$view_path . "print._head")->render();
            $html[$page_number] .= view($view_path . "._print_info_page1",
                    compact("product_packing_form_count", "product_request_form_logs", "product_request_form", "form", "sum_sub_amount", "sum_amount", "packing_form", "qr", "software_name"))->render() . $html[$page_number];
            $html[$page_number] .= view(ExitFormController::$view_path . "print._footer")->render();
            $page_number++;
        }
        if ($print_type == "product_and_packing_form" || $print_type == "packing_form") {

            if ($form->item()->count() > 350) {
                return [
                    "result" => false,
                    "error" => "با توجه به اینکه تعداد ردیف های بسته بندی بیش از حد مجاز است، امکان پرینت برگ خروج به تفکیک بسته بندی وجود ندارد."
                ];
            }
            // Page 2
            if (view()->exists($view_path . "._print_info_page2")) {
                $html[$page_number] = view(ExitFormController::$view_path . "print._head")->render();
                $html[$page_number] .= view($view_path . "._print_info_page2",
                        compact("product_request_form_logs", "product_request_form", "form", "sum_sub_amount", "sum_amount", "packing_form", "qr", "software_name"))->render() . $html[$page_number];
                $html[$page_number] .= view(ExitFormController::$view_path . "print._footer")->render();
                $page_number++;
            }
        }
        $printer = $packing_type_label_printing_type->getPrinter($worker);
        $print_file = null;
        if ($type != "download") {
            $print_file = PrinterFile::create([
                "user_id" => $worker->id,
                "filename" => $form->code . ".pdf",
                "status_id" => 305001, // در انتظار دانلود
                "is_landscape" => 0,
                "printer_id" => $printer->id,
                "number_of_prints" => $number_of_prints
            ]);

        }

        return ["result" => true, "html" => $html, "print_file" => $print_file];
    }
}
