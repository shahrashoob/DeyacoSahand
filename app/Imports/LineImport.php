<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\LineProduct\Line;
class LineImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
       
        $cols=[
            "code"=>0,
            "caption"=>1,
          ];

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

            $line=Line::where(["code"=>$row[$cols["code"]]])->firstOrCreate([
                "code"=>$row[$cols["code"]],
                "caption"=>$row[$cols["caption"]],
            ]);
            
        }
    }
}
