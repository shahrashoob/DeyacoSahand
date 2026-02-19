<?php

namespace App\Models\Contractor;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractorPost extends Model
{
    use HasFactory;
    protected $table="contractor_post";
    protected $fillable=["post_id","contractor_id"];
}
