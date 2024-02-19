UPDATE `tb_user` SET kode = LOWER(kode);
UPDATE `tb_user` SET `password` = md5(kode);
