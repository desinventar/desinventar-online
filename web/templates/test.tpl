{-config_load file="$lg.conf" section="di8_index"-}
<?xml version="1.0" encoding="UTF-8" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8; no-cache" />
	<link rel="stylesheet" type="text/css" href="css/desinventar.css?version={-$jsversion-}" />
	<link rel="stylesheet" type="text/css" href="css/datacards.css?version={-$jsversion-}" />
	{-include file="jquery.tpl" -}
	<script type="text/javascript" src="{-$desinventarURL-}/include/prototype.js"></script>
	
	<script type="text/javascript" src="{-$desinventarURL-}/external/swfupload/swfupload.js"></script>
	<script type="text/javascript" src="{-$desinventarURL-}/external/jquery.swfupload.js"></script>
	<script type="text/javascript" src="{-$desinventarURL-}/js/jquery.snippets.js?version={-$jsversion-}"></script>
	<script type="text/javascript" src="{-$desinventarURL-}/js/database_import.js?version={-$jsversion-}"></script>
	<script type="text/javascript" src="{-$desinventarURL-}/js/datacards.js?version={-$jsversion-}"></script>
	<script type="text/javascript">
		jQuery(document).ready(function() {
			//onReadyDatabaseImport();
			onReadyDatacards();
		});
	</script>
</head>
<body>
{-include file="datacards.tpl" -}
{-include file="database_import2.tpl" -}
{-include file="desinventarinfo.tpl"-}
</body>
</html>
