<?php
$hari_ini = date('Y-m-d');
$w = date('w',strtotime($hari_ini));
$ahad_skg = date('Y-m-d',strtotime("-$w day",strtotime($hari_ini)));
$besok = date('Y-m-d H:i',strtotime('+1 day', strtotime('today')));
$kemarin = date('Y-m-d H:i',strtotime('-1 day', strtotime('today')));
$lusa = date('Y-m-d H:i',strtotime('+2 day', strtotime('today')));
$awal_bulan = date('Y-m').'-1';
$awal_tahun = date('Y').'-1-1';

$senin_skg = date('Y-m-d',strtotime("+1 day",strtotime($ahad_skg)));
$selasa_skg = date('Y-m-d',strtotime("+2 day",strtotime($ahad_skg)));
$rabu_skg = date('Y-m-d',strtotime("+3 day",strtotime($ahad_skg)));
$kamis_skg = date('Y-m-d',strtotime("+4 day",strtotime($ahad_skg)));
$jumat_skg = date('Y-m-d',strtotime("+5 day",strtotime($ahad_skg)));
$sabtu_skg = date('Y-m-d',strtotime("+6 day",strtotime($ahad_skg)));
$ahad_depan = date('Y-m-d',strtotime("+7 day",strtotime($ahad_skg)));

$senin_skg_show = 'Senin, '.date('d M Y',strtotime($senin_skg));
$sabtu_skg_show = 'Sabtu, '.date('d M Y',strtotime($sabtu_skg));

function durasi_hari($a,$b){
  if (intval($a) == 0 || intval($b) == 0) {
    return "-";
    
  } 
  $dStart = new DateTime($a);
  $dEnd  = new DateTime($b);
  $dDiff = $dStart->diff($dEnd);
  return $dDiff->format('%r%a'); 
}

$waktu = "Pagi";
if(date("H")>=9) $waktu = "Siang";
if(date("H")>=15) $waktu = "Sore";
if(date("H")>=18) $waktu = "Malam";

$nama_hari = ['Ahad','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
$nama_bulan = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
$hari_ini = $nama_hari[date('w')].', '.date('d').' '.$nama_bulan[intval(date('m'))-1].' '.date('Y');

$now = date('Y-m-d H:i:s');
$today = date('Y-m-d');

$hari_ini_show = $nama_hari[date('w',strtotime('today'))].', '.date('d M Y H:i',strtotime('now'));
