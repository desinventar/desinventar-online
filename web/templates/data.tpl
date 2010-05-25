{-config_load file=`$lg`.conf section="dc_data"-}
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
	{-include file="jquery.tpl" -}
    <script type="text/javascript">
	function setDIForm(prmDisasterId) {
		parent.w.collapse();
		parent.difw.show();
		setDICardfromId('{-$reg-}', prmDisasterId, 'DATA');
		jQuery('#PrevDisasterSerial').val(jQuery('#DisasterSerial').val());
		{-if $role == 'USER' || $role == 'SUPERVISOR' || $role == 'ADMINREGION'-}
			cupd = window.parent.frames['dif'].document.getElementById('cardupd');
			if (cupd != null) {
				cupd.enable();
				Element.addClassName(cupd, 'bb');
				Element.removeClassName(cupd, 'disabled');
			}
		{-/if-}
	}
	function displayPage(page) {
		var mypag = page;
		now = parseInt($('pp').value);
		if (page == 'prev')
			mypag = now - 1;
		else if (page == 'next')
			mypag = now + 1;
		if (mypag < 1 || mypag > {-$NumberOfPages-})
			return false;
		$('pp').value = mypag ;
		var lsAjax = new Ajax.Updater('lst_dis', 'data.php', {
			method: 'post', parameters: 'r={-$reg-}&page='+ mypag +'&RecordsPerPage={-$RecordsPerPage-}&sql={-$sql-}&fld={-$fld-}',
			onLoading: function(request) {
				$(div).innerHTML = "<img src='loading.gif>";
			}
		} );
	}
	window.onload = function() {
		var qrydet = parent.document.getElementById('querydetails');
		var qdet = "";
		{-foreach key=k item=i from=$qdet-}
			{-if $k == "GEO"-}qdet += "<b>{-#geo#-}:</b> {-$i-} ";{-/if-}
			{-if $k == "EVE"-}qdet += "<b>{-#eve#-}:</b> {-$i-} ";{-/if-}
			{-if $k == "CAU"-}qdet += "<b>{-#cau#-}:</b> {-$i-} ";{-/if-}
			{-if $k == "EFF"-}qdet += "<b>{-#eff#-}:</b> {-$i-} ";{-/if-}
			{-if $k == "BEG"-}qdet += "<b>{-#beg#-}:</b> {-$i-} ";{-/if-}
			{-if $k == "END"-}qdet += "<b>{-#end#-}:</b> {-$i-} ";{-/if-}
			{-if $k == "SOU"-}qdet += "<b>{-#sou#-}:</b> {-$i-} ";{-/if-}
			{-if $k == "SER"-}qdet += "<b>{-#ser#-}:</b> {-$i-} ";{-/if-}
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
				<a href="javascript:void(null)" onclick="displayPage({-$NumberOfPages-});">{-$NumberOfPages-}</a>
				&nbsp;&nbsp;|&nbsp;&nbsp;
				<input type="button" id="first" value="<<" class="line" onClick="displayPage(1)" />
				<input type="button" id="prev"  value="<"  class="line" onClick="displayPage('prev')" />
				<input type="button" id="next"  value=">"  class="line" onClick="displayPage('next')" />
				<input type="button" id="last"  value=">>" class="line" onClick="displayPage({-$NumberOfPages-})" />
			</td>
			<td align="center">
				<span id="datstatusmsg" class="dlgmsg"></span><!--{-$time-}s-->
			</td>
			<td align="right">
				{-#trepnum#-}: {-$tot-}
			</td>
		</tr>
	</table>
	<table width="930px" class="col">
		<thead>
			<tr>
				<th class="header">{-#trow#-}</th>
				{-foreach name=sel key=key item=item from=$sel-}
					{-strip-}
						{-if $item != "DisasterId"-}
							<th class="header">{-$dk.$item-}
							</th>
						{-/if-}
					{-/strip-}
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
					<td>
						<a href="javascript:void(null);" onClick="setDIForm('{-$item.DisasterId-}');">{-$offset+$smarty.foreach.dl.iteration-}</a>
					</td>
					{-foreach name=sel key=k item=i from=$sel-}
						{-strip-}
							{-if $i != "DisasterId"-}
								<td {-if $i=="DisasterSerial" || $i=="DisasterBeginTime" || $i=="EventName" || $i=="GeographyFQName" || 
								         $i=="DisasterSiteNotes" || $i=="DisasterSource" || $i=="EffectNotes" || $i=="EffectOtherLosses" || $i=="CauseName" || $i=="CauseNotes"-}
								         class="GridCellText"
									{-else-}
										class="GridCellNumber"
									{-/if-}>
									{-if $i=="EffectNotes" || $i=="EffectOtherLosses" || $i=="EventNotes" || $i=="CauseNotes"-}
										<div class="dwin" style="width:200px; height: 40px;">{-$item[$i]-}
										</div>
									{-elseif $i=="DisasterSource" || $i=="DisasterSiteNotes"-}
										<div class="dwin" style="width:150px; height: 40px;">{-$item[$i]-}
										</div>
									{-elseif $item[$i] == -1-}
										<input type="checkbox" checked disabled />
									{-elseif $item[$i] == -2-}?
									{-else-}
										{-$item[$i]-}
									{-/if-}
								</td>
							{-/if-}
						{-/strip-}
					{-/foreach-}
				</tr>
			{-/foreach-}
{-/if-}
{-if $ctl_showres-}
		</tbody>
	</table>
</body>
</html>
{-/if-}
