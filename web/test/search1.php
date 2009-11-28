<html>
<head>
<title>Demo Search by Serial</title>
<link rel="stylesheet" type="text/css" href="../css/checkboxtree.css" charset="utf-8">
<script type="text/javascript" src="/jquery/jquery.js"></script>
<script type="text/javascript" src="/jquery/jquery.json.js"></script>
<script type="text/javascript" language="javascript">
	$(document).ready(function() {
		alert('Jquery Load');
	});
</script>
</head>
<script language="php">
require_once('../include/loader.php');
$RegionId = 'COL-1250694506-colombia_inventario_historico_de_desastres';
//$RegionId = 'CHL-1257983285-chile_inventario_historico_de_desastres';
//$RegionId = 'ARG-1250694407-argentina_inventario_historico_de_desastres';
//$RegionId = 'MEX-1250695136-mexico_inventario_historico_de_desastres';
$us->open($RegionId);
</script>
<?
	print_r($_POST);
?>
<hr size="2" />
<div id="searchBySerial"></div>
<?
$us->close();
?>
