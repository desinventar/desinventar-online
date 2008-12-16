{-config_load file=`$lg`.conf section="di8_reginfo"-}
{-** REGIONINFO: Interface to Edit Info over Region.. **-}
{-if $ctl_adminreg-}
<!-- LOG RECORDS -->
	<b onMouseOver="showtip('{-$dic.DBLog[2]-}');">{-$dic.DBLog[0]-}</b><br>
	<div class="dwin" style="width:280px; height:200px;">
		<table width="100%" class="grid">
   	 <thead>
   		<tr>
   			<td class="header" onMouseOver="showtip('{-$dic.DBLogType[2]-}');">
   				<b>{-$dic.DBLogType[0]-}</b>
   			</td>
   			<td class="header" onMouseOver="showtip('{-$dic.DBLogNote[2]-}');">
   				<b>{-$dic.DBLogNote[0]-}</b>
   			</td>
   		</tr>
   	 </thead>
   	 <tbody id="lst_log">
{-/if-}
{-if $ctl_loglist-}
{-foreach name=log key=key item=item from=$log-}
			<tr class="{-if ($smarty.foreach.log.iteration - 1) % 2 == 0-}normal{-else-}under{-/if-}"
					onMouseOver="Element.addClassName(this, 'highlight');" onMouseOut="Element.removeClassName(this, 'highlight');"
					onClick="setRolLog('{-$item[0]-}','{-$item[1]-}', 'log'); $('DBLogDate').value='{-$key-}'; 
												$('logcmd').value='update';">
					<td>
 {-if $item[0] == "CREDIT"-}
						{-$dic.DBLogCredits[0]-}
 {-elseif $item[0] == "METHODOLOGY"-}
    				{-$dic.DBLogMethodology[0]-}
 {-elseif $item[0] == "MILESTONE"-}
    				{-$dic.DBLogStaff[0]-}
 {-elseif $item[0] == "SUPPORT"-}
    				{-$dic.DBLogSupport[0]-}
 {-elseif $item[0] == "DELETED"-}
    				Borrado
 {-/if-}
					</td>
					<td>{-$item[1]|truncate:20-}</td>
			</tr>
{-/foreach-}
{-/if-}
{-if $ctl_adminreg-}
  	 </tbody>
  	</table>
	</div>
  <br><br>
  <input id="add" type="button" value="{-#baddoption#-}" class="line"
  		onclick="setRolLog('', '', 'log'); $('logcmd').value='insert';">
  <span id="logstatusmsg" class="dlgmsg"></span>
  <br><br>
  <div id="logaddsect" style="display:none; width:280px;">
   	<form name="logfrm" id="logfrm" method="GET" 
   		action="javascript: var s=$('logfrm').serialize(); sendData('{-$reg-}', 'regionlog.php', s, '');"
   		onSubmit="javascript: var a=new Array('DBLogType','DBLogNotes'); return(checkForm(a, '{-#errmsgfrmlog#-}'));">
			<input id="r" name="r" type="hidden" value="{-$reg-}">
			<a class="info" href="javascript:void(null)" onMouseOver="showtip('{-$dic.DBLogType[2]-}');">
			{-$dic.DBLogType[0]-}<b style="color:darkred;">*</b><span>{-$dic.DBLogType[1]-}</span></a><br>
			<select id="DBLogType" name="DBLogType" {-$ro-} onFocus="showtip('{-$dic.DBLogType[2]-}');" class="line fixw">
				<option value=""></option>
				<option value="CREDIT" onMouseOver="showtip('{-$dic.DBLogCredits[2]-}');">{-$dic.DBLogCredits[0]-}</option>
				<option value="METHODOLOGY" onMouseOver="showtip('{-$dic.DBLogMethodology[2]-}');">{-$dic.DBLogMethodology[0]-}</option>
				<option value="MILESTONE" onMouseOver="showtip('{-$dic.DBLogStaff[2]-}');">{-$dic.DBLogStaff[0]-}</option>
				<option value="SUPPORT" onMouseOver="showtip('{-$dic.DBLogSupport[2]-}');">{-$dic.DBLogSupport[0]-}</option>
				<option value="DELETED">- X -</option>
			</select>
			<br><br>
			<a class="info" href="javascript:void(null)" onMouseOver="showtip('{-$dic.DBLogNote[2]-}');">
			{-$dic.DBLogNote[0]-}<b style="color:darkred;">*</b><span>{-$dic.DBLogNote[1]-}</span></a><br>
			<textarea id="DBLogNotes" name="DBLogNotes" cols="22" {-$ro-} class="fixw"
					onFocus="showtip('{-$dic.DBLogNote[2]-}');"></textarea>
			<br><br>
			<p align="center" class="fixw">
				<input id="DBLogDate" name="DBLogDate" type="hidden">
				<input id="logcmd" name="logcmd" type="hidden">
				<input type="submit" value="{-#bsave#-}" {-$ro-} class="line">
				<input type="reset" value="{-#bcancel#-}" class="line"
						onClick="$('logaddsect').style.display='none'; mod='log'; uploadMsg('');" {-$ro-}>
			</p>
		</form>
	</div>
{-/if-}

{-** INFO AND ERRORS MESSAGES **-}
{-if $ctl_msginslog-}
 {-#msginslog#-}
{-elseif $ctl_errinslog-}
 {-#terror#-}[{-$insstatlog-}]: {-#errinslog#-}
{-elseif $ctl_msgupdlog-}
 {-#msgupdlog#-}
{-elseif $ctl_errupdlog-}
 {-#terror#-}[{-$updstatlog-}]: {-#errupdlog#-}
{-/if-}
