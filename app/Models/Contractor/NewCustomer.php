<?php

namespace App\Models\Customer;

use App\Models\Accounting\Tariff\Tariff;
use App\Models\Utility\Address\Province;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewCustomer extends Model
{
    use HasFactory;
    protected $table="new_customers";
    protected $fillable=[
        "firstname",
        "lastname",
        "code",
        "caption",
        "tariff_id",
        "channel_id",
        "sub_channel_caption",
        "birth_date",
        "gender_id",
        "customer_type_id",
        "economic_number",
        "bail_amount",
        "cash_off_percent",
        "priority_id",
        "register_code",
        "detailed_code",
        "province_id",
        "national_id",
        "national_code",

        "order_permission_type_1",
        "order_permission_type_2",
        "order_permission_type_3",
        "order_permission_type_4",
        "order_permission_type_5",
        "order_permission_type_6",
        "order_permission_type_7",
        "order_permission_type_8",
        "order_permission_type_9",
        "order_permission_type_10",
    ];


    public function channelType()
    {
        return $this->belongsTo(ChannelType::class, "channel_id", "id");
    }

    public function tariff()
    {
        return $this->belongsTo(Tariff::class);
    }


    public function province()
    {
        return $this->belongsTo(Province::class);
    }
}
