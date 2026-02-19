<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Accounting\Tariff\ProductTariffLog;
use App\Models\Accounting\Tariff\TariffLog;
use App\Models\Form\Form;
use App\Models\Form\FormItem;
use App\Models\LineProduct\Degree;
use App\Models\Order\Order;
use App\Models\Order\OrderFactor;
use App\Models\Order\OrderListPackingType;
use App\Models\Utility\Pdf;
use App\Models\Utility\Setting;
use App\Models\Warehouse\WarehouseProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class PrintController extends Controller {
    //

    public function factor( Order $order ) {

        return \App\Http\Controllers\Customer\PrintController::getFactor( $order );
    }

    public function register_xml_download( Order $order ) {
        $this->permission();

        $xml = "<?xml version='1.0' encoding='UTF-8' standalone='yes'?>";
        $xml .= view( "sales.print.order_xml", compact( "order" ) )->render();


        $response = Response::make( $xml, 200 );
        $response->header( 'Content-Type', 'text/xml' );
        $response->header( 'Cache-Control', 'public' );
        $response->header( 'Content-Description', 'File Transfer' );
        $response->header( 'Content-Disposition', 'attachment; filename=' . "order_" . $order->code() . '.xml' );
        $response->header( 'Content-Transfer-Encoding', 'binary' );

        return $response;

    }

    public function exit_form_factor( Order $order, Form $form ) {

        // بررسی اینکه فرم درخواست کالا تراکنش انبار نداشته باشد

        if ( ! $form->hasWarehouseTransaction() ) {
            return back()->withErrors( "با توجه به اینکه تراکنش های انبار برای  برگ خروج  " . $form->getCode() . " ثبت نشده است، امکان دریافت فاکتور وجود ندارد. " );
        }
        $result =Order::GetFactorFromExitFrom( $order, $form );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }
        $text_footer_per_factor = $result["text_footer_per_factor"];
        $seller                 = $result["seller"];
        $national_code          = $result["national_code"];
        $economic_number        = $result["economic_number"];
        $form_factor            = $result["form_factor"];


        $setting_data = Setting::getIntegerValueList([
            "show_product_caption_in_pre_factor",
            "show_packing_type_caption_in_pre_factor",
            "show_packing_type_code_in_pre_factor",
            "show_property_1_in_pre_factor",
            "show_property_2_in_pre_factor",
            "show_property_3_in_pre_factor",
        ]);

        $col_span_count=array_sum($setting_data);
        if($setting_data["show_product_caption_in_pre_factor"] == 1 && $setting_data["show_packing_type_caption_in_pre_factor"] == 1){
            $col_span_count--; // چون این دو مقدار را در یک ستون نمایش می دهیم.
        } // گرفتن مشخصه های اصلی در رسته کالایی اولین آیتم سفارش
        $goods_kind = $order->orderList()->first()->product->goods_kind ?? null;
        $property[1] = $goods_kind->property_1 ?? null;
        $property[2] = $goods_kind->property_2 ?? null;
        $property[3] = $goods_kind->property_3 ?? null;
// گروه بندی به تفکیک بسته بندی و درجه


        $html = view( "customer.print.exit_form_factor", compact( "col_span_count","property","setting_data","order", "form", "text_footer_per_factor", "seller", "national_code", "economic_number", "form_factor" ) )->render();

        return Pdf::createAsHtml( $html, "L", $order->code(), "A4", " " );

    }

    public function exit_form_pre_factor( Order $order, Form $form ) {


         $result =Order::GetFactorFromExitFrom( $order, $form );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }


        $text_footer_per_factor = $result["text_footer_per_factor"];
        $seller                 = $result["seller"];
        $national_code          = $result["national_code"];
        $economic_number        = $result["economic_number"];
        $form_factor            = $result["form_factor"];

        $setting_data = Setting::getIntegerValueList([
            "show_product_caption_in_pre_factor",
            "show_packing_type_caption_in_pre_factor",
            "show_packing_type_code_in_pre_factor",
            "show_property_1_in_pre_factor",
            "show_property_2_in_pre_factor",
            "show_property_3_in_pre_factor",
        ]);

        $col_span_count=array_sum($setting_data);
        if($setting_data["show_product_caption_in_pre_factor"] == 1 && $setting_data["show_packing_type_caption_in_pre_factor"] == 1){
            $col_span_count--; // چون این دو مقدار را در یک ستون نمایش می دهیم.
        } // گرفتن مشخصه های اصلی در رسته کالایی اولین آیتم سفارش
        $goods_kind = $order->orderList()->first()->product->goods_kind ?? null;
        $property[1] = $goods_kind->property_1 ?? null;
        $property[2] = $goods_kind->property_2 ?? null;
        $property[3] = $goods_kind->property_3 ?? null;

        $html = view( "customer.print.exit_form_pre_factor", compact( "col_span_count","property","setting_data","order", "form", "text_footer_per_factor", "seller", "national_code", "economic_number", "form_factor" ) )->render();

        return Pdf::createAsHtml( $html, "L", $order->code(), "A4", " " );

    }


    private function permission() {
        $post_user = \Auth::user()->posts->first();
        if ( ! $post_user->checkButtonPermission( "sales.register_xml" ) ) {
            return back()->withErrors( "شما اجازه دسترسی به صفحه را ندارید" );
        }
    }

}
