<?php

namespace App\Exports\Report;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithProperties;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class Report1009_1Export implements FromView, WithProperties,WithStyles,ShouldAutoSize,WithColumnFormatting
{
    var $list = [];
    var $init_inventory = 0;
    var $product = null;

    public function properties(): array
    {
        return config("export.setting");
    }

    public function view(): View
    {
        return view('report.1009.export1', ["list" => $this->list]);
    }
    public function columnFormats(): array
    {
        return [
            '2' => NumberFormat::FORMAT_NUMBER,
        ];
    }
    public function styles(Worksheet $sheet)
    {

        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true]],

        ];
    }
}
