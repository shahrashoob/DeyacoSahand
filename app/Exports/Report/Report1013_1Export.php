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


class Report1013_1Export implements FromView, WithProperties, WithStyles, ShouldAutoSize, WithColumnFormatting {
    var $order_factor = [];
    var $form_item_currency = [];

    public function properties(): array {
        return config( "export.setting" );
    }

    public function view(): View {
        return view( 'report.1013.export', [
            "order_factor" => $this->order_factor,
            "form_item_currency" => $this->form_item_currency,
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
