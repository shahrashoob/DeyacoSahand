<?php

namespace App\Http\Controllers\Import;

use App\Events\Form\PackingLogEvent;
use App\Events\Warehouse\Form\FormLogEvent;
use App\Events\Warehouse\PutInWarehouseEvent;
use App\Http\Controllers\Controller;
use App\Imports\AddingExistingProductsImport;
use App\Models\Form\Form;
use App\Models\Form\FormItem;
use App\Models\Utility\Message;
use App\Models\Warehouse\AddingExistingProduct;
use App\Models\Warehouse\WarehouseProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class AddingExistingProductsController extends Controller {
    // افزودن کالاهای موجود که در سامانه ثبت نشده است (ویژه دوره پیاده سازی)
    // انبارگردای سابق
    public function index() {

        $model = [
            "name"    => "add_existing_products",
            "route"   => "import.add_existing_products.upload",
            "caption" => "انبارگردانی"
        ];

        return view( "import/index", compact( "model" ) );

    }

    public function upload() {

        Excel::import( new AddingExistingProductsImport(), request()->file( 'file_uploaded' ) );

        return redirect()->route( "import.add_existing_products.show" )->with( [ "success" => "آپلود با موفقیت انجام شده" ] );
    }

    public function show() {

        $list = AddingExistingProduct::orderBy( "has_error", "desc" )->orderBy( "id" )->paginate( 50 );

        $error_count = AddingExistingProduct::where( "error", "!=", "" )->count();

        return view( "import.add_existing_products", compact( "list", "error_count" ) );
    }

    public function update() {
        $list = AddingExistingProduct::get();
        if ( count( $list ) == 0 ) {
            return back()->withErrors( "لیست انبار گردانی خالی می باشد، لطفا دوباره تلاش کنید." );
        }
        $add_existing_products = $list[0];
        // ثبت فرم خروج از انبار
        $form = Form::CreateFrom( [
            "order_id"      => 0,
            "order_list_id" => 0,
            "user_id"       => Auth::user()->id,
            "trans_kind"    => $add_existing_products->trans_kind,
            "ic"            => null,
            "status_id"     => 500000200 // تایید شده
        ] );
        $form->getCode("DCEF");

        foreach ( $list as $item ) {

            foreach ( $item->packing_form->items as $packing_form_item ) {
                $form_item = FormItem::create( [
                    "form_id"                      => $form->id,
                    "product_id"                   => $packing_form_item->product_id,
                    "amount"                       => $packing_form_item->final_amount,
                    "sub_amount"                   => $packing_form_item->sub_amount,
                    "carrier_id"                   => $packing_form_item->packing_form->carrier_id ?? null,
                    "degree_id"                    => $packing_form_item->degree_id,
                    "lot_number_id"                => $packing_form_item->lot_number_id,
                    "packing_type_id"              => $packing_form_item->packing_form->packing_type_id,
                    "packing_form_item_id"         => $packing_form_item->id,
                    "io_line_code"                 => 11,
                    "product_request_form_item_id" => null,
                    "description"                  => "مازاد انبار گردانی - خروج بسته بندی ".$item->packing_form->code
                ] );
            }
            $item->packing_form->status_id = 7007012; // خارج شده از انبار (مازاد انبارگردانی)
            $item->packing_form->save();
            // ثبت تراکنش خروج  از انبار
            event( new PackingLogEvent( $item->packing_form, 7007010, null, "انبار گردانی", $form->id ) );


        }

        $form               = Form::find( $form->id );
        $form->warehouse_id = $add_existing_products->warehouse_id;
        event( new FormLogEvent( $form ) );
        //         ثبت برگ خروج از انبار در انبار
        event( new PutInWarehouseEvent( $form ) );

        AddingExistingProduct::where( "id", ">", 0 )->delete();

        return redirect()->route( "import.add_existing_products.index" )->with( [ "success" => "انبار گردانی با موفقیت انجام شد." ] );
    }
}
