<?php

namespace App\Models\Accounting\Store;

use App\Models\Utility\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'caption',
        'description',
        'price',
        'status_id',
        'client_transaction_id',
        'client_factor_id',
        'validity_date'
    ];
    protected $table = 'stores';
    public function status()
    {
        return $this->belongsTo(Status::class);
    }
}
