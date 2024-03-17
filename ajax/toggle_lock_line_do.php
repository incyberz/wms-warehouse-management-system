<?php
include 'harus_login.php';
include '../include/crud_icons.php';
ONLY('PPIC');

$id_pick = $_GET['id_pick'] ?? die(erid('id_pick'));
if (!$id_pick) die(erid('id_pick::empty'));
$aksi = $_GET['aksi'] ?? die(erid('aksi'));
if (!$aksi) die(erid('aksi::empty'));

$boleh_allocate = $aksi == 'unlock' ? 1 : 'NULL';

$s = "UPDATE tb_pick SET boleh_allocate=$boleh_allocate WHERE id=$id_pick";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
echo 'sukses';
