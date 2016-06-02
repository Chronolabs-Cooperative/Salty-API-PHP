<?php
/**
 * Chronolabs REST Blowfish Salts Repository API
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       Chronolabs Cooperative http://labs.coop
 * @license         GNU GPL 2 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 * @package         salty
 * @since           2.0.1
 * @author          Simon Roberts <wishcraft@users.sourceforge.net>
 * @version         $Id: functions.php 1000 2015-06-16 23:11:55Z wishcraft $
 * @subpackage		functions
 * @description		Blowfish Salts Repository API
 * @link			http://cipher.labs.coop
 * @link			http://sourceoforge.net/projects/chronolabsapis
 */

if (!function_exists("whitelistGetIP")) {

	/* function whitelistGetIPAddy()
	 *
	* 	provides an associative array of whitelisted IP Addresses
	* @author 		Simon Roberts (Chronolabs) simon@labs.coop
	*
	* @return 		array
	*/
	function whitelistGetIPAddy() {
		return array_merge(whitelistGetNetBIOSIP(), file(dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'whitelist.txt'));
	}
}

if (!function_exists("whitelistGetNetBIOSIP")) {

	/* function whitelistGetNetBIOSIP()
	 *
	* 	provides an associative array of whitelisted IP Addresses base on TLD and NetBIOS Addresses
	* @author 		Simon Roberts (Chronolabs) simon@labs.coop
	*
	* @return 		array
	*/
	function whitelistGetNetBIOSIP() {
		$ret = array();
		foreach(file(dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'whitelist-domains.txt') as $domain) {
			$ip = gethostbyname($domain);
			$ret[$ip] = $ip;
		}
		return $ret;
	}
}

if (!function_exists("whitelistGetIP")) {

	/* function whitelistGetIP()
	 *
	* 	get the True IPv4/IPv6 address of the client using the API
	* @author 		Simon Roberts (Chronolabs) simon@labs.coop
	*
	* @param		$asString	boolean		Whether to return an address or network long integer
	*
	* @return 		mixed
	*/
	function whitelistGetIP($asString = true){
		// Gets the proxy ip sent by the user
		$proxy_ip = '';
		if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$proxy_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else
		if (!empty($_SERVER['HTTP_X_FORWARDED'])) {
			$proxy_ip = $_SERVER['HTTP_X_FORWARDED'];
		} else
		if (! empty($_SERVER['HTTP_FORWARDED_FOR'])) {
			$proxy_ip = $_SERVER['HTTP_FORWARDED_FOR'];
		} else
		if (!empty($_SERVER['HTTP_FORWARDED'])) {
			$proxy_ip = $_SERVER['HTTP_FORWARDED'];
		} else
		if (!empty($_SERVER['HTTP_VIA'])) {
			$proxy_ip = $_SERVER['HTTP_VIA'];
		} else
		if (!empty($_SERVER['HTTP_X_COMING_FROM'])) {
			$proxy_ip = $_SERVER['HTTP_X_COMING_FROM'];
		} else
		if (!empty($_SERVER['HTTP_COMING_FROM'])) {
			$proxy_ip = $_SERVER['HTTP_COMING_FROM'];
		}
		if (!empty($proxy_ip) && $is_ip = preg_match('/^([0-9]{1,3}.){3,3}[0-9]{1,3}/', $proxy_ip, $regs) && count($regs) > 0)  {
			$the_IP = $regs[0];
		} else {
			$the_IP = $_SERVER['REMOTE_ADDR'];
		}
			
		$the_IP = ($asString) ? $the_IP : ip2long($the_IP);
		return $the_IP;
	}
}

/**
 * apiSearch() ~ searches for an result on the API
 * 
 * @param string $method
 * @param string $query
 * @param boolean $peers
 * @param integer $number
 * @param array $peerings
 * 
 * @return array
 */
function apiSearch($method = '', $query = '', $peers = false, $number = 1, $peerings = array())
{

	switch ($method)
	{
		case "uri":
			$url['protocol'] = (substr($query,0,5)=="https"?"https://":(substr($query,0,5)=="httpx"?"httpx://":"http://"));
			$url['domain'] = parse_url($query, PHP_URL_HOST);
			$url['path'] = parse_url($query, PHP_URL_PATH);
			$url['base'] = getBaseDomain($url['protocol'].$url['domain']);
			$parts = array_reverse(explode('.', $url['base']));
			$url['fallout'] = (strlen($parts[0])==2?$parts[0]:"");
			$url['strata'] = (strlen($parts[0])!=2?$parts[0]:$parts[1]);
			$sql = "SELECT count(*) FROM `" . $GLOBALS['saltyDB']->prefix("uri") . "` WHERE `uri-key` = '" . $urikey = sha1(implode("", $url)) ."'";
			list($count) = $GLOBALS['saltyDB']->FetchRow($GLOBALS['saltyDB']->queryF($sql));
			if ($count > 0)
			{
				return array("count"=>$count, "code" => 200);
			}
			break;
		case "email":
			$sql = "SELECT count(*) FROM `" . $GLOBALS['saltyDB']->prefix("email") . "` WHERE `email` LIKE '" . $email ."'";
			list($count) = $GLOBALS['saltyDB']->FetchRow($GLOBALS['saltyDB']->queryF($sql));
			if ($count > 0)
			{
				return array("count"=>$count, "code" => 200);
			}
	}
	return array("error"=>"nothing found", "code" => 500);
}

/**
 * apiLodge() ~ create's a blowfish salt lodgement
 * 
 * @param string $email
 * @param string $name
 * @param integer $pin
 * @param string $uri
 * @param string $salt
 * @param array $peerings
 * 
 * @return array
 */
function apiLodge($email = '', $name = '', $pin = 0, $uri = '', $salt = '', $peerings = array())
{
	
	$url['protocol'] = (substr($uri,0,5)=="https"?"https://":(substr($uri,0,5)=="httpx"?"httpx://":"http://"));
	$url['domain'] = parse_url($uri, PHP_URL_HOST);
	$url['path'] = parse_url($uri, PHP_URL_PATH);
	$url['base'] = getBaseDomain($url['protocol'].$url['domain']);
	$parts = array_reverse(explode('.', $url['base']));
	$url['fallout'] = (strlen($parts[0])==2?$parts[0]:"");
	$url['strata'] = (strlen($parts[0])!=2?$parts[0]:$parts[1]);
	
	$sql = "SELECT count(*) FROM `" . $GLOBALS['saltyDB']->prefix("uri") . "` WHERE `uri-key` = '" . $urikey = sha1(implode("", $url)) ."'";
	list($count) = $GLOBALS['saltyDB']->FetchRow($GLOBALS['saltyDB']->queryF($sql));
	if ($count == 0)
	{
		$query[] = "INSERT INTO `" . $GLOBALS['saltyDB']->prefix("uri") . "` (`uri-key`, `encounted`, `protocol`, `domain`, `base`, `strata`, `fallout`, `salts`, `retrieves`) VALUES('" . $urikey ."', 1, '" . $GLOBALS['saltyDB']->escape($url['protocol']) . "', '" . $GLOBALS['saltyDB']->escape($url['domain']) . "', '" . $GLOBALS['saltyDB']->escape($url['base']) . "','" . $GLOBALS['saltyDB']->escape($url['strata']) . "','" . $GLOBALS['saltyDB']->escape($url['fallout']) . "', 1, 0)";
	} else {
		$query[] = "UPDATE `" . $GLOBALS['saltyDB']->prefix("uri") . "` SET `encounted` = `encounted` + 1, `salts` = `salts` + 1 WHERE `uri-key` = '" . $urikey ."'";
	}
	$sql = "SELECT count(*) FROM `" . $GLOBALS['saltyDB']->prefix("email") . "` WHERE `email-key` = '" . $emailkey = sha1($email.$name.$urikey) ."'";
	list($count) = $GLOBALS['saltyDB']->FetchRow($GLOBALS['saltyDB']->queryF($sql));
	if ($count == 0)
	{
		$query[] = "INSERT INTO `" . $GLOBALS['saltyDB']->prefix("email") . "` (`email-key`,`uri-key`, `email`, `name`) VALUES('" . $emailkey ."', '" . $urikey ."', '" . $GLOBALS['saltyDB']->escape($email) . "', '" . $GLOBALS['saltyDB']->escape($name) . "')";
	}
	$sql = "SELECT count(*) FROM `" . $GLOBALS['saltyDB']->prefix("salts") . "` WHERE `salt-key` = '" . $saltkey = sha1($pin.$emailkey.$urikey.$pin) ."'";
	list($count) = $GLOBALS['saltyDB']->FetchRow($GLOBALS['saltyDB']->queryF($sql));
	if ($count == 0)
	{
		$query[] = "INSERT INTO `" . $GLOBALS['saltyDB']->prefix("salts") . "` (`salt-key`, `email-key`, `uri-key`, `fingerprint`, `salt`, `created`, `retrieves`, `retrieved`) VALUES('" . $saltkey ."', '" . $emailkey ."', '" . $urikey ."', md5('" . $GLOBALS['saltyDB']->escape($salt) . "'), COMPRESS(AES_ENCRYPT('" . $GLOBALS['saltyDB']->escape($salt) . "', '" . $GLOBALS['saltyDB']->escape($pin . $url['base'] . $email . $pin) . "')), UNIX_TIMESTAMP(), 0, 0)";
	} else
		return array("error"=>"salt already exists", "code" => 500);
	foreach($query as $question)
		if (!$GLOBALS['saltyDB']->QueryF($question))
			return array("error"=>"salt creation errored non-existent!", "code" => 500);
	return array("record"=>sha1(implode("|:|",$query)), "code" => 200);
}

/**
 * apiRetrieve() ~ retieves a salt from the database
 * 
 * @param string $email
 * @param integer $pin
 * @param string $uri
 * @param array $peerings
 * 
 * @return array
 */
function apiRetrieve($email = '', $pin = 0, $uri = '', $peerings = array())
{
	$url['protocol'] = (substr($uri,0,5)=="https"?"https://":(substr($uri,0,5)=="httpx"?"httpx://":"http://"));
	$url['domain'] = parse_url($uri, PHP_URL_HOST);
	$url['path'] = parse_url($uri, PHP_URL_PATH);
	$url['base'] = getBaseDomain($url['protocol'].$url['domain']);
	$parts = array_reverse(explode('.', $url['base']));
	$url['fallout'] = (strlen($parts[0])==2?$parts[0]:"");
	$url['strata'] = (strlen($parts[0])!=2?$parts[0]:$parts[1]);
	
	$sql = "SELECT count(*) FROM `" . $GLOBALS['saltyDB']->prefix("uri") . "` WHERE `uri-key` = '" . $urikey = sha1(implode("", $url)) ."'";
	list($count) = $GLOBALS['saltyDB']->FetchRow($GLOBALS['saltyDB']->queryF($sql));
	if ($count == 0)
	{
		return array("error"=>"email not found", "code" => 500);
	}
	$sql = "SELECT count(*) FROM `" . $GLOBALS['saltyDB']->prefix("email") . "` WHERE `email` = '" . $email ."'";
	list($count) = $GLOBALS['saltyDB']->FetchRow($GLOBALS['saltyDB']->queryF($sql));
	if ($count == 0)
	{
		return array("error"=>"email not found", "code" => 500);
	}
	$sql = "SELECT DECOMPRESS(AES_DECRYPT(`salt`, '" . $GLOBALS['saltyDB']->escape($pin . $url['base'] . $email . $pin) . "')) as `salt` FROM `" . $GLOBALS['saltyDB']->prefix("salts") . "` WHERE `fingerprint` = md5(DECOMPRESS(AES_DECRYPT(`salt`, '" . $GLOBALS['saltyDB']->escape($pin . $url['base'] . $email . $pin) . "')))";
	list($salt) = $GLOBALS['saltyDB']->FetchRow($GLOBALS['saltyDB']->queryF($sql));
	if (empty($salt))
	{
		return array("error"=>"no salt found could be wrong pin or email or url", "code" => 500);
	} 
	return array("salt"=>$salt, 'md5'=>md5($salt), "code" => 200);
}

?>
