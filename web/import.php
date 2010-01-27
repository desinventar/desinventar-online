<script language="php">
/*
 DesInventar8 - http://www.desinventar.org
 (c) 1998-2010 Corporacion OSSO
*/

require_once('include/loader.php');
require_once('include/diimport.class.php');

function loadCSV($ocsv) {
	$handle = fopen($ocsv, "r");
	$res = array();
	while (($data = fgetcsv($handle, 100, ",")) !== FALSE)
		$res[] = array($data[0], $data[1], $data[2], $data[3], $data[4]);
	fclose($handle);
	return $res;
}

$post = $_POST;
$get = $_GET;

$RegionId = getParameter('_REG', getParameter('r'), '');

if ($RegionId == '') {
	exit();
}
$us->open($RegionId);

$ImportType = $post['diobj'];

if (isset($_FILES['desinv']) && isset($post['diobj'])) {
	$iserror = true;
	if (isset($post['cmd']) && $post['cmd'] == "upload" && $_FILES['desinv']['error'] == UPLOAD_ERR_OK) {
		$error = '';
		if ($_FILES['desinv']['type'] == "text/comma-separated-values" ||
			$_FILES['desinv']['type'] == "application/octet-stream" ||
			$_FILES['desinv']['type'] == "text/x-csv" ||
			$_FILES['desinv']['type'] == "text/csv" ||
			$_FILES['desinv']['type'] == "text/plain") {
			$tmp_name = $_FILES['desinv']['tmp_name'];
			$name = $_FILES['desinv']['name'];
			if (ARCH == 'LINUX') {
				// This step executes iconv to convert the uploaded file to UTF-8
				$Cmd = "/usr/bin/iconv --from-code=ISO-8859-1 --to-code=UTF-8 " . $tmp_name . "> " . TEMP . "/" . $name;
				system($Cmd);
			} else {
				move_uploaded_file($tmp_name, TEMP ."/$name");
			}
			$iserror = false;
			$FileName = TEMP . '/' . $name;
			// load basic field of dictionary
			$dic = array();
			$dic = array_merge($dic, $us->q->queryLabelsFromGroup('Disaster', $lg));
			$dic = array_merge($dic, $us->q->queryLabelsFromGroup('Record|2', $lg));
			$dic = array_merge($dic, $us->q->queryLabelsFromGroup('Geography', $lg));
			$dic = array_merge($dic, $us->q->queryLabelsFromGroup('Event', $lg));
			$dic = array_merge($dic, $us->q->queryLabelsFromGroup('Cause', $lg));
			$dic = array_merge($dic, $us->q->queryLabelsFromGroup('Effect', $lg));
			$dic = array_merge($dic, $us->q->queryLabelsFromGroup('Sector', $lg));
			$dic = array_merge($dic, $us->q->getEEFieldList("True"));
			$GeographyImport = "GeographyLevel,GeographyCode,GeographyName,GeographyParentCode";
			$DisasterImport = "DisasterId,DisasterSerial,DisasterBeginTime,GeographyId,".
				"DisasterSiteNotes,DisasterSource,DisasterLongitude,DisasterLatitude,RecordAuthor,".
				"RecordCreation,RecordStatus,EventId,EventDuration,EventMagnitude,EventNotes,CauseId,".
				"CauseNotes,EffectPeopleDead,EffectPeopleMissing,EffectPeopleInjured,EffectPeopleHarmed,".
				"EffectPeopleAffected,EffectPeopleEvacuated,EffectPeopleRelocated,EffectHousesDestroyed,".
				"EffectHousesAffected,EffectLossesValueLocal,EffectLossesValueUSD,EffectRoads,EffectFarmingAndForest,".
				"EffectLiveStock,EffectEducationCenters,EffectMedicalCenters,EffectOtherLosses,EffectNotes,".
				"SectorTransport,SectorCommunications,SectorRelief,SectorAgricultural,SectorWaterSupply,".
				"SectorSewerage,SectorEducation,SectorPower,SectorIndustry,SectorHealth,SectorOther";
			switch($ImportType) {
			case 4:
				$FieldList = $GeographyImport;
				break;
			case 5:
				$FieldList = $DisasterImport;
				break;
			default:
				break;
			}
			$lst = explode(",", $FieldList);
			foreach ($lst as $v) {
				if (isset($dic[$v][0]))
					$fld[$v] = $dic[$v][0];
				else
					$fld[$v] = $v;
			}
			$handle = fopen($FileName, "r");
			$res = array();
			$i = 0;
			// Get first 10 lines, jump first line
			while (($data = fgetcsv($handle, 1000, ",")) !== FALSE && $i < 10) {
				if ($i > 0)
					$csv[] = $data;
				$i++;
			}
			fclose($handle);
			$t->assign ("csv", $csv);
			$t->assign ("FileName", $FileName);
			$t->assign ("fld", $fld);
			$t->assign ("ctl_import", true);
		} else {
			$error = "FILETYPE IS UNKNOWN.. FORMAT MUST BE TEXT COMMA SEPARATED!";
		}
	} elseif (isset($post['cmd']) && $post['cmd'] == "import") {
		// first validate file to continue with importation
		$i = new DIImport($us);
		//$valm = $i->validateFromCSV($post['FileName']);
		$valm = 0;
		if (is_array($valm)) {
			$stat = (int) $valm['Status'];
			if (!iserror($stat))
				$valm = $i->importFromCSV($post['FileName'], DI_DISASTER);
			$t->assign ("msg", $valm);
			$t->assign ("res", loadCSV($valm['FileName']));
			$t->assign ("ctl_msg", true);
			$iserror = false;
		} else {
			$error = "UNKNOWN ERROR WHEN UPLOADING FILE, TRY AGAIN..";
		}
		// nothing to upload
		if ($iserror) {
			$t->assign ("error", $error);
			$t->assign ("ctl_error", true);
		}
	} else {
		$error = "UPLOAD FAILED";
	}
} else {
	// show upload form
	$urol = $us->getUserRole($RegionId);
	if ($urol == "OBSERVER")
		$t->assign ("ro", "disabled");
	$t->assign ("ctl_show", true);
}

$t->assign ("RegionId", $RegionId);
$t->display ("import.tpl");

</script>
