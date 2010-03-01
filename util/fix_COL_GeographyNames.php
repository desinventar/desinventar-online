#!/usr/bin/php -d session.save_path='/tmp'
<script language="php">
/*
  DesInventar - http://www.desinventar.org
  (c) 1998-2010 Corporacion OSSO
  
  2010-01-25 Jhon H. Caicedo <jhcaiced@desinventar.org>
  
  Import data from CSV file and update the GeographyName of 
  all COL Geography items to lowercase.
*/

require_once('../web/include/loader.php');
require_once(BASE . '/include/digeography.class.php');

$RegionId = 'COL-1250694506-colombia_inventario_historico_de_desastres';
$us->login('diadmin','di8');
$us->open($RegionId);

// First line of headers
$a = fgetcsv(STDIN, 1000, ',');
$line = 1;
while (! feof(STDIN) ) {
	$a = fgetcsv(STDIN, 1000, ',');
	if (count($a) > 1) {
		for($i = 0; $i<count($a); $i++) {
			$a[$i] = trim($a[$i]);
		}
		$GeographyId = $a[0];
		$GeographyName = $a[3];
		print $GeographyId . ' ' . $GeographyName . "\n";
		if (DIGeography::existId($us, $GeographyId)) {
			$g = new DIGeography($us, $GeographyId);
			$g->set('GeographyName', $GeographyName);
			$g->update();
			$g->setGeographyFQName();
		}
	}
	$line++;
} //while

$us->close();
$us->logout();

</script>
