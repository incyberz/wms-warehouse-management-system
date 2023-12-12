<?php
if(isset($_POST['btn_upload_profile'])){
  echo '<br>Processing upload images...';

  $filePath = "assets/img/user/$id_user.jpg";
  $tmpName = $_FILES['profile']['tmp_name'];
  $max_res = 100;
  $min_res = 10;

  $result = move_uploaded_file($tmpName, $filePath);
  $orig_image = imagecreatefromjpeg($filePath);
  $image_info = getimagesize($filePath); 
  $width_orig  = $image_info[0]; // current width as found in image file
  $height_orig = $image_info[1]; // current height as found in image file
  if($width_orig<$min_res || $height_orig<$min_res){
    echo 'Resolusi image terlalu kecil. Silahkan pilih gambar lain!';
  }else if($width_orig>$max_res || $height_orig>$max_res){
    if($width_orig>$height_orig){
      $width = $max_res;
      $height = round($height_orig*$max_res/$width_orig,0);
    }else{
      $height = $max_res;
      $width = round($width_orig*$max_res/$height_orig,0);
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
  // jsurl("?my_profile");
  exit;
}