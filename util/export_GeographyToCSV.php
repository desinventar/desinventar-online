#!/usr/bin/php -d session.save_path='/tmp'
<script language="php">
	$_SERVER["DI8_WEB"] = '../web';
	require_once($_SERVER["DI8_WEB"] . '/include/loader.php');
	require_once(BASE . '/include/digeography.class.php');
	
	$RegionId = 'ECU-1250695659-ecuador_sist_de_inf_de_desastres_y_emergencias';
	$us->login('diadmin', 'di8');
	$us->open($RegionId);
	
	$sQuery  = "SELECT * FROM Geography ";
	$sQuery .= " ORDER By GeographyLevel,GeographyCode";
	foreach($us->q->dreg->query($sQuery) as $row) {
		$ParentCode = substr($row['GeographyCode'],0, $row['GeographyLevel']*2);
		printf('"%s","%s","%s","%s","%s"' . "\n", 
			$row['GeographyLevel'],
			$row['GeographyId'],
			$row['GeographyCode'],
			$row['GeographyName'],
			$ParentCode
		);
	}
	$us->close();
	$us->logout();

</script>
