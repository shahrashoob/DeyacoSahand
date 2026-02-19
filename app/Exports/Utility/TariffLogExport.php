<?php

namespace App\Exports\Utility;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithProperties;

class TariffLogExport implements FromView, WithProperties
{
    var $list=[];
    /**
    * @return \Illuminate\Support\Collection
    */
    public function properties(): array
    {
        return config("export.setting");
    }
    public function view(): View
    {
        return view('accounting.tariff.log_list_export',
            ["list"=>$this->list]);
    }
}
