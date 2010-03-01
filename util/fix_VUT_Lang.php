#!/usr/bin/php -d session.save_path='/tmp'
<script language="php">
/*
  DesInventar - http://www.desinventar.org
  (c) 1998-2010 Corporacion OSSO
  
  2010-03-01 Jhon H. Caicedo <jhcaiced@desinventar.org>
  
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


$RegionId = 'VUT-1250695628-disaster_database_of_the_republic_of_vanuatu';
$us->login('root','97ossonp');
$us->open($RegionId);

$r = new DIRegion($us, $RegionId);
$r->q->dreg->query('DELETE FROM Event WHERE LangIsoCode="spa" and EventPredefined=1');
$r->q->dreg->query('DELETE FROM Cause WHERE LangIsoCode="spa" and CausePredefined=1');
$r->copyEvents('eng');
$r->copyCauses('eng');

$us->close();
$us->logout();
</script>
