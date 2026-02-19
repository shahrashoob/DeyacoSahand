<?php

namespace App\Models\HR\Personal;

use Illuminate\Database\Eloquent\Model;

class Nationality extends Model
{
    protected $fillable = [
        'caption',
    ];
    public function personal()
    {
        return $this->hasMany(Personal::class);
    }
    protected $table = 'nationalities';
}
