<?php
# ==========================================
# INCLUDE PADA :
# - Picking List 
# - Master Pengeluaran
# ==========================================
$FROM = "FROM tb_pick a 
  JOIN tb_sj_kumulatif b ON a.id_kumulatif=b.id 
  JOIN tb_sj_item c ON b.id_sj_item=c.id 
  JOIN tb_sj d ON c.kode_sj=d.kode 
  JOIN tb_barang f ON c.kode_barang=f.kode 
  JOIN tb_satuan g ON f.satuan=g.satuan 
  JOIN tb_lokasi h ON b.kode_lokasi=h.kode 
  JOIN tb_do i ON a.id_do=i.id 
  WHERE i.id_kategori=$id_kategori ";
$sql_pick_one = "SELECT 1 $FROM";
$sql_pick = "SELECT 
  a.id as id_pick,
  a.qty as qty_pick,
  a.is_hutangan,
  a.qty_allocate,
  a.tanggal_pick,
  a.tanggal_allocate,
  a.is_repeat,
  a.boleh_allocate,
  b.no_lot,
  b.kode_lokasi,
  b.is_fs,
  b.tmp_qty,
  c.kode_sj,
  d.kode_po,
  f.kode_lama,
  f.kode as kode_barang,
  f.nama as nama_barang, 
  f.keterangan as keterangan_barang,
  g.satuan, 
  g.step, 
  h.brand,
  i.kode_do,
  i.kode_artikel,
  i.date_created as tanggal_do, 
    -- =========================================
    -- PEMASUKAN
    -- =========================================
  (
    SELECT sum(p.qty) FROM tb_roll p 
    JOIN tb_sj_kumulatif q ON p.id_kumulatif=q.id 
    WHERE q.id=a.id_kumulatif 
    AND q.is_fs is null
    AND q.tanggal_qc is null) qty_transit,
  (
    SELECT sum(p.qty) FROM tb_roll p 
    JOIN tb_sj_kumulatif q ON p.id_kumulatif=q.id 
    WHERE q.id=a.id_kumulatif 
    AND q.is_fs is not null
    AND q.tanggal_qc is null) qty_tr_fs,

  (
    SELECT sum(p.qty) FROM tb_roll p 
    JOIN tb_sj_kumulatif q ON p.id_kumulatif=q.id 
    WHERE q.id=a.id_kumulatif 
    AND q.is_fs is null
    AND q.tanggal_qc is not null) qty_qc,
  (
    SELECT sum(p.qty) FROM tb_roll p 
    JOIN tb_sj_kumulatif q ON p.id_kumulatif=q.id 
    WHERE q.id=a.id_kumulatif 
    AND q.is_fs is not null
    AND q.tanggal_qc is not null) qty_qc_fs,
  (
    SELECT SUM(p.qty) FROM tb_retur p 
    JOIN tb_sj_kumulatif q ON p.id_kumulatif=q.id 
    WHERE q.id=a.id_kumulatif 
    ) qty_retur, 
    -- ALL RETUR = RETUR REG + RETUR FS  

  (
    SELECT SUM(p.qty) FROM tb_ganti p
    JOIN tb_retur q ON p.id_retur=q.id  
    JOIN tb_sj_kumulatif r ON q.id_kumulatif=r.id 
    WHERE r.id=a.id_kumulatif 
    ) qty_ganti, 
    -- ALL GANTI



    -- =========================================
    -- PENGELUARAN
    -- =========================================
  (
    SELECT SUM(p.qty) FROM tb_pick p 
    WHERE p.id != a.id -- bukan pick yang ini
    AND p.is_hutangan is null -- tidak termasuk hutangan
    AND p.id_kumulatif = a.id_kumulatif) qty_pick_by_other,
  (
    SELECT SUM(p.qty_allocate) FROM tb_pick p 
    WHERE p.id != a.id -- bukan allocate yang ini
    AND p.is_hutangan is null -- tidak termasuk hutangan
    AND p.id_kumulatif = a.id_kumulatif) qty_allocate_by_other,
  (
    SELECT count(1) FROM tb_roll 
    WHERE id_kumulatif = b.id) count_roll,


    -- =========================================
    -- PICKER | ALLOCATOR
    -- =========================================
  (
    SELECT nama FROM tb_user 
    WHERE id = a.pick_by) picker,
  (
    SELECT nama FROM tb_user 
    WHERE id = a.allocate_by) allocator

  $FROM
";
