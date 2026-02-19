<?php

namespace App\Models\Utility\OfficeAutomation;

use App\Models\File\File;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficeAutomationFile extends Model
{
    use HasFactory;
    protected $fillable=["office_automation_work_id","office_automation_to_do_list_id","office_automation_log_id","user_id","file_id"];

    public function file(){
        return $this->belongsTo(File::class);
    }
    public function office_automation_work() {
        return $this->belongsTo( OfficeAutomationWork::class );
    }
    public function office_automation_log() {
        return $this->belongsTo( OfficeAutomationLog::class );
    }
    public function to_do_list() {
        return $this->belongsTo( OfficeAutomationToDoList::class );
    }
}
