<?php

namespace App\Models\Warehouse;

use App\Models\Form\Form;
use App\Models\Form\FormItem;
use App\Models\Form\Packing\PackingForm;
use App\Models\Form\Packing\PackingFormItem;
use App\Models\LineProduct\Carrier\Carrier;
use App\Models\LineProduct\Degree;
use App\Models\LineProduct\LotNumber;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\Order\TransKind;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\LineProduct\Product;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Support\Facades\DB;

class WarehouseProduct extends Model {
	use HasFactory;

	protected $table = "warehouse_product";
	protected $fillable = [
		"warehouse_id",
		"product_id",
		"input",
		"sub_input",
		"output",
		"sub_output",
		"total_remaining",
		"warehouse_remaining",
		"factory_remaining",
		"form_id",
		"form_item_id",
		"ic",
		"opp_kind",
		"trans_kind",
		"packing_type_id",
		"carrier_id",
		"degree_id",
		"lot_number_id",
		"master_packing_form_id",
		"packing_form_item_id",
		"financial_software_status_id"
	];

	public function get_create_date() {
		return jdate( Carbon::parse( $this->created_at )->timestamp )->format( 'Y/m/d' );
	}

	public function get_create_time() {
		return jdate( Carbon::parse( $this->created_at )->timestamp )->format( 'H:i' );
	}

	public function warehouse() {
		return $this->belongsTo( Warehouse::class, "warehouse_id", "id" );
	}

	public function product() {
		return $this->belongsTo( Product::class, "product_id", "id" );
	}

	public function lot_number() {
		return $this->belongsTo( LotNumber::class );
	}

	public function carrier() {
		return $this->belongsTo( Carrier::class );
	}

	public function packing_type() {
		return $this->belongsTo( PackingType::class );
	}

	public function degree() {
		return $this->belongsTo( Degree::class );
	}

	public function trans_kind_item() {
		return $this->belongsTo( TransKind::class, "trans_kind", "id" );
	}

	public function form() {
		return $this->belongsTo( Form::class );
	}

	public function form_item() {
		return $this->belongsTo( FormItem::class );
	}

	public function packing_form_item() {
		return $this->belongsTo( PackingFormItem::class );
	}

	public function update_remaining() {

		$total_remaining = WarehouseProduct::where( "product_id", $this->product_id )->
		addSelect( DB::raw( "(sum(input) -sum(output)) as value " ) )->first();


		$warehouse_remaining = WarehouseProduct::where( [
			"product_id"   => $this->product_id,
			"warehouse_id" => $this->warehouse_id
		] )->
		addSelect( DB::raw( "(sum(input) -sum(output)) as value " ) )->first();

		$factory_remaining = WarehouseProduct::where( [
			"product_id" => $this->product_id,
			"factory_id" => $this->factory_id
		] )->
		addSelect( DB::raw( "(sum(input) -sum(output)) as value " ) )->first();

		$this->update( [
			"total_remaining"     => $total_remaining->value,
			"warehouse_remaining" => $warehouse_remaining->value,
			"factory_remaining"   => $factory_remaining->value,
		] );

	}

	public static function getProductInventoryList(
        $product_ids,
        $degree_id = null,
        $warehouse_id = null,
        $carrier_id = null,
        $lot_number_id = null,
        $TRUNCATE = 6,
        $group_by = "product_id",
        $packing_form_item_id = null,
        $from_date=null,
        $to_date=null,
        $is_null_packing_form_item_id = false,
    ) {

		$query = WarehouseProduct::
		when( $warehouse_id != 0, function ( $query ) use ( $warehouse_id ) {
			$query->where( "warehouse_id", $warehouse_id );
		} )->
		whereIn( "product_id", $product_ids )->
		when( $degree_id, function ( $query ) use ( $degree_id ) {
			$query->where( "degree_id", $degree_id );
		} )->
		when( $carrier_id, function ( $query ) use ( $carrier_id ) {
			$query->where( "carrier_id", $carrier_id );
		} )->
		when( $lot_number_id, function ( $query ) use ( $lot_number_id ) {
			$query->where( "lot_number_id", $lot_number_id );
		} )->
		when( $packing_form_item_id, function ( $query ) use ( $packing_form_item_id ) {
			$query->where( "packing_form_item_id", $packing_form_item_id );
		} )->
		when( $from_date, function ( $query ) use ( $from_date ) {
			$query->where( "created_at",">=", $from_date );
		} )->
		when( $to_date, function ( $query ) use ( $to_date ) {
			$query->where( "created_at","<=", $to_date );
		} )->
		when( $is_null_packing_form_item_id, function ( $query ) {
			$query->whereNull( "packing_form_item_id" );
		} )->
		groupBy( $group_by );

		$round_number = $TRUNCATE - 1;
		$inventory    = $query->addSelect( DB::raw( "round( TRUNCATE(sum(input),$TRUNCATE) - TRUNCATE(sum(output),$TRUNCATE) ,$round_number) as value," . $group_by ) )->pluck( "value", $group_by );

		if ( $group_by == "product_id" ) {
			foreach ( $product_ids as $id ) {
				if ( ! isset( $inventory[ $id ] ) ) {
					$inventory[ $id ] = 0;
				}
			}
		}

		return $inventory;
	}

	public function getDesc( $type = false ) {

		switch ( $type ) {
			case "group_by_product":
				if ( $this->input != 0 ) {
					// توضیحات ورود
					return $this->trans_kind_item->caption . "(کد مرکز " . $this->ic . ") - فرم ورود " . $this->form->getCode();
				}
				if ( $this->output != 0 ) {
					// توضیحات خروج
					return $this->trans_kind_item->caption . "(" . ( $this->form_item->product_request_form_item->product_request_form->applicant->caption ?? "" ) . ") - برگ خروج" . $this->form->getCode();
				}


				break;
			default:
				return $this->form_item->description ?? "error in form item:" . $this->form_item_id . ", warehouse_product_id:" . $this->id;

		}

	}

}
