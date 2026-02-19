<?php

namespace App\Http\Controllers\Utility\Update;

use App\Http\Controllers\Controller;
use App\Models\Utility\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    //  درخواست بروز رسانی سامانه
    public function index()
    {
        $time=Carbon::now()->format('Y/m/d ')." 15:30";
        $updated_at=jdate(Carbon::parse($time)->addDay())->format('H:i Y/m/d ');
        return view('utility.update.dashboard.index',compact("updated_at"));
    }
    public function submit(Request $request)
    {
        $has_active_contract=Setting::getIntegerValue("has_active_contract");
        if(!$has_active_contract){
            return back()->withErrors("با توجه به اینکه هیچ قرارداد پشتیبانی فعالی برای شرکت وجود ندارد، امکان ثبت درخواست بروزرسانی امکان پذیر نمی باشد."

                    ."<br/> جهت تمدید قرارداد پشتیبانی به فروشگاه آنلاین دیاکو مراجعه فرمایید."."<br/> <a href='".route("accounting.store.dashboard.index")."'>وورد به فروشگاه آنلاین دیاکو</a>"
            );
        }
        $s=Setting::where("key","request_update_at")->first();
        $time=Carbon::now()->format('Y/m/d ')." 15:30";
        $updated_at=jdate(Carbon::parse($time))->format('H:i Y/m/d ');
        $s->update(["string_value"=>"در انتظار  انجام بروز رسانی - ".Auth::user()->fullName()." - ".$updated_at]);
        return redirect("dashboard")->with(["success"=>"درخواست بروز رسانی با موفقتی ثبت گردید و در ساعت تعیین شده، به صورت اتوماتیک انجام خواهد شد."]);

    }
}
