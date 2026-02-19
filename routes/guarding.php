<?php
use App\Http\Controllers\Garding;

Route::middleware( [ 'url_check:guarding.dashboard.index' ] )->prefix('dashboard')->name("dashboard.")->group(function () {

    Route::get("index", [Garding\DashboardController::class, "index"])->name("index");

    Route::get("show_transport/{transport}", [Garding\DashboardController::class, "show_transport"])->name("show_transport");


    Route::get("show_exit_form/{form}", [Garding\DashboardController::class, "show_exit_form"])->name("show_exit_form");
    Route::post("confirm_exist_form/{form}", [Garding\DashboardController::class, "confirm_exist_form"])->name("confirm_exist_form");
    Route::get("reject_exist_form/{form}", [Garding\DashboardController::class, "reject_exist_form"])->name("reject_exist_form");

    Route::get("show_input_form/{form}", [Garding\DashboardController::class, "show_input_form"])->name("show_input_form");
    Route::post("confirm_input_form/{form}", [Garding\DashboardController::class, "confirm_input_form"])->name("confirm_input_form");
    Route::get("reject_input_form/{form}", [Garding\DashboardController::class, "reject_input_form"])->name("reject_input_form");

});

Route::middleware( [ 'url_check:guarding.reject_product.index' ] )->prefix('reject_product')->name("reject_product.")->group(function () {

    Route::get("index", [Garding\RejectProductController::class, "index"])->name("index");
    Route::get("view/{reject_product_form}", [Garding\RejectProductController::class, "view"])->name("view");
    Route::get("confirm_form/{reject_product_form}", [Garding\RejectProductController::class, "confirm_form"])->name("confirm_form");

});
