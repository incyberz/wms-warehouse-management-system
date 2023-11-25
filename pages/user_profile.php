<section id="tokoKami" class="section">
  <div class="section-title">
    <h2>User Profile</h2>
    <p>Halo <?=$nama_user?>! Kamu login sebagai <?=$sebagai?>.</p>
  </div>
  <div>
    <h3>Profile Saya</h3>
    Lorem ipsum dolor sit amet consectetur adipisicing elit. Nulla
    architecto quam voluptatem debitis laudantium enim a corrupti,
    ducimus vel ad asperiores libero illum dolor! Inventore magni ad
    itaque doloremque nisi!
  </div>
  <hr>
  <div>
    <h3>History Checkout</h3>
    <?php
    $s = "SELECT a.*, 
    a.id as id_checkout 
    FROM tb_checkout a WHERE 1";
    $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
    if(mysqli_num_rows($q)==0){
      echo div_alert('info', "Kamu belum pernah checkout.");
    }else{
      $div = '';
      $i=0;
      while($d=mysqli_fetch_assoc($q)){
        $i++;
        $id_checkout=$d['id_checkout'];
        $status = $d['sudah_dibayar'] ? 'Sudah terbayar' : 'Belum dibayar';
        $div .= "
          <div class='wadah'>
            <div>$i.</div>
            <div>Tanggal : $d[tanggal_checkout]</div>
            <div>Nominal : Rp $d[nominal]</div>
            <div>Status : $status</div>
          </div>
        ";
      }
      echo $div;
    } 
    ?>
  </div>

</section>