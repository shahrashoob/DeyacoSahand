<?php

namespace App\Models\Accounting;

use App\Models\Accounting\FinancialOperation\FinancialOperationPatternItem;
use App\Models\Utility\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class   Account extends Model
{

    use HasFactory;

    protected $fillable = ["caption", "code", "parent_id", "full_code", "has_separator_in_full_code"];

    public function fullCaption()
    {
        return $this->full_code . " - " . $this->caption;
    }

    public function parent()
    {
        return $this->belongsTo(Account::class, "parent_id");
    }

    public function fullCode()
    {
        if (!$this->full_code) {
            $this->full_code = $this->code;
            $this->save();
        }

        return $this->full_code;
    }

    public function UpdateFullCode()
    {
        $separator = "";
        if (($this->parent->has_separator_in_full_code??false)) {
            $separator = "/";
        }
        if ($this->parent_id) {
            $this->full_code = $this->parent->full_code . $separator . $this->code;
        } else {
            $this->full_code = $this->code;
        }
        $this->save();
        return $this->full_code;
    }
    public static function UpdateFullCodeFroAllSubAccount(Account $account){
        $account->UpdateFullCode();
        foreach ($account->items as $sub_account){
            self::UpdateFullCodeFroAllSubAccount($sub_account);
        }
    }

    public static function ExistsCode($code, $parent_id, $id = false)
    {
        if ($id) {
            return Account::where(["code" => $code, "parent_id" => $parent_id])->where("id", "!=", $id)->exists();
        }

        return Account::where(["code" => $code, "parent_id" => $parent_id])->exists();
    }

    public static function ExistsCaption($code, $parent_id, $id = false)
    {
        if ($id) {
            return Account::where(["caption" => $code, "parent_id" => $parent_id])->where("id", "!=", $id)->exists();
        }

        return Account::where(["caption" => $code, "parent_id" => $parent_id])->exists();
    }

    public function items()
    {
        return $this->hasMany(Account::class, "parent_id");
    }

    /**
     * آیا می توان برای حساب یک زیر حساب تعریف کرد
     * @param $account
     * @return void
     */
    public static function AllowCreateSubAccount($account)
    {
        $pattern_item = FinancialOperationPatternItem::
        where("account_id", $account->id)->get();
        $error = "";
        if (count($pattern_item) != 0) {
            $error .= "با توجه به اینکه حساب در الگوهای عملیات مالی زیر تعریف شده اند، امکان تعریف زیر حساب وجود ندارد" . "<br/>";
            foreach ($pattern_item as $item) {
                $error .= $item->financial_operation_pattern->caption . ",";
            }
        }
        if ($error != "") {
            return [
                "result" => false,
                "error" => $error
            ];
        }
        return ["result" => true];
    }
}
