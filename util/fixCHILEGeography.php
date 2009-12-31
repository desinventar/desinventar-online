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

$g = new DIGeography($us, '0000100001');
$g->moveNodeTo($us,'0000100001','00015','0101','151');
$g->moveNodeTo($us,'0000100002','00015','0102','152');
$us->close();
$us->logout();

</script>
