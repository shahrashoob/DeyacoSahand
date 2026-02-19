<?php

namespace App\Models\Utility\Notification;

use App\Models\Accounting\Client\ClientTransaction;
use App\Models\Accounting\Client\ClientTransactionType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SMSMessageResult extends Model
{
    use HasFactory;
    protected $table="sms_message_results";
    protected $fillable=[
        "messageid",
        "message",
        "status",
        "statustext",
        "sender",
        "receptor",
        "cost",
        "cost_with_coefficient",
        "client_transaction_id",
        'sms_template_id'

    ];
    public function get_created_at()
    {
        return jdate(Carbon::parse($this->created_at)->timestamp)->format('H:i Y/m/d ');
    }
    public function client_transaction()
    {
        return $this->belongsTo(ClientTransaction::class, 'client_transaction_id');
    }
    public function sms_template()
    {
        return $this->belongsTo(SMSTemplate::class, 'sms_template_id');
    }
}
