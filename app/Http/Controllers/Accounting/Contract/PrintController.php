<?php

namespace App\Http\Controllers\Accounting\Contract;

use App\Http\Controllers\Controller;
use App\Models\Accounting\Contract\Contract;
use App\Models\Accounting\Contract\ContractClauseType;
use App\Models\Accounting\Contract\ContractKeyword;
use App\Models\HR\Employment\Employment;
use App\Models\Post\PostUser;
use App\Models\Utility\Pdf;
use App\Models\Utility\Setting;
use Illuminate\Http\Request;
use Mpdf\Mpdf;

class PrintController extends Controller
{
    //استفاده تابع در قراداد
    public static  function PrintContract(Contract $contract,$file_name,$keys=[])
    {
        $contract_clause_type_list = ContractClauseType::join('clause_types', 'contract_clause_type.clause_type_id', 'clause_types.id')->
        where('contract_clause_type.contract_id', $contract->id)->
        orderBy('contract_clause_type.priority_number')->
        pluck('caption', "clause_type_id");
        $article_list = [];
        $clause_article_list = ContractClauseType::join('clause_articles', 'contract_clause_type.clause_article_id', 'clause_articles.id')->
        where('contract_clause_type.contract_id', $contract->id)->
        select('clause_articles.*', 'contract_clause_type.*')->
        get();
        //بند
        foreach ($clause_article_list as $item) {
            $article_list[$item->clause_type_id][$item->id] = $item;
        }

        $keys_id = [];
        foreach (ContractKeyword::all() as $item) {
            if (isset($keys[$item->keyword])) {
                $keys_id[$item->id] = $keys[$item->keyword];
            } else {
                $keys_id[$item->id] = "...............";
            }
        }
        $html[0] = view("accounting.contract.print._header", compact('contract' ))->render();
        $html[0] .= view("accounting.contract.print._info", compact('contract', 'contract_clause_type_list', 'article_list','keys','keys_id'))->render();
        $html[0] .= view("accounting.contract.print._footer" )->render();
        Pdf::createAsHtml( $html,"P",$file_name,"A4"," ",false);

    }
    //استفاده از تابع در همکاری با ما
    public static  function CreatePdfFile(Contract $contract,$employment,$file_name,$keys=[])
    {
        $company_name_setting = Setting::getStringValue("company_name");
        $post_id_for_contract_setting = Setting:: getIntegerValue("post_id_for_contract");
        $post_user = PostUser::where('post_id', $post_id_for_contract_setting)->first();
        if (!$post_user) {
            return redirect()->back()->withErrors("تنظیمات کد پست سازمانی جهت عقد قرارداد نامعتبر است.");
        }
        $contract_clause_type_list = ContractClauseType::join('clause_types', 'contract_clause_type.clause_type_id', 'clause_types.id')->
        where('contract_clause_type.contract_id', $contract->id)->
        orderBy('contract_clause_type.priority_number')->
        pluck('caption', "clause_type_id");
        $article_list = [];
        $clause_article_list = ContractClauseType::join('clause_articles', 'contract_clause_type.clause_article_id', 'clause_articles.id')->
        where('contract_clause_type.contract_id', $contract->id)->
        select('clause_articles.*', 'contract_clause_type.*')->
        get();
        //بند
        foreach ($clause_article_list as $item) {
            $article_list[$item->clause_type_id][$item->id] = $item;
        }

        $keys_id = [];
        foreach (ContractKeyword::all() as $item) {
            if (isset($keys[$item->keyword])) {
                $keys_id[$item->id] = $keys[$item->keyword];
            } else {
                $keys_id[$item->id] = "...............";
            }
        }

        if(in_array($employment->cooperation_type_id,[1,11]) ){
            $html[0] = view("hr.employment.register.personal.confirm_drafting_contract.print", compact('contract','company_name_setting','post_user',
                'employment', 'contract_clause_type_list', 'article_list','keys','keys_id'))->render();
        }
        if(in_array($employment->cooperation_type_id,[6,61]) ){
            $html[0] = view("hr.employment.register.supplier.confirm_drafting_contract.print", compact('contract','company_name_setting','post_user',
                'employment', 'contract_clause_type_list', 'article_list','keys','keys_id'))->render();
        }
        if(in_array($employment->cooperation_type_id,[3,31]) ){
            $html[0] = view("hr.employment.register.customer.confirm_drafting_contract.print", compact('contract','company_name_setting','post_user',
                'employment', 'contract_clause_type_list', 'article_list','keys','keys_id'))->render();
        }
        if(in_array($employment->cooperation_type_id,[2,21]) ){
            $html[0] = view("hr.employment.register.contractor.confirm_drafting_contract.print", compact('contract','company_name_setting','post_user',
                'employment', 'contract_clause_type_list', 'article_list','keys','keys_id'))->render();
        }
        Pdf::createAsHtml( $html,"P",$file_name,"A4"," ",false);
    }
}
