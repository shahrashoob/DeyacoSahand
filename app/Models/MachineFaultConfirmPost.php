<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MachineFaultConfirmPost extends Model
{
    protected $table = 'machine_fault_confirm_posts';

    protected $fillable = [
        'post_id',
        'machine_id',
        'machine_fault_ids',
    ];

    public function faultType()
    {
        return $this->belongsTo(
            MachineFaultType::class,
            'machine_fault_ids' , 'id'
        );
    }

}
