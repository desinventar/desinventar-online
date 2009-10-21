{-config_load file=`$lg`.conf section="di8_user"-}
{-*** ERRMSG: INVALID USER OR PASSWORD - USER MENU ***-}
{-if $ctl_invalid-}
	<span class="dlgmsg">{-#errinvaliduser#-}</span>
{-/if-}

{-*** USER LOGIN WINDOW - USER MENU ***-}
{-if $ctl_login-}
	<form action="javascript:updateUserBar('user.php', 'login', $('userid').value, $('password').value);" id="uf" method="get">
	<table border="0" cellpadding="0" cellspacing="0">
		<tr>
		<td>
			<table border=0 align=center style="margin-top:1px">
				<tr>
				<td align="right">{-#tuser#-} <input type="text" id="userid" name="userid" value="" size="16" /></td>
				<td align="right">{-#tpassword#-} <input type="password" name="password" id="password" value="" size="8" /></td>
				</tr>
			</table>
		</td>
		<td>
			<input type="submit" value="{-#benter#-}" class="line" />&nbsp;&nbsp;
			<span class="rememberpasswordlink">
				<a href="javascript:void(null)" onclick="updateList('pagecontent', 'user.php', 'cmd=passlost');">{-#tpasslost#-}</a>
			</span>
		</td>
		</tr>
	</table>
	</form>
{-/if-}

{-*** LOGIN SUCESSFULL - USER MENU ***-}
{-if $ctl_logged-}
	<span class="txt"><b>{-#tuser#-}: {-$user-}</b></span>&nbsp;&nbsp;
	<a href="javascript:void(null)" onclick="updateList('pagecontent', 'user.php', 'cmd=welcome');">{-#tmyregions#-}</a> |
	<a href="javascript:void(null)" onclick="mod='userpa'; updateList('pagecontent', 'user.php', 'cmd=viewpref');">{-#tconfigacc#-}</a> |
	<a href="javascript:void(null)" onclick="updateUserBar('user.php', 'logout', '', '');">{-#tclosesess#-}</a>
{-/if-}

{-*** WELCOME PAGE TO LOGGED USER WITH ROLES AND OPTIONS - CONTENT ***-}
{-if $ctl_welcome-}
	<h2>{-#thello#-} {-$fullname-},</h2>
 {-** Show lists with roles and regions acccess **-}
 {-if $ctl_portalperms-}
	<b>{-#tadminperms#-}: </b><br />
	<ul class="">
		<li><a href="javascript:void(null)" onclick="updateList('pagecontent', 'region.php', 'cmd=adminreg');">{-#tadminregs#-}</a></li>
		<li><a href="javascript:void(null)" onclick="updateList('pagecontent', 'user.php', 'cmd=adminusr');">{-#tadminusrs#-}</a></li>
	</ul>
	<br /><hr /><br />
 {-/if-}
{-#tyourrol#-}<br /><br />
	<table border="0">
 {-if $radm-}
 {-foreach name=radm key=key item=item from=$radm-}
		<tr>
		<td><b>{-#tadminof#-}</b></td>
		<td><a href="javascript:void(null)" onclick="parent.window.location = 'index.php?r={-$key-}'">{-$item-}</a></td>
		</tr>
 {-/foreach-}
 {-/if-}
 {-if $robs-}
 {-foreach name=robs key=key item=item from=$robs-}
		<tr>
		<td><b>{-#tobservof#-}</b></td>
		<td><a href="javascript:void(null)" onclick="parent.window.location = 'index.php?r={-$key-}'">{-$item-}</a></td>
		</tr>
 {-/foreach-}
 {-/if-}
 {-if $rsup-}
 {-foreach name=rsup key=key item=item from=$rsup-}
		<tr>
		<td><b>{-#tsupervof#-}</b></td>
		<td><a href="javascript:void(null)" onclick="parent.window.location = 'index.php?r={-$key-}'">{-$item-}</a></td>
		</tr>
 {-/foreach-}
 {-/if-}
 {-if $rusr-}
 {-foreach name=rusr key=key item=item from=$rusr-}
		<tr>
		<td><b>{-#tuserof#-}</b></td>
		<td><a href="javascript:void(null)" onclick="parent.window.location = 'index.php?r={-$key-}'">{-$item-}</a></td>
		</tr>
 {-/foreach-}
 {-/if-}
	</table><br />
{-/if-} {-** end ctl_welcome **-}

{-*** PASSWORD RECOVERY - CONTENT SECTION ***-}
{-if $ctl_passlost-}
	<h2>{-#tgetemailpass#-}</h2><br>
	<span id="plostaddsect">{-#tmsgpasslost#-}</span>
	<span id="ploststatusmsg" class="dlgmsg"></span>
	<br /><br />
	<form id="passlostfrm" name="passlostfrm" method="GET" 
		action="javascript:var s=$('passlostfrm').serialize(); mod='plost'; sendData('', 'user.php', s, '');"
		onSubmit="javascript:var a=new Array('UserEMail'); return(checkForm(a, '{-#errsendemail#-}'));">
		{-#tuseremail#-} <input id="UserEMail" name="UserEMail" type="text" size="20" class="line">
		<input id="cmd" name="cmd" value="passlost" type="hidden" />
		<input id="opt" name="opt" value="sendnewpass" type="hidden" />
		<input type="submit" value="{-#bsend#-}" class="line" /> <input type="reset" value="{-#bcancel#-}" class="line" />
	</form>
{-/if-}

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
		onClick="setUserPA('{-$key-}','{-$item[2]-}','{-$item[0]-}','{-$item[1]-}','{-$item[4]-}','{-$item[5]-}','{-$item[9]-}');
				$('cmd').value='update';">
		<td>{-$key-}</td>
		<td>{-$item[2]-}</td>
		<td>{-$item[0]-}</td>
		<td><input type="checkbox" {-if ($item[9] == 1) -} checked {-/if-} disabled /></td>
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
 {-if $ctl_adminusr || $ctl_viewpref-}
  {-if $ctl_viewpref-}
	<h2>{-#tuserprefer#-}</h2>
	<span id="userpastatusmsg" class="dlgmsg"></span><br>
  {-else-}
	<div id="userpaaddsect" style="display:none">
  {-/if-}
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
			<input type="button" value="ok" class="line" onClick="$('userpaaddsect').style.display='block';
				updateList('userpaaddsect', 'user.php', 'cmd=chkpasswd&UserPasswd='+ $('UserPasswd').value);" />
			<input type="hidden" id="UserId" name="UserId" value="{-$usri.UserId-}" />
			<input type="hidden" id="UserCountry" name="UserCountry" value="{-$usri.UserCountry-}" />
			<input type="hidden" id="UserActive" name="UserActive" value="{-$usri.UserActive-}" />
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
			<input type="hidden" id="UserPasswd" name="UserPasswd" value="{-$usri.UserPasswd-}" />
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
  {-/if-}{-* End ctl_chkpasswd *-}
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
<!--onBlur="updateList('userpastatusmsg', 'user.php',  'cmd=chknewpass&NUserPasswd='+ $('NUserPasswd').value + '&NUserPasswd2='+ $('NUserPasswd2').value);"> -->
			{-#trenewpassw#-}<input type="password" id="NUserPasswd2" name="NUserPasswd2" size="8" maxlength="20" class="line" />
<!--onBlur="updateList('userpastatusmsg', 'user.php',  'cmd=chknewpass&NUserPasswd='+ $('NUserPasswd').value + '&NUserPasswd2='+ $('NUserPasswd2').value);">-->
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
{-if $ctl_msgsend-}
 {-#msgsendemail#-} {-$rempass[0]-}<br />
 {-#msgsendemail2#-} {-$rempass[1]-}
{-/if-}
{-if $ctl_errsend-}
 {-#errsendemail#-}
{-/if-}
