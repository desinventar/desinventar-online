#!/usr/bin/php
<script language="php">
/*
  DesInventar - http://www.desinventar.org
  (c) 1998-2009 Corporacion OSSO
  
  2009-12-18 Jhon H. Caicedo <jhcaiced@desinventar.org>
  
  Import data from DGR (Direccion de Gestion del Riesgo) 
  SIGPAD - Colombia
*/

$_SERVER["DI8_WEB"] = '../web';
require_once('../web/include/loader.php');
while (! feof(STDIN) ) {
	$a = fgetcsv(STDIN, 1000, ',');
	if (count($a) > 1) {
		$v = strToISO8601($a[0]);
		fb($v);
	}
} //while

function strToISO8601($prmDate) {
	$v = '';
	$v = $prmDate;
	return $v;
}
</script>
