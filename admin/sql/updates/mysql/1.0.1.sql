ALTER TABLE `#__shadowsocks_user` CHANGE `ss_user_last_traffic` `ss_user_last_traffic` VARCHAR(2048) NOT NULL DEFAULT 0;

ALTER TABLE `#__shadowsocks_user` CHANGE `ss_user_traffic` `ss_user_traffic` VARCHAR(2048) NOT NULL DEFAULT 0;
