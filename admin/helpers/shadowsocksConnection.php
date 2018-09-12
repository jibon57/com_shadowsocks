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
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class ShadowsocksConnectionClass{
	
	public static function getTokenFromServer($host, $port, $password, $updatedb = false){
		
		$post = http_build_query(array(
			'password' => $password,
		));
		
		$return = new stdClass();
		$return->status = false;
		$return->token = 'error';
		
		$url = "https://" . $host . ":" . $port . "/login";
		
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, 1);

		$output = curl_exec ($ch);

		if (curl_error($ch)) {
			$error_msg = curl_error($ch);
		}
		curl_close ($ch);

		if (isset($error_msg)) {
			
			$return->status = false;
			$return->token = $error_msg;
			
		}else{
			
			preg_match("/HTTP\/1.1(.*)/", $output, $content);
			
			if(preg_match("/201\ Created/", $content[1])){
				
				preg_match('/{"token":"(.*)"}/', $output, $token);
				$return->status = true;
				$return->token = $token[1];
				
				if($updatedb){
					
					$db = JFactory::getDbo();
					$query = $db->getQuery(true);
					
					$fields = array(
						$db->quoteName('ss_server_token') . ' = ' . $db->quote($token[1])
					);

					$conditions = array(
						$db->quoteName('ss_server_host') . ' = ' . $db->quote($host)
					);

					$query->update($db->quoteName('#__shadowsocks_server'))->set($fields)->where($conditions);
					$db->setQuery($query);
					$result = $db->execute();
				}
			} else {
				$return->status = false;
				$return->token = $content[1];
			}
		}
		
		return $return;
	}
	
	public static function createUserPort($serverId, $userPort, $userPassword, $userEncryption){
		
		$server = self::getServerToken($serverId);
	
		$return = new stdClass();
		$return->status = false;
		$return->msg = 'error';
		
		if(!$server['status']){
			$return->msg = $server['token'];
			return $return;
		}
		
		$url = $server['server_url'] . "/";
		
		$post = http_build_query(array(
			'port' => $userPort,
			'password' => $userPassword,
			'method' => $userEncryption
		));
		$authorization = "Authorization: Bearer ". $server['token'];

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array($authorization));

		$output = curl_exec ($ch);

		if (curl_error($ch)) {
			$error_msg = curl_error($ch);
		}
		curl_close ($ch);

		if (isset($error_msg)) {
			
			$return->status = false;
			$return->msg = $error_msg;
			
		}else{
			
			preg_match("/HTTP\/1.1(.*)/", $output, $content);
			
			if(preg_match("/201\ Created/", $content[1])){
				$return->status = true;
				$return->msg = $content[1];
			} else {
				$return->status = false;
				$return->msg = $content[1];
			}
		}
		
		return $return;
	}
	
	public static function deletePort($serverId, $port){
		
		$server = self::getServerToken($serverId);
	
		$return = new stdClass();
		$return->status = false;
		$return->msg = 'error';
		
		if(!$server['status']){
			$return->msg = $server['token'];
			return $return;
		}
		
		$url = $server['server_url'] . "?port=". $port;
		
		$authorization = "Authorization: Bearer ". $server['token'];

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array($authorization));

		$output = curl_exec ($ch);

		if (curl_error($ch)) {
			$error_msg = curl_error($ch);
		}
		curl_close ($ch);

		if (isset($error_msg)) {
			
			$return->status = false;
			$return->msg = $error_msg;
			
		}else{
			
			preg_match("/HTTP\/1.1(.*)/", $output, $content);
			
			if(preg_match("/204\ No\ Content/", $content[1])){
				$return->status = true;
				$return->msg = $content[1];
			} else {
				$return->status = false;
				$return->msg = $content[1];
			}
		}
		
		return $return;
	}
	
	public static function getTrafficOfPorts($serverId){
		
		$server = self::getServerToken($serverId);
	
		$return = new stdClass();
		$return->status = false;
		$return->msg = 'error';
		
		if(!$server['status']){
			$return->msg = $server['token'];
			return $return;
		}
		
		$url = $server['server_url'] . "/traffic/all";
		
		$authorization = "Authorization: Bearer ". $server['token'];

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array($authorization));

		$output = curl_exec ($ch);

		if (curl_error($ch)) {
			$error_msg = curl_error($ch);
		}
		curl_close ($ch);

		if (isset($error_msg)) {
			
			$return->status = false;
			$return->msg = $error_msg;
			
		}else{
			
			preg_match("/HTTP\/1.1(.*)/", $output, $content);
			
			if(preg_match("/200\ OK/", $content[1])){
				preg_match('/\[(.*)\]/', $output, $ports);
				$return->status = true;
				$return->msg = $ports[0];
			} else {
				$return->status = false;
				$return->msg = $content[1];
			}
		}
		
		return $return;
	}
	
	public static function getServerToken($serverId){
		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		$query->select(array('*'))
			->from($db->quoteName('#__shadowsocks_server'))
			->where($db->quoteName('id')." = ".$db->quote($serverId));

		$db->setQuery($query);
		$server = $db->loadObject();
		//file_put_contents(__DIR__."/out.txt", print_r($server, true));
		
		/*$date = new DateTime($server->ss_token_time);
		$date->modify("+23 hour");
		
		$expireTime = strtotime($date->format("Y-m-d H:i:s"));
		$currentTime = strtotime('now');*/
		
		$return = array(
			'status' => true,
			'server_url' => "https://". $server->ss_server_host .":". $server->ss_server_port,
			'token' => $server->ss_server_token
		);
		
		$ping = self::pingToServer($return['server_url'], $return['token']);
		
		if(preg_match("/401\ Unauthorized/", $ping->msg)){
			file_put_contents(__DIR__."/out.txt", print_r($ping, true));
			$newtoken = self::getTokenFromServer($server->ss_server_host, $server->ss_server_port, $server->ss_server_password, true);
			$ping->status = true;
			$return['token'] = $newtoken->token;
		}
		
		if(!$ping->status){
			$return['status'] = false;
			$return['token'] = $ping->msg;
		}
		
		return $return;
		
	}
	
	public static function pingToServer($url, $token){
		
		
		$authorization = "Authorization: Bearer ". $token;
		
		$return = new stdClass();
		$return->status = false;
		$return->msg = 'error';

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url . "/ping");
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array($authorization));

		$output = curl_exec ($ch);

		if (curl_error($ch)) {
			$error_msg = curl_error($ch);
		}
		curl_close ($ch);

		if (isset($error_msg)) {
			
			$return->status = false;
			$return->msg = $error_msg;
			
		}else{
			
			preg_match("/HTTP\/1.1(.*)/", $output, $content);
		
			if(preg_match("/200\ OK/", $content[1])){
				$return->status = true;
				$return->msg = $content[1];
			} else {
				$return->status = false;
				$return->msg = $content[1];
			}
		}
		
		return $return;
	}
	
	public static function generatePassword($size = 25){
		
		$bag = "abcefghijknopqrstuwxyzABCDDEFGHIJKLLMMNOPQRSTUVVWXYZ0123456789abcddefghijkllmmnopqrstuvvwxyzABCEFGHIJKNOPQRSTUWXYZ0123456789";
		$key = array();
		$bagsize = strlen($bag) - 1;
		for ($i = 0; $i < $size; $i++)
		{
			$get = rand(0, $bagsize);
			$key[] = $bag[$get];
		}
		return implode($key);
	}
	
	public static function addTrafficLogic($serverid, $user, $currentTraffic){
		$addToDB = true;
		$fromDate = $user->ss_user_traffic_reset;
		
		if($user->ss_user_traffic_reset == 0){
			$fromDate = $user->created;
		}
		$currentTime = strtotime('now');
		
		switch($user->ss_package_period){
			case 0:
				$date = new DateTime($fromDate);
				$date->modify("+". $user->ss_package_duration ." day");
				$resetTime = strtotime($date->format("Y-m-d H:i:s"));
				if($currentTime >= $resetTime){
					//echo "Need to reset";
					self::addNewResetTimeToDB($serverid, date("Y-m-d H:i:s", $resetTime), $user);
				}
				self::addNewTraffic($serverid, $user, $currentTraffic);
			break;
			
			case 1:
				$date = new DateTime($fromDate);
				$date->modify("+". $user->ss_package_duration ." month");
				$resetTime = strtotime($date->format("Y-m-d H:i:s"));
				if($currentTime >= $resetTime){
					//echo "Need to reset";
					self::addNewResetTimeToDB($serverid, date("Y-m-d H:i:s", $resetTime), $user);
				}
				self::addNewTraffic($serverid, $user, $currentTraffic);
				
			break;
			
			case 2:
				$date = new DateTime($fromDate);
				$date->modify("+". $user->ss_package_duration ." year");
				$resetTime = strtotime($date->format("Y-m-d H:i:s"));
				if($currentTime >= $resetTime){
					//echo "Need to reset";
					self::addNewResetTimeToDB($serverid, date("Y-m-d H:i:s", $resetTime), $user);
				}
				self::addNewTraffic($serverid, $user, $currentTraffic);
			break;
		}
	}
	
	public static function addNewResetTimeToDB($serverid, $newDate, $user){
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		$fields = array(
			$db->quoteName('ss_user_traffic_reset') . ' = ' . $db->quote($newDate),
			$db->quoteName('ss_user_traffic') .' = '. $db->quote(0),
			$db->quoteName('ss_user_last_traffic') .' = '. $db->quote(0)
		);
		
		if($user->published == 0){
			$createPort = self::createUserPort($serverId, $user->ss_user_port, $user->ss_user_password, $user->ss_user_encryption);
			if($createPort->status){
				$publish = $db->quoteName('published') .' = '. $db->quote(1);
				array_push($fields, $publish);
			}
		}

		$conditions = array(
			$db->quoteName('id') . ' = ' . $db->quote($user->id)
		);

		$query->update($db->quoteName('#__shadowsocks_user'))->set($fields)->where($conditions);
		$db->setQuery($query);
		$result = $db->execute();
		
		return $result;
	}
	
	public static function addNewTraffic($serverid, $user, $currentTraffic){
		
		if(!$currentTraffic > 0){
			return;
		}
		$limit = ($user->ss_package_traffic * 1000000);
		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$newTraffic = $user->ss_user_traffic;
		$totalTraffic = $user->ss_user_total_traffic;
		
		if(($currentTraffic - $user->ss_user_last_traffic) > 0){
			$add = $currentTraffic - $user->ss_user_last_traffic;
			$newTraffic = $newTraffic + $add;
			$totalTraffic = $totalTraffic + $add;
		}
		
		$fields = array(
			$db->quoteName('ss_user_last_traffic') . ' = ' . $db->quote($currentTraffic),
			$db->quoteName('ss_user_traffic') .' = '. $db->quote(($newTraffic)),
			$db->quoteName('ss_user_total_traffic') .' = '. $db->quote(($totalTraffic))
		);
		
		if($newTraffic > $limit){
			if($user->published == 1){
				$deletePort = self::deletePort($serverid, $user->ss_user_port);
				if($deletePort->status){
					$unpublish = $db->quoteName('published') .' = '. $db->quote(0);
					array_push($fields, $unpublish);
				}
			}
		}

		$conditions = array(
			$db->quoteName('id') . ' = ' . $db->quote($user->id)
		);

		$query->update($db->quoteName('#__shadowsocks_user'))->set($fields)->where($conditions);
		$db->setQuery($query);
		$result = $db->execute();
		
		return $result;
	}
	
	public static function generateQRCode($userid){
		
		require_once __DIR__.'/php-qrcode/vendor/autoload.php';
		
		$db = JFactory::getDbo();
		$output = array();
		$query = $db->getQuery(true);
		$query->select(array('a.ss_user_encryption', 'a.ss_user_password', 'a.ss_user_port', 'a.ss_user_server'))
			->from($db->quoteName('#__shadowsocks_user', 'a'))
			//->join('LEFT', $db->quoteName('#__shadowsocks_server', 'b') .' ON a.ss_user_server = b.id')
			->where($db->quoteName('a.id') ." = ".$db->quote($userid));
		
		$db->setQuery($query);
		$user = $db->loadObject();
		
		$servers = json_decode($user->ss_user_server);
		
		foreach($servers as $server){
			$query = $db->getQuery(true);
			$query->select(array('ss_server_host', 'ss_server_name'))
				->from('#__shadowsocks_server')
				->where($db->quoteName('id') ." = ".$db->quote($server));
			
			$db->setQuery($query);
			$server = $db->loadObject();
			
			$server_name = str_replace(" ", "%20", $server->ss_server_name);
			$server_name = rtrim($server_name, "%20");
			
			$data = base64_encode("{$user->ss_user_encryption}:{$user->ss_user_password}@{$server->ss_server_host}:{$user->ss_user_port}");
			$ssURL = "ss://". $data . "#" . $server_name;
			
			$QRCode = (new QRCode)->render($ssURL);
			
			
			$tmp = array(
				'url' => $ssURL,
				'QR' => $QRCode,
				'server_name' => $server->ss_server_name
			);
			$output[] = $tmp;
		}
		
		return $output;
		
	}
	
	public static function checkIfUserPortExistInDB($serverid, $port){
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select(array('ss_user_server'))
			->from($db->quoteName('#__shadowsocks_user'))
			->where(/*$db->quoteName('ss_user_server') ." = ". $db->quote($serverid). " AND " .*/
					$db->quoteName('ss_user_port') ." = ". $db->quote($port));
		$db->setQuery($query);
		$results = $db->loadObjectList();
		
		if($results > 0){
			foreach($results as $data){
				$servers = json_decode($data->ss_user_server);
				foreach($servers as $server){
					if($server == $serverid){
						return true;
						break;
					}
				}
			}
		}
		
		return false;
	}
	
}
?>
