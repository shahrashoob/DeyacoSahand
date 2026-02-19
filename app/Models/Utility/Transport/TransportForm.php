<?php

namespace App\Models\Utility\Transport;

use App\Models\Form\Form;
use App\Models\Form\Packing\PackingFormItem;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestForm;
use App\Models\Utility\Status;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TransportForm extends Model {
    use HasFactory;

    protected $table = "transport_form";
    protected $fillable = [ "transport_id", "form_id" ];

    public function transport() {
        return $this->belongsTo( Transport::class );
    }

    public function form() {
        return $this->belongsTo( Form::class );
    }
}
