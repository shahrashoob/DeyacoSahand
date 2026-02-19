<?php

namespace App\Models\Utility\Financial;

use App\Models\Form\Form;
use App\Models\Utility\Message;
use App\Models\Utility\Status;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialSoftwareTransKindLog extends Model {
    use HasFactory;

    protected $table = "financial_software_trans_kind_log";
    protected $fillable = [
        "user_id",
        "message_id",
        "form_id",
        "status_id",
        "financial_software_transfer_form_id",
        "financial_software_trans_kind_type_id"
    ];

//    public function financial_software(){
//        return $this->belongsTo(FinancialSoftware::class);
//    }
    public function financial_software_transfer_form(){
        return $this->belongsTo(FinancialSoftwareTransferForm::class);
    }
    public function form(){
        return $this->belongsTo(Form::class);
    }
    public function worker(){
        return $this->belongsTo(Worker::class,"user_id");
    }
    public function message(){
        return $this->belongsTo(Message::class);
    }
    public function status(){
        return $this->belongsTo(Status::class,);
    }
    public function create_datetime() {
        return jdate( Carbon::parse( $this->created_at )->timestamp )->format( 'H:i Y/m/d ' );

    }

}
