<?php

namespace App\Exports\Product;

use App\Models\LineProduct\GoodsKind;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithProperties;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductPropertySampleFormatExport implements  FromView,WithStyles, WithProperties,WithColumnFormatting,ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    var $goods_kind_id;
    public function properties(): array {
        return config( "export.setting" );
    }

    public function view(): View {
       $goods_kind=GoodsKind::find($this->goods_kind_id);
        return view( 'import.product.property.excel_export',
            [
                "goods_kind" => $goods_kind
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
            2    => ['font' => ['bold' => true]],

        ];
    }
}
