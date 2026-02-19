<?php

namespace App\Models\Post;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostEntryStatus extends Model
{
    use HasFactory;
    protected $table="post_entry_status";
    protected $fillable=["post_id","status_id","allow_show_all_menu","allow_show_personal_menu","allow_filter_menu"];
    public $timestamps=false;
}
