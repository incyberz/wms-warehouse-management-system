<?php
if(isset($_POST['btn_upload'])){
  echo '<br>Processing upload images...';
  // echo '<pre>';
  // var_dump($_POST);
  // echo '</pre>';

  // echo '<pre>';
  // var_dump($_FILES);
  // echo '</pre>';

  $filePath = "uploads/bbm/$_POST[id_bbm].jpg";
  $tmpName = $_FILES['surat_jalan']['tmp_name'];

  $result = move_uploaded_file($tmpName, $filePath);
  $orig_image = imagecreatefromjpeg($filePath);
  $image_info = getimagesize($filePath); 
  $width_orig  = $image_info[0]; // current width as found in image file
  $height_orig = $image_info[1]; // current height as found in image file
  if($width_orig<100 || $height_orig<100){
    echo 'Resolusi image terlalu kecil. Silahkan pilih gambar lain!';
  }else if($width_orig>1000 || $height_orig>1000){
    if($width_orig>$height_orig){
      $width = 1000;
      $height = round($height_orig*1000/$width_orig,0);
    }else{
      $height = 1000;
      $width = round($width_orig*1000/$height_orig,0);
    }
    echo "<br>Current : $width_orig x $height_orig px";
    echo "<br>Resize to : $width x $height px";

    $destination_image = imagecreatetruecolor($width, $height);
    imagecopyresampled($destination_image, $orig_image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
    // This will just copy the new image over the original at the same filePath.
    imagejpeg($destination_image, $filePath, 50);  
  }else{
    echo '<br>No need to be resized.';
  }

  echo div_alert('success',"Upload sukses.");
  jsurl("?penerimaan&p=terima_barang&kode_po=$_GET[kode_po]&id_bbm=$_GET[id_bbm]");
  exit;
}