<?php

namespace App\Models\Utility;

use Illuminate\Database\Eloquent\Model;
use Mpdf\Mpdf;

class Pdf extends Model {
    //
    public static function createAsHtml( $html, $orientation = "L", $filename = "filename", $size = "A4", $text_header = "" ,$printer_file=false ) {
        $mpdfConfig                   = array(
            'mode'          => 'utf-8',
            'format'        => $size,
            'margin_header' => 10,     // 30mm not pixel
            'margin_footer' => 10,     // 10mm
            'orientation'   => $orientation
        );
        $mpdf                         = new \Mpdf\Mpdf( $mpdfConfig );
        $mpdf->defaultheaderfontsize  = 9; /* in pts */
        $mpdf->defaultheaderfontstyle = 'B'; /* blank, B, I, or BI */
        $mpdf->defaultheaderline      = 0; /* 1 to include line below header/above footer */
//
        $company_name = Setting::find( 1 )->string_value;
        $text_header  = $text_header == "" ? $company_name : $text_header;
        $mpdf->SetHeader( "|" . $text_header . "|" );


        $mpdf->defaultfooterfontsize  = 9; /* in pts */
        $mpdf->defaultfooterfontstyle = ''; /* blank, B, I, or BI */
        $mpdf->defaultfooterline      = 0; /* 1 to include line below header/above footer */
//
        $text_footer = "" . verta()->format( ' H:i Y-n-j ' ) . '|صفحه  {PAGENO} از {nb}|' . " ";
        $mpdf->SetFooter( $text_footer ); /* defines footer for Odd and Even Pages - placed at Outer margin */
//        $mpdf->SetHTMLHeader( "" );
//        $mpdf->SetHTMLFooter( '' );
//
//$mpdf->SetWatermarkImage("data:image/png;base64,

        $mpdf->showWatermarkImage = true;

        $mpdf->showImageErrors = true;
        $mpdf->debug           = true;
        if ( is_array( $html ) ) {
            $number = count( $html );
            $k      = 1;
            foreach ( $html as $item ) {
                $mpdf->WriteHTML( $item );
                if ( $k < $number ) {
                    $mpdf->AddPage();
                }
                $k ++;
            }
        } else {
            $mpdf->WriteHTML( $html );
        }


        // return $html;
        if ( $printer_file ) {
            return $mpdf->Output( public_path( "printer_files/" . $printer_file->id . '.pdf' ), \Mpdf\Output\Destination::FILE );
        }

        return $mpdf->Output( $filename . '.pdf', "D" );
    }

    public static function labelPrinter(
        $html, $orientation = "L", $filename = "filename", $size = [
        60,
        87
    ], $printer_file = false
    ) {
        $mpdfConfig = array(
            'mode'          => 'utf-8',
            'format'        => $size,
            'margin_left'   => 1,
            'margin_right'  => 1,
            'margin_top'    => 1,
            'margin_bottom' => 1,
            'margin_header' => 0,
            'margin_footer' => 0,
            'orientation'   => $orientation
        );
        $mpdf       = new \Mpdf\Mpdf( $mpdfConfig );


        $mpdf->showWatermarkImage = false;

        $mpdf->showImageErrors = true;
        $mpdf->debug           = true;

        if ( is_array( $html ) ) {
            $number = count( $html );
            $k      = 1;
            foreach ( $html as $item ) {
                $mpdf->WriteHTML( $item );
                if ( $k < $number ) {
                    $mpdf->AddPage();
                }
                $k ++;
            }
        } else {
            $mpdf->WriteHTML( $html );
        }


        // return $html;

        if ( $printer_file ) {
            return $mpdf->Output( public_path( "printer_files/" . $printer_file->id . '.pdf' ), \Mpdf\Output\Destination::FILE );
        }

        return $mpdf->Output( $filename . '.pdf', "D" );
    }
}
