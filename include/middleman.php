<?php
# ======================================================
# MIDDLEMAN OF SESSION
# ======================================================
function login_only(){
  if(!isset($_SESSION['lshop_username'])) die('<script>location.replace("?")</script>');
}

function admin_only(){
  if(!isset($_SESSION['lshop_username']) || $_SESSION['lshop_id_role']!=2) die('<script>location.replace("?")</script>');
}

function pelanggan_only(){
  if(!isset($_SESSION['lshop_username']) || $_SESSION['lshop_id_role']!=1) die('<script>location.replace("?")</script>');
}