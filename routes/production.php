<?php

use App\Http\Controllers\Production;


//Dashboard
Route::prefix('a46/ldj/lasdfsefie/dashboard')->name("dashboard.")->group(function () {

    Route::middleware(['url_check:production.dashboard.index_details'])->match(['get', 'post'], "index_details", [Production\DashboardController::class, "index_details"])->name("index_details");
    Route::middleware(['url_check:production.dashboard.list,production.dashboard.index_details'])->match(['get', 'post'], "index", [Production\DashboardController::class, "index"])->name("list");
    Route::middleware(['url_check:production.dashboard.list,production.dashboard.index_details'])->match(['get'], "download_excel/{queue_of_large_operation}", [Production\DashboardController::class, "download_excel"])->name("download_excel");

    Route::middleware(['url_check:production.dashboard.list,production.dashboard.change_allocation'])->
    match(['get', 'post'], "change_allocation/{allocation}/{machine}", [Production\DashboardController::class, "change_allocation"])->
    name("change_allocation");


});
Route::middleware(['url_check:production.dashboard.list,production.dashboard.index_details'])->prefix('a46/ldj/lasdfsefie/dashboard')->name("dashboard.")->group(function () {

    Route::get("view_card/{production}/{back_url_type?}", [Production\DashboardController::class, "view_card"])->name("view_card");
    Route::get("machine_allocation/{production}", [Production\DashboardController::class, "machine_allocation"])->name("machine_allocation");
    Route::get("reallocation/{production}/{machine_allocation}", [Production\DashboardController::class, "reallocation"])->name("reallocation");
    Route::get("allocation_cancel/{allocation}/{production}", [Production\DashboardController::class, "allocation_cancel"])->name("allocation_cancel");

    Route::get("print_card/{production}", [
        Production\DashboardController::class,
        "print_card"
    ])->name("print_card");

    Route::get("production_500", [
        Production\DashboardController::class,
        "production_500"
    ])->name("export.production_500");

//        Route::get("request_material/{production}", [ProductionDashboardController::class, "request_material"])->name("request_material");
//        Route::post("request_material_submit/{production}", [ProductionDashboardController::class, "request_material_submit"])->name("request_material_submit");
//        Route::get("request_material_result/{production}", [ProductionDashboardController::class, "request_material_result"])->name("request_material_result");
//        Route::get("confirm_material_form/{production}", [ProductionDashboardController::class, "confirm_material_form"])->name("confirm_material_form");
//        Route::post("confirm_material_form_submit/{production}/{form}", [ProductionDashboardController::class, "confirm_material_form_submit"])->name("confirm_material_form_submit");
//        Route::get("reject_material_form/{production}", [ProductionDashboardController::class, "reject_material_form"])->name("reject_material_form");
//        Route::get("confirm_quality_control/{production}", [ProductionDashboardController::class, "confirm_quality_control"])->name("confirm_quality_control");
//        Route::get("confirm_delivery_to_warehouse/{production}", [ProductionDashboardController::class, "confirm_delivery_to_warehouse"])->name("confirm_delivery_to_warehouse");
//
//        Route::get("edit_number_product/{production}", [ProductionDashboardController::class, "edit_number_product"])->name("edit_number_product");
//        Route::post("edit_number_product_submit/{production}", [ProductionDashboardController::class, "edit_number_product_submit"])->name("edit_number_product_submit");
//
//        Route::get("confirm_warehouse/{production}", [ProductionDashboardController::class, "confirm_warehouse"])->name("confirm_warehouse");
//        Route::post("confirm_warehouse_submit/{production}", [ProductionDashboardController::class, "confirm_warehouse_submit"])->name("confirm_warehouse_submit");
//        Route::get("confirm_warehouse_submit_show/{production}", [ProductionDashboardController::class, "confirm_warehouse_submit_show"])->name("confirm_warehouse_submit_show");
//        Route::post("confirm_warehouse_form_submit/{production}", [ProductionDashboardController::class, "confirm_warehouse_form_submit"])->name("confirm_warehouse_form_submit");
//        Route::get("reject_warehouse/{production}", [ProductionDashboardController::class, "reject_warehouse"])->name("reject_warehouse");
//
//######### ثبت کارت تولید
//        Route::get("form1/{production}", [ProductionDashboardController::class, "form1"])->name("form1");
//        Route::post("submit_form1/{production}", [ProductionDashboardController::class, "submit_form1"])->name("submit_form1");
//
//
//        Route::get('/datetime/{production}/{version}/{action?}', [ProductionDashboardController::class, "datetime"])->name('datetime.create');
//        Route::post('/datetime_store/{production}/{version}/{action?}', [ProductionDashboardController::class, "datetime_store"])->name('datetime.store');
//        Route::get('/datetime_delete/{production}/{version}', [ProductionDashboardController::class, "datetime_delete"])->name('datetime.delete');
//
//        Route::get('replace/{production}', [ProductionDashboardController::class, "replace"])->name('replace');
//        Route::post('submit_replace/{production}', [ProductionDashboardController::class, "submit_replace"])->name('submit_replace');
//        Route::post('submit_confirm_replace/{production}', [ProductionDashboardController::class, "submit_confirm_replace"])->name('submit_confirm_replace');
//
//
//        Route::get('cancel/{production}', [ProductionDashboardController::class, "cancel"])->name('cancel');
//        Route::post("submit_cancel/{production}", [ProductionDashboardController::class, "submit_cancel"])->name("submit_cancel");
//
//        Route::get('edit/{production}', [ProductionDashboardController::class, "edit"])->name('edit');
//        Route::post("submit_edit/{production}", [ProductionDashboardController::class, "submit_edit"])->name("submit_edit");
//
//        Route::get('confirm/{production}', [ProductionDashboardController::class, "confirm"])->name('confirm');
//        Route::post("submit_confirm/{production}", [ProductionDashboardController::class, "submit_confirm"])->name("submit_confirm");
//
//        Route::get('result_confirm/{production}', [ProductionDashboardController::class, "result_confirm"])->name('result_confirm');
//        Route::post('replace_group/{production}', [ProductionDashboardController::class, "replace_group"])->name('replace_group');

});

//Production Form
Route::middleware(['url_check:production.production_form.index'])->prefix('dgff/kug/dgfsefie/production_form')->name("production_form.")->group(function () {
    Route::match(['get', 'post'], "index", [Production\ProductionFormController::class, "index"])->name("index");
    Route::get("view/{production_form}", [Production\ProductionFormController::class, "view"])->name("view");
});

//Machine
Route::middleware(['url_check:production.machine.index'])->prefix('a4rtg/ldert/gfsefie/machine')->name("machine.")->group(function () {
    Route::match(['get', 'post'], "index", [Production\MachineController::class, "index"])->name("index");
    Route::match(['get', 'post'], "index_1", [Production\MachineController::class, "index_1"])->name("index_1");
    Route::get("view/{machine}", [Production\MachineController::class, "view"])->name("view");
    Route::get("short_link/{machine}", [Production\MachineController::class, "short_link"])->name("short_link");
});


# M 1: RegisterProductionController
Route::prefix('public_module/register_production')->name("public_module.register_production.")->group(function () {
    Route::get("index/{machine_allocation}", [
        Production\PublicModule\RegisterProductionController::class,
        "index"
    ])->name("index");

    Route::post("submit_register_production/{machine_allocation}/{source_production_form_item_id}", [
        Production\PublicModule\RegisterProductionController::class,
        "submit_register_production"
    ])->name("submit_register_production");

    Route::get("register_production_and_send_to_warehouse/{machine_allocation}/{source_production_form_item_id}", [
        Production\PublicModule\RegisterProductionController::class,
        "register_production_and_send_to_warehouse"
    ])->name("register_production_and_send_to_warehouse");

    Route::get("add_new_packing/{machine_allocation}/{packing_form}/{source_production_form_item_id}", [
        Production\PublicModule\RegisterProductionController::class,
        "add_new_packing"
    ])->name("add_new_packing");

    Route::get("delete_packing_form/{machine_allocation}/{packing_form}", [
        Production\PublicModule\RegisterProductionController::class,
        "delete_packing_form"
    ])->name("delete_packing_form");

    Route::get("delete_form_general_item/{machine_allocation}/{form_general_item}", [
        Production\PublicModule\RegisterProductionController::class,
        "delete_form_general_item"
    ])->name("delete_form_general_item");

    Route::get("sending_packing_form/{machine_allocation}", [
        Production\PublicModule\RegisterProductionController::class,
        "sending_packing_form"
    ])->name("sending_packing_form");

    Route::post("submit_sending_packing_form/{machine_allocation}", [
        Production\PublicModule\RegisterProductionController::class,
        "submit_sending_packing_form"
    ])->name("submit_sending_packing_form");


    Route::get("print_packing_form/{machine_allocation}/{packing_form}", [
        Production\PublicModule\RegisterProductionController::class,
        "print_packing_form"
    ])->name("print_packing_form");


    Route::get("download_packing_form/{machine_allocation}/{packing_form}", [
        Production\PublicModule\RegisterProductionController::class,
        "download_packing_form"
    ])->name("download_packing_form");

    Route::get("terminate_production/{machine_allocation}/{type_of_confirm}", [
        Production\PublicModule\RegisterProductionController::class,
        "terminate_production"
    ])->name("terminate_production");

    Route::post("submit_terminate_production/{machine_allocation}", [
        Production\PublicModule\RegisterProductionController::class,
        "submit_terminate_production"
    ])->name("submit_terminate_production");

    Route::get("complete_information/{machine_allocation}/{machine_allocation_packing_form}/{source_production_form_item_id}", [
        Production\PublicModule\RegisterProductionController::class,
        "complete_information"
    ])->name("complete_information");

    Route::post("submit_complete_information/{machine_allocation}/{machine_allocation_packing_form}/{source_production_form_item_id}", [
        Production\PublicModule\RegisterProductionController::class,
        "submit_complete_information"
    ])->name("submit_complete_information");

    /* افزودن بدون جزئیات بسته بندی */
    Route::get("add_without_details/{machine_allocation}/{other_machine_allocation_id?}", [
        Production\PublicModule\RegisterProductionController::class,
        "add_without_details"
    ])->name("add_without_details");
    Route::post("submit_without_details/{machine_allocation}/{other_machine_allocation_id?}", [
        Production\PublicModule\RegisterProductionController::class,
        "submit_without_details"
    ])->name("submit_without_details");

    Route::get("add_from_order_packing_form/{machine_allocation}", [
        Production\PublicModule\RegisterProductionController::class,
        "add_from_order_packing_form"
    ])->name("add_from_order_packing_form");

    Route::post("submit_add_from_order_packing_form/{machine_allocation}", [
        Production\PublicModule\RegisterProductionController::class,
        "submit_add_from_order_packing_form"
    ])->name("submit_add_from_order_packing_form");

    Route::get("end_of_source_production_form_item/{machine_allocation}/{source_production_form_item_id}", [
        Production\PublicModule\RegisterProductionController::class,
        "end_of_source_production_form_item"
    ])->name("end_of_source_production_form_item");

#copy


    Route::get("copy_packing_form/{machine_allocation}/{packing_form}", [
        Production\PublicModule\RegisterProductionController::class,
        "copy_packing_form"
    ])->name("copy_packing_form");


    Route::post("submit_copy_packing_form/{machine_allocation}/{packing_form}", [
        Production\PublicModule\RegisterProductionController::class,
        "submit_copy_packing_form"
    ])->name("submit_copy_packing_form");


});


Route::prefix('public_module/upload')->name("public_module.upload.")->group(function () {
    Route::get("index/{machine_allocation}", [
        Production\PublicModule\UploadController::class,
        "index"
    ])->name("index");

    Route::post("submit/{machine_allocation}", [
        Production\PublicModule\UploadController::class,
        "submit"
    ])->name("submit");

    Route::get("preview/{machine_allocation}", [
        Production\PublicModule\UploadController::class,
        "preview"
    ])->name("preview");

    Route::post("confirm/{machine_allocation_parent}", [
        Production\PublicModule\UploadController::class,
        "confirm"
    ])->name("confirm");


});


