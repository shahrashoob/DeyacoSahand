<?php

namespace App\Models\Accounting\FinancialOperation;

use App\Models\Accounting\Account;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialOperationPattern extends Model
{
    use HasFactory;

    protected $fillable = [
        "caption",
        "financial_operation_pattern_type_id",
        "register_detailed_code_for_customer",
        "detailed_code_for_customer_number"
    ];

    public function financial_operation_pattern_type()
    {
        return $this->belongsTo(FinancialOperationPatternType::class);
    }

    public function items()
    {
        return $this->hasMany(FinancialOperationPatternItem::class);
    }
    /**
     * حساب های مرتبط اصلی
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function main_items()
    {
        return $this->hasMany(FinancialOperationPatternItem::class)->where("financial_operation_pattern_item_type_id",1);
    }
    /**
     * حساب های مرتبط ارزش افزوده
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tax_items()
    {
        return $this->hasMany(FinancialOperationPatternItem::class)->where("financial_operation_pattern_item_type_id",2);
    }
    /**
     * حساب های مرتبط ارزش افزوده
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function off_items()
    {
        return $this->hasMany(FinancialOperationPatternItem::class)->where("financial_operation_pattern_item_type_id",3);
    }

    public static function ExistsCaption($caption, $id)
    {
        if ($id) {
            return FinancialOperationPattern::where(["caption" => $caption])->where("id", "!=", $id)->exists();
        }
        return FinancialOperationPattern::where(["caption" => $caption])->exists();
    }
}
