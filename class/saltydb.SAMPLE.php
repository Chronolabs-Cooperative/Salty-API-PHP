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
 * @version         $Id: manager.php 1000 2015-06-16 23:11:55Z wishcraft $
 * @subpackage		database
 * @description		Blowfish Salts Repository API
 * @link			http://cipher.labs.coop
 * @link			http://sourceoforge.net/projects/chronolabsapis
 */

/**
 * @var string		Database Name (Database Source One)
 */
define('DB_SALTY_NAME', '');

/**
 * @var string		Database Username (Database Source One)
 */
define('DB_SALTY_USER', '');

/**
 * @var string		Database Password (Database Source One)
 */
define('DB_SALTY_PASS', '');

/**
 * @var string		Database Host Address/IP (Database Source One)
 */
define('DB_SALTY_HOST', 'localhost');

/**
 * @var string		Database Character Set (Global)
 */
define('DB_SALTY_CHAR', 'utf8');

/**
 * @var string		Database Persistency Connection (Global)
 */
define('DB_SALTY_PERS', false);

/**
 * @var string		Database Types (Global)
 */
define('DB_SALTY_TYPE', 'mysql');

/**
 * @var string		Database Prefix (Global)
 */
define('DB_SALTY_PREF', 'salty');

/**
 * @var string		Database Name (Database Source Two)
 */
define('DB_SALTY_NAME2', '');

/**
 * @var string		Database Username (Database Source Two)
 */
define('DB_SALTY_USER2', '');

/**
 * @var string		Database Password (Database Source Two)
 */
define('DB_SALTY_PASS2', '');

/**
 * @var string		Database Host Address/IP (Database Source Two)
 */
define('DB_SALTY_HOST2', 'localhost');


require_once dirname(__FILE__) . '/database.php';
require_once dirname(__FILE__) . '/databasefactory.php';

/**
 * @var object		Database Handler Object (Globals)
 */
$GLOBALS['saltyDB'] = SaltyDatabaseFactory::getDatabaseConnection();

?>
