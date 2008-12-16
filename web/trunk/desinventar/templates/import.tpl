{-config_load file=`$lg`.conf section="di8_import"-}
{-** IMPORT: Interface to import datacards. **-}
<?xml version="1.0" encoding="UTF-8" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8; no-cache" />
</head>
<body>
{-if $ctl_show-}
	<b onMouseOver="showtip('{-$dic.DBImport[2]-}');">{-$dic.DBImport[0]-}</b><br>
	<p class="fixw"><br>
		<form method="POST" action="import.php" target="iframe2" enctype="multipart/form-data">
			<input type="hidden" name="r" value="{-$reg-}">
			<input type="hidden" name="cmd" value="upload">
			<input type="hidden" name="diobj" value="5">
			<input type="file" id="ieff" name="desinv" class="fixw line" {-$ro-}>
			<input type="submit" value="{-#tsend#-}" class="line" {-$ro-} onClick="$('iframe2').src='loading.gif';">
		</form>
		<br>
		<iframe name="iframe2" id="iframe2" frameborder="1" src="about:blank"
			style="height:400px; width:280px;"></iframe>
  </p>
{-/if-}
{-if $ctl_msg-}
 {-if $msg.Status == 1-}
 <b>{-#tsuccess#-}</b>
 {-else-}
 <b>{-#tfail#-}</b>
 {-/if-}
 <br>
 {-#tfound1#-} {-$msg.ErrorCount-} {-#tfound2#-}<br>
	<table style="font-size:11px;" border="1" width="100%">
		<tr><td>{-#tfile#-}</td><td>Detalles</td></tr>
 {-foreach name=csv key=key item=it from=$csv-}
	{-if $it[0] == "ERROR"-}
		<tr><td bgcolor="red">{-$it[1]-}</td><td>{-$it[3]-} | {-$it[4]-}</td></tr>
	{-elseif $it[0] == "WARNING"-}
		<tr><td bgcolor="yellow">{-$it[1]-}</td><td>{-$it[3]-} | {-$it[4]-}</td></tr>
	{-else-}
		<tr><td>{-$it[0]-}</td><td>{-$it[2]-}</td></tr>
	{-/if-}
 {-/foreach-}
	</table>
{-/if-}
{-if $ctl_error-}
  ** {-#tfound1#-} {-#tfound2#-}:<br>
 <b>{-$error-}..</b><br>
{-/if-}
</body>
</html>
