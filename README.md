# com_shadowsocks
Joomla component for managing shadowsocks using shadowsocks-restful-api. By using this component you will be able to manage your shadowsocks server(s) more efficiently. 

**Features**
1) Manage multiple users from Joomla backend.
2) Support multiple servers.
3) Bandwidth calculation & disable users upon over usage.
4) Easy package management & more....

First of all you will need to have a working server with `shadowsocks-restful-api` install. You can get more information from here: https://github.com/shadowsocks/shadowsocks-restful-api

**Install**:
===========
1) Download the zip archive & install from your Joomla backend.
2) Now navigate from `component menu` to `Shadowsocks`
3) First of all you will have to add a server. So, from server menu click new to add. In that form you will need to add information of you `shadowsocks-restful-api`.
4) Now go to package menu & add one package. At present you can add daily, monthly & yearly packages.
5) Now go to user menu & assign user to one or multiple servers & a package. You can use any port or password. Remember once you will save this information, you can't change.
6) By click on `QR Code` you can get configuration for that particular user's port.
7) You will need to add a cron job for calculating bandwidth. The link will be `http://YOUR_JOOMLA_SITE/index.php?option=com_shadowsocks&view=cron`. You can set this cron to run every 5 minutes or more.

**Tips**
========
This extension will allow to use only `chacha20-ietf-poly1305` encryption method now. So you can run your shadowsocks manager like this:

`ss-manager -m chacha20-ietf-poly1305 -u --manager-address /tmp/shadowsocks-manager.sock --fast-open`

If you have plan to use plugin then can be like this:

`ss-manager -m chacha20-ietf-poly1305 -u --manager-address /tmp/shadowsocks-manager.sock --fast-open --plugin "/usr/local/bin/obfs-server" --plugin-opts "obfs=http;fast-open"`

Cron job command can be use like this:

`*/5 * * * * wget --quiet --spider --timeout=0 --tries=1 "http://YOUR_JOOMLA_SITE/index.php?option=com_shadowsocks&view=cron"`