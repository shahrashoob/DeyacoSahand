<?php

use App\Http\Controllers\GoodsKindProcess\FabricRaw;
use App\Http\Controllers\GoodsKindProcess\FabricRaw\PackingForm\QualityControlController;

Route::name("production_card.")->prefix("/production_card")->group(function () {

    Route::get("view_card/{production}/{back_url_type?}", [
        FabricRaw\ProductionCardController::class,
        "view_card"
    ])->name("view_card");

    Route::name("finished_allocation.")->prefix("/finished_allocation")->group(function () {
        Route::get("index/{production}", [
            FabricRaw\ProductionCard\FinishedAllocationController::class,
            "index"
        ])->name("index");
        Route::get("allocation_input/{production}/{allocation}", [
            FabricRaw\ProductionCard\FinishedAllocationController::class,
            "allocation_input"
        ])->name("allocation_input");
        Route::get("allocation_data/{production}/{allocation}", [
            FabricRaw\ProductionCard\FinishedAllocationController::class,
            "allocation_data"
        ])->name("allocation_data");
    });

    Route::name("terminate_production.")->prefix("/terminate_production")->group(function () {

        Route::get("index/{production}", [
            FabricRaw\ProductionCard\TerminateProductionController::class,
            "index"
        ])->name("index");

        Route::post("submit/{production}", [
            FabricRaw\ProductionCard\TerminateProductionController::class,
            "submit"
        ])->name("submit");

    });
});


Route::name("allocation_cancel.")->prefix("/allocation_cancel")->group(function () {
    Route::get("index/{allocation}/{production}", [
        FabricRaw\ProductionCard\AllocationCancelController::class,
        "index"
    ])->name("index");

});


Route::name("machine_allocation.")->prefix("/machine_allocation")->group(function () {
    Route::get("index/{production}", [
        FabricRaw\ProductionCard\MachineAllocationController::class,
        "index"
    ])->name("index");

    Route::post("select_machine_type/{production}/{machine_type}", [
        FabricRaw\ProductionCard\MachineAllocationController::class,
        "select_machine_type"
    ])->name("select_machine_type");

});


Route::name("production_form.")->prefix("/production_form")->group(function () {

    Route::get("view/{production_form}", [FabricRaw\ProductionFormController::class, "view"])->name("view");

    Route::name("fabric_extraction.")->prefix("/fabric_extraction")->group(function () {
        Route::get("index/{production_form}", [
            FabricRaw\ProductionForm\FabricExtractionController::class,
            "index"
        ])->name("index");
    });


});


Route::name("packing_form.")->prefix("/packing_form")->group(function () {
    Route::match(['get', 'post'], "index", [
        FabricRaw\PackingFormController::class,
        "index"
    ])->name("index");
    Route::get("view/{packing_form}/{page?}/{back_url_route?}/{id?}/{id2?}", [FabricRaw\PackingFormController::class, "view"])->name("view");
    Route::get("view_consumption/{packing_form}/{page?}", [FabricRaw\PackingFormController::class, "view_consumption"])->name("view_consumption");
    Route::get("view_cost/{packing_form}/{page?}", [FabricRaw\PackingFormController::class, "view_cost"])->name("view_cost");
    Route::get("qr/{packing_form}", [FabricRaw\PackingFormController::class, "qr"])->name("qr");

    Route::get("delivery_to_warehouse/{packing_form}", [
        FabricRaw\PackingFormController::class,
        "delivery_to_warehouse"
    ])->name("delivery_to_warehouse");

    Route::name("delivery_to_warehouse.")->prefix("/delivery_to_warehouse")->group(function () {
        Route::get("get_nosa_code/{packing_form}", [
            FabricRaw\PackingForm\DeliveryToWarehouseController::class,
            "get_nosa_code"
        ])->name("get_nosa_code");
        Route::post("submit_nosa_code/{packing_form}", [
            FabricRaw\PackingForm\DeliveryToWarehouseController::class,
            "submit_nosa_code"
        ])->name("submit_nosa_code");
        Route::post("submit/{packing_form}", [
            FabricRaw\PackingForm\DeliveryToWarehouseController::class,
            "submit"
        ])->name("submit");
    });

    Route::name("print_qr.")->prefix("/print_qr")->group(function () {
        Route::get("index/{packing_form}/{back_url_route?}/{id?}", [
            FabricRaw\PackingForm\PrintQRController::class,
            "index"
        ])->name("index");
        Route::post("submit/{packing_form}", [
            FabricRaw\PackingForm\PrintQRController::class,
            "submit"
        ])->name("submit");
        Route::get("download/{packing_form}", [
            FabricRaw\PackingForm\PrintQRController::class,
            "download"
        ])->name("download");
    });
    Route::name("change_packing.")->prefix("/change_packing")->group(function () {
        Route::get("index/{packing_form}", [
            FabricRaw\PackingForm\ChangePackingController::class,
            "index"
        ])->name("index");

        Route::post("submit/{packing_form}", [
            FabricRaw\PackingForm\ChangePackingController::class,
            "submit"
        ])->name("submit");

        Route::post("confirm/{packing_form}", [
            FabricRaw\PackingForm\ChangePackingController::class,
            "confirm"
        ])->name("confirm");

        Route::get("section/{packing_form}/{band_code}", [
            FabricRaw\PackingForm\ChangePackingController::class,
            "section"
        ])->name("section");
        Route::post("submit_section/{packing_form}/{band_code}", [
            FabricRaw\PackingForm\ChangePackingController::class,
            "submit_section"
        ])->name("submit_section");
        Route::post("end_of_section/{packing_form}", [
            FabricRaw\PackingForm\ChangePackingController::class,
            "end_of_section"
        ])->name("end_of_section");
        Route::get("delete_section/{packing_form}/{FabricRaw_grading}", [
            FabricRaw\PackingForm\ChangePackingController::class,
            "delete_section"
        ])->name("delete_section");
    });
    Route::name("change_in_warehouse.")->prefix("/change_in_warehouse")->group(function () {
        Route::get("index/{packing_form}", [
            FabricRaw\PackingForm\ChangeInWarehouseController::class,
            "index"
        ])->name("index");

        Route::post("submit/{packing_form}", [
            FabricRaw\PackingForm\ChangeInWarehouseController::class,
            "submit"
        ])->name("submit");

        Route::post("confirm/{packing_form}", [
            FabricRaw\PackingForm\ChangeInWarehouseController::class,
            "confirm"
        ])->name("confirm");

        Route::get("section/{packing_form}/{band_code}", [
            FabricRaw\PackingForm\ChangeInWarehouseController::class,
            "section"
        ])->name("section");
        Route::post("submit_section/{packing_form}/{band_code}", [
            FabricRaw\PackingForm\ChangeInWarehouseController::class,
            "submit_section"
        ])->name("submit_section");
        Route::post("end_of_section/{packing_form}", [
            FabricRaw\PackingForm\ChangeInWarehouseController::class,
            "end_of_section"
        ])->name("end_of_section");
        Route::get("delete_section/{packing_form}/{FabricRaw_grading}", [
            FabricRaw\PackingForm\ChangeInWarehouseController::class,
            "delete_section"
        ])->name("delete_section");
    });


    Route::name("change_packing_quick.")->prefix("/change_packing_quick")->group(function () {
        Route::get("index/{packing_form}", [
            FabricRaw\PackingForm\ChangePackingQuickController::class,
            "index"
        ])->name("index");

        Route::post("submit/{packing_form}", [
            FabricRaw\PackingForm\ChangePackingQuickController::class,
            "submit"
        ])->name("submit");

        Route::get("select_packing_forms/{packing_form}", [
            FabricRaw\PackingForm\ChangePackingQuickController::class,
            "select_packing_forms"
        ])->name("select_packing_forms");

        Route::get("show_list/{packing_form}", [
            FabricRaw\PackingForm\ChangePackingQuickController::class,
            "show_list"
        ])->name("show_list");

        Route::post("confirm/{packing_form}", [
            FabricRaw\PackingForm\ChangePackingQuickController::class,
            "confirm"
        ])->name("confirm");

    });


    Route::name("complete_information.")->prefix("/complete_information")->group(function () {
        Route::get("index/{packing_form}", [
            FabricRaw\PackingForm\CompleteInformationController::class,
            "index"
        ])->name("index");
        Route::post("submit/{packing_form}", [
            FabricRaw\PackingForm\CompleteInformationController::class,
            "submit"
        ])->name("submit");
    });

    Route::name("merger.")->prefix("/merger")->group(function () {
        Route::get("index/{packing_form}", [
            FabricRaw\PackingForm\MergerController::class,
            "index"
        ])->name("index");
        Route::post("submit/{packing_form}", [
            FabricRaw\PackingForm\MergerController::class,
            "submit"
        ])->name("submit");
    });



    Route::name("quality_control.")->prefix("/quality_control")->group(function () {
        Route::get("index/{packing_form}", [
            FabricRaw\PackingForm\QualityControlController::class,
            "index"
        ])->name("index");
        Route::post("submit_partner/{packing_form}", [
            FabricRaw\PackingForm\QualityControlController::class,
            "submit_partner"
        ])->name("submit_partner");
        Route::get("reset/{packing_form}", [
            FabricRaw\PackingForm\QualityControlController::class,
            "reset"
        ])->name("reset");
        Route::get("control/{packing_form}", [
            FabricRaw\PackingForm\QualityControlController::class,
            "control"
        ])->name("control");
        Route::get("end_of_qc/{packing_form}", [
            FabricRaw\PackingForm\QualityControlController::class,
            "end_of_qc"
        ])->name("end_of_qc");
        Route::post("submit/{packing_form}", [
            FabricRaw\PackingForm\QualityControlController::class,
            "submit"
        ])->name("submit");
    });


    Route::name("page_printing.")->prefix("/page_printing")->group(function () {
        Route::get("index", [
            FabricRaw\PackingForm\PagePrintingController::class,
            "index"
        ])->name("index");
        Route::match(['get', 'post'], "submit", [
            FabricRaw\PackingForm\PagePrintingController::class,
            "submit"
        ])->name("submit");
        Route::post("confirm_submit", [
            FabricRaw\PackingForm\PagePrintingController::class,
            "confirm_submit"
        ])->name("confirm_submit");

    });
//    Route::name("warehouse_shelving.")->prefix("/warehouse_shelving")->group(function () {
//        Route::get("index/{packing_form}", [
//            FabricRaw\PackingForm\WarehouseShelvingController::class,
//            "index"
//        ])->name("index");
//        Route::post("submit/{packing_form}", [
//            FabricRaw\PackingForm\WarehouseShelvingController::class,
//            "submit"
//        ])->name("submit");
//    });
});



