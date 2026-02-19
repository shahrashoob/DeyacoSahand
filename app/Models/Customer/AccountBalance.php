<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountBalance extends Model
{
    use HasFactory;
    protected $table="account_balances";
    protected $fillable=["customer_id"];

    public function getBalance1401(){

        if($this->debtor_1401 != 0){
            return number_format($this->debtor_1401). " بد";
        }
        if($this->creditor_1401 != 0){
            return number_format($this->creditor_1401). " بس";
        }
        return 0;
    }
    public function getBalance1301(){

        if($this->debtor_1301 != 0){
            return number_format($this->debtor_1301). " بد";
        }
        if($this->creditor_1301 != 0){
            return number_format($this->creditor_1301). " بس";
        }
        return 0;
    }
    public function getBalance1302(){

        if(($this->debtor_1302+ $this->debtor_1304) != 0){
            return number_format(($this->debtor_1302+ $this->debtor_1304)). " بد";
        }
        if(($this->creditor_1302+ $this->creditorr_1304) != 0){
            return number_format(($this->creditor_1302+ $this->creditorr_1304)). " بس";
        }
        return 0;
    }
    public function getBalance1303(){

        if($this->debtor_1303 != 0){
            return number_format($this->debtor_1303). " بد";
        }
        if($this->creditor_1303 != 0){
            return number_format($this->creditor_1303). " بس";
        }
        return 0;
    }
}
