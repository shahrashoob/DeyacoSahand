<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\LineProductStation;
use App\Models\Production\Production;
use App\Models\Utility\Menu\MenuPost;
use App\Models\Utility\Option;
use Illuminate\Http\Request;

class Report1002Controller extends Controller
{
    //
    public function index(Request $request)
    {

        $lines_id_permission = \Auth::user()->posts->first()->post->get_lines_id_permission();

        if (count($lines_id_permission) == 0) {
            return back()->withErrors("هیچ گونه دسترسی برای شما در کارتابل تولید تعریف نشده است.");
        }

        if ($request->isMethod('post')) {
            $search =$request->search;
            $order_by =$request->order_by;
        }
        else{
            $search =session("search_report_1002_card");
            $order_by =session("order_by_report_1002")??"production_cards.updated_at__desc";
        }
        session(["search_report_1002_card"=>$search, "order_by_report_1002"=>$order_by]);

        $list = Production::search($search, $order_by, $lines_id_permission);
        if ($search == "") {
            $list = $list->where("production_cards.status_id","!=", 500);
        }
        $list = $list->paginate(50);

        $orber_by_Option = Option::OrderBy("production", $order_by);

        return view("report.1002.index", compact("orber_by_Option", "list", "search"));
    }
    public function search(Request $request)
    {
        //   $date_card=   jdate( \Carbon\Carbon::parse($request->card_date)->timestamp)->format('%Y%m%d');
        $production_series = $request->product_code;
        $production = Production::where(["serial" => $production_series])->first();

        if (Production::where(["serial" => $production_series])->exists()) {

            return view("report.1002.search", compact("production"));

        }
        return back()->withErrors("کارت تولید یافت نشد");
    }

    public function view_card(Production $production)
    {
        // Check Permission
        $post_user = \Auth::user()->posts->first();
        $lines_id_permission = \Auth::user()->posts->first()->post->get_lines_id_permission();
        if (count($lines_id_permission) == 0) {
            return back()->withErrors("هیچ گونه دسترسی برای شما در کارتابل تولید تعریف نشده است.");
        }
        $lines = LineProductStation::where([ "product_id" => $production->product_id])->pluck("line_id")->toArray();
        $allow = False;
        if (count($lines) > 0) {
            foreach ($lines as $line_id) {
                if (in_array($line_id, $lines_id_permission)) {
                    $allow = True;
                }
            }
        }

        if (!$allow) {
            return back()->withErrors("شما اجازه مشاهده این کارت تولید را ندارید");
        }

        // دسترسی موقت
        $menu_planning=  MenuPost::where([
            "menu_id"=>111,
            "post_id"=>\Auth::user()->posts->first()->post->id
        ])->first();

        return view("report.1002.view_card", compact("post_user","production","menu_planning"));

    }



}
