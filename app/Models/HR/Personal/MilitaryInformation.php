<?php

namespace App\Models\HR\Personal;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MilitaryInformation extends Model
{
    use HasFactory;

    protected $fillable = [
        'caption','document_caption'
    ];

    protected $table = 'military_informations';
}
