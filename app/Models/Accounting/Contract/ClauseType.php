<?php

namespace App\Models\Accounting\Contract;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClauseType extends Model
{
    use HasFactory;
    protected $fillable = [
        'caption',
    ];
    protected $table = 'clause_types';

    public function clause_articles()
    {
        return $this->hasMany(ClauseArticle::class );
    }
}
