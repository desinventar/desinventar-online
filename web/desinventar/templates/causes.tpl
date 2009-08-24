{-config_load file=`$lg`.conf section="di8_causes"-}
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
	<br>
	<div class="dwin" style="width:600px; height:100px;">
		<table class="grid">
			<thead>
				<tr>
					<td class="header" onMouseOver="showtip('{-$dic.DBCauPersonName[2]-}');">
						<b>{-$dic.DBCauPersonName[0]-}</b></td>
					<td class="header" onMouseOver="showtip('{-$dic.DBCauPersonDef[2]-}');">
						<b>{-$dic.DBCauPersonDef[0]-}</b></td>
					<td class="header" onMouseOver="showtip('{-$dic.DBCauActive[2]-}');">
						<b>{-$dic.DBCauActive[0]-}</b></td>
				</tr>
			</thead>
			<tbody id="lst_cauuser">
{-/if-}
{-if $ctl_caupers-}
   {-foreach name=cau key=key item=item from=$cauuserl-}
				<tr class="{-if ($smarty.foreach.cau.iteration - 1) % 2 == 0-}normal{-else-}under{-/if-}" 
					onMouseOver="Element.addClassName(this, 'highlight');" onMouseOut="Element.removeClassName(this, 'highlight');"
					onClick="setEveCau('{-$key-}','{-$item[0]-}','{-$item[1]-}','{-$item[2]-}','0','cause'); 
							uploadMsg(''); $('cmd').value='update';">
   				<td>{-$item[0]-}</td>
   				<td>{-$item[1]|truncate:150-}</td>
   				<td><input type="checkbox" {-if ($item[2] == 1) -} checked {-/if-} disabled></td>
   			</tr>
   {-/foreach-}
{-/if-}
{-if $ctl_show-}
			</tbody>
		</table>
	</div>
	<br>
	<div class="dwin" style="width:600px; height:100px;">
		<table width="100%" class="grid">
			<thead>
				<tr>
					<td class="header" onMouseOver="showtip('{-$dic.DBCauPredefName[2]-}');">
						<b>{-$dic.DBCauPredefName[0]-}</b></td>
					<td class="header" onMouseOver="showtip('{-$dic.DBCauPredefDef[2]-}');">
						<b>{-$dic.DBCauPredefDef[0]-}</b></td>
					<td class="header" onMouseOver="showtip('{-$dic.DBCauActive[2]-}');">
						<b>{-$dic.DBCauActive[0]-}</b></td>
				</tr>
			</thead>
			<tbody id="lst_caupred">
{-/if-}
{-if $ctl_caupred-}
   {-foreach name=cau key=key item=item from=$caupredl-}
				<tr class="{-if ($smarty.foreach.cau.iteration - 1) % 2 == 0-}normal{-else-}under{-/if-}" 
					onMouseOver="Element.addClassName(this, 'highlight');" onMouseOut="Element.removeClassName(this, 'highlight');"
					onClick="setEveCau('{-$key-}','{-$item[0]-}','{-$item[1]-}','{-$item[2]-}','1','cause'); 
											uploadMsg(''); $('cmd').value='update';">
					<td>{-$item[0]-}</td>
					<td>{-$item[1]|truncate:150-}</td>
					<td><input type="checkbox" {-if ($item[2] == 1) -} checked {-/if-} disabled></td>
   			</tr>
   {-/foreach-}
{-/if-}
{-if $ctl_show-}
			</tbody>
		</table>
	</div>
	<br><br>
	<input id="add" type="button" value="{-#baddelem#-}" class="line"
  		onclick="setEveCau('','','','1','0','cause'); uploadMsg(''); $('cmd').value='insert';">
	<span id="causestatusmsg" class="dlgmsg"></span>
	<br><br>
	<div id="causeaddsect" style="display:none">
		<form id="causefrm" name="causefrm" method="GET" 
			action="javascript:var s=$('causefrm').serialize(); sendData('{-$reg-}', 'causes.php', s, $('aCausePreDefined').value);"
			onSubmit="javascript: var a=new Array('aCauseName','aCauseDesc'); return(checkForm(a, '{-#errmsgfrm#-}'));">
			<input id="r" name="r" type="hidden" value="{-$reg-}">
			{-$dic.DBCauPersonName[0]-}<b style="color:darkred;">*</b><br>
			<input id="aCauseName" name="CauseName" type="text" maxlength="40" {-$ro-} class="line" style="width:500px;"
					onBlur="updateList('causestatusmsg', 'causes.php', 'r={-$reg-}&cmd=chkname&CauseId='+ $('CauseId').value +'&CauseName='+ $('aCauseName').value);"
					onFocus="showtip('{-$dic.DBCauPersonName[2]-}');">
			<br><br>
			{-$dic.DBCauPersonDef[0]-}<b style="color:darkred;">*</b><br>
			<textarea id="aCauseDesc" name="CauseDesc" rows="2" style="width:500px;"
					onFocus="showtip('{-$dic.DBCauPersonDef[2]-}');" {-$ro-}></textarea>
			<input id="aCauseDesc2" name="CauseDesc2" type="hidden">
			<br><br>
			{-$dic.DBCauActive[0]-} 
			<input id="aCauseActive" name="CauseActive" type="checkbox" {-$ro-} 
					onFocus="showtip('{-$dic.DBCauActive[2]-}');"
					onClick="javascript:if (!this.checked) updateList('causestatusmsg', 'causes.php', 'r={-$reg-}&cmd=chkstatus&CauseId='+
										$('aCauseId').value);">
			<br><br>
			<p align="center" style="width:500px;">
				<input id="aCauseId" name="CauseId" type="hidden">
				<input id="aCausePreDefined" name="CausePreDefined" type="hidden">
				<input id="cmd" name="cmd" type="hidden">
				<input type="submit" id="sendform" name="sendform" value="{-#bsave#-}" {-$ro-} class="line">
				<input type="reset" value="{-#bcancel#-}" 
						onClick="uploadMsg(''); $('causeaddsect').style.display='none';" {-$ro-} class="line">
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
