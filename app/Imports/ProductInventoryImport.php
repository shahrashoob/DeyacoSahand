<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\LineProduct\Product;
use App\Models\Utility\Unit;
use App\Models\LineProduct\NewProductInventory;
class ProductInventoryImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        NewProductInventory::where("id",">",0)->delete();
        $cols=[
            "product_code"=>2,
            "end_inventory"=>8,
            "caption"=>3,
          ];

          $i = 0;
        foreach ( $rows as $row ) {
            $i ++;
            if ( $i <= 2 ) {
                continue;
            }
            foreach ($cols as $k=>$v ){
                if(!isset($row[$v])){
                    $row[$v]=0;
                }
            }

            $error_text="";
            $product=Product::GetIdFromCode($row[$cols["product_code"]]);

            $NPI=new NewProductInventory();
            $NPI->product_code=$row[$cols["product_code"]];
            $NPI->caption=$row[$cols["caption"]];
            $NPI->end_inventory=0;
            if($product){
                $NPI->end_inventory=floor($row[$cols["end_inventory"]]/$product->number_in_carton);

               // $error_text.="<br/>"." اطلاعات کالا در سامانه وجود ندارد";
                //$NPI->error=$error_text;
            }

            $NPI->product_id=$product->id??0;


            $NPI->save();


        }
    }
}
