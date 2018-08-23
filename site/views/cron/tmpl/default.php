<?php
/**
 * @package    Shadowsocks
 *
 * @created    14th August, 2018
 * @author     Jibon L. Costa <support@mynaparrot.com>
 * @website    https://www.mynaparrot.com
 * @copyright  Copyright (C) MynaParrot 2018. All Rights Reserved
 * @license    MIT
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access'); 

?>
<?php echo $this->toolbar->render(); ?> 
<?php 
echo "<pre>";
foreach($this->items as $server){
	$traffic = ShadowsocksConnectionClass::getTrafficOfPorts($server->id);
	if($traffic->status == 1){
		$ports = json_decode($traffic->msg);
		
		$users = $this->getUsersByPort($server->id, 0);
		foreach($users as $user){
			$traffic = $this->findUserPortFromAllPorts($ports, $user->ss_user_port);
			if(!$traffic > 0){
				$traffic = 0;
			}
			ShadowsocksConnectionClass::addTrafficLogic($server->id, $user, $traffic);
		}
	}
}
jexit();

?>    
