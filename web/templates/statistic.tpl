{-config_load file=`$lg`.conf section="dc_statistic"-}
{-config_load file=`$lg`.conf section="dc_qdetails"-}
{-if $ctl_showres-}
<?xml version="1.0" encoding="UTF-8" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8; no-cache" />
	<link rel="stylesheet" href="css/desinventar.css?version={-$jsversion-}" type="text/css"/>
	<script type="text/javascript" src="include/prototype.js"></script>
	<script type="text/javascript" src="js/diadmin.js?version={-$jsversion-}"></script>
	<script type="text/javascript">
		function displayPage(page) {
			var mypag = page;
			now = parseInt($('pp').value);
			if (page == 'prev')
				mypag = now - 1;
			else if (page == 'next')
				mypag = now + 1;
			if (mypag < 1 || mypag > {-$last-})
				return false;
			$('pp').value = mypag ;
			var lsAjax = new Ajax.Updater('lst_dis', 'statistic.php', {
				method: 'post', parameters: 'r={-$reg-}&page='+ mypag +'&rxp={-$rxp-}&sql={-$sql-}&fld={-$fld-}&geo={-$geo-}',
				onLoading: function(request) {
					$(div).innerHTML = "<img src='loading.gif>";
				}
			} );
		}
		function orderByField(field, dir) {
			var lsAjax = new Ajax.Updater('lst_dis', 'statistic.php', {
				method: 'post', 
				parameters: 'r={-$reg-}&page='+ $('pp').value +'&rxp={-$rxp-}&sql={-$sql-}&fld={-$fld-}&ord='+ field +'&geo={-$geo-}&dir='+ dir,
				onLoading: function(request) {
					$(div).innerHTML = "<img src='loading.gif>";
				}
			} );
		}
		window.onload = function() {
			var qrydet = parent.document.getElementById('querydetails');
			var qdet = "";
			{-foreach key=k item=i from=$qdet-}
				{-if $k == "GEO"-}qdet += "<b>{-#geo#-}:</b> {-$i-}";{-/if-}
				{-if $k == "EVE"-}qdet += "<b>{-#eve#-}:</b> {-$i-}";{-/if-}
				{-if $k == "CAU"-}qdet += "<b>{-#cau#-}:</b> {-$i-}";{-/if-}
				{-if $k == "EFF"-}qdet += "<b>{-#eff#-}:</b> {-$i-}";{-/if-}
				{-if $k == "BEG"-}qdet += "<b>{-#beg#-}:</b> {-$i-}";{-/if-}
				{-if $k == "END"-}qdet += "<b>{-#end#-}:</b> {-$i-}";{-/if-}
				{-if $k == "SOU"-}qdet += "<b>{-#sou#-}:</b> {-$i-}";{-/if-}
				{-if $k == "SER"-}qdet += "<b>{-#ser#-}:</b> {-$i-}";{-/if-}
			{-/foreach-}
			qrydet.innerHTML = qdet;
		}
	</script>
</head>
<body>
	<table width="920px" class="grid">
		<tr>
			<td>
				{-#tpage#-}
				<input type="text" id="pp" size="2" value="1" class="line" onKeyDown="if(event.keyCode==13) displayPage(this.value);"
					onkeypress="return blockChars(event, this.value, 'integer:');">
				&nbsp; {-#tnumof#-} &nbsp;
				<a href="javascript:void(null)" onclick="displayPage({-$last-});">{-$last-}</a>
				&nbsp;&nbsp;|&nbsp;&nbsp;
				<input type="button" id="first" value="<<" class="line" onClick="displayPage(1)" />
				<input type="button" id="prev"  value="<"  class="line" onClick="displayPage('prev')" />
				<input type="button" id="next"  value=">"  class="line" onClick="displayPage('next')" />
				<input type="button" id="last"  value=">>" class="line" onClick="displayPage({-$last-})" />
			</td>
			<td align="center">
				<span id="stdstatusmsg" class="dlgmsg"></span>
			</td>
			<td align="right">
				{-#tsumnum#-}: {-$cou-} | {-#trepnum#-}: {-$tot-}
			</td>
		</tr>
	</table>
	<table width="930px" height="95%" class="col">
		<thead>
			<tr>
				<th class="header">{-#trow#-}
				</th>
				{-foreach name=sel key=key item=item from=$sel-}
					<th class="header">
						<table cellpadding=0 cellspacing=0 border=0>
							<tr>
								<td>
									<a href="javascript:void(null)" onclick="orderByField('{-$item-}', 'ASC');"><img src="images/asc.gif" border=0></a>
								</td>
								<td>
									{-if $item =="DisasterId_"-}{-#trepnum#-}{-elseif $item != "DisasterId"-}{-$dk.$item-}{-/if-}
								</td>
								<td>
									<a href="javascript:void(null)" onclick="orderByField('{-$item-}', 'DESC');"><img src="images/desc.gif" border=0></a>
								</td>
							</tr>
						</table>
					</th>
				{-/foreach-}
			</tr>
			<tr>
				<th style="border: thin solid; text-align: right;">{-#ttotals#-}
				</th>
				{-foreach name=sel key=key item=item from=$sel-}
					{-if $item != "DisasterId"-}
						<th style="border: thin solid; text-align: right;">
							{-if $item != $gp[0] && $item != $gp[1] && $item != $gp[2]-} {-$dlt.$item-}{-/if-}
						</th>
					{-/if-}
				{-/foreach-}
			</tr>
		</thead>
		<tbody id="lst_dis">
{-/if-}
			{-*** SHOW RESULT LIST: PAGING ***-}
{-if $ctl_dislist-}
			{-foreach name=dl key=key item=item from=$dislist-}
				<tr class="{-if ($smarty.foreach.dl.iteration - 1) % 2 == 0-}normal{-else-}under{-/if-}" 
					onClick="Element.addClassName(this, 'highlight');" ondblClick="Element.removeClassName(this, 'highlight');">
					<td>{-$offset+$smarty.foreach.dl.iteration-}
					</td>
					{-strip-}
						{-foreach name=sel key=k item=i from=$sel-}
							{-if $i != "DisasterId"-}
								<td {-if $i=="GeographyId_0" || $i=="GeographyId_1" || $i=="GeographyId_2" || 
								         $i=="EventName" || $i=="CauseName" -}
										class="GridCellText"
									{-else-}
										class="GridCellNumber"
									{-/if-}>{-$item[$i]-}
								</td>
							{-/if-}
						{-/foreach-}
					{-/strip-}
				</tr>
			{-/foreach-}
{-/if-}
{-if $ctl_showres-}
		</tbody>
	</table>
</body>
</html>
{-/if-}
