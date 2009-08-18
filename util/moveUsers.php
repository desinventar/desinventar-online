#!/usr/bin/php -d session.save_path='/tmp'
<script language="php">
/*
 DesInventar - http://www.desinventar.org
 (c) 1999-2009 Corporacion OSSO

 Utility to export databases from MySQL to SQLite

 2009-08-18 Jhon H. Caicedo <jhcaiced@desinventar.org>
*/
$_SERVER["DI8_WEB"] = '../web';
require_once($_SERVER["DI8_WEB"] . '/include/loader.php');
require_once(BASE . '/include/diregionauth.class.php');
require_once(BASE . '/include/diuser.class.php');

$dbh = new PDO('mysql:host=localhost;dbname=di8db', '','',
   array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

//Migrate Users from di-8.1 to di-8.2
moveUsers($dbh,$us);

function moveUsers($dbh, $us) {
	foreach($dbh->query("SELECT * FROM Users") as $row) {
		$u = new DIUser($us);
		$u->setFromArray($row);
		$UserId = $row['UserEMail'];
		$u->set('UserNotes', $u->get('UserNotes') . '(UserName=' . $row['UserName'] . ')');
		if ($UserId == '') { $UserId = $row['UserName']; }
		$u->set('UserId', $UserId);
		$u->set('CountryIso', $row['UserCountry']);
		$u->insert();
		//print $u->getInsertQuery() . "\n";
		//print $u->getUpdateQuery() . "\n";
	}
}

</script>
