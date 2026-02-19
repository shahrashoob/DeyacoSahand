<?php

namespace App\Models\Accounting\Contract;

use App\Models\Utility\Status;
use App\Models\Worker;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;

    protected $fillable = [
        'caption', 'contract_type_id', 'active_status_id','description','number_of_contract','guide'
    ];
    protected $table = 'contracts';

    public function contract_type()
    {
        return $this->belongsTo(ContractType::class);
    }

    public function active_status()
    {
        return $this->belongsTo(Status::class, 'active_status_id');
    }

    public function contract_clause_types()
    {
        return $this->hasMany(ContractClauseType::class );
    }

}
