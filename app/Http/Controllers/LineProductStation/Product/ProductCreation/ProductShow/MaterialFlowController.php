<?php

namespace App\Http\Controllers\LineProductStation\Product\ProductCreation\ProductShow;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\LineProductStation;
use App\Models\LineProduct\Machine\MachineTypeOutputBand;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\ProductCreation\ProductCreationProcess;
use Illuminate\Http\Request;

class MaterialFlowController extends Controller
{
    //
    var $view_path = "line_product_station.product.product_creation.product_show.material_flow.";
    var $route_path = "line_product_station.product.product_creation.product_show.material_flow.";

    public function index(ProductCreationProcess $product_creation_process)
    {

        return \App\Http\Controllers\LineProductStation\Product\MaterialFlowController::GetIndex($product_creation_process->product, $this->view_path, $this->route_path, $product_creation_process);
    }
    public
    function graph(
        ProductCreationProcess $product_creation_process, Product\BOM\BOM $bom, LineProductStation $line_product_station
    )
    {
        return self::GetGraph($product_creation_process->product, $bom, $line_product_station, $this->view_path, $this->route_path, $product_creation_process);
    }

    public static function GetGraph(Product $product, Product\BOM\BOM $bom, LineProductStation $line_product_station, $view_path, $route_path, $product_creation_process)
    {
        // حذف لینک های نامعتبر در صورتی که وجود داشته باشند، یعنی نوع مسیر محصول را تغییر داده باشند
        $list = Product\MaterialFlow::where([
            "product_id" => $product->id,
            "bill_of_material_id" => $bom->id,
        ])->
        with("bom_item")->
        get();
        foreach ($list as $item) {
            if (!$item->bom_item) {
                $item->delete();
            }
        }

        $option = self::getOption($product, $bom, $line_product_station);
        /// return ($option);
        $info = "graph";

        return view($view_path . "index", compact("info", "option", "bom", "product", "line_product_station", "product_creation_process", "view_path", "route_path"));

    }public static function getOption(
    Product $product, Product\BOM\BOM $bom, LineProductStation $line_product_station
)
{

    // آیا قبل از خط محصول، کالا از یک گروه ماشین دیگری خارج می شود یا خیر
    // اگر بله، پس یکی از ورودی های این ماشین، کلای میانی ماشین قبلی است.
    $has_before_machine_type = LineProductStation::
    where([
        "product_id" => $line_product_station->product_id,
        "product_route_id" => $line_product_station->product_route_id
    ])->
    where("machine_type_id", "!=", $line_product_station->machine_type_id)->
    where("priority_number", "<", $line_product_station->priority_number)->
    first();
    // آیا بعد از خط محصول، کالا از یک گروه ماشین دیگری خارج می شود یا خیر
    // اگر بله، پس خروجی ماشین، کالای میانی است
    $has_next_machine_type = LineProductStation::
    where([
        "product_id" => $line_product_station->product_id,
        "product_route_id" => $line_product_station->product_route_id
    ])->
    where("machine_type_id", "!=", $line_product_station->machine_type_id)->
    where("priority_number", ">", $line_product_station->priority_number)->
    first();

    $station_operation_ids = LineProductStation::
    where([
        "product_id" => $line_product_station->product_id,
        "machine_type_id" => $line_product_station->machine_type_id
    ])->
    pluck("station_operation_id")->
    toArray();

    $option = [];
    $nodes = [];
    $links = [];
    $goods_kind = [];
    $y = isset($has_before_machine_type) ? 50 : 0;
    $y_distance = 50;
    $y_max = 0;
    $items = Product\BOM\BOMItem::join("bill_of_materials", "bill_of_material_id", "bill_of_materials.id")->
    join("products", "material_id", "products.id")->
    orderBy("goods_kind_id")->
    where("bill_of_material_id", $bom->id)->
    //  where("station_id", $line_product_station->station_id)->
    whereIn("station_operation_id", $station_operation_ids)->
    select("bill_of_material_item.id", "material_id", "input_line_code", "goods_kind_id")->
    groupBy("material_id", "input_line_code")->
    get();
    $category = 0;
    // ایجاد نود به ازای هر ورودی
    foreach ($items as $bom_item) {

        if (!isset($goods_kind[$bom_item->material->goods_kind_id])) {
            $category++;
            $goods_kind[$bom_item->material->goods_kind_id]["name"] = $bom_item->material->goods_kind->caption;
            $goods_kind[$bom_item->material->goods_kind_id]["value"] = "goods_kind_" . $bom_item->material->goods_kind->id;
            $goods_kind[$bom_item->material->goods_kind_id]["start_y"] = $y;
            $goods_kind[$bom_item->material->goods_kind_id]["end_y"] = $y;
            $goods_kind[$bom_item->material->goods_kind_id]["number_node"] = 0;
            $goods_kind[$bom_item->material->goods_kind_id]["category"] = $category;

        }

        $nodes[] = [

            "id" => "bom_item_" . $bom_item->id,
            "name" => ' ورودی ' . $bom_item->input_line_code . ": " . $bom_item->material->caption,
            "value" => "bom_item_" . $bom_item->id,
            "x" => 300,
            "y" => $y,
            "category" => $category,
            "symbolSize" => 50,
            "label" => [
                "show" => true,
                "position" => 'top',
            ]

        ];


        $goods_kind[$bom_item->material->goods_kind_id]["end_y"] = $y;
        $goods_kind[$bom_item->material->goods_kind_id]["number_node"] += 1;
        $y += $y_distance;
    }

    $y_max = $y - $y_distance;

    if ($has_before_machine_type) {
        // اگر اولویت پایین تر با گروه ماشین دیگر وجود دارد، یک نوع تزریق کالای میانی می گذاریم.
        $category++;
        $nodes[] = [

            "id" => "bom_item_mid",
            "name" => "خروجی " . $has_before_machine_type->machine_type->caption,
            "value" => "bom_item_min",
            "x" => 300,
            "y" => 0,
            "category" => $category,
            "symbolSize" => 50,
            "label" => [
                "show" => true,
                "position" => 'top',
            ]

        ];
    }
    // ایجاد نود به ازای هر رسته کالایی
    foreach ($goods_kind as $item) {
        $nodes[] = [

            "id" => $item["value"],
            "name" => $item["name"],
            "value" => $item["value"],
            "x" => 400,
            "y" => $item["start_y"] + ($item["end_y"] - $item["start_y"]) / ($item["number_node"] == 1 ? 1 : 2),
            "category" => $item["category"],
            "symbolSize" => 50,
            "label" => [
                "show" => true,
                "position" => 'top',
            ]

        ];
    }


    // ایجاد نود به ازای باندهای خروجی
    $output_bands = MachineTypeOutputBand::where([
        "machine_type_id" => $line_product_station->machine_type_id
    ])->get();
    if (count($output_bands) != 1) {
        return back()->withErrors("مشخصات گروه ماشین به درستی ثبت نشده است، لطفا با پشیتبانی تماس بگیرید.");
    }

    $number = $output_bands[0]->output_line_number;
    for ($k = 1; $k <= $number; $k++) {
        $nodes[] = [
            "name" => "  باند خروجی " . $k,
            "value" => "band_code_" . $k,
            "id" => "band_code_" . $k,
            "x" => 60,
            "y" => ($y_max / ($number + 1)) * $k,
            "category" => 4,
            "symbolSize" => 40,
            "label" => [
                "show" => true,
                "position" => 'top',
            ]
        ];
    }

    $nodes[] = [
        "name" => $has_next_machine_type ? "کالای میانی (ورودی " . $has_next_machine_type->machine_type->caption . ")" : $product->goods_kind->caption,
        "value" => "output",
        "id" => "output",
        "x" => 10,
        "y" => $y_max / 2,
        "category" => 4,
        "symbolSize" => 40,
        "label" => [
            "show" => true,
            "position" => 'top',
        ]
    ];
    // افزودن لینک های رسته به خروجی
    for ($k = 1; $k <= $number; $k++) {
        $links[] = [

            "source" => "band_code_" . $k,
            "target" => "output",
            "symbolSize" => [1, 1],
            "label" => [
                "show" => false,
            ],
            "lineStyle" => [
                "width" => 5,
                "curveness" => 0.0,
                "color" => "source"
            ],
        ];
        if ($has_before_machine_type) {
            // ایجاد لینک به ازای ووردی کالای میانی و ارتباط با باند های خروجی
            $links[] = [

                "source" => "bom_item_mid",
                "target" => "band_code_" . $k,
                "symbolSize" => [5, 15],
                "label" => [
                    "show" => true,
                    "formatter" => ""
                ],
                "lineStyle" => [
                    "width" => 5,
                    "curveness" => 0.0,
                    "color" => "source"
                ],
            ];
        }
    }
    // افزودن لینک های رسته به ورودی
    foreach ($items as $bom_item) {
        $links[] = [

            "source" => "goods_kind_" . $bom_item->material->goods_kind->id,
            "target" => "bom_item_" . $bom_item->id,
            "symbolSize" => [1, 1],
            "label" => [
                "show" => false,
            ],
            "lineStyle" => [
                "width" => 5,
                "curveness" => 0.0,
                "color" => "source"
            ],
        ];

    }


    // افزودن لینک های از Bom به خروجی که توسط کاربر ایجاد شده اند

    $list = Product\MaterialFlow::where([
        "product_id" => $product->id,
        "bill_of_material_id" => $bom->id,
        "machine_type_id" => $line_product_station->machine_type_id,
    ])->get();
    foreach ($list as $link) {
        $links[] = [

            "source" => "bom_item_" . $link->bill_of_material_item_id,
            "target" => "band_code_" . $link->band_code,
            "symbolSize" => [5, 15],
            "label" => [
                "show" => true,
                "formatter" => ""
            ],
            "lineStyle" => [
                "width" => 5,
                "curveness" => -0.0,
                "color" => "source"
            ],

        ];
    }


    $option = [
        "title" => [
            "text" => ' گراف جریان مواد برای ' . $line_product_station->route->caption . " - " . $bom->caption . " - گروه ماشین " . $line_product_station->machine_type->caption,
            "right" => '10%'
        ],
        "textStyle" => [
            "fontFamily" => 'IranSans'
        ],
        "tooltip" => [],
        "animationDurationUpdate" => 1500,
        "animationEasingUpdate" => 'quinticInOut',
        "color" => ['#EE6666', '#A389D4', '#9A60B4', "#EA7CCC", "#46C1F5"],
        "series" => [
            [
                "type" => 'graph',
                "layout" => 'none',
                "symbolSize" => 40,
                "roam" => false,
                "label" => [
                    "show" => true,
                    "position" => 'right',
                ],
                "tooltip" => [
                    "show" => true,
                    "formatter" => '{b}',
                ],
                "edgeSymbol" => ['circle', 'arrow'],
                "edgeSymbolSize" => [4, 15],
                "edgeLabel" => [
                    "fontSize" => 15
                ],
                "data" => $nodes,
                "links" => $links,

                "lineStyle" => [
                    "opacity" => 0.9,
                    "width" => 5,
                    "curveness" => -0.2,
                ],
                "categories" => [
                    [
                        "name" => "A"
                    ],
                    [
                        "name" => "B"
                    ],
                    [
                        "name" => "C"
                    ],
                    [
                        "name" => "D"
                    ],
                    [
                        "name" => "E"
                    ],

                ]
            ]
        ]
    ];

    return ($option);
}

    public
    function add_link(
        Request $request, Product $product, Product\BOM\BOM $bom, LineProductStation $line_product_station
    )
    {

        $route_path = $request->route_path;
        $product_creation_process_id = $request->product_creation_process_id ?? 0;

        $product_creation_process = Product\ProductCreation\ProductCreationProcess::find($product_creation_process_id);

        $bill_of_material_item = Product\BOM\BOMItem::find($request->source);
        $material_flow = Product\MaterialFlow::
        join("bill_of_material_item", "bill_of_material_item.id", "bill_of_material_item_id")->
        where([
            "material_flows.product_id" => $product->id,
            "input_line_code" => $bill_of_material_item->input_line_code,
            "material_flows.material_id" => $bill_of_material_item->material_id,
            "material_flows.machine_type_id" => $line_product_station->machine_type_id,
            "material_flows.band_code" => $request->target
        ])->
        select("material_flows.*")->
        first();
        if ($material_flow) {

            $material_flow->delete();
        } else {
            Product\MaterialFlow::create([
                "product_id" => $product->id,
                "bill_of_material_id" => $bom->id,
                "bill_of_material_item_id" => $request->source,
                "material_id" => $bill_of_material_item->material_id,
                "machine_type_id" => $line_product_station->machine_type_id,
                "band_code" => $request->target
            ]);
        }

        $option = self::getOption($product, $bom, $line_product_station);


        return view($this->view_path . "_graph", compact("option", "bom", "product", "line_product_station", "route_path", "product_creation_process"));


    }
}
