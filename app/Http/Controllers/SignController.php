<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;

class SignController extends Controller
{
    public function signValue(Request $request)
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

        $data = "879,09130656899,4420207817";
        $privateKeyId = openssl_pkey_get_private(file_get_contents(storage_path('/app/tara/deyaco.p8.pem')));
        if ($privateKeyId === false) {
            echo "Failed to get the private key.";
        }

        openssl_sign($data, $signature, $privateKeyId, 'RSA-SHA256');
        openssl_free_key($privateKeyId);
        echo "signature: \n" . base64_encode($signature) . "\n";

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
/*
 *
 * How to Use
You can use a tool like Postman or cURL to send a POST request to the /sign endpoint:

Example cURL Command
bash
curl -X POST http://your-app-url/sign \
-H "Content-Type: application/json" \
-d '{
    "input": "This is the message to sign",
    "private_key": "-----BEGIN PRIVATE KEY-----\nMIG..."
}'
Explanation of the Laravel Code
Validation:
The request is validated to ensure that both the input message and the private key are provided.

generateSignature Method:

Uses readPrivateKey to decode the private key and then signs the message with openssl_sign.
The signature is returned in Base64 format.
readPrivateKey Method:

Removes unnecessary headers and footers and decodes the Base64 string to create a usable private key.
Error Handling:
Exceptions are caught, and appropriate error messages are returned to the client.

This implementation allows you to effectively sign messages with RSA in a Laravel application, closely reflecting the original Java logic you provided. Just be cautious of your private key management and security practices.
 */