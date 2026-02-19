<?php

namespace App\Models\Accounting\WelfareService;

use App\Models\Accounting\FinancialOperation\FinancialOperationPatternItem;
use App\Models\Utility\Status;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WelfareService extends Model
{

    use HasFactory;

    protected $table = "welfare_services";
    protected $fillable = ["user_id", "mobile", "national_code", "account_number"];

    public function worker()
    {
        return $this->belongsTo(Worker::class, "user_id");
    }

    public function create_datetime()
    {
        return jdate(Carbon::parse($this->created_at)->timestamp)->format('Y/m/d H:i:s');

    }
}
