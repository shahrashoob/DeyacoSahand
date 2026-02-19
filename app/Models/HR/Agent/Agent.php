<?php

namespace App\Models\HR\Agent;

use App\Models\Company\Company;
use App\Models\Contractor\Contractor;
use App\Models\Customer\Customer;
use App\Models\HR\Employment\Employment;
use App\Models\Supplier\Supplier;
use App\Models\Utility\Status;
use App\Models\Worker;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    use HasFactory;

    protected $fillable = [
        'agent_type_id','user_id','company_id','address_id','customer_id','supplier_id','contractor_id','has_the_right_to_sign','employment_id','status_id','active_code'
    ];
    protected $table = 'agents';
    public function worker()
    {
        return $this->belongsTo(Worker::class, 'user_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
    public function contractor()
    {
        return $this->belongsTo(Contractor::class, 'contractor_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
    public function agent_type()
    {
        return $this->belongsTo(AgentType::class );
    }
    public function employment()
    {
        return $this->belongsTo(Employment::class );
    }
    public function status()
    {
        return $this->belongsTo(Status::class );
    }
}
