{-config_load file=`$lg`.conf section="di8_import"-}
{-** IMPORT: Interface to import datacards. **-}
<?xml version="1.0" encoding="UTF-8" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8; no-cache" />
	<script type="text/javascript" src="../include/prototype.js"></script>
	<script type="text/javascript">
	function enadisField(lnow, lnext, val) {
		var sour = $(lnow);
		if (val)
			sour.disabled = false;
		else {
			sour.disabled = true;
			fillColumn(lnow, lnext, false);
			for (var i = sour.length - 1; i>=0; i--) {
				sour.remove(i);
			}
		}
	}
	function fillColumn(lnow, lnext, exclude) {
		var sour = $(lnow);
		var dest = $(lnext);
		// clean dest list
		for (var i = dest.length - 1; i>=0; i--) {
			dest.remove(i);
		}
		for (var i=0; i < sour.length; i++) {
			if (exclude)
				test = !sour[i].selected;
			else
				test = true;
	        if (test) {
				var opt = document.createElement('option');
				opt.value = sour[i].value;
				opt.text = sour[i].text;
				var pto = dest.options[i];
				try {
					dest.add(opt, pto);  }
				catch(ex) {
					dest.add(opt, i);    }
			}
		}
	}
	</script>
</head>
<body>
{-** Show select CSV file interface **-}
{-if $ctl_show-}
	<!--<b onMouseOver="showtip('{-$dic.DBImport[2]-}');">{-$dic.DBImport[0]-}</b>--><br>
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
			style="height:400px; width:1280px;"></iframe>
	</p>
{-/if-}
{-** Show import interface to assign specific fields **-}
{-if $ctl_import-}
<table>
 <tr>
  <td>
   <input type="checkbox" onclick="enadisField('col{-$k-}', 'col{-`$k+1`-}', this.checked);" checked>
   <select id="col1" name="col1" style="width:180px;" onChange="fillColumn('col{-$k-}', 'col{-`$k+1`-}', true);">
    <option value=""></option>
 {-foreach name=fld2 key=k2 item=i2 from=$fld-}
    <option value="{-$i2-}">{-$i2-}</option>
 {-/foreach-}
	</select>
  </td>
  </tr>
</table>
{-/if-}
{-** Show importation results **-}
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
 {-foreach name=res key=key item=it from=$res-}
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
