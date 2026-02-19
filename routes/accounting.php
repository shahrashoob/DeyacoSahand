<?php
/**
 * Copyright 2016-2021 Appnitro Software. This code cannot be redistributed without
 * permission from http://www.arjnet.ir/
 * Created by Alireza Jalayegh.
 * Date: 5/18/2021, 8:31 AM
 * Description:
 *
 */

use App\Http\Controllers\Accounting\Client\BuyController;
use App\Http\Controllers\Accounting\Client\FactorController;
use App\Http\Controllers\Accounting\Client\TransactionController;
use App\Http\Controllers\Accounting\Definition;
use App\Http\Controllers\Accounting\Contract;
use App\Http\Controllers\Accounting\Store\DashboardController;
use App\Http\Controllers\Accounting\Setting;
use App\Http\Controllers\Accounting\Tariff;
use App\Http\Controllers\Accounting\Store;
use App\Http\Controllers\Accounting\WelfareService\Tara;

Route::name("definition.")->prefix("/definition")->group(function () {

    Route::middleware(['url_check:accounting.definition.cost_center.index'])->name("cost_center.")->prefix("/cost_center")->group(function () {
        Route::match(['get', 'post'], "index", [Definition\CostCenterController::class, "index"])->name("index");
        Route::get("create", [Definition\CostCenterController::class, "create"])->name("create");
        Route::post("store", [Definition\CostCenterController::class, "store"])->name("store");
        Route::get("edit/{cost_center}", [Definition\CostCenterController::class, "edit"])->name("edit");
        Route::post("update/{cost_center}", [Definition\CostCenterController::class, "update"])->name("update");
        Route::get("destroy/{cost_center}", [Definition\CostCenterController::class, "destroy"])->name("destroy");
    });

    Route::middleware(['url_check:accounting.definition.financial_operation_pattern.index'])->name("financial_operation_pattern.")->prefix("/financial_operation_pattern")->group(function () {
        Route::match(['get', 'post'], "index", [Definition\FinancialOperationPatternController::class, "index"])->name("index");
        Route::get("create", [Definition\FinancialOperationPatternController::class, "create"])->name("create");
        Route::post("store", [Definition\FinancialOperationPatternController::class, "store"])->name("store");

        Route::get("edit/{financial_operation_pattern}", [Definition\FinancialOperationPatternController::class, "edit"])->name("edit");
        Route::post("update/{financial_operation_pattern}", [Definition\FinancialOperationPatternController::class, "update"])->name("update");

        Route::get("add_item/{financial_operation_pattern}", [Definition\FinancialOperationPatternController::class, "add_item"])->name("add_item");
        Route::post("submit_add_item/{financial_operation_pattern}", [Definition\FinancialOperationPatternController::class, "submit_add_item"])->name("submit_add_item");
        Route::get("remove_item/{financial_operation_pattern}/{financial_operation_pattern_item}", [Definition\FinancialOperationPatternController::class, "remove_item"])->name("remove_item");

        Route::get("destroy/{financial_operation_pattern}", [Definition\FinancialOperationPatternController::class, "destroy"])->name("destroy");
    });
    Route::middleware(['url_check:accounting.definition.account.index'])->name("account.")->prefix("/account")->group(function () {
        Route::match(['get', 'post'], "index", [Definition\AccountController::class, "index"])->name("index");
        Route::match(['get', 'post'], "account_list/{account}", [Definition\AccountController::class, "account_list"])->name("account_list");
        Route::get("create", [Definition\AccountController::class, "create"])->name("create");
        Route::post("store", [Definition\AccountController::class, "store"])->name("store");
        Route::get("create_sub_account/{account}", [Definition\AccountController::class, "create_sub_account"])->name("create_sub_account");
        Route::post("store_sub_account/{account}", [Definition\AccountController::class, "store_sub_account"])->name("store_sub_account");
        Route::get("edit/{account}", [Definition\AccountController::class, "edit"])->name("edit");
        Route::post("update/{account}", [Definition\AccountController::class, "update"])->name("update");
    });
});

Route::name("contract.")->prefix("/contract")->group(function () {

    Route::middleware(['url_check:accounting.contract.contract.index'])->name("contract.")->prefix("/contract")->group(function () {
        Route::get("index", [Contract\ContractController::class, "index"])->name("index");
        Route::get("create", [Contract\ContractController::class, "create"])->name("create");
        Route::post("store", [Contract\ContractController::class, "store"])->name("store");
        Route::get("add_clause/{contract}/{clause_type_id?}", [Contract\ContractController::class, "add_clause"])->name("add_clause");
        Route::post("store_clause/{contract}", [Contract\ContractController::class, "store_clause"])->name("store_clause");
        Route::get("edit/{contract}", [Contract\ContractController::class, "edit"])->name("edit");
        Route::post("update/{contract}", [Contract\ContractController::class, "update"])->name("update");
        Route::get("print/{contract}", [Contract\ContractController::class, "print"])->name("print");
        Route::get("destroy_contract_clause_type/{contract}/{contract_clause_type}", [Contract\ContractController::class, "destroy_contract_clause_type"])->name("destroy_contract_clause_type");


    });

    Route::middleware(['url_check:accounting.contract.contract.index'])->name("clause.")->prefix("/clause")->group(function () {
        Route::get("index", [Contract\ClauseContoller::class, "index"])->name("index");
        Route::get("create", [Contract\ClauseContoller::class, "create"])->name("create");
        Route::post("store", [Contract\ClauseContoller::class, "store"])->name("store");
        Route::get("add_clause_article/{clause_type}", [Contract\ClauseContoller::class, "add_clause_article"])->name("add_clause_article");
        Route::post("submit_clause_article/{clause_type}", [Contract\ClauseContoller::class, "submit_clause_article"])->name("submit_clause_article");
        Route::get("destroy_clause_article/{clause_article}", [Contract\ClauseContoller::class, "destroy_clause_article"])->name("destroy_clause_article");
    });
});
Route::middleware(['url_check:accounting.tariff.index'])->name("tariff.")->prefix("tariff")->group(function () {
    Route::match(['get', 'post'], "tariff/index", [Tariff\TariffController::class, "index"])->name("index");
    Route::get("create", [Tariff\TariffController::class, "create"])->name("create");
    Route::post("store", [Tariff\TariffController::class, "store"])->name("store");
    Route::get("edit/{tariff}", [Tariff\TariffController::class, "edit"])->name("edit");
    Route::post("update/{tariff}", [Tariff\TariffController::class, "update"])->name("update");
    Route::get("upload/{tariff}", [Tariff\TariffController::class, "upload"])->name("upload");
    Route::post("submit_upload/{tariff}", [Tariff\TariffController::class, "submit_upload"])->name("submit_upload");
    Route::get("show_upload/{tariff}", [Tariff\TariffController::class, "show_upload"])->name("show_upload");
    Route::get("upload_product/{tariff}", [Tariff\TariffController::class, "upload_product"])->name("upload_product");
    Route::get("view_log/{tariff}", [Tariff\TariffController::class, "view_log"])->name("view_log");
    Route::get("download_log_list/{tariff}/{tariff_log}", [
        Tariff\TariffController::class,
        "download_log_list"
    ])->name("download_log_list");
});
Route::name("client.")->prefix("/client")->group(function () {
    Route::name("buy.")->prefix("/buy")->group(function () {
        Route::get("index", [BuyController::class, "index"])->name("index");
        Route::post("store", [BuyController::class, "store"])->name("store");
        Route::get("show", [BuyController::class, "show"])->name("show");
        Route::match(["get", "post"], "confirm", [BuyController::class, "confirm"])->name("confirm");
        Route::get("verify", [BuyController::class, "verify"])->name("verify");
    });
    Route::name("transaction.")->prefix("/transaction")->group(function () {
        Route::get("index", [TransactionController::class, "index"])->name("index");
        Route::get("show/{client_transaction}", [TransactionController::class, "show"])->name("show");

    });
    Route::name("factor.")->prefix("/factor")->group(function () {
        Route::get("index", [FactorController::class, "index"])->name("index");
        Route::get("print/{client_factor}", [FactorController::class, "print"])->name("print");
        Route::get("show/{client_factor}", [FactorController::class, "show"])->name("show");
    });
});
Route::name("store.")->prefix("/store")->group(function () {

    Route::name("setting.")->prefix("/setting")->group(function () {
        Route::get("index", [Store\SettingController::class, "index"])->name("index");
        Route::post("store", [Store\SettingController::class, "store"])->name("store");
    });

    Route::name("dashboard.")->prefix("/dashboard")->group(function () {
        Route::get("index", [DashboardController::class, "index"])->name("index");
        Route::post("create/{store}", [DashboardController::class, "create"])->name("create");
        Route::post("store/{store}", [DashboardController::class, "store"])->name("store");
    });
    Route::name("store_setting.")->prefix("/store_setting")->group(function () {

        Route::name("store1.")->prefix("/store1")->group(function () {
            Route::get("create/{store}/{special_license_type}", [Store\StoreSetting\Store1Controller::class, "create"])->name("create");
            Route::post("store/{store}/{special_license_type}", [Store\StoreSetting\Store1Controller::class, "store"])->name("store");
        });

    });

});


Route::name("welfare_service.tara.")->prefix("/welfare_service/tara/client")->group(function () {

    Route::name("client.")->prefix("/client")->group(function () {
        Route::name("dashboard.")->prefix("/dashboard")->group(function () {
        Route::get("index", [Tara\Client\DashboardController::class, "index"])->name("index");
        Route::get("purchase_key", [Tara\Client\DashboardController::class, "purchase_key"])->name("purchase_key");
        Route::get("transactions", [Tara\Client\DashboardController::class, "transactions"])->name("transactions");
        Route::post("store", [Tara\Client\DashboardController::class, "store"])->name("store");
    });

    });

    Route::name("admin.")->prefix("/admin")->group(function () {
        Route::name("dashboard.")->prefix("/dashboard")->group(function () {
        Route::get("index", [Tara\Admin\DashboardController::class, "index"])->name("index");
        Route::match(['get', 'post'],"add_user", [Tara\Admin\DashboardController::class, "add_user"])->name("add_user");
        Route::post("submit_add_user/{worker}", [Tara\Admin\DashboardController::class, "submit_add_user"])->name("submit_add_user");

        Route::get("charge/{welfare_service}", [Tara\Admin\DashboardController::class, "charge"])->name("charge");
        Route::post("submit_charge/{welfare_service}", [Tara\Admin\DashboardController::class, "submit_charge"])->name("submit_charge");

        Route::get("decharge/{welfare_service}", [Tara\Admin\DashboardController::class, "decharge"])->name("decharge");
        Route::post("submit_decharge/{welfare_service}", [Tara\Admin\DashboardController::class, "submit_decharge"])->name("submit_decharge");
    });

    });

});