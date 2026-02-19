<?php

namespace App\Http\Controllers\Utility\Printer;

use App\Http\Controllers\Controller;
use App\Models\Utility\Printer\Printer;
use App\Models\Utility\Printer\PrinterFile;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Livewire\Response;

class PrinterAPIController extends Controller {
    //

    public function getPrinterWidth( $printer_id ) {

        $printer = Printer::find( $printer_id );
        if ( ! $printer ) {
            return - 1;
        }

        return $printer->width;
    }

    public function getPrinterHeight( $printer_id ) {

        $printer = Printer::find( $printer_id );
        if ( ! $printer ) {
            return - 1;
        }

        return $printer->height;
    }

    public function getLatestId( $password, $printer_id ) {

        $printer = Printer::find( $printer_id );
        if ( ! $printer || $printer->password != $password ) {
            return - 1;
        }
        $last_check_id = 0;
        while ( 1 ) {
            $printer_file = PrinterFile::
            where( [ "status_id" => 305001, "printer_id" => $printer_id ] )->
            where( "number_of_prints", ">", 0 )->
            when( $last_check_id > 0, function ( $query ) use ( $last_check_id ) {
                return $query->where( "id", ">", $last_check_id );
            } )->
            orderBy( "number_of_prints" )->
            orderBy( "id" )->
            first();

            if ( ! $printer_file ) {
                break;
            }
            $last_check_id = $printer_file->id;
            if ( $printer_file && File::exists( public_path() . '/printer_files/' . ( $printer_file->id ?? 0 ) . '.pdf' ) ) {
                return $printer_file->id;
            }
        }

        $removed_print = PrinterFile::
        where( [ "status_id" => 305001, ] )->
        where( "created_at", "<", Carbon::now()->addDay( - 3 ) )->
        first();
        if ( $removed_print ) {
            $removed_print->delete();
        }


        $printer_file = PrinterFile::where( "status_id", "!=", 305001 )->
        where( "created_at", "<", Carbon::now()->addDay( - 1 ) )->first();
        if ( File::exists( public_path() . '/printer_files/' . ( $printer_file->id ?? 0 ) . '.pdf' ) ) {
            File::delete( public_path() . '/printer_files/' . ( $printer_file->id ?? 0 ) . '.pdf' );
        }
        if ( $printer_file ) {
            $printer_file->delete();
        }

        return 0;

    }

    public function getPDFFile( $password, $id ) {

        $printer_file = PrinterFile::find( $id );
        if (!$printer_file || $printer_file->printer->password != $password ) {
            return false;
        }
        $printer_file->number_of_prints = $printer_file->number_of_prints - 1;
        $printer_file->status_id        = 305003;
        if ( $printer_file->number_of_prints <= 0 ) {
            // no thing
        } else {
            $new_printer_file = PrinterFile::create( [
                "user_id"          => $printer_file->user_id,
                "filename"         => $printer_file->filename,
                "status_id"        => 305001,
                "is_landscape"     => $printer_file->is_landscape,
                "printer_id"       => $printer_file->printer_id,
                "number_of_prints" => $printer_file->number_of_prints,
            ] );

            File::copy( public_path( "printer_files/" . $printer_file->id . '.pdf' ), public_path( "printer_files/" . $new_printer_file->id . '.pdf' ) );
        }
        $printer_file->save();

        $header = [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $printer_file->id . '.pdf' . '"'
        ];

        return response()->file( public_path( "printer_files/" . $printer_file->id . '.pdf' ), $header );

    }

    public function PrintOK( $password, $id ) {

        $printer_file = PrinterFile::find( $id );
        if ( $printer_file->printer->password != $password ) {
            return false;
        }

        $printer_file                   = PrinterFile::find( $id );
        $printer_file->number_of_prints = $printer_file->number_of_prints - 1;
//        if ( $printer_file->number_of_prints <= 0 ) {
//            $printer_file->status_id = 305003;
//        }
//        else{
//            $printer_file->status_id = 305001;
//        }
        $printer_file->save();

        return $printer_file->is_landscape;

    }

}
