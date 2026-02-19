<?php

namespace App\Imports;

use App\Models\LineProduct\LineGroup;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\LineProduct\NewProduct;
use App\Models\LineProduct\Product;
use App\Models\Utility\Unit;
use App\Models\LineProduct\ProductType;
class ProductImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        //
        $cols=[
            "product_code"=>0,
            "caption"=>1,
            "number_in_carton"=>2,
            "unit_caption"=>3,
            "supply_type_id"=>4,
            "goods_type_id"=>5,
            "percent_of_waste"=>6,
            "min_production"=>7,
            "max_production"=>8,
            "batch"=>9,
            "weight"=>10,
            "min_inventory"=>11,
            "product_type_caption"=>12,
            "active_status_id"=>13,
            "line_group_id"=>14,
          ];

        NewProduct::where("id",">",0)->delete();
          $i = 0;
        foreach ( $rows as $row ) {
            $i ++;
            if ( $i <= 1 ) {
                continue;
            }
            foreach ($cols as $k=>$v ){
                if(!isset($row[$v])){
                    $row[$v]=0;
                }
            }

            $product=Product::GetIdFromCode($row[$cols["product_code"]]);

            $new_product=new NewProduct();
            if(isset($product)){
                 $new_product->product_id=$product->id;
            }


            $error_text="";

            $new_product->code=$row[$cols["product_code"]];
            $new_product->caption=$row[$cols["caption"]];

            $new_product->number_in_carton=$row[$cols["number_in_carton"]];
            if($new_product->number_in_carton<1){
                $error_text.=" تعداد در کفی نمی تواند عددی کوچکتر از 1 باشد"."<br/>";
             }

            $new_product->supply_type_id=$row[$cols["supply_type_id"]];

            $unit_id=Unit::GetIdFromCaption($row[$cols["unit_caption"]]);
             $error_text.=$unit_id==-100?"واحد نامعتبر است"."<br/>".$row[$cols["unit_caption"]]."A":"";

             $new_product->unit_id=$unit_id;


             $line_group=LineGroup::where("id",$row[$cols["line_group_id"]])->first();
            $new_product->line_group_id=$line_group->id??0;

            if(!$line_group){
                $error_text.="<br/>". " خط - محصول معتبر نمی باشد.";
            }

            $product_type=ProductType::where("id",$row[$cols["product_type_caption"]]);

            if(!$product_type){
                $error_text.="<br/>". "گروه کالایی معتبر نمی باشد.";
            }

             $new_product->product_type_id=$row[$cols["product_type_caption"]];

             $new_product->weight=$row[$cols["weight"]];
             if($row[$cols["weight"]]=="" && $new_product->weight!=0 ){ //|| $product->weight==0
                 $error_text.="وزن نامعتبر می باشد"."<br/>".$row[$cols["weight"]]."A";
             }

             $new_product->percent_of_waste=$row[$cols["percent_of_waste"]];
             $new_product->min_production=$row[$cols["min_production"]];
             $new_product->max_production=$row[$cols["max_production"]];

             $new_product->goods_type_id=$row[$cols["goods_type_id"]];
             if($new_product->goods_type_id>2 || $new_product->goods_type_id<1){
                $error_text.=" کد نوع کالا نامعتبر است"."<br/>";
             }

             $new_product->batch=$row[$cols["batch"]];
            if($new_product->batch<1){
                $error_text.=" بچ نمی تواند مقداری کمتر از 1 باشد"."<br/>";
            }

             $new_product->min_inventory=$row[$cols["min_inventory"]];

             $new_product->error=$error_text;


             $new_product->active_status_id=$row[$cols["active_status_id"]];

             if(!($new_product->active_status_id==1200 || $new_product->active_status_id==1210)){
                 $error_text.="کد فعال بودن نادرست است";
             }
             $new_product->save();

        }

    }
}
