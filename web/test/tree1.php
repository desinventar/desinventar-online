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
//$RegionId = 'ARG-1250694407-argentina_inventario_historico_de_desastres';
$RegionId = 'MEX-1250695136-mexico_inventario_historico_de_desastres';
$us->open($RegionId);

function getGeographyList($us, $Level, $ParentCode='') {
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
			print '<li>';
			print '<input type="checkbox" name="foo" value="' . $row['GeographyId'] . '" />';
			print '<label>' . $row['GeographyName'] . ' ' . $CurLevel . ' ' . $PrevLevel . '</label>';
			getGeographyList($us, $Level + 1, $row['GeographyId']);
			print '</li>';	
		}
		if ($Level > 0) { print '</ul>'; }
	}
}
print '<ul class="mytree">';
getGeographyList($us, 0);
print '</ul>';
$us->close();
</script>
