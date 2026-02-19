<?php

use App\Http\Controllers\Contractor\Definition;
use App\Http\Controllers\Contractor\Admin;
use App\Http\Controllers\Contractor\Panel;
use App\Http\Controllers\Contractor\Report;
use App\Http\Controllers\Contractor;

Route::prefix('agent')->name("agent.")->group(function () {
    Route::get("index", [Contractor\AgentController::class, "index"])->name("index");
    Route::get("create", [Contractor\AgentController::class, "create"])->name("create");
    Route::post("submit", [Contractor\AgentController::class, "submit"])->name("submit");
});
# Loading
Route::middleware(['url_check:contractor.definition.dashboard.index'])->prefix('definition')->name("definition.")->group(function () {


        #default
    Route::prefix('default')->name("default.")->group(function () {
        Route::get("index", [Definition\DefaultController::class, "index"])->name("index");
        Route::post("submit", [Definition\DefaultController::class, "submit"])->name("submit");
    });
    # Dashboard
    Route::prefix('dashboard')->name("dashboard.")->group(function () {
        Route::get("index", [Definition\DashboardController::class, "index"])->name("index");

        Route::get("create", [Definition\DashboardController::class, "create"])->name("create");
        Route::post("store", [Definition\DashboardController::class, "store"])->name("store");

        Route::get("edit/{contractor}", [Definition\DashboardController::class, "edit"])->name("edit");
        Route::post("update/{contractor}", [Definition\DashboardController::class, "update"])->name("update");
        Route::get( "edit_software_system/{contractor}", [Definition\DashboardController::class, "edit_software_system"] )->name( "edit_software_system" );
        Route::post( "update_software_system/{contractor}", [Definition\DashboardController::class, "update_software_system"] )->name( "update_software_system" );
    });

    # Operation
    Route::prefix('operation')->name("operation.")->group(function () {
        Route::get("index/{contractor}", [Definition\OperationController::class, "index"])->name("index");

        Route::get("create/{contractor}", [Definition\OperationController::class, "create"])->name("create");
        Route::post("store/{contractor}", [Definition\OperationController::class, "store"])->name("store");

        Route::get("edit/{contractor}/{contractor_operation}", [
            Definition\OperationController::class,
            "edit"
        ])->name("edit");
        Route::post("update/{contractor_operation}", [
            Definition\OperationController::class,
            "update"
        ])->name("update");
    });
    # Property
    Route::prefix('property')->name("property.")->group(function () {
        Route::get("index/{contractor}", [Definition\PropertyController::class, "index"])->name("index");
        Route::post("update/{contractor}", [Definition\PropertyController::class, "update"])->name("update");

    });
});

Route::middleware(['url_check:contractor.admin.dashboard.index'])->prefix('admin')->name("admin.")->group(function () {

    # Dashboard
    Route::prefix('dashboard')->name("dashboard.")->group(function () {
        Route::match(['get', 'post'], "index", [Admin\DashboardController::class, "index"])->name("index");
        Route::get("view_card/{production}/{back_url_type?}", [Admin\DashboardController::class, "view_card"])->name("view_card");
        Route::get("log/{contractor_allocation}", [Admin\DashboardController::class, "log"])->name("log");
        Route::get("view_form/{contractor_allocation}/{form}", [Admin\DashboardController::class, "view_form"])->name("view_form");

    });
    # ContractorAllocation
    Route::prefix('contractor_allocation')->name("contractor_allocation.")->group(function () {
        Route::get("index/{production}/{contractor?}/{production_channel_type?}", [Admin\ContractorAllocationController::class, "index"])->name("index");
        Route::get("show_product_inventory/{production}/{other_production_id}/{product}/{contractor}/{production_channel_type}", [Admin\ContractorAllocationController::class, "show_product_inventory"])->name("show_product_inventory");
        Route::post("submit_show_product_inventory/{production}/{production_channel_type}", [Admin\ContractorAllocationController::class, "submit_show_product_inventory"])->name("submit_show_product_inventory");
        Route::get("remove_packing_forms/{production}/{packing_form}/{production_channel_type}", [Admin\ContractorAllocationController::class, "remove_packing_forms"])->name("remove_packing_forms");
//        Route::get("select_packing_forms/{production}/{contractor}", [Admin\ContractorAllocationController::class, "select_packing_forms"])->name("select_packing_forms");
        Route::post("submit/{production}", [
            Admin\ContractorAllocationController::class,
            "submit"
        ])->name("submit");

    });
    # ContractorAllocationQuick
    Route::prefix('contractor_allocation_quick')->name("contractor_allocation_quick.")->group(function () {
        Route::get("index/{contractor?}", [Admin\ContractorAllocationQuickController::class, "index"])->name("index");
        Route::post("submit", [Admin\ContractorAllocationQuickController::class, "submit"])->name("submit");
        Route::get("select_packing_forms/{contractor}/{production_channel_type}", [Admin\ContractorAllocationQuickController::class, "select_packing_forms"])->name("select_packing_forms");
        Route::get("show_selected_packing/{contractor}/{production_channel_type}", [Admin\ContractorAllocationQuickController::class, "show_selected_packing"])->name("show_selected_packing");
        Route::get("show_selected_packing_by_packing_form/{contractor}/{production_channel_type}", [Admin\ContractorAllocationQuickController::class, "show_selected_packing_by_packing_form"])->name("show_selected_packing_by_packing_form");
        Route::get("go_to_contractor_allocation/{contractor}/{production_channel_type}", [Admin\ContractorAllocationQuickController::class, "go_to_contractor_allocation"])->name("go_to_contractor_allocation");
        Route::get("remove_packing_forms/{contractor}/{production_channel_type}/{packing_form}", [Admin\ContractorAllocationQuickController::class, "remove_packing_forms"])->name("remove_packing_forms");


    });
    # ContractorTerminate
    Route::prefix('contractor_terminate')->name("contractor_terminate.")->group(function () {
        Route::get("index/{production}", [Admin\ContractorTerminateController::class, "index"])->name("index");

    });

    // ConfirmationOfFinancialUnitController
    Route::prefix('confirmation_of_financial_unit')->name("confirmation_of_financial_unit.")->group(function () {

        Route::get("index/{machine_allocation}/{form}", [Admin\ConfirmationOfFinancialUnitController::class, "index"])->name("index");
        Route::post("confirm_exist_form/{machine_allocation}/{form}", [Admin\ConfirmationOfFinancialUnitController::class, "confirm_exist_form"])->name("confirm_exist_form");
        Route::get("reject_exist_form/{machine_allocation}/{form}", [Admin\ConfirmationOfFinancialUnitController::class, "reject_exist_form"])->name("reject_exist_form");

    });

    // ConfirmationOfDraftFormController
    Route::prefix('confirmation_of_draft_form')->name("confirmation_of_draft_form.")->group(function () {

        Route::get("index/{machine_allocation}/{form}", [Admin\ConfirmationOfDraftFormController::class, "index"])->name("index");
        Route::post("confirm_exist_form/{machine_allocation}/{form}", [Admin\ConfirmationOfDraftFormController::class, "confirm_exist_form"])->name("confirm_exist_form");
        Route::get("reject_exist_form/{machine_allocation}/{form}", [Admin\ConfirmationOfDraftFormController::class, "reject_exist_form"])->name("reject_exist_form");

    });

});

Route::middleware(['url_check:contractor.panel.dashboard.index'])->prefix('panel')->name("panel.")->group(function () {

    # Dashboard
    Route::prefix('dashboard')->name("dashboard.")->group(function () {
        Route::match(['get', 'post'], "index", [Panel\DashboardController::class, "index"])->name("index");
        Route::get("view/{contractor_allocation}", [Panel\DashboardController::class, "view"])->name("view");
        Route::get("view_packing/{contractor_allocation}/{packing_form}", [
            Panel\DashboardController::class,
            "view_packing"
        ])->name("view_packing");
        Route::get("view_form/{contractor_allocation}/{form}", [
            Panel\DashboardController::class,
            "view_form"
        ])->name("view_form");
        Route::get("view_product_request_form/{contractor_allocation}/{product_request_form}", [
            Panel\DashboardController::class,
            "view_product_request_form"
        ])->name("view_product_request_form");

//        Route::get( "download_transport/{contractor_allocation}/{transport}", [
//            Panel\DashboardController::class,
//            "download_transport"
//        ] )->name( "download_transport" );
    });
    # EndOfCoordinationForSending
    Route::prefix('coordination_for_sending')->name("coordination_for_sending.")->group(function () {
        Route::get("index/{contractor_allocation}", [
            Panel\CoordinationForSendingController::class,
            "index"
        ])->name("index");
        Route::post("submit/{contractor_allocation}", [
            Panel\CoordinationForSendingController::class,
            "submit"
        ])->name("submit");
    });

    # ConfirmationOfReceiptOfProductController
    Route::prefix('confirmation_of_receipt_of_product')->name("confirmation_of_receipt_of_product.")->group(function () {
        Route::get("index/{contractor_allocation}/{product_request_form_form}/{form}", [
            Panel\ConfirmationOfReceiptOfProductController::class,
            "index"
        ])->name("index");
        Route::post("submit/{contractor_allocation}/{product_request_form_form}/{form}", [
            Panel\ConfirmationOfReceiptOfProductController::class,
            "submit"
        ])->name("submit");
    });

    # RegisterProductionController
    Route::prefix('register_production')->name("register_production.")->group(function () {
        Route::get("index/{machine_allocation}", [
            Panel\RegisterProductionController::class,
            "index"
        ])->name("index");

//        Route::post( "submit_register_production/{machine_allocation}", [
//            Panel\RegisterProductionController::class,
//            "submit_register_production"
//        ] )->name( "submit_register_production" );
//
//        Route::get( "register_production_and_send_to_warehouse/{machine_allocation}", [
//            Panel\RegisterProductionController::class,
//            "register_production_and_send_to_warehouse"
//        ] )->name( "register_production_and_send_to_warehouse" );
//
//        Route::get( "add_new_packing/{machine_allocation}/{packing_form}", [
//            Panel\RegisterProductionController::class,
//            "add_new_packing"
//        ] )->name( "add_new_packing" );
//
//        Route::get( "delete_packing_form/{machine_allocation}/{packing_form}", [
//            Panel\RegisterProductionController::class,
//            "delete_packing_form"
//        ] )->name( "delete_packing_form" );
//
//        Route::get( "sending_packing_form/{machine_allocation}", [
//            Panel\RegisterProductionController::class,
//            "sending_packing_form"
//        ] )->name( "sending_packing_form" );
//
//        Route::post( "submit_sending_packing_form/{machine_allocation}", [
//            Panel\RegisterProductionController::class,
//            "submit_sending_packing_form"
//        ] )->name( "submit_sending_packing_form" );
//
//
//        Route::get( "print_packing_form/{machine_allocation}/{packing_form}", [
//            Panel\RegisterProductionController::class,
//            "print_packing_form"
//        ] )->name( "print_packing_form" );
//
//
//        Route::get( "download_packing_form/{machine_allocation}/{packing_form}", [
//            Panel\RegisterProductionController::class,
//            "download_packing_form"
//        ] )->name( "download_packing_form" );
//
//        Route::get( "complete_information/{machine_allocation}/{machine_allocation_packing_form}", [
//            Panel\RegisterProductionController::class,
//            "complete_information"
//        ] )->name( "complete_information" );
//
//        Route::post( "submit_complete_information/{machine_allocation}/{machine_allocation_packing_form}", [
//            Panel\RegisterProductionController::class,
//            "submit_complete_information"
//        ] )->name( "submit_complete_information" );

    });

    Route::prefix('upload_packing_form')->name("upload_packing_form.")->group(function () {

        Route::get("index/{contractor_allocation}", [
            Panel\UploadPackingFormController::class,
            "index"
        ])->name("index");

        Route::post("submit/{contractor_allocation}", [
            Panel\UploadPackingFormController::class,
            "submit"
        ])->name("submit");

        Route::get("show_upload/{contractor_allocation}", [
            Panel\UploadPackingFormController::class,
            "show_upload"
        ])->name("show_upload");

        Route::get("upload_product/{contractor_allocation}", [
            Panel\UploadPackingFormController::class,
            "upload_product"
        ])->name("upload_product");
    });
    // Upload


    Route::name("send_to_employer.")->prefix("/send_to_employer")->group(function () {

        Route::get("index/{contractor_allocation}", [
            Panel\SendToEmployerController::class,
            "index"
        ])->name("index");

        Route::post("submit/{contractor_allocation}", [
            Panel\SendToEmployerController::class,
            "submit"
        ])->name("submit");

        Route::get("get_nosa_code/{contractor_allocation}", [
            Panel\SendToEmployerController::class,
            "get_nosa_code"
        ])->name("get_nosa_code");
        Route::post("submit_nosa_code/{contractor_allocation}", [
            Panel\SendToEmployerController::class,
            "submit_nosa_code"
        ])->name("submit_nosa_code");

    });


    # Print
    Route::prefix('print')->name("print.")->group(function () {

        Route::get("report_1/{contractor_allocation}", [
            Panel\PrintController::class,
            "report_1"
        ])->name("report_1");

        Route::get("allocation_card/{contractor_allocation}", [
            Panel\PrintController::class,
            "allocation_card"
        ])->name("allocation_card");

        Route::get("download_transport_card/{transport}/{random}", [
            Panel\PrintController::class,
            "download_transport_card"
        ])->name("download_transport_card");

    });

});


Route::prefix('report')->name("report.")->group(function () {

    Route::middleware(['url_check:contractor.report.report_1.index'])->prefix('report_1')->name("report_1.")->group(function () {
        Route::get("index", [
            Report\Report1Controller::class,
            "index"
        ])->name("index");
        Route::post("submit", [
            Report\Report1Controller::class,
            "submit"
        ])->name("submit");
    });
});
