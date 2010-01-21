{-config_load file=`$lg`.conf section="di8_user"-}
<!--
{-*** ERRMSG: INVALID USER OR PASSWORD - USER MENU ***-}
{-if $ctl_invalid-}
	<span class="dlgmsg">{-#errinvaliduser#-}</span>
{-/if-}
-->
{-*** REGISTER NEW USER - CONTENT SECTION ***-}
{-if $ctl_adminusr-}
	<br />
	<h2>{-#tuserprefer#-}</h2>
	<br />
	<table width="500px;" height="180px" class="col dwin">
	<thead>
	<tr>
		<td class="header"><b>{-#tuser#-}</b></td>
		<td class="header"><b>{-#tname#-}</b></td>
		<td class="header"><b>{-#temail#-}</b></td>
		<td class="header"><b>{-#tactive#-}</b></td>
	</tr>
	</thead>
	<tbody id="lst_userpa">
{-/if-}{-* ctl_adminusr *-}
{-if $ctl_usrlist-}
{-foreach name=upa key=key item=item from=$usrpa-}
	<tr class="{-if ($smarty.foreach.upa.iteration - 1) % 2 == 0-}normal{-else-}under{-/if-}"
		onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'"
		onClick="setUserPA('{-$key-}','{-$item[2]-}','{-$item[0]-}','{-$item[1]-}','{-$item[4]-}','{-$item[5]-}','{-$item[8]-}');
				$('cmd').value='update';">
		<td>{-$key-}</td>
		<td>{-$item[2]-}</td>
		<td>{-$item[0]-}</td>
		<td><input type="checkbox" {-if ($item[8] == 1) -} checked{-/if-} disabled /></td>
	</tr>
{-/foreach-}
{-/if-}
{-if $ctl_adminusr-}
	</tbody>
	</table>
	<br />
	<input id="add" type="button" value="{-#baddoption#-}" class="line"
		onclick="setUserPA('','','','','','','1'); $('cmd').value='insert'; $('UserPasswd').disabled=true;" />
	<span id="userpastatusmsg" class="dlgmsg"></span>
	<br /><br />
{-/if-}
{-*** USER INFO EDIT - CONTENT ***-}
{-if $ctl_adminusr || $ctl_viewpref || $ctl_chkpasswd-}
 {-if $ctl_viewpref-}
	<h2>{-#tuserprefer#-}</h2>
	<span id="userpastatusmsg" class="dlgmsg"></span><br />
 {-/if-}
 {-if $ctl_adminusr-}
	<div id="userpaaddsect" style="display:none">
 {-/if-}
 {-if $ctl_adminusr || $ctl_viewpref-}
	<form id="userpafrm" name="userpafrm" method="GET" 
		action="javascript:var s=$('userpafrm').serialize(); sendData('','user.php', s, '');"
		onSubmit="javascript:var a=new Array('UserId', 'UserEMail', 'UserFullName'
				{-if $ctl_viewpref-}, 'UserPasswd'{-/if-}); return(checkForm(a, '{-#errmsgfrmregist#-}'));">
 {-/if-}
 {-if $ctl_viewpref-}
	<table>
	<tr>
		<td><b style="color:darkred;">{-#toldpassword#-}</b></td>
		<td><input type="password" id="UserPasswd" name="UserPasswd" size="8" maxlength="20" class="line" />
			<input type="button" value="Ok" class="line" onClick="$('userpaaddsect').style.display='block';
				updateList('userpaaddsect', 'user.php', 'cmd=chkpasswd&UserPasswd='+ $('UserPasswd').value);" />
		</td>
	</tr>
	</table>
	<div id="userpaaddsect" style="display:block"></div>
 {-/if-}
 {-if $ctl_adminusr || $ctl_chkpasswd-}
	<table class="grid">
	<tr>
		<td><b style="color:darkred;">{-#tuser#-}</b></td>
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
		<td>{-#tcountry#-}</td>
		<td><select id="UserCountry" name="UserCountry">
				<option value=""></option>
  {-foreach name=cnt key=key item=item from=$cnt-}
				<option value="{-$key-}">{-$item-}</option>
  {-/foreach-}
			</select>
		</td>
  {-/if-}
	</tr>
	<tr>
		<td><b style="color:darkred;">{-#tname#-}</b></td>
		<td><input type="text" id="UserFullName" name="UserFullName" size="40" maxlength="40" class="line"
			{-if $ctl_chkpasswd-} value="{-$usri.UserFullName-}"{-/if-} /></td>
	</tr>
	<tr>
		<td><b style="color:darkred;">{-#temail#-}</b></td>
		<td><input type="text" id="UserEMail" name="UserEMail" size="50" maxlength="50" class="line"
			{-if $ctl_chkpasswd-} value="{-$usri.UserEMail-}"{-/if-} /></td>
	</tr>
	<tr>
		<td>{-#tcity#-}</td>
		<td><input type="text" id="UserCity" name="UserCity" size="20" maxlength="20" class="line"
			{-if $ctl_chkpasswd-} value="{-$usri.UserCity-}"{-/if-} /></td>
	</tr>
	<tr>
  {-if $ctl_chkpasswd-}
		<td>{-#tnewpassword#-}</td>
		<td><input type="password" id="NUserPasswd" name="NUserPasswd" size="8" maxlength="20" class="line" />
			{-#trenewpassw#-}<input type="password" id="NUserPasswd2" name="NUserPasswd2" size="8" maxlength="20" class="line" />
		</td>
  {-else-}
		<td><b>{-#tactive#-}</b></td>
		<td><input id="UserActive" name="UserActive" type="checkbox" /></td>
  {-/if-}
	</tr>
	<tr>
		<td colspan="2" align="center">
			<input type="hidden" id="cmd" name="cmd" {-if $ctl_chkpasswd-} value="update"{-/if-} />
			<input type="submit" value="{-#bsave#-}" class="line" />
			<input type="reset" value="{-#bcancel#-}" class="line" onClick="$('userpaaddsect').style.display='none'; uploadMsg('');" />
		</td>
	</tr>
	</table>
 {-/if-}
 {-* End ctl_adminusr || ctl_chkpasswd *-}
 {-if $ctl_adminusr || $ctl_viewpref-}
	</form>
 {-/if-}
 {-if $ctl_adminusr-}
	</div>
 {-/if-}
{-/if-}

{-*** SHOW MESSAGES AND ERRORS ***-}
 {-if $ctl_chklogin-}
  {-if !$clogin-}
 	 {-#errinvaliduser#-}
  {-/if-}
 {-/if-}
{-if $ctl_msginsert-}
 {-if $errinsuser-}
  {-#terror#-}[{-$insstat-}]: {-#errinsert#-} {-$userid-}
 {-elseif $noerrorins-}
  {-#msginsertsucc#-} {-$userid-}
 {-/if-}
{-/if-}
{-if $ctl_msgupdate-}
 {-if $errnomatch-}
  {-#errnomatch#-}
 {-elseif $errbadpass-}
  {-#errbadpasswd#-}
 {-elseif $errupduser-}
  {-#terror#-}[{-$updstat-}] {-#errupdate#-} {-$userid-}
 {-elseif $noerrorupd-}
  {-#msgupdatesucc#-} {-$userid-}
 {-/if-}
{-/if-}
