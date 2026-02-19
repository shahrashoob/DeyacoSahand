<?php

namespace App\Models\Accounting\Client;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientFactor extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_factor_type_id',
        'code',
        "caption",
        "sum_amount",
        "service_code",
        "total_amount",
        'tax',
    ];
    protected $table = 'client_factors';

    public function client_factor_type()
    {
        return $this->belongsTo(ClientFactorType::class, 'client_factor_type_id');
    }

    public function get_created_at()
    {

        return jdate(Carbon::parse($this->created_at)->timestamp)->format('Y/m/d ');
    }
}
