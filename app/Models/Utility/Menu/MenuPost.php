<?php

namespace App\Models\Utility\Menu;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuPost extends Model {
    use HasFactory;
    use Loggable;

    protected $table = "menu_post";
    public $timestamps = false;

    public function menu() {
        return $this->belongsTo( Menu::class );
    }
}
