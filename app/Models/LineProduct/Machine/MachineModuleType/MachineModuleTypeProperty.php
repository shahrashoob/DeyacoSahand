<?php

namespace App\Models\LineProduct\Machine\MachineModuleType;

use App\Models\LineProduct\Machine\Allocation;
use App\Models\Post\Post;
use App\Models\Utility\Setting;
use App\Models\Utility\SpecialUnit;
use App\Notifications\SMSNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Notification;

class MachineModuleTypeProperty extends Model
{
    use HasFactory;
    public function special_unit(){
        return $this->belongsTo(SpecialUnit::class);
    }

    // ارسال پیامک برای عدم راه اندازی شیفت، عدم قفسه گذاری و ...
    public static function SendSms( $message, Allocation $allocation,$machine_module_type_priority_id ) {

        $post_list=[];
        // ارسال پیامک
        $post_ids_form_sms = MachineModuleTypePropertyValue::getValue( $machine_module_type_priority_id,$allocation->machine->machine_type_id,"text_value" );
        if ( $post_ids_form_sms != "" ) {
            $post_list = explode("-", $post_ids_form_sms );
        }

        $allocation_item = $allocation->items()->first();
        foreach ( $post_list as $post_id ) {

            $post = Post::find( $post_id );
            if ( ! $post ) {
                continue;
            }

            $system_name = Setting::getStringValue( "software_name" );
            foreach ( $post->worker as $worker ) {

                $token10 = $post->caption . " ";
                $token20 = $message;
                $token   = $allocation_item->production->serial??"***";
                $token3  = $system_name;
                $token2  =$allocation_item->product->caption??"" ;

                Notification::send(
                    "00" . ( $worker->mobile_country->area_code ?? "98" ) . $worker->mobile,
                    new SMSNotification( "machinemoduletypeproperty",
                        $token,
                        $token2,
                        $token3,
                        $token10,
                        $token20 ) );

            }


        }

    }
}
