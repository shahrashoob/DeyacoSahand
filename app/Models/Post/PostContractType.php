<?php

namespace App\Models\Post;

use App\Models\Accounting\Contract\ContractType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostContractType extends Model
{
    use HasFactory;
    protected $table = "post_contract_types";
    protected $fillable = [ "post_id", "contract_type_id" ];

    public function contract_type()
    {
        return $this->belongsTo(ContractType::class);
    }
}
