<?php

namespace App\Models\HR\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CooperationTypePermission extends Model
{
    use HasFactory;
    protected $table="cooperation_type_permission";
    protected $fillable=["post_id","cooperation_type_id"];
}
