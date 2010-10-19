#!/usr/bin/php -d session.save_path='/tmp'
<script language="php">
	require_once('../web/include/loader.php');
	require_once(BASE . '/include/didisaster.class.php');
	
	$RegionId = 'GAR-ISDR-2011_MOZ';
	$us->login('diadmin','di8');
	$us->open($RegionId);

	$iCount = array();
	$iCount['SINEFECTOS'] = 0;
	$iCount['SINFUENTE'] = 0;
	$iCount['SINGEO'] = 0;
	$iCount['UPDATE'] = 0;
	$sQuery  = 'SELECT DisasterId FROM Disaster';
	//$sQuery .= ' WHERE DisasterId="f9e8cdec-a1f8-4c8d-b33d-e880d316ca97"';
	$sth = $us->q->dreg->prepare($sQuery);
	//$sth->bindParam(':SessionId', $prmSessionId, PDO::PARAM_STR);
	$sth->execute();
	while($row = $sth->fetch(PDO::FETCH_ASSOC)) {
		$d = new DIDisaster($us, $row['DisasterId']);
		$bUpdate = -1;
		$i = $d->validateUpdate();
		if ($d->get('RecordStatus') != 'DRAFT') {
			if ($d->status->hasWarning(-61)) {
				$bUpdate = 0;
				$iCount['SINEFECTOS']++;
			}
			if ($d->status->hasWarning(-56)) {
				$bUpdate = 0;
				$iCount['SINFUENTE']++;
			}
			if (strlen($d->get('GeographyId')) == 5) {
				$bUpdate = 1;
				$iCount['SINGEO']++;
			}
			if ($bUpdate > 0) {
				$sQuery = 'UPDATE Disaster SET RecordStatus="DRAFT" WHERE DisasterId="' . $d->get('DisasterId') . '"';
				$us->q->dreg->query($sQuery);
				$iCount['UPDATE']++;
			}
		}
	} //foreach $RegionId
	print_r($iCount);
	print "\n";
	$us->close();
	$us->logout();
</script>
