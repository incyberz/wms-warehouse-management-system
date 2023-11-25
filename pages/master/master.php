<?php
$p = $_GET['p'] ?? jsurl('?');
?>
<section class='section'>
  <div class="section-title">
    <h2 class='proper'>Master <?=$p?></h2>
    <!-- <p>Silahkan Register untuk melanjutkan berbelanja!</p> -->
  </div>
  <div id="blok_<?=$p?>">
    <?php include "master-$p.php"; ?>
  </div>
</section>