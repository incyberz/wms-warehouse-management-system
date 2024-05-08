<?php

$s = "SELECT 
a.id,
a.tmp_qty,
a.kode_kumulatif,
(
  SELECT SUM(qty) FROM tb_roll WHERE id_kumulatif=a.id
) sum_roll,
(
  SELECT count(1) FROM tb_roll WHERE id_kumulatif=a.id
) count_roll
FROM tb_sj_kumulatif a 
WHERE 1 
";
$q = mysqli_query($cn, $s) or die(mysqli_error($cn));
$tr = '';
if (mysqli_num_rows($q)) {
  $i = 0;
  $th = '';
  while ($d = mysqli_fetch_assoc($q)) {
    $i++;
    if ($d['tmp_qty'] != $d['sum_roll'] and $d['sum_roll'] and $d['count_roll'] == 1) {
      // update count_roll
      $s2 = "UPDATE tb_roll SET qty=$d[tmp_qty] WHERE id_kumulatif=$d[id]";
      $q2 = mysqli_query($cn, $s2) or die(mysqli_error($cn));

      echo "<br>UPDATING $d[id] - $d[kode_kumulatif] - $d[tmp_qty] == $d[sum_roll] | $d[count_roll]";
    }
    if ($d['tmp_qty'] != $d['sum_roll'] and $d['sum_roll'] and $d['count_roll'] > 1) {
      echo "<br>ROLL LEBIH DARI SATU || $d[id] - $d[kode_kumulatif] - $d[tmp_qty] == $d[sum_roll] | $d[count_roll]";
    }

    // ROLL ZERO
    if ($d['tmp_qty'] != $d['sum_roll'] and !$d['sum_roll'] and $d['count_roll'] > 0) {
      echo "<br>ROLL ZERO || $d[id] - $d[kode_kumulatif] - $d[tmp_qty] == $d[sum_roll] | $d[count_roll]";
    }

    // BERBEDA QTY
    if ($d['tmp_qty'] != $d['sum_roll']) {
      echo "<br>BERBEDA QTY || $d[id] - $d[kode_kumulatif] - $d[tmp_qty] == $d[sum_roll] | $d[count_roll]";
    }
  }
}
