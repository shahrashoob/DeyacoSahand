<?php

namespace App\Models\HR\User;

use App\Models\Accounting\Bank\Bank;
use App\Models\HR\Employment\EmploymentSelectionIndicatorValue;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBankAccount extends Model
{
    use HasFactory;

    protected $fillable = ["user_id",
        'account_number',
        'shaba_number',
        'card_number',
        'bank_id',
        'bank_branch',
    ];

    protected $table = 'user_bank_accounts';


    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }
}
