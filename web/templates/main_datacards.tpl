<!DOCTYPE HTML>
<html>
	<head>
		<meta charset="UTF-8" />
		<title>DesInventar</title>
		{-include file="jquery.tpl"-}
		{-include file="extjs.tpl"-}
		<link rel="stylesheet" type="text/css" href="{-$desinventarURL-}/css/desinventar.css?version={-$jsversion-}" />
		<link rel="stylesheet" type="text/css" href="{-$desinventarURL-}/css/main.css?version={-$jsversion-}" />
		<script type="text/javascript" src="{-$desinventarURL-}/js/common.js?version={-$jsversion-}"></script>
		<script type="text/javascript" src="{-$desinventarURL-}/js/datacards.js?version={-$jsversion-}"></script>

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
				jQuery.post(
					jQuery('#desinventarURL').val() + '/',
					{
						cmd      : 'cmdDatabaseLoadData',
						RegionId : jQuery('#desinventarRegionId').val()
					},
					function(data)
					{
						if (parseInt(data.Status) > 0)
						{
							jQuery('body').data('GeolevelsList', data.GeolevelsList);
							jQuery('body').data('EventList', data.EventList);
							jQuery('body').data('CauseList', data.CauseList);
							jQuery('body').data('RecordCount', data.RecordCount);
							var dataItems = jQuery('body').data();
							jQuery.each(dataItems, function(index, value) {
								if (index.substr(0,13) === 'GeographyList')
								{
									jQuery('body').removeData(index);
								}
							});
							jQuery('body').data('GeographyList', data.GeographyList);

							onReadyCommon();
							onReadyDatacards();
							jQuery('body').trigger('cmdDatacardShow');
						}
					},
					'json'
				);
			});
		</script>
	</head>
	<body>
		{-include file="datacards.tpl"-}
		{-include file="desinventarinfo.tpl"-}
		{-include file="block_help.tpl"-}
	</body>
</html>
