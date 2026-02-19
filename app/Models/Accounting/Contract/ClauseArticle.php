<?php

namespace App\Models\Accounting\Contract;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClauseArticle extends Model
{
    use HasFactory;
    protected $fillable = [
        'caption','clause_type_id','token_id1','token_id2','token_id3','token_id4','token_id5','token_id6','token_id7','token_id8','token_id9','token_id10'
    ];
    protected $table = 'clause_articles';

    public function token1()
    {
        return $this->belongsTo(ContractKeyword::class,'token_id1');
    }
    public function token2()
    {
        return $this->belongsTo(ContractKeyword::class,'token_id2');
    }
    public function token3()
    {
        return $this->belongsTo(ContractKeyword::class,'token_id3');
    }
    public function token4()
    {
        return $this->belongsTo(ContractKeyword::class,'token_id4');
    }
    public function token5()
    {
        return $this->belongsTo(ContractKeyword::class,'token_id5');
    }
    public function token6()
    {
        return $this->belongsTo(ContractKeyword::class,'token_id6');
    }
    public function token7()
    {
        return $this->belongsTo(ContractKeyword::class,'token_id7');
    }
    public function token8()
    {
        return $this->belongsTo(ContractKeyword::class,'token_id8');
    }
    public function token9()
    {
        return $this->belongsTo(ContractKeyword::class,'token_id9');
    }
    public function token10()
    {
        return $this->belongsTo(ContractKeyword::class,'token_id10');
    }
}
