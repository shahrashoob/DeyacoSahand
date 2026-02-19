<?php

namespace App\Models\Utility\Financial;

use App\Models\Utility\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialSoftware extends Model {
    use HasFactory;

    protected $table = "financial_softwares";
    protected $fillable = [ "database_name", "server_url", "username", "password" ];

    public function active_status() {
        return $this->belongsTo( Status::class, "active_status_id" );
    }
}
