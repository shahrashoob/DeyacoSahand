<?php

namespace App\Models\HR\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class UserEntryImage extends Model
{
    use HasFactory;
    protected $table ="entry_user_images";
    protected $fillable = [
        'user_id',
        'name',
        'path',
        'trained',
        'trained_at'
    ];
    protected $casts = [
        'trained' => 'boolean',
        'trained_at' => 'datetime',
    ];

    // رابطه با کاربر
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
