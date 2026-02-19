<?php

namespace App\Models\Accounting\Contract;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractClauseType extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id', 'clause_article_id', 'priority_number', 'clause_type_id'
    ];
    protected $table = 'contract_clause_type';

    public function clause_type()
    {
        return $this->belongsTo(ClauseType::class);
    }

    public function clause_article()
    {
        return $this->belongsTo(ClauseArticle::class, 'clause_article_id');
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }
}
