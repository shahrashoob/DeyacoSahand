<?php

namespace App\Http\Controllers\Utility\PopUp;

use App\Http\Controllers\Controller;
use App\Models\Customer\Customer;
use App\Models\Post\Post;
use App\Models\Utility\Option;
use App\Models\Utility\PupUp\PupUp;
use App\Models\Utility\Setting;
use Illuminate\Http\Request;

class AdminPopUpController extends Controller
{
    protected $route_path = "utility.pup_up.admin.";
    protected $view_path = "utility/pup_up/admin/";
    public function index() {

        $list = PupUp::orderByDesc("id")->paginate();

        return view( $this->view_path . "index", compact( "list" ) );
    }

    public function create( Request $request ) {

        $post_list=Post::all();

        $customer_level_list=[];
        $post_show_ids=[];

        return view( $this->view_path . "create", compact( "post_show_ids", "post_list","customer_level_list" ) );
    }

    public function store(Request $request){


        $level_list=[];

        // Post
        $post_list=[];

        foreach (Post::all() as $post){
            $key="post_".$post->id;
            if(isset($request->$key)){
                $post_list[]=$post->id;
            }
        }

        if(count($post_list) == 0){
            return  redirect()->back()->withErrors("لطفا حداقل یک پست انتخاب نمایید.");
        }

        $pup_up=PupUp::create($request->all());
        $pup_up->post_show_ids=implode( ",", $post_list );
        $pup_up->save();

        return redirect()->route($this->route_path."index")->with(["success"=>"یک pup up با موفقیت اضافه گردید."]);

    }

    public function edit( Request $request,PupUp $pup_up) {



        $post_list=Post::all();

        $customer_level_list= explode( ",", $pup_up->level_numbers );
        $post_show_ids=explode( ",", $pup_up->post_show_ids );

        return view( $this->view_path . "edit", compact( "pup_up","post_show_ids", "post_list","customer_level_list" ) );
    }
    public function update(Request $request,PupUp $pup_up){


        $level_list=[];


        // Post
        $post_list=[];

        foreach (Post::all() as $post){
            $key="post_".$post->id;
            if(isset($request->$key)){
                $post_list[]=$post->id;
            }
        }

        if(count($post_list) == 0){
            return  redirect()->back()->withErrors("لطفا حداقل یک پست انتخاب نمایید.");
        }

        $pup_up->update($request->all());
        $pup_up->post_show_ids=implode( ",", $post_list );
        $pup_up->save();

        return redirect()->route($this->route_path."index")->with(["success"=>"اطلاعات با موفقیت بروزرسانی شد. "]);

    }

    public function destroy(PupUp $pup_up){
        $pup_up->delete();
        return redirect()->route($this->route_path."index")->with(["success"=>"یک pup up با موفقیت حذف گردید."]);

    }
}
