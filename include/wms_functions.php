<?php
function locked_icon($id_role,$arr_allowed = [],$sebagai = ''){
  if($arr_allowed){
    if(in_array($id_role,$arr_allowed)){
      $locked_icon = '';
    }else{
      $sebagai = $sebagai ? "(sebagai $sebagai)" : '';
      $locked_icon = "<span onclick='alert(\"Pada lama ini terdapat fitur-fitur yang dikunci dan disesuaikan dengan hak akses Anda $sebagai. \")'><img class='zoom pointer' src='assets/img/icons/locked.png' alt='locked' height=20px></span>";
    }
  }else{
    // no locked icon
    $locked_icon = '';
  }
  return $locked_icon;
}