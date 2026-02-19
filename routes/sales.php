<?php


use App\Http\Controllers\Sales\ReceiptOfReceivablesController;
use App\Http\Controllers\Sales;

# Loading
Route::prefix('loading.')->name("loading.")->group(function () {

    # Dashboard
    Route::prefix('dashboard')->name("dashboard.")->group(function () {

        Route::get("create_order_to_collection/{order}", [Sales\Loading\DashboardController::class, "create_order_to_collection"])->name("create_order_to_collection");
        Route::post("order_to_collection_submit/{order}", [Sales\Loading\DashboardController::class, "order_to_collection_submit"])->name("order_to_collection_submit");
    });
});

Route::prefix('confirmation_of_financial_unit')->name("confirmation_of_financial_unit.")->group(function () {

    Route::get("index/{order}/{form}", [Sales\ConfirmationOfFinancialUnitController::class, "index"])->name("index");
    Route::post("confirm_exist_form/{order}/{form}", [Sales\ConfirmationOfFinancialUnitController::class, "confirm_exist_form"])->name("confirm_exist_form");
    Route::get("reject_exist_form/{order}/{form}", [Sales\ConfirmationOfFinancialUnitController::class, "reject_exist_form"])->name("reject_exist_form");

});

Route::prefix('confirmation_of_demands_form')->name("confirmation_of_demands_form.")->group(function () {

    Route::get("index/{order}/{form}", [Sales\ConfirmationOfDemandsFormController::class, "index"])->name("index");
    Route::post("confirm_exist_form/{order}/{form}", [Sales\ConfirmationOfDemandsFormController::class, "confirm_exist_form"])->name("confirm_exist_form");
    Route::get("reject_exist_form/{order}/{form}", [Sales\ConfirmationOfDemandsFormController::class, "reject_exist_form"])->name("reject_exist_form");

});
Route::prefix('confirmation_of_draft_form')->name("confirmation_of_draft_form.")->group(function () {

    Route::get("index/{order}/{form}", [Sales\ConfirmationOfDraftFormController::class, "index"])->name("index");
    Route::post("confirm_exist_form/{order}/{form}", [Sales\ConfirmationOfDraftFormController::class, "confirm_exist_form"])->name("confirm_exist_form");
    Route::get("reject_exist_form/{order}/{form}", [Sales\ConfirmationOfDraftFormController::class, "reject_exist_form"])->name("reject_exist_form");

});
Route::prefix('confirmation_of_customer_form')->name("confirmation_of_customer_form.")->group(function () {

    Route::get("index/{order}/{form}", [Sales\ConfirmationOfCustomerFormController::class, "index"])->name("index");
    Route::post("confirm_exist_form/{order}/{form}", [Sales\ConfirmationOfCustomerFormController::class, "confirm_exist_form"])->name("confirm_exist_form");
    Route::get("reject_exist_form/{order}/{form}", [Sales\ConfirmationOfCustomerFormController::class, "reject_exist_form"])->name("reject_exist_form");

});

Route::middleware(['url_check:sales.dashboard.index'])->prefix('production_processing')->name("production_processing.")->group(function () {

    Route::get("index/{order_list}", [Sales\ProductionProcessingController::class, "index"])->name("index");
    Route::post("submit/{order_list}", [Sales\ProductionProcessingController::class, "submit"])->name("submit");
    Route::post("submit_3/{order_list}", [Sales\ProductionProcessingController::class, "submit_3"])->name("submit_3");
    Route::post("submit_over_production/{order_list}", [Sales\ProductionProcessingController::class, "submit_over_production"])->name("submit_over_production");

});



Route::middleware(['url_check:sales.dashboard.index'])->prefix( 'sending_material' )->name( "sending_material." )->group( function () {

    Route::get( "index/{order}/{product}", [ Sales\SendingMaterialController::class, "index" ] )->name( "index" );
    Route::get( "show_allocation/{order}/{machine_allocation}", [ Sales\SendingMaterialController::class, "show_allocation" ] )->name( "show_allocation" );

} );

Route::middleware(['url_check:sales.dashboard.index'])->prefix('dashboard')->name("dashboard.")->group(function () {

    Route::match(['get', 'post'], "index/", [Sales\DashboardController::class, "index"])->name("index");
    Route::get("view_order/{order}", [Sales\DashboardController::class, "view_order"])->name("view_order");
    Route::get("view_form/{order}/{form}", [Sales\DashboardController::class, "view_form"])->name("view_form");
    Route::get("view_leads_in_warehouse/{order}", [Sales\DashboardController::class, "view_leads_in_warehouse"])->name("view_leads_in_warehouse");
    Route::get("view_reject_product_form/{order}/{reject_product_form}", [Sales\DashboardController::class, "view_reject_product_form"])->name("view_reject_product_form");

    Route::get("view_product_request_form/{order}/{product_request_form}/{back_type?}/{q1?}/{q2?}", [Sales\DashboardController::class, "view_product_request_form"])->name("view_product_request_form");
    Route::post("reject/{order}/{reject_type}", [Sales\DashboardController::class, "reject"])->name("reject");
    Route::post("terminate_order/{order}", [Sales\DashboardController::class, "terminate_order"])->name("terminate_order");
    Route::post("confirm/{order}", [Sales\DashboardController::class, "confirm"])->name("confirm");
    Route::post("special_off/{order}", [Sales\DashboardController::class, "special_off"])->name("special_off");
    Route::post("loading_permission/{order}", [Sales\DashboardController::class, "loading_permission"])->name("loading_permission");
    Route::get("log/{order}", [Sales\DashboardController::class, "log"])->name("log");
    Route::post("confirm_receipt_from_customer/{order}", [Sales\DashboardController::class, "confirm_receipt_from_customer"])->name("confirm_receipt_from_customer");
    Route::get("receipt_from_customer/{order}", [Sales\DashboardController::class, "receipt_from_customer"])->name("receipt_from_customer");
    Route::get("register_xml/{order}", [Sales\DashboardController::class, "register_xml"])->name("register_xml");
    Route::post("register_xml_submit/{order}", [Sales\DashboardController::class, "register_xml_submit"])->name("register_xml_submit");
    Route::get("download_form/{order}/{form}/{packing_type_label_printing_type}/{print_type?}", [Sales\DashboardController::class, "download_form"])->name("download_form");
    Route::get("print_product_request_form/{order}/{product_request_form}/{packing_type_label}/{print_type}", [Sales\DashboardController::class, "print_product_request_form"])->name("print_product_request_form");

    Route::get("will_be_processed_later/{order}/", [Sales\DashboardController::class, "will_be_processed_later"])->name("will_be_processed_later");
    Route::get("register_processed_later/{order}/", [Sales\DashboardController::class, "register_processed_later"])->name("register_processed_later");
    Route::get("change_processed_by_script/{order}/", [Sales\DashboardController::class, "change_processed_by_script"])->name("change_processed_by_script");

    Route::prefix('reject_product')->name("reject_product.")->group(function () {

        Route::get("index/{order}/{form}", [Sales\RejectProductController::class, "index"])->name("index");
        Route::post("step1/{order}/{form}", [Sales\RejectProductController::class, "step1"])->name("step1");
        Route::post("confirm/{order}/{form}", [Sales\RejectProductController::class, "confirm"])->name("confirm");
    });

});



Route::middleware(['url_check:sales.dashboard.index'])->prefix('product_request_permission')->name("product_request_permission.")->group(function () {

    Route::get("index/{order}", [Sales\ProductRequestPermissionController::class, "index"])->name("index");
    Route::get("index_product/{order}", [Sales\ProductRequestPermissionController::class, "index_product"])->name("index_product");
    Route::match(['get', 'post'],"index_customer/{customer_id}", [Sales\ProductRequestPermissionController::class, "index_customer"])->name("index_customer");
    Route::get("create/{order}", [Sales\ProductRequestPermissionController::class, "create"])->name("create");
    Route::post("submit/{order}", [Sales\ProductRequestPermissionController::class, "submit"])->name("submit");
    Route::get("show_permission/{order}", [Sales\ProductRequestPermissionController::class, "show_permission"])->name("show_permission");
    Route::post("confirm/{order}", [Sales\ProductRequestPermissionController::class, "confirm"])->name("confirm");
    Route::get("get_other_customer_permission/{order}/{product}", [Sales\ProductRequestPermissionController::class, "get_other_customer_permission"])->name("get_other_customer_permission");
    Route::get("get_other_customer_order/{order}/{product}", [Sales\ProductRequestPermissionController::class, "get_other_customer_order"])->name("get_other_customer_order");
    Route::get("get_current_delivery/{order}/{product}", [Sales\ProductRequestPermissionController::class, "get_current_delivery"])->name("get_current_delivery");
    Route::get("get_other_order/{order}/{product}", [Sales\ProductRequestPermissionController::class, "get_other_order"])->name("get_other_order");
    Route::get("remove_product_request_form_item/{product_request_form}/{product}/{back_type}/{back_order_id?}", [Sales\ProductRequestPermissionController::class, "remove_product_request_form_item"])->name("remove_product_request_form_item");
    Route::get("remove_product_request_form/{product_request_form}/{product_id}/{back_type}", [Sales\ProductRequestPermissionController::class, "remove_product_request_form"])->name("remove_product_request_form");
    Route::get("remove_permission_order_list/{order_list}/{product}", [Sales\ProductRequestPermissionController::class, "remove_permission_order_list"])->name("remove_permission_order_list");
    Route::get("show_packing_form_inventory/{order_list}/{product}/{type}", [Sales\ProductRequestPermissionController::class, "show_packing_form_inventory"])->name("show_packing_form_inventory");
    Route::get( "address_edit/{order}/{address}", [
        Sales\ProductRequestPermissionController::class,
        "address_edit"
    ] )->name( "address_edit" );
    Route::post( "address_edit_submit/{order}/{address}", [
        Sales\ProductRequestPermissionController::class,
        "address_edit_submit"
    ] )->name( "address_edit_submit" );
});



Route::middleware(['url_check:sales.dashboard.index'])->prefix('product_request_permission_test')->name("product_request_permission_test.")->group(function () {

    Route::get("index/{order}", [Sales\ProductRequestPermissionControllerTest::class, "index"])->name("index");
    Route::get("index_product/{order}", [Sales\ProductRequestPermissionControllerTest::class, "index_product"])->name("index_product");
    Route::get("index_customer/{customer_id}", [Sales\ProductRequestPermissionControllerTest::class, "index_customer"])->name("index_customer");
    Route::get("create/{order}", [Sales\ProductRequestPermissionControllerTest::class, "create"])->name("create");
    Route::post("submit/{order}", [Sales\ProductRequestPermissionControllerTest::class, "submit"])->name("submit");
    Route::get("show_permission/{order}", [Sales\ProductRequestPermissionControllerTest::class, "show_permission"])->name("show_permission");
    Route::post("confirm/{order}", [Sales\ProductRequestPermissionControllerTest::class, "confirm"])->name("confirm");
    Route::get("get_other_customer_permission/{order}/{product}", [Sales\ProductRequestPermissionControllerTest::class, "get_other_customer_permission"])->name("get_other_customer_permission");
    Route::get("get_other_customer_order/{order}/{product}", [Sales\ProductRequestPermissionControllerTest::class, "get_other_customer_order"])->name("get_other_customer_order");
    Route::get("get_other_order/{order}/{product}", [Sales\ProductRequestPermissionControllerTest::class, "get_other_order"])->name("get_other_order");
    Route::get("remove_product_request_form_item/{product_request_form}/{product}/{back_type}/{back_order_id?}", [Sales\ProductRequestPermissionControllerTest::class, "remove_product_request_form_item"])->name("remove_product_request_form_item");
    Route::get("remove_product_request_form/{product_request_form}/{product_id}/{back_type}", [Sales\ProductRequestPermissionControllerTest::class, "remove_product_request_form"])->name("remove_product_request_form");
    Route::get("remove_permission_order_list/{order_list}/{product}", [Sales\ProductRequestPermissionControllerTest::class, "remove_permission_order_list"])->name("remove_permission_order_list");
    Route::get("show_packing_form_inventory/{order_list}/{product}/{type}", [Sales\ProductRequestPermissionControllerTest::class, "show_packing_form_inventory"])->name("show_packing_form_inventory");
    Route::get( "address_edit/{order}/{address}", [
        Sales\ProductRequestPermissionControllerTest::class,
        "address_edit"
    ] )->name( "address_edit" );
    Route::post( "address_edit_submit/{order}/{address}", [
        Sales\ProductRequestPermissionControllerTest::class,
        "address_edit_submit"
    ] )->name( "address_edit_submit" );
});



Route::middleware(['url_check:sales.dashboard.index'])->prefix('print')->name("print.")->group(function () {

    Route::get("register_xml_download/{order}", [Sales\PrintController::class, "register_xml_download"])->name("register_xml_download");
    Route::get("factor/{order}", [Sales\PrintController::class, "factor"])->name("factor");
    Route::get("exit_form_factor/{order}/{form}", [Sales\PrintController::class, "exit_form_factor"])->name("exit_form_factor");
    Route::get("exit_form_pre_factor/{order}/{form}", [Sales\PrintController::class, "exit_form_pre_factor"])->name("exit_form_pre_factor");

});

Route::prefix('customer')->name("customer.")->group(function () {

    Route::match(['get', 'post'], "index/", [
        Sales\CustomerController::class,
        "index"
    ])->name("index");

    Route::match(['get', 'post'], "orders/{customer}", [
        Sales\CustomerController::class,
        "orders"
    ])->name("orders");

    Route::get("new_order_for_customer/{customer}", [
        Sales\CustomerController::class,
        "new_order_for_customer"
    ])->name("new_order_for_customer");
});

Route::middleware(['url_check:sales.receipt_of_receivables.list'])->prefix('receipt_of_receivables')->name("receipt_of_receivables.")->group(function () {

    Route::match(['get', 'post'], "list/", [ReceiptOfReceivablesController::class, "list"])->name("list");

    Route::get("exit_permission/{order}", [
        ReceiptOfReceivablesController::class,
        "exit_permission"
    ])->name("exit_permission");

    Route:: get("finished_order/{order}", [
        ReceiptOfReceivablesController::class,
        "finished_order"
    ])->name("finished_order");
});

Route::middleware(['url_check:sales.setting.index'])->prefix('setting')->name("setting.")->group(function () {

    Route::get("index", [Sales\SettingController::class, "index"])->name("index");
    Route::post("submit", [Sales\SettingController::class, "submit"])->name("submit");
    Route::post("submit_formal_status", [Sales\SettingController::class, "submit_formal_status"])->name("submit_formal_status");
    Route::post("submit_planing_status", [Sales\SettingController::class, "submit_planing_status"])->name("submit_planing_status");


});

 Route::middleware(['url_check:sales.loading_implementation.index'])->prefix('loading_implementation')->name("loading_implementation.")->group(function () {

        Route::get("index/{customer_id?}", [Sales\LoadingImplementationController::class, "index"])->name("index");
        Route::post("submit", [Sales\LoadingImplementationController::class, "submit"])->name("submit");

        Route::get("confirm", [Sales\LoadingImplementationController::class, "confirm"])->name("confirm");
        Route::post("submit_confirm", [Sales\LoadingImplementationController::class, "submit_confirm"])->name("submit_confirm");

        Route::get("delete/{product_id}", [Sales\LoadingImplementationController::class, "delete"])->name("delete");
       


 });

