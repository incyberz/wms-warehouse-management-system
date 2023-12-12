<div class="section-title">
  <!-- <h2 class='proper'>Master <?=$p?></h2> -->
  <div class="flexy proper">
    <div class='miring abu'>Sheets: </div>
    <?php 
    foreach ($arr_sheet as $sheet){
      $href = strtolower(str_replace(' ','_',$sheet));
      echo $href==$p ? "<div class=tebal id=p>$p</div>" : "<div><a href='?$href'>$sheet</a></div>"; 
    } 
    ?>
  </div>
</div>