<?php
session_start();
$username = $_SESSION['wms_username'] ?? die('Anda belum login. Tidak dapat mengakses picking list add assign.');
include '../../conn.php';
include '../../data_user.php';
if ($id_role != 7) die('Invalid role at picking list add assign.');
$id_kumulatif = $_GET['id_kumulatif'] ?? die(erid('id_kumulatif'));
$id_do = $_GET['id_do'] ?? die(erid('id_do'));
$is_hutangan = $_GET['is_hutangan'] ?? die(erid('is_hutangan'));

if (!$id_kumulatif) die(erid('id_kumulatif::empty'));
if (!$id_do) die(erid('id_do::empty'));
if (!$is_hutangan) $is_hutangan = 'NULL';

$s = "SELECT 1 FROM tb_pick WHERE id_kumulatif=$id_kumulatif AND id_do='$id_do' ";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
if (mysqli_num_rows($q)) {
  // allow multi ID
  $is_repeat = '1';
} else {
  // first insert
  $is_repeat = 'NULL';
}
$s = "INSERT INTO tb_pick (
  id_kumulatif,
  id_do,
  is_hutangan,
  pick_by,
  is_repeat
) VALUES (
  $id_kumulatif,
  '$id_do',
  $is_hutangan,
  $id_user,
  $is_repeat
)";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
echo 'sukses';
