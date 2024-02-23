<?php
// $kode_po = 'AK202401026';
if(!$kode_po) die(div_alert('danger','Dibutuhkan po_number untuk GET API.'));

$api_url = "http://103.110.9.22:81/apiwhrm/Get_po?po_id=AK202401026";
$api_url = "http://103.110.9.21:81/api/purchasing/purchase-order/get-po-by?number=$kode_po";

// Create a stream
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

// DOCS: https://www.php.net/manual/en/function.stream-context-create.php
$context = stream_context_create($opts);


// Read JSON file
$json_data = file_get_contents($api_url,false,$context);

echo '<pre>';
var_dump($json_data);
echo '</pre>';

// Decode JSON data into PHP array
$response_data = json_decode($json_data);

if($response_data){
	// All item data exists in 'data' object
	$arr_item_po = $response_data->data;
	
	// echo '<pre>';
	// var_dump($arr_item_po);
	// echo '</pre>';
	
	// Traverse array and display item data
	// foreach ($arr_item_po as $item) {
	// 	echo '<br/>po_number : '.$item->po_number;
	// }
	
	$debug .= "<br>PO Item Count FROM API | arr_item_po : ".count($arr_item_po);
}else{
	echo 'No data response from API.';
}


?>