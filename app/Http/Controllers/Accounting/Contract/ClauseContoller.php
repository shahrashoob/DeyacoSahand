<?php

namespace App\Http\Controllers\Accounting\Contract;

use App\Http\Controllers\Controller;
use App\Models\Accounting\Contract\ClauseArticle;
use App\Models\Accounting\Contract\ClauseType;
use App\Models\Accounting\Contract\Contract;
use App\Models\Accounting\Contract\ContractClauseType;
use App\Models\Accounting\Contract\ContractRegister;
use App\Models\Accounting\Contract\ContractRegisterClauseType;
use App\Models\HR\Education\Education;
use App\Models\Utility\Option;
use Illuminate\Http\Request;

//این کنترلر مرتبط با ماده و بند های می باشد.
class ClauseContoller extends Controller
{
    private $view_path = "accounting.contract.clause.";
    private $route_path = "accounting.contract.clause.";


    public function index()
    {
        $list = ClauseType::paginate(50);
        return view($this->view_path . "index", compact('list'));
    }

    public function create()
    {

        return view($this->view_path . "create");
    }

    public function store(Request $request)
    {
        $exist = ClauseType::where('caption', $request->caption)->exists();
        if ($exist) {
            return back()->withErrors("عنوان قرارداد تکراری می باشد.");
        }
        $clause_type = ClauseType::create([

            "caption" => $request->caption,

        ]);

        return redirect()->route($this->route_path . "index", $clause_type)->with(["success" => "ماده قراداد با موفقیت افزوده شد"]);;
    }

    public function add_clause_article(ClauseType $clause_type)
    {
        $contract_keyword_option = Option::get("contract_keyword");
        $cluase_articles = ClauseArticle::where('clause_type_id',$clause_type->id)->get();
        return view($this->view_path . "add_clause_article", compact('clause_type','cluase_articles','contract_keyword_option'));
    }

    public function submit_clause_article(ClauseType $clause_type, Request $request)
    {
        $exist = ClauseArticle::where('caption', $request->caption)->where('clause_type_id',$clause_type->id)->exists();
        if ($exist) {
            return back()->withErrors("محتوای بند تکراری می باشد.");
        }
         ClauseArticle::create([
            "caption" => $request->caption,
            "clause_type_id" => $clause_type->id,
             "token_id1" => $request->token_id1,
             "token_id2" => $request->token_id2,
             "token_id3" => $request->token_id3,
             "token_id4" => $request->token_id4,
             "token_id5" => $request->token_id5,
             "token_id6" => $request->token_id6,
             "token_id7" => $request->token_id7,
             "token_id8" => $request->token_id8,
             "token_id9" => $request->token_id9,
             "token_id10" => $request->token_id10,
        ]);

        return redirect()->route($this->route_path . "add_clause_article",$clause_type)->with(["success" => "یک بند قراداد با موفقیت افزوده شد."]);;
    }
    public function destroy_clause_article(ClauseArticle $clause_article)
    {

        $contract_Cluse_type=ContractClauseType::where('clause_article_id',$clause_article->id)->exists();
        if( $contract_Cluse_type){
            return back()->withErrors("این بند در یک قراداد وجود دارد بنابراین حذف امکان پذیر نیست.");
        }
        $contract_register_cluse_type=ContractRegisterClauseType::where('clause_article_id',$clause_article->id)->exists();
        if( $contract_register_cluse_type){
            return back()->withErrors("این بند در یک قراداد  ثبت  شده وجود دارد بنابراین حذف امکان پذیر نیست.");
        }
        $clause_article->delete();

        return redirect()->back()->with(["success" => "یک  بند  با موفقیت حذف گردید."]);
    }

}
