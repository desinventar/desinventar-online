#!/usr/bin/php
<script language="php">
/*
  DesInventar - http://www.desinventar.org
  (c) 1998-2010 Corporacion OSSO
  
  2010-11-08 Jhon H. Caicedo <jhcaiced@desinventar.org>

  Process GAR2011 databases, apply rules to discard records.

*/

require_once('../web/include/loader.php');

$RegionList = array();
foreach($us->q->core->query("SELECT * FROM Region WHERE RegionId LIKE 'GAR-ISDR-2011_%'") as $row) {
	$RegionList[] = $row['RegionId'];
}
//DEBUG
//$RegionList = array('GAR-ISDR-2011_COL');
foreach ($RegionList as $RegionId) {
	$newRegionId = str_replace('ISDR','UMBRAL',$RegionId);
	print $RegionId . ' ' . $newRegionId . "\n";
	
	$us->open($RegionId);
	$sQuery = 'UPDATE Disaster SET RecordStatus="PUBLISHED" WHERE LENGTH(GeographyId)==5';
	$us->q->dreg->query($sQuery);

	$Dir1 = DBDIR . '/' . $RegionId;
	$Dir2 = DBDIR . '/' . $newRegionId;
	recurse_copy($Dir1, $Dir2);
	$r = new DIRegion($us, $newRegionId);
	$r->set('RegionLabel', $r->get('RegionLabel') . ' UMBRALES');
	$r->insert();

	$us->open($newRegionId);
	$sQuery = 'UPDATE Disaster SET RecordStatus="DRAFT" WHERE LENGTH(GeographyId)==5';
	$us->q->dreg->query($sQuery);

	$us->setUserRole('osso@osso.org.co',$newRegionId,'ADMINREGION');
	$us->close();
} //foreach
exit();

function recurse_copy($src,$dst) {
	$dir = opendir($src);
	@mkdir($dst);
	while(false !== ( $file = readdir($dir)) ) {
		if (( $file != '.' ) && ( $file != '..' )) {
			if ( is_dir($src . '/' . $file) ) {
				recurse_copy($src . '/' . $file,$dst . '/' . $file);
			} else {
				copy($src . '/' . $file,$dst . '/' . $file);
			}
		}
	}
	closedir($dir);
} //function

</script>
