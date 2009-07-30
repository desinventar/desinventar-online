{-config_load file=`$lg`.conf section="di8_reginfo"-}
{-** REGIONROL: Interface to Edit Info over Region.. **-}
{-if $ctl_adminreg-}
<!-- PERMISSIONS -->
	<b onMouseOver="showtip('{-$dic.DBPermissions[2]-}');">{-$dic.DBPermissions[0]-}</b>
	<div class="dwin" style="width:250px; height:150px;">
		<table width="100%" class="grid">
			<thead>
				<tr>
					<td class="header" onMouseOver="showtip('{-$dic.DBUserId[2]-}');">
						<b>{-$dic.DBUserId[0]-}</b></td>
					<td class="header" onMouseOver="showtip('{-$dic.DBRole[2]-}');">
						<b>{-$dic.DBRole[0]-}</b></td>
				</tr>
			</thead>
			<tbody id="lst_role">
{-/if-}
{-if $ctl_rollist-}
{-foreach name=rol key=key item=item from=$rol-}
{-if $usern != $key-}
				<tr class="{-if ($smarty.foreach.rol.iteration - 1) % 2 == 0-}normal{-else-}under{-/if-}"
						onMouseOver="Element.addClassName(this, 'highlight');" onMouseOut="Element.removeClassName(this, 'highlight');"
						onClick="setRolLog('{-$key-}','{-$item-}', 'role'); $('rolecmd').value='update';">
					<td>{-$key-}</td>
					<td>
{-if $item == "NONE"-}			{-$dic.DBRoleNone[0]-}
{-elseif $item == "USER"-}		{-$dic.DBRoleUser[0]-}
{-elseif $item == "OBSERVER"-}	{-$dic.DBRoleObserver[0]-}
{-elseif $item == "SUPERVISOR"-}{-$dic.DBRoleSupervisor[0]-}
{-/if-}
					</td>
				</tr>
{-/if-}
{-/foreach-}
{-/if-}
{-if $ctl_adminreg-}
			</tbody>
		</table>
	</div>
  <br><br>
  <input id="add" type="button" value="{-#baddoption#-}" class="line"
  		onclick="setRolLog('', '', 'role'); $('rolecmd').value='insert';">
  <span id="rolestatusmsg" class="dlgmsg"></span>
  <br><br>
  <div id="roleaddsect" style="display:none">
  	<form name="rolefrm" id="rolefrm" method="GET" 
  		action="javascript: var s=$('rolefrm').serialize(); sendData('{-$reg-}', 'regionrol.php', s, '');"
  		onSubmit="javascript: var a=new Array('UserId','AuthAuxValue'); return(checkForm(a, '{-#errmsgfrmrol#-}'));">
			<input id="r" name="r" type="hidden" value="{-$reg-}">
			<a class="info" href="javascript:void(null)" onMouseOver="showtip('{-$dic.DBUserId[2]-}')">
  					{-$dic.DBUserId[0]-}<b style="color:darkred;">*</b><span>{-$dic.DBUserId[1]-}</span></a><br>
  		<select id="UserId" name="UserId" {-$ro-} class="line fixw" onFocus="showtip('{-$dic.DBUserId[2]-}');">
				<option value=""></option>
{-foreach name=usr key=key item=item from=$usr-}
{-if $usern != $key-}
				<option value="{-$key-}">{-$item-}</option>
{-/if-}
{-/foreach-}
			</select>
			<br><br>
			<a class="info" href="javascript:void(null)" onMouseOver="showtip('{-$dic.DBRole[2]-}')">
						{-$dic.DBRole[0]-}<b style="color:darkred;">*</b><span>{-$dic.DBRole[1]-}</span></a><br>
			<select id="AuthAuxValue" name="AuthAuxValue" {-$ro-} class="fixw line" onFocus="showtip('{-$dic.DBRole[2]-}');">
				<option value="NONE" onMouseOver="showtip('{-$dic.DBRoleNone[2]-}');">{-$dic.DBRoleNone[0]-}</option>
				<option value="USER" onMouseOver="showtip('{-$dic.DBRoleUser[2]-}');">{-$dic.DBRoleUser[0]-}</option>
				<option value="OBSERVER" onMouseOver="showtip('{-$dic.DBRoleObserver[2]-}');">{-$dic.DBRoleObserver[0]-}</option>
				<option value="SUPERVISOR" onMouseOver="showtip('{-$dic.DBRoleSupervisor[2]-}');">{-$dic.DBRoleSupervisor[0]-}</option>
			</select>
			<br><br>
			<p class="fixw">
				<input id="rolecmd" name="rolecmd" type="hidden">
				<input type="submit" value="{-#bsave#-}" {-$ro-} class="line">
				<input type="reset" value="{-#bcancel#-}" class="line"
   	  				onClick="$('roleaddsect').style.display='none'; mod='role'; uploadMsg('');" {-$ro-}>
			</p>
		</form>
	</div>
{-/if-}

{-** INFO AND ERRORS MESSAGES **-}
{-if $ctl_msgupdrole-}
 {-#msgupdrole#-} 
{-elseif $ctl_errupdrole-}
 {-#terror#-}[{-$updstatrole-}]: {-#errupdrole#-}
{-/if-}
