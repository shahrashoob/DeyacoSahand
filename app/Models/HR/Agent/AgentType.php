<?php

namespace App\Models\HR\Agent;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgentType extends Model
{
    use HasFactory;
    protected $fillable = [
        'caption',"number_of_agent"
    ];
    protected $table = 'agent_types';
}
