<?php

namespace App\Exports\Product;

use App\Models\LineProduct\GoodsKind;
use App\Models\LineProduct\Product\ProductPackingType;
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

class ProductPricingSampleFormatExport implements FromView, WithStyles, WithProperties, WithColumnFormatting, ShouldAutoSize
{
    var $goods_kind_id;

    public function properties(): array
    {
        return config("export.setting");
    }

    public function view(): View
    {
        $packing_type_product = ProductPackingType::join("products", "products.id", "packing_type_product.product_id")
            ->join("packing_types", "packing_types.id", "packing_type_product.packing_type_id")
            ->where('products.goods_kind_id', $this->goods_kind_id)
            ->select(
                'products.code as product_code',
                'products.caption as product_caption',
                'packing_types.code as packing_type_code',
                'packing_types.caption as packing_type_caption',
                'packing_type_product.product_id',
                'packing_type_product.packing_type_id'
            )
            ->get();

        // لیست قیمت آنهایی که نوع بسته بندی دارند و ندارد، را جدا می گیریم.
        $prices_packing_type = Product\Pricing\ProductPricing::selectRaw('CONCAT(product_id, "_", packing_type_id) as col, price')->
        whereNotNull('packing_type_id')->
        pluck('price', 'col')->
        toArray();

        $prices_without_packing_type = Product\Pricing\ProductPricing::selectRaw('CONCAT(product_id, "_") as col, price')->
        whereNull('packing_type_id')->
        pluck('price', 'col')->
        toArray();

        // لیست کالاهایی که انبارش ندارند.
        $product_without_packing = Product::
        where("warehouse_storage_type_id", "!=", 2)-> // انبارش با بسته بندی نیست
        where("goods_kind_id", $this->goods_kind_id)->
        get();
        return view('import.product.pricing.excel_export',
            [
                "packing_type_product" => $packing_type_product,
                'prices_packing_type' => $prices_packing_type,
                'prices_without_packing_type' => $prices_without_packing_type,
                "product_without_packing" => $product_without_packing,
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
