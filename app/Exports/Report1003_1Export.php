<?php

namespace App\Exports;

use App\Models\Accounting\CostCenter;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithProperties;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class Report1003_1Export implements FromView, WithStyles, WithProperties, WithColumnFormatting, ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    var $values = [];
    var $breaking_by_lot_number;
    var $breaking_by_degree;
    var $breaking_by_packing_item;
    var $breaking_by_transaction;
    var $breaking_by_classification;

    public function properties(): array
    {
        return config("export.setting");
    }

    public function view(): View
    {
        $cost_center_list = [];
        if ($this->breaking_by_transaction) {
            $cost_center_list = CostCenter::pluck("caption", "code")->toArray();
        }
        return view('report.1003.export',
            [
                "values" => $this->values,
                "breaking_by_lot_number" => $this->breaking_by_lot_number,
                "breaking_by_degree" => $this->breaking_by_degree,
                "breaking_by_packing_item" => $this->breaking_by_packing_item,
                "breaking_by_transaction" => $this->breaking_by_transaction,
                "breaking_by_classification" => $this->breaking_by_classification,
                "cost_center_list" => $cost_center_list,
            ]);
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
            1 => ['font' => ['bold' => true]],

        ];
    }
}
