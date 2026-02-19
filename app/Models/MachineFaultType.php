<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MachineFaultType extends Model
{
    
    protected $table = "machine_fault_types";

    protected $fillable = [
        'caption',
    ];

    public function confirmPosts()
    {
        return $this->hasMany(
            MachineFaultConfirmPost::class,
            'machine_fault_ids'
        );
    }
}
