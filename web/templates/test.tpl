{-config_load file=`$lg`.conf section="di8_index"-}
<?xml version="1.0" encoding="UTF-8" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8; no-cache" />
	<link rel="stylesheet" type="text/css" href="css/desinventar.css?version={-$jsversion-}" />
	{-include file="jquery.tpl" -}
	<script type="text/javascript" src="js/database_create.js?version={-$jsversion-}"></script>
	<script type="text/javascript" src="external/swfupload/swfupload.js"></script>
	<script type="text/javascript" src="external/jquery.swfupload.js"></script>
	<script type="text/javascript">
		jQuery(document).ready(function() {
			onReadyDatabaseCreate();
		});
	</script>
</head>
<body>
{-include file="database_create.tpl"-}
{-include file="desinventarinfo.tpl"-}
</body>
</html>

