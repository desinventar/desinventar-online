{-*** PASSWORD RECOVERY - CONTENT SECTION ***-}
{-config_load file=`$lg`.conf section="di8_user"-}
<h2>{-#tgetemailpass#-}</h2><br>
<span id="plostaddsect">{-#tmsgpasslost#-}</span>
<span id="ploststatusmsg" class="dlgmsg"></span>
<br /><br />
<form id="passlostfrm" name="passlostfrm" method="GET" 
	action="javascript:var s=$('passlostfrm').serialize(); mod='plost'; sendData('', 'user.php', s, '');"
	onSubmit="javascript:var a=new Array('UserEMail'); return(checkForm(a, '{-#errsendemail#-}'));">
	{-#tuseremail#-} <input id="UserEMail" name="UserEMail" type="text" size="20" class="line" />
	<input id="cmd" name="cmd" value="passlost" type="hidden" />
	<input id="opt" name="opt" value="sendnewpass" type="hidden" />
	<input type="submit" value="{-#bsend#-}" class="line" /> <input type="reset" value="{-#bcancel#-}" class="line" />
</form>
