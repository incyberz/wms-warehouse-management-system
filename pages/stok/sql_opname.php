<?php
# ==========================================
# INCLUDE PADA :
# - Picking List 
# - Master Pengeluaran
# ==========================================
$FROM = "
  FROM tb_sj_kumulatif a 
  JOIN tb_sj_item b ON a.id_sj_item=b.id 
  JOIN tb_sj c ON b.kode_sj=c.kode 
  JOIN tb_barang d ON b.kode_barang=d.kode 
  WHERE c.id_kategori = $id_kategori 
";
$sql_opname_one = "SELECT 1 $FROM";
$sql_opname = "SELECT 
a.id as id_kumulatif,
a.no_lot,
a.kode_lokasi,
b.kode_sj,
a.tanggal_masuk,
a.tanggal_qc,
a.tmp_qty,
c.kode_po,
d.kode as kode_barang, 
d.nama as nama_barang, 
d.keterangan as keterangan_barang,
d.kode_lama,
d.satuan,
(
  SELECT sum(qty) FROM tb_roll 
  WHERE id_kumulatif=a.id 
  AND is_fs is null
  AND tanggal_qc is null) qty_transit,
(
  SELECT sum(qty) FROM tb_roll 
  WHERE id_kumulatif=a.id 
  AND is_fs is not null
  AND tanggal_qc is null) qty_tr_fs,
(
  SELECT sum(qty) FROM tb_roll 
  WHERE id_kumulatif=a.id 
  AND is_fs is null
  AND tanggal_qc is not null) qty_qc,
(
  SELECT sum(qty) FROM tb_roll 
  WHERE id_kumulatif=a.id 
  AND is_fs is not null
  AND tanggal_qc is not null) qty_qc_fs,
(
  SELECT sum(qty) FROM tb_retur 
  WHERE id_kumulatif=a.id) qty_retur,
(
  SELECT sum(p.qty) FROM tb_ganti p 
  JOIN tb_retur q ON p.id_retur=q.id  
  WHERE q.id_kumulatif=a.id) qty_ganti,
(
  SELECT count(1) FROM tb_roll 
  WHERE id_kumulatif=a.id ) count_roll,

  -- =================================
  -- PENGELUARAN
  -- =================================
(
  SELECT sum(qty) FROM tb_pick 
  WHERE id_kumulatif=a.id) qty_pick,
(
  SELECT sum(qty_allocate) FROM tb_pick 
  WHERE id_kumulatif=a.id) qty_allocate,
(
  SELECT sum(qty) FROM tb_retur_do 
  WHERE id_kumulatif=a.id) qty_retur_do

$FROM
";
