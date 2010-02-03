#!/usr/bin/php -d session.save_path='/tmp'
<script language="php">
/*
  DesInventar - http://www.desinventar.org
  (c) 1998-2010 Corporacion OSSO
  
  2010-01-21 Jhon H. Caicedo <jhcaiced@desinventar.org>
  
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
require_once(BASE . '/include/date.class.php');


$RegionId = 'HTI-1263415699-haiti_disaster_database';
$us->login('root','di8');
$us->open($RegionId);

$r = new DIRegion($us, $RegionId);
$r->copyEvents('fre');
$r->copyCauses('fre');

$us->close();
$us->logout();
</script>
