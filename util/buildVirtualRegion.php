#!/usr/bin/php -d session.save_path='/tmp'
<script language="php">
	/*
	Use this script to rebuild the Virtual Region Databases
	CAN, GranChaco
	2009-07-30 Jhon H. Caicedo <jhcaiced@desinventar.org>
	*/
	
	$_SERVER["DI8_WEB"] = '../web';
	require_once($_SERVER["DI8_WEB"] . '/include/loader.php');
	require_once(BASE . '/include/diregion.class.php');
	require_once(BASE . '/include/digeography.class.php');
	require_once(BASE . '/include/diregionitem.class.php');
	require_once(BASE . '/include/digeolevel.class.php');
	require_once(BASE . '/include/digeocarto.class.php');
	require_once(BASE . '/include/disync.class.php');
	
	$RegionId = '';
	$bOption = 1;
	if ($bOption) {
		// Gran Chaco
		$RegionItems = array('ARG-1248983690-argentina_gran_chaco' => 'Argentina',
		                     'BOL-1248983699-bolivia_gran_chaco'   => 'Bolivia',
		                     'PAR-1248983701-paraguay_gran_chaco'  => 'Paraguay');
		//$RegionItems = array('BOL-1248983699-bolivia_gran_chaco');
		$RegionId = 'DESINV-1249126759-subregion_gran_chaco';
		$RegionLabel = 'Subregion Gran Chaco';
		$PeriodBeginDate = '1997-01-01';
		$PeriodEndDate   = '2008-12-31';
		$GeoLimitMinX    = -70;
		$GeoLimitMaxX    = -54;
		$GeoLimitMinY    = -35;
		$GeoLimitMaxY    = -11;
	} else {
		// CAN - SubRegion Andina
		$RegionItems = array('BOL-1248983224-bolivia_inventario_historico_de_desastres'  => 'Bolivia',
	 	      	             'COL-1248983239-colombia_inventario_historico_de_desastres' => 'Colombia',
	   	    	             'ECU-1248983677-ecuador_inventario_historico_de_desastres'  => 'Ecuador',
	   	    	             'PER-1248983875-peru_inventario_historico_de_desastres'     => 'Perú',
	   	    	             'VEN-1248984232-inventario_de_desastres_de_venezuela'       => 'Venezuela');
		//$RegionItems = array('BOL-1248983224-bolivia_inventario_historico_de_desastres');
		$RegionId = 'DESINV-1249040429-can_subregion_andina';
		$RegionLabel = 'CAN Subregion Andina';
		$PeriodBeginDate = '1970-01-01';
		$PeriodEndDate   = '2007-12-31';
		$GeoLimitMinX    = -86;
		$GeoLimitMaxX    = -53;
		$GeoLimitMinY    = -25;
		$GeoLimitMaxY    =  13;
	}
	// loader.php creates a UserSession when loaded...
	$r = ERR_NO_ERROR;
	$r = $us->login('diadmin','di8');
	if ($r > 0) {
		if ($RegionId == '') {
			$RegionId = DIRegion::buildRegionId('', $RegionLabel);
		}
		$o = new DIRegion($us, $RegionId);
		$o->set('RegionLabel', $RegionLabel);
		$o->set('RegionId'    , $RegionId);
		$o->set('RegionStatus', CONST_REGIONACTIVE | CONST_REGIONPUBLIC);
		$o->set('IsCRegion'   , TRUE);
		$o->set('PeriodBeginDate', $PeriodBeginDate);
		$o->set('PeriodEndDate'  , $PeriodEndDate);
		$o->set('GeoLimitMinX', $GeoLimitMinX);
		$o->set('GeoLimitMaxX', $GeoLimitMaxX);
		$o->set('GeoLimitMinY', $GeoLimitMinY);
		$o->set('GeoLimitMaxY', $GeoLimitMaxY);
		$iReturn = $o->createRegionDB('País');

		$us->open($RegionId);
		$o->clearSyncTable();
		foreach($RegionItems as $RegionItemId => $RegionItemGeographyName) {
			printf("%-60s %-20s\n", $RegionItemId, $RegionItemGeographyName);
			//$RegionItemGeographyId = $o->getRegionItemGeographyId($RegionItemId);
			//fb($RegionItemGeographyId);
			//$o->addRegionItemSync($RegionItemId);
			$o->addRegionItem($RegionItemId,$RegionItemGeographyName);
		}
		/*
		$us->open($RegionId);
		$o = new DIRegion($us, $RegionId);
		$o->rebuildDataDisaster();
		$us->close();
		*/
	}
	$us->logout();	
</script>
