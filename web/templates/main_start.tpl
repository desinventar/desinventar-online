{-config_load file="$lg.conf" section="di8_index"-}
<?xml version="1.0" encoding="UTF-8" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8; no-cache" />
	<title>{-#ttitle#-}</title>
</head>
<body>
	<table border="0" cellpadding="0" cellspacing="0" style="border: thin solid;" bgcolor="#e2e2e0" >
		<tr style="background:url(images/bgmain.png)">
			<td width="400px">
				<a href="{-$desinventarURL-}/index.php"><img src="{-$desinventarURL-}/images/di_logo1.png" border=0></a><br/>
			</td>
			<td height="100%" align="center">
				{-#mlang#-}:
				<select onChange="window.location=jQuery('#desinventarURL').val() + '/index.php?lang='+ this.value;">
				{-foreach name=LanguageList key=key item=item from=$LanguageList-}
					<option value="{-$key-}" {-if $lg == $key-}selected{-/if-}>{-$item-}</option>
				{-/foreach-}
				</select>
			</td>
			<td>
				<input type="button" value="{-#tstartpage#-} &rarr;" style="font-family:arial,tahoma,helvetica,cursive; font-size:24px; font-weight:bolder;"
					onClick="javascript:myw = window.open(jQuery('#desinventarURL').val() + '/index.php?{-$option-}','DI', 
					'width=1020,height=700,left=0,top=0,screenX=0,screenY=0,resizable=no,status=yes,scrollbars=no,toolbar=no'); myw.focus();" />
			</td>
		</tr>
		<tr>
			<td colspan="3">
				<hr />
				<table border="0">
					<tr valign="top">
						<td>
							<h1>{-#twelcome#-}</h1>
							<a href="doc/howmakequeries_spa.htm" target="idoc">Inicio rapido</a> (1 minuto)<br />
							<a href="javascript:void(null);" 
								onClick="window.open('http://www.desinventar.org/{-if $lg == "spa"-}es/metodologia{-else-}en/methodology{-/if-}/', '', '');">{-#hmoreinfo#-}</a><br />
							<a href="javascript:void(null);" 
								onClick="window.open('http://www.desinventar.org/{-if $lg == "spa"-}es{-else-}en{-/if-}/software/', '', '');">{-#hotherdoc#-}</a><br />
							<a href="javascript:void(null);" 
								onClick="window.open('http://www.desinventar.org', '', '');">{-#mwebsite#-}</a><br />
						</td>
						<td>
							<iframe id="idoc" name="idoc" frameborder="0" height="510px;" width="750px"></iframe>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</body>
</html>
