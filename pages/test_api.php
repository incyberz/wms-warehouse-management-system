<?php
// echo date('Y-m-d',strtotime('2024-01-29T00:00:00.000000Z'));
// exit;
$api_url = "http://103.110.9.22:81/apiwhrm/Get_po?po_id=AK202401026";
$api_url = "http://103.110.9.21:81/api/purchasing/purchase-order/get-po-by?number=AK202401026";

// Create a stream
$opts = [
    "http" => [
        "method" => "GET",
        "header" => "Accept-language: en\r\n" .
            "Content-Type: application/json\r\n".
            "Accept: application/json\r\n".
            "Authorization: Bearer 1|ew9TqiRCqH3U1QjaBP893g2uBpjWAIntkSWZ0GxH18783f4b\r\n"
    ]
];

// DOCS: https://www.php.net/manual/en/function.stream-context-create.php
$context = stream_context_create($opts);

// Open the file using the HTTP headers set above
// DOCS: https://www.php.net/manual/en/function.file-get-contents.php
// $file = file_get_contents('http://www.example.com/', false, $context);

// Read JSON file
$json_data = file_get_contents($api_url,false,$context);

// Decode JSON data into PHP array
$response_data = json_decode($json_data);

if($response_data){
	// All item data exists in 'data' object
	$item_data = $response_data->data;
	
	// echo '<pre>';
	// var_dump($item_data);
	// echo '</pre>';
	
	// Traverse array and display item data
	foreach ($item_data as $item) {
		echo '<br/>po_number : '.$item->po_number;
	}
	
	echo '<hr/>count: '.count($item_data);

}else{
	echo 'No response from API.';
}

?>