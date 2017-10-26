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


error_reporting(E_ERROR);
ini_set('display_errors', true);
ini_set('log_errors', false);


if (!function_exists("getDomainSupportism")) {
    
    /**
     * getDomainSupportism ~ gets the domains supporting API
     *
     * @param string $variable
     * @param string $realm
     * @return array
     */
    function getDomainSupportism($variable = 'array', $realm = '')
    {
        static $ret = array();
        if (empty($ret))
        {
            $supporters = file(API_FILE_IO_DOMAINS);
            foreach($supporters as $supporter)
            {
                $parts = explode("||", $supporter);
                if (strpos(' '.strtolower($realm), strtolower($parts[0]))>0)
                {
                    $ret['domain'] = $parts[0];
                    $ret['protocol'] = $parts[1];
                    $ret['business'] = $parts[2];
                    $ret['entity'] = $parts[3];
                    $ret['contact'] = $parts[4];
                    $ret['referee'] = $parts[5];
                    continue;
                }
            }
        }
        if (isset($ret[$variable]))
            return $ret[$variable];
            return $ret;
    }
}


if (!function_exists("checkDisplayHelp")) {
    
    /**
     * checkDisplayHelp ~ checks if help will need to be displayed
     *
     * @param string $action
     * @return boolean
     */
    function checkDisplayHelp($action = '')
    {
        global $errors;
        apiLoadLanguage('errors', _API_LANGUAGE_DEFAULT);
        if (empty($action))
            return true;
        return false;
    }
}


if (!function_exists("apiLoadLanguage")) {
    
    /**
     * apiLoadLanguage ~ loads a language files
     *
     * @param unknown_type $definition
     * @param unknown_type $language
     * @return boolean
     */
    function apiLoadLanguage($definition = 'help', $language = 'english')
    {
        if (!empty($language)) $language = _API_LANGUAGE_DEFAULT;
        if (file_exists($file = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'language' . DIRECTORY_SEPARATOR . $language . DIRECTORY_SEPARATOR . "$definition.php"))
        {
            return include_once($file);
        }
        return false;
    }
}


if (!function_exists("getURIData")) {
    
    /**
     * getURIData() ~ get data from a URI/URL
     *
     * @param string $uri
     * @param array $posts
     * @param getURIData $headers
     * @param integer $timeout
     * @param integer $connectout
     * @return string
     */
    function getURIData($uri = '', $posts = array(), $headers = array(), $timeout = 36, $connectout = 44)
    {
        if (!function_exists("curl_init"))
        {
            return file_get_contents($uri);
        }
        if (!$btt = curl_init($uri)) {
            return false;
        }
        if (count($headers)) {
            curl_setopt($btt, CURLOPT_HEADER, true);
            curl_setopt($btt, CURLOPT_HEADERS, $headers);
        } else
            curl_setopt($btt, CURLOPT_HEADER, 0);
            if (count($posts)) {
                curl_setopt($btt, CURLOPT_POST, true);
                curl_setopt($btt, CURLOPT_POSTFIELDS, http_build_query($posts));
            } else
                curl_setopt($btt, CURLOPT_POST, 0);
                curl_setopt($btt, CURLOPT_CONNECTTIMEOUT, $connectout);
                curl_setopt($btt, CURLOPT_TIMEOUT, $timeout);
                curl_setopt($btt, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($btt, CURLOPT_VERBOSE, false);
                curl_setopt($btt, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($btt, CURLOPT_SSL_VERIFYPEER, false);
                $data = curl_exec($btt);
                curl_close($btt);
                return $data;
    }
}

if (!function_exists("readRawFile")) {
    
    /**
     * Return the contents of this File as a string.
     *
     * @param string $file
     * @param string $bytes where to start
     * @param string $mode
     * @param boolean $force If true then the file will be re-opened even if its already opened, otherwise it won't
     * @return mixed string on success, false on failure
     * @access public
     */
    function readRawFile($file = '', $bytes = false, $mode = 'rb', $force = false)
    {
        $success = false;
        if ($bytes === false) {
            $success = file_get_contents($file);
        } elseif ($fhandle = fopen($file, $mode)) {
            if (is_int($bytes)) {
                $success = fread($fhandle, $bytes);
            } else {
                $data = '';
                while (! feof($fhandle)) {
                    $data .= fgets($fhandle, 4096);
                }
                $success = trim($data);
            }
            fclose($fhandle);
        }
        return $success;
    }
}

if (!function_exists("writeRawFile")) {
    /**
     *
     * @param string $file
     * @param string $data
     */
    function writeRawFile($file = '', $data = '')
    {
        if (!is_dir(dirname($file)))
            mkdir(dirname($file), 0777, true);
            if (is_file($file))
                unlink($file);
                $ff = fopen($file, 'w');
                fwrite($ff, $data, strlen($data));
                return fclose($ff);
    }
}

if (!function_exists("writeCache")) {
    /**
     * Write data for key into cache
     *
     * @param string $key Identifier for the data
     * @param mixed $data Data to be cached
     * @param mixed $duration How long to cache the data, in seconds
     * @return boolean True if the data was succesfully cached, false on failure
     * @access public
     */
    function writeCache($key, $data = array(), $duration = 3600)
    {
        if (!isset($data)) {
            return false;
        }
        
        if (!empty($key))
            $key .= substr(md5($_SERVER["HTTP_HOST"]), 3, 7) . '--' . $key;
            else
                return false;
                
                if ($duration == null) {
                    $duration = 3600;
                }
                $windows = false;
                $lineBreak = "\n";
                
                if (substr(PHP_OS, 0, 3) == "WIN") {
                    $lineBreak = "\r\n";
                    $windows = true;
                }
                $expires = time() + $duration;
                $contents = $expires . $lineBreak . "return " . var_export($data, true) . ";" . $lineBreak;
                return  writeRawFile(API_PATH . DIRECTORY_SEPARATOR . $key . '.php');
    }
}

if (!function_exists("readCache")) {
    /**
     * Read a key from the cache
     *
     * @param string $key Identifier for the data
     * @return mixed The cached data, or false if the data doesn't exist, has expired, or if there was an error fetching it
     * @access public
     */
    function readCache($key)
    {
        if (!empty($key))
            $key .= substr(md5($_SERVER["HTTP_HOST"]), 3, 7) . '--' . $key;
            else
                return false;
                
                $cachetime = readRawFile(API_PATH . DIRECTORY_SEPARATOR . $key . '.php', 11);
                if ($cachetime !== false && intval($cachetime) < time()) {
                    return false;
                }
                $data = readRawFile(API_PATH . DIRECTORY_SEPARATOR . $key . '.php', true);
                if (!empty($data))
                    $data = eval($data);
                    return $data;
    }
}

if (!function_exists("checkEmail")) {
    /**
     * checkEmail()
     *
     * @param mixed $email
     * @return bool|mixed
     */
    function checkEmail($email)
    {
        if (!$email || !preg_match('/^[^@]{1,64}@[^@]{1,255}$/', $email)) {
            return false;
        }
        $email_array = explode("@", $email);
        $local_array = explode(".", $email_array[0]);
        for ($i = 0; $i < sizeof($local_array); $i++) {
            if (!preg_match("/^(([A-Za-z0-9!#$%&'*+\/\=?^_`{|}~-][A-Za-z0-9!#$%&'*+\/\=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$/", $local_array[$i])) {
                return false;
            }
        }
        if (!preg_match("/^\[?[0-9\.]+\]?$/", $email_array[1])) {
            $domain_array = explode(".", $email_array[1]);
            if (sizeof($domain_array) < 2) {
                return false; // Not enough parts to domain
            }
            for ($i = 0; $i < sizeof($domain_array); $i++) {
                if (!preg_match("/^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$/", $domain_array[$i])) {
                    return false;
                }
            }
        }
        return $email;
    }
}


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
            $sql = "SELECT count(*) FROM `" . $GLOBALS['APIDB']->prefix("uri") . "` WHERE `uri-key` = '" . $urikey = sha1(implode("", $url)) ."'";
            list($count) = $GLOBALS['APIDB']->FetchRow($GLOBALS['APIDB']->queryF($sql));
            if ($count > 0)
            {
                return array("count"=>$count, "code" => 200);
            }
            break;
        case "email":
            $sql = "SELECT count(*) FROM `" . $GLOBALS['APIDB']->prefix("email") . "` WHERE `email` LIKE '" . $email ."'";
            list($count) = $GLOBALS['APIDB']->FetchRow($GLOBALS['APIDB']->queryF($sql));
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
    
    $sql = "SELECT count(*) FROM `" . $GLOBALS['APIDB']->prefix("uri") . "` WHERE `uri-key` = '" . $urikey = sha1(implode("", $url)) ."'";
    list($count) = $GLOBALS['APIDB']->FetchRow($GLOBALS['APIDB']->queryF($sql));
    if ($count == 0)
    {
        $query[] = "INSERT INTO `" . $GLOBALS['APIDB']->prefix("uri") . "` (`uri-key`, `encounted`, `protocol`, `domain`, `base`, `strata`, `fallout`, `salts`, `retrieves`) VALUES('" . $urikey ."', 1, '" . $GLOBALS['APIDB']->escape($url['protocol']) . "', '" . $GLOBALS['APIDB']->escape($url['domain']) . "', '" . $GLOBALS['APIDB']->escape($url['base']) . "','" . $GLOBALS['APIDB']->escape($url['strata']) . "','" . $GLOBALS['APIDB']->escape($url['fallout']) . "', 1, 0)";
    } else {
        $query[] = "UPDATE `" . $GLOBALS['APIDB']->prefix("uri") . "` SET `encounted` = `encounted` + 1, `salts` = `salts` + 1 WHERE `uri-key` = '" . $urikey ."'";
    }
    $sql = "SELECT count(*) FROM `" . $GLOBALS['APIDB']->prefix("email") . "` WHERE `email-key` = '" . $emailkey = sha1($email.$name.$urikey) ."'";
    list($count) = $GLOBALS['APIDB']->FetchRow($GLOBALS['APIDB']->queryF($sql));
    if ($count == 0)
    {
        $query[] = "INSERT INTO `" . $GLOBALS['APIDB']->prefix("email") . "` (`email-key`,`uri-key`, `email`, `name`) VALUES('" . $emailkey ."', '" . $urikey ."', '" . $GLOBALS['APIDB']->escape($email) . "', '" . $GLOBALS['APIDB']->escape($name) . "')";
    }
    $sql = "SELECT count(*) FROM `" . $GLOBALS['APIDB']->prefix("salts") . "` WHERE `salt-key` = '" . $saltkey = sha1($pin.$emailkey.$urikey.$pin) ."'";
    list($count) = $GLOBALS['APIDB']->FetchRow($GLOBALS['APIDB']->queryF($sql));
    if ($count == 0)
    {
        $query[] = "INSERT INTO `" . $GLOBALS['APIDB']->prefix("salts") . "` (`salt-key`, `email-key`, `uri-key`, `fingerprint`, `salt`, `created`, `retrieves`, `retrieved`) VALUES('" . $saltkey ."', '" . $emailkey ."', '" . $urikey ."', md5('" . $GLOBALS['APIDB']->escape($salt) . "'), COMPRESS(AES_ENCRYPT('" . $GLOBALS['APIDB']->escape($salt) . "', '" . $GLOBALS['APIDB']->escape($pin . $url['base'] . $email . $pin) . "')), UNIX_TIMESTAMP(), 0, 0)";
    } else
        return array("error"=>"salt already exists", "code" => 500);
        foreach($query as $question)
            if (!$GLOBALS['APIDB']->QueryF($question))
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
    
    $sql = "SELECT count(*) FROM `" . $GLOBALS['APIDB']->prefix("uri") . "` WHERE `uri-key` = '" . $urikey = sha1(implode("", $url)) ."'";
    list($count) = $GLOBALS['APIDB']->FetchRow($GLOBALS['APIDB']->queryF($sql));
    if ($count == 0)
    {
        return array("error"=>"email not found", "code" => 500);
    }
    $sql = "SELECT count(*) FROM `" . $GLOBALS['APIDB']->prefix("email") . "` WHERE `email` = '" . $email ."'";
    list($count) = $GLOBALS['APIDB']->FetchRow($GLOBALS['APIDB']->queryF($sql));
    if ($count == 0)
    {
        return array("error"=>"email not found", "code" => 500);
    }
    $sql = "SELECT DECOMPRESS(AES_DECRYPT(`salt`, '" . $GLOBALS['APIDB']->escape($pin . $url['base'] . $email . $pin) . "')) as `salt` FROM `" . $GLOBALS['APIDB']->prefix("salts") . "` WHERE `fingerprint` = md5(DECOMPRESS(AES_DECRYPT(`salt`, '" . $GLOBALS['APIDB']->escape($pin . $url['base'] . $email . $pin) . "')))";
    list($salt) = $GLOBALS['APIDB']->FetchRow($GLOBALS['APIDB']->queryF($sql));
    if (empty($salt))
    {
        return array("error"=>"no salt found could be wrong pin or email or url", "code" => 500);
    }
    return array("salt"=>$salt, 'md5'=>md5($salt), "code" => 200);
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
function apiSearchV3($method = '', $query = '', $variable = '', $peers = false, $number = 1, $peerings = array())
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
            $sql = "SELECT count(`a`.*) FROM `" . $GLOBALS['APIDB']->prefix("uri") . "` as `a` INNER JOIN  `" . $GLOBALS['APIDB']->prefix("salts") . "` as `b` ON `a`.`uri-key` = `b`.`uri-key` WHERE `a`.`uri-key` = '" . $urikey = sha1(implode("", $url)) ."' HAVING `b`.`variable` = " . $GLOBALS['APIDB']->quote($variable);
            list($count) = $GLOBALS['APIDB']->FetchRow($GLOBALS['APIDB']->queryF($sql));
            if ($count > 0)
            {
                return array("count"=>$count, "code" => 200);
            }
            break;
        case "email":
            $sql = "SELECT count(`a`.*) FROM `" . $GLOBALS['APIDB']->prefix("email") . "` as `a` INNER JOIN  `" . $GLOBALS['APIDB']->prefix("salts") . "` as `b` ON `a`.`email-key` = `b`.`email-key` WHERE `a`.`email` LIKE '" . $email ."' HAVING `b`.`variable` = " . $GLOBALS['APIDB']->quote($variable);
            list($count) = $GLOBALS['APIDB']->FetchRow($GLOBALS['APIDB']->queryF($sql));
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
function apiLodgeV3($email = '', $variable = '', $name = '', $pin = 0, $uri = '', $salt = '', $peerings = array())
{
    
    $url['protocol'] = (substr($uri,0,5)=="https"?"https://":(substr($uri,0,5)=="httpx"?"httpx://":"http://"));
    $url['domain'] = parse_url($uri, PHP_URL_HOST);
    $url['path'] = parse_url($uri, PHP_URL_PATH);
    $url['base'] = getBaseDomain($url['protocol'].$url['domain']);
    $parts = array_reverse(explode('.', $url['base']));
    $url['fallout'] = (strlen($parts[0])==2?$parts[0]:"");
    $url['strata'] = (strlen($parts[0])!=2?$parts[0]:$parts[1]);
    
    $sql = "SELECT count(*) FROM `" . $GLOBALS['APIDB']->prefix("uri") . "` WHERE `uri-key` = '" . $urikey = sha1(implode("", $url)) ."'";
    list($count) = $GLOBALS['APIDB']->FetchRow($GLOBALS['APIDB']->queryF($sql));
    if ($count == 0)
    {
        $query[] = "INSERT INTO `" . $GLOBALS['APIDB']->prefix("uri") . "` (`uri-key`, `encounted`, `protocol`, `domain`, `base`, `strata`, `fallout`, `salts`, `retrieves`) VALUES('" . $urikey ."', 1, '" . $GLOBALS['APIDB']->escape($url['protocol']) . "', '" . $GLOBALS['APIDB']->escape($url['domain']) . "', '" . $GLOBALS['APIDB']->escape($url['base']) . "','" . $GLOBALS['APIDB']->escape($url['strata']) . "','" . $GLOBALS['APIDB']->escape($url['fallout']) . "', 1, 0)";
    } else {
        $query[] = "UPDATE `" . $GLOBALS['APIDB']->prefix("uri") . "` SET `encounted` = `encounted` + 1, `salts` = `salts` + 1 WHERE `uri-key` = '" . $urikey ."'";
    }
    $sql = "SELECT count(*) FROM `" . $GLOBALS['APIDB']->prefix("email") . "` WHERE `email-key` = '" . $emailkey = sha1($email.$name.$urikey) ."'";
    list($count) = $GLOBALS['APIDB']->FetchRow($GLOBALS['APIDB']->queryF($sql));
    if ($count == 0)
    {
        $query[] = "INSERT INTO `" . $GLOBALS['APIDB']->prefix("email") . "` (`email-key`,`uri-key`, `email`, `name`) VALUES('" . $emailkey ."', '" . $urikey ."', '" . $GLOBALS['APIDB']->escape($email) . "', '" . $GLOBALS['APIDB']->escape($name) . "')";
    }
    $sql = "SELECT count(*) FROM `" . $GLOBALS['APIDB']->prefix("salts") . "` WHERE `variable` = '$variable' AND `salt-key` = '" . $saltkey = sha1($pin.$emailkey.$urikey.$pin) ."'";
    list($count) = $GLOBALS['APIDB']->FetchRow($GLOBALS['APIDB']->queryF($sql));
    if ($count == 0)
    {
        $query[] = "INSERT INTO `" . $GLOBALS['APIDB']->prefix("salts") . "` (`salt-key`, `email-key`, `uri-key`, `variable`, `fingerprint`, `salt`, `created`, `retrieves`, `retrieved`) VALUES('" . $saltkey ."', '" . $emailkey ."', '" . $urikey ."', '" . $variable ."', md5('" . $GLOBALS['APIDB']->escape($salt) . "'), COMPRESS(AES_ENCRYPT('" . $GLOBALS['APIDB']->escape($salt) . "', '" . $GLOBALS['APIDB']->escape($pin . $url['base'] . $email . $pin) . "')), UNIX_TIMESTAMP(), 0, 0)";
    } else
        return array("error"=>"salt already exists", "code" => 500);
        foreach($query as $question)
            if (!$GLOBALS['APIDB']->QueryF($question))
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
function apiRetrieveV3($email = '', $variable = '', $pin = 0, $uri = '', $peerings = array())
{
    $url['protocol'] = (substr($uri,0,5)=="https"?"https://":(substr($uri,0,5)=="httpx"?"httpx://":"http://"));
    $url['domain'] = parse_url($uri, PHP_URL_HOST);
    $url['path'] = parse_url($uri, PHP_URL_PATH);
    $url['base'] = getBaseDomain($url['protocol'].$url['domain']);
    $parts = array_reverse(explode('.', $url['base']));
    $url['fallout'] = (strlen($parts[0])==2?$parts[0]:"");
    $url['strata'] = (strlen($parts[0])!=2?$parts[0]:$parts[1]);
    
    $sql = "SELECT count(*) FROM `" . $GLOBALS['APIDB']->prefix("uri") . "` WHERE `uri-key` = '" . $urikey = sha1(implode("", $url)) ."'";
    list($count) = $GLOBALS['APIDB']->FetchRow($GLOBALS['APIDB']->queryF($sql));
    if ($count == 0)
    {
        return array("error"=>"email not found", "code" => 500);
    }
    $sql = "SELECT count(*) FROM `" . $GLOBALS['APIDB']->prefix("email") . "` WHERE `email` = '" . $email ."'";
    list($count) = $GLOBALS['APIDB']->FetchRow($GLOBALS['APIDB']->queryF($sql));
    if ($count == 0)
    {
        return array("error"=>"email not found", "code" => 500);
    }
    $sql = "SELECT DECOMPRESS(AES_DECRYPT(`salt`, '" . $GLOBALS['APIDB']->escape($pin . $url['base'] . $email . $pin) . "')) as `salt` FROM `" . $GLOBALS['APIDB']->prefix("salts") . "` WHERE `fingerprint` = md5(DECOMPRESS(AES_DECRYPT(`salt`, '" . $GLOBALS['APIDB']->escape($pin . $url['base'] . $email . $pin) . "'))) HAVING `variable` = '$variable'";
    list($salt) = $GLOBALS['APIDB']->FetchRow($GLOBALS['APIDB']->queryF($sql));
    if (empty($salt))
    {
        return array("error"=>"no salt found could be wrong pin or email or url", "code" => 500);
    }
    return array("salt"=>$salt, 'md5'=>md5($salt), "code" => 200);
}

?>
