<?php
$kode_caption = $p=='user' ? 'Username' : 'Kode';

$hak_akses = [

]; // zzz next project

if(isset($_POST['btn_update'])){
  $pairs = '';
  foreach ($_POST as $key => $value) {
    if($key=="id_$p" || $key=='btn_update') continue;
    $value = $value=='' ? 'NULL' : '\''.clean_sql(strtoupper($value)).'\'';
    $pairs .= "$key = $value, ";
  }

  $id = $_POST["id_$p"];
  $s = "UPDATE tb_$p SET $pairs WHERE id=$id ";
  $s = str_replace(',  WHERE', ' WHERE',$s); // hilangkan koma
  // echo $s;
  $link_back = "<hr><a href='?master&p=$p'>Kembali ke Master $p</a>";

  try {
    $q = mysqli_query($cn,$s) or throw new Exception(mysqli_error($cn));
    echo div_alert('success', "Update success. $link_back");
  } catch (Exception $e) {
    if(strpos("salt$e",'Duplicate entry')){
      echo div_alert('danger',"Input kode sudah ada di database. Silahkan memakai kode unik lainnya. $link_back");
    }else{
      echo div_alert('danger',"Tidak bisa menjalankan Query SQL.");
    }
  }

}else{

  # =========================================
  # DESCRIBE THIS TABLE
  # =========================================
  $s = "DESCRIBE tb_$p";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  $colField = [];
  $colType = [];
  $colLength = [];
  $colNull = [];
  $colKey = [];
  $colDefault = [];
  while($d=mysqli_fetch_assoc($q)){
    array_push($colField, $d['Field']);
    array_push($colNull, $d['Null']);
    array_push($colKey, $d['Key']);
    array_push($colDefault, $d['Default']);
  
    if($d['Type']=='timestamp'){
      $Type = 'timestamp';
      $Length = 19;
    }else{
      $pos = strpos($d['Type'],'(');
      $pos2 = strpos($d['Type'],')');
      $len = strlen($d['Type']);
      $len_type = $len - ($len-$pos);
      $len_length = $len - ($len-$pos2) - $len_type - 1;
    
      $Type = substr($d['Type'],0,$len_type);
      $Length = intval(substr($d['Type'],$pos+1, $len_length));
    }
  
    array_push($colType, $Type);
    array_push($colLength, $Length);
    // echo "<h1>Length : $Length</h1>";
  }
  
  
  # =========================================
  # SELECT MAIN DATA
  # =========================================
  $keyword = $_GET['keyword'] ?? '';
  $sql_keyword = $keyword=='' ? '1' : "(a.kode like '%$keyword%' OR a.nama like '%$keyword%' )";

  $sql_from = "
  FROM tb_$p a 
  WHERE status=1 
  AND $sql_keyword 
  ";

  $s = "SELECT 1 $sql_from ";
  // echo "<pre>$s</pre>";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  $jumlah_row = mysqli_num_rows($q);
  
  $s = "SELECT 
  a.id as id_$p, 
  a.kode as kode_$p, 
  a.nama as nama_$p,
  a.* 
  
  $sql_from 
  ORDER BY a.kode 
  LIMIT 10
  ";
  // echo "<pre>$s</pre>";
  $q = mysqli_query($cn,$s) or die(mysqli_error($cn));
  $jumlah_show = mysqli_num_rows($q);
  
  $tr = '';
  $i = 0;
  $tambah_id = '';
  while($d=mysqli_fetch_assoc($q)){
    $i++;
    $id_p = $d["id_$p"];
  
    # =========================================
    # GET PROPERTIES
    # =========================================
    $td_ket = '';
    $th_ket = '';
    $inputs = '';
    foreach ($colField as $key => $field) {
      if($field=='id'
      ||$field=='kode'
      ||$field=='nama'
      ||$field=='password'
      ||$field=='date_created'
      ||$field=='date_modified'
      ||$field=='status'
      ) continue;
      // echo "<h1>field : $field</h1>";
      $nama_kolom = ucwords(str_replace('_',' ',$field));
      $isi = $d[$field]=='' ? $unset : $d[$field];
      $td_ket .= "<td>$isi</td>";
      $th_ket .= "<th>$nama_kolom</th>";
      
  
      # =========================================
      # FIELD CAN BE NULL
      # =========================================
      $required = $colNull[$key]=='YES' ? '' : 'required';
      $stars = $colNull[$key]=='YES' ? '' : $bintang;
      
      # =========================================
      # KEY FIELD HANDLERS
      # =========================================
      $unik = $colKey[$key]=='UNI' ? $unique : '';
      $link_fkey = '';
      if($colKey[$key]=='MUL'){
        # =========================================
        # CREATE INPUT SELECT
        # =========================================
        if($colField[$key]=='satuan'){
          $s2 = "SELECT satuan FROM tb_satuan";
        }else{
          $arr = explode('_', $colField[$key]);
          $link_fkey = ($id_role!=9 and $p=='user') 
          ? '<span class="kecil miring">hanya admin yang dapat mengubah field ini</span>' 
          : "<a href='?master&p=$arr[1]' onclick='return confirm(\"Menuju manage $arr[1]?\")'>$img_manage</a>";
          $s2 = "SELECT id,nama FROM tb_$arr[1] WHERE status=1";
        }
        // echo "$s2";
        $q2 = mysqli_query($cn,$s2) or die(mysqli_error($cn));
        $opt = '';
        while($d2=mysqli_fetch_assoc($q2)){
          if($colField[$key]=='satuan'){
            $selected = $d2['satuan'] == $d[$colField[$key]] ? 'selected' : '';
            $opt .= "<option value='$d2[satuan]' $selected>$d2[satuan]</option>";
          }else{
            $selected = $d2['id'] == $d[$colField[$key]] ? 'selected' : '';
            $opt .= "<option value='$d2[id]' $selected>$d2[nama]</option>";
          }

        }

        $disabled_change_role = ($id_role!=9 and $p=='user') ? 'disabled' : '';
        $input = "<select class='form-control mb2' name='$field' $disabled_change_role>$opt</select>";
        
      }else{
        # =========================================
        # NORMAL INPUT OR TEXTAREA
        # =========================================
        $param_max = '';
        $param_maxlength = '';
        $param_step = '';
    
        $ftype = $colType[$key];
        if($ftype=='varchar'||$ftype=='char'){
          $type = 'text';
          $param_maxlength = $ftype=='varchar' ? "maxlength='$colLength[$key]' " : "maxlength='$colLength[$key]' minlength='$colLength[$key]' ";
        }elseif($ftype=='int'||$ftype=='smallint'||$ftype=='tinyint'||$ftype=='decimal'){
          $type = 'number';
          if($ftype=='decimal') $param_step = 'step="0.01"';
    
        }elseif($ftype=='timestamp'){
          $type = 'date';
        }else{
          die(div_alert('danger',"Type of field: $ftype belum ditentukan."));
        }
        $params = "
          type='$type'
          class='form-control mb2' 
          name='$field' 
          value='$d[$field]' 
          placeholder='$nama_kolom' 
          $param_max 
          $param_maxlength 
          $param_step 
          $required
        ";
    
        if($colLength[$key]>100){
          $input = "<textarea $params>$d[$field]</textarea>";
        }else{
          $input = "<input $params>";
        }
    
        
      }
  
      # =========================================
      # FINAL OUTPUT OF INPUT | TEXTAREA | SELECT
      # =========================================
      $inputs .= "
      <div class='mb1 darkabu '>$nama_kolom $stars $unik $link_fkey</div>
      $input
      ";
    }
  
  
    $kode = $d["kode_$p"];
    $nama = $d["nama_$p"];
  
    $dual_id = $p."__$id_p";
    $toggle_id = 'edit_'.$p.'__toggle__'.$id_p;
    $delete_id = 'edit_'.$p.'__delete__'.$id_p.'__from_db';
    $close_id = 'edit_'.$p.'__close__'.$id_p;
    $cancel_id = 'edit_'.$p.'__cancel__'.$id_p;
    $tambah_id = $p.'__tambah';
  
    $is_new = strpos("salt$kode",'NEW');
    $hideit_blok_edit = $is_new ? '' : 'hideit';
    $gradasi_item = $is_new ? 'gradasi-kuning red bold' : '';

    $login_as = $id_role==9 ? "<a href='?login_as&id_user=$id_p'>$img_login_as</a> " : '';

    // only admin yang dapat edit / delete role
    $edit_delete = ($id_role!=9 and ($p=='role' || $p=='user')) ? '' : "
      <span class='btn_aksi' id=$toggle_id>$img_edit</span> 
      <span class='btn_aksi' id=$delete_id>$img_delete</span>
    ";

    // jika hak akses dibatasi
    if($locked_icon) $edit_delete = $edit_delete ? $locked_icon : '';

    // boleh edit user sendiri
    $edit_delete = ($username == $d["kode_$p"]) ? "<span class='btn_aksi' id=$toggle_id>$img_edit</span>" : $edit_delete;

    // null to dash
    $edit_delete = $edit_delete ? $edit_delete : '-';
    

    # ==============================================================
    # FINAL OUTPUT TR 
    # ==============================================================
    $tr .= "
      <tr id=source_edit_$dual_id class='$gradasi_item'>
        <td>
          $edit_delete
        </td>
        <td class='abu miring f12'><div style='padding-top:3px'>$i</div></td>
        <td>$kode</td>
        <td>
          $nama 
          $login_as 
          <span class='btn_aksi' id='ket_detail__toggle__$id_p'>$img_detail</span>
          <div class='kecil ket hideit' id='ket_detail__$id_p'>
            <ul class='p0 pl3'></ul>
          </div>
        </td>
        $td_ket
      </tr>
      <tr class=$hideit_blok_edit id=edit_$dual_id>
        <td colspan=100%>
          <form method=post class=formulir style='max-width:70vw'>
            <input name=id_$p value=$id_p type=hidden>
            <div class='p2 br5 mb4 border-merah gradasi-kuning'>
              <div class='flex-between mb1'>
                <div class='tebal abu proper'>Edit Data $p</div>
                <div>
                  <span class='btn_aksi' id='$close_id'>$img_close</span>
                </div>
              </div>  
  
              <div class='mb1 proper'>$kode_caption $p $bintang $unique</div>
              <input name=kode class='form-control mb2' value='$kode' minlength=3 maxlength=20>
              <div class='mb1 proper'>Nama $p $bintang</div>
              <input name=nama class='form-control mb2' value='$nama' minlength=3 maxlength=50>
              
              $inputs
  
              <div class=' '>
                <button class='btn btn-success btn-sm' name=btn_update>Update</button>
                <span class='btn btn-danger btn-sm btn_aksi' id=$cancel_id>Cancel</span>
              </div>
            </div>
          </form>
  
        </td>
      </tr>
    ";
    if($is_new) break;
  }

  if($keyword==''){
    $info = "Belum ada data $p pada database.";
    $clear = '';
    $gradasi = '';
  }else{
    $info = "Data $p dengan <b>filter: $keyword</b> tidak ditemukan. | <a href='?master&p=$p'>Clear Filter</a>";
    $gradasi = 'gradasi-kuning';
    $clear = "<div class=kecil><a href='?master&p=$p'>Clear</a></div>";
  }


  $btn_tambah = ($id_role!=9 and ($p=='user'||$p=='role')) ? '<span class="miring abu">non-admin users</span>' : "<a class='btn btn-success btn-sm btn_aksi' id=$tambah_id>Tambah</a>";
  
  echo $tr=='' ? div_alert('danger mt-4', $info) : "
    <div class='kanan'>
      $btn_tambah
    </div>
    <div class=flexy>
      <div class='kecil miring abu mb1 ml1'><span class='f20 darkblue'>$jumlah_row</span> records found</div>
      <div>
        <input class='form-control form-control-sm $gradasi' placeholder='Cari ...' id=input_cari value='$keyword'>
      </div>
      $clear
    </div>
    <div style='background:linear-gradient(#fff,#eee); padding:5px;overflow:scroll; max-height:70vh'>
      <table class='table table-hover f13'>
        <thead class='upper gradasi-toska'>
          <th colspan=2><div><img src='#' width=80px height=1px/></div>NO</th>
          <th>$kode_caption</th>
          <th>$p</th>
          $th_ket
        </thead>
        $tr
      </table>
    </div>
  ";
}


















?><script>
  $(function(){
    let p = $('#p').text();
    $('#input_cari').keyup(function(e){
      let keyword = $(this).val();
      let href = keyword.length>2 ? `?master&p=${p}&keyword=${keyword}` : '';

      if(e.keyCode == 13 && keyword.length>2){
        // alert('Enter')
        location.replace(`?master&p=${p}&keyword=${keyword}`);
      }
    })
  })
</script>