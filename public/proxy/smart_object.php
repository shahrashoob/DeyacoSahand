<?php
$python_host=$_POST["python_host"];
$python_port=$_POST["python_port"];

$host=$_POST["host"];
$port=$_POST["port"];
$smart_id=$_POST["smart_id"];
$url = "$python_host:$python_port/get_smart_object_weight?port=$port&host=$host&smart_id=$smart_id";

$curl = curl_init($url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($curl);
curl_close($curl);
$response= str_replace ( "b'ST,GS,", "", $response);
$response= str_replace ( ",kg", "", $response);
$response= str_replace ( "\\r\\n", "", $response);
echo $response;

?>
