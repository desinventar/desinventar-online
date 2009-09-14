{-config_load file=`$lg`.conf section="di8_events"-}
{-** EVENTS: Interface to Edit Custom and Predefined events lists.. **-}
{-if $ctl_show-}
<?xml version="1.0" encoding="UTF-8" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8; no-cache" />
</head>
<body>
	<b onMouseOver="showtip('{-$dic.DBEvent[2]-}');">{-$dic.DBEvent[0]-}</b>
	<br>
	<div class="dwin" style="width:600px; height:100px;">
		<table class="grid">
			<thead>
				<tr>
					<td class="header" onMouseOver="showtip('{-$dic.DBEvePersonName[2]-}');">
						<b>{-$dic.DBEvePersonName[0]-}</b></td>
					<td class="header" onMouseOver="showtip('{-$dic.DBEvePersonDef[2]-}');">
						<b>{-$dic.DBEvePersonDef[0]-}</b></td>
					<td class="header" onMouseOver="showtip('{-$dic.DBEveActive[2]-}');">
						<b>{-$dic.DBEveActive[0]-}</b></td>
				</tr>
			</thead>
			<tbody id="lst_eveuser">
{-/if-}
{-if $ctl_evepers-}
   {-foreach name=eve key=key item=item from=$eveuserl-}
				<tr class="{-if ($smarty.foreach.eve.iteration - 1) % 2 == 0-}normal{-else-}under{-/if-}"
					onMouseOver="Element.addClassName(this, 'highlight');" onMouseOut="Element.removeClassName(this, 'highlight');"
					onClick="setEveCau('{-$key-}','{-$item[0]-}','{-$item[1]-}','{-$item[2]-}','0','event'); 
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
					<td class="header" onMouseOver="showtip('{-$dic.DBEvePredefName[2]-}');">
						<b>{-$dic.DBEvePredefName[0]-}</b></td>
					<td class="header" onMouseOver="showtip('{-$dic.DBEvePredefDef[2]-}');">
						<b>{-$dic.DBEvePredefDef[0]-}</b></td>
					<td class="header" onMouseOver="showtip('{-$dic.DBEveActive[2]-}');">
						<b>{-$dic.DBEveActive[0]-}</b></td>
				</tr>
			</thead>
			<tbody id="lst_evepred">
{-/if-}
{-if $ctl_evepred-}
   {-foreach name=eve key=key item=item from=$evepredl-}
				<tr class="{-if ($smarty.foreach.eve.iteration - 1) % 2 == 0-}normal{-else-}under{-/if-}"
						onMouseOver="Element.addClassName(this, 'highlight');" onMouseOut="Element.removeClassName(this, 'highlight');"
						onClick="setEveCau('{-$key-}','{-$item[0]-}','{-$item[1]-}','{-$item[2]-}','1','event'); uploadMsg('');
													$('cmd').value='update';">
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
	<input id="add" type="button" value="{-#baddelem#-}" class="line"
		onclick="setEveCau('','','','1','0','event'); uploadMsg(''); $('cmd').value='insert';">
	<span id="eventstatusmsg" class="dlgmsg"></span>
	<br><br>
	<div id="eventaddsect" style="display:none">
		<form id="eventfrm" name="eventfrm" method="GET" 
			action="javascript:var s=$('eventfrm').serialize(); sendData('{-$reg-}', 'events.php', s, $('aEventPreDefined').value);"
			onSubmit="javascript: var a=new Array('aEventName','aEventDesc'); return(checkForm(a, '{-#errmsgfrm#-}'));">
			<input id="r" name="r" type="hidden" value="{-$reg-}">
			{-$dic.DBEvePersonName[0]-}<b style="color:darkred;">*</b><br>
			<input id="aEventName" name="EventName" type="text" class="line" maxlength="40" style="width:500px;" {-$ro-}
					onBlur="updateList('eventstatusmsg', 'events.php', 'r={-$reg-}&cmd=chkname&EventId='+ $('EventId').value +'&EventName='+ $('aEventName').value);"
					onFocus="showtip('{-$dic.DBEvePersonName[2]-}');">
			<br><br>
			{-$dic.DBEvePersonDef[0]-}<b style="color:darkred;">*</b><br>
			<textarea id="aEventDesc" name="EventDesc" class="line" rows="2" style="width:500px;"
					onFocus="showtip('{-$dic.DBEvePersonDef[2]-}');" {-$ro-}></textarea>
			<input id="aEventDesc2" name="EventDesc2" type="hidden">
			<br><br>
			{-$dic.DBEveActive[0]-}
			<input id="aEventActive" name="EventActive" type="checkbox" {-$ro-} 
					onFocus="showtip('{-$dic.DBEveActive[2]-}');"
					onClick="if (!this.checked) updateList('eventstatusmsg', 'events.php', 'r={-$reg-}&cmd=chkstatus&EventId='+ $('EventId').value);">
			<br><br>
			<p class="line">
				<input id="aEventId" name="EventId" type="hidden">
				<input id="aEventPreDefined" name="EventPreDefined" type="hidden">
				<input id="cmd" name="cmd" type="hidden">
				<input type="submit" value="{-#bsave#-}" {-$ro-} class="line">
				<input type="reset" value="{-#bcancel#-}" onClick="$('eventaddsect').style.display='none'; uploadMsg('');" {-$ro-} class="line">
			</p>
		</form>
	</div>
</body>
</html>
{-/if-}

{-** INFO AND ERRORS MESSAGES **-}
{-if $ctl_msginseve-}
 {-#msginseve#-}
{-elseif $ctl_errinseve-}
 {-#terror#-}[{-$insstateve-}]: 
{-elseif $ctl_msgupdeve-}
 {-#msgupdeve#-}
{-elseif $ctl_errupdeve-}
 {-#terror#-}[{-$updstateve-}]:
{-/if-}
 
{-*** CHECK Event-Availability MESSAGES - STATUS SPAN ***-}
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
