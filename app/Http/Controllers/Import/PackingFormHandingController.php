<?php

namespace App\Http\Controllers\Import;

use App\Events\Form\PackingLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\FabricRaw\PackingForm\PrintQRController;
use App\Imports\PackingFormHandlingImport;
use App\Models\Form\Form;
use App\Models\Form\FormItem;
use App\Models\Form\Packing\NewPackingFormHandling;
use App\Models\Form\Packing\PackingForm;
use App\Models\Form\Packing\PackingFormItem;
use App\Models\LineProduct\Carrier\Carrier;
use App\Models\Production\ProductionForm;
use App\Models\Production\ProductionFormItem;
use App\Models\Utility\Message;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class PackingFormHandingController extends Controller {
    public function index() {

        $model = [
            "name"    => "packing_form_handling",
            "route"   => "import.packing_form_handling.upload",
            "caption" => " بسته بندی های جدید"
        ];

        return view( "import/index", compact( "model" ) );

    }

    public function upload() {

        Excel::import( new PackingFormHandlingImport(), request()->file( 'file_uploaded' ) );

        return redirect()->route( "import.packing_form_handling.show" )->with( [ "success" => "آپلود با موفقیت انجام شده" ] );
    }

    public function show() {

        $list = NewPackingFormHandling::orderBy( "has_error", "desc" )->orderBy( "id" )->paginate( 50 );

        $error_count = NewPackingFormHandling::where( "error", "!=", "" )->count();

        $worker = Worker::find( Auth::id() );

        return view( "import.packing_form_handling", compact( "list", "error_count", "worker" ) );
    }

    public function update( $print_label ) {
        $list                        = NewPackingFormHandling::get();
        $new_packing_form_first_item = NewPackingFormHandling::first();

        $packing_form_list         = [];

        if ( count( $list ) == 0 ) {
            return back()->withErrors( "لطفا فایلی که حداقل یک رکورد دارد بارگذاری کنید." );
        }

        // ایجاد یک فرم تولید برای کل بسته ها
        $form = Form::CreateFrom( [
            "order_id"           => 0,
            "order_list_id"      => 0,
            "production_card_id" => 0,
            "user_id"            => Auth::user()->id,
            "form_type_id"       => 304,
            "trans_kind"         => 38, // کسری انبار گردانی
            "warehouse_id"       => $new_packing_form_first_item->warehouse_id,
            "status_id"          => 500000410, // در انتظار تایید انبار
            "ic"                 => null
        ] );

        $form->getCode();
        foreach ( $list as $item ) {

            // ایجاد بسته بندی جدید
            if ( ! isset( $packing_form_list[ $item->packing_form_number ] ) ) {

                $packing_form = PackingForm::create( [
                    "carrier_id"      => $item->carrier->id ?? null,
                    "status_id"       => 7007002, // در انتظار تایید انبار
                    "packing_type_id" => $item->packing_type_id,
                ] );
                $packing_form->getCode();
                $packing_form_list[$item->packing_form_number]=$packing_form;

                event( new PackingLogEvent( $packing_form, 7007001 ) ); // ایجاد بسته بندی

                event( new PackingLogEvent( $packing_form, 7007005 ) );// تکمیل و تحویل به انبار
            }

            $packing_form=  $packing_form_list[$item->packing_form_number];
           $packing_form_item= PackingFormItem::create( [
                "packing_form_id"         => $packing_form->id,
                "final_amount"            => $item->amount,
                "amount"                  => $item->amount,
                "amount_after_control"    => $item->amount,
                "sub_amount"              => $item->sub_amount,
                "init_sub_amount"              => $item->sub_amount,
                "status_id"               => $packing_form->status_id,
                "product_id"              => $item->product_id,
                "degree_id"               => $item->degree_id,
                "lot_number_id"           => $item->lot_number_id,
                "production_form_item_id" => null,
                "band_code"               => 1,
            ] );

            FormItem::create( [
                "form_id"              => $form->id,
                "packing_form_item_id" => $packing_form_item->id,
                "product_id"           => $item->product_id,
                "amount"               => $item->amount,
                "sub_amount"           => $item->sub_amount,
                "carrier_id"           => $item->carrier->id ?? null,
                "degree_id"            => $item->degree_id,
                "lot_number_id"        => $item->lot_number_id,
                "packing_type_id"      => $item->packing_type_id,
                "description"          => "کسری انبار گردانی - ایجاد بسته بندی با کد " . ( $packing_form->getCode() )
            ] );

            if( $item->carrier){
                $item->carrier->addProduct($item->product_id);
                $item->carrier->SetStatus(5320004,null,5320111,$item->product_id);
            }

        }
        NewPackingFormHandling::where( "id", ">", 0 )->delete();

        if($print_label) {

            $worker = Worker::find( Auth::id() );
            foreach ( $packing_form_list as $packing_form ) {
                PrintQRController::direct_print( $packing_form, $worker );
            }
        }


        return redirect()->route( "import.warehouse_handling.index" )->with( [ "success" => "انبار گردانی با موفقیت انجام شد." ] );
    }
}
