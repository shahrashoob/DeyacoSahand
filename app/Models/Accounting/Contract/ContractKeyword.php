<?php

namespace App\Models\Accounting\Contract;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractKeyword extends Model
{
    use HasFactory;

    protected $fillable = [
        'caption','keyword'
    ];
    protected $table = 'contract_keywords';
}
