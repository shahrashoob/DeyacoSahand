<?php

namespace Database\Seeders\OldSeeder;

use App\Models\LineProduct\LineProductStation;
use App\Models\LineProduct\Product\BOM\BOM;
use App\Models\LineProduct\Product\BOM\BOMItem;
use App\Models\LineProduct\Product\ProductRoute;
use Illuminate\Database\Seeder;

class ProductRouteSeeder extends Seeder {
    /**
     * Run the database seeds.
     * این سید به صورت موفت و فقط یک بار اجرا می شود.
     * @return void
     */
    public function run() {

        //
        $list = LineProductStation::whereNull( "product_route_id" )->get();
        foreach ( $list as $item ) {
            if ( $item->product ) {
                $route = ProductRoute::create( [
                    "product_id"       => $item->product_id,
                    "active_status_id" => 1200,
                    "caption"          => "مسیر " . ( $item->product->route()->count() + 1 )
                ] );

                $item->product_route_id = $route->id;
                $item->save();
            }
        }

        $list = BOMItem::where( "bill_of_material_id",0 )->OrWhereNull("bill_of_material_id")->get();
        foreach ( $list as $bom_item ) {

            $bom = BOM::where( "product_id", $bom_item->product_id )->first();

            if ( ! $bom ) {
                $route = ProductRoute::where( "product_id", $bom_item->product_id )->first();
                $bom   = BOM::create( [
                    "product_id"       => $bom_item->product_id,
                    "product_route_id" => $route->id ?? - 1,
                    "caption"          => "BOM1",
                    "active_status_id" => 1200
                ] );
            }

            $bom_item->bill_of_material_id = $bom->id;
            $bom_item->save();

        }
    }
}
