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

class Report1014_1Export implements  FromView, WithProperties, WithStyles, ShouldAutoSize, WithColumnFormatting
{
    /**
    * @return \Illuminate\Support\Collection
    */
    var $operation_list=[];
    var $template="";
    public function properties(): array {
        return config( "export.setting" );
    }
    public function view(): View {
        return view( 'report.1014.export'.$this->template, [
            "operation_list" => $this->operation_list,
        ] );
    }

    public function columnFormats(): array {
        return [
            '2' => NumberFormat::FORMAT_NUMBER,
        ];
    }

    public function styles( Worksheet $sheet ) {

        return [
            // Style the first row as bold text.
            1 => [ 'font' => [ 'bold' => true ] ],

        ];
    }
}
