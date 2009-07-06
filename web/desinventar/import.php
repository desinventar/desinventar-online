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

if (isset($_POST['r']) && !empty($_POST['r']))
	$reg = $_POST['r'];
elseif (isset($_GET['r']) && !empty($_GET['r']))
	$reg = $_GET['r'];
else
	exit();

if (isset($_FILES['desinv']) && isset($_POST['diobj'])) {
	$iserror = true;
	if (isset($_POST['cmd']) && $_POST['cmd'] == "upload") {
		if ($_FILES['desinv']['error'] == UPLOAD_ERR_OK) {
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
				// first validate file to continue with importation
				$i = new DIImport($us);
				$valm = $i->validateFromCSV($FileName);
				if (is_array($valm)) {
					$stat = (int) $valm['Status'];
					if (!iserror($stat))
						$valm = $i->importFromCSV($FileName);
					$t->assign ("msg", $valm);
					$t->assign ("csv", loadCSV($valm['FileName']));
					$iserror = false;
					$t->assign ("ctl_msg", true);
				} else {
					$error = "UNKNOWN ERROR WHEN UPLOADING FILE, TRY AGAIN..";
				}
			} else {
				$error = "TYPE IS UNKNOWN.. MUST BE TEXT COMMA SEPARATED!";
			}
		} else {
			$error = "UPLOAD FAILED";
		}
	} else {
		$error = "UNKWOWN ERROR ... ";
	}
	
	// nothing to upload
	if ($iserror) {
		$t->assign ("error", $error);
		$t->assign ("ctl_error", true);
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
