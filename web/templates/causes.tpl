{-config_load file="$lg.conf" section="grpAdminCauses"-}
{-** CAUSES: Interface to Edit Custom and Predefined causes lists.. **-}
{-if $ctl_show-}
<?xml version="1.0" encoding="UTF-8" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8; no-cache" />
</head>
<body>
	<b onMouseOver="showtip('{-$dic.DBCause[2]-}');">{-$dic.DBCause[0]-}</b>
	<br />
	<div class="dwin" style="width:600px; height:100px;">
		<table id="tblCauseListUser" class="grid">
			<thead>
				<tr>
					<td style="display:none;">
						Id
					</td>
					<td style="display:none;">
						Predefined
					</td>
					<td class="header" onMouseOver="showtip('{-$dic.DBCauPersonName[2]-}');">
						<b>{-$dic.DBCauPersonName[0]-}</b>
					</td>
					<td class="header" onMouseOver="showtip('{-$dic.DBCauPersonDef[2]-}');">
						<b>{-$dic.DBCauPersonDef[0]-}</b>
					</td>
					<td class="header" onMouseOver="showtip('{-$dic.DBCauActive[2]-}');">
						<b>{-$dic.DBCauActive[0]-}</b>
					</td>
				</tr>
			</thead>
			<tbody id="lst_cauuser">
{-/if-}
				{-if $ctl_caupers-}
					{-foreach name=cau key=key item=item from=$cauuserl-}
						<tr class="{-if ($smarty.foreach.cau.iteration - 1) % 2 == 0-}normal{-else-}under{-/if-}" >
							<td id="Id" style="display:none;">{-$key-}</td>
							<td id="Predefined" style="display:none;">0</td>
							<td id="Name">{-$item[0]-}</td>
							<td id="Desc">{-$item[1]|truncate:150-}</td>
							<td><input id="Active" type="checkbox" {-if ($item[2]==1)-} checked {-/if-} disabled /></td>
						</tr>
					{-/foreach-}
				{-/if-}
{-if $ctl_show-}
			</tbody>
		</table>
	</div>
	<br />
	<div class="dwin" style="width:600px; height:100px;">
		<table id="tblCauseListPredef" width="100%" class="grid">
			<thead>
				<tr>
					<td style="display:none;">
						Id
					</td>
					<td style="display:none;">
						Predefined
					</td>
					<td class="header" onMouseOver="showtip('{-$dic.DBCauPredefName[2]-}');">
						<b>{-$dic.DBCauPredefName[0]-}</b>
					</td>
					<td class="header" onMouseOver="showtip('{-$dic.DBCauPredefDef[2]-}');">
						<b>{-$dic.DBCauPredefDef[0]-}</b>
					</td>
					<td class="header" onMouseOver="showtip('{-$dic.DBCauActive[2]-}');">
						<b>{-$dic.DBCauActive[0]-}</b>
					</td>
				</tr>
			</thead>
			<tbody id="lst_caupred">
{-/if-}
				{-if $ctl_caupred-}
					{-foreach name=cau key=key item=item from=$caupredl-}
						<tr class="{-if ($smarty.foreach.cau.iteration - 1) % 2 == 0-}normal{-else-}under{-/if-}">
							<td id="Id" style="display:none;">{-$key-}</td>
							<td id="Predefined" style="display:none;">1</td>
							<td id="Name">{-$item[0]-}</td>
							<td id="Desc">{-$item[1]|truncate:150-}</td>
							<td><input id="Active" type="checkbox" {-if ($item[2]==1)-} checked {-/if-} disabled /></td>
						</tr>
					{-/foreach-}
				{-/if-}
{-if $ctl_show-}
			</tbody>
		</table>
	</div>
	<br />
	<a class="button" id="btnCauseEditAdd"><span>{-#baddelem#-}</span></a>
	<span id="causestatusmsg" class="dlgmsg"></span>
	<br /><br />
	<div id="causeaddsect" style="display:none;width:600px;">
		<form id="frmCauseEdit">
			<input id="Id" name="Info[CauseId]" type="hidden" />
			{-$dic.DBCauPersonName[0]-}<b style="color:darkred;">*</b><br />
			<input id="Name" name="Info[CauseName]" type="text" class="line" maxlength="40" style="width:500px;" tabindex="1" 
				onBlur="updateList('causestatusmsg', jQuery('#desinventarURL').val() + '/causes.php', 'r={-$reg-}&cmd=chkname&CauseId='+ $('aCauseId').value +'&CauseName='+ $('aCauseName').value);"
				onFocus="showtip('{-$dic.DBCauPersonName[2]-}');" />
			<br /><br />
			{-$dic.DBCauPersonDef[0]-}<b style="color:darkred;">*</b><br />
			<textarea id="Desc" name="Info[CauseDesc]" class="line" rows="2" style="width:500px;" tabindex="2" 
				onFocus="showtip('{-$dic.DBCauPersonDef[2]-}');"></textarea>
			<br /><br />
			{-$dic.DBCauActive[0]-} 
			<input id="Active" name="Info[CauseActive]" type="checkbox" 
				onFocus="showtip('{-$dic.DBCauActive[2]-}');" tabindex="3" />
			<br /><br />
			<input id="Predefined" name="Info[CausePredefined]" type="hidden" />
			<p align="center" style="width:500px;">
				<input id="r" name="r" type="hidden" value="{-$reg-}" />
				<input id="cmd" name="cmd" type="hidden" value="" />
				<div class="center">
					<a class="button" id="btnCauseEditSend" tabindex="4"><span>{-#bsave#-}</span></a>
					<a class="button" id="btnCauseEditCancel" tabindex="5"><span>{-#bcancel#-}</span></a>
				</div>
			</p>
		</form>
	</div>
</body>
</html>
{-/if-}

{-** INFO AND ERRORS MESSAGES **-}
{-if $ctl_msginscau-}
 {-#msginscau#-}
{-elseif $ctl_errinscau-}
 {-#terror#-}[{-$insstatcau-}]: {-#errinscau#-}
{-elseif $ctl_msgupdcau-}
 {-#msgupdcau#-}
{-elseif $ctl_errupdcau-}
 {-#terror#-}[{-$updstatcau-}]: {-#errupdcau#-}
{-/if-}
 
{-*** CHECK Causes-Availability MESSAGES - STATUS SPAN ***-}
{-if $ctl_chkname-}
 {-if !$chkname-}
 	 - {-#errchkname#-}
 {-/if-}
{-/if-}

{-if $ctl_chkstatus-}
 {-if !$chkstatus-}
 	 - {-#errchkstatus#-}
 {-/if-}
{-/if-}
