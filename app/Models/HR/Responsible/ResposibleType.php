<?php

namespace App\Models\HR\Responsible;

use App\Models\HR\Education\Education;
use App\Models\HR\Interview\Interview;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResposibleType extends Model
{
    use HasFactory;
    protected $fillable = [
        'caption',
    ];
    protected $table='responsible_types';
    public function educations() {

        return $this->belongsToMany(Education::class);
    }
}
