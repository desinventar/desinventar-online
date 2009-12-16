{-*** REGISTER NEW USER - CONTENT SECTION ***-}
{-config_load file=`$lg`.conf section="di8_user"-}

<link rel="stylesheet" href="css/desinventar.css" type="text/css">
{-include file="jquery.tpl" -}
<script type="text/javascript" src="js/useradmin.js"></script>
<script type="text/javascript">
	jQuery(document).ready(function() {
		onReadyUserAdmin();
	});	
</script>
<div id="divUserList">
	<h2>{-#tuserprefer#-}</h2>
	<br />
	<table id="tblUserList" width="550px;" height="180px" class="col dwin">
	<thead>
		<tr>
			<th class="header"><b>{-#tuser#-}</b></th>
			<th class="header"><b>{-#tname#-}</b></th>
			<th class="header"><b>{-#temail#-}</b></th>
			<th class="header"><b>{-#tactive#-}</b></th>
		</tr>
		</thead>
		<tbody id="lst_userpa">
			{-foreach name=upa key=key item=item from=$usrpa-}
				<tr>
					<td>{-$key-}</td>
					<td>{-$item[2]-}</td>
					<td>{-$item[0]-}</td>
					<td><input type="checkbox" {-if ($item[8] == 1) -} checked{-/if-} disabled /></td>
				</tr>
			{-/foreach-}
		</tbody>
	</table>
	<br />
	<input id="btnUserAdd" type="button" value="{-#baddoption#-}" class="line" />
	<span id="userpastatusmsg" class="dlgmsg"></span>
	<br />
	<br />
</div> {-* divUserList *-}
<div id="divUserEdit" style="display:block">
	<form id="frmUserEdit" name="userpafrm" method="GET" 
		action="javascript:var s=$('userpafrm').serialize(); sendData('','user.php', s, '');"
		onSubmit="javascript:var a=new Array('UserId', 'UserEMail', 'UserFullName'); return(checkForm(a, '{-#errmsgfrmregist#-}'));">
		<table class="grid">
			<tr>
				<td><b style="color:darkred;">{-#tuser#-}</b>
				</td>
				<td>
					<input type="text" id="UserId" name="UserId" size="15" maxlength="15" class="line"
						onBlur="updateList('chklogin', 'user.php', 'cmd=chklogin&UserId='+ $('UserId').value);" />
					<span id="chklogin" style="display:inline"></span>
					<input type="hidden" id="UserPasswd" name="UserPasswd" value="" />
				</td>
			</tr>
			<tr>
				<td>{-#tcountry#-}</td>
				<td><select id="UserCountry" name="UserCountry">
						<option value=""></option>
						{-foreach name=cnt key=key item=item from=$cnt-}
							<option value="{-$key-}">{-$item-}</option>
						{-/foreach-}
					</select>
				</td>
			</tr>
			<tr>
				<td><b style="color:darkred;">{-#tname#-}</b></td>
				<td>
					<input type="text" id="UserFullName" name="UserFullName" size="40" maxlength="40" class="line" />
				</td>
			</tr>
			<tr>
				<td><b style="color:darkred;">{-#temail#-}</b>
				</td>
				<td><input type="text" id="UserEMail" name="UserEMail" size="50" maxlength="50" class="line" />
				</td>
			</tr>
			<tr>
				<td>
					{-#tcity#-}
				</td>
				<td>
					<input type="text" id="UserCity" name="UserCity" size="20" maxlength="20" class="line" />
				</td>
			</tr>
			<tr>
				<td>
					<b>{-#tactive#-}</b>
				</td>
				<td>
					<input id="UserActive" name="UserActive" type="checkbox" />
				</td>
			</tr>
			<tr>
				<td colspan="2" align="center">
					<input type="hidden" id="cmd" name="cmd" />
					<input type="submit" id="btnUserEditSubmit" value="{-#bsave#-}" class="line" />
					<input type="reset"  id="btnUserEditCancel" value="{-#bcancel#-}" class="line" />
				</td>
			</tr>
		</table>
	</form>
</div> {-* divUserEdit *-}
