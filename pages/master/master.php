<?php $p = $_GET['p'] ?? jsurl('?'); ?>
<section class='section'>
  <div class="section-title">
    <!-- <h2 class='proper'>Master <?=$p?></h2> -->
    <div class="flexy proper">
      <div class='miring abu'>Masters: </div>
      <?php foreach ($arr_master as $master) echo $master==$p ? "<div class=tebal id=p>$p</div>" : "<div><a href='?master&p=$master'>$master</a></div>"; ?>
    </div>
  </div>
  <div id="blok_<?=$p?>">
    <?php 
      include "master-detail.php"; 
    ?>
  </div>
</section>