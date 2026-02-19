<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use App\Models\Order\OrderList;
use App\Models\Order\RequstFromWarehouse;
use App\Models\Production\Production;
use App\Models\Utility\RCheckList;
use Illuminate\Http\Request;
use App\Models\Utility\Call;
use App\Models\Utility\Option;
use Illuminate\Support\Facades\Auth;

class CallController extends Controller
{
    var $path;
    var $public_args;
    var $rscript;

    var $connection=["ip"=>"45.149.78.134","port"=>3306, "user"=>"root","password"=>"Ali#8812583*Shire"];

    public function __construct( Request $request ) {
        $this->path = base_path() . "/RCall/";
        $user=env("DB_USERNAME");
        $password=env("DB_PASSWORD")==""?"empty":env("DB_PASSWORD");
        $host=env("DB_HOST");
        $dbname=env("DB_DATABASE");
        $this->public_args=" ". $this->path . " $user $password $host $dbname";
        $this->rscript="RScript " . $this->path ."";
    }
    //
    public function index($step=1){

        $call_error=Call::whereIn("status_id",[3310,3300])->where("warehouse_import_status_id",3330)
            ->where("nosa_import_status_id",3330)
            ->first();

        $stepInfo       = Option::stepInfo( "call_steps", $step );
        $last_check_list=RCheckList::orderByDesc("id")->limit(20)->get();
        return view("utility.call.index",compact("stepInfo","call_error","last_check_list"));


    }

    public function create(Request $request){

        $call=Call::where("status_id",3300)->firstOrCreate();
        $call->user_id=Auth::user()->id;
        $call->save();

        return redirect()->route("import.nosa.index",$call);

    }

    public function rscript(){

        $call=Call::where("status_id",3300)->firstOrCreate();
        $call->user_id=Auth::user()->id;
        $call->save();

        $step=5;
        $stepInfo       = Option::stepInfo( "call_steps", $step );
        return view("utility.call.rscript",compact("call","stepInfo","step"));

    }

    public function test_r() {

       return $cmd = $this->rscript. " test.r" .$this->public_args;
        // execute R script from shell
        $output = shell_exec($cmd);

        if(trim($output)!="[1] TRUE"){
//            return redirect()->back()->withErrors(__("message.error_in_test.ir"));
            return json_encode(["data" => [
                'result' => false,
                'message'=>__("message.error_in_test.ir")
            ]]);
        }
        return "ok";
    }

    public function checkIfCallExist($id){

    }
    public function cancel(Call $call,$pass){

//        if($pass=="Ali884564586723678fsdf(*3"){
//
//            $call->status_id=3325;
//            $call->save();
//
//            OrderList::where("call_id",$call->id)->delete();
//            Production::where("call_id",$call->id)->delete();
//            RequstFromWarehouse::where("call_id",$call->id)->delete();
//            return back()->with(["success"=>"فراخوانی کنسل شد"]);
//        }
    }


}
