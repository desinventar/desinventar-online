{-config_load file=`$lg`.conf section="di8_user"-}
{-*** USER INFO EDIT - CONTENT ***-}

<h2>{-#tuserprefer#-}</h2>
<span id="userpastatusmsg" class="dlgmsg"></span><br />
<form id="userpafrm" name="userpafrm" method="GET" 
	action="javascript:var s=$('userpafrm').serialize(); sendData('','user.php', s, '');"
	onSubmit="javascript:var a=new Array('UserId', 'UserEMail', 'UserFullName', 'UserPasswd'); return(checkForm('userpafrm',a, '{-#errmsgfrmregist#-}'));">
	<table>
		<tr>
			<td><b style="color:darkred;">{-#toldpassword#-}</b>
			</td>
			<td><input type="password" id="UserPasswd" name="UserPasswd" size="8" maxlength="20" class="line" />
				<input type="button" value="Ok" class="line" onClick="$('userpaaddsect').style.display='block';
					updateList('userpaaddsect', 'user.php', 'cmd=chkpasswd&UserPasswd='+ $('UserPasswd').value);" />
			</td>
		</tr>
	</table>
	<div id="userpaaddsect" style="display:block">
	</div>
	{-if $ctl_chkpasswd-}
		<table class="grid">
			<tr>
				<td><b style="color:darkred;">{-#tuser#-}</b>
				</td>
				<td>
					{-if $ctl_chkpasswd-}
						<input type="text" id="UserId" name="UserId" size="15" maxlength="15" class="line" value="{-$usri.UserId-}" disabled />
						</td>
					{-else-}
						<input type="text" id="UserId" name="UserId" size="15" maxlength="15" class="line"
							onBlur="updateList('chklogin', 'user.php', 'cmd=chklogin&UserId='+ $('UserId').value);" />
							<span id="chklogin" style="display:inline"></span>
							<input type="hidden" id="UserPasswd" name="UserPasswd" value="" />
						</td>
						</tr>
						<tr>
							<td>{-#tcountry#-}
							</td>
							<td>
								<select id="UserCountry" name="UserCountry">
									<option value=""></option>
									{-foreach name=cnt key=key item=item from=$cnt-}
										<option value="{-$key-}">{-$item-}</option>
									{-/foreach-}
								</select>
							</td>
					{-/if-}
			</tr>
			<tr>
				<td><b style="color:darkred;">{-#tname#-}</b>
				</td>
				<td><input type="text" id="UserFullName" name="UserFullName" size="40" maxlength="40" class="line"
					{-if $ctl_chkpasswd-} value="{-$usri.UserFullName-}"{-/if-} />
				</td>
			</tr>
			<tr>
				<td><b style="color:darkred;">{-#temail#-}</b>
				</td>
				<td><input type="text" id="UserEMail" name="UserEMail" size="50" maxlength="50" class="line"
					{-if $ctl_chkpasswd-} value="{-$usri.UserEMail-}"{-/if-} />
				</td>
			</tr>
			<tr>
				<td>{-#tcity#-}
				</td>
				<td><input type="text" id="UserCity" name="UserCity" size="20" maxlength="20" class="line"
					{-if $ctl_chkpasswd-} value="{-$usri.UserCity-}"{-/if-} />
				</td>
			</tr>
			{-if $ctl_chkpasswd-}
				<tr>
					<td>
						{-#tnewpassword#-}
					</td>
					<td>
						<input type="password" id="NUserPasswd" name="NUserPasswd" size="8" maxlength="20" class="line" />
					</td>
				</tr>
				<tr>
					<td>
						{-#trenewpassw#-}
					</td>
					<td>
						<input type="password" id="NUserPasswd2" name="NUserPasswd2" size="8" maxlength="20" class="line" />
					</td>
				</tr>
			{-else-}
				<tr>
					<td>
						<b>{-#tactive#-}</b>
					</td>
					<td>
						<input id="UserActive" name="UserActive" type="checkbox" />
					</td>
				</tr>
			{-/if-}
			<tr>
			<td colspan="2" align="center">
				<input type="hidden" id="cmd" name="cmd" {-if $ctl_chkpasswd-} value="update"{-/if-} />
				<input type="submit" value="{-#bsave#-}" class="line" />
				<input type="reset" value="{-#bcancel#-}" class="line" onClick="$('userpaaddsect').style.display='none'; uploadMsg('');" />
			</td>
			</tr>
		</table>
	{-/if-} {-* End $ctl_chkpasswd *-}
</form>
