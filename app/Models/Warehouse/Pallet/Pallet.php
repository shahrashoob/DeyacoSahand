<?php

namespace App\Models\Warehouse\Pallet;

use App\Models\Form\Packing\PackingForm;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\Utility\Status;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Pallet extends Model
{
    use HasFactory;

    protected $table = "pallets";
    protected $fillable=["allocation_id","status_id"];


    public function status(){
        return $this->belongsTo(Status::class);
    }

    public function items(){
        return $this->hasMany(PalletItem::class);
    }

    public static function CurrentPallet(Allocation $allocation)
    {
        return Pallet::where("allocation_id", $allocation->id)->
        where("status_id", 6080001)->// در حال پرشدن
        first();
    }

    public static function CreateNewPallet(Allocation $allocation)
    {

        $current_pallet = Pallet::CurrentPallet($allocation);
        if ($current_pallet) {

            if($current_pallet->items()->count() > 0){
                $current_pallet->status_id = 6080002;
                $current_pallet->save();
            }
            else{
                return $current_pallet;
            }

        }

        $new_pallet= Pallet::create([
            "allocation_id" => $allocation->id,
            "status_id" => 6080001,
        ]);

        return $new_pallet;
    }

    public static function CreateNewPalletByStatus($status_id=6080001)
    {
        $new_pallet= Pallet::create([
            "allocation_id" =>null,
            "status_id" => 6080001,
        ]);

        return $new_pallet;
    }

    public static function AddPackingForm($pallet,PackingForm $packing_form, $delete_from_other_pallet=false)
    {
        if(!$pallet){
            return false;
        }
        if($delete_from_other_pallet){
            PalletItem::where("packing_form_id", $packing_form->id)->delete();
        }
        PalletItem::create(["pallet_id"=>$pallet->id,"packing_form_id"=>$packing_form->id]);

        return true;
    }

    public static function RemovePackingForm($pallet,PackingForm $packing_form)
    {
        if(!$pallet){
            return false;
        }

            PalletItem::where("packing_form_id", $packing_form->id)->delete();

        return true;
    }

    public function getRandom()
    {
        if ($this->random == null) {
            $this->random = Str::random(6);
            $this->save();
        }
        return $this->random;
    }
    public function getCodeNumber()
    {
        if ($this->code == "") {
            $this->code = "PC" . $this->id;
            $this->save();
        }
       return $this->code;
    }
    public function get_created_datetime()
    {
        return jdate(Carbon::parse($this->created_at)->timestamp)->format('H:i Y/m/d ');

    }
    public function get_Number_of_packing_for_sett_o_warehouse()
    {
       $pallet_count= PalletItem::join("packing_forms","packing_forms.id","pallet_items.packing_form_id")->
            where("pallet_id",$this->id)->
            whereIn("status_id",[7007002])-> // در انتظار تایید انبار
            count();

       return $pallet_count;
    }

}
