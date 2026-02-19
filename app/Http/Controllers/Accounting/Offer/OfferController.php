<?php

namespace App\Http\Controllers\Accounting\Offer;

use App\Http\Controllers\Controller;
use App\Models\Accounting\Offer;
use App\Models\LineProduct\Degree;
use App\Models\LineProduct\Product;
use App\Models\Utility\Option;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    //
    public function index()
    {
        $list = Offer::where("status_id",522000200)->paginate(50);

        return view("accounting.offer.index", compact("list"));
    }

    public function create($offer_type_id)
    {

        $product_option = Option::get("product_goods_1");
        $channel_type_option = Option::get("channel_type");
        $customer_option = Option::get("customer");
        $offer = new Offer();

        return view("accounting.offer.create", compact("offer_type_id", "offer", "product_option", "channel_type_option", "customer_option"));

    }

    public function store(Request $request)
    {

        $offer = new Offer($request->all());

        $offer_error = Offer::checkForNewItem($offer);
        if ($offer_error) {
           return redirect()->route("accounting.offer.index")->withErrors(" تخفیف جدید با تخفیف کد ".$offer_error->id. " تداخل دارد");
        }
        if($offer->min_buy > $offer->max_buy){
            return back()->withErrors(" حداقل خرید و حداکثر خرید به درستی وارد نشده است");

        }
        $start_datetime = Carbon::create($offer->start_datetime);
          $end_dateitme = Carbon::create($offer->end_datetime);


        if ($start_datetime > $end_dateitme) {
            return back()->withErrors("تاریخ شروع و پایان به درستی وارد نشده است");
        }

        $product=Product::find($request->product_id);
        $degree=Degree::where(["code"=>$request->degree_code,"goods_kind_id"=>$product->goods_kind_id??0])->first();
        if(!isset($degree) ){
            return back()->withErrors("کد درجه کالا به نادرست است. ");
        }
        $request["degree_id"]=$degree->id;

        $product_free=Product::find($request->product_free_id);
        $degree_free=Degree::where(["code"=>$request->degree_free_code,"goods_kind_id"=>$product_free->goods_kind_id??0])->first();
        if(!isset($degree_free) ){
            return back()->withErrors("کد درجه کالا به نادرست است. ");
        }
        $request["degree_free_id"]=$degree->id;
        $request["status_id"]=522000200;
        Offer::create($request->all());
        return redirect()->route("accounting.offer.index")->with(["success"=>"درج با موفقیت انجام شد."]);
    }

    public function de_active (Offer $offer){
        $offer->status_id=522000300;
        $offer->save();
        return redirect()->route("accounting.offer.index")->with(["success"=>"تخفیف با موفقیت غیر فعال شد."]);
    }
}
