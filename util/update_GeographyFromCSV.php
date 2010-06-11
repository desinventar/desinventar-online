#!/usr/bin/php -d session.save_path='/tmp'
<script language="php">
	/*
		Updates Geography using a CSV File
	*/
	$_SERVER["DI8_WEB"] = '../web';
	require_once($_SERVER["DI8_WEB"] . '/include/loader.php');
	require_once(BASE . '/include/digeography.class.php');
	
	$RegionId = 'ECU-1250695659-ecuador_sist_de_inf_de_desastres_y_emergencias';
	$us->login('diadmin', 'di8');
	$us->open($RegionId);
	
	// First Line with Headers
	$a = fgetcsv(STDIN, 1000, ',');
	while (! feof(STDIN) ) {
		$a = fgetcsv(STDIN, 1000, ',');
		if (count($a) > 1) {
			$GeographyLevel  = $a[0];
			$GeographyCode   = $a[2];
			$GeographyName   = $a[6];
			$GeographyParent = $a[4];
			if (DIGeography::getIdByCode($us, $GeographyCode) != '') {
				// Update existing Geography Item
				$g = DIGeography::loadByCode($us, $GeographyCode);
				printf("%-5s %-20s %-20s\n", $g->get('GeographyCode'), $g->get('GeographyName'), $GeographyName);
				$g->set('GeographyName', $GeographyName);
				$g->update();
			} else {
				// Insert new Geography Item
				$ParentId = '';
				$GeographyCode = $a[5];
				print 'New Item : ' . $GeographyCode . ' (' . $GeographyParent . ') ' . $GeographyName . "\n";
				if ($GeographyParent != '') {
					$p = DIGeography::loadByCode($us, $GeographyParent);
					$ParentId = $p->get('GeographyId');
				}
				$g = new DIGeography($us);
				$g->set('GeographyCode', $GeographyCode);
				$g->set('GeographyName', $GeographyName);
				$g->setGeographyId($ParentId);
				$g->setGeographyFQName();
				$g->insert();				
			}
		}
	}
	$us->close();
	$us->logout();

</script>
