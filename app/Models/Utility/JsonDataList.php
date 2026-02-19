<?php

namespace App\Models\Utility;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JsonDataList extends Model
{
    use HasFactory;

    protected $fillable = ["data", "other_id", "message_type_id"];

    public static function SetData($other_id, $message_type_id, $data = [])
    {
        $jsn_data_list = JsonDataList::firstOrcreate(
            [
                "other_id" => $other_id,
                "message_type_id" => $message_type_id,
            ],
            [
                "data" => "[]"
            ]);
        $data = json_encode($data);
        $jsn_data_list->data = $data;
        $jsn_data_list->save();
        return $jsn_data_list;
    }

    public static function SetFilter($other_id, $message_type_id, $data = [])
    {
        $jsn_data_list = JsonDataList::firstOrcreate(
            [
                "other_id" => $other_id,
                "message_type_id" => $message_type_id,
            ],
            [
                "data" => "[]"
            ]);
        $data = json_encode($data);
        $jsn_data_list->data = $data;
        $jsn_data_list->save();
        return $jsn_data_list;
    }
    public static function GetData($other_id, $message_type_id)
    {
        $jsn_data_list = JsonDataList::where(
            [
                "other_id" => $other_id,
                "message_type_id" => $message_type_id,
            ])->first();
        if(!$jsn_data_list){
            return [];
        }
        $data = json_decode($jsn_data_list->data, true);

        return $data;
    }
    public static function RemoveData($other_id, $message_type_id)
    {
        $jsn_data_list = JsonDataList::where(
            [
                "other_id" => $other_id,
                "message_type_id" => $message_type_id,
            ])->delete();

    }
}
