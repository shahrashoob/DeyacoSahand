<?php

namespace App\Exports\Contractor\Report;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithProperties;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class Report1Export implements  FromView,WithStyles, WithProperties,WithColumnFormatting,ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    var $list = [];

    public function properties(): array {
        return config( "export.setting" );
    }
    public function view(): View {
        return view( 'contractor.report.report1.export',
            [
                "list"                  => $this->list,
            ] );
    }
    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_NUMBER,
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
