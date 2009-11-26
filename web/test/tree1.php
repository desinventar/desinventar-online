<html>
<head>
<title>Demo TEST</title>
<link rel="stylesheet" type="text/css" href="../css/checkboxtree.css" charset="utf-8">
<script type="text/javascript" src="/jquery/jquery.js"></script>
<script type="text/javascript" src="/jquery/jquery.json.js"></script>
<script type="text/javascript" src="../include/jquery.checkboxtree.js"></script>
<script type="text/javascript" language="javascript">
	$(document).ready(function() {
		$(".mytree").checkboxTree({
			collapsedarrow: "../images/checkboxtree/img-arrow-collapsed.gif",
			expandedarrow: "../images/checkboxtree/img-arrow-expanded.gif",
			blankarrow: "../images/checkboxtree/img-arrow-blank.gif"
		});
	});
</script>
</head>
<script language="php">
require_once('../include/loader.php');
//$RegionId = 'COL-1250694506-colombia_inventario_historico_de_desastres';
//$RegionId = 'CHL-1257983285-chile_inventario_historico_de_desastres';
$RegionId = 'ARG-1250694407-argentina_inventario_historico_de_desastres';
//$RegionId = 'MEX-1250695136-mexico_inventario_historico_de_desastres';
$us->open($RegionId);

function getGeographyList($us, $Level, &$Selected, $ParentCode='') {
	$querysuffix .= 'FROM Geography WHERE GeographyLevel=' . $Level;
	if ($Level > 0) {
		$querysuffix .= " AND GeographyId LIKE '" . $ParentCode . "%' ";
	}
	$querysuffix .= ' ORDER BY GeographyName';
	
	// First issue a count(*) query to count the number of rows...
	$query = 'SELECT COUNT(*) ' . $querysuffix;
	$stmt = $us->q->dreg->query($query);
	$rows = $stmt->fetchColumn();
	if ($rows > 0) {
		$query = 'SELECT * ' . $querysuffix;
		$stmt = $us->q->dreg->prepare($query);
		$stmt->execute();
		if ($Level > 0) { print '<ul>'; }
		while ($row = $stmt->fetch()) {
			$Checked = '';
			if (array_key_exists($row['GeographyId'], $Selected)) {
				$Checked = 'CHECKED';
			}
			print '<li>';
			print '<input type="checkbox" name="foo[' . $row['GeographyId'] . ']" value="' . $row['GeographyId'] . '" ' . $Checked . ' />';
			print '<label>' . $row['GeographyName'] . ' ' . $CurLevel . ' ' . $PrevLevel . '</label>';
			getGeographyList($us, $Level + 1, $Selected, $row['GeographyId']);
			print '</li>';	
		}
		if ($Level > 0) { print '</ul>'; }
	}
}
</script>
<?
	print_r($_POST);
?>
<hr size="2" />
<form method="POST" action="tree1.php">
<input type="submit" name="ok" value="ok" />
<?php
print '<ul class="mytree">';
getGeographyList($us, 0, $_POST['foo']);
print '</ul>';
$us->close();
?>
</form>
