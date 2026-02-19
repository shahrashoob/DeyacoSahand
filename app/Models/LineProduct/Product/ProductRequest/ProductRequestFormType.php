<?php

namespace App\Models\LineProduct\Product\ProductRequest;

use App\Models\Form\Form;
use App\Models\Utility\Event;
use App\Models\Utility\JsonDataList;
use App\Models\Utility\Message;
use App\Models\Utility\Status;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductRequestFormType extends Model{
    protected $table='product_request_form_types';
}