<?php

use App\Http\Controllers\LineProductStation\Machine\MachineProductionChannelTypeController;
use App\Http\Controllers\LineProductStation\Product\ProductCreation\RegisterInFinancialSoftwareController;
use App\Http\Controllers\LineProductStation\StationController;
use App\Http\Controllers\LineProductStation\LineController;
use App\Http\Controllers\LineProductStation\ProductController;
use App\Http\Controllers\LineProductStation\MachineController;
use App\Http\Controllers\LineProductStation\MachineTypeController;
use App\Http\Controllers\LineProductStation\MachineStatusController;
use App\Http\Controllers\LineProductStation\MachinePropertyController;
use App\Http\Controllers\LineProductStation\Machine;
use App\Http\Controllers\LineProductStation\Machine\DashboardController as Dashboard;
use App\Http\Controllers\LineProductStation\ProductTypeController;
use App\Http\Controllers\LineProductStation\ProductionMethodController;
use App\Http\Controllers\LineProductStation\Product;
use App\Http\Controllers\LineProductStation\GoodsKindController;
use App\Http\Controllers\LineProductStation\GoodsKindPropertyController;
use App\Http\Controllers\LineProductStation\GoodsKindOptionController;
use App\Http\Controllers\LineProductStation\DegreeController;
use App\Http\Controllers\LineProductStation\CarrierTypeController;
use App\Http\Controllers\LineProductStation\Maintenance;
use App\Http\Controllers\LineProductStation\Packing;
use App\Http\Controllers\LineProductStation\Carrier;
use App\Http\Controllers\LineProductStation\Reservoir;
use App\Http\Controllers\LineProductStation\GoodsKind\GoodsKindClassificationController;
use App\Http\Controllers\LineProductStation\GoodsKind\GoodsKindClassificationOptionController;
use App\Http\Controllers\LineProductStation\MachineType;
use App\Http\Controllers\LineProductStation\GoodsKind;
use App\Http\Controllers\LineProductStation\ProductionChannelType;

Route::middleware(['url_check:line_product_station.line.index'])->name("station.")->prefix("/station")->group(function () {
    Route::get("index/{line}", [StationController::class, "index"])->name("index");
    Route::get("create/{line}", [StationController::class, "create"])->name("create");
    Route::post("store/{line}", [StationController::class, "store"])->name("store");
    Route::get("edit/{station}", [StationController::class, "edit"])->name("edit");
    Route::post("update/{station}", [StationController::class, "update"])->name("update");
    Route::post("property_update/{station}", [StationController::class, "property_update"])->name("property_update");


    Route::get("warehouse_index/{station}", [StationController::class, "warehouse_index"])->name("warehouse_index");
    Route::get("warehouse_create/{station}", [StationController::class, "warehouse_create"])->name("warehouse_create");
    Route::post("warehouse_store/{station}", [StationController::class, "warehouse_store"])->name("warehouse_store");


    Route::name("operation.")->prefix("/operation")->group(function () {

        Route::get("operation_create/{station}", [
            StationController::class,
            "operation_create"
        ])->name("create");
        Route::post("operation_store/{station}", [
            StationController::class,
            "operation_store"
        ])->name("store");
        Route::get("operation_edit/{station}/{station_operation}", [
            StationController::class,
            "operation_edit"
        ])->name("edit");
        Route::post("operation_update/{station_operation}", [
            StationController::class,
            "operation_update"
        ])->name("update");
        Route::get("operation_index/{station}", [
            StationController::class,
            "operation_index"
        ])->name("index");
    });

    Route::name("station_category.")->prefix("/station_category")->group(function () {

        Route::get("station_category_index/{station}", [
            StationController::class,
            "station_category_index"
        ])->name("index");

        Route::post("station_category_store/{station}", [
            StationController::class,
            "station_category_store"
        ])->name("store");
    });

    Route::name("sub_operation.")->prefix("/sub_operation")->group(function () {

        Route::get("sub_operation_create/{station_operation}", [
            StationController::class,
            "sub_operation_create"
        ])->name("create");
        Route::post("sub_operation_store/{station_operation}", [
            StationController::class,
            "sub_operation_store"
        ])->name("store");
        Route::get("sub_operation_edit/{station_operation}/{station_sub_operation}", [
            StationController::class,
            "sub_operation_edit"
        ])->name("edit");
        Route::post("sub_operation_update/{station_sub_operation}", [
            StationController::class,
            "sub_operation_update"
        ])->name("update");
        Route::get("sub_operation_index/{station_operation}", [
            StationController::class,
            "sub_operation_index"
        ])->name("index");
    });

});

Route::middleware(['url_check:line_product_station.line.index'])->name("machine.")->prefix("/machine")->group(function () {
    Route::get("index/{machine_type}", [MachineController::class, "index"])->name("index");
    Route::get("create/{machine_type}", [MachineController::class, "create"])->name("create");
    Route::post("store/{machine_type}", [MachineController::class, "store"])->name("store");
    Route::get("edit/{machine}", [MachineController::class, "edit"])->name("edit");
    Route::post("update/{machine}", [MachineController::class, "update"])->name("update");

});

Route::middleware(['url_check:line_product_station.line.index'])->prefix('machine/{machine}/production_channel_type')->name('machine.production_channel_type.')->group(function () {
        Route::get('index', [MachineProductionChannelTypeController::class, 'index'])->name('index');
        Route::post('store', [MachineProductionChannelTypeController::class, 'store']) ->name('store');
    });
Route::middleware(['url_check:line_product_station.line.index'])->name("machine_type.")->prefix("/machine_type")->group(function () {
    Route::get("index/{station}", [MachineTypeController::class, "index"])->name("index");
    Route::get("create/{station}", [MachineTypeController::class, "create"])->name("create");
    Route::post("store/{station}", [MachineTypeController::class, "store"])->name("store");
    Route::get("edit/{machine_type}", [MachineTypeController::class, "edit"])->name("edit");
    Route::post("update/{machine_type}", [MachineTypeController::class, "update"])->name("update");


    Route::get("warehouse_index/{machine_type}", [MachineTypeController::class, "warehouse_index"])->name("warehouse_index");
    Route::get("warehouse_create/{machine_type}", [MachineTypeController::class, "warehouse_create"])->name("warehouse_create");
    Route::post("warehouse_store/{machine_type}", [MachineTypeController::class, "warehouse_store"])->name("warehouse_store");


    Route::post("property_update/{machine_type}", [
        MachineTypeController::class,
        "property_update"
    ])->name("property_update");
    Route::post("warehouse_handling_update/{machine_type}", [
        MachineTypeController::class,
        "warehouse_handling_update"
    ])->name("warehouse_handling_update");

    Route::name("input_band.")->prefix("/input_band")->group(function () {

        Route::get("index/{machine_type}", [
            MachineType\InputBandController ::class,
            "index"
        ])->name("index");

        Route::get("create/{machine_type}", [
            MachineType\InputBandController::class,
            "create"
        ])->name("create");
        Route::post("store/{machine_type}", [
            MachineType\InputBandController::class,
            "store"
        ])->name("store");
        Route::get("edit/{machine_type}/{machine_type_input_band}", [
            MachineType\InputBandController::class,
            "edit"
        ])->name("edit");
        Route::post("update/{machine_type_input_band}", [
            MachineType\InputBandController::class,
            "update"
        ])->name("update");

        Route::post("update_goods_kind_algorithm/{machine_type}", [
            MachineType\InputBandController::class,
            "update_goods_kind_algorithm"
        ])->name("update_goods_kind_algorithm");

        Route::get("add_goods_kind/{machine_type_input_band}", [
            MachineType\InputBandController::class,
            "add_goods_kind"
        ])->name("add_goods_kind");

        Route::post("add_goods_kind_submit_step1/{machine_type_input_band}", [
            MachineType\InputBandController::class,
            "add_goods_kind_submit_step1"
        ])->name("add_goods_kind_submit_step1");

        Route::get("add_goods_kind_step2/{machine_type_input_band}/{goods_kind}/{effect_is_shared}", [
            MachineType\InputBandController::class,
            "add_goods_kind_step2"
        ])->name("add_goods_kind_step2");
        Route::post("add_goods_kind_submit_step2/{machine_type_input_band}/{goods_kind}/{effect_is_shared}", [
            MachineType\InputBandController::class,
            "add_goods_kind_submit_step2"
        ])->name("add_goods_kind_submit_step2");

        Route::get("delete_goods_kind/{machine_type_input_band}/{goods_kind}", [
            MachineType\InputBandController::class,
            "delete_goods_kind"
        ])->name("delete_goods_kind");

        Route::post("goods_kind_update/{machine_type_input_band}", [
            MachineType\InputBandController::class,
            "goods_kind_update"
        ])->name("goods_kind_update");

    });

    Route::name("output_band.")->prefix("/output_band")->group(function () {

        Route::get("index/{machine_type}", [
            MachineType\OutputBandController ::class,
            "index"
        ])->name("index");

        Route::get("create/{machine_type}", [
            MachineType\OutputBandController::class,
            "create"
        ])->name("create");
        Route::post("store/{machine_type}", [
            MachineType\OutputBandController::class,
            "store"
        ])->name("store");
        Route::get("edit/{machine_type}/{machine_type_output_band}", [
            MachineType\OutputBandController::class,
            "edit"
        ])->name("edit");

        Route::post("update/{machine_type_output_band}", [
            MachineType\OutputBandController::class,
            "update"
        ])->name("update");

        Route::get("calculation_method/{machine_type}/{machine_type_output_band}/{mtob_goods_kind_id}", [
            MachineType\OutputBandController::class,
            "calculation_method"
        ])->name("calculation_method");

        Route::post("submit_calculation_method/{machine_type}/{machine_type_output_band}/{mtob_goods_kind_id}", [
            MachineType\OutputBandController::class,
            "submit_calculation_method"
        ])->name("submit_calculation_method");


        Route::post("submit_output_band_warehouse/{machine_type}/{mtob_goods_kind_id}", [
            MachineType\OutputBandController::class,
            "submit_output_band_warehouse"
        ])->name("submit_output_band_warehouse");

        Route::get("add_goods_kind/{machine_type_output_band}", [
            MachineType\OutputBandController::class,
            "add_goods_kind"
        ])->name("add_goods_kind");

        Route::post("add_goods_kind_submit_step1/{machine_type_output_band}", [
            MachineType\OutputBandController::class,
            "add_goods_kind_submit_step1"
        ])->name("add_goods_kind_submit_step1");

        Route::get("add_goods_kind_step2/{machine_type_output_band}/{goods_kind}", [
            MachineType\OutputBandController::class,
            "add_goods_kind_step2"
        ])->name("add_goods_kind_step2");

        Route::post("add_goods_kind_submit_step2/{machine_type_output_band}/{goods_kind}", [
            MachineType\OutputBandController::class,
            "add_goods_kind_submit_step2"
        ])->name("add_goods_kind_submit_step2");

        Route::get("delete_goods_kind/{machine_type_output_band}/{goods_kind}", [
            MachineType\OutputBandController::class,
            "delete_goods_kind"
        ])->name("delete_goods_kind");

        Route::post("goods_kind_update/{machine_type_output_band}", [
            MachineType\OutputBandController::class,
            "goods_kind_update"
        ])->name("goods_kind_update");

    });

    Route::name("machine_fault.")->prefix("/machine_fault")->group(function () {

        Route::get("index/{machine_type}", [
            MachineType\MachineTypeMachineFaultController ::class,
            "index"
        ])->name("index");

        Route::post("store/{machine_type}", [
            MachineType\MachineTypeMachineFaultController::class,
            "store"
        ])->name("store");

        Route::post("store_product_fault/{machine_type}/{machine_type_machine_fault}", [
            MachineType\MachineTypeMachineFaultController::class,
            "store_product_fault"
        ])->name("store_product_fault");

        Route::get("delete/{machine_type}/{machine_type_machine_fault}", [
            MachineType\MachineTypeMachineFaultController ::class,
            "delete"
        ])->name("delete");

    });
    Route::name("production_channel.")->prefix("/production_channel")->group(function () {

        Route::get("index/{machine_type}", [
            MachineType\MachineTypeProductionChannelController ::class,
            "index"
        ])->name("index");

        Route::post("store/{machine_type}", [
            MachineType\MachineTypeProductionChannelController::class,
            "store"
        ])->name("store");

        Route::get("delete/{machine_type}/{production_channel_type}", [
            MachineType\MachineTypeProductionChannelController::class,
            "delete"
        ])->name("delete");

        Route::get("edit_next_ones/{machine_type}/{production_channel_type}", [
            MachineType\MachineTypeProductionChannelController::class,
            "edit_next_ones"
        ])->name("edit_next_ones");

        Route::post("update_next_ones/{machine_type}/{production_channel_type}", [
            MachineType\MachineTypeProductionChannelController::class,
            "update_next_ones"
        ])->name("update_next_ones");

        Route::get("delete_next_ones/{machine_type}/{production_channel_type}/{production_channel_next_one_id}", [
            MachineType\MachineTypeProductionChannelController::class,
            "delete_next_ones"
        ])->name("delete_next_ones");


    });
});

Route::middleware(['url_check:line_product_station.line.index'])->name("machine_status.")->prefix("/machine_status")->group(function () {
    Route::get("index/{machine_type}/{machine_module_type}", [
        MachineStatusController::class,
        "index"
    ])->name("index");
    Route::post("update_possibility_allocation/{machine_module_type}", [
        MachineStatusController::class,
        "update_possibility_allocation"
    ])->name("update_possibility_allocation");
});


Route::middleware(['url_check:line_product_station.line.index'])->name("machine.machine_module_type.")->prefix("/machine/machine_module_type/")->group(function () {
    Route::get("index/{machine_type}/{machine_module_type}", [
        Machine\MachineModuleTypeController::class,
        "index"
    ])->name("index");
    Route::post("update/{machine_type}/{machine_module_type}", [
        Machine\MachineModuleTypeController::class,
        "update"
    ])->name("update");
});


Route::middleware(['url_check:line_product_station.line.index'])->name("line.")->prefix("/line")->group(function () {
    Route::get("index", [LineController::class, "index"])->name("index");
    Route::get("create", [LineController::class, "create"])->name("create");
    Route::post("store", [LineController::class, "store"])->name("store");
    Route::get("edit/{line}", [LineController::class, "edit"])->name("edit");
    Route::post("update/{line}", [LineController::class, "update"])->name("update");

    Route::post("property_update/{line}", [LineController::class, "property_update"])->name("property_update");


    Route::get("warehouse_index/{line}", [LineController::class, "warehouse_index"])->name("warehouse_index");
    Route::get("warehouse_create/{line}", [LineController::class, "warehouse_create"])->name("warehouse_create");
    Route::post("warehouse_store/{line}", [LineController::class, "warehouse_store"])->name("warehouse_store");
});

Route::middleware(['url_check:line_product_station.line.index'])->name("machine_property.")->prefix("/machine_property")->group(function () {
    Route::get("index/{station}", [MachinePropertyController::class, "index"])->name("index");
    Route::post("store/{station}", [MachinePropertyController::class, "store"])->name("store");
    Route::post("update/{station}", [MachinePropertyController::class, "update"])->name("update");
    Route::get("delete/{station}/{machine_property}", [
        MachinePropertyController::class,
        "delete"
    ])->name("delete");

});

Route::middleware(['url_check:line_product_station.line.index'])->name("machine_product_property.")->prefix("/machine_product_property")->group(function () {
    Route::get("index/{station}", [Machine\MachineProductPropertyController::class, "index"])->name("index");
    Route::post("store/{station}", [Machine\MachineProductPropertyController::class, "store"])->name("store");
    Route::post("update/{station}", [Machine\MachineProductPropertyController::class, "update"])->name("update");
    Route::get("delete/{station}/{machine_property}", [
        Machine\MachineProductPropertyController::class,
        "delete"
    ])->name("delete");
});

Route::name("product.")->prefix("/product")->group(function () {

    Route::middleware(['url_check:line_product_station.product.index'])->group(function () {

        Route::match(['get', 'post'], "index", [ProductController::class, "index"])->name("index");

        Route::get("create", [ProductController::class, "create"])->name("create");
        Route::post("store", [ProductController::class, "store"])->name("store");

        Route::get("copy_from_other", [ProductController::class, "copy_from_other"])->name("copy_from_other");
        Route::post("submit_copy_from_other", [
            ProductController::class,
            "submit_copy_from_other"
        ])->name("submit_copy_from_other");

        Route::get("edit/{product}", [ProductController::class, "edit"])->name("edit");
        Route::post("update/{product}", [ProductController::class, "update"])->name("update");

        Route::get("edit_supplementary/{product}", [ProductController::class, "edit_supplementary"])->name("edit_supplementary");
        Route::post("update_supplementary/{product}", [ProductController::class, "update_supplementary"])->name("update_supplementary");

        Route::get("edit_production/{product}", [
            ProductController::class,
            "edit_production"
        ])->name("edit_production");
//    Route::post( "update_production/{product}", [
//        ProductController::class,
//        "update_production"
//    ] )->name( "update_production" );

        Route::post("update_line_product_item/{product}/{line_product_station}", [
            ProductController::class,
            "update_line_product_item"
        ])->name("update_line_product_item");

        Route::get("print/{product}", [ProductController::class, "print"])->name("print");

        Route::name("replace_product.")->prefix("/replace_product")->group(function () {
            Route::get("index/{product}", [
                Product\ReplaceProductController::class,
                "index"
            ])->name("index");
            Route::post("submit/{product}", [
                Product\ReplaceProductController::class,
                "submit"
            ])->name("submit");
            Route::get("delete/{product}/{replace_product_id}", [
                Product\ReplaceProductController::class,
                "delete"
            ])->name("delete");
        });


        Route::name("warehouse.")->prefix("/warehouse")->group(function () {

            Route::get("index/{product}", [Product\WarehouseController::class, "index"])->name("index");
            Route::post("submit/{product}", [Product\WarehouseController::class, "submit"])->name("submit");
            Route::get("warehouse_shelving/{product}", [Product\WarehouseController::class, "warehouse_shelving"])->name("warehouse_shelving");

        });
        #Actual Cost
        Route::name("actual_cost.")->prefix("/actual_cost")->group(function () {

            Route::get("index/{product}", [Product\ActualCostController::class, "index"])->name("index");

        });
        #Pricing
        Route::name("pricing.")->prefix("/pricing")->group(function () {

            Route::get("index/{product}", [Product\PricingController::class, "index"])->name("index");
            Route::get("create_product_tariff/{product}", [Product\PricingController::class, "create_product_tariff"])->name("create_product_tariff");
            Route::post("store_product_tariff/{product}", [Product\PricingController::class, "store_product_tariff"])->name("store_product_tariff");
            Route::post("submit_add_pricing_to_tariff/{product}", [Product\PricingController::class, "submit_add_pricing_to_tariff"])->name("submit_add_pricing_to_tariff");


            Route::get("change_packing_type/{product}", [Product\PackingTypeController::class, "index"])->name("change_packing_type");
            Route::get("remove_product_tariff_pricing/{product}/{product_tariff_pricing}", [Product\PricingController::class, "remove_product_tariff_pricing"])->name("remove_product_tariff_pricing");
            Route::get("remove_product_tariff/{product}/{product_tariff}", [Product\PricingController::class, "remove_product_tariff"])->name("remove_product_tariff");

        });
        #Packing Type
        Route::name("packing_type.")->prefix("/packing_type")->group(function () {
            Route::get("index/{product}", [Product\PackingTypeController::class, "index"])->name("index");
            Route::post("submit/{product}", [Product\PackingTypeController::class, "submit"])->name("submit");
        });


        #Quality Control
        Route::name("quality_control.")->prefix("/quality_control")->group(function () {
            Route::get("index/{product}", [Product\QualityControlController::class, "index"])->name("index");
            Route::post("submit/{product}", [Product\QualityControlController::class, "submit"])->name("submit");
        });


        #Version
        Route::name("version.")->prefix("/version")->group(function () {
            Route::get("index/{product}", [Product\VersionController::class, "index"])->name("index");
            Route::get("details/{product}/{type}/{product_version}", [Product\VersionController::class, "details"])->name("details");
        });
        #Palning Algorithm
        Route::name("planing.")->prefix("/planing")->group(function () {
            Route::get("index/{product}", [Product\PlaningController::class, "index"])->name("index");
            Route::post("submit/{product}", [Product\PlaningController::class, "submit"])->name("submit");
        });

        Route::name("classification.")->prefix("/classification")->group(function () {

            Route::get("index/{product}", [Product\ClassificationController::class, "index"])->name("index");
            Route::post("submit/{product}", [Product\ClassificationController::class, "submit"])->name("submit");

        });
        Route::name("property.")->prefix("/property")->group(function () {
            Route::get("index/{product}", [Product\PropertyController::class, "index"])->name("index");
            Route::post("submit/{product}", [
                Product\PropertyController::class,
                "submit"
            ])->name("submit");

            Route::get("upload_image/{product}/{goods_kind_property}", [
                Product\PropertyController::class,
                "upload_image"
            ])->name("upload_image");
            Route::post("submit_upload_image/{product}/{goods_kind_property}/{product_creation_process_id?}", [
                Product\PropertyController::class,
                "submit_upload_image"
            ])->name("submit_upload_image");
            Route::get("delete/{product}/{goods_kind_property}", [
                Product\PropertyController::class,
                "delete"
            ])->name("delete");
        });

        Route::name("lot_number.")->prefix("/lot_number")->group(function () {
            Route::get("index/{product}", [
                Product\LotNumberController::class,
                "index"
            ])->name("index");
            Route::post("store/{product}", [
                Product\LotNumberController::class,
                "store"
            ])->name("store");
            Route::get("edit/{product}/{lot_number}", [
                Product\LotNumberController::class,
                "edit"
            ])->name("edit");
            Route::post("update/{product}/{lot_number}", [
                Product\LotNumberController::class,
                "update"
            ])->name("update");
        });

        Route::name("route.")->prefix("/route")->group(function () {

            Route::get("index/{product}", [
                Product\ProductRouteController::class,
                "index"
            ])->name("index");

            Route::get("create/{product}", [
                Product\ProductRouteController::class,
                "create"
            ])->name("create");

            Route::post("store/{product}", [
                Product\ProductRouteController::class,
                "store"
            ])->name("store");

            Route::get("edit/{product}/{product_route}", [
                Product\ProductRouteController::class,
                "edit"
            ])->name("edit");

            Route::post("update/{product}/{product_route}", [
                Product\ProductRouteController::class,
                "update"
            ])->name("update");

            Route::get("destroy/{product}/{product_route}", [
                Product\ProductRouteController::class,
                "destroy"
            ])->name("destroy");

        });

        Route::name("route_property.")->prefix("/route_property")->group(function () {

            Route::get("index/{product}", [
                Product\ProductRoutePropertyController::class,
                "index"
            ])->name("index");
            Route::post("submit/{product}", [
                Product\ProductRoutePropertyController::class,
                "submit"
            ])->name("submit");


        });

        Route::name("bom.")->prefix("/bom")->group(function () {

            Route::get("index/{product}/{show_route_code?}", [Product\BOM\BOMController::class, "index"])->name("index");
            Route::get("create/{product_route}", [Product\BOM\BOMController::class, "create"])->name("create");
            Route::get("edit/{bom}", [Product\BOM\BOMController::class, "edit"])->name("edit");
            Route::post("update/{bom}", [Product\BOM\BOMController::class, "update"])->name("update");
            Route::get("destroy/{product}/{bom}/{delete_all?}", [
                Product\BOM\BOMController::class,
                "destroy"
            ])->name("destroy");
        });

        Route::name("bom_permutation.")->prefix("/bom_permutation")->group(function () {

            Route::get("index/{product}", [
                Product\BOM\BOMPermutationController::class,
                "index"
            ])->name("index");

            Route::post("submit/{product}", [
                Product\BOM\BOMPermutationController::class,
                "submit"
            ])->name("submit");

        });

        Route::name("material_flow.")->prefix("/material_flow")->group(function () {
            Route::get("index/{product}", [
                Product\MaterialFlowController::class,
                "index"
            ])->name("index");

            Route::get("graph/{product}/{bom}/{line_product_station}", [
                Product\MaterialFlowController::class,
                "graph"
            ])->name("graph");
            Route::get("draw_graph_one_to_one/{product}/{bom}/{line_product_station}", [
                Product\MaterialFlowController::class,
                "draw_graph_one_to_one"
            ])->name("draw_graph_one_to_one");
        });

        Route::name("shade_number.")->prefix("/shade_number")->group(function () {
            Route::get("index/{product}", [
                Product\ShadeNumberController::class,
                "index"
            ])->name("index");
            Route::post("store/{product}", [
                Product\ShadeNumberController::class,
                "store"
            ])->name("store");
        });

        Route::name("sale.")->prefix("/sale")->group(function () {
            Route::get("index/{product}", [
                Product\SaleController::class,
                "index"
            ])->name("index");
            Route::post("submit/{product}", [
                Product\SaleController::class,
                "submit"
            ])->name("submit");
        });

        Route::name("consumed_product.")->prefix("/consumed_product")->group(function () {
            Route::get("index/{product}", [
                Product\ConsumedProductController::class,
                "index"
            ])->name("index");
            Route::get("create/{product}", [
                Product\ConsumedProductController::class,
                "create"
            ])->name("create");
            Route::post("submit/{product}", [
                Product\ConsumedProductController::class,
                "submit"
            ])->name("submit");
            Route::get("delete/{consumed_product}/{product}/{material_id?}", [
                Product\ConsumedProductController::class,
                "delete"
            ])->name("delete");
            Route::get("replace/{consumed_product}/{product}", [
                Product\ConsumedProductController::class,
                "replace"
            ])->name("replace");
            Route::post("store_replace/{consumed_product}/{product}", [
                Product\ConsumedProductController::class,
                "store_replace"
            ])->name("store_replace");
            Route::get("change_choose_material/{product}/{material_id?}", [
                Product\ConsumedProductController::class,
                "change_choose_material"
            ])->name("change_choose_material");
        });


        Route::name("waste.")->prefix("/waste")->group(function () {

            Route::get("index/{product}", [Product\WasteController::class, "index"])->name("index");
            Route::post("submit/{product}", [Product\WasteController::class, "submit"])->name("submit");

        });

    });

    /******* روت های اشتراکی ***********/

    Route::middleware(['url_check:line_product_station.product.index,line_product_station.product.product_creation.dashboard.index'])->group(function () {

        Route::name("product_station.")->prefix("/product_station")->group(function () {

            Route::get("create/{product}/{product_route}/{product_creation_process?}", [
                Product\ProductStationController::class,
                "create"
            ])->name("create");
            Route::post("store/{product}/{product_route}/{product_creation_process?}", [
                Product\ProductStationController::class,
                "store"
            ])->name("store");
            Route::get("edit/{product}/{line_product_station}/{product_creation_process?}", [
                Product\ProductStationController::class,
                "edit"
            ])->name("edit");
            Route::post("update/{product}/{line_product_station}/{product_creation_process?}", [
                Product\ProductStationController::class,
                "update"
            ])->name("update");
            Route::get("destroy/{product}/{line_product_station}/{product_creation_process?}", [
                Product\ProductStationController::class,
                "destroy"
            ])->name("destroy");
        });

        Route::name("bom_item.")->prefix("/bom_item")->group(function () {

            Route::get("create/{bom}/{product_creation_process?}", [
                Product\BOM\BOMItemController::class,
                "create"
            ])->name("create");

            Route::post("store/{bom}/{product_creation_process?}", [
                Product\BOM\BOMItemController::class,
                "store"
            ])->name("store");

            Route::get("destroy/{bom_item}", [
                Product\BOM\BOMItemController::class,
                "destroy"
            ])->name("destroy");


            Route::get("edit/{bom_item}/{product_creation_process?}", [
                Product\BOM\BOMItemController::class,
                "edit"
            ])->name("edit");

            Route::post("update/{bom}/{bom_item}/{product_creation_process?}", [
                Product\BOM\BOMItemController::class,
                "update"
            ])->name("update");

        });
        Route::name("bom_log.")->prefix("/bom_log")->group(function () {

            Route::get("index/{bom}/{product_creation_process?}", [
                Product\BOM\BOMLogController::class,
                "index"
            ])->name("index");


        });

        Route::name("bom_degree.")->prefix("/bom_degree")->group(function () {

            Route::get("index/{product}/{material}/{bom_item}/{product_creation_process?}", [
                Product\BOM\BOMDegreeController::class,
                "index"
            ])->name("index");
            Route::get("delete/{BOM_degree}/{product}/{material}/{degree}/{product_creation_process?}", [
                Product\BOM\BOMDegreeController::class,
                "delete"
            ])->name("delete");
            Route::post("store/{product}/{material}/{bom_item}/{product_creation_process?}", [
                Product\BOM\BOMDegreeController::class,
                "store"
            ])->name("store");

        });

        Route::name("bom_replace.")->prefix("/bom_replace")->group(function () {

            Route::get("index/{product}/{material}/{bom_item}/{product_creation_process?}", [
                Product\BOM\BOMReplaceController::class,
                "index"
            ])->name("index");
            Route::post("store/{product}/{material}/{bom_item}/{product_creation_process?}", [
                Product\BOM\BOMReplaceController::class,
                "store"
            ])->name("store");
            Route::get("delete/{bom_item}/{product_id}/{material_id}/{replace_product_id}", [
                Product\BOM\BOMReplaceController::class,
                "delete"
            ])->name("delete");

        });


        Route::name("bom_fault_illegal.")->prefix("/bom_fault_illegal")->group(function () {

            Route::get("index/{product}/{material}/{bom_item}/{product_creation_process?}", [
                Product\BOM\BOMFaultIllegalController::class,
                "index"
            ])->name("index");

            Route::post("store/{product}/{material}/{bom_item}/{product_creation_process?}", [
                Product\BOM\BOMFaultIllegalController::class,
                "store"
            ])->name("store");

        });
    });
});


Route::middleware(['url_check:line_product_station.product.fault.product_fault.index'])->
name("product.fault.product_fault.")->
prefix("/product/fault/product_fault")->group(function () {

    Route::match(['get', 'post'], "index", [
        Product\Fault\ProductFaultController::class,
        "index"
    ])->name("index");
    Route::get("create", [Product\Fault\ProductFaultController::class, "create"])->name("create");
    Route::post("store", [Product\Fault\ProductFaultController::class, "store"])->name("store");
    Route::get("edit/{product_fault}", [Product\Fault\ProductFaultController::class, "edit"])->name("edit");
    Route::post("update/{product_fault}", [
        Product\Fault\ProductFaultController::class,
        "update"
    ])->name("update");


});

Route::middleware(['url_check:line_product_station.product.fault.product_fault.index'])->
name("product.fault.product_fault_sign.")->
prefix("/product/fault/product_fault_sign")->group(function () {

    Route::match(['get', 'post'], "index", [
        Product\Fault\ProductFaultSignController::class,
        "index"
    ])->name("index");
    Route::get("create", [Product\Fault\ProductFaultSignController::class, "create"])->name("create");
    Route::post("store", [Product\Fault\ProductFaultSignController::class, "store"])->name("store");
    Route::get("edit/{product_fault_sign}", [Product\Fault\ProductFaultSignController::class, "edit"])->name("edit");
    Route::post("update/{product_fault_sign}", [
        Product\Fault\ProductFaultSignController::class,
        "update"
    ])->name("update");


});

Route::middleware(['url_check:line_product_station.machine.fault.machine_fault.index'])->
name("machine.fault.machine_fault.")->
prefix("/machine/fault/machine_fault")->group(function () {
    Route::match(['get', 'post'], "index", [
        Machine\Fault\MachineFaultController::class,
        "index"
    ])->name("index");
    Route::get("create", [Machine\Fault\MachineFaultController::class, "create"])->name("create");
    Route::post("store", [Machine\Fault\MachineFaultController::class, "store"])->name("store");
    Route::get("edit/{machine_fault}", [Machine\Fault\MachineFaultController::class, "edit"])->name("edit");
    Route::post("update/{machine_fault}", [
        Machine\Fault\MachineFaultController::class,
        "update"
    ])->name("update");
});

Route::middleware(['url_check:line_product_station.machine.fault.machine_fault.index'])->
name("machine.fault.machine_fault_sign.")->
prefix("/machine/fault/machine_fault_sign")->group(function () {
    Route::match(['get', 'post'], "index", [
        Machine\Fault\MachineFaultSignController::class,
        "index"
    ])->name("index");
    Route::get("create", [Machine\Fault\MachineFaultSignController::class, "create"])->name("create");
    Route::post("store", [Machine\Fault\MachineFaultSignController::class, "store"])->name("store");
    Route::get("edit/{machine_fault_sign}", [Machine\Fault\MachineFaultSignController::class, "edit"])->name("edit");
    Route::post("update/{machine_fault_sign}", [
        Machine\Fault\MachineFaultSignController::class,
        "update"
    ])->name("update");
});


Route::name("product.product_creation.")->prefix("/product/product_creation")->group(function () {


    Route::name("dashboard.")->prefix("/dashboard")->group(function () {
        Route::match(['get', 'post'], "index", [Product\ProductCreation\DashboardController::class, "index"])->name("index");
        Route::get("view/{product_creation_process}", [
            Product\ProductCreation\DashboardController::class,
            "view"
        ])->name("view");
        Route::get("log/{product_creation_process}", [
            Product\ProductCreation\DashboardController::class,
            "log"
        ])->name("log");

        Route::get("go_to_before_step/{product_creation_process}/{status}", [
            Product\ProductCreation\DashboardController::class,
            "go_to_before_step"
        ])->name("go_to_before_step");

        Route::get("show_print_form/{product_creation_process}", [
            Product\ProductCreation\DashboardController::class,
            "show_print_form"
        ])->name("show_print_form");
        Route::get("print/{product_creation_process}", [
            Product\ProductCreation\DashboardController::class,
            "print"
        ])->name("print");
        Route::get("download/{product_creation_process}", [
            Product\ProductCreation\DashboardController::class,
            "download"
        ])->name("download");

    });
    Route::name("product_show.")->prefix("/product_show")->group(function () {
        Route::get("index/{product_creation_process}", [Product\ProductCreation\ProductShowController::class, "index"])->name("index");

        Route::name("supplementary.")->prefix("/supplementary")->group(function () {
            Route::get("index/{product_creation_process}", [Product\ProductCreation\ProductShow\SupplementaryController::class, "index"])->name("index");
        });
        Route::name("sale.")->prefix("/sale")->group(function () {
            Route::get("index/{product_creation_process}", [Product\ProductCreation\ProductShow\SaleController::class, "index"])->name("index");
        });
        Route::name("warehouse.")->prefix("/warehouse")->group(function () {
            Route::get("index/{product_creation_process}", [Product\ProductCreation\ProductShow\WarehouseController::class, "index"])->name("index");
        });
        Route::name("quality_control.")->prefix("/quality_control")->group(function () {
            Route::get("index/{product_creation_process}", [Product\ProductCreation\ProductShow\QualityControlController::class, "index"])->name("index");
        });
        Route::name("planing.")->prefix("/planing")->group(function () {
            Route::get("index/{product_creation_process}", [Product\ProductCreation\ProductShow\PlaningController::class, "index"])->name("index");
        });
        Route::name("classification.")->prefix("/classification")->group(function () {
            Route::get("index/{product_creation_process}", [Product\ProductCreation\ProductShow\ClassificationController::class, "index"])->name("index");
        });
        Route::name("property.")->prefix("/property")->group(function () {
            Route::get("index/{product_creation_process}", [Product\ProductCreation\ProductShow\PropertyController::class, "index"])->name("index");
        });
           Route::name("consumed_product.")->prefix("/consumed_product")->group(function () {
               Route::get("index/{product_creation_process}", [Product\ProductCreation\ProductShow\ConsumedProductController::class, "index"])->name("index");
           });
        Route::name("route.")->prefix("/route")->group(function () {
            Route::get("index/{product_creation_process}", [Product\ProductCreation\ProductShow\ProductRouteController::class, "index"])->name("index");
        });
        Route::name("route_property.")->prefix("/route_property")->group(function () {
            Route::get("index/{product_creation_process}", [Product\ProductCreation\ProductShow\ProductRoutePropertyController::class, "index"])->name("index");
        });
        Route::name("bom.")->prefix("/bom")->group(function () {
            Route::get("index/{product_creation_process}", [Product\ProductCreation\ProductShow\BOMController::class, "index"])->name("index");
            Route::get("bom_degree/{product_creation_process}/{material}/{bom_item}", [Product\ProductCreation\ProductShow\BOMController::class, "bom_degree"])->name("bom_degree");
            Route::get("bom_replace/{product_creation_process}/{material}/{bom_item}", [Product\ProductCreation\ProductShow\BOMController::class, "bom_replace"])->name("bom_replace");
            Route::get("bom_fault_illegal/{product_creation_process}/{material}/{bom_item}", [Product\ProductCreation\ProductShow\BOMController::class, "bom_fault_illegal"])->name("bom_fault_illegal");
        });
        Route::name("bom_permutation.")->prefix("/bom_permutation")->group(function () {
            Route::get("index/{product_creation_process}", [Product\ProductCreation\ProductShow\BOMPermutationController::class, "index"])->name("index");
        });
        Route::name("replace_product.")->prefix("/replace_product")->group(function () {
            Route::get("index/{product_creation_process}", [Product\ProductCreation\ProductShow\ReplaceProductController::class, "index"])->name("index");
        });
        Route::name("waste.")->prefix("/waste")->group(function () {
            Route::get("index/{product_creation_process}", [Product\ProductCreation\ProductShow\WasteController::class, "index"])->name("index");
        });
        Route::name("material_flow.")->prefix("/material_flow")->group(function () {
            Route::get("index/{product_creation_process}", [Product\ProductCreation\ProductShow\MaterialFlowController::class, "index"])->name("index");
            Route::get("graph/{product_creation_process}/{bom}/{line_product_station}", [Product\ProductCreation\ProductShow\MaterialFlowController::class, "graph"])->name("graph");
        });
        Route::name("lot_number.")->prefix("/lot_number")->group(function () {
            Route::get("index/{product_creation_process}", [Product\ProductCreation\ProductShow\LotNumberController::class, "index"])->name("index");
        });
        Route::name("shade_number.")->prefix("/shade_number")->group(function () {
            Route::get("index/{product_creation_process}", [Product\ProductCreation\ProductShow\ShadeNumberController::class, "index"])->name("index");
        });
             Route::name("packing_type.")->prefix("/packing_type")->group(function () {
                 Route::get("index/{product_creation_process}", [Product\ProductCreation\ProductShow\PackingTypeController::class, "index"])->name("index");
             });
        Route::name("pricing.")->prefix("/pricing")->group(function () {
            Route::get("index/{product_creation_process}", [Product\ProductCreation\ProductShow\PricingController::class, "index"])->name("index");
        });

        Route::name("version.")->prefix("/version")->group(function () {
            Route::get("index/{product_creation_process}", [Product\ProductCreation\ProductShow\VersionController::class, "index"])->name("index");
            Route::get("details/{product}/{type}/{product_version}/{product_creation_process}", [Product\ProductCreation\ProductShow\VersionController::class, "details"])->name("details");
        });
    });

    Route::name("new_form.")->prefix("/new_form")->group(function () {
        Route::get("index/{product_creation_process_id?}", [
            Product\ProductCreation\NewFormController::class,
            "index"
        ])->name("index");
        Route::post("submit", [Product\ProductCreation\NewFormController::class, "submit"])->name("submit");
        Route::get("posttex_address/{product_creation_process}", [
            Product\ProductCreation\NewFormController::class,
            "posttex_address"
        ])->name("posttex_address");
        Route::get("create_from_exist_product/{product_creation_process_id?}", [
            Product\ProductCreation\NewFormController::class,
            "create_from_exist_product"
        ])->name("create_from_exist_product");
        Route::post("submit_from_exist_product", [Product\ProductCreation\NewFormController::class, "submit_from_exist_product"])->name("submit_from_exist_product");

    });
// تعریف سریع کالا
    Route::name("new_form_quick.")->prefix("/new_form_quick")->group(function () {
        Route::get("index", [
            Product\ProductCreation\NewFormQuickController::class,
            "index"
        ])->name("index");
        Route::post("submit", [Product\ProductCreation\NewFormQuickController::class, "submit"])->name("submit");
    });

    Route::name("receive_product_sample.")->prefix("/receive_product_sample")->group(function () {
        Route::post("submit/{product_creation_process}", [
            Product\ProductCreation\ReceiveProductSampleController::class,
            "submit"
        ])->name("submit");
    });
    Route::name("step1_design.")->prefix("/step1_design")->group(function () {
        Route::post("submit/{product_creation_process}", [
            Product\ProductCreation\Step1DesignController::class,
            "submit"
        ])->name("submit");
    });
    Route::name("step2_design.")->prefix("/step2_design")->group(function () {
        Route::post("submit/{product_creation_process}", [
            Product\ProductCreation\Step2DesignController::class,
            "submit"
        ])->name("submit");
    });
    Route::name("step3_design.")->prefix("/step3_design")->group(function () {
        Route::post("submit/{product_creation_process}", [
            Product\ProductCreation\Step3DesignController::class,
            "submit"
        ])->name("submit");
    });

    Route::name("step4_design.")->prefix("/step4_design")->group(function () {
        Route::post("submit/{product_creation_process}", [
            Product\ProductCreation\Step4DesignController::class,
            "submit"
        ])->name("submit");
    });

    Route::name("step5_design.")->prefix("/step5_design")->group(function () {
        Route::post("submit/{product_creation_process}", [
            Product\ProductCreation\Step5DesignController::class,
            "submit"
        ])->name("submit");
    });


    Route::name("basic_information_registration.")->prefix("/basic_information_registration")->group(function () {
        Route::get("index/{product_creation_process}", [
            Product\ProductCreation\BasicInformationRegistrationController::class,
            "index"
        ])->name("index");
        Route::post("submit/{product_creation_process}", [
            Product\ProductCreation\BasicInformationRegistrationController::class,
            "submit"
        ])->name("submit");

        Route::get("copy_form_other/{product_creation_process}", [
            Product\ProductCreation\BasicInformationRegistrationController::class,
            "copy_form_other"
        ])->name("copy_form_other");

        Route::post("submit_copy_form_other/{product_creation_process}", [
            Product\ProductCreation\BasicInformationRegistrationController::class,
            "submit_copy_form_other"
        ])->name("submit_copy_form_other");
    });

    Route::name("basic_information_service.")->prefix("/basic_information_service")->group(function () {
        Route::get("index/{product_creation_process}", [
            Product\ProductCreation\BasicInformationServiceController::class,
            "index"
        ])->name("index");
        Route::post("submit/{product_creation_process}", [
            Product\ProductCreation\BasicInformationServiceController::class,
            "submit"
        ])->name("submit");
    });
    Route::name("register_service_in_financial_software.")->prefix("/register_service_in_financial_software")->group(function () {
        Route::get("index/{product_creation_process}", [
            Product\ProductCreation\RegisterServiceInFinancialSoftwareController::class,
            "index"
        ])->name("index");
        Route::post("submit/{product_creation_process}", [
            Product\ProductCreation\RegisterServiceInFinancialSoftwareController::class,
            "submit"
        ])->name("submit");
    });
    Route::name("confirm_final_service.")->prefix("/confirm_final_service")->group(function () {
        Route::post("submit/{product_creation_process}", [
            Product\ProductCreation\ConfirmFinalServiceController::class,
            "submit"
        ])->name("submit");
    });

    Route::name("sale.")->prefix("/sale")->group(function () {
        Route::get("index/{product_creation_process}", [
            Product\ProductCreation\SaleController::class,
            "index"
        ])->name("index");
        Route::post("submit/{product}/{product_creation_process}", [
            Product\ProductCreation\SaleController::class,
            "submit"
        ])->name("submit");
    });

    Route::name("warehouse.")->prefix("/warehouse")->group(function () {
        Route::get("index/{product_creation_process}", [
            Product\ProductCreation\WarehouseController::class,
            "index"
        ])->name("index");
        Route::post("submit/{product}/{product_creation_process}", [
            Product\ProductCreation\WarehouseController::class,
            "submit"
        ])->name("submit");
    });

    Route::name("classification.")->prefix("/classification")->group(function () {
        Route::get("index/{product_creation_process}", [
            Product\ProductCreation\ClassificationController::class,
            "index"
        ])->name("index");
        Route::post("submit/{product}/{product_creation_process}", [
            Product\ProductCreation\ClassificationController::class,
            "submit"
        ])->name("submit");
    });

    Route::name("property.")->prefix("/property")->group(function () {
        Route::get("index/{product_creation_process}", [Product\ProductCreation\PropertyController::class, "index"])->name("index");
        Route::post("submit/{product}/{product_creation_process}", [
            Product\ProductCreation\PropertyController::class,
            "submit"
        ])->name("submit");

        Route::get("upload_image/{product}/{goods_kind_property}/{product_creation_process_id?}", [
            Product\ProductCreation\PropertyController::class,
            "upload_image"
        ])->name("upload_image");
//        Route::post("submit_upload_image/{product}/{goods_kind_property}", [
//            Product\ProductCreation\PropertyController::class,
//            "submit_upload_image"
//        ])->name("submit_upload_image");
        Route::get("delete/{product}/{goods_kind_property}", [
            Product\ProductCreation\PropertyController::class,
            "delete"
        ])->name("delete");
    });

    Route::name("property_quick.")->prefix("/property_quick")->group(function () {
        Route::get("index/{product_creation_process}", [Product\ProductCreation\PropertyQuickController::class, "index"])->name("index");
        Route::post("submit/{product}/{product_creation_process}", [
            Product\ProductCreation\PropertyQuickController::class,
            "submit"
        ])->name("submit");

        Route::get("upload_image/{product}/{goods_kind_property}/{product_creation_process_id?}", [
            Product\ProductCreation\PropertyQuickController::class,
            "upload_image"
        ])->name("upload_image");
//        Route::post("submit_upload_image/{product}/{goods_kind_property}", [
//            Product\ProductCreation\PropertyQuickController::class,
//            "submit_upload_image"
//        ])->name("submit_upload_image");
        Route::get("delete/{product}/{goods_kind_property}", [
            Product\ProductCreation\PropertyQuickController::class,
            "delete"
        ])->name("delete");
    });

    Route::name("consumed_product.")->prefix("/consumed_product")->group(function () {
        Route::get("index/{product_creation_process}", [
            Product\ProductCreation\ConsumedProductController::class,
            "index"
        ])->name("index");
        Route::post("submit/{product}/{product_creation_process}", [
            Product\ProductCreation\ConsumedProductController::class,
            "submit"
        ])->name("submit");

        Route::get("delete/{consumed_product}/{product}/{material_id?}/{product_creation_process?}", [
            Product\ProductCreation\ConsumedProductController::class,
            "delete"
        ])->name("delete");

        Route::get("replace/{consumed_product}/{product}/{product_creation_process}", [
            Product\ProductCreation\ConsumedProductController::class,
            "replace"
        ])->name("replace");

        Route::post("store_replace/{consumed_product}/{product}/{product_creation_process}", [
            Product\ProductCreation\ConsumedProductController::class,
            "store_replace"
        ])->name("store_replace");

        Route::get("change_choose_material/{product}/{material_id?}/{product_creation_process?}", [
            Product\ProductCreation\ConsumedProductController::class,
            "change_choose_material"
        ])->name("change_choose_material");


        Route::get("confirm_step/{product_creation_process}", [
            Product\ProductCreation\ConsumedProductController::class,
            "confirm_step"
        ])->name("confirm_step");


    });

    Route::name("consumed_product_quick.")->prefix("/consumed_product_quick")->group(function () {
        Route::get("index/{product_creation_process}", [
            Product\ProductCreation\ConsumedProductQuickController::class,
            "index"
        ])->name("index");
        Route::post("submit/{product}/{product_creation_process}", [
            Product\ProductCreation\ConsumedProductQuickController::class,
            "submit"
        ])->name("submit");

        Route::get("delete/{consumed_product}/{product}/{material_id?}/{product_creation_process?}", [
            Product\ProductCreation\ConsumedProductQuickController::class,
            "delete"
        ])->name("delete");

        Route::get("replace/{consumed_product}/{product}/{product_creation_process}", [
            Product\ProductCreation\ConsumedProductQuickController::class,
            "replace"
        ])->name("replace");

        Route::post("store_replace/{consumed_product}/{product}/{product_creation_process}", [
            Product\ProductCreation\ConsumedProductQuickController::class,
            "store_replace"
        ])->name("store_replace");

        Route::get("change_choose_material/{product}/{material_id?}/{product_creation_process?}", [
            Product\ProductCreation\ConsumedProductQuickController::class,
            "change_choose_material"
        ])->name("change_choose_material");


        Route::get("confirm_step/{product_creation_process}", [
            Product\ProductCreation\ConsumedProductQuickController::class,
            "confirm_step"
        ])->name("confirm_step");
    });

    Route::name("waste.")->prefix("/waste")->group(function () {

        Route::get("index/{product_creation_process}", [Product\ProductCreation\WasteController::class, "index"])->name("index");
        Route::post("submit/{product}/{product_creation_process}", [Product\ProductCreation\WasteController::class, "submit"])->name("submit");

    });


    Route::name("material_flow.")->prefix("/material_flow")->group(function () {
        Route::get("index/{product_creation_process}", [
            Product\ProductCreation\MaterialFlowController::class,
            "index"
        ])->name("index");

        Route::get("graph/{product}/{bom}/{line_product_station}/{product_creation_process}", [
            Product\ProductCreation\MaterialFlowController::class,
            "graph"
        ])->name("graph");
        Route::get("draw_graph_one_to_one/{product}/{bom}/{line_product_station}/{product_creation_process}", [
            Product\ProductCreation\MaterialFlowController::class,
            "draw_graph_one_to_one"
        ])->name("draw_graph_one_to_one");

        Route::get("confirm_step/{product_creation_process}", [
            Product\ProductCreation\MaterialFlowController::class,
            "confirm_step"
        ])->name("confirm_step");
    });


    #Packing Type
    Route::name("packing_type.")->prefix("/packing_type")->group(function () {
        Route::get("index/{product_creation_process}", [Product\ProductCreation\PackingTypeController::class, "index"])->name("index");
        Route::post("submit/{product}/{product_creation_process}", [Product\ProductCreation\PackingTypeController::class, "submit"])->name("submit");
    });

    #Quality Control
    Route::name("quality_control.")->prefix("/quality_control")->group(function () {
        Route::get("index/{product_creation_process}", [Product\ProductCreation\QualityControlController::class, "index"])->name("index");
        Route::post("submit/{product}/{product_creation_process}", [Product\ProductCreation\QualityControlController::class, "submit"])->name("submit");
    });


    #Planing
    Route::name("planing.")->prefix("/planing")->group(function () {
        Route::get("index/{product_creation_process}", [Product\ProductCreation\PlaningController::class, "index"])->name("index");
        Route::post("submit/{product}/{product_creation_process}", [Product\ProductCreation\PlaningController::class, "submit"])->name("submit");
    });

    #lotNumber
    Route::name("lot_number.")->prefix("/lot_number")->group(function () {
        Route::get("index/{product_creation_process}", [
            Product\ProductCreation\LotNumberController::class,
            "index"
        ])->name("index");
        Route::post("store/{product}/{product_creation_process}", [
            Product\ProductCreation\LotNumberController::class,
            "store"
        ])->name("store");
        Route::get("edit/{product}/{lot_number}/{product_creation_process}", [
            Product\ProductCreation\LotNumberController::class,
            "edit"
        ])->name("edit");
        Route::post("update/{product}/{lot_number}/{product_creation_process}", [
            Product\ProductCreation\LotNumberController::class,
            "update"
        ])->name("update");
        Route::get("confirm_step/{product_creation_process}", [
            Product\ProductCreation\LotNumberController::class,
            "confirm_step"
        ])->name("confirm_step");
    });

    #Shade Number
    Route::name("shade_number.")->prefix("/shade_number")->group(function () {
        Route::get("index/{product_creation_process}", [
            Product\ProductCreation\ShadeNumberController::class,
            "index"
        ])->name("index");
        Route::post("store/{product}/{product_creation_process}", [
            Product\ProductCreation\ShadeNumberController::class,
            "store"
        ])->name("store");
        Route::get("confirm_step/{product_creation_process}", [
            Product\ProductCreation\ShadeNumberController::class,
            "confirm_step"
        ])->name("confirm_step");
    });


    #Actual Cost
    Route::name("actual_cost.")->prefix("/actual_cost")->group(function () {

        Route::get("index/{product_creation_process}", [Product\ProductCreation\ActualCostController::class, "index"])->name("index");
        Route::get("confirm_step/{product_creation_process}", [
            Product\ProductCreation\ActualCostController::class,
            "confirm_step"
        ])->name("confirm_step");
    });

    # Route
    Route::name("route.")->prefix("/route")->group(function () {

        Route::get("index/{product_creation_process}", [
            Product\ProductCreation\ProductRouteController::class,
            "index"
        ])->name("index");
        Route::get("confirm_step/{product_creation_process}", [
            Product\ProductCreation\ProductRouteController::class,
            "confirm_step"
        ])->name("confirm_step");

        Route::get("create/{product}/{product_creation_process}", [
            Product\ProductCreation\ProductRouteController::class,
            "create"
        ])->name("create");

        Route::post("store/{product}/{product_creation_process}", [
            Product\ProductCreation\ProductRouteController::class,
            "store"
        ])->name("store");

        Route::get("edit/{product}/{product_route}/{product_creation_process}", [
            Product\ProductCreation\ProductRouteController::class,
            "edit"
        ])->name("edit");

        Route::post("update/{product}/{product_route}/{product_creation_process}", [
            Product\ProductCreation\ProductRouteController::class,
            "update"
        ])->name("update");

        Route::get("destroy/{product}/{product_route}/{product_creation_process}", [
            Product\ProductCreation\ProductRouteController::class,
            "destroy"
        ])->name("destroy");

    });

    # RouteProperty
    Route::name("route_property.")->prefix("/route_property")->group(function () {

        Route::get("index/{product_creation_process}", [
            Product\ProductCreation\ProductRoutePropertyController::class,
            "index"
        ])->name("index");
        Route::post("submit/{product}/{product_creation_process}", [
            Product\ProductCreation\ProductRoutePropertyController::class,
            "submit"
        ])->name("submit");
        Route::get("confirm_step/{product_creation_process}", [
            Product\ProductCreation\ProductRoutePropertyController::class,
            "confirm_step"
        ])->name("confirm_step");


    });

    # BOM
    Route::name("bom.")->prefix("/bom")->group(function () {

        Route::get("index/{product_creation_process}/{show_route_code?}", [Product\ProductCreation\BOMController::class, "index"])->name("index");
        Route::get("create/{product_route}/{product_creation_process}", [Product\ProductCreation\BOMController::class, "create"])->name("create");
        Route::get("edit/{bom}/{product_creation_process}", [Product\ProductCreation\BOMController::class, "edit"])->name("edit");
        Route::post("update/{bom}/{product_creation_process}", [Product\ProductCreation\BOMController::class, "update"])->name("update");
        Route::get("destroy/{product}/{bom}/{product_creation_process}/{delete_all?}", [
            Product\ProductCreation\BOMController::class,
            "destroy"
        ])->name("destroy");
        Route::get("confirm_step/{product_creation_process}", [
            Product\ProductCreation\BOMController::class,
            "confirm_step"
        ])->name("confirm_step");
    });

    # BOM Permutation
    Route::name("bom_permutation.")->prefix("/bom_permutation")->group(function () {

        Route::get("index/{product_creation_process}", [
            Product\ProductCreation\BOMPermutationController::class,
            "index"
        ])->name("index");

        Route::post("submit/{product}/{product_creation_process}", [
            Product\ProductCreation\BOMPermutationController::class,
            "submit"
        ])->name("submit");

    });

    # Replace Product
    Route::name("replace_product.")->prefix("/replace_product")->group(function () {
        Route::get("index/{product_creation_process}", [
            Product\ProductCreation\ReplaceProductController::class,
            "index"
        ])->name("index");
        Route::post("submit/{product}/{product_creation_process}", [
            Product\ProductCreation\ReplaceProductController::class,
            "submit"
        ])->name("submit");
        Route::get("delete/{product}/{replace_product_id}/{product_creation_process}", [
            Product\ProductCreation\ReplaceProductController::class,
            "delete"
        ])->name("delete");
        Route::get("confirm_step/{product_creation_process}", [
            Product\ProductCreation\ReplaceProductController::class,
            "confirm_step"
        ])->name("confirm_step");
    });

    # Priority Setting
    Route::name("priority_setting.")->prefix("/priority_setting")->
    middleware(['url_check:utility.setting.index'])->group(function () {
        Route::get("index", [
            Product\ProductCreation\PrioritySettingController::class,
            "index"
        ])->name("index");
        Route::post("submit", [
            Product\ProductCreation\PrioritySettingController::class,
            "submit"
        ])->name("submit");

    });

    /********** مازول های نمونه گیری*******/
    Route::name("sample_end_of_production.")->prefix("/sample_end_of_production")->group(function () {
        Route::post("submit/{product_creation_process}", [
            Product\ProductCreation\SampleEndOfProductionController::class,
            "submit"
        ])->name("submit");
    });
    Route::name("sample_final_approval.")->prefix("/sample_final_approval")->group(function () {
        Route::post("submit/{product_creation_process}", [
            Product\ProductCreation\SampleFinalApprovalController::class,
            "submit"
        ])->name("submit");
    });
    Route::name("sample_initial_approval.")->prefix("/sample_initial_approval")->group(function () {

        Route::post("submit/{product_creation_process}", [
            Product\ProductCreation\SampleInitialApprovalController::class,
            "submit"
        ])->name("submit");
    });
    Route::name("sample_production_order.")->prefix("/sample_production_order")->group(function () {

        Route::get("index/{product_creation_process}", [
            Product\ProductCreation\SampleProductionOrderController::class,
            "index"
        ])->name("index");

        Route::post("submit/{product_creation_process}", [
            Product\ProductCreation\SampleProductionOrderController::class,
            "submit"
        ])->name("submit");
    });

    Route::name("sample_send_to_customer.")->prefix("/sample_send_to_customer")->group(function () {
        Route::post("submit/{product_creation_process}", [
            Product\ProductCreation\SampleSendToCustomer::class,
            "submit"
        ])->name("submit");
    });

    Route::name("sample_approval_by_customer.")->prefix("/sample_approval_by_customer")->group(function () {
        Route::post("submit/{product_creation_process}", [
            Product\ProductCreation\SampleApprovalByCustomerController::class,
            "submit"
        ])->name("submit");
    });
    # Product Image
    Route::name("product_image.")->prefix("/product_image")->group(function () {
        Route::get("index/{product_creation_process}", [
            Product\ProductCreation\ProductImageController::class,
            "index"
        ])->name("index");
        Route::post("submit/{product_creation_process}", [
            Product\ProductCreation\ProductImageController::class,
            "submit"
        ])->name("submit");
    });
    Route::name("product_image_quick.")->prefix("/product_image_quick")->group(function () {
        Route::get("index/{product_creation_process}", [
            Product\ProductCreation\ProductImageQuickController::class,
            "index"
        ])->name("index");
        Route::post("submit/{product_creation_process}", [
            Product\ProductCreation\ProductImageQuickController::class,
            "submit"
        ])->name("submit");
    });
    # Register In Financial Software
    Route::name("register_in_financial_software.")->prefix("/register_in_financial_software")->group(function () {
        Route::get("index/{product_creation_process}", [
            Product\ProductCreation\RegisterInFinancialSoftwareController::class,
            "index"
        ])->name("index");
        Route::post("submit/{product_creation_process}", [
            Product\ProductCreation\RegisterInFinancialSoftwareController::class,
            "submit"
        ])->name("submit");
    });


    # Product Tariff
    Route::name("add_tariff_rows.")->prefix("/add_tariff_rows")->group(function () {
        Route::get("index/{product_creation_process}", [
            Product\ProductCreation\AddTariffRowsController::class,
            "index"
        ])->name("index");
        Route::post("submit/{product_creation_process}", [
            Product\ProductCreation\AddTariffRowsController::class,
            "submit"
        ])->name("submit");
        Route::get("create_product_tariff/{product}/{product_creation_process}", [
            Product\ProductCreation\AddTariffRowsController::class,
            "create_product_tariff"
        ])->name("create_product_tariff");


        Route::get("remove_product_tariff_pricing/{product}/{product_tariff_pricing}/{product_creation_process}", [Product\ProductCreation\AddTariffRowsController::class, "remove_product_tariff_pricing"])->name("remove_product_tariff_pricing");
        Route::get("remove_product_tariff/{product}/{product_tariff}/{product_creation_process}", [Product\ProductCreation\AddTariffRowsController::class, "remove_product_tariff"])->name("remove_product_tariff");
        Route::get("edit_product_tariff_pricing/{product}/{product_tariff}/{product_creation_process}", [Product\ProductCreation\AddTariffRowsController::class, "edit_product_tariff_pricing"])->name("edit_product_tariff_pricing");
        Route::post("remove_add_product_tariff/{product}/{product_tariff}/{product_creation_process}", [Product\ProductCreation\AddTariffRowsController::class, "remove_add_product_tariff"])->name("remove_add_product_tariff");


        Route::post("store_product_tariff/{product}/{product_creation_process}", [
            Product\ProductCreation\AddTariffRowsController::class,
            "store_product_tariff"
        ])->name("store_product_tariff");

        Route::get("confirm_step/{product_creation_process}", [
            Product\ProductCreation\AddTariffRowsController::class,
            "confirm_step"
        ])->name("confirm_step");

        Route::post("set_parent_product/{product_creation_process}", [
            Product\ProductCreation\AddTariffRowsController::class,
            "set_parent_product"
        ])->name("set_parent_product");

        Route::get("change_packing_type/{product}/{product_creation_process}", [
            Product\ProductCreation\AddTariffRowsController::class,
            "change_packing_type"
        ])->name("change_packing_type");

        Route::post("submit_change_packing_type/{product}/{product_creation_process}", [
            Product\ProductCreation\AddTariffRowsController::class,
            "submit_change_packing_type"
        ])->name("submit_change_packing_type");


        Route::get("add_tariff_together/{product}/{product_tariff_pricing}/{product_creation_process}", [Product\ProductCreation\AddTariffRowsController::class, "add_tariff_together"])->name("add_tariff_together");
        Route::match(['get', 'post'],"show_tariff_together/{product}/{product_tariff_pricing}/{product_creation_process}", [Product\ProductCreation\AddTariffRowsController::class, "show_tariff_together"])->name("show_tariff_together");
        Route::post("submit_tariff_together/{product}/{product_tariff_pricing}/{product_creation_process}", [Product\ProductCreation\AddTariffRowsController::class, "submit_tariff_together"])->name("submit_tariff_together");



    });
    #  Pricing
    Route::name("pricing.")->prefix("/pricing")->group(function () {
        Route::get("index/{product_creation_process}", [
            Product\ProductCreation\PricingController::class,
            "index"
        ])->name("index");
        Route::post("submit_add_pricing_to_tariff/{product}/{product_creation_process}", [
            Product\ProductCreation\PricingController::class,
            "submit_add_pricing_to_tariff"
        ])->name("submit_add_pricing_to_tariff");

    });

});


Route::middleware(['url_check:line_product_station.line.index'])->name("product_type.")->prefix("/product_type")->group(function () {
    Route::get("index/{goods_kind}", [ProductTypeController::class, "index"])->name("index");
    Route::get("create/{goods_kind}", [ProductTypeController::class, "create"])->name("create");
    Route::post("store/{goods_kind}", [ProductTypeController::class, "store"])->name("store");
    Route::get("edit/{product_type}", [ProductTypeController::class, "edit"])->name("edit");
    Route::post("update/{product_type}", [ProductTypeController::class, "update"])->name("update");
    Route::get("destroy/{product_type}", [ProductTypeController::class, "destroy"])->name("destroy");
});



Route::middleware(['url_check:line_product_station.line.index'])->name("production_method.")->prefix("/production_method")->group(function () {
    Route::get("index", [ProductionMethodController::class, "index"])->name("index");
    Route::get("create", [ProductionMethodController::class, "create"])->name("create");
    Route::post("store", [ProductionMethodController::class, "store"])->name("store");
    Route::get("edit/{production_method}", [ProductionMethodController::class, "edit"])->name("edit");
    Route::post("update/{production_method}", [ProductionMethodController::class, "update"])->name("update");
});


Route::middleware(['url_check:line_product_station.goods_kind.index'])->name("goods_kind.")->prefix("/goods_kind")->group(function () {
    Route::get("index", [GoodsKindController::class, "index"])->name("index");
    Route::get("create", [GoodsKindController::class, "create"])->name("create");
    Route::post("store", [GoodsKindController::class, "store"])->name("store");
    Route::get("edit/{goods_kind}", [GoodsKindController::class, "edit"])->name("edit");
    Route::post("update/{goods_kind}", [GoodsKindController::class, "update"])->name("update");
    Route::get("destroy/{goods_kind}", [GoodsKindController::class, "destroy"])->name("destroy");

    Route::get("packing_type_list/{goods_kind}", [
        GoodsKindController::class,
        "packing_type_list"
    ])->name("packing_type_list");
    Route::post("packing_type_add_store/{goods_kind}", [
        GoodsKindController::class,
        "packing_type_add_store"
    ])->name("packing_type_add_store");
    Route::get("packing_type_delete/{goods_kind}/{packing_type}", [
        GoodsKindController::class,
        "packing_type_delete"
    ])->name("packing_type_delete");

    Route::name("product_fault.")->prefix("/product_fault")->group(function () {

        Route::get("index/{goods_kind}", [
            GoodsKind\GoodsKindProductFaultController::class,
            "index"
        ])->name("index");
        Route::post("store/{goods_kind}", [
            GoodsKind\GoodsKindProductFaultController::class,
            "store"
        ])->name("store");
        Route::get("delete/{goods_kind}/{goods_kind_product_fault}", [
            GoodsKind\GoodsKindProductFaultController::class,
            "delete"
        ])->name("delete");

    });

    Route::name("setting.product_creation_priority.")->prefix("/setting/product_creation_priority/")->group(function () {

        Route::get("index/{goods_kind}", [
            GoodsKind\GoodsKindProductCreationPrioritySettingController::class,
            "index"
        ])->name("index");
        Route::post("submit/{goods_kind}", [
            GoodsKind\GoodsKindProductCreationPrioritySettingController::class,
            "submit"
        ])->name("submit");

    });

    Route::name("setting.")->prefix("/setting")->group(function () {

        Route::get("index/{goods_kind}/{tab_index?}", [
            GoodsKind\GoodsKindSettingController::class,
            "index"
        ])->name("index");

        Route::post("init_info_store/{goods_kind}", [
            GoodsKind\GoodsKindSettingController::class,
            "init_info_store"
        ])->name("init_info_store");

        Route::post("submit_algorithm_info/{goods_kind}", [
            GoodsKind\GoodsKindSettingController::class,
            "submit_algorithm_info"
        ])->name("submit_algorithm_info");


    });

    Route::name("property.")->prefix("/property")->group(function () {
        Route::get("index/{goods_kind}", [GoodsKindPropertyController::class, "index"])->name("index");

        Route::get("create/{goods_kind}", [GoodsKindPropertyController::class, "create"])->name("create");
        Route::post("store/{goods_kind}", [GoodsKindPropertyController::class, "store"])->name("store");

        Route::get("edit/{goods_kind}/{goods_kind_property}", [
            GoodsKindPropertyController::class,
            "edit"
        ])->name("edit");
        Route::post("update/{goods_kind}/{goods_kind_property}", [
            GoodsKindPropertyController::class,
            "update"
        ])->name("update");

        Route::get("destroy/{goods_kind}/{goods_kind_property}", [
            GoodsKindPropertyController::class,
            "destroy"
        ])->name("destroy");
        Route::get("edit_product_type/{goods_kind}/{goods_kind_property}", [
            GoodsKindPropertyController::class,
            "edit_product_type"
        ])->name("edit_product_type");
        Route::post("update_product_type/{goods_kind}/{goods_kind_property}", [
            GoodsKindPropertyController::class,
            "update_product_type"
        ])->name("update_product_type");

        Route::get("edit_property_dependent/{goods_kind_property}", [
            GoodsKindPropertyController::class,
            "edit_property_dependent"
        ])->name("edit_property_dependent");
        Route::post("update_property_dependent/{goods_kind_property}", [
            GoodsKindPropertyController::class,
            "update_property_dependent"
        ])->name("update_property_dependent");

    });

    Route::name("option.")->prefix("/option")->group(function () {
        Route::get("index/{goods_kind_property}", [GoodsKindOptionController::class, "index"])->name("index");
        Route::post("store/{goods_kind_property}", [GoodsKindOptionController::class, "store"])->name("store");
        Route::post("update/{goods_kind_property}/{good_kind_property", [
            GoodsKindOptionController::class,
            "update"
        ])->name("update");
        Route::get("destroy/{goods_kind_property}/{goods_kind_property_option}", [
            GoodsKindOptionController::class,
            "destroy"
        ])->name("destroy");

        Route::get("edit_number/{goods_kind_property}", [
            GoodsKindOptionController::class,
            "edit_number"
        ])->name("edit_number");
        Route::post("update_number/{goods_kind_property}", [
            GoodsKindOptionController::class,
            "update_number"
        ])->name("update_number");

    });

    Route::name("classification.")->prefix("/classification")->group(function () {
        Route::get("index/{goods_kind}", [GoodsKindClassificationController::class, "index"])->name("index");
        Route::get("create/{goods_kind}", [GoodsKindClassificationController::class, "create"])->name("create");
        Route::post("store/{goods_kind}", [GoodsKindClassificationController::class, "store"])->name("store");
        Route::get("edit/{goods_kind_classification}", [
            GoodsKindClassificationController::class,
            "edit"
        ])->name("edit");
        Route::post("update/{goods_kind_classification}", [
            GoodsKindClassificationController::class,
            "update"
        ])->name("update");
        Route::get("destroy/{goods_kind_classification}", [
            GoodsKindClassificationController::class,
            "destroy"
        ])->name("destroy");
        Route::get("change_classification_type/{goods_kind_classification}", [
            GoodsKindClassificationController::class,
            "change_classification_type"
        ])->name("change_classification_type");

        Route::name("option.")->prefix("/option")->group(function () {
            Route::get("index/{goods_kind_classification}", [
                GoodsKindClassificationOptionController::class,
                "index"
            ])->name("index");
            Route::get("create/{goods_kind_classification}", [
                GoodsKindClassificationOptionController::class,
                "create"
            ])->name("create");
            Route::post("store/{goods_kind_classification}", [
                GoodsKindClassificationOptionController::class,
                "store"
            ])->name("store");
            Route::get("edit/{goods_kind_classification_option}", [
                GoodsKindClassificationOptionController::class,
                "edit"
            ])->name("edit");
            Route::post("update/{goods_kind_classification_option}", [
                GoodsKindClassificationOptionController::class,
                "update"
            ])->name("update");
            Route::get("destroy/{goods_kind_classification_option}", [
                GoodsKindClassificationOptionController::class,
                "destroy"
            ])->name("destroy");
        });

    });


    Route::get("production_waiting_status/{goods_kind}", [
        GoodsKindController::class,
        "production_waiting_status"
    ])->name("production_waiting_status");
    Route::get("production_form_status/{goods_kind}", [
        GoodsKindController::class,
        "production_form_status"
    ])->name("production_form_status");

});


Route::middleware(['url_check:line_product_station.goods_kind.index'])->name("degree.")->prefix("/degree")->group(function () {
    Route::get("index/{goods_kind}", [DegreeController::class, "index"])->name("index");
    Route::get("create/{goods_kind}", [DegreeController::class, "create"])->name("create");
    Route::post("store/{goods_kind}", [DegreeController::class, "store"])->name("store");
    Route::get("edit/{goods_kind}/{degree}", [DegreeController::class, "edit"])->name("edit");
    Route::post("update/{goods_kind}/{degree}", [DegreeController::class, "update"])->name("update");
});


Route::middleware(['url_check:line_product_station.carrier.index'])->name("carrier.")->prefix("/carrier")->group(function () {
    Route::get("index/{code??}", [Carrier\DashboardController::class, "index"])->name("index");
    Route::post("search", [Carrier\DashboardController::class, "search"])->name("search");
    Route::get("edit/{carrier}", [Carrier\DashboardController::class, "edit"])->name("edit");
    Route::post("update/{carrier}", [Carrier\DashboardController::class, "update"])->name("update");
});


Route::middleware(['url_check:line_product_station.carrier.carrier_type.index'])->name("carrier.carrier_type_ic.")->prefix("/carrier/carrier_type_ic")->group(function () {
    Route::get("index/{search??}", [Carrier\CarrierTypeIcController::class, "index"])->name("index");
    Route::post("search", [Carrier\CarrierTypeIcController::class, "search"])->name("search");
    Route::get("create/{carrier_type_id}", [Carrier\CarrierTypeIcController::class, "create"])->name("create");
    Route::get("edit/{carrier_type}", [Carrier\CarrierTypeIcController::class, "edit"])->name("edit");
    Route::post("update/{carrier_type}", [Carrier\CarrierTypeIcController::class, "update"])->name("update");
});

Route::name("reservoir.")->prefix("/reservoir")->group(function () {

Route::middleware(['url_check:line_product_station.reservoir.definition.index'])->name("definition.")->prefix("/definition")->group(function () {
    Route::get("index/", [Reservoir\DefinitionController::class, "index"])->name("index");
    Route::get("create", [Reservoir\DefinitionController::class, "create"])->name("create");
    Route::post("store", [Reservoir\DefinitionController::class, "store"])->name("store");
    Route::get("edit/{reservoir}", [Reservoir\DefinitionController::class, "edit"])->name("edit");
    Route::post("update/{reservoir}", [Reservoir\DefinitionController::class, "update"])->name("update");
    Route::get("print_label/{reservoir}", [Reservoir\DefinitionController::class, "print_label"])->name("print_label");


});
Route::middleware(['url_check:line_product_station.reservoir.definition.index'])->name("dashboard.")->prefix("/dashboard")->group(function () {


    Route::get("index/{reservoir}", [Reservoir\DashboardController::class, "index"])->name("index");
    Route::get("log/{reservoir}", [Reservoir\DashboardController::class, "log"])->name("log");
});
Route::middleware(['url_check:line_product_station.reservoir.definition.index'])->name("inject_to_reservoir.")->prefix("/inject_to_reservoir")->group(function () {


    Route::get("index/{reservoir}", [Reservoir\InjectToReservoirController::class, "index"])->name("index");
    Route::post("submit/{reservoir}", [Reservoir\InjectToReservoirController::class, "submit"])->name("submit");
});
});


Route::middleware(['url_check:line_product_station.carrier.carrier_type.index'])->name("carrier.carrier_type.")->prefix("/carrier/carrier_type")->group(function () {

    Route::match(['get', 'post'], "index", [Carrier\CarrierTypeController::class, "index"])->name("index");

    Route::get("create", [Carrier\CarrierTypeController::class, "create"])->name("create");
    Route::post("store", [Carrier\CarrierTypeController::class, "store"])->name("store");

    Route::get("edit/{carrier_type}", [Carrier\CarrierTypeController::class, "edit"])->name("edit");
    Route::post("update/{carrier_type}", [Carrier\CarrierTypeController::class, "update"])->name("update");

    Route::get("carrier_list/{carrier_type}", [
        Carrier\CarrierTypeController::class,
        "carrier_list"
    ])->name("carrier_list");

    Route::get("create_carrier/{carrier_type}", [
        Carrier\CarrierTypeController::class,
        "create_carrier"
    ])->name("create_carrier");
    Route::post("store_carrier/{carrier_type}", [
        Carrier\CarrierTypeController::class,
        "store_carrier"
    ])->name("store_carrier");

    Route::get("edit_carrier/{carrier_type}/{carrier}", [
        Carrier\CarrierTypeController::class,
        "edit_carrier"
    ])->name("edit_carrier");
    Route::post("update_carrier/{carrier_type}/{carrier}", [
        Carrier\CarrierTypeController::class,
        "update_carrier"
    ])->name("update_carrier");

    Route::get("download/{carrier}", [Carrier\CarrierTypeController::class, "download"])->name("download");
    Route::get("direct_print/{carrier}", [
        Carrier\CarrierTypeController::class,
        "direct_print"
    ])->name("direct_print");

});


//Route::middleware(['url_check:line_product_station.carrier_type.index'])->name("carrier_type.")->prefix("/carrier_type")->group(function () {
//    Route::get("index/{goods_kind}", [CarrierTypeController::class, "index"])->name("index");
//    Route::post("store/{goods_kind}", [CarrierTypeController::class, "store"])->name("store");
//    Route::get("edit/{goods_kind}/{carrier_type}", [CarrierTypeController::class, "edit"])->name("edit");
//    Route::post("update/{goods_kind}/{carrier_type}", [CarrierTypeController::class, "update"])->name("update");
//    Route::get("delete/{goods_kind}/{carrier_type}", [CarrierTypeController::class, "delete"])->name("delete");
//});


Route::middleware(['url_check:line_product_station.maintenance.dashboard.index'])->name("maintenance.")->prefix("/maintenance")->group(function () {
    Route::name("dashboard.")->prefix("/dashboard")->group(function () {

        Route::match(['get', 'post'], "index", [
            Maintenance\DashboardController::class,
            "index"
        ])->name("index");
        Route::get("view/{maintenance}", [Maintenance\DashboardController::class, "view"])->name("view");

        Route::post("submit_start/{maintenance}", [
            Maintenance\DashboardController::class,
            "submit_start"
        ])->name("submit_start");

        Route::post("submit_end/{maintenance}", [
            Maintenance\DashboardController::class,
            "submit_end"
        ])->name("submit_end");


        Route::post("confirm_maintenance/{maintenance}", [
            Maintenance\DashboardController::class,
            "confirm_maintenance"
        ])->name("confirm_maintenance");

        Route::post("reject_maintenance/{maintenance}", [
            Maintenance\DashboardController::class,
            "reject_maintenance"
        ])->name("reject_maintenance");

    });
});

Route::middleware(['url_check:line_product_station.packing.packing_type.index'])->name("packing.")->prefix("/packing")->group(function () {
    Route::name("packing_type.")->prefix("/packing_type")->group(function () {

        Route::match(['get', 'post'], "index", [
            Packing\PackingTypeController::class,
            "index"
        ])->name("index");
        Route::get("edit/{packing_type}", [Packing\PackingTypeController::class, "edit"])->name("edit");
        Route::post("update/{packing_type}", [Packing\PackingTypeController::class, "update"])->name("update");
        Route::get("create", [Packing\PackingTypeController::class, "create"])->name("create");
        Route::post("store", [Packing\PackingTypeController::class, "store"])->name("store");
        Route::get("add_layer/{packing_type}", [
            Packing\PackingTypeController::class,
            "add_layer"
        ])->name("add_layer");
        Route::post("add_layer_store/{packing_type}", [
            Packing\PackingTypeController::class,
            "add_layer_store"
        ])->name("add_layer_store");
        Route::get("remove_layer/{packing_type}/{layer_code}", [
            Packing\PackingTypeController::class,
            "remove_layer"
        ])->name("remove_layer");


        Route::get("change_machine_type/{packing_type}", [
            Packing\PackingTypeController::class,
            "change_machine_type"
        ])->name("change_machine_type");
        Route::post("submit_change_machine_type/{packing_type}", [
            Packing\PackingTypeController::class,
            "submit_change_machine_type"
        ])->name("submit_change_machine_type");

    });
    Route::name("packing_type_ic.")->prefix("/packing_type_ic")->group(function () {

        Route::get("index/{search??}", [Packing\PackingTypeInIcController::class, "index"])->name("index");
        Route::post("search", [Packing\PackingTypeInIcController::class, "search"])->name("search");
        Route::get("create/{packing_type_id}", [Packing\PackingTypeInIcController::class, "create"])->name("create");
        Route::get("edit/{packing_type}", [Packing\PackingTypeInIcController::class, "edit"])->name("edit");
        Route::post("update/{packing_type}", [Packing\PackingTypeInIcController::class, "update"])->name("update");
        Route::get("show/{packing_type}", [Packing\PackingTypeInIcController::class, "show"])->name("show");

    });
});


Route::middleware(['url_check:line_product_station.production_channel_type.definition.index'])->name("production_channel_type.")->prefix("/production_channel_type")->group(function () {

    Route::name("definition.")->prefix("/definition")->group(function () {
        Route::get("index", [ProductionChannelType\DefinitionController::class, "index"])->name("index");
        Route::get("create", [ProductionChannelType\DefinitionController::class, "create"])->name("create");
        Route::post("store", [ProductionChannelType\DefinitionController::class, "store"])->name("store");
        Route::get("edit/{production_channel_type}", [ProductionChannelType\DefinitionController::class, "edit"])->name("edit");
        Route::post("update/{production_channel_type}", [ProductionChannelType\DefinitionController::class, "update"])->name("update");
        Route::post("update_machine_types/{production_channel_type}", [ProductionChannelType\DefinitionController::class, "update_machine_types"])->name("update_machine_types");
        Route::get("edit_machine_production_channel_type/{machine_type}/{production_channel_type}", [ProductionChannelType\DefinitionController::class, "edit_machine_production_channel_type"])->name("edit_machine_production_channel_type");
        Route::post("update_machine_production_channel_type/{machine_type}/{production_channel_type}", [ProductionChannelType\DefinitionController::class, "update_machine_production_channel_type"])->name("update_machine_production_channel_type");

        Route::get("edit_next_ones/{machine_type}/{production_channel_type}", [ProductionChannelType\DefinitionController::class, "edit_next_ones"])->name("edit_next_ones");
        Route::post("update_next_ones/{machine_type}/{production_channel_type}", [ProductionChannelType\DefinitionController::class, "update_next_ones"])->name("update_next_ones");
        Route::get("delete_next_ones/{machine_type}/{production_channel_type}/{next_production_channel_type_id}", [ProductionChannelType\DefinitionController::class, "delete_next_ones"])->name("delete_next_ones");

        Route::get("edit_before_ones/{machine_type}/{production_channel_type}", [ProductionChannelType\DefinitionController::class, "edit_before_ones"])->name("edit_before_ones");
        Route::post("update_before_ones/{machine_type}/{production_channel_type}", [ProductionChannelType\DefinitionController::class, "update_before_ones"])->name("update_before_ones");
        Route::get("delete_before_ones/{machine_type}/{production_channel_type}/{next_production_channel_type_id}", [ProductionChannelType\DefinitionController::class, "delete_before_ones"])->name("delete_before_ones");


    });
});
