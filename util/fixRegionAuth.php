#!/usr/bin/php
<script language="php">
/*
  DesInventar - http://www.desinventar.org
  (c) 1999-2009 Corporacion OSSO
  
  2009-08-18 Jhon H. Caicedo <jhcaiced@desinventar.org>
 
  Fix core.RegionAuth table, update UserId field based
  on changes to User table.
*/

$_SERVER["DI8_WEB"] = '../web';
require_once($_SERVER["DI8_WEB"] . '/include/loader.php');
require_once(BASE . '/include/diuser.class.php');

$q = new Query();
$query = "SELECT DISTINCT UserId FROM RegionAuth";
$userlist = array();
foreach($q->core->query($query) as $row) {
	$u = new DIUser($us, $row['UserId']);
	printf("%-40s %-40s\n", $row['UserId'], $u->get('UserId'));	
	$userlist[$row['UserId']] = $u->get('UserId');
}
foreach($userlist as $UserName => $UserId) {
	$query = "UPDATE RegionAuth SET UserId='" . $UserId . "' WHERE UserId='" . $UserName . "'";
	$q->core->query($query);
}
$q = null;
</script>
