<?php
$api_url = "http://103.110.9.22:81/apiwhrm/Get_po?po_id=AK202401026z";

// Read JSON file
$json_data = file_get_contents($api_url);

// Decode JSON data into PHP array
$response_data = json_decode($json_data);

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

?>