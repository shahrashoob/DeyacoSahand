<?php

use App\Http\Controllers\Warehouse\Input;
use App\Http\Controllers\Warehouse\MaterialDashboardController;
use App\Http\Controllers\Warehouse\Out;
use App\Http\Controllers\Warehouse\ProductionWarehouse;
use App\Http\Controllers\Warehouse\ProductDashboardController;
use App\Http\Controllers\Warehouse\Transport;
use App\Http\Controllers\Warehouse\WarehouseController;
use App\Http\Controllers\Warehouse\WarehousePrintController;
use App\Http\Controllers\Warehouse;
use App\Http\Controllers\Warehouse\WarehouseHandling;
use App\Http\Controllers\Warehouse\WarehouseShelving;
use App\Http\Controllers\Warehouse\Pallet;

Route::prefix('input/')->name("input.")->group(function () {
    Route::middleware(['url_check:wh.input.entry_form.index'])->prefix('entry_form.')->name("entry_form.")->group(function () {

        Route::get("index/{is_edit??}", [
            Input\EntryFormController::class,
            "index"
        ])->name("index");
        Route::post("submit/", [
            Input\EntryFormController::class,
            "submit"
        ])->name("submit");
        Route::post("confirm/", [
            Input\EntryFormController::class,
            "confirm"
        ])->name("confirm");


        Route::get("index2/{is_edit??}", [
            Input\EntryFormController::class,
            "index2"
        ])->name("index2");
        Route::post("submit2/", [
            Input\EntryFormController::class,
            "submit2"
        ])->name("submit2");
        Route::post("confirm2/", [
            Input\EntryFormController::class,
            "confirm2"
        ])->name("confirm2");

        

        
    });

    Route::middleware(['url_check:wh.dashboard.index'])->prefix('complete_information.')->name("complete_information.")->group(function () {

        Route::get("index/{form}", [
            Input\CompleteInformationController::class,
            "index"
        ])->name("index");
        Route::post("submit/{form}", [
            Input\CompleteInformationController::class,
            "submit"
        ])->name("submit");

        Route::get("confirm/{form}", [
            Input\CompleteInformationController::class,
            "confirm"
        ])->name("confirm");
        Route::post("submit_confirm/{form}", [
            Input\CompleteInformationController::class,
            "submit_confirm"
        ])->name("submit_confirm");


//          Route::get("complated_information_with_value/{form}" ,
//       [Input\ComplatedInformationwithValueController::class,'index']
//        )->name("complated_information_with_value_index");
//
//        Route::get("complated_information_with_value_control/{general_item}" ,
//            [Input\ComplatedInformationwithValueController::class,'QualityControl']
//        )->name("complated_information_with_value_control_index");
//
//        Route::get("complated_information_with_value_control_final/{general_item}" ,
//            [Input\ComplatedInformationwithValueController::class,'final_step_to_separation']
//        )->name("complated_information_with_value_control_final_index");
//
//        Route::get("complated_information_with_value_show_list/{form}" ,
//            [Input\ComplatedInformationwithValueController::class,'view_list_of_separation']
//        )->name("complated_information_with_value_show_list");
//

        


    });

    Route::middleware(['url_check:wh.dashboard.index'])->prefix('complete_information_with_value.')->name("complete_information_with_value.")->group(function () {
        Route::get("index/{form}" ,
            [Input\CompleteInformationWithValueController::class,'index']
        )->name("index");

        Route::get("quality_control/{form_general_item}" ,
            [Input\CompleteInformationWithValueController::class,'quality_control']
        )->name("quality_control");

        Route::get("quality_confirmed/{form_general_item}" ,
            [Input\CompleteInformationWithValueController::class,'quality_confirmed']
        )->name("quality_confirmed");

        Route::get("separation/{form_general_item}" ,
            [Input\CompleteInformationWithValueController::class,'separation']
        )->name("separation");
        Route::get("end_of_separation/{form_general_item}" ,
            [Input\CompleteInformationWithValueController::class,'end_of_separation']
        )->name("end_of_separation");

        Route::get("confirm_form/{form}" ,
            [Input\CompleteInformationWithValueController::class,'confirm_form']
        )->name("confirm_form");

        Route::get("show_list/{form}" ,
            [Input\CompleteInformationWithValueController::class,'show_list']
        )->name("show_list");

        Route::get("delete_packing_form/{form_general_item}/{form_general_item_packing_form}" ,
            [Input\CompleteInformationWithValueController::class,'delete_packing_form']
        )->name("delete_packing_form");

        Route::get("print_packing_form/{form_general_item_packing_form}" ,
            [Input\CompleteInformationWithValueController::class,'print_packing_form']
        )->name("print_packing_form");
    });
});


//Route::match( [ 'get', 'post' ], "list", [ CurrentDashboardController::class, "list" ] )->name( "cd.list" );
//Route::get( "view_material/{production}", [
//    CurrentDashboardController::class,
//    "view_material"
//] )->name( "cd.view_material" );
//Route::post( "confirm_form_request/{production}", [
//    CurrentDashboardController::class,
//    "confirm_form_request"
//] )->name( "cd.confirm_form_request" );
//Route::post( "store_form_request/{production}/{form}", [
//    CurrentDashboardController::class,
//    "store_form_request"
//] )->name( "cd.store_form_request" );
//Route::get( "delivery_form_request/{production}", [
//    CurrentDashboardController::class,
//    "delivery_form_request"
//] )->name( "cd.delivery_form_request" );
//Route::get( "current/show_exit_form/{production}/{form}", [
//    CurrentDashboardController::class,
//    "show_exit_form"
//] )->name( "cd.show_exit_form" );


//Route::get( "entry_form_to_warehouse/", [
//    CurrentDashboardController::class,
//    "entry_form_to_warehouse"
//] )->name( "entry_form_to_warehouse" );
//Route::post( "entry_form_to_warehouse_submit/", [
//    CurrentDashboardController::class,
//    "entry_form_to_warehouse_submit"
//] )->name( "entry_form_to_warehouse_submit" );
//Route::post( "entry_form_to_warehouse_confirm/", [
//    CurrentDashboardController::class,
//    "entry_form_to_warehouse_confirm"
//] )->name( "entry_form_to_warehouse_confirm" );
//Route::get( "entry_form_show/{form}", [
//    CurrentDashboardController::class,
//    "entry_form_show"
//] )->name( "entry_form_show" );
//
//Route::get( "entry_form_list", [ CurrentDashboardController::class, "entry_form_list" ] )->name( "entry_form_list" );

Route::prefix('fse/wouse')->name("material.")->group(function () {
    Route::match(['get', 'post'], "list", [MaterialDashboardController::class, "list"])->name("list");
    Route::get("view_materials/{production}/{delivery_form??}", [
        MaterialDashboardController::class,
        "view_materials"
    ])->name("view_materials");
    Route::post("confirm_form_request/{production}", [
        MaterialDashboardController::class,
        "confirm_form_request"
    ])->name("confirm_form_request");
    Route::get("confirm_form_request_show/{production}/{form}", [
        MaterialDashboardController::class,
        "confirm_form_request_show"
    ])->name("confirm_form_request_show");
    Route::post("store_form_request/{production}/{form}", [
        MaterialDashboardController::class,
        "store_form_request"
    ])->name("store_form_request");
    Route::get("show_exit_form/{production}/{form}", [
        MaterialDashboardController::class,
        "show_exit_form"
    ])->name("show_exit_form");

});


Route::prefix('financial_software')->name("financial_software.")->group(function () {

    Route::get("index/", [
        Warehouse\FinancialSoftwareController::class,
        "index"
    ])->name("index");
    Route::post("submit_nosa_xml/", [
        Warehouse\FinancialSoftwareController::class,
        "submit_nosa_xml"
    ])->name("submit_nosa_xml");

    Route::match(['get', 'post'], "form_list/", [
        Warehouse\FinancialSoftwareController::class,
        "form_list"
    ])->name("form_list");

    Route::get("log/{transfer_form_id}/{financial_trans_kind_type_id}/{page}", [
        Warehouse\FinancialSoftwareController::class,
        "log"
    ])->name("log");
    Route::get("transfer_from_list/{form}/{page}", [
        Warehouse\FinancialSoftwareController::class,
        "transfer_from_list"
    ])->name("transfer_from_list");

    Route::get("review/{transfer_form_id}/{trans_kind_form_type_id}", [
        Warehouse\FinancialSoftwareController::class,
        "review"
    ])->name("review");

    Route::get("no_need_to_register/{transfer_form_id}/{trans_kind_form_type_id}", [
        Warehouse\FinancialSoftwareController::class,
        "no_need_to_register"
    ])->name("no_need_to_register");

});

//Route::get( "send_to_nosa/", [ WarehousePrintController::class, "send_to_nosa" ] )->name( "send_to_nosa" );
//Route::post( "send_to_nosa_xml/", [ WarehousePrintController::class, "send_to_nosa_xml" ] )->name( "send_to_nosa_xml" );


//Route::match(['get','post'],"list",[CurrentDashboardController::class,"list"])->name("current_dashboard.palet_sheet");

Route::match(['get', 'post'], "product/list", [
    ProductDashboardController::class,
    "list"
])->name("product.list");
Route::get("product/view_order/{order}", [
    ProductDashboardController::class,
    "view_order"
])->name("product.view_order");
Route::post("product/confirm_form_request/{order}", [
    ProductDashboardController::class,
    "confirm_form_request"
])->name("product.confirm_form_request");
Route::post("product/store_form_request/{order}/{form}", [
    ProductDashboardController::class,
    "store_form_request"
])->name("product.store_form_request");
Route::get("product/delivery_form_request/{order}", [
    ProductDashboardController::class,
    "delivery_form_request"
])->name("product.delivery_form_request");
Route::get("product/show_exit_form/{order}/{form}", [
    ProductDashboardController::class,
    "show_exit_form"
])->name("product.show_exit_form");

// Print

Route::prefix('print')->name("print.")->group(function () {

    Route::get("request/{production}", [WarehousePrintController::class, "print_rfw"])->name("print_rfw");
    Route::get("print_rfw_group/{production}", [
        WarehousePrintController::class,
        "print_rfw_group"
    ])->name("print_rfw_group");

    Route::get("request_order/{order}/{include_header}", [
        WarehousePrintController::class,
        "print_request_order"
    ])->name("print_rfw_order");

    Route::get("request_current_orders/", [
        WarehousePrintController::class,
        "print_request_current_orders"
    ])->name("request_current_orders");

    Route::get("exit_form/{order}/{form}", [
        WarehousePrintController::class,
        "exit_form"
    ])->name("exit_form");


    Route::get("palet_sheet/", [
        WarehousePrintController::class,
        "print_palet_sheet"
    ])->name("palet_sheet");

    Route::post("palet_sheet_post/", [
        WarehousePrintController::class,
        "palet_sheet_post"
    ])->name("palet_sheet_post");

    Route::get("form/{form}", [WarehousePrintController::class, "form"])->name("form");


});


Route::name("warehouse.")->prefix("/warehouse")->group(function () {
    Route::match(['get', 'post'], "index", [WarehouseController::class, "index"])->name("index");
    Route::get("create", [WarehouseController::class, "create"])->name("create");
    Route::post("store", [WarehouseController::class, "store"])->name("store");
    Route::get("edit/{warehouse}", [WarehouseController::class, "edit"])->name("edit");
    Route::post("update/{warehouse}", [WarehouseController::class, "update"])->name("update");

    Route::post("shelving_print_label/{warehouse}", [WarehouseController::class, "shelving_print_label"])->name("shelving.print_label");
    Route::get("shelving_index/{warehouse}", [WarehouseController::class, "shelving_index"])->name("shelving.index");
    Route::post("shelving_submit/{warehouse}", [WarehouseController::class, "shelving_submit"])->name("shelving.submit");


});

// Out
Route::name("out.")->prefix("/out")->group(function () {

    Route::middleware(['url_check:wh.out.dashboard.index'])->group(function () {
        Route::name("dashboard.")->prefix("/dashboard")->group(function () {
            Route::match(['get', 'post'], "index", [Out\DashboardController::class, "index"])->name("index");

            Route::get("view/{product_request_form}/{page?}", [
                Out\DashboardController::class,
                "view"
            ])->name("view");
            Route::get("view_order/{product_request_form}", [
                Out\DashboardController::class,
                "view_order"
            ])->name("view_order");

            Route::get("checkout/{product_request_form}", [
                Out\DashboardController::class,
                "checkout"
            ])->name("checkout");
            Route::post("confirm/{product_request_form}", [
                Out\DashboardController::class,
                "confirm"
            ])->name("confirm");

            Route::get("checkout_1/{product_request_form_item}", [
                Out\DashboardController::class,
                "checkout_1"
            ])->name("checkout_1");
            Route::post("confirm_1/{product_request_form_item}", [
                Out\DashboardController::class,
                "confirm_1"
            ])->name("confirm_1");


            Route::get("checkout_variety_1/{product_request_form_item}", [
                Out\DashboardController::class,
                "checkout_variety_1"
            ])->name("checkout_variety_1");
            Route::post("confirm_variety_1/{product_request_form_item}", [
                Out\DashboardController::class,
                "confirm_variety_1"
            ])->name("confirm_variety_1");


            Route::get("show_form/{product_request_form}/{form}/{page?}", [
                Out\DashboardController::class,
                "show_form"
            ])->name("show_form");

            Route::get("show_json_data/{product_request_form}/{product_request_form_log}/{page?}", [
                Out\DashboardController::class,
                "show_json_data"
            ])->name("show_json_data");

            Route::get("log/{product_request_form}/{page?}", [
                Out\DashboardController::class,
                "log"
            ])->name("log");

            Route::get("print_product_request_form/{product_request_form}/{packing_type_label}/{type}/{dashboard_type?}", [
                Out\DashboardController::class,
                "print_product_request_form"
            ])->name("print_product_request_form");



        });
        Route::name("customer.")->prefix("/customer")->group(function () {
            Route::match(['get', 'post'], "index", [Out\CustomerController::class, "index"])->name("index");

            Route::get("view/{customer}", [
                Out\CustomerController::class,
                "view"
            ])->name("view");
        });
        Route::name("download.")->prefix("/download")->group(function () {

            Route::get("index", [Out\DownloadLicenseController::class, "index"])->name("index");
        });

        Route::name("exit_form.")->prefix("/exit_form")->group(function () {
            Route::get("print/{product_request_form}/{form}/{packing_type_label_printing_type}/{print_number?}/{print_type?}", [
                Out\ExitFormController::class,
                "print"
            ])->name("print");

            Route::get("download/{product_request_form}/{form}/{packing_type_label_printing_type}/{print_type?}", [
                Out\ExitFormController::class,
                "download"
            ])->name("download");

            Route::get("print_with_out_request_form/{form}/{packing_type_label_printing_type}/{print_number?}/{print_type?}", [
                Out\ExitFormController::class,
                "print_with_out_request_form"
            ])->name("print_with_out_request_form");

            Route::get("download_with_out_request_form/{form}/{packing_type_label_printing_type}/{print_type?}", [
                Out\ExitFormController::class,
                "download_with_out_request_form"
            ])->name("download_with_out_request_form");


        });

        Route::name("delivery.")->prefix("/delivery")->group(function () {
            Route::get("index/{product_request_form}/{product_id?}/{page?}/{dashboard_type?}", [
                Out\DeliveryController::class,
                "index"
            ])->name("index");

            Route::get("packing_list_data_for_select/{product_request_form}/{product_id?}/{page?}/{dashboard_type?}", [
                Out\DeliveryController::class,
                "packing_list_data_for_select"
            ])->name("packing_list_data_for_select");


            Route::post("submit/{product_request_form}/{page?}/{product_id_delivery?}/{dashboard_type?}", [
                Out\DeliveryController::class,
                "submit"
            ])->name("submit");

            Route::get("select_other_product_request_form_item/{product_request_form}/{page?}/{dashboard_type?}",
                [Out\DeliveryController::class, "select_other_product_request_form_item"])->
            name("select_other_product_request_form_item");

            Route::post("submit_select_other_product_request_form_item/{product_request_form}/{page?}/{dashboard_type?}",
                [Out\DeliveryController::class, "submit_select_other_product_request_form_item"])->
            name("submit_select_other_product_request_form_item");

            Route::post("submit_select_new_packing_type/{product_request_form}/{page?}",
                [Out\DeliveryController::class, "submit_select_new_packing_type"])->
            name("submit_select_new_packing_type");

            Route::get("checkout/{product_request_form}/{page?}/{dashboard_type?}",
                [Out\DeliveryController::class, "checkout"])->
            name("checkout");

            Route::post("confirm/{product_request_form}/{page?}",
                [Out\DeliveryController::class, "confirm"])->
            name("confirm");

            Route::get("checkout_warehouse_type/{product_request_form}", [
                Out\DeliveryController::class,
                "checkout_warehouse_type"
            ])->name("checkout_warehouse_type");

//            Route::post( "confirm_warehouse_type/{product_request_form}", [
//                Out\DeliveryController::class,
//                "confirm_warehouse_type"
//            ] )->name( "confirm_warehouse_type" );

            Route::get("print_new_packing/{product_request_form}/{page?}",
                [Out\DeliveryController::class, "print_new_packing"])->
            name("print_new_packing");

            Route::post("submit_print_new_packing/{page?}",
                [Out\DeliveryController::class, "submit_print_new_packing"])->
            name("submit_print_new_packing");

            Route::get("delivery_product_btn/{product_request_form}/{page?}/{dashboard_type?}",
                [Out\DeliveryController::class, "delivery_product_btn"])->
            name("delivery_product_btn");

            Route::get("remove_packing_form_request/{product_request_form}/{packing_form?}/{dashboard_type?}",
                [Out\DeliveryController::class, "remove_packing_form_request"])->
            name("remove_packing_form_request");


//        Route::get( "select_amount_of_product_request_form/{product_request_form}",
//            [ Out\DeliveryController::class, "select_amount_of_product_request_form" ] )->
//        name( "select_amount_of_product_request_form" );
//
//        Route::post( "submit_select_amount_of_product_request_form/{product_request_form}",
//            [ Out\DeliveryController::class, "submit_select_amount_of_product_request_form" ] )->
//        name( "submit_select_amount_of_product_request_form" );
//
//        Route::get( "select_amount_of_product_request_form_variety_1/{product_request_form_item}",
//            [ Out\DeliveryController::class, "select_amount_of_product_request_form_variety_1" ] )->
//        name( "select_amount_of_product_request_form_variety_1" );
//
//        Route::post( "submit_select_amount_of_product_request_form_variety_1/{product_request_form_item}",
//            [ Out\DeliveryController::class, "submit_select_amount_of_product_request_form_variety_1" ] )->
//        name( "submit_select_amount_of_product_request_form_variety_1" );


        });

        Route::name("check_packing_form.")->prefix("/check_packing_form")->group(function () {
            Route::get("index/{product_request_form}/{page?}/{dashboard_type?}", [
                Out\CheckPackingFormController::class,
                "index"
            ])->name("index");
            Route::post("submit/{product_request_form}/{page?}/{dashboard_type?}", [
                Out\CheckPackingFormController::class,
                "submit"
            ])->name("submit");
        });
    });

    Route::middleware(['url_check:wh.out.exit_form_implementation.index'])->prefix('exit_form_implementation')->name("exit_form_implementation.")->group(function () {

        Route::get("index/{search_exist_form_id?}", [
            Out\ExitFormImplementationController::class,
            "index"
        ])->name("index");

        Route::post("submit/", [
            Out\ExitFormImplementationController::class,
            "submit"
        ])->name("submit");


        Route::get("show_list/", [
            Out\ExitFormImplementationController::class,
            "show_list"
        ])->name("show_list");

        Route::post("confirm/", [
            Out\ExitFormImplementationController::class,
            "confirm"
        ])->name("confirm");

    });

    Route::middleware(['url_check:wh.out.exit_form_implementation2.index'])->prefix('exit_form_implementation2')->name("exit_form_implementation2.")->group(function () {

        Route::get("index/{search_exist_form_id?}", [
            Out\ExitFormImplementation2Controller::class,
            "index"
        ])->name("index");

        Route::post("submit/", [
            Out\ExitFormImplementation2Controller::class,
            "submit"
        ])->name("submit");


        Route::get("read_packing_forms", [
            Out\ExitFormImplementation2Controller::class,
            "read_packing_forms"
        ])->name("read_packing_forms");
        Route::post("remove_packing_data/{count}", [
            Out\ExitFormImplementation2Controller::class,
            "remove_packing_data"
        ])->name("remove_packing_data");

        Route::get("show_list/", [
            Out\ExitFormImplementation2Controller::class,
            "show_list"
        ])->name("show_list");

        Route::post("confirm/", [
            Out\ExitFormImplementation2Controller::class,
            "confirm"
        ])->name("confirm");


        Route::get("read_product_info_type4", [
            Out\ExitFormImplementation2Controller::class,
            "read_product_info_type4"
        ])->name("read_product_info_type4");


        Route::post("submit_read_product_info_type4", [
            Out\ExitFormImplementation2Controller::class,
            "submit_read_product_info_type4"
        ])->name("submit_read_product_info_type4");

        Route::post("confirm_read_product_info_type4", [
            Out\ExitFormImplementation2Controller::class,
            "confirm_read_product_info_type4"
        ])->name("confirm_read_product_info_type4");

    });

    Route::middleware(['url_check:wh.out.product_request_form_implementation.index'])->prefix('product_request_form_implementation')->name("product_request_form_implementation.")->group(function () {

        Route::get("index/{search_exist_form_id?}", [
            Out\ProductRequestFormImplementationController::class,
            "index"
        ])->name("index");

        Route::post("submit", [
            Out\ProductRequestFormImplementationController::class,
            "submit"
        ])->name("submit");

        Route::get("product_request_form_item", [
            Out\ProductRequestFormImplementationController::class,
            "product_request_form_item"
        ])->name("product_request_form_item");

        Route::post("submit_product_request_form", [
            Out\ProductRequestFormImplementationController::class,
            "submit_product_request_form"
        ])->name("submit_product_request_form");

        Route::post("confirm_product_request_form", [
            Out\ProductRequestFormImplementationController::class,
            "confirm_product_request_form"
        ])->name("confirm_product_request_form");

    });

});


// Production Warehouse
Route::name("production_warehouse.")->prefix("/production_warehouse")->group(function () {

    Route::middleware(['url_check:wh.production_warehouse.dashboard.index'])->group(function () {
        Route::name("dashboard.")->prefix("/dashboard")->group(function () {
            Route::match(['get', 'post'], "index", [
                ProductionWarehouse\DashboardController::class,
                "index"
            ])->name("index");

            Route::get("confirm/{form}/{product_request_form}/{warehouse}", [
                ProductionWarehouse\DashboardController::class,
                "confirm"
            ])->name("confirm");

            Route::post("submit_confirm/{form}/{product_request_form}/{warehouse}", [
                ProductionWarehouse\DashboardController::class,
                "submit_confirm"
            ])->name("submit_confirm");

            Route::get("reject/{form}/{product_request_form}/{warehouse}", [
                ProductionWarehouse\DashboardController::class,
                "reject"
            ])->name("reject");

            Route::post("submit_reject/{form}/{product_request_form}/{warehouse}", [
                ProductionWarehouse\DashboardController::class,
                "submit_reject"
            ])->name("submit_reject");


        });

    });

});

// Transport
Route::name("transport.")->prefix("/transport")->middleware(['url_check:wh.out.dashboard.index'])->group(function () {

    Route::name("dashboard.")->prefix("/dashboard")->group(function () {

        Route::get("index/{product_request_form}/{page?}/{dashboard_type?}", [
            Transport\DashboardController::class,
            "index"
        ])->name("index");

        Route::post("confirm_multi_transport_item/{product_request_form}/{page?}", [
            Transport\DashboardController::class,
            "confirm_multi_transport_item"
        ])->
        name("confirm_multi_transport_item");

        Route::get("transport_item/{product_request_form}", [
            Transport\DashboardController::class,
            "transport_item"
        ])->name("transport_item");
        Route::get("packing_list/{transport_item}/{page?}/{dashboard_type?}", [
            Transport\DashboardController::class,
            "packing_list"
        ])->name("packing_list");

        Route::get("transport_confirm/{transport_item}/{type}/{page?}/{dashboard_type?}", [
            Transport\DashboardController::class,
            "transport_confirm"
        ])->name("transport_confirm");
        Route::get("transport_final_confirm/{transport_item}/{page?}/{dashboard_type?}", [
            Transport\DashboardController::class,
            "transport_final_confirm"
        ])->name("transport_final_confirm");


        Route::get("create_transport_item/{product_request_form}/{page?}/{dashboard_type?}", [
            Transport\DashboardController::class,
            "create_transport_item"
        ])->name("create_transport_item");
        Route::post("store_transport_item/{product_request_form}", [
            Transport\DashboardController::class,
            "store_transport_item"
        ])->name("store_transport_item");
        Route::get("download/{transport_item}", [
            Transport\DashboardController::class,
            "download"
        ])->name("download");
        Route::get("print/{transport_item}/{i}/{j}", [
            Transport\DashboardController::class,
            "print"
        ])->name("print");
        Route::get("download_transport/{product_request_form}", [
            Transport\DashboardController::class,
            "download_transport"
        ])->name("download_transport");
        Route::get("download_report1/{product_request_form}", [
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


    });

});

Route::name("dashboard.")->prefix("/dashboard")->group(function () {
    Route::match(['get', 'post'], "index", [
        \App\Http\Controllers\Warehouse\DashboardController::class,
        "index"
    ])->name("index");
    Route::get("show_form/{form}/{page?}", [
        \App\Http\Controllers\Warehouse\DashboardController::class,
        "show_form"
    ])->name("show_form");
//    Route::post( "confirm_form/{form}/{page?}", [
//        \App\Http\Controllers\Warehouse\DashboardController::class,
//        "confirm_form"
//    ] )->name( "confirm_form" );
//    Route::post( "reject_form/{form}/{page?}", [
//        \App\Http\Controllers\Warehouse\DashboardController::class,
//        "reject_form"
//    ] )->name( "reject_form" );
    Route::post("confirm_packing_list/{form}/{page?}", [
        \App\Http\Controllers\Warehouse\DashboardController::class,
        "confirm_packing_list"
    ])->name("confirm_packing_list");
    Route::get("reject_packing_list/{form}", [
        \App\Http\Controllers\Warehouse\DashboardController::class,
        "reject_packing_list"
    ])->name("reject_packing_list");


    Route::get("print_packing_form/{form}/{page}", [
        \App\Http\Controllers\Warehouse\DashboardController::class,
        "print_packing_form"
    ])->name("print_packing_form");

    Route::post("submit_print_packing_form/{form}/{page}", [
        \App\Http\Controllers\Warehouse\DashboardController::class,
        "submit_print_packing_form"
    ])->name("submit_print_packing_form");

    Route::get("set_to_warehouse_all", [
        \App\Http\Controllers\Warehouse\DashboardController::class,
        "set_to_warehouse_all"
    ])->name("set_to_warehouse_all");


    Route::get("print_entry_form/{form}/{packing_type_label_printing_type}", [
        \App\Http\Controllers\Warehouse\DashboardController::class,
        "print_entry_form"
    ])->name("print_entry_form");


    Route::get("download_entry_form/{form}/{packing_type_label_printing_type}", [
        \App\Http\Controllers\Warehouse\DashboardController::class,
        "download_entry_form"
    ])->name("download_entry_form");


});

Route::name("delivery_dashboard-delete.")->prefix("/delivery_dashboard")->group(function () {
    Route::get("index", [
        \App\Http\Controllers\Warehouse\DeliveryDashboardController::class,
        "index"
    ])->name("index");
    Route::get("show_form/{warps_request_form}", [
        \App\Http\Controllers\Warehouse\DeliveryDashboardController::class,
        "show_form"
    ])->name("show_form");

    Route::post("delivery_confirmation/{warps_request_form}", [
        \App\Http\Controllers\Warehouse\DeliveryDashboardController::class,
        "delivery_confirmation"
    ])->name("delivery_confirmation");
    Route::get("log/{warps_request_form}", [
        \App\Http\Controllers\Warehouse\DeliveryDashboardController::class,
        "log"
    ])->name("log");

});

// Reject Product

Route::get("reject.reject_product_form.confirm_form/{reject_product_form}", [
    \App\Http\Controllers\Warehouse\Reject\RejectProductFormController::class,
    "confirm_form"
])->name("reject.reject_product_form.confirm_form");

Route::get("show_output_packing_form/{form}/{key}", [
    Out\ExitFormController::class,
    "show_output_packing_form"
])->name("show_output_packing_form");

########################## Warehouse Shelving


Route::name("warehouse_shelving.")->prefix("/warehouse_shelving")->group(function () {

    Route::name("definition.")->prefix("/definition")->group(function () {
        Route::match(['get', 'post'], "index/{warehouse}", [
            WarehouseShelving\DefinitionController::class,
            "index"
        ])->name("index");
        Route::get("list/{warehouse}/{warehouse_shelving}", [
            WarehouseShelving\DefinitionController::class,
            "list"
        ])->name("list");
        Route::get("create/{warehouse}", [
            WarehouseShelving\DefinitionController::class,
            "create"
        ])->name("create");
        Route::post("store/{warehouse}", [
            WarehouseShelving\DefinitionController::class,
            "store"
        ])->name("store");
        Route::get("create_sub_line/{warehouse}/{warehouse_shelving}", [
            WarehouseShelving\DefinitionController::class,
            "create_sub_line"
        ])->name("create_sub_line");
        Route::post("store_sub_line/{warehouse}/{warehouse_shelving}", [
            WarehouseShelving\DefinitionController::class,
            "store_sub_line"
        ])->name("store_sub_line");
        Route::get("edit/{warehouse}/{warehouse_shelving}", [
            WarehouseShelving\DefinitionController::class,
            "edit"
        ])->name("edit");
        Route::post("update/{warehouse}/{warehouse_shelving}", [
            WarehouseShelving\DefinitionController::class,
            "update"
        ])->name("update");
        Route::get("shelving_print_label/{warehouse}/{warehouse_shelving}/{label_printing_type_id}", [
            WarehouseShelving\DefinitionController::class,
            "shelving_print_label"
        ])->name("shelving_print_label");
        Route::get("shelving_download_label/{warehouse}/{warehouse_shelving}/{label_printing_type_id}", [
            WarehouseShelving\DefinitionController::class,
            "shelving_download_label"
        ])->name("shelving_download_label");

    });

    Route::name("dashboard.")->prefix("/dashboard")->group(function () {
        Route::match(['get', 'post'], "view_qr/{warehouse_shelving}", [
            WarehouseShelving\DashboardController::class,
            "view_qr"
        ])->name("view_qr");


        Route::match(['get', 'post'], "submit_add_packing_form/{warehouse_shelving}", [
            WarehouseShelving\DashboardController::class,
            "submit_add_packing_form"
        ])->name("submit_add_packing_form");

        Route::match(['get', 'post'], "submit_add_reservoir/{warehouse_shelving}", [
            WarehouseShelving\DashboardController::class,
            "submit_add_reservoir"
        ])->name("submit_add_reservoir");
    });
});
########################### Warehouse Handling
Route::name("warehouse_handling.")->prefix("/warehouse_handling")->group(function () {

    Route::name("dashboard.")->prefix("/dashboard")->group(function () {
        Route::match(['get', 'post'], "index", [
            WarehouseHandling\DashboardController::class,
            "index"
        ])->name("index");
        Route::get("view/{warehouse_handling}/{page?}", [
            WarehouseHandling\DashboardController::class,
            "view"
        ])->name("view");
        Route::get("packing_form_list/{warehouse_handling}/{status_id}", [
            WarehouseHandling\DashboardController::class,
            "packing_form_list"
        ])->name("packing_form_list");

    });

    Route::name("new_handling.")->prefix("/new_handling")->group(function () {
        Route::get("index", [
            WarehouseHandling\NewHandlingController::class,
            "index"
        ])->name("index");
        Route::post("submit", [
            WarehouseHandling\NewHandlingController::class,
            "submit"
        ])->name("submit");

    });

    Route::name("add_packing_form.")->prefix("/add_packing_form")->group(function () {

//        Route::get("index/{warehouse_handling}", [
//            WarehouseHandling\AddPackingFromController::class,
//            "index"
//        ])->name("index");
//        Route::get("packing_form_list/{warehouse_handling}", [
//            WarehouseHandling\AddPackingFromController::class,
//            "packing_form_list"
//        ])->name("packing_form_list");

    });


    Route::name("end_of_handling.")->prefix("/end_of_handling")->group(function () {

        Route::post("submit/{warehouse_handling}", [
            WarehouseHandling\EndOfHandlingController::class,
            "submit"
        ])->name("submit");
    });


    Route::name("end_of_review.")->prefix("/end_of_review")->group(function () {

        Route::post("submit/{warehouse_handling}", [
            WarehouseHandling\EndOfReviewController::class,
            "submit"
        ])->name("submit");
        Route::get("check_packing_form/{warehouse_handling}/{warehouse_handling_packing_form}", [
            WarehouseHandling\EndOfReviewController::class,
            "check_packing_form"
        ])->name("check_packing_form");
        Route::get("reading_packing_form_after/{warehouse_handling}/{warehouse_handling_packing_form}", [
            WarehouseHandling\EndOfReviewController::class,
            "reading_packing_form_after"
        ])->name("reading_packing_form_after");
        Route::post("submit_reading_packing_form_after/{warehouse_handling}/{warehouse_handling_packing_form}", [
            WarehouseHandling\EndOfReviewController::class,
            "submit_reading_packing_form_after"
        ])->name("submit_reading_packing_form_after");
    });

    Route::name("confirm_step1.")->prefix("/confirm_step1")->group(function () {

        Route::post("submit/{warehouse_handling}", [
            WarehouseHandling\ConfirmStep1Controller::class,
            "submit"
        ])->name("submit");
    });
    Route::name("confirm_step2.")->prefix("/confirm_step2")->group(function () {

        Route::post("submit/{warehouse_handling}", [
            WarehouseHandling\ConfirmStep2Controller::class,
            "submit"
        ])->name("submit");
    });
    Route::name("confirm_step3.")->prefix("/confirm_step3")->group(function () {

        Route::post("submit/{warehouse_handling}", [
            WarehouseHandling\ConfirmStep3Controller::class,
            "submit"
        ])->name("submit");
    });
    Route::name("reject.")->prefix("/reject")->group(function () {

        Route::post("submit/{warehouse_handling}", [
            WarehouseHandling\RejectController::class,
            "submit"
        ])->name("submit");
    });

    Route::name("print.")->prefix("/print")->group(function () {

        Route::get("print/{warehouse_handling}/{form}", [
            WarehouseHandling\PrintController::class,
            "print"
        ])->name("print");
        Route::get("download/{warehouse_handling}/{form}", [
            WarehouseHandling\PrintController::class,
            "download"
        ])->name("download");
        Route::get("index/{warehouse_handling}", [
            WarehouseHandling\PrintController::class,
            "index"
        ])->name("index");
    });

    Route::name("check_packing_form.")->prefix("/check_packing_form")->group(function () {

        Route::get("index/{warehouse_handling}", [
            WarehouseHandling\CheckPackingFormController::class,
            "index"
        ])->name("index");
        Route::post("submit/{warehouse_handling}", [
            WarehouseHandling\CheckPackingFormController::class,
            "submit"
        ])->name("submit");
    });


});
########################### pallet
Route::name("pallet.")->prefix("/pallet")->group(function () {

    Route::name("dashboard.")->prefix("/dashboard")->group(function () {
        Route::match(['get', 'post'], "index", [
            Pallet\DashboardController::class,
            "index"
        ])->name("index");

        Route::get("view/{pallet}", [
            Pallet\DashboardController::class,
            "view"
        ])->name("view");

        Route::get("print/{pallet}/{packingTypeLabelPrintingType}", [
            Pallet\DashboardController::class,
            "print"
        ])->name("print");

        Route::get("download/{pallet}/{packingTypeLabelPrintingType}", [
            Pallet\DashboardController::class,
            "download"
        ])->name("download");

    });
    Route::name("add_packing_form.")->prefix("/add_packing_form")->group(function () {


        Route::get("index/{pallet}", [
            Pallet\AddPackingFormController::class,
            "index"
        ])->name("index");

        Route::post("submit/{pallet}", [
            Pallet\AddPackingFormController::class,
            "print"
        ])->name("print");

        Route::get("packing_forms/{pallet}", [
            Pallet\AddPackingFormController::class,
            "packing_forms"
        ])->name("packing_forms");

        Route::get("show_list/{pallet}", [
            Pallet\AddPackingFormController::class,
            "show_list"
        ])->name("show_list");

        Route::post("confirm/{pallet}", [
            Pallet\AddPackingFormController::class,
            "confirm"
        ])->name("confirm");

    });
    Route::name("remove_packing_form.")->prefix("/remove_packing_form")->group(function () {


        Route::get("index/{pallet}", [
            Pallet\RemovePackingFormController::class,
            "index"
        ])->name("index");

        Route::post("submit/{pallet}", [
            Pallet\RemovePackingFormController::class,
            "print"
        ])->name("print");

        Route::get("packing_forms/{pallet}", [
            Pallet\RemovePackingFormController::class,
            "packing_forms"
        ])->name("packing_forms");

        Route::get("show_list/{pallet}", [
            Pallet\RemovePackingFormController::class,
            "show_list"
        ])->name("show_list");

        Route::post("confirm/{pallet}", [
            Pallet\RemovePackingFormController::class,
            "confirm"
        ])->name("confirm");

    });


});
