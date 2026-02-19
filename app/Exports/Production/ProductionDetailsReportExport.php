<?php

namespace App\Exports\Production;

use App\Models\Accounting\CostCenter;
use App\Models\Production\ProductionDetailsReport;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithProperties;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductionDetailsReportExport implements FromView, WithStyles, WithProperties, WithColumnFormatting, ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    var $user_id =0;

    public function properties(): array
    {
        return config("export.setting");
    }

    public function view(): View
    {
      $list=  ProductionDetailsReport::where("user_id",$this->user_id)->with("product","parent_product")->get();
        return view('production.details_dashboard.export.detail_report',
            [
                "list" => $list,
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
