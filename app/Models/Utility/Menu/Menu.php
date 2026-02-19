<?php

namespace App\Models\Utility\Menu;

use App\Models\Utility\OfficeAutomation\OfficeAutomationAction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Menu extends Model {
    use HasFactory;

    protected $table = "menus";
    protected $fillabel = [ "id", "caption" ];

    public function menu_type() {
        return $this->belongsTo( MenuType::class );
    }

    public function button() {
        return $this->hasMany( Button::class );
    }

    public function getBadge( $user_id ) {
        switch ( $this->id ) {
            case 2601:
                $count = OfficeAutomationAction::where( [
                    "user_id"        => Auth::id(),
                    "view_status_id" => "5250011"
                ] )->
                distinct("office_automation_to_do_list_id")->
                count();

                return $count;
                break;
            default:
                return 0;
        }
    }


}
