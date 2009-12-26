#!/usr/bin/php -d session.save_path='/tmp'
<script language="php">
	$_SERVER["DI8_WEB"] = '../web';
	require_once($_SERVER["DI8_WEB"] . '/include/loader.php');
	require_once(BASE . '/include/digeography.class.php');
	
	$RegionId = 'COL-1250694506-colombia_inventario_historico_de_desastres';
	$us->login('diadmin', 'di8');
	$us->open($RegionId);
	
	while (! feof(STDIN) ) {
		$a = fgetcsv(STDIN, 1000, ',');
		if (count($a) > 1) {
			$GeographyCode = $a[0];
			$GeographyName = $a[1];
			$GeographyParent = $a[2];
			
			if (DIGeography::getIdByCode($us, $GeographyCode) != '') {
				// Update existing Geography Item
				$g = DIGeography::loadByCode($us, $GeographyCode);
				printf("%-5s %-20s %-20s\n", $g->get('GeographyCode'), $g->get('GeographyName'), $GeographyName);
				$g->set('GeographyName', $GeographyName);
				$g->update();
			} else {
				// Insert new Geography Item
				$p = DIGeography::loadByCode($us, $GeographyParent);				
				$g = new DIGeography($us);
				$g->set('GeographyCode', $GeographyCode);
				$g->set('GeographyName', $GeographyName);
				$g->setGeographyId($p->get('GeographyId'));
				$g->setGeographyFQName();
				$g->insert();				
			}
		}
	}
	$us->close();
	$us->logout();

</script>
