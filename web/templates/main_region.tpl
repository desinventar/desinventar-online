<!DOCTYPE HTML>
<html>
	<head>
		<meta charset="UTF-8" />
		<title>DesInventar</title>
		{-include file="jquery.tpl"-}
		<script type="text/javascript" src="{-$desinventarURL-}/external/prototype.js"></script>
		{-include file="extjs.tpl"-}

		<link rel="stylesheet" type="text/css" href="{-$desinventarURL-}/css/desinventar.css?version={-$jsversion-}" />
		<script type="text/javascript" src="{-$desinventarURL-}/js/common.js?version={-$jsversion-}"></script>
		<script type="text/javascript" src="/jquery/jquery.ba-bbq.min.js"></script>
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
				jQuery(window).bind('hashchange', function(e) {
					var url = jQuery.param.fragment();
					//var url = e.fragment;
				});
				jQuery(window).trigger('hashchange');
			});
		</script>
	</head>
	<body>
		DesInventar Database Admin
		Version : {-$jsversion-}<br />
		Language : {-$lg-}<br />
		<br />
		<a class="button" id="btnTest"><span>Test</span></a>
		<a href="#datacards">Datacards</a>
		<a href="#users">Users</a>
		<a href="#inicio">Inicio</a>
		{-include file="desinventarinfo.tpl"-}
	</body>
</html>
