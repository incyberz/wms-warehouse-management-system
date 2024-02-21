UPDATE `tb_user` SET kode = LOWER(kode);
UPDATE `tb_user` SET `password` = md5(kode);


ALTER TABLE `tb_sj_subitem` CHANGE `kode_kumulatif` `kode_kumulatif` VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'kode_barang~ kode_po~ no_lot~ kode_lokasi~ is_fs'; 
ALTER TABLE `tb_sj_subitem` CHANGE `nomor` `nomor` TINYINT(4) NOT NULL COMMENT 'no urut subitem'; 
