ALTER TABLE `#__shadowsocks_user` ADD `ss_user_enable_plugin` INT(1) NOT NULL DEFAULT 0 AFTER `assign_to`;

ALTER TABLE `#__shadowsocks_user` ADD `ss_user_plugin_options` VARCHAR(2048) NOT NULL DEFAULT '' AFTER `ss_user_password`;
