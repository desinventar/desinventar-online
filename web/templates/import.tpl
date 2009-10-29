{-config_load file=`$lg`.conf section="di8_import"-}
{-** IMPORT: Interface to import datacards. **-}
<?xml version="1.0" encoding="UTF-8" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8; no-cache" />
	<script type="text/javascript">
	function sendForm() {
		var fr = document.getElementById('iframe2');
		var im = document.getElementById('import');
		fr.src='loading.gif';
		im.submit();
	}
	function enadisField(lnow, lnext, val) {
		var sour = document.getElementById(lnow);
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
		var sour = document.getElementById(lnow);
		var dest = document.getElementById(lnext);
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
{-* Show select CSV file interface *-}
{-if $ctl_show-}
	<p class="fixw">
		<form id="import" method="POST" action="import.php" target="importres" enctype="multipart/form-data">
			<input type="hidden" name="_REG" value="{-$reg-}" />
			<input type="hidden" name="cmd" value="upload" />
			<input type="hidden" name="diobj" value="5" />
			<input type="file" id="ieff" name="desinv" class="fixw" {-$ro-} /> <!--  onChange="sendForm();"-->
			<input type="submit" value="Ok" />
		</form>
		<br />
		<iframe name="importres" id="importres" frameborder="1" src="about:blank" style="height:400px; width:830px;"></iframe>
	</p>
{-/if-}
{-* Show import interface to assign specific fields *-}
{-if $ctl_import-}
	<form method="POST" action="import.php">
		<input type="hidden" name="cmd" value="import" />
		<input type="hidden" name="FileName" value="{-$FileName-}" />
		<input type="submit" value="{-#tsend#-}" class="line" />
		<br />
		<table border="1" style="font-size: 11px;">
		<tr>
{-foreach name=fld key=k item=i from=$fld-}
{-assign var="nxt" value="`$smarty.foreach.fld.iteration+1`"-}
			<td>
			<!--   <input type="checkbox" onclick="enadisField('col{-$smarty.foreach.fld.iteration-}', 'col{-$nxt-}', this.checked);" checked> 
				onChange="fillColumn('col{-$smarty.foreach.fld.iteration-}', 'col{-$nxt-}', true);"-->
			<select id="col{-$smarty.foreach.fld.iteration-}" name="col{-$smarty.foreach.fld.iteration-}">
				<option value="{-$k-}">{-$i-}</option>
			</select>
			</td>
{-/foreach-}
		</tr>
{-foreach name=csv key=k item=i from=$csv-}
		<tr>
  {-foreach name=cs2 key=ky item=it from=$i-}
			<td>{-$it-}</td>
  {-/foreach-}
		</tr>
{-/foreach-}
		</table>
	</form>
{-/if-}
{-* Show importation results *-}
{-if $ctl_msg-}
 {-if $msg.Status == 1-}
	<b>{-#tsuccess#-}</b>
 {-else-}
	<b>{-#tfail#-}</b>
 {-/if-}
	<br />
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
	** {-#tfound1#-} {-#tfound2#-}:<br />
	<b>{-$error-}..</b><br />
{-/if-}
</body>
</html>
