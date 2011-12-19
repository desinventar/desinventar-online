<fieldset style="padding:5px 5px 5px 5px;">
	<legend>
		<b onMouseOver="showtip('{-$dic.DBPermissions[2]-}');">{-$dic.DBPermissions[0]-}</b>
	</legend>
	<div class="dwin" style="width:280px; height:120px;">
		<table width="100%" class="grid">
			<thead>
				<tr>
					<td class="header" onMouseOver="showtip('{-$dic.DBUserId[2]-}');">
						<b>{-$dic.DBUserId[0]-}</b>
					</td>
					<td class="header" onMouseOver="showtip('{-$dic.DBRole[2]-}');">
						<b>{-$dic.DBRole[0]-}</b>
					</td>
				</tr>
			</thead>
			<tbody id="lst_role">
				{-foreach name=rol key=key item=item from=$rol-}
					{-if $usern != $key-}
						<tr class="{-if ($smarty.foreach.rol.iteration - 1) % 2 == 0-}normal{-else-}under{-/if-}"
							onMouseOver="Element.addClassName(this, 'highlight');" onMouseOut="Element.removeClassName(this, 'highlight');"
							onClick="setRolLog('{-$key-}','{-$item-}', 'role'); $('RoleCmd').value='cmdDBInfoRoleUpdate';">
							<td>
								{-$key-}
							</td>
							<td>
								{-if $item == "NONE"-}			{-$dic.DBRoleNone[0]-}
								{-elseif $item == "USER"-}		{-$dic.DBRoleUser[0]-}
								{-elseif $item == "OBSERVER"-}	{-$dic.DBRoleObserver[0]-}
								{-elseif $item == "SUPERVISOR"-}	{-$dic.DBRoleSupervisor[0]-}
								{-/if-}
							</td>
						</tr>
					{-/if-}
				{-/foreach-}
			</tbody>
		</table>
	</div>
	<br />
	<a class="button" id="add" onclick="setRolLog('', '', 'role'); $('RoleCmd').value='cmdDBInfoRoleInsert';"><span>{-#baddoption#-}</span></a>
	<span id="rolestatusmsg" class="dlgmsg"></span>
	<br />
	<div id="roleaddsect" style="display:none">
		<form name="rolefrm" id="rolefrm" method="GET" 
			action="javascript: var s=$('rolefrm').serialize(); sendData('{-$reg-}', '{-$desinventarURL-}/info.php', s, '');"
			onSubmit="javascript: var a=new Array('UserId','AuthAuxValue'); return(checkForm('rolefrm',a, '{-#errmsgfrmrol#-}'));">
			<a class="info" href="javascript:void(null)" onMouseOver="showtip('{-$dic.DBUserId[2]-}')">
			{-$dic.DBUserId[0]-}<b style="color:darkred;">*</b><span>{-$dic.DBUserId[1]-}</span></a><br />
			<select id="UserId" name="UserId" {-$ro-} class="line fixw" onFocus="showtip('{-$dic.DBUserId[2]-}');" tabindex="1">
				<option value=""></option>
				{-foreach name=usr key=key item=item from=$usr-}
					{-if $usern != $key-}
						<option value="{-$key-}">{-$item-}</option>
					{-/if-}
				{-/foreach-}
			</select>
			<br />
			<br />
			<a class="info" href="javascript:void(null)" onMouseOver="showtip('{-$dic.DBRole[2]-}')">
			{-$dic.DBRole[0]-}<b style="color:darkred;">*</b><span>{-$dic.DBRole[1]-}</span></a><br />
			<select id="AuthAuxValue" name="AuthAuxValue" {-$ro-} class="fixw line" onFocus="showtip('{-$dic.DBRole[2]-}');" tabindex="2">
				<option value="NONE" onMouseOver="showtip('{-$dic.DBRoleNone[2]-}');">{-$dic.DBRoleNone[0]-}</option>
				<option value="USER" onMouseOver="showtip('{-$dic.DBRoleUser[2]-}');">{-$dic.DBRoleUser[0]-}</option>
				<option value="OBSERVER" onMouseOver="showtip('{-$dic.DBRoleObserver[2]-}');">{-$dic.DBRoleObserver[0]-}</option>
				<option value="SUPERVISOR" onMouseOver="showtip('{-$dic.DBRoleSupervisor[2]-}');">{-$dic.DBRoleSupervisor[0]-}</option>
			</select>
			<br />
			<br />
			<p class="fixw" align="center">
				<input name="r" type="hidden" value="{-$reg-}" />
				<input id="RoleCmd" name="cmd" type="hidden" />
				<input type="submit" value="{-#bsave#-}" {-$ro-} class="line" tabindex="3"/>
				<input type="reset" value="{-#bcancel#-}" class="line"
					onClick="$('roleaddsect').style.display='none'; mod='role'; uploadMsg('');" {-$ro-} />
			</p>
		</form>
	</div>
</fieldset>
<br />
<br />
