{-config_load file=`$lg`.conf section="di8_user"-}
{-*** ERRMSG: INVALID USER OR PASSWORD - USER MENU ***-}
{-if $ctl_invalid-}
<span class="dlgmsg">{-#errinvaliduser#-}</span>
{-/if-}
{-*** USER LOGIN WINDOW - USER MENU ***-}
{-if $ctl_login-}
<form action="javascript:updateUserBar('user.php', 'login', $('userid').value, $('password').value);" id="uf" method="get">
 <table border=0 cellpadding=0 cellspacing=0>
  <tr>
   <td>
	<table width="133" border=0 cellpadding=0 cellspacing=0 align=center style="margin-top:1px">
	 <tr>
	  <td><img src="images/c1.gif" width="133" height="4"></td>
	  <td><img src="images/c1.gif" width="133" height="4"></td>
	 </tr>
	 <tr>
	  <td class="mizq" align="right">{-#tuser#-} <input type="text" id="userid" name="userid" value="" size="8" class="f"></td>
	  <td class="mizq" align="right">{-#tpassword#-} <input type="password" name="password" id="password" value="" size="8" class="f"></td>
	 </tr>
	 <tr>
	  <td><img src="images/c3.gif" width="133" height="3" alt=""></td>
	  <td><img src="images/c3.gif" width="133" height="4" alt=""></td>
	 </tr>
	</table>
   </td>
   <td>
    <input type="submit" value="{-#benter#-}" class="line usr_inp">&nbsp;&nbsp;
	<span class="olvido"><a href="javascript:void(null)" onclick="updateList('pagecontent', 'user.php', 'cmd=passlost');">{-#tpasslost#-}</a></span>
   </td>
  </tr>
 </table>
</form>
{-/if-}
{-*** LOGIN SUCESSFULL - USER MENU ***-}
{-if $ctl_logged-}
  <table width="133" border=0 cellpadding=0 cellspacing=0 align=center style="margin-top:7px">
    <tr><td style="padding-bottom:3px"><span class="txt"><b>{-#tuser#-}: {-$user-}</b></span></td></tr>
    <tr><td><img src="images/c1.gif" width="133" height="4"></td></tr>
    <tr><td class="mizq2"><a href="javascript:void(null)" 
    	onclick="updateList('pagecontent', 'user.php', 'cmd=welcome');" class="txt">{-#tmyregions#-}</a></td></tr>
    <tr><td><img src="images/c2.gif" width="133" height="3" alt=""></td></tr>
    <tr><td class="mizq2"><a href="javascript:void(null)" 
    	onclick="mod='userpa'; updateList('pagecontent', 'user.php', 'cmd=viewpref');" class="txt">{-#tconfigacc#-}</a></td></tr>
    <tr><td><img src="images/c3.gif" width="133" height="4" alt=""></td></tr>
    <tr><td><div style="float:right"><img src="images/c_entrar1.jpg" width="11" height="18" 
    	border="0"><input type="button" value="{-#tclosesess#-}" class="line usr_out" 
    	onclick="updateUserBar('user.php', 'logout', '', '');"><img src="images/c_entrar3.jpg" 
    	width="5" height="18" border="0"></div>
    </td></tr>
  </table>
{-/if-}
{-*** WELCOME PAGE TO LOGGED USER WITH ROLES AND OPTIONS - CONTENT ***-}
{-if $ctl_welcome-}
<h2>{-#thello#-} {-$fullname-},</h2>
{-** Show lists with roles and regions acccess **-}
{-** ADMINPORTAL ROLE **-}
{-if $ctl_portalperms-}
<b>{-#tadminperms#-}: </b><br>
<ul class="">
	<li><a href="javascript:void(null)" onclick="updateList('pagecontent', 'region.php', 'cmd=adminreg');">{-#tadminregs#-}</a></li>
	<li><a href="javascript:void(null)" onclick="updateList('pagecontent', 'user.php', 'cmd=adminusr');">{-#tadminusrs#-}</a></li>
</ul>
<hr>
{-/if-}
{-** ADMINREGION ROLE **-}
{-#tyourrol#-}<br><br>
<li><b>{-#tadminof#-}</b><br>
{-if $radm == false-}
 {-#tnorol#-}
{-else-}
	<select onChange="updateList('shwadm', 'region.php', 'r='+ this.value)" size=4 style="width: 500px;">
{-foreach name=radm key=key item=item from=$radm-}
  	<option value="{-$key-}" class="regl">{-$item-}</option>
{-/foreach-}
	</select>
	<div id="shwadm"></div>
</li>
<!--
	<dl class="accordion">
  	<dt onclick="updateList('dd{-$key-}', 'region.php', 'r={-$key-}');">{-$item-}</dt>
  	<dd id="dd{-$key-}">
  	</dd>
	</dl>
-->
{-/if-}
<br>
{-** OBSERVER ROLE **-}
<li><b>{-#tobservof#-}</b></li>
{-if $robs == false-}
 {-#tnorol#-}
{-else-}
	<select onChange="updateList('shwobs', 'region.php', 'r='+ this.value)" size=4 style="width: 500px;">
		<option value=""></option>
{-foreach name=robs key=key item=item from=$robs-}
  	<option value="{-$key-}">{-$item-}</option>
{-/foreach-}
	</select>
	<div id="shwobs"></div>
{-/if-}
<br>
{-** SUPERVISOR ROLE **-}
<li><b>{-#tsupervof#-}</b></li>
{-if $rsup == false-}
 {-#tnorol#-}
{-else-}
	<select onChange="updateList('shwsup', 'region.php', 'r='+ this.value)" size=4 style="width: 500px;">
		<option value=""></option>
{-foreach name=rsup key=key item=item from=$rsup-}
  	<option value="{-$key-}">{-$item-}</option>
{-/foreach-}
	</select>
	<div id="shwsup"></div>
{-/if-}
<br>
{-** USER ROLE **-}
<li><b>{-#tuserof#-}</b></li>
{-if $rusr == false-}
 {-#tnorol#-}
{-else-}
	<select onChange="updateList('shwusr', 'region.php', 'r='+ this.value)" size=4 style="width: 500px;">
		<option value=""></option>
{-foreach name=rusr key=key item=item from=$rusr-}
  	<option value="{-$key-}">{-$item-}</option>
{-/foreach-}
	</select>
	<div id="shwusr"></div>
{-/if-}
<br>
{-/if-} {-** end ctl_welcome **-}
{-*** PASSWORD RECOVERY - CONTENT SECTION ***-}
{-if $ctl_passlost-}
<h2>{-#tgetemailpass#-}</h2>
<br>
<span id="plostaddsect">{-#tmsgpasslost#-}</span>
<span id="ploststatusmsg" class="dlgmsg"></span>
<br><br>
<form id="passlostfrm" name="passlostfrm" method="GET" 
	action="javascript:var s=$('passlostfrm').serialize(); mod='plost'; sendData('', 'user.php', s, '');"
	onSubmit="javascript:var a=new Array('UserEMail'); return(checkForm(a, '{-#errsendemail#-}'));">
	{-#tuseremail#-} <input id="UserEMail" name="UserEMail" type="text" size="20" class="line">
  <input id="cmd" name="cmd" value="passlost" type="hidden">
  <input id="opt" name="opt" value="sendnewpass" type="hidden">
	<input type="submit" value="{-#bsend#-}" class="line"> <input type="reset" value="{-#bcancel#-}" class="line">
</form>
{-/if-}
<!-- SECTION ADMIN USERS -->
{-*** REGISTER NEW USER - CONTENT SECTION ***-}
{-if $ctl_adminusr-}
<br>
  <h2>{-#tuserprefer#-}</h2>
  <br>
  <table width="500px;" class="col">
	<thead>
	 <tr>
	 	<td class="header"><b>{-#tuser#-}</b></td>
	 	<td class="header"><b>{-#tname#-}</b></td>
    <td class="header"><b>{-#temail#-}</b></td>
    <td class="header"><b>{-#tactive#-}</b></td>
   </tr>
  </thead>
  <tbody id="lst_userpa" style="overflow:auto; height:180px;">
{-/if-}
{-if $ctl_usrlist-}
{-foreach name=upa key=key item=item from=$usrpa-}
   <tr class="{-if ($smarty.foreach.upa.iteration - 1) % 2 == 0-}normal{-else-}under{-/if-}"
   		 onMouseOver="this.className='highlight'" onMouseOut="this.className='normal'"
   		 onClick="setUserPA('{-$key-}','{-$item[2]-}','{-$item[0]-}','{-$item[1]-}','{-$item[4]-}','{-$item[5]-}','{-$item[9]-}');
   												$('cmd').value='update';">
    <td>{-$key-}</td>
    <td>{-$item[2]-}</td>
    <td>{-$item[0]-}</td>
    <td><input type="checkbox" {-if ($item[9] == 1) -} checked {-/if-} disabled></td>
   </tr>
{-/foreach-}
{-/if-}
{-* Continue with userreg.. *-}
{-if $ctl_adminusr-}
  </tbody>
  </table>
  <br>
  <input id="add" type="button" value="{-#baddoption#-}" class="line"
  		onclick="setUserPA('','','','','','','1'); $('cmd').value='insert';
  							$('UserPasswd').disabled=true;">
  <span id="userpastatusmsg" class="dlgmsg"></span>
  <br><br>
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
  		<td><input type="password" id="UserPasswd" name="UserPasswd" size="8" maxlength="20" class="line">
  				<input type="button" value="ok" class="line"
  						onClick="updateList('userpaaddsect', 'user.php', 'cmd=chkpasswd&UserPasswd='+ $('UserPasswd').value);
  								$('userpaaddsect').style.display='block';">
  				<input type="hidden" id="UserId" name="UserId" value="{-$usri.UserId-}">
 		 			<input type="hidden" id="UserCountry" name="UserCountry" value="{-$usri.UserCountry-}">
  				<input type="hidden" id="UserActive" name="UserActive" value="{-$usri.UserActive-}">
  		</td>
 		 </tr>
 		</table>
 		<div id="userpaaddsect" style="display:block"></div>
 {-/if-}
 {-if $ctl_adminusr || $ctl_chkpasswd-}
 	  <table class="grid">
 		 <tr>
  		<td><b style="color:darkred;">{-#tuser#-}</b></td>
  		<td><input type="text" id="UserId" name="UserId" size="15" maxlength="15" class="line"
 {-if $ctl_chkpasswd-} value="{-$usri.UserId-}" disabled></td>
 		 </tr>
 {-else-} onBlur="updateList('chklogin', 'user.php', 'cmd=chklogin&UserId='+ $('UserId').value);">
  				<span id="chklogin" style="display:inline"></span>
  		<input type="hidden" id="UserPasswd" name="UserPasswd" value="{-$usri.UserPasswd-}"></td>
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
 {-/if-}
 		 <tr>
  		<td><b style="color:darkred;">{-#tname#-}</b></td>
  		<td><input type="text" id="UserFullName" name="UserFullName" size="40" maxlength="40" class="line"
  				{-if $ctl_chkpasswd-} value="{-$usri.UserFullName-}" {-/if-}></td>
 		 </tr>
 		 <tr>
  		<td><b style="color:darkred;">{-#temail#-}</b></td>
  		<td><input type="text" id="UserEMail" name="UserEMail" size="50" maxlength="50" class="line"
  				{-if $ctl_chkpasswd-} value="{-$usri.UserEMail-}" {-/if-}></td>
 		 </tr>
 		 <tr>
  		<td>{-#tcity#-}</td>
  		<td><input type="text" id="UserCity" name="UserCity" size="20" maxlength="20" class="line"
  				{-if $ctl_chkpasswd-} value="{-$usri.UserCity-}" {-/if-}></td>
 		 </tr>
		 <tr>
  {-if $ctl_chkpasswd-}
		  <td>{-#tnewpassword#-}</td>
		  <td><input type="password" id="NUserPasswd" name="NUserPasswd" size="8" maxlength="20" class="line">
<!--
  				onBlur="updateList('userpastatusmsg', 'user.php', 
  					'cmd=chknewpass&NUserPasswd='+ $('NUserPasswd').value + '&NUserPasswd2='+ $('NUserPasswd2').value);">
-->
  		 {-#trenewpassw#-}
  		 <input type="password" id="NUserPasswd2" name="NUserPasswd2" size="8" maxlength="20" class="line">
  		</td>
<!--
  				onBlur="updateList('userpastatusmsg', 'user.php', 
  					'cmd=chknewpass&NUserPasswd='+ $('NUserPasswd').value + '&NUserPasswd2='+ $('NUserPasswd2').value);">
-->
  {-else-}
  		<td><b>{-#tactive#-}</b></td>
  		<td><input id="UserActive" name="UserActive" type="checkbox"></td>
  {-/if-}
 		 </tr>
 		 <tr>
  		<td colspan="2" align="center">
  			<input type="hidden" id="cmd" name="cmd" {-if $ctl_chkpasswd-} value="update"{-/if-}>
  			<input type="submit" value="{-#bsave#-}" class="line">
  			<input type="reset" value="{-#bcancel#-}" class="line" 
  					onClick="$('userpaaddsect').style.display='none'; uploadMsg('');">
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
 {-#msgsendemail#-} {-$rempass[0]-}<br>
 {-#msgsendemail2#-} {-$rempass[1]-}
{-/if-}
{-if $ctl_errsend-}
 {-#errsendemail#-}
{-/if-}
