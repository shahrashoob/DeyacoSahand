<?php

namespace App\Http\Controllers\Utility\Transport\Loading;

use App\Events\Product\ProductRequestFormLogEvent;
use App\Events\Utility\TransportLogEvent;
use App\Events\Warehouse\Form\FormLogEvent;
use App\Http\Controllers\Controller;
use App\Models\Contractor\Contractor;
use App\Models\Form\Form;
use App\Models\Form\FormItem;
use App\Models\Form\Packing\PackingForm;
use App\Models\Form\Packing\PackingFormItem;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormForm;
use App\Models\Utility\Car\Car;
use App\Models\Utility\Option;
use App\Models\Utility\Pdf;
use App\Models\Utility\Transport\Transport;
use App\Models\Utility\Transport\TransportForm;
use App\Models\Utility\Transport\TransportPackingForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoadRegistrationController extends Controller
{
    //
    var $view_path = "utility.transport.loading.load_registration.";
    var $route_path = "utility.transport.loading.load_registration.";
    var $dashboard_path = "utility.transport.loading.dashboard.index";

    public function create(Form $form)
    {

        $list = Form::
        leftjoin("transport_form", "form_id", "forms.id")->
        whereNull("transport_form.id")->
        where("forms.status_id", 500000525)->
        select("forms.*")->
        get();

        $car_type_option = Option::get("car_types");
        $car_list = Car::get();
        $car_option["items"] = [];
        foreach ($car_list as $car) {
            $car_option["items"][] = [
                "id" => $car->id,
                "value" => $car->id,
                "text" => $car->driver_firstname . " " .
                    $car->driver_lastname . "-" .
                    $car->car_type->caption . "-" .
                    $car->car_plaque
            ];
        }


        return view($this->view_path . "create", compact("list", "form", "car_type_option", "car_list", "car_option"));

    }

    public function store(Request $request)
    {

        $forms = $request->form;
        $driver_firstname = $request->driver_firstname;
        $driver_lastname = $request->driver_lastname;
        $driver_mobile = $request->driver_mobile;
        $car_plaque = $request->car_plaque;
        $car_type_id = $request->car_type_id;
        $car_id = $request->car_id;
        if (!$forms) {
            return back()->withErrors("لطفا حداقل یک فرم را انتخاب نمایید.");
        }


        foreach ($forms as $item => $value) {
            $transport_form = TransportForm::where("form_id", $item)->first();
            if ($transport_form) {
                return back()->withErrors("برگ خروج " . ($transport_form->form->code ?? "***") . " قبلا در بار   " . ($transport_form->transport->code ?? "***") . " قرار داده شده است.");
            }
        }

        if ($car_id) {
            $car = Car::find($car_id);
        } else {
            $request["user_id"] = Auth::id();

            if(!$car_type_id){
                return back()->withErrors("لطفا نوع ماشین را انتخاب کنید.");
            }
            if (!$driver_lastname ) {
                return back()->withErrors("لطفا نام راننده را وارد نمایید.");
            }
            if (!$car_plaque ) {
                return back()->withErrors("لطفا  پلاک خودرو را وارد نمایید.");
            }

            $car = Car::create($request->all());
        }

        $transport = Transport::create([
            "car_id" => $car->id,
            "user_id" => Auth::id(),
            "status_id" => 6010103,//در حال بارگیری
        ]);

        event(new TransportLogEvent($transport, 6010101));

        foreach ($forms as $item => $value) {

            TransportForm::create(["transport_id" => $transport->id, "form_id" => $item]);
        }

        return redirect()->route($this->route_path . "show_transport", $transport)->with(["success" => "یک بار با موفقیت ثبت گردید."]);


    }

    public function show_transport(Transport $transport)
    {

        $allow_confirm = $this->allow_confirm($transport);
        $transport = Transport::find($transport->id);
        $packing_form_data = json_decode($transport->packing_form_data, true);

        $packing_form_data2 = $packing_form_data;
        $packing_form_data2[] = -1;
        $transport_packing_form_ids = TransportPackingForm::whereIn("packing_form_id", array_keys($packing_form_data2))->pluck("transport_item_id", "packing_form_id")->toArray();

        $result = Transport::getWeight($transport);

        $forms_ids = $transport->transport_forms()->pluck("form_id")->toArray();
        $packing_form_count = Transport::getPackingFromCount($transport, $forms_ids);

        $weight = $result["weight"];
        $gross_weight = $result["gross_weight"];

        return view($this->view_path . "show_transport", compact("transport", "packing_form_data", "allow_confirm", "weight", "gross_weight", "packing_form_count", "transport_packing_form_ids"));
    }

    public function download_transport_card(Transport $transport, $random, $size = "A5")
    {

        if ($transport->random != $random) {
            return back()->withErrors("اطلاعات بار نادرست است.");
        }
        if (!in_array($size, ["A4", "A5"])) {
            return back()->withErrors("نوع کاغذ جهت ایجاد فایل pdf نامعتبر است.");
        }
        $result = \App\Http\Controllers\Utility\Transport\PrintQRController::create_pdf_file($transport, "download");
        Pdf::createAsHtml($result["html"],
            $size == "A5" ? "L" : "P",
            $transport->id, $size, " "
        );
    }

    public function print_transport_card(Transport $transport, $random, $size = "A5")
    {
        $worker = Auth::user();
        if (!isset($worker->default_printer_id)) {
            return back()->withErrors("لطفا پرینتر پیش فرض را انتخاب نمایید.");
        }
        if ($transport->random != $random) {
            return back()->withErrors("اطلاعات بار نادرست است.");
        }
        if (!in_array($size, ["A4", "A5"])) {
            return back()->withErrors("نوع کاغذ جهت ایجاد فایل pdf نامعتبر است.");
        }
        $result = \App\Http\Controllers\Utility\Transport\PrintQRController::create_pdf_file($transport, "print");
        Pdf::createAsHtml($result["html"],
            $size == "A5" ? "L" : "P",
            $transport->id, $size, " ", $result["print_file"]
        );

        return back()->with(["success" => "پرینت بارنامه به پرینتر پیشفرض ارسال گردید."]);
    }

    public function release_of_exit_form(Transport $transport)
    {

        $packing_form_data = json_decode($transport->packing_form_data, true);
        if (array_sum($packing_form_data) == 0) {
            return back()->withErrors("هیچ بسته بندی جهت بارگیری انتخاب نشده است.");
        }
        $form_packing_form_list = [];
        foreach ($transport->transport_forms as $t_form) {

            $form_packing_form_list[$t_form->form_id] = [];

            // اگر تنظیمات کنترل بارگیری برای انبار T است، لیست بسته بندی های آن اضافه می شود.
            if ($t_form->form->warehouse->cheek_loading_control_for_exist_form == 1) {
                foreach ($t_form->form->getPackingFrom() as $item) {
                    $form_packing_form_list[$t_form->form_id][$item->id] = $packing_form_data[$item->id];
                }
                foreach ($t_form->form->getMasterPackingFrom() as $item) {
                    $form_packing_form_list[$t_form->form_id][$item->id] = $packing_form_data[$item->id];
                }
            }
        }

        return view($this->view_path . "release_of_exit_form", compact("transport", "form_packing_form_list"));
    }

    public function submit_release_of_exit_form(Request $request, Transport $transport)
    {

        $form_status = $request->form;
        foreach ($transport->transport_forms as $t_form) {
            if (!isset($form_status[$t_form->form_id])) {
                return back()->withErrors("روش آزاد سازی بسته بندی های برگ خروج" . $t_form->form->code . " مشخص نشده است.");
            }

            if ($form_status[$t_form->form_id] == "delete") {
                return back()->withErrors("برای حذف برگ خروج از بار، با پشیتیبانی تماس بگیرید.");
            }
        }

        $packing_form_ids_must_be_exit = [];
        $packing_form_data = json_decode($transport->packing_form_data, true);

        if (array_sum($packing_form_data) == 0) {
            return back()->withErrors("هیچ بسته بندی جهت بارگیری انتخاب نشده است.");
        }

        foreach ($packing_form_data as $packing_form_id => $value) {

            if ($value == 0) {
                $packing_form_ids_must_be_exit[] = $packing_form_id;
            }
        }

//return $packing_form_ids_must_be_exit;
        foreach ($transport->transport_forms as $t_form) {

//            $t_form->form->form_edited = 1;
//            $t_form->form->save();

            $form_item_where_must_be_exit_master = FormItem::
            join("packing_form_item", "packing_form_item.id", "packing_form_item_id")->
            join("packing_forms", "packing_forms.id", "packing_form_id")->
            where("form_item.form_id", $t_form->form_id)->
            whereIn("packing_forms.id", $packing_form_ids_must_be_exit)->
            whereNotNull("packing_form_master_id")->
            select("form_item.*")->
            get();

            $form_item_where_must_be_exit = FormItem::
            join("packing_form_item", "packing_form_item.id", "packing_form_item_id")->
            join("packing_forms", "packing_forms.id", "packing_form_id")->
            where("form_item.form_id", $t_form->form_id)->
            whereIn("packing_forms.id", $packing_form_ids_must_be_exit)->
            whereNull("packing_form_master_id")->
            select("form_item.*")->
            get();

            $k = 0;

            // اگر همه بسته بندی های فرم خوانده شده، نیازی نیست کاری انجام شود.
            if (count($form_item_where_must_be_exit_master) + count($form_item_where_must_be_exit) == 0) {
                continue;
            }
            switch ($form_status[$t_form->form_id]) {
                case "release": //آزاد سازی بسته بندی های بارگیری نشده

                    $prff_list = ProductRequestFormForm::where("form_id", $t_form->form_id)->get();

                    $packing_form_cods = "آزاد سازی بسته بندی های ذیل از برگ خروج:" . "<br/>";
                    foreach ($form_item_where_must_be_exit_master as $item) {
                        $packing_form_cods .= $item->packing_form_item->packing_form->code . " ";;
                        $item->packing_form_item->packing_form->warehouse_status_id = 4201;
                        $item->packing_form_item->packing_form->save();

                        $k++;
                        if ($k % 10 == 0) {
                            $packing_form_cods .= "<br/>";
                        }
                    }

                    foreach ($form_item_where_must_be_exit as $item) {
                        $packing_form_cods .= $item->packing_form_item->packing_form->code . " ";;
                        $item->packing_form_item->packing_form->warehouse_status_id = 4201;
                        $item->packing_form_item->packing_form->save();

                        $k++;
                        if ($k % 10 == 0) {
                            $packing_form_cods .= "<br/>";
                        }
                    }

                    event(new FormLogEvent($t_form->form, $packing_form_cods));
                    foreach ($prff_list as $item) {
                        event(new ProductRequestFormLogEvent($item->product_request_form, $packing_form_cods, $t_form->form, 7005017));
                    }


                    $form_item_where_must_be_exit_master = FormItem::
                    join("packing_form_item", "packing_form_item.id", "packing_form_item_id")->
                    join("packing_forms", "packing_forms.id", "packing_form_id")->
                    where("form_item.form_id", $t_form->form_id)->
                    whereIn("packing_forms.id", $packing_form_ids_must_be_exit)->
                    whereNotNull("packing_form_master_id")->
                    delete();

                    $form_item_where_must_be_exit = FormItem::
                    join("packing_form_item", "packing_form_item.id", "packing_form_item_id")->
                    join("packing_forms", "packing_forms.id", "packing_form_id")->
                    where("form_item.form_id", $t_form->form_id)->
                    whereIn("packing_forms.id", $packing_form_ids_must_be_exit)->
                    whereNull("packing_form_master_id")->
                    delete();

                    break;

                case "new_exit_form": // ایجاد برگ خروج جدید

                    $prff_list = ProductRequestFormForm::where("form_id", $t_form->form_id)->get();

                    $new_form = Form::CreateFrom([
                        "order_id" => 0,
                        "order_list_id" => 0,
                        "user_id" => Auth::user()->id,
                        "trans_kind" => $t_form->form->trans_kind,
                        "ic" => $t_form->form->ic,
                        "status_id" => $t_form->form->status_id,
                        "warehouse_id" => $t_form->form->warehouse_id
                    ]);
                    $new_form->getCode("DCEF");// Warehouse Exit Form

                    $packing_form_cods = "حذف بسته بندی های ذیل از برگ خروج و افزودن به برگ خروج :" . $new_form->code . "<br/>";
                    foreach ($form_item_where_must_be_exit_master as $item) {
                        $packing_form_cods .= $item->packing_form_item->packing_form->code . " ";

                        $item->form_id = $new_form->id;
                        $item->save();

                        $k++;
                        if ($k % 10 == 0) {
                            $packing_form_cods .= "<br/>";
                        }
                    }

                    foreach ($form_item_where_must_be_exit as $item) {
                        $packing_form_cods .= $item->packing_form_item->packing_form->code . " ";;

                        $item->form_id = $new_form->id;
                        $item->save();

                        $k++;
                        if ($k % 10 == 0) {
                            $packing_form_cods .= "<br/>";
                        }

                    }

                    event(new FormLogEvent($t_form->form, $packing_form_cods));
                    event(new FormLogEvent($new_form, $packing_form_cods));

                    foreach ($prff_list as $item) {
                        ProductRequestFormForm::create([
                            "product_request_form_id" => $item->product_request_form->id,
                            "form_id" => $new_form->id
                        ]);
                        event(new ProductRequestFormLogEvent($item->product_request_form, $packing_form_cods, $t_form->form_id, 7005017));
                        event(new ProductRequestFormLogEvent($item->product_request_form, "برگ خروج جدید، جدا شده از برگ خروج " . $t_form->form->code, $new_form->id, 7005018));
                    }
                    break;


            }


        }

        $result = LoadRegistrationController::ConfirmLoading($request, $transport);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }
        $transport->status_id = 6010101;// در انتظار خروج از سازمان
        $transport->save();

        event(new TransportLogEvent($transport, 6010102)); //پایان بارگیری

        return redirect()->route($this->dashboard_path)->with(["success" => "ثبت بارگیری با موفقیت انجام شد."]);

    }

    public function confirm_transport(Request $request, Transport $transport)
    {

        // یک باردیگر قبل از ثبت نهایی بسته بندی های خوانده شده را بررسی می کنیم.
        $this->control_packing_api($request);

        // بررسی اینکه همه بسته بندی ها بارگیری شده باشد، در صورت بارگیری همه بسته بندی ها، بار را تایید می کنیم در غیر این صورت به صفحه آزاد سازی بسته بندی ها می رویم.
        $transport = Transport::find($transport->id);

        $packing_form_data = json_decode($transport->packing_form_data, true);
        if (count($packing_form_data) == array_sum($packing_form_data)) {

            $result = LoadRegistrationController::ConfirmLoading($request, $transport);
            if (!$result["result"]) {
                return back()->withErrors($result["error"]);
            }

            $transport->status_id = 6010101;// در انتظار خروج از سازمان
            $transport->save();

            event(new TransportLogEvent($transport, 6010102)); //پایان بارگیری

            return redirect()->route($this->dashboard_path)->with(["success" => "ثبت بارگیری با موفقیت انجام شد."]);
        } else {
            return redirect()->route($this->view_path . "release_of_exit_form", $transport);
        }
    }

    public function show_packing_list(Transport $transport)
    {

        $packing_form_data = json_decode($transport->packing_form_data, true);
        $packing_form_data_ids = $packing_form_data;
        $packing_form_read_ids[] = -1;
        $packing_form_no_read_ids[] = -1;
        $packing_form_all_ids[] = -1;
        foreach ($packing_form_data_ids as $key => $val) {
            if ($val == 1) {
                $packing_form_read_ids[] = $key;
            } else {
                $packing_form_no_read_ids[] = $key;
            }
            $packing_form_all_ids[] = $key;
        }

        $packing_form_list = PackingForm::whereIn("id", $packing_form_read_ids)->orderBy("id")->get();

        $packing_form_product_all = PackingFormItem::
        join("products", "products.id", "product_id")->
        whereIn("packing_form_id", $packing_form_all_ids)->
        groupBy("product_id")->
        selectRaw("products.code,products.caption,products.id, count(distinct(packing_form_id)) as sum_packing_form_read")->
        get()->keyBy("id");

        $packing_form_read = PackingFormItem::
        join("products", "products.id", "product_id")->
        whereIn("packing_form_id", $packing_form_read_ids)->
        groupBy("product_id")->
        selectRaw("products.id, count(distinct(packing_form_id)) as sum_packing_form_read")->
        pluck("sum_packing_form_read", "id")->
        toArray();

        $packing_form_no_read = PackingFormItem::
        join("products", "products.id", "product_id")->
        whereIn("packing_form_id", $packing_form_no_read_ids)->
        groupBy("product_id")->
        selectRaw("products.id, count(distinct(packing_form_id)) as sum_packing_form_read")->
        pluck("sum_packing_form_read", "id")->
        toArray();

        return view($this->view_path . "show_packing_list", compact("packing_form_list", "transport", "packing_form_data", "packing_form_read", "packing_form_no_read", "packing_form_product_all"));
    }

    public function allow_confirm(Transport $transport)
    {

        if (!$transport->packing_form_data) {


            $packing_form_ids = [];

            foreach ($transport->transport_forms as $t_form) {

                // اگر تنظیمات کنترل بارگیری برای انبار T است، لیست بسته بندی های آن اضافه می شود.
                if ($t_form->form->warehouse->cheek_loading_control_for_exist_form == 1) {
                    foreach ($t_form->form->getPackingFrom() as $item) {
                        $packing_form_ids[$item->id] = 0;
                    }
                    foreach ($t_form->form->getMasterPackingFrom() as $item) {
                        $packing_form_ids[$item->id] = 0;
                    }
                }
            }

            $transport->packing_form_data = json_encode($packing_form_ids);
            $transport->save();
        }

        $packing_form_data = json_decode($transport->packing_form_data, true);

        return array_sum($packing_form_data) == count($packing_form_data);
    }

    public static function ConfirmLoading(Request $request, Transport $transport)
    {

        $controllerLoading = new \App\Http\Controllers\Utility\Transport\Loading\DashboardController();
        if (!\Auth::user()->posts->first()->checkButtonPermission("utility.transport.loading.show_form")) {
            return [
                "result" => false,
                "error" => "شما اجازه دسترسی به عملیات مورد نظر را ندارید"
            ];

        }

        $check_call = false;
        foreach ($transport->transport_forms as $t_form) {
            if (!$check_call) {
                // اگر پیمان کار باشد، باید فرم ورود بخورد.
                $result = Transport::CallApiAddInputFormForTransport($transport, $t_form->form);
                if (!$result["result"]) {
                    return $result;
                }
                $check_call = true;
            }

            $result_confirm_exist_form = $controllerLoading->result_confirm_exist_form($request, $t_form->form, true);
            if (!$result_confirm_exist_form["result"]) {
                return $result_confirm_exist_form;
            }
        }

        return [
            "result" => true,
        ];

    }

    public function control_packing_api(Request $request)
    {

        $transport = Transport::find($request->transport_id);
        if (!$transport) {
            return -1;
        }

        $packing_form_data = json_decode($transport->packing_form_data, true);

        $packing_form_data_post = json_decode($request->packing_form_data, true);

        foreach ($packing_form_data as $packing_form_id => $value) {

            if (isset($packing_form_data_post[$packing_form_id])) {

                $packing_form_data[$packing_form_id] = max($packing_form_data[$packing_form_id], $packing_form_data_post[$packing_form_id]);

            }
        }

        $packing_form_data_new = json_encode($packing_form_data);
        $transport->packing_form_data = $packing_form_data_new;
        $transport->save();

        return $packing_form_data_new;
    }


}
