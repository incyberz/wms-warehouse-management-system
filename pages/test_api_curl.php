<?php
$kode_po = 'AK202401026';
if(!$kode_po) die(div_alert('danger','Dibutuhkan po_number untuk GET API.'));

$api_url = "http://103.110.9.22:81/apiwhrm/Get_po?po_id=AK202401026";
$api_url = "http://103.110.9.21:81/api/purchasing/purchase-order/get-po-by?number=$kode_po";

$ch = curl_init();
$opts = [
		
    "http" => [
        "method" => "GET",
        "header" => "Accept-language: en\r\n" .
            "Content-Type: application/json\r\n".
            "Accept: application/json\r\n".
            "Authorization: Bearer 1|ew9TqiRCqH3U1QjaBP893g2uBpjWAIntkSWZ0GxH18783f4b\r\n"
		],
		"ssl" => [
			"verify_peer" => false,
			"verify_peer_name" => false,
		],
];
$headers = [
  "Content-Type: application/json",
  "Accept: application/json",
  'Authorization: "Bearer 1|ew9TqiRCqH3U1QjaBP893g2uBpjWAIntkSWZ0GxH18783f4b"'
];

curl_setopt_array($ch,[
  CURLOPT_URL => $api_url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_HTTPHEADER => $headers,
  CURLOPT_TIMEOUT => 5,
  CURLOPT_CONNECTTIMEOUT => 5,

]);

$data = curl_exec($ch);
$status_code = curl_getinfo($ch,CURLINFO_HTTP_CODE);
curl_close($ch);

var_dump($status_code);
var_dump($data);

echo 'OK';
