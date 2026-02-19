<?php

use App\Http\Controllers\Accounting\Tariff\TariffController;
use App\Http\Controllers\Utility;
use App\Http\Controllers\Utility\File;
use App\Http\Controllers\Utility\Help;
use App\Http\Controllers\Utility\Notification;
use App\Http\Controllers\Utility\OfficeAutomation;
use App\Http\Controllers\Utility\OptionController;
use App\Http\Controllers\Utility\PlaningController;
use App\Http\Controllers\Utility\PopUp;
use App\Http\Controllers\Utility\SettingController;
use App\Http\Controllers\Utility\SmartObjectsController;
use App\Http\Controllers\Utility\SpecialUnitController;
use App\Http\Controllers\Utility\Ticket;
use App\Http\Controllers\Utility\Transport;
use App\Http\Controllers\Utility\Update;
use App\Http\Controllers\Utility\Planing\Product;


Route::name("planing.")->prefix("planing")->group(function () {

    Route::get("index", [PlaningController::class, "index"])->name("index");
    Route::get("production_order", [PlaningController::class, "production_order"])->name("production_order");
    Route::post("production_order_submit", [
        PlaningController::class,
        "production_order_submit"
    ])->name("production_order_submit");

    Route::middleware(['url_check:utility.planing.production_order_demo'])->group(function () {
        Route::get("production_order_demo", [
            PlaningController::class,
            "production_order_demo"
        ])->name("production_order_demo");
        Route::post("production_order_demo_submit", [
            PlaningController::class,
            "production_order_demo_submit"
        ])->name("production_order_demo_submit");
        Route::post("production_order_demo_confirm", [
            PlaningController::class,
            "production_order_demo_confirm"
        ])->name("production_order_demo_confirm");
    });

    Route::match(['get', 'post'], "view_order/{order?}", [
        PlaningController::class,
        "view_order"
    ])->name("view_order");
    Route::match(['get', 'post'], "view_production/{production?}", [
        PlaningController::class,
        "view_production"
    ])->name("view_production");
    Route::get("view_order_list/{order_list}", [
        PlaningController::class,
        "view_order_list"
    ])->name("view_order_list");

    Route::middleware(['url_check:utility.planing.product.dashboard.index'])->
    name("product.dashboard.")->
    prefix("product.dashboard.")->
    group(function () {
        Route::match(['get', 'post'], "index", [
            Product\DashboardController::class,
            "index"
        ])->name("index");

        Route::get("packing_type_details/{product}", [Product\DashboardController::class, "packing_type_details"])->name("packing_type_details");
        Route::get("packing_form_details/{product}/{packing_type}", [Product\DashboardController::class, "packing_form_details"])->name("packing_form_details");
    });
    Route::middleware(['url_check:utility.planing.product.production_channel_type.index'])->
    name("product.production_channel_type.")->
    prefix("product.production_channel_type.")->
    group(function () {
        Route::match(['get', 'post'], "index", [
            Product\ProductionChannelTypeController::class,
            "index"
        ])->name("index");

      //  Route::get("packing_type_details/{product}", [Product\DashboardController::class, "packing_type_details"])->name("packing_type_details");
    });

});

Route::name("setting.")->prefix("/setting")->group(function () {
    Route::get("index", [SettingController::class, "index"])->name("index");
    Route::post("update/{back_url?}", [SettingController::class, "update"])->name("update");
    Route::post("update_logo", [SettingController::class, "update_logo"])->name("update_logo");
    Route::get("software_lock", [SettingController::class, "software_lock"])->name("software_lock");
    Route::get("sms_test", [SettingController::class, "sms_test"])->name("sms_test");
});

Route::name("option.")->prefix("/option")->group(function () {
    Route::get("post", [OptionController::class, "get"])->name("get");

});

Route::name("help.")->prefix("/help")->group(function () {
    Route::name("api.")->prefix("/api")->group(function () {
        Route::get("index", [Help\APIController::class, "index"])->name("index");
        Route::get("fabric_raw_start_quality_control", [
            Help\APIController::class,
            "fabric_raw_quality_control"
        ])->name("fabric_raw_quality_control");
    });
});


Route::name("special_unit.")->prefix("/special_unit")->group(function () {
    Route::get("index", [SpecialUnitController::class, "index"])->name("index");
    Route::get("create/{goods_kind}", [SpecialUnitController::class, "create"])->name("create");
    Route::post("store", [SpecialUnitController::class, "store"])->name("store");
    Route::get("destroy/{special_unit}", [SpecialUnitController::class, "destroy"])->name("destroy");
});


Route::name("ticket.")->prefix("/sfsd/fdfsdfdsf/io/ticket")->group(function () {
    Route::get("index", [Ticket\TicketController::class, "index"])->name("index");
    Route::get("create/{ticket_type?}", [Ticket\TicketController::class, "create"])->name("create");
    Route::post("store/{ticket_type}", [Ticket\TicketController::class, "store"])->name("store");

});


Route::name("file.")->prefix("/wertyu/file/sfsd")->group(function () {

    Route::name("product.")->prefix("/waio/sdsdfsaning/product/")->group(function () {
        Route::get("show_property/{product}/{goods_kind_property}/{back_to_edit??}", [
            File\Product\ShowController::class,
            "show_property"
        ])->name("show_property");
    });
});


Route::name("notification.")->prefix("/notification")->group(function () {
    Route::name("dashboard.")->prefix("/dashboard")->group(function () {
        Route::get("index", [Notification\DashboardController::class, "index"])->name("index");
        Route::get("create", [Notification\DashboardController::class, "create"])->name("create");
        Route::post("submit", [Notification\DashboardController::class, "store"])->name("store");
        Route::post("confirm", [Notification\DashboardController::class, "confirm"])->name("confirm");
    });
});

Route::name("transport.")->prefix("/transport")->group(function () {

    Route::middleware(['url_check:utility.transport.dashboard.index'])->name("dashboard.")->prefix("/dashboard")->group(function () {

        Route::match(['get', 'post'], "index", [Transport\DashboardController::class, "index"])->name("index");
        Route::get("transport_item/{transport}", [
            Transport\DashboardController::class,
            "transport_item"
        ])->name("transport_item");
        Route::get("packing_list/{transport_item}", [
            Transport\DashboardController::class,
            "packing_list"
        ])->name("packing_list");

        Route::get("view_transport/{transport}", [
            Transport\DashboardController::class,
            "view_transport"
        ])->name("view_transport");

        Route::get("create_transport/{customer_caption?}/{series?}/{order_code?}", [
            Transport\DashboardController::class,
            "create_transport"
        ])->name("create_transport");
        Route::post("store_transport", [
            Transport\DashboardController::class,
            "store_transport"
        ])->name("store_transport");
        Route::get("transport_confirm/{transport_item}/{type}", [
            Transport\DashboardController::class,
            "transport_confirm"
        ])->name("transport_confirm");
        Route::get("transport_final_confirm/{transport_item}/{type}", [
            Transport\DashboardController::class,
            "transport_final_confirm"
        ])->name("transport_final_confirm");

        Route::get("create_transport_item/{transport}", [
            Transport\DashboardController::class,
            "create_transport_item"
        ])->name("create_transport_item");
        Route::post("store_transport_item/{transport}", [
            Transport\DashboardController::class,
            "store_transport_item"
        ])->name("store_transport_item");
        Route::get("download/{transport_item}", [
            Transport\DashboardController::class,
            "download"
        ])->name("download");
        Route::get("print/{transport_item}", [
            Transport\DashboardController::class,
            "print"
        ])->name("print");
        Route::get("download_report2/{transport}", [
            Transport\DashboardController::class,
            "download_report2"
        ])->name("download_report2");
        Route::get("download_report1/{transport}", [
            Transport\DashboardController::class,
            "download_report1"
        ])->name("download_report1");
        Route::get("delete_packing_form/{transport_item}/{transport_packing_form}", [
            Transport\DashboardController::class,
            "delete_packing_form"
        ])->name("delete_packing_form");
        Route::get("transport_item_delete/{transport_item}", [
            Transport\DashboardController::class,
            "transport_item_delete"
        ])->name("transport_item_delete");

        Route::get("set_to_warehouse/{transport}", [
            Transport\DashboardController::class,
            "set_to_warehouse"
        ])->name("set_to_warehouse");


    });
    Route::name("dashboard.")->prefix("/dashboard")->group(function () {

        Route::post("confirm_input/{transport}", [
            Transport\Loading\DashboardController::class,
            "confirm_input"
        ])->name("confirm_input");

        Route::get("reject_input/{transport}", [
            Transport\Loading\DashboardController::class,
            "reject_input"
        ])->name("reject_input");

        Route::post("confirm_output/{transport}", [
            Transport\Loading\DashboardController::class,
            "confirm_output"
        ])->name("confirm_output");
        Route::get("reject_output/{transport}", [
            Transport\Loading\DashboardController::class,
            "reject_output"
        ])->name("reject_output");


    });


    Route::middleware(['url_check:utility.transport.loading.dashboard.index'])->prefix('loading')->name("loading.")->group(function () {

        Route::prefix('dashboard')->name("dashboard.")->group(function () {

            Route::get("index", [Transport\Loading\DashboardController::class, "index"])->name("index");
            Route::get("show_form/{form}", [
                Transport\Loading\DashboardController::class,
                "show_form"
            ])->name("show_form");
            Route::post("confirm_exist_form/{form}", [
                Transport\Loading\DashboardController::class,
                "confirm_exist_form"
            ])->name("confirm_exist_form");

        });

        Route::prefix('load_registration')->name("load_registration.")->group(function () {

            Route::get("create/{form}", [
                Transport\Loading\LoadRegistrationController::class,
                "create"
            ])->name("create");
            Route::post("store", [Transport\Loading\LoadRegistrationController::class, "store"])->name("store");

            Route::get("show_transport/{transport}", [
                Transport\Loading\LoadRegistrationController::class,
                "show_transport"
            ])->name("show_transport");

            Route::get("download_transport_card/{transport}/{random}/{size}", [
                Transport\Loading\LoadRegistrationController::class,
                "download_transport_card"
            ])->name("download_transport_card");
            Route::get("print_transport_card/{transport}/{random}/{size}", [
                Transport\Loading\LoadRegistrationController::class,
                "print_transport_card"
            ])->name("print_transport_card");

            Route::post("confirm_transport/{transport}", [
                Transport\Loading\LoadRegistrationController::class,
                "confirm_transport"
            ])->name("confirm_transport");

            Route::get("show_packing_list/{transport}", [
                Transport\Loading\LoadRegistrationController::class,
                "show_packing_list"
            ])->name("show_packing_list");


            Route::get("release_of_exit_form/{transport}", [
                Transport\Loading\LoadRegistrationController::class,
                "release_of_exit_form"
            ])->name("release_of_exit_form");

            Route::post("submit_release_of_exit_form/{transport}", [
                Transport\Loading\LoadRegistrationController::class,
                "submit_release_of_exit_form"
            ])->name("submit_release_of_exit_form");


        });
    });

});


Route::middleware(['url_check:utility.script.index'])->name("script.")->prefix("script")->group(function () {

    Route::match(['get', 'post'], "index", [Utility\Script\ScriptController::class, "index"])->name("index");
    Route::get("log/{script}", [Utility\Script\ScriptController::class, "log"])->name("log");


    Route::name("1001.")->prefix("/1001")->group(function () {

        Route::get("edit/{script}", [Utility\Script\Script1001Controller::class, "edit"])->name("edit");
        Route::post("update/{script}", [Utility\Script\Script1001Controller::class, "update"])->name("update");
    });

    Route::name("1002.")->prefix("/1002")->group(function () {

        Route::get("edit/{script}", [Utility\Script\Script1002Controller::class, "edit"])->name("edit");
        Route::post("update/{script}", [Utility\Script\Script1002Controller::class, "update"])->name("update");


        Route::get("edit_post/{script}/{post}", [
            Utility\Script\Script1002Controller::class,
            "edit_item_1002"
        ])->name("edit_post");
        Route::post("update_post/{script}/{post}", [
            Utility\Script\Script1002Controller::class,
            "update_item_1002"
        ])->name("update_post");

    });

    Route::name("1003.")->prefix("/1003")->group(function () {

        Route::get("edit/{script}", [Utility\Script\Script1003Controller::class, "edit"])->name("edit");
        Route::post("update/{script}", [Utility\Script\Script1003Controller::class, "update"])->name("update");

    });
    Route::name("1004.")->prefix("/1004")->group(function () {

        Route::get("edit/{script}", [Utility\Script\Script1004Controller::class, "edit"])->name("edit");
        Route::post("update/{script}", [Utility\Script\Script1004Controller::class, "update"])->name("update");

        Route::get("edit_post/{script}/{post}", [
            Utility\Script\Script1004Controller::class,
            "edit_post"
        ])->name("edit_post");
        Route::post("update_post/{script}/{post}", [
            Utility\Script\Script1004Controller::class,
            "update_post"
        ])->name("update_post");
    });


    Route::name("1005.")->prefix("/1005")->group(function () {

        Route::get("edit/{script}", [Utility\Script\Script1005Controller::class, "edit"])->name("edit");
        Route::post("update/{script}", [Utility\Script\Script1005Controller::class, "update"])->name("update");

    });


    Route::name("1006.")->prefix("/1006")->group(function () {

        Route::get("edit/{script}", [Utility\Script\Script1006Controller::class, "edit"])->name("edit");
        Route::post("update/{script}", [Utility\Script\Script1006Controller::class, "update"])->name("update");

        Route::get("edit_post/{script}/{post}", [
            Utility\Script\Script1006Controller::class,
            "edit_post"
        ])->name("edit_post");
        Route::post("update_post/{script}/{post}", [
            Utility\Script\Script1006Controller::class,
            "update_post"
        ])->name("update_post");
    });


    Route::name("1007.")->prefix("/1007")->group(function () {

        Route::get("edit/{script}", [Utility\Script\Script1007Controller::class, "edit"])->name("edit");
        Route::post("update/{script}", [Utility\Script\Script1007Controller::class, "update"])->name("update");
    });

    Route::name("1008.")->prefix("/1008")->group(function () {

        Route::get("edit/{script}", [Utility\Script\Script1008Controller::class, "edit"])->name("edit");
        Route::post("update/{script}", [Utility\Script\Script1008Controller::class, "update"])->name("update");
    });
    Route::name("1009.")->prefix("/1009")->group(function () {

        Route::get("edit/{script}", [Utility\Script\Script1009Controller::class, "edit"])->name("edit");
        Route::post("update/{script}", [Utility\Script\Script1009Controller::class, "update"])->name("update");
    });

    Route::name("1010.")->prefix("/1010")->group(function () {

        Route::get("edit/{script}", [Utility\Script\Script1010Controller::class, "edit"])->name("edit");
        Route::post("update/{script}", [Utility\Script\Script1010Controller::class, "update"])->name("update");
    });
    Route::name("1011.")->prefix("/1011")->group(function () {

        Route::get("edit/{script}", [Utility\Script\Script1011Controller::class, "edit"])->name("edit");
        Route::post("update/{script}", [Utility\Script\Script1011Controller::class, "update"])->name("update");
    });
    Route::name("1012.")->prefix("/1012")->group(function () {

        Route::get("edit/{script}", [Utility\Script\Script1012Controller::class, "edit"])->name("edit");
        Route::post("update/{script}", [Utility\Script\Script1012Controller::class, "update"])->name("update");
    });
    Route::name("1013.")->prefix("/1013")->group(function () {

        Route::get("edit/{script}", [Utility\Script\Script1013Controller::class, "edit"])->name("edit");
        Route::post("update/{script}", [Utility\Script\Script1013Controller::class, "update"])->name("update");
    });

    Route::name("1014.")->prefix("/1014")->group(function () {

        Route::get("edit/{script}", [Utility\Script\Script1014Controller::class, "edit"])->name("edit");
        Route::post("update/{script}", [Utility\Script\Script1014Controller::class, "update"])->name("update");

        Route::get("edit_machine/{script}", [Utility\Script\Script1014Controller::class, "edit_machine"])->name("edit_machine");
        Route::post("update_machine/{script}", [Utility\Script\Script1014Controller::class, "update_machine"])->name("update_machine");
    });

    Route::name("1015.")->prefix("/1015")->group(function () {

        Route::get("edit/{script}", [Utility\Script\Script1015Controller::class, "edit"])->name("edit");
        Route::post("update/{script}", [Utility\Script\Script1015Controller::class, "update"])->name("update");
    });

    Route::name("1016.")->prefix("/1016")->group(function () {
        Route::get("edit/{script}", [Utility\Script\Script1016Controller::class, "edit"])->name("edit");
        Route::post("update/{script}", [Utility\Script\Script1016Controller::class, "update"])->name("update");
    });

    Route::name("1017.")->prefix("/1017")->group(function () {
        Route::get("edit/{script}", [Utility\Script\Script1017Controller::class, "edit"])->name("edit");
        Route::post("update/{script}", [Utility\Script\Script1017Controller::class, "update"])->name("update");
    });


    Route::name("1018.")->prefix("/1018")->group(function () {
        Route::get("edit/{script}", [Utility\Script\Script1018Controller::class, "edit"])->name("edit");
        Route::post("update/{script}", [Utility\Script\Script1018Controller::class, "update"])->name("update");
    });


    Route::name("1019.")->prefix("/1019")->group(function () {
        Route::get("edit/{script}", [Utility\Script\Script1019Controller::class, "edit"])->name("edit");
        Route::post("update/{script}", [Utility\Script\Script1019Controller::class, "update"])->name("update");
    });

    Route::name("1020.")->prefix("/1020")->group(function () {
        Route::get("edit/{script}", [Utility\Script\Script1020Controller::class, "edit"])->name("edit");
        Route::post("update/{script}", [Utility\Script\Script1020Controller::class, "update"])->name("update");
    });
    Route::name("1021.")->prefix("/1021")->group(function () {
        Route::get("edit/{script}", [Utility\Script\Script1021Controller::class, "edit"])->name("edit");
        Route::post("update/{script}", [Utility\Script\Script1021Controller::class, "update"])->name("update");
    });
    Route::name("1022.")->prefix("/1022")->group(function () {
        Route::get("edit/{script}", [Utility\Script\Script1022Controller::class, "edit"])->name("edit");
        Route::post("update/{script}", [Utility\Script\Script1022Controller::class, "update"])->name("update");
    });
    Route::name("1023.")->prefix("/1023")->group(function () {
        Route::get("edit/{script}", [Utility\Script\Script1023Controller::class, "edit"])->name("edit");
        Route::post("update/{script}", [Utility\Script\Script1023Controller::class, "update"])->name("update");
    });
    Route::name("1024.")->prefix("/1024")->group(function () {
        Route::get("edit/{script}", [Utility\Script\Script1024Controller::class, "edit"])->name("edit");
        Route::post("update/{script}", [Utility\Script\Script1024Controller::class, "update"])->name("update");
    });
    Route::name("1025.")->prefix("/1025")->group(function () {
        Route::get("edit/{script}", [Utility\Script\Script1025Controller::class, "edit"])->name("edit");
        Route::post("update/{script}", [Utility\Script\Script1025Controller::class, "update"])->name("update");
    });
    Route::name("1026.")->prefix("/1026")->group(function () {
        Route::get("edit/{script}", [Utility\Script\Script1026Controller::class, "edit"])->name("edit");
        Route::post("update/{script}", [Utility\Script\Script1026Controller::class, "update"])->name("update");
    });


    Route::name("1027.")->prefix("/1027")->group(function () {
        Route::get("edit/{script}", [Utility\Script\Script1027Controller::class, "edit"])->name("edit");
        Route::post("update/{script}", [Utility\Script\Script1027Controller::class, "update"])->name("update");
    });

    Route::name("1028.")->prefix("/1028")->group(function () {
        Route::get("edit/{script}", [Utility\Script\Script1028Controller::class, "edit"])->name("edit");
        Route::post("update/{script}", [Utility\Script\Script1028Controller::class, "update"])->name("update");
    });


    Route::name("1029.")->prefix("/1029")->group(function () {
        Route::get("edit/{script}", [Utility\Script\Script1029Controller::class, "edit"])->name("edit");
        Route::post("update/{script}", [Utility\Script\Script1029Controller::class, "update"])->name("update");
    });

    Route::name("1030.")->prefix("/1030")->group(function () {
        Route::get("edit/{script}", [Utility\Script\Script1003Controller::class, "edit"])->name("edit");
        Route::post("update/{script}", [Utility\Script\Script1003Controller::class, "update"])->name("update");
    });
    Route::name("1033.")->prefix("/1033")->group(function () {
        Route::get("edit/{script}", [Utility\Script\Script1033Controller::class, "edit"])->name("edit");
        Route::post("update/{script}", [Utility\Script\Script1033Controller::class, "update"])->name("update");
    });
    Route::name("1034.")->prefix("/1034")->group(function () {
        Route::get("edit/{script}", [Utility\Script\Script1034Controller::class, "edit"])->name("edit");
        Route::post("update/{script}", [Utility\Script\Script1034Controller::class, "update"])->name("update");
    });
});


Route::middleware(['url_check:utility.pup_up.admin.index'])->name("pup_up.")->prefix("/pup_up")->group(function () {

    Route::name("admin.")->prefix("/admin")->group(function () {
        Route::get("index", [PopUp\AdminPopUpController::class, "index"])->name("index");
        Route::get("create", [PopUp\AdminPopUpController::class, "create"])->name("create");
        Route::post("store", [PopUp\AdminPopUpController::class, "store"])->name("store");
        Route::get("edit/{pup_up}", [PopUp\AdminPopUpController::class, "edit"])->name("edit");
        Route::post("update/{pup_up}", [PopUp\AdminPopUpController::class, "update"])->name("update");
        Route::get("destroy/{pup_up}", [PopUp\AdminPopUpController::class, "destroy"])->name("destroy");
    });
});


Route::name("office_automation.")->prefix("/sdjio/iohlke/omsqzc/office_automation.")->group(function () {

    Route::name("dashboard.")->prefix("/dashboard")->group(function () {

        Route::match(['get', 'post'], "index", [
            OfficeAutomation\DashboardController::class,
            "index"
        ])->name("index");

        Route::get("create/{to_do_list_parent_id?}/{work_parent_id?}", [
            OfficeAutomation\DashboardController::class,
            "create"
        ])->name("create");

        Route::post("store", [
            OfficeAutomation\DashboardController::class,
            "store"
        ])->name("store");

        Route::get("create_to_do/{office_automation_to_do_list}/{office_automation_action_id?}", [
            OfficeAutomation\DashboardController::class,
            "create_to_do"
        ])->name("create_to_do");

        Route::post("store_to_do", [
            OfficeAutomation\DashboardController::class,
            "store_to_do"
        ])->name("store_to_do");


        Route::get("view/{office_automation_work}/{current_user_id}/{parent_user_id?}", [
            OfficeAutomation\DashboardController::class,
            "view"
        ])->name("view");
        Route::get("view_other/{office_automation_action}/{office_automation_to_do_list_id?}/{work_parent_id?}", [
            OfficeAutomation\DashboardController::class,
            "view_other"
        ])->name("view_other");

        Route::get("download/{office_automation_work}/{office_automation_file}", [
            OfficeAutomation\DashboardController::class,
            "download"
        ])->name("download");

        Route::get("print_office/{office_automation_work}/{key}", [
            OfficeAutomation\DashboardController::class,
            "print_office"
        ])->name("print_office");
        Route::get("download_office/{office_automation_work}/{key}", [
            OfficeAutomation\DashboardController::class,
            "download_office"
        ])->name("download_office");

    });

    Route::name("work_done.")->prefix("/work_done")->group(function () {

        Route::post("submit/{office_automation_to_do_list}", [
            OfficeAutomation\WorkDoneController::class,
            "submit"
        ])->name("submit");
    });

    Route::name("work_done_confirm.")->prefix("/work_done_confirm")->group(function () {

        Route::post("submit", [
            OfficeAutomation\WorkDoneConfirmController::class,
            "submit"
        ])->name("submit");
    });

    Route::name("reject.")->prefix("/reject")->group(function () {

        Route::post("submit", [
            OfficeAutomation\RejectController::class,
            "submit"
        ])->name("submit");
    });

});


Route::middleware(['url_check:utility.smart_object.index'])->name("smart_object.")->prefix("/smart_object")->group(function () {
    Route::match(['get', 'post'], "index", [SmartObjectsController::class, "index"])->name("index");
    Route::get("create", [SmartObjectsController::class, "create"])->name("create");
    Route::post("store", [SmartObjectsController::class, "store"])->name("store");
    Route::get("edit/{smart_object}", [SmartObjectsController::class, "edit"])->name("edit");
    Route::post("update/{smart_object}", [SmartObjectsController::class, "update"])->name("update");
    Route::get("destroy/{smart_object}", [SmartObjectsController::class, "destroy"])->name("destroy");
    Route::get("check_connection/{smart_object}", [SmartObjectsController::class, "check_connection"])->name("check_connection");


    Route::get("setting", [SmartObjectsController::class, "setting"])->name("setting");
    Route::post("submit_setting", [SmartObjectsController::class, "submit_setting"])->name("submit_setting");
});


Route::middleware(['url_check:utility.printer.index'])->name("printer.")->prefix("/printer")->group(function () {
    Route::match(['get', 'post'], "index", [Utility\Printer\PrinterController::class, "index"])->name("index");
    Route::get("create", [Utility\Printer\PrinterController::class, "create"])->name("create");
    Route::post("store", [Utility\Printer\PrinterController::class, "store"])->name("store");
    Route::get("edit/{printer}", [Utility\Printer\PrinterController::class, "edit"])->name("edit");
    Route::post("update/{printer}", [Utility\Printer\PrinterController::class, "update"])->name("update");
    Route::get("test/{printer}", [Utility\Printer\PrinterController::class, "test"])->name("test");
});


Route::name("printer.")->prefix("/sfsd/fdfg/safsd/se/sfsf/printer")->group(function () {
    Route::get("select_default_printer", [
        Utility\Printer\PrinterController::class,
        "select_default_printer"
    ])->name("select_default_printer");
    Route::post("submit_select_default_printer", [
        Utility\Printer\PrinterController::class,
        "submit_select_default_printer"
    ])->name("submit_select_default_printer");

});


Route::prefix('/sdfi/iefsnsdfk/financial_software/setting')->name("financial_software.setting.")->group(function () {

    Route::get("index/", [
        Utility\FinancialSoftware\FinancialSettingController::class,
        "index"
    ])->name("index");

    Route::post("submit/", [
        Utility\FinancialSoftware\FinancialSettingController::class,
        "submit"
    ])->name("submit");


    Route::get("warehouse/{warehouse}/{financial_software}", [
        Utility\FinancialSoftware\FinancialSettingController::class,
        "warehouse"
    ])->name("warehouse");

    Route::post("submit_warehouse/{warehouse}/{financial_software}", [
        Utility\FinancialSoftware\FinancialSettingController::class,
        "submit_warehouse"
    ])->name("submit_warehouse");

});

Route::prefix('/sdfi/iefsnsdfk/financial_software/')->name("financial_software.")->group(function () {


    Route::middleware(['url_check:utility.financial_software.definition.index'])->
    prefix('definition')->name("definition.")->group(function () {

        Route::get("index/", [
            Utility\FinancialSoftware\FinancialDefinitionController::class,
            "index"
        ])->name("index");

        Route::get("edit/{financial_software}", [
            Utility\FinancialSoftware\FinancialDefinitionController::class,
            "edit"
        ])->name("edit");

        Route::post("update/{financial_software}", [
            Utility\FinancialSoftware\FinancialDefinitionController::class,
            "update"
        ])->name("update");


    });
});

Route::name("json_view.")->prefix("/sfsd/fdfg/safsd/se/sfsf/json_view")->group(function () {
    Route::middleware(['url_check:production.machine.index'])->get("actual_consumption_view/{json_data_list}", [
        Utility\JsonView\JsonViewController::class,
        "actual_consumption_view"
    ])->name("actual_consumption_view");

    Route::middleware(['url_check:production.machine.index'])->get("allocation_data_type_200/{production_id}/{machine_id}/{allocation_id}", [
        Utility\JsonView\JsonViewController::class,
        "allocation_data_type_200"
    ])->name("allocation_data_type_200");


});


Route::middleware(['url_check:utility.other.barcode_link.index'])->name("other.barcode_link.")->prefix("/other/barcode_link")->group(function () {
    Route::match(['get', 'post'], "index", [Utility\Other\BarcodeLinkController::class, "index"])->name("index");
    Route::get("create", [Utility\Other\BarcodeLinkController::class, "create"])->name("create");
    Route::post("store", [Utility\Other\BarcodeLinkController::class, "store"])->name("store");
    Route::get("edit/{barcode_link}", [Utility\Other\BarcodeLinkController::class, "edit"])->name("edit");
    Route::post("update/{barcode_link}", [Utility\Other\BarcodeLinkController::class, "update"])->name("update");
    Route::post("update/{barcode_link}", [Utility\Other\BarcodeLinkController::class, "update"])->name("update");
    Route::get("download_qr/{barcode_link}", [Utility\Other\BarcodeLinkController::class, "download_qr"])->name("download_qr");
});


Route::name("special_license.")->prefix("special_license")->group(function () {
    Route::middleware(['url_check:utility.special_license.definition.dashboard.index'])->name("definition.")->prefix("definition")->group(function () {
        Route::name("dashboard.")->prefix("dashboard")->group(function () {

            Route::get("index/", [
                Utility\SpecialLicense\Definition\DashboardController::class,
                "index"
            ])->name("index");

            Route::get("edit/{special_license_type}", [
                Utility\SpecialLicense\Definition\DashboardController::class,
                "edit"
            ])->name("edit");

            Route::post("update/{special_license_type}", [
                Utility\SpecialLicense\Definition\DashboardController::class,
                "update"
            ])->name("update");
            Route::get("edit_status/{special_license_type}", [
                Utility\SpecialLicense\Definition\DashboardController::class,
                "edit_status"
            ])->name("edit_status");

            Route::post("update_status/{special_license_type}", [
                Utility\SpecialLicense\Definition\DashboardController::class,
                "update_status"
            ])->name("update_status");
            Route::get("percent_of_committee/{special_license_type}", [
                Utility\SpecialLicense\Definition\DashboardController::class,
                "percent_of_committee"
            ])->name("percent_of_committee");

            Route::post("submit_percent_of_committee/{special_license_type}", [
                Utility\SpecialLicense\Definition\DashboardController::class,
                "submit_percent_of_committee"
            ])->name("submit_percent_of_committee");

        });
    });

    Route::middleware(['url_check:utility.special_license.panel.dashboard.index'])->name("panel.")->prefix("panel")->group(function () {
        Route::name("dashboard.")->prefix("dashboard")->group(function () {

            Route::match(['get', 'post'], "index", [
                Utility\SpecialLicense\Panel\DashboardController::class,
                "index"
            ])->name("index");
            Route::match(['get', 'post'], "my_license", [
                Utility\SpecialLicense\Panel\DashboardController::class,
                "my_license"
            ])->name("my_license");

            Route::get("view/{special_license}", [
                Utility\SpecialLicense\Panel\DashboardController::class,
                "view"
            ])->name("view");

            Route::get("view_confirm/{special_license}", [
                Utility\SpecialLicense\Panel\DashboardController::class,
                "view_confirm"
            ])->name("view_confirm");

            Route::post("confirm/{special_license}", [
                Utility\SpecialLicense\Panel\DashboardController::class,
                "confirm"
            ])->name("confirm");

        });
        Route::name("new_special_license.")->prefix("new_special_license")->group(function () {

            Route::get("index/{special_license_type}/{reference_id}/{param1?}/{param2?}/{param3?}/{param4?}/{param5?}", [
                Utility\SpecialLicense\Panel\NewSpecialLicenseController::class,
                "index"
            ])->name("index");
            Route::post("store/{special_license_type}/{reference_id}/{param1?}/{param2?}/{param3?}/{param4?}/{param5?}", [
                Utility\SpecialLicense\Panel\NewSpecialLicenseController::class,
                "store"
            ])->name("store");

        });
        Route::name("new_special_license_13.")->prefix("new_special_license_13")->group(function () {

            Route::get("index", [Utility\SpecialLicense\Panel\NewSpecialLicense13Controller::class, "index"])->name("index");
            Route::match(['get', 'post'], "store", [Utility\SpecialLicense\Panel\NewSpecialLicense13Controller::class, "store"])->name("store");

        });
    });

    Route::middleware(['url_check:utility.special_license.admin.dashboard.index'])->name("admin.")->prefix("admin")->group(function () {
        Route::name("dashboard.")->prefix("dashboard")->group(function () {

            Route::match(['get', 'post'], "index", [
                Utility\SpecialLicense\Admin\DashboardController::class,
                "index"
            ])->name("index");

            Route::get("view/{special_license}", [
                Utility\SpecialLicense\Admin\DashboardController::class,
                "view"
            ])->name("view");

        });

    });
});

Route::name("update.")->prefix("update")->group(function () {

    Route::middleware(['url_check:utility.update.dashboard.index'])->name("dashboard.")->prefix("dashboard")->group(function () {

        Route::get("index/", [
            Update\DashboardController::class,
            "index"
        ])->name("index");

        Route::post("submit", [
            Update\DashboardController::class,
            "submit"
        ])->name("submit");

    });
});


Route::name("car.car_type.")->prefix("car/car_type/")->group(function () {
    Route::get("index", [Utility\Car\CarTypeController::class, "index"])->name("index");
    Route::get("edit/{car_type}", [Utility\Car\CarTypeController::class, "edit"])->name("edit");
    Route::post("update/{car_type}", [Utility\Car\CarTypeController::class, "update"])->name("update");

});



