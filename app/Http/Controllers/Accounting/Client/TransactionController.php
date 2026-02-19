<?php

namespace App\Http\Controllers\Accounting\Client;

use App\Http\Controllers\Controller;
use App\Models\Accounting\Client\ClientTransaction;
use App\Models\Utility\Notification\SMSMessageResult;
use App\Models\Utility\Setting;
use App\Models\Worker;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public $route_path = "accounting.client.transaction.";
    public $view_path = "accounting.client.transaction.";

    public function index()
    {
            $list = ClientTransaction::where('client_transaction_type_id',2)->whereNull('client_factor_id')->orderByDesc( "created_at" )->paginate( 50 );
        if (!$list) {
            return back()->withErrors("شما هنوز هیچ تراکنشی انجام نداده اید.");
        }
        return view($this->view_path . "index", compact("list"));

    }
    public function show(ClientTransaction $client_transaction)
    {
        $list = SMSMessageResult::where('client_transaction_id',$client_transaction->id)->paginate( 50 );
        if (!$list) {
            return back()->withErrors("شما با خطایی مواجه شده اید لطفا با پشتیبانی تماس بگیرید.");
        }
        $mobiles=[];
        foreach ($list as $item) {
            $mobiles[$item->receptor]=ltrim($item->receptor,"0");
        }
        $mobiles[0]=-1;

      $worker_list=  Worker::whereIn("mobile",$mobiles)->get()->keyBy("mobile");
        return view($this->view_path . "show", compact("list",'client_transaction',"worker_list"));
    }
}
