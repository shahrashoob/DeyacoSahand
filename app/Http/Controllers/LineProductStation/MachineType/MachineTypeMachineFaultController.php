<?php

namespace App\Http\Controllers\LineProductStation\MachineType;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\GoodsKind;
use App\Models\LineProduct\GoodsKind\GoodsKindClassification;
use App\Models\LineProduct\Machine\Fault\MachineFault;
use App\Models\LineProduct\Machine\MachineType;
use App\Models\LineProduct\Machine\MachineTypeMachineFault;
use App\Models\LineProduct\Machine\MachineTypeOutputBand;
use App\Models\LineProduct\Product\Fault\ProductFault;
use App\Models\LineProduct\Product\Fault\ProductFaultProductFaultSign;
use App\Models\LineProduct\Product\Fault\ProductFaultSign;
use App\Models\Utility\Option;
use Illuminate\Http\Request;

class MachineTypeMachineFaultController extends Controller {
    private $view_path = "line_product_station.machine_type.machine_fault.";
    private $route_path = "line_product_station.machine_type.machine_fault.";

    public function index( MachineType $machine_type ) {


        // رسته کالایی های خروجی
        $out_put_goods_kind = MachineTypeOutputBand::
        join( "machine_type_output_band_goods_kind", "machine_type_output_band_id", "machine_type_output_bands.id" )->
        where( "machine_type_id", $machine_type->id )->
        pluck( "goods_kind_id" )->
        toArray();

        $product_fault_ids = GoodsKind\GoodsKindProductFault::
        where( "goods_kind_id", $out_put_goods_kind )->
        pluck( "product_fault_id" )->
        toArray();

        //  عیب  کالا که در رسته کالایی های خروجی وجود دارد.
        $product_fault_list = ProductFaultProductFaultSign::
        join( "product_faults", "product_faults.id", "product_fault_id" )->
        whereIn( "product_fault_id", $product_fault_ids )->
        groupBy( "product_faults.id" )->
        select( "product_faults.*" )->
        get();


        $list_value = MachineType\MachineTypeMachineFaultProductFault::where( "machine_type_id", $machine_type->id )->
        get()->keyBy( function ( $item ) {
            return $item->machine_type_machine_fault_id . "_" . $item->product_fault_id;
        } );


        $machine_fault_option = Option::get( "machine_fault" );

        return view( $this->view_path . "index", compact( "list_value", "machine_type", "machine_fault_option", "product_fault_list" ) );
    }

    public function store( Request $request, MachineType $machine_type ) {

        $machine_false = MachineFault::find( $request->machine_fault_id );
        if ( ! $machine_false ) {
            return back()->withErrors( "نوع نقص معتبر نمی باشد." );
        }

        $exists = MachineTypeMachineFault::where( [
            "machine_type_id"  => $machine_type->id,
            "machine_fault_id" => $request->machine_fault_id
        ] )->exists();

        if ( $exists ) {
            return back()->withErrors( "این نقص قبلا به گروه ماشین اضافه شده است." );
        }
        MachineTypeMachineFault:: create( [
            "machine_type_id"  => $machine_type->id,
            "machine_fault_id" => $request->machine_fault_id
        ] );

        return redirect()->back()->with( [ "success" => "نقص با موفقیت اضافه شد" ] );

    }

    public function delete( MachineType $machine_type, MachineTypeMachineFault $machine_type_machine_fault ) {

        $list = MachineType\MachineTypeMachineFaultProductFault:: where( [
            "machine_type_id"  => $machine_type->id,
            "machine_fault_id" => $machine_type_machine_fault->machine_fault_id
        ] )->get();
        if ( count( $list ) != 0 ) {
            return back()->withErrors( "لطفا قبل از حذف، نمود های نقص کالا را برای نقص ماشین حذف نمایید." );
        }

        MachineTypeMachineFault:: where( [
            "machine_type_id" => $machine_type->id,
            "id"              => $machine_type_machine_fault->id
        ] )->delete();

        return redirect()->back()->with( [ "success" => "نقص با موفقیت حذف شد" ] );
    }

    public function store_product_fault( Request $request, MachineType $machine_type, MachineTypeMachineFault $machine_type_machine_fault ) {

        MachineType\MachineTypeMachineFaultProductFault::where( "machine_type_machine_fault_id", $machine_type_machine_fault->id )->delete();

        if ( isset( $request->product_fault ) ) {
            foreach ( $request->product_fault as $product_fault_id => $value ) {
                MachineType\MachineTypeMachineFaultProductFault::create( [
                    "machine_type_id"               => $machine_type->id,
                    "machine_fault_id"              => $machine_type_machine_fault->machine_fault_id,
                    "machine_type_machine_fault_id" => $machine_type_machine_fault->id,
                    "product_fault_id"              => $product_fault_id
                ] );
            }
        }

        return back()->with( [ "success" => "اطلاعات با موفقیت ذخیره شد." ] );
    }

}
