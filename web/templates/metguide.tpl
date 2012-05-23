{-config_load file="$lg.conf" section="di_doc"-}
<!DOCTYPE HTML>
<html>
	<head>
		<meta charset="UTF-8" />
		<title>DesInventar - {-#metguide_Title#-}</title>
		<script type="text/javascript" src="/jquery/jquery.min.js"></script>
		<script type="text/javascript">
			jQuery(document).ready(function() {
				jQuery('a.page').on('click', function(event) {
					jQuery('div.page').hide();
					jQuery('div.page[id="' + jQuery(this).attr('id') + '"]').show();
					event.stopPropagation();
				});
			});
		</script>
	</head>
	<body>
		<table width="100%">
			<tr>
				<td valign="top" style="width:180px;">
					<h3>{-#metguide_Title#-}</h3>
					{-foreach $metguide as $key => $page-}
						<li><a class="page" href="#" id="{-$key-}">{-$page.DictTranslation-}</a></li>
					{-/foreach-}
				</td>
				<td valign="top">
					{-foreach $metguide as $key => $page-}
						<div class="page" id="{-$key-}" style="display:none;">
							<h4>{-$page.DictTranslation-}</h4>
							<p class="justify"><i>{-$page.DictBastDesc-}</i></p>
							<p class="justify">{-$page.DictFullDesc-}</p>
						</div>
					{-/foreach-}
				</td>
			</tr>
		</table>
	</body>
</html>
