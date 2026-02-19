<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\Worker;

class WorkerImport implements ToCollection
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {

        $cols = [
            "national_code" => 0,
            "firstname" => 1,
            "lastname" => 2,
        ];

        $i = 0;
        foreach ($rows as $row) {
            $i++;
            if ($i <= 1) {
                continue;
            }
            foreach ($cols as $k => $v) {
                if (!isset($row[$v])) {
                    $row[$v] = 0;
                }
            }

            $worker = Worker::where(["national_code" => $row[$cols["national_code"]]])->first();
            if(!$worker){
                $worker=Worker::create(["national_code" => $row[$cols["national_code"]], "email"=>$row[$cols["national_code"]]]);
            }
            $worker->firstname = $row[$cols["firstname"]];
            $worker->lastname = $row[$cols["lastname"]];
            $worker->name = $row[$cols["firstname"]] . " " . $row[$cols["lastname"]];


            $worker->save();

        }
    }
}
