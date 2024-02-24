<h1>Super Delete Penerimaan PO</h1>
<form action="" method="post">
  <input type="text" class="form-control" name=kode_sj_for_delete>
  <button class="btn btn-primary">Cari Kode Surat Jalan</button>
</form>
<?php
if(isset($_POST['kode_sj_for_delete'])){
  $s = "SELECT kode as kode_sj FROM tb_sj WHERE kode like '%$_POST[kode_sj_for_delete]%'";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  $count_row = mysqli_num_rows($q);
  echo "<div>Count Row : $count_row</div>";
  while($d=mysqli_fetch_assoc($q)){
    $kode_sj=$d['kode_sj'];

    $s2 = "DELETE FROM tb_bbm WHERE kode_sj='$kode_sj'";
    echo "<br>deleting... $s2";
    $q2 = mysqli_query($cn,$s2) or die(mysqli_error($cn));
    
    $s2 = "SELECT id as id_sj_item FROM tb_sj_item WHERE kode_sj='$kode_sj'";
    echo "<hr>looping... $s2";
    $q2 = mysqli_query($cn,$s2) or die(mysqli_error($cn));
    while($d2=mysqli_fetch_assoc($q2)){
      $id_sj_item=$d2['id_sj_item'];
      
      $s3 = "SELECT id as id_sj_kumulatif FROM tb_sj_kumulatif WHERE id_sj_item='$id_sj_item'";
      echo "<hr>looping... $s3";
      $q3 = mysqli_query($cn,$s3) or die(mysqli_error($cn));
      while($d3=mysqli_fetch_assoc($q3)){
        $id_sj_kumulatif=$d3['id_sj_kumulatif'];
        
        $s4 = "DELETE FROM tb_picking WHERE id_sj_kumulatif='$id_sj_kumulatif'";
        echo "<br>deleting... $s4";
        $q4 = mysqli_query($cn,$s4) or die(mysqli_error($cn));
        
        $s4 = "SELECT id as id_retur FROM tb_retur WHERE id='$id_sj_kumulatif'";
        echo "<hr>looping... $s4";
        $q4 = mysqli_query($cn,$s4) or die(mysqli_error($cn));
        while($d4=mysqli_fetch_assoc($q4)){
          $id_retur=$d4['id_retur'];
          $s5 = "DELETE FROM tb_terima_retur WHERE id='$id_retur'";
          echo "<br>deleting... $s5";
          $q5 = mysqli_query($cn,$s5) or die(mysqli_error($cn));
        }


        $s4 = "DELETE FROM tb_retur WHERE id='$id_sj_kumulatif'";
        echo "<br>deleting... $s4";
        $q4 = mysqli_query($cn,$s4) or die(mysqli_error($cn));

        $s4 = "DELETE FROM tb_roll WHERE id_sj_kumulatif='$id_sj_kumulatif'";
        echo "<br>deleting... $s4";
        $q4 = mysqli_query($cn,$s4) or die(mysqli_error($cn));
      }

      // $s3 = "SELECT id as id_sj_kumulatif FROM tb_sj_kumulatif WHERE id_sj_item='$id_sj_item'";
      // echo "<hr>looping... $s3";
      // $q3 = mysqli_query($cn,$s3) or die(mysqli_error($cn));
      // while($d3=mysqli_fetch_assoc($q3)){}      

      $s3 = "DELETE FROM tb_sj_kumulatif WHERE id_sj_item='$id_sj_item'";
      echo "<br>deleting... $s3";
      $q3 = mysqli_query($cn,$s3) or die(mysqli_error($cn));

    }


    $s2 = "DELETE FROM tb_sj_item WHERE kode_sj='$kode_sj'";
    echo "<br>deleting... $s2";
    $q2 = mysqli_query($cn,$s2) or die(mysqli_error($cn));


    // final delete
    $s2 = "DELETE FROM tb_sj WHERE kode='$kode_sj'";
    echo "<br>FINAL deleting... $s2";
    $q2 = mysqli_query($cn,$s2) or die(mysqli_error($cn));
  }

}
