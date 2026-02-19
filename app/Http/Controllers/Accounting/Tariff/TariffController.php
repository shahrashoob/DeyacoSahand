<?php

namespace App\Http\Controllers\Accounting\Tariff;

use App\Exports\Utility\TariffLogExport;
use App\Http\Controllers\Controller;
use App\Imports\TariffProductImport;
use App\Models\Accounting\Tariff\NewTariffProduct;
use App\Models\Accounting\Tariff\ProductTariff;
use App\Models\Accounting\Tariff\ProductTariffLog;
use App\Models\Accounting\Tariff\Tariff;
use App\Models\Accounting\Tariff\TariffLog;
use App\Models\Production\Production;
use App\Models\Utility\Option;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class TariffController extends Controller
{
    //
    public function index(Request $request)
    {
        $post_user = \Auth::user()->posts->first();

        if ($request->isMethod('post')) {
            $search = $request->search;
        } else {
            $search = session("search_tariff");
        }
        session([
            "search_tariff" => $search,
        ]);

        $list = Tariff::
        when($search, function ($query) use ($search) {
            return $query->where(function ($query) use ($search) {
                return $query->where("caption", "like", "%" . $search . "%")->
                orWhere("id", "like", "%" . $search . "%");
            });

        })->
        paginate(30);
        return view("accounting.tariff.index", compact("list", "post_user","search"));
    }

    public function create()
    {
        $post_user = \Auth::user()->posts->first();
        if (!$post_user->checkButtonPermission("accounting.tariff.add_new")) {
            return back()->withErrors("شما اجازه دسترسی به عملیات را ندارید.");
        }
        $currencies_option = Option::get("currency");
        $tariff = new Tariff();
        return view("accounting.tariff.create", compact("currencies_option", "tariff"));
    }

    public function store(Request $request)
    {
        $post_user = \Auth::user()->posts->first();
        if (!$post_user->checkButtonPermission("accounting.tariff.add_new")) {
            return back()->withErrors("شما اجازه دسترسی به عملیات را ندارید.");
        }
        $tarrif=self::CreateTariff($request);
        if(!$tarrif['result']){
            return  back()->withErrors($tarrif['error']);
        }
        return redirect()->route("accounting.tariff.index")->with(["success" => "تعرفه با موفقیت اضافه شد"]);
    }

    public function edit(Tariff $tariff)
    {
        $post_user = \Auth::user()->posts->first();
        if (!$post_user->checkButtonPermission("accounting.tariff.edit_info")) {
            return back()->withErrors("شما اجازه دسترسی به عملیات را ندارید.");
        }

        $currencies_option = Option::get("currency", $tariff->currency->id);
        $status_option = Option::get("status", $tariff->status_id, 5201);
        return view("accounting.tariff.edit", compact("post_user", "currencies_option", "tariff", "status_option"));
    }

    public function update(Request $request, Tariff $tariff)
    {
        $post_user = \Auth::user()->posts->first();
        if (!$post_user->checkButtonPermission("accounting.tariff.upload_list")) {
            return back()->withErrors("شما اجازه دسترسی به عملیات را ندارید.");
        }

        $post_user = \Auth::user()->posts->first();
        if (!$post_user->checkButtonPermission("accounting.tariff.edit_info")) {
            return back()->withErrors("شما اجازه دسترسی به عملیات را ندارید.");
        }

        $tariff->update($request->all());
        $tariff->log(520100520);
        return redirect()->route("accounting.tariff.index")->with(["success" => "ویرایش با موفقیت انجام شد."]);
    }

    public function upload(Tariff $tariff)
    {

        $post_user = \Auth::user()->posts->first();
        if (!$post_user->checkButtonPermission("accounting.tariff.upload_list")) {
            return back()->withErrors("شما اجازه دسترسی به عملیات را ندارید.");
        }

        $model = ["id" => $tariff->id, "name" => "tariff", "route" => "accounting.tariff.submit_upload", "caption" => "آپلود تعرفه " . $tariff->id];
        return view("import/index", compact("model"));

    }

    public function submit_upload(Tariff $tariff)
    {

        $post_user = \Auth::user()->posts->first();
        if (!$post_user->checkButtonPermission("accounting.tariff.upload_list")) {
            return back()->withErrors("شما اجازه دسترسی به عملیات را ندارید.");
        }

        $TPI = new TariffProductImport();
        $TPI->tariff_id = $tariff->id;

        Excel::import($TPI, request()->file('file_uploaded'));

        return redirect()->route("accounting.tariff.show_upload", $tariff);

    }

    public function show_upload(Tariff $tariff)
    {

        $post_user = \Auth::user()->posts->first();
        if (!$post_user->checkButtonPermission("accounting.tariff.upload_list")) {
            return back()->withErrors("شما اجازه دسترسی به عملیات را ندارید.");
        }

        $list = NewTariffProduct::orderBy("error", "desc")->paginate(50);

        $error_count = NewTariffProduct::where("error", "!=", "")->count();

        $count = NewTariffProduct::count();;

        return view("import/tariff_product", compact("list", "error_count", "tariff", "count"));
    }

    public function upload_product(Tariff $tariff)
    {

        $post_user = \Auth::user()->posts->first();
        if (!$post_user->checkButtonPermission("accounting.tariff.upload_list")) {
            return back()->withErrors("شما اجازه دسترسی به عملیات را ندارید.");
        }


        $list = NewTariffProduct::select("tariff_id", "product_id","service_id","degree_id","warehouse_id", "fea", "min_buy", "max_buy", "tax", "fare", "consumer_price","packing_type_id","type_of_sale_of_product_id","customer_product_code","customer_product_caption","increase_percentage_deadline_per_day")->get()->toArray();

        ProductTariff::where("tariff_id", $tariff->id)->delete();
        ProductTariff::insert($list);
        ProductTariffLog::insert($list);

        $tariff_log = $tariff->log(520100530); // آپلود لیست

        ProductTariffLog::
        where("tariff_log_id", 0)->
        where("tariff_id", $tariff->id)->
        update(["tariff_log_id" => $tariff_log->id]);

        NewTariffProduct::where("id", ">", 0)->delete();

        return redirect()->route("accounting.tariff.index")->with(["success" => "آپلود با موفقیت انجام شد."]);

    }

    public function view_log(Tariff $tariff)
    {
        $post_user = \Auth::user()->posts->first();
        if (!$post_user->checkButtonPermission("accounting.tariff.view_log")) {
            return back()->withErrors("شما اجازه دسترسی به عملیات را ندارید.");
        }

        $list = TariffLog::where("tariff_id", $tariff->id)->orderByDesc("id")->paginate(50);
        return view("accounting.tariff.view_log", compact("tariff", "list"));

    }

    public function download_log_list(Tariff $tariff , TariffLog $tariff_log){

        $export=new TariffLogExport();
        $export->list=ProductTariffLog::
        where("tariff_log_id", $tariff_log->id)->
        where("tariff_id", $tariff->id)->
        get();
        return Excel::download($export, 'tariff_'.$tariff->id."_". jdate(Carbon::now()->timestamp)->format('Y_m_d') . '.xlsx');
    }
    public static function CreateTariff($request) {
        $request["status_id"] = 520100200;
        $tariff_exist = Tariff::where('caption', $request->caption)->exists();
        if ($tariff_exist) {
            return [
                'result' => false,
                'error' => 'عنوان تعرفه تکراری می باشد',
            ];
        }
        $tariff = Tariff::create($request->all());
        $tariff->log(520100510);
        return [
            'result' => true,
            'message' => 'تعرفه با موفقیت ثبت شد.',
        ];
    }
}
