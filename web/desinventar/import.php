<script language="php">
/*
 DesInventar8 - http://www.desinventar.org
 (c) 1999-2009 Corporacion OSSO
*/

require_once('../include/loader.php');
require_once('../include/usersession.class.php');
require_once('../include/query.class.php');
require_once('../include/region.class.php');
require_once('../include/diimport.class.php');
require_once('../include/didisaster.class.php');
require_once('../include/digeography.class.php');
require_once('../include/dievent.class.php');
require_once('../include/dicause.class.php');
require_once('../include/dieedata.class.php');

function loadCSV($csv) {
	$handle = fopen($csv, "r");
	$res = array();
	while (($data = fgetcsv($handle, 100, ",")) !== FALSE)
		$res[] = array($data[0], $data[1], $data[2], $data[3], $data[4]);
	fclose($handle);
	return $res;
}

$post = $_POST;
$get = $_GET;

if (isset($post['r']) && !empty($post['r']))
	$reg = $post['r'];
elseif (isset($get['r']) && !empty($get['r']))
	$reg = $get['r'];
else
	exit();

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
			$ocsv = null;
			$DisasterImport = array(0 => 'DisasterId', 
		                        1 => 'DisasterSerial',
		                        2 => 'DisasterBeginTime',
		                        3 => 'DisasterGeographyId',
			                    4 => 'DisasterSiteNotes',
			                    5 => 'DisasterSource',
			                    6 => 'DisasterLongitude',
			                    7 => 'DisasterLatitude',
			                    8 => 'RecordAuthor',
			                    9 => 'RecordCreation',
			                    10 => 'RecordStatus',
			                    11 => 'EventId',
			                    12 => 'EventDuration',
			                    13 => 'EventMagnitude',
			                    14 => 'EventNotes',
			                    15 => 'CauseId',
			                    16 => 'CauseNotes',
			                    // Effects on People
			                    17 => 'EffectPeopleDead',
			                    18 => 'EffectPeopleMissing',
			                    19 => 'EffectPeopleInjured',
			                    20 => 'EffectPeopleHarmed',
			                    21 => 'EffectPeopleAffected',
			                    22 => 'EffectPeopleEvacuated',
			                    23 => 'EffectPeopleRelocated',
			                    // Effects on Houses
			                    24 => 'EffectHousesDestroyed',
			                    25 => 'EffectHousesAffected',
			                    // Effects General
			                    26 => 'EffectLossesValueLocal',
			                    27 => 'EffectLossesValueUSD',
			                    28 => 'EffectRoads',
			                    29 => 'EffectFarmingAndForest',
			                    30 => 'EffectLiveStock',
			                    31 => 'EffectEducationCenters',
			                    32 => 'EffectMedicalCenters',
			                    // Other Losses
			                    33 => 'EffectOtherLosses',
			                    34 => 'EffectNotes',
			                    // Sectors Affected
			                    35 => 'SectorTransport',
			                    36 => 'SectorCommunications',
			                    37 => 'SectorRelief',
			                    38 => 'SectorAgricultural',
			                    39 => 'SectorWaterSupply',
			                    40 => 'SectorSewerage',
			                    41 => 'SectorEducation',
			                    42 => 'SectorPower',
			                    43 => 'SectorIndustry',
			                    44 => 'SectorHealth',
			                    45 => 'SectorOther'
						   );
			$t->assign ("csv", $ocsv);
			$t->assign ("fld", $DisasterImport);
			$t->assign ("ctl_import", true);
		} else {
			$error = "FILETYPE IS UNKNOWN.. FORMAT MUST BE TEXT COMMA SEPARATED!";
		}
	}
	elseif (isset($post['cmd']) && $post['cmd'] == "import") {
		// first validate file to continue with importation
		$i = new DIImport($us);
		$valm = $i->validateFromCSV($FileName);
		if (is_array($valm)) {
			$stat = (int) $valm['Status'];
			if (!iserror($stat))
				$valm = $i->importFromCSV($FileName);
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
	$urol = $us->getUserRole($reg);
	if ($urol == "OBSERVER")
		$t->assign ("ro", "disabled");
	$t->assign ("ctl_show", true);
}

$t->assign ("reg", $reg);
$t->display ("import.tpl");

</script>
