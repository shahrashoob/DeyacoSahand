<?php

namespace App\Models\Utility;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Verta;

class Utility extends Model
{
    use HasFactory;

    public static function ShamsiToMiladi($shamshi_str = "")
    {

        if ($shamshi_str == "") return null;
        $shamsi = Str::of($shamshi_str)->explode("/");
        if (!isset($shamsi[1]) || !isset($shamsi[2]))
            return null;
        $milidi = Verta::getGregorian($shamsi[0], $shamsi[1], $shamsi[2]);

        return Carbon::create($milidi[0], $milidi[1], $milidi[2]);
    }

    public static function GetRSASignature($data)
    {


        /**
         * Create RSA Signature and Verify it
         *
         * @see http://php.net/manual/ja/function.openssl-sign.php
         *
         * $ openssl genrsa -out privatekey_rsa.pem 2048
         * $ openssl rsa -pubout -in privatekey_rsa.pem -out public_rsa.pem
         */

        // supported algorothms
        //print_r(openssl_get_md_methods(true));

//        $data = "879,09130656899,4420207817";
        $privateKeyId = openssl_pkey_get_private(file_get_contents(storage_path('/app/tara/deyaco.p8.pem')));
        if ($privateKeyId === false) {
            return [
                "result" => false,
                "error" => "کلید خصوصی جهت ساخت امضا نامعتبر است."
            ];
        }

        openssl_sign($data, $signature, $privateKeyId, 'RSA-SHA256');
        openssl_free_key($privateKeyId);
        return [
            "result" => true,
            "signature" => base64_encode($signature),
        ];
//        echo "signature: \n" . base64_encode($signature) . "\n";

        /* تست امضا با کلید عمومی */
        $publicKeyId = openssl_pkey_get_public(file_get_contents(storage_path('/app/tara/deyaco.pubkey.pem')));
        if ($publicKeyId === false) {
            echo "Failed to get the publicKeyId key.";
        }
        $result = openssl_verify($data, $signature, $publicKeyId, 'RSA-SHA256');
        openssl_free_key($publicKeyId);
        if ($result == 1) {
            echo "result: valid\n";
        } elseif ($result == 0) {
            echo "result: invalid\n";
        } else {
            echo "result: error\n";
        }

    }
}
