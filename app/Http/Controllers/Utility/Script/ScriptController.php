<?php

namespace App\Http\Controllers\Utility\Script;

use App\Http\Controllers\Controller;
use App\Models\Form\Packing\PackingForm;
use App\Models\LineProduct\GoodsKind;
use App\Models\LineProduct\Product;
use App\Models\Post\Post;
use App\Models\Post\PostScript;
use App\Models\Post\PostUser;
use App\Models\Utility\Option;
use App\Models\Utility\Script\Script;


class ScriptController extends Controller {
    var $view_path = "utility.script.";
    var $route_path = "utility.script.";

    public function index() {

        $post_ids   = PostUser::getCurrentPostByShiftWorkAndLeaveOvertime( "post_ids" );
        $script_ids = PostScript::getAllowedScript( $post_ids );
        $list       = Script::
        orderBy( "priority" )->
        whereIn( "id", $script_ids )->
        paginate( 50 );

        $post_script = PostScript::
        whereIn( "script_id", $script_ids )->
        whereIn( "post_id", $post_ids )->
        get()->keyBy("script_id");

        return view( $this->view_path . "script.index", compact( "list", "post_script" ) );
    }

    public function log( Script $script ) {

        $post_ids    = PostUser::getCurrentPostByShiftWorkAndLeaveOvertime( "post_ids" );
        $post_script = PostScript::
        where( "script_id", $script->id )->
        whereIn( "post_id", $post_ids )->
        where( "allow_view_log", 1 )->
        first();
        if ( ! $post_script ) {
            return back()->withErrors( "شما مجوز لازم جهت مشاهده صفحه را ندارید." );
        }
        $list = $script->logs()->orderByDesc( "id" )->paginate( 50 );

        return view( $this->view_path . "script.log", compact( "list", "script" ) );
    }

    public static function checkEditPermission( Script $script ) {
        $post_ids    = PostUser::getCurrentPostByShiftWorkAndLeaveOvertime( "post_ids" );
        $post_script = PostScript::
        where( "script_id", $script->id )->
        whereIn( "post_id", $post_ids )->
        where( "allow_edit", 1 )->
        first();
        if ( ! $post_script ) {
            return back()->withErrors( "شما مجوز لازم جهت مشاهده صفحه را ندارید." );
        }

        return "";
    }

}
