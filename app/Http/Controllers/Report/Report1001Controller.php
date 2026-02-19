<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\LineGroup;
use App\Models\LineProduct\Product;
use App\Models\Production\Production;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Psy\Util\Str;

class Report1001Controller extends Controller
{
    //
    public function index()
    {
//        where("id", "!=", LineGroup::$NotGrouping)->
        $line_groups = LineGroup::pluck("caption", "id");

        $list1 = $this->getList()->toArray();

        $weights = collect($list1)->groupBy("line_group_id")->map(function ($row) {
            return $row->sum('weight_production_cards');
        });

        $production_cards = Production::join("products", "production_cards.product_id", "products.id")
            ->select("line_group_id")
            ->where("status_id", 500)
           // ->where("line_group_id", "!=", LineGroup::$NotGrouping)
            ->groupBy("line_group_id")
            ->addSelect(DB::raw("count(production_cards.id) as count_production_cards"))
            ->pluck("count_production_cards", "line_group_id");


        $list_0_3 = $this->getList(0, 3)->toArray();
        $list_0_3 = collect($list_0_3)->groupBy("line_group_id")->map(function ($row) {
            return $row->sum('weight_production_cards');
        });


        $list_4_7 = $this->getList(3, 7)->toArray();
        $list_4_7 = collect($list_4_7)->groupBy("line_group_id")->map(function ($row) {
            return $row->sum('weight_production_cards');
        });

        $list_8_100 = $this->getList(7, 100000)->toArray();
        $list_8_100 = collect($list_8_100)->groupBy("line_group_id")->map(function ($row) {
            return $row->sum('weight_production_cards');
        });

        return view("report.1001.index", compact("production_cards", "weights", "list_0_3", "list_4_7", "list_8_100", "line_groups"));

    }

    public function getList($to = false, $from = false)
    {
        $list = Production::join("products", "production_cards.product_id", "products.id")
            ->select("line_group_id", "product_id")
            ->where("status_id", 500)
            ->where("line_group_id", "!=", LineGroup::$NotGrouping);

        if ($from)
            $list = $list->where("production_cards.created_at", ">", Carbon::now()->subDays($from));

        if ($to)
            $list = $list->where("production_cards.created_at", "<=", Carbon::now()->subDays($to));

        $list = $list->addSelect(DB::raw("sum(number) as count_production_cards"))
            ->addSelect(DB::raw("sum(number) *weight as weight_production_cards"))
            ->groupBy("line_group_id", "product_id")
            ->get();

        return $list;
    }


    public function line_group(LineGroup $lineGroup)
    {


        $list1 = $this->getListLineGroup($lineGroup)->get();
        $list_0_3_count = $this->getListLineGroup($lineGroup, 0, 3)->pluck("sum_number", "product_id");
        $list_0_3_weight = $this->getListLineGroup($lineGroup, 0, 3)->pluck("sum_weight", "product_id");

        $list_4_7_count = $this->getListLineGroup($lineGroup, 3, 7)->pluck("sum_number", "product_id");
        $list_4_7_weight = $this->getListLineGroup($lineGroup, 3, 7)->pluck("sum_weight", "product_id");

        $list_8_100_count = $this->getListLineGroup($lineGroup, 7)->pluck("sum_number", "product_id");
        $list_8_100_weight = $this->getListLineGroup($lineGroup, 7)->pluck("sum_weight", "product_id");

        return view("report.1001.line_group", compact("lineGroup", "list1", "list_0_3_count", "list_4_7_count", "list_8_100_count", "list_0_3_weight", "list_4_7_weight", "list_8_100_weight"));

    }

    public function getListLineGroup(LineGroup $lineGroup, $to = false, $from = false)
    {
        $list = Production::join("products", "production_cards.product_id", "products.id")
            ->select("product_id", "code", "caption")
            ->where("status_id", 500)
            ->where("line_group_id", $lineGroup->id);

        if ($from)
            $list = $list->where("production_cards.created_at", ">", Carbon::now()->subDays($from));

        if ($to)
            $list = $list->where("production_cards.created_at", "<=", Carbon::now()->subDays($to));

        $list = $list->addSelect(DB::raw("count(production_cards.id) as count_production_cards"))
            ->addSelect(DB::raw("sum(number)  as sum_number"))
            ->addSelect(DB::raw("sum(number) * weight as sum_weight"))
            ->groupBy("product_id");

        return $list;
    }

    public function product(Product $product)
    {
        $production = Production::where(["product_id" => $product->id, "status_id" => 500])->get();
        return view("report.1001.product", compact("production", "product"));
    }
}
