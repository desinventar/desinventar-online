#!/usr/bin/php -d session.save_path='/tmp'
<script language="php">
/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2009 Corporacion OSSO

 Utility to export databases from MySQL to SQLite

 2009-11-02 Jhon H. Caicedo <jhcaiced@desinventar.org>
*/
$_SERVER["DI8_WEB"] = '../web';
require_once($_SERVER["DI8_WEB"] . '/include/loader.php');
require_once(BASE . '/include/diregion.class.php');
require_once(BASE . '/include/diregionauth.class.php');
require_once(BASE . '/include/diuser.class.php');
require_once(BASE . '/include/didisaster.class.php');
require_once(BASE . '/include/dievent.class.php');
require_once(BASE . '/include/dicause.class.php');
require_once(BASE . '/include/digeolevel.class.php');
require_once(BASE . '/include/digeography.class.php');

$q = new Query();
$dbh = new PDO('mysql:host=localhost;dbname=di8db', '','',
   array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
$RegionList = array();
foreach($q->core->query("SELECT * FROM Region ORDER BY RegionId") as $row) {
	$RegionList[] = $row['RegionId'];
}
//$RegionList = array('GTM-1255694888-guatemala_inventario_historico_de_desastres');
//$RegionList = array('BOL-1250695036-bolivia_gran_chaco');
foreach ($RegionList as $RegionId) {
	$r = new DIRegion($us, $RegionId);
	$RegionUUID = $r->get('OptionOldName');
	$query = "UPDATE Region SET RegionId='" . $RegionId . "' WHERE RegionUUID='" . $RegionUUID . "'";
	$dbh->query($query);
} //foreach

$dbh = null;
$q = null;

</script>
