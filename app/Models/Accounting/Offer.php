<?php

namespace App\Models\Accounting;

use App\Models\Customer\ChannelType;
use App\Models\Customer\Customer;
use App\Models\LineProduct\Degree;
use App\Models\LineProduct\Product;
use Carbon\Carbon;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model {
    use HasFactory;
    use Loggable;

    public static $OffDefaultFea = 1;
    protected $fillable = [
        "product_id",
        "degree_id",
        "min_buy",
        "max_buy",
        "start_datetime",
        "end_datetime",
        "channel_type_id",
        "customer_id",
        "percent_free",
        "product_free_id",
        "degree_free_id",
        "percent_off",
        "status_id",
        "offer_type_id",
        "free_count"
    ];

    public function product() {
        return $this->belongsTo( Product::class );
    }

    public function degree() {
        return $this->belongsTo( Degree::class );
    }

    public function product_free() {
        return $this->belongsTo( Product::class, "product_free_id", "id" );
    }

    public function degree_free() {
        return $this->belongsTo( Degree::class, "degree_free_id", "id" );
    }

    public function customer() {
        return $this->belongsTo( Customer::class );
    }

    public function channel_type() {
        return $this->belongsTo( ChannelType::class );
    }

    public function get_start_datetime() {
        return jdate( Carbon::parse( $this->start_datetime )->timestamp )->format( 'Y/m/d ' );

    }

    public function get_end_datetime() {
        return jdate( Carbon::parse( $this->end_datetime )->timestamp )->format( 'Y/m/d ' );

    }

    public static function checkForNewItem( $offer ) {

        $start_datetime = $offer->start_datetime;
        $end_datetime   = $offer->end_datetime;
        $min_buy        = $offer->min_buy;
        $max_buy        = $offer->max_buy;
        $diff_offers    = Offer::
        where( "product_id", $offer->product_id )->
        where( "status_id", 522000200 )->
        where( "offer_type_id", $offer->offer_type_id )->
        where( function ( $query ) use ( $start_datetime, $end_datetime ) {
            $query->Where( function ( $query ) use ( $start_datetime ) {
                $query->where( 'end_datetime', '>=', $start_datetime );
                $query->where( 'start_datetime', '<=', $start_datetime );
            } );
            $query->orWhere( function ( $query ) use ( $end_datetime ) {
                $query->where( 'end_datetime', '>=', $end_datetime );
                $query->Where( 'start_datetime', '<=', $end_datetime );
            } );

            $query->orWhere( function ( $query ) use ( $start_datetime ) {
                $query->Where( 'start_datetime', '<', $start_datetime );
                $query->where( 'end_datetime', '>', $start_datetime );
            } );

        } )->
        where( function ( $query ) use ( $min_buy, $max_buy ) {

            $query->Where( function ( $query ) use ( $min_buy ) {
                $query->where( 'max_buy', '>=', $min_buy );
                $query->Where( 'min_buy', '<=', $min_buy );
            } );

            $query->orWhere( function ( $query ) use ( $max_buy ) {
                $query->where( 'min_buy', '>=', $max_buy );
                $query->Where( 'max_buy', '<=', $max_buy );
            } );

            $query->orWhere( function ( $query ) use ( $min_buy ) {
                $query->where( 'min_buy', '<', $min_buy );
                $query->Where( 'max_buy', '>', $min_buy );
            } );

        } );

        if ( $offer->offer_type_id == 500 ) {
            $diff_offers = $diff_offers->where( "channel_type_id", $offer->channel_type_id );
        }
        if ( $offer->offer_type_id == 510 ) {
            $diff_offers = $diff_offers->where( "customer_id", $offer->customer_id );
        }

        return $diff_offers->first();
    }
}
