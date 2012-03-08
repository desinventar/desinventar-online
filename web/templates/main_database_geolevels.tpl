<!DOCTYPE HTML>
<html>
	<head>
		<meta charset="UTF-8" />
		<title>DesInventar</title>
		{-include file="jquery.tpl"-}
		{-include file="extjs.tpl"-}
		<link rel="stylesheet" type="text/css" href="{-$desinventarURL-}/css/desinventar.css?version={-$jsversion-}" />

		<script type="text/javascript" src="/fileuploader/fileuploader.js"></script>

		<script type="text/javascript" src="{-$desinventarURL-}/external/json2.js"></script>
		<script type="text/javascript" src="{-$desinventarURL-}/external/form2js.js"></script>
		<script type="text/javascript" src="{-$desinventarURL-}/external/js2form.js"></script>
		<script type="text/javascript" src="{-$desinventarURL-}/external/jquery.toObject.js"></script>
		<script type="text/javascript" src="{-$desinventarURL-}/js/common.js?version={-$jsversion-}"></script>
		<script type="text/javascript" src="{-$desinventarURL-}/js/database_geolevels.js?version={-$jsversion-}"></script>

		<script type="text/javascript">
			jQuery(document).ready(function() {
				// 2011-04-29 (jhcaiced) Fix for use of ExtJS in IE9 ?
				if ((typeof Range !== "undefined") && !Range.prototype.createContextualFragment)
				{
					Range.prototype.createContextualFragment = function(html)
					{
						var frag = document.createDocumentFragment(), div = document.createElement("div");
						frag.appendChild(div);
						div.outerHTML = html;
						return frag;
					};
				}
				onReadyCommon();
				onReadyGeolevels();
				jQuery('body').trigger('cmdGeolevelsShow');
			});
		</script>
	</head>
	<body>
		{-include file="database_geolevels.tpl"-}
		{-include file="desinventarinfo.tpl"-}
	</body>
</html>
