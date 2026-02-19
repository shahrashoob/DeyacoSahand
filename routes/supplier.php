<?php

use App\Http\Controllers\Supplier\Definition;
use App\Http\Controllers\Supplier\Admin;
use App\Http\Controllers\Supplier;




Route::prefix('agent')->name("agent.")->group(function () {
    Route::get("index", [Supplier\AgentController::class, "index"])->name("index");
    Route::get("create", [Supplier\AgentController::class, "create"])->name("create");
    Route::post("submit", [Supplier\AgentController::class, "submit"])->name("submit");
});
Route::middleware(['url_check:supplier.definition.dashboard.index'])->prefix('definition')->name("definition.")->group(function () {

    # Dashboard
    Route::prefix('dashboard')->name("dashboard.")->group(function () {
        Route::get("index", [Definition\DashboardController::class, "index"])->name("index");

        Route::get("create", [Definition\DashboardController::class, "create"])->name("create");
        Route::post("store", [Definition\DashboardController::class, "store"])->name("store");

        Route::get("edit/{supplier}", [Definition\DashboardController::class, "edit"])->name("edit");
        Route::post("update/{supplier}", [Definition\DashboardController::class, "update"])->name("update");
    });
    #Default
    Route::prefix('default')->name("default.")->group(function () {
        Route::get("index", [Definition\DefaultController::class, "index"])->name("index");
        Route::post("submit", [Definition\DefaultController::class, "submit"])->name("submit");
    });
});

Route::middleware(['url_check:supplier.admin.dashboard.index'])->prefix('admin')->name("admin.")->group(function () {

    # Dashboard
    Route::prefix('dashboard')->name("dashboard.")->group(function () {
        Route::get("index", [Definition\DashboardController::class, "index"])->name("index");

    });

});

Route::prefix('admin')->name("admin.")->group(function () {

    # Dashboard
    Route::middleware(['url_check:supplier.admin.dashboard.index'])->prefix('dashboard')->name("dashboard.")->group(function () {

        Route::match(['get', 'post'], "index", [
            Admin\DashboardController::class,
            "index"
        ])->name("index");
        Route::get("view/{machine_allocation}", [Admin\DashboardController::class, "view"])->name("view");

    });

    # Print
    Route::middleware(['url_check:supplier.admin.dashboard.index'])->prefix('print')->name("print.")->group(function () {

        Route::get("download_transport_card/{transport}/{random}", [
            Admin\PrinterController::class,
            "download_transport_card"
        ])->name("download_transport_card");
    });

    Route::middleware(['url_check:supplier.admin.supplier_register.index'])->prefix('supplier_register')->name("supplier_register.")->group(function () {

        Route::get("index", [Admin\SupplierRegisterController::class, "index"])->name("index");
       
        Route::post("submit", [Admin\SupplierRegisterController::class, "submit"])->name("submit");

        Route::get("confirm", [Admin\SupplierRegisterController::class, "confirm"])->name("confirm");
        Route::post("submit_confirm", [Admin\SupplierRegisterController::class, "submit_confirm"])->name("submit_confirm");

        Route::get("complete_financial_info/{form_general_item}", [Admin\SupplierRegisterController::class, "complete_financial_info"])->name("complete_financial_info");
        Route::post("submit_complete_financial_info/{form_general_item}", [Admin\SupplierRegisterController::class, "submit_complete_financial_info"])->name("submit_complete_financial_info");

    });

});

Route::middleware(['url_check:supplier.trust_product.dashboard.index'])->prefix('trust_product')->name("trust_product.")->group(function () {

    # Dashboard
    Route::prefix('dashboard')->name("dashboard.")->group(function () {
        Route::match( [ 'get', 'post' ], "index", [Supplier\TrustProduct\DashboardController::class, "index"])->name("index");
        Route::get("view_card/{production}", [Supplier\TrustProduct\DashboardController::class, "view_card"])->name("view_card");
        Route::get( "log/{machine_allocation}", [ Admin\DashboardController::class, "log" ] )->name( "log" );
//        Route::get( "view_form/{machine_allocation}/{form}", [ Admin\DashboardController::class, "view_form" ] )->name( "view_form" );

    });
    # Trust Product Allocation
    Route::prefix('trust_product_allocation')->name("trust_product_allocation.")->group(function () {
        Route::get( "index/{production}", [Supplier\TrustProduct\TrustProductAllocation::class, "index"])->name("index");
        Route::post( "submit/{production}", [Supplier\TrustProduct\TrustProductAllocation::class, "submit"])->name("submit");
        Route::get( "terminate/{production}", [Supplier\TrustProduct\TrustProductAllocation::class, "terminate"])->name("terminate");

    });

});

