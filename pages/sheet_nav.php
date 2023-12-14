<?php 
$aks_fab = $cat=='aks' 
? "<a href='?$parameter&cat=fab'>FAB</a> | <b class='darkblue f18'>AKS</b>"
: "<a href='?$parameter&cat=aks'>AKS</a> | <b class='darkblue f18'>FAB</b>"
;
?>

<div class="section-title">
  <div class="flexy proper">
    <div class='kecil abu'><?=$aks_fab?></div>
    <?php 
    foreach ($arr_sheet as $sheet){
      $href = strtolower(str_replace(' ','_',$sheet));
      echo $href==$p ? "<div class='tebal f18 darkblue' id=p>$sheet</div>" : "<div class=pt1><a href='?$href' class=kecil>$sheet</a></div>"; 
    } 
    ?>
  </div>
</div>