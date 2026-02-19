<?php

namespace App\Models\Accounting\FinancialOperation;

use App\Models\Accounting\Account;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialOperationPatternItem extends Model
{
    use HasFactory;
    protected $table="financial_operation_pattern_item";
    protected $fillable=["financial_operation_pattern_id","account_id","financial_operation_pattern_item_type_id"];
    public function financial_operation_pattern(){
        return $this->belongsTo(FinancialOperationPattern::class);
    }
    public function financial_operation_pattern_item_type(){
        return $this->belongsTo(FinancialOperationPatternItemType::class);
    }
    public function account(){
        return $this->belongsTo(Account::class);
    }
}
