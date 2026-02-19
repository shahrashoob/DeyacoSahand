<?php

namespace App\Models\Accounting\Client;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientFactorType extends Model
{
    use HasFactory;
    protected $fillable = [
'caption'
    ];
    protected $table = 'client_factor_types';
}
