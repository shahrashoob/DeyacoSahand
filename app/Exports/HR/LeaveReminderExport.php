<?php

namespace App\Exports\HR;

use App\Models\LineProduct\GoodsKind;
use App\Models\LineProduct\Product\ProductPackingType;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\LineProduct\Product;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithProperties;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LeaveReminderExport implements FromView, WithStyles, WithProperties, WithColumnFormatting, ShouldAutoSize
{
    var $goods_kind_id;

    public function properties(): array
    {
        return config("export.setting");
    }

    public function view(): View
    {

        $workers = Worker::
        whereIn("cooperation_type_id", [1, 11])->
        whereNotIn("status_id", [4620006, 4620007, 4620009])->
        get();

        $year = jdate(Carbon::parse(Carbon::now())->timestamp)->format('Y');
        return view('import.hr._excel_leave_reminder',
            [
                'workers' => $workers,
                "year" => $year,
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
            2 => ['font' => ['bold' => true]],

        ];
    }
}
