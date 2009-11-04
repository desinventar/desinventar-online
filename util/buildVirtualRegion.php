#!/usr/bin/php -d session.save_path='/tmp'
<script language="php">
	/*
	Use this script to rebuild the Virtual Region Databases
	CAN, GranChaco
	2009-08-21 Jhon H. Caicedo <jhcaiced@desinventar.org>
	*/
	
	$_SERVER["DI8_WEB"] = '../web';
	require_once($_SERVER["DI8_WEB"] . '/include/loader.php');
	require_once(BASE . '/include/diregion.class.php');
	require_once(BASE . '/include/dievent.class.php');
	require_once(BASE . '/include/dicause.class.php');
	require_once(BASE . '/include/digeography.class.php');
	require_once(BASE . '/include/diregionitem.class.php');
	require_once(BASE . '/include/digeolevel.class.php');
	require_once(BASE . '/include/digeocarto.class.php');
	require_once(BASE . '/include/disync.class.php');
	
	$RegionId = '';
	$bOption = 1;
	if ($bOption) {
		// Gran Chaco
		$RegionItems = array('ARG-1250695025-argentina_gran_chaco' => 'Argentina',
		                     'BOL-1250695036-bolivia_gran_chaco'   => 'Bolivia',
		                     'PAR-1250695038-paraguay_gran_chaco'  => 'Paraguay');
		//$RegionItems = array('BOL-1250695036-bolivia_gran_chaco' => 'Bolivia');
		$RegionId = 'DESINV-1249126759-subregion_gran_chaco';
		$RegionLabel = 'Subregion Gran Chaco';
		$PeriodBeginDate = '1997-01-01';
		$PeriodEndDate   = '2008-12-31';
		$GeoLimitMinX    = -70;
		$GeoLimitMaxX    = -54;
		$GeoLimitMinY    = -35;
		$GeoLimitMaxY    = -11;
		$InfoGeneral     = ''; //file_get_contents('desc2.txt');
	} else {
		// CAN - SubRegion Andina
		$RegionItems = array('BOL-1248983224-bolivia_inventario_historico_de_desastres'   => 'Bolivia',
	 	      	             'COL-1250694506-colombia_inventario_historico_de_desastres'  => 'Colombia',
	   	    	             'ECU-1250695011-ecuador_inventario_historico_de_desastres'   => 'Ecuador',
	   	    	             'PER-1250695241-peru_inventario_historico_de_desastres'      => 'Perú',
	   	    	             'VEN-1250695640-venezuela_inventario_historico_de_desastres' => 'Venezuela');
		//$RegionItems = array('BOL-1248983224-bolivia_inventario_historico_de_desastres');
		$RegionId = 'DESINV-1249040429-can_subregion_andina';
		$RegionLabel = 'CAN Subregion Andina';
		$PeriodBeginDate = '1970-01-01';
		$PeriodEndDate   = '2007-12-31';
		$GeoLimitMinX    = -86;
		$GeoLimitMaxX    = -53;
		$GeoLimitMinY    = -25;
		$GeoLimitMaxY    =  13;
		$InfoGeneral     = ''; //file_get_contents('desc1.txt');
	}
	// loader.php creates a UserSession when loaded...
	$r = ERR_NO_ERROR;
	$r = $us->login('diadmin','di8');
	if ($r > 0) {
		if ($RegionId == '') {
			$RegionId = DIRegion::buildRegionId('', $RegionLabel);
		}
		$o = new DIRegion($us, $RegionId);
		$o->setLanguage('spa');
		$o->set('RegionLabel'    , $RegionLabel);
		$o->set('RegionId'       , $RegionId);
		$o->set('RegionStatus'   , CONST_REGIONACTIVE | CONST_REGIONPUBLIC);
		$o->set('PeriodBeginDate', $PeriodBeginDate);
		$o->set('PeriodEndDate'  , $PeriodEndDate);
		$o->set('GeoLimitMinX'   , $GeoLimitMinX);
		$o->set('GeoLimitMaxX'   , $GeoLimitMaxX);
		$o->set('GeoLimitMinY'   , $GeoLimitMinY);
		$o->set('GeoLimitMaxY'   , $GeoLimitMaxY);
		$o->set('InfoGeneral'    , $InfoGeneral);

		$iReturn = $o->createRegionDB('País');

		//Open database
		$us->open($RegionId);
		
		// Now make this a VirtualRegion
		$o->set('IsCRegion'   , TRUE);
		//$o->update();
		/*
		// Add RegionItem
		$o->clearSyncTable();
		foreach($RegionItems as $RegionItemId => $RegionItemGeographyName) {
			printf("%-60s %-20s\n", $RegionItemId, $RegionItemGeographyName);
			$o->addRegionItem($RegionItemId, $RegionItemGeographyName);
		}
		$o->rebuildRegionData();
		*/
		$us->close();
	}
	$us->logout();	
</script>
