<?php

namespace App\Models\Accounting\Contract;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractRegisterClauseType extends Model
{
    use HasFactory;
    protected $fillable = [
        'contract_register_id','clause_article_id','priority_number'];
    protected $table = 'contract_register_clause_type';


}
