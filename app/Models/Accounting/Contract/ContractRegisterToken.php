<?php

namespace App\Models\Accounting\Contract;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractRegisterToken extends Model
{
    use HasFactory;
    protected $fillable = [
        'contract_register_id','contract_keyword_id','value'];
    protected $table = 'contract_register_token';


}
