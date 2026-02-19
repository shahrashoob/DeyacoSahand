<?php

use App\Http\Controllers\Report\Report1001Controller;
use App\Http\Controllers\Report\Report1002Controller;
use App\Http\Controllers\Report\Report1003Controller;
use App\Http\Controllers\Report\Report1004Controller;
use App\Http\Controllers\Report\Report1005Controller;
use App\Http\Controllers\Report\Report1006Controller;
use App\Http\Controllers\Report\Report1007Controller;
use App\Http\Controllers\Report;

# 1001
Route::middleware(['url_check:report.1001.index'])->name("1001.")->prefix("/1001")->group(function () {

    Route::match(['get', 'post'], "index", [Report1001Controller::class, "index"])->name("index");
    Route::match(['get', 'post'], "line_group/{line_group}", [
        Report1001Controller::class,
        "line_group"
    ])->name("line_group");
    Route::match(['get', 'post'], "product/{product}", [
        Report1001Controller::class,
        "product"
    ])->name("product");
});
# 1002
Route::middleware(['url_check:report.1002.index'])->name("1002.")->prefix("/1002")->group(function () {
    Route::match(['get', 'post'], "index", [Report1002Controller::class, "index"])->name("index");
    Route::match(['get', 'post'], "view_card/{production}", [
        Report1002Controller::class,
        "view_card"
    ])->name("view_card");
});
# 1003
Route::middleware(['url_check:report.1003.index'])->name("1003.")->prefix("/1003")->group(function () {
    Route::match(['get', 'post'], "index", [Report1003Controller::class, "index"])->name("index");
    Route::match(['get', 'post'], "submit_form", [
        Report1003Controller::class,
        "submit_form"
    ])->name("submit_form");

    Route::get("download_file", [Report1003Controller::class, "download_file"])->name("download_file");
    Route::post("submit_download_file", [Report1003Controller::class, "submit_download_file"])->name("submit_download_file");
    Route::get("download_report/{queueOfLargeOperation}", [Report1003Controller::class, "download_report"])->name("download_report");

});
# 1004
Route::middleware(['url_check:report.1004.index'])->name("1004.")->prefix("/1004")->group(function () {
    Route::match(['get', 'post'], "index", [Report1004Controller::class, "index"])->name("index");
    Route::match(['get', 'post'], "submit_form", [
        Report1004Controller::class,
        "submit_form"
    ])->name("submit_form");
});
# 1005
Route::middleware(['url_check:report.1005.index'])->name("1005.")->prefix("/1005")->group(function () {

    Route::match(['get', 'post'], "index", [Report1005Controller::class, "index"])->name("index");
    Route::post("submit", [Report1005Controller::class, "submit"])->name("submit");
    Route::match(['get'], "add_access/{line_id}/{station_id}/{machine_type_id}/{machine_id}", [
        Report1005Controller::class,
        "add_access"
    ])->name("add_access");
    Route::match(['get'], "remove_access/{line_id}/{station_id}/{machine_type_id}/{machine_id}", [
        Report1005Controller::class,
        "remove_access"
    ])->name("remove_access");
    Route::match(['get'], "remove_access/{line_id}/{station_id}/{machine_type_id}/{machine_id}", [
        Report1005Controller::class,
        "remove_access"
    ])->name("remove_access");
    Route::match(['get'], "manage_access/{line_id}/{station_id}/{machine_type_id}/{machine_id}", [
        Report1005Controller::class,
        "manage_access"
    ])->name("manage_access");
    Route::match(['post'], "machine_permission/{machine_type}", [
        Report1005Controller::class,
        "machine_permission"
    ])->name("machine_permission");
    Route::get("select_machine/", [
        Report1005Controller::class,
        "select_machine"
    ])->name("select_machine");
});
# 1006
Route::middleware(['url_check:report.1006.index'])->name("1006.")->prefix("/1006")->group(function () {
    Route::match(['get', 'post'], "index", [Report1006Controller::class, "index"])->name("index");
    Route::match(['get','post'], "submit_form", [
        Report1006Controller::class,
        "submit_form"
    ])->name("submit_form");
});
# 1007
Route::middleware(['url_check:report.1007.index'])->name("1007.")->prefix("/1007")->group(function () {
    Route::match(['get', 'post'], "index", [Report1007Controller::class, "index"])->name("index");
    Route::match(['post'], "submit", [Report1007Controller::class, "submit"])->name("submit");
});
# 1009
Route::middleware(['url_check:report.cross_sectional_management.dashboard.index'])->name("1009.")->prefix("/1009")->group(function () {
    Route::match(['get', 'post'], "index", [Report\Report1009Controller::class, "index"])->name("index");
    Route::match(['get'], "submit/{percent_pie_chart?}", [Report\Report1009Controller::class, "submit"])->name("submit");
    Route::match(['post'], "export", [Report\Report1009Controller::class, "export"])->name("export");
});
# 1010
Route::middleware(['url_check:report.cross_sectional_management.dashboard.index'])->name("1010.")->prefix("/1010")->group(function () {
    Route::match(['get', 'post'], "index", [Report\Report1010Controller::class, "index"])->name("index");
    Route::match(['get'], "submit/{percent_pie_chart?}", [Report\Report1010Controller::class, "submit"])->name("submit");
    Route::match(['get'], "export_ReportForCrossSection", [Report\Report1010Controller::class, "export_ReportForCrossSection"])->name("export_ReportForCrossSection");
});
# 1011
Route::middleware(['url_check:report.cross_sectional_management.dashboard.index'])->name("1011.")->prefix("/1011")->group(function () {
    Route::match(['get', 'post'], "index", [Report\Report1011Controller::class, "index"])->name("index");
    Route::match(['get'], "submit/{machine_id?}", [Report\Report1011Controller::class, "submit"])->name("submit");
    Route::match(['get'], "export_ReportForCrossSection", [Report\Report1011Controller::class, "export_ReportForCrossSection"])->name("export_ReportForCrossSection");

});
# 1012
Route::middleware(['url_check:report.1012.index'])->name("1012.")->prefix("/1012")->group(function () {
    Route::match(['get', 'post'], "index", [Report\Report1012Controller::class, "index"])->name("index");
    Route::match(['get', 'post'], "submit", [Report\Report1012Controller::class, "submit"])->name("submit");

});
# 1013
Route::middleware(['url_check:report.cross_sectional_management.dashboard.index'])->name("1013.")->prefix("/1013")->group(function () {
    Route::match(['get', 'post'], "index", [Report\Report1013Controller::class, "index"])->name("index");
    Route::get("set_chart_percent/{percent}/{type}", [Report\Report1013Controller::class, "set_chart_percent"])->name("set_chart_percent");
    Route::get("download_excel", [Report\Report1013Controller::class, "download_excel"])->name("download_excel");
    Route::post("submit_download_excel", [Report\Report1013Controller::class, "submit_download_excel"])->name("submit_download_excel");
});


# 1014
Route::middleware(['url_check:report.1014.index'])->name("1014.")->prefix("/1014")->group(function () {
    Route::match(['get', 'post'], "index", [Report\Report1014Controller::class, "index"])->name("index");
    Route::match(['get', 'post'], "submit", [Report\Report1014Controller::class, "submit"])->name("submit");

});

# cross_sectional_management.dashboard
Route::middleware(['url_check:report.cross_sectional_management.dashboard.index'])->name("cross_sectional_management.dashboard.")->prefix("/cross_sectional_management/dashboard")->group(function () {
    Route::match(['get', 'post'], "index", [Report\CrossSectionalManagment\DashboardController::class, "index"])->name("index");
    Route::match(['post'], "submit", [Report\CrossSectionalManagment\DashboardController::class, "submit"])->name("submit");
});


# real time داشبورد لحظه ای
Route::middleware(['url_check:report.real_time.dashboard.index'])->name("real_time.dashboard.")->prefix("/real_time/dashboard")->group(function () {
    Route::match(['get', 'post'], "index", [Report\RealTime\DashboardController::class, "index"])->name("index");
    Route::match(['post'], "submit", [Report\RealTime\DashboardController::class, "submit"])->name("submit");
});
Route::middleware(['url_check:report.real_time.dashboard.index'])->name("real_time.setting.")->prefix("/real_time/setting")->group(function () {
    Route::match(['get', 'post'], "index", [Report\RealTime\SettingController::class, "index"])->name("index");
    Route::match([ 'post'], "submit\{type}", [Report\RealTime\SettingController::class, "submit"])->name("submit");

});
