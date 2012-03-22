<!DOCTYPE HTML>
<html>
	<head>
		<meta charset="UTF-8" />
		<title>DesInventar</title>
		{-include file="jquery.tpl"-}
		{-include file="extjs.tpl"-}
		<link rel="stylesheet" type="text/css" href="{-$desinventarURL-}/css/desinventar.css?version={-$jsversion-}" />

		<script type="text/javascript" src="{-$desinventarURL-}/js/common.js?version={-$jsversion-}"></script>
		<script type="text/javascript" src="{-$desinventarURL-}/js/admin_users.js?version={-$jsversion-}"></script>

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
				onReadyAdminUsers();
				jQuery('div.AdminUsers').trigger('cmdLoadData');
			});
		</script>
	</head>
	<body>
		{-include file="admin_users.tpl"-}
		{-include file="desinventarinfo.tpl"-}
	</body>
</html>
