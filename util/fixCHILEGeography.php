#!/usr/bin/php -d session.save_path='/tmp'
<script language="php">
/*
  DesInventar - http://www.desinventar.org
  (c) 1998-2009 Corporacion OSSO
  
  2009-12-29 Jhon H. Caicedo <jhcaiced@desinventar.org>
  
  Import data from DGR (Direccion de Gestion del Riesgo) 
  SIGPAD - Colombia
*/

require_once('../web/include/loader.php');
require_once(BASE . '/include/diregion.class.php');
require_once(BASE . '/include/didisaster.class.php');
require_once(BASE . '/include/dieedata.class.php');
require_once(BASE . '/include/digeography.class.php');
require_once(BASE . '/include/dicause.class.php');
require_once(BASE . '/include/dievent.class.php');
require_once(BASE . '/include/dieefield.class.php');

$RegionId = 'CHL-1257983285-chile_inventario_historico_de_desastres';
$us->login('diadmin','di8');
$us->open($RegionId);

$line = 1;
while (! feof(STDIN) ) {
	$iReturn = ERR_NO_ERROR;
	$Msg = '';
	$a = fgetcsv(STDIN, 1000, ',');
	if (count($a) > 1) {
		$OldCode   = $a[0];
		$NewCode   = $a[5];
		$Parent    = $a[1];
		$Name      = $a[3];
		$newParent = $a[4];
		$OldId = '';
		if ( ($Name != '--') && ($Name != '') ) {
			$OldParent = DIGeography::getIdByName($us, $Parent, '');
			$OldId = DIGeography::getIdByName($us, $Name, $OldParent);
			if ($OldId == '') {
				$Msg .= 'Id No hallado para ' . $Name;
				$iReturn = ERR_UNKNOWN_ERROR;
			}
			if ($iReturn > 0) {
				$g = new DIGeography($us, $OldId);
				if ($g->get('GeographyLevel') == 2) {
					printf("%-20s %-20s %-5s %-10s %-10s\n", $Name, $OldId, $newParent, $OldCode, $NewCode);
				}
			}
		}
	}
	if ($Msg != '') {
		fb($line . ' ' . $Msg);
	}
	$line++;
}

/*
$g = new DIGeography($us, '0000100001');
$g->moveNodeTo($us,'0000100001','00015','0101','151');
$g->moveNodeTo($us,'0000100002','00015','0102','152');
*/

$us->close();
$us->logout();

</script>
