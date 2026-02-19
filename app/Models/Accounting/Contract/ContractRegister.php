<?php

namespace App\Models\Accounting\Contract;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractRegister extends Model
{
    use HasFactory;
    protected $fillable = [
        'contract_id','contract_party_id','code'
    ];
    protected $table = 'contract_registers';


}
