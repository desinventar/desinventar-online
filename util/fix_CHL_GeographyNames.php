#!/usr/bin/php -d session.save_path='/tmp'
<script language="php">
/*
  DesInventar - http://www.desinventar.org
  (c) 1998-2010 Corporacion OSSO
  
  2010-01-03 Jhon H. Caicedo <jhcaiced@desinventar.org>
  
  Fix CHILE Geography, update codes and create new items
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
		$OldCode      = $a[2];
		$NewCode      = $a[7];
		$Parent       = $a[1];
		$Name         = $a[3];
		$newParent    = $a[4];
		$OldId = '';
		if ( ($Name != '--') && ($Name != '') ) {
			$OldParent = '';
			$OldParent = DIGeography::getIdByName($us, $Parent, '');
			$OldId = DIGeography::getIdByName($us, $Name, $OldParent);
			if ($OldId == '') {
				$Msg .= 'Id No hallado para ' . $Name;
				$iReturn = ERR_UNKNOWN_ERROR;
			}
			if ($iReturn > 0) {
				$g = new DIGeography($us, $OldId);
				fb(sprintf("%3s %-20s %-20s %-10s %-10s %-25s", $line, $OldId, $newParent, $OldCode, $NewCode, $Name));
				DIGeography::moveNodeTo($us, $OldId, $newParent, $OldCode, $NewCode, false);
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
