<?php

namespace App\Models\LineProduct\Machine\Allocation;

use App\Models\LineProduct\Machine\Allocation;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AllocationData extends Model {
    use HasFactory;

    protected $table = "allocation_data";
    protected $fillable = [
        "allocation_id",
        "machine_allocation_id",
        "allocation_data_type_id",
        "float_value",
        "string_value",
        "data"
    ];

    public function allocation(){
        return $this->belongsTo(Allocation::class);
    }
    public function allocation_data_type(){
        return $this->belongsTo(AllocationDataType::class);
    }
    public function datetime() {
        return jdate( Carbon::parse( $this->created_at )->timestamp )->format( 'H:i Y/m/d ' );

    }

    public static function SetFloatValue( $allocation_data_type_id, $value, $allocation_id ) {

        $data = AllocationData::where( [
            "allocation_id"           => $allocation_id,
            "allocation_data_type_id" => $allocation_data_type_id
        ] )->first();
        if ( $data ) {
            $data->float_value = $value;
            $data->save();
        } else {
            $data = AllocationData::create( [
                "allocation_id"           => $allocation_id,
                "allocation_data_type_id" => $allocation_data_type_id,
                "float_value"             => $value
            ] );
        }

        return $data;
    }


    public static function getFloatValue( $allocation_data_type_id, $allocation_id  ) {

        $data = AllocationData::where( [
            "allocation_id"           => $allocation_id,
            "allocation_data_type_id" => $allocation_data_type_id
        ] )->first();

        return $data->float_value ?? null;
    }

    public static function getStringValue( $allocation_data_type_id, $allocation_id ) {
        $data = AllocationData::where( [
            "allocation_id"           => $allocation_id,
            "allocation_data_type_id" => $allocation_data_type_id
        ] )->first();

        return $data->string_value ?? null;
    }


    public static function SetData( $allocation_data_type_id, $json_data, $allocation_id ) {

        $data = AllocationData::where( [
            "allocation_id"           => $allocation_id,
            "allocation_data_type_id" => $allocation_data_type_id
        ] )->first();
        if ( $data ) {
            $data->data = json_encode($json_data);
            $data->save();
        } else {
            $data = AllocationData::create( [
                "allocation_id"           => $allocation_id,
                "allocation_data_type_id" => $allocation_data_type_id,
                "data"             =>  json_encode($json_data)
            ] );
        }

        return $data;
    }


    public static function getData( $allocation_data_type_id, $allocation_id) {

        $data = AllocationData::where( [
            "allocation_id"           => $allocation_id,
            "allocation_data_type_id" => $allocation_data_type_id
        ] )->first();

        return json_decode( $data->data ??null,true);
    }
}
