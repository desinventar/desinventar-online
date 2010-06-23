{-config_load file=`$lg`.conf section="di8_region"-}
{-* ADMINREG: Interface to Edit Portal Admin *-}
{-if $ctl_adminreg-}
	<h2>{-#ttname#-}</h2>
	<br />
	<input id="directory" type="hidden" value="{-#bloaddir#-}"
		onClick="updateList('lst_regionpa', 'region.php', 'cmd=createRegionsFromDBDir');" />
	<b>{-#bupzipfile#-}</b> <br />
	<form id="putregion" method="POST" action="region.php" target="fresult" enctype="multipart/form-data">
		<input type="hidden" name="cmd" value="createRegionFromZip" />
		{-#tregnamlist#-} <input type="text" name="RegionLabel" />
		<input type="file" name="filereg" />
		<input type="submit" value="Ok" onClick="uploadMsg('');" />
	</form>
	<br /><hr /><br />
	<div class="dwin" style="width:500px; height:150px;">
	 <table class="col">
	  <thead>
		<tr>
			<td class="header"><b>{-#tregcntlist#-}</b></td>
			<td class="header"><b>{-#tregnamlist#-}</b></td>
			<td class="header"><b>{-#tregadmlist#-}</b></td>
			<td class="header"><b>{-#tregactlist#-}</b></td>
			<td class="header"><b>{-#tregpublist#-}</b></td>
		</tr>
	  </thead>
	  <tbody id="lst_regionpa">
{-/if-}
{-** ADMINREG: reload region lists **-}
{-if $ctl_reglist-}
{-foreach name=rpa key=key item=item from=$regpa-}
		<tr class="{-if ($smarty.foreach.rpa.iteration - 1) % 2 == 0-}normal{-else-}under{-/if-}"
			onMouseOver="Element.addClassName(this, 'highlight');" onMouseOut="Element.removeClassName(this, 'highlight');"
			onClick="uploadMsg(''); mod='regionpa'; $('cmd').value='update';
					setRegionPA('{-$key-}','{-$item[0]-}','{-$item[1]-}','{-$item[2]-}','{-$item[3]-}','{-$item[4]-}','{-$item[5]-}');">
			<td>{-$item[0]-}</td>
			<td>{-$item[1]-}</td>
			<td>{-$item[3]-}</td>
			<td><input type="checkbox" {-if ($item[4] == 1) -} checked {-/if-} disabled /></td>
			<td><input type="checkbox" {-if ($item[5] == 1) -} checked {-/if-} disabled /></td>
		</tr>
{-/foreach-}
{-/if-}
{-* Continue with adminreg.. *-}
{-if $ctl_adminreg-}
	  </tbody>
	 </table>
	</div>
	<br />
	<input id="add" type="button" value="{-#baddoption#-}" onclick="mod='regionpa'; setRegionPA('','', '', '', '', '1','0'); 
		$('cmd').value='insert'; $('fresult').src='about:blank;'" />
	<span id="regionpastatusmsg" class="dlgmsg"></span><br />
	<iframe name="fresult" id="fresult" frameborder="0" src="about:blank" style="height:30px; width:400px;"></iframe>
	<div id="regionpaaddsect" style="display:none">
   	  <form name="regionpafrm" id="regionpafrm" method="GET" action="javascript: var s=$('regionpafrm').serialize(); 
			mod='regionpa'; sendData('','region.php', s, '');" onSubmit="javascript: 
			var a=new Array('CountryIso','RegionLabel','LangIsoCode','RegionUserAdmin'); return(checkForm(a, '{-#errmsgfrm#-}'));">
		<table class="grid">
			<tr>
				<td>{-#tregcntlist#-}<b style="color:darkred;">*</b></td>
				<td>
					<select id="CountryIso" name="CountryIso" class="fixw" tabindex="1">
						<option value=""></option>
						{-foreach name=CountryList key=key item=item from=$CountryList-}
							<option value="{-$key-}">{-$item-}</option>
						{-/foreach-}
					</select>
				</td>
			</tr>
			<tr>
				<td>{-#tregnamlist#-}<b style="color:darkred;">*</b></td>
				<td><input id="RegionLabel" name="RegionLabel" type="text" maxlength="200" class="line fixw" tabindex="2" /></td>
			</tr>
			<tr>
				<td>{-$dic.DBLangIsoCode[0]-}<b style="color:darkred;">*</b></td>
				<td>
					<select id="LangIsoCode" name="LangIsoCode" {-$ro-} class="line fixw" tabindex="3">
						{-foreach name=LanguageList key=key item=item from=$LanguageList-}
							<option value="{-$key-}">{-$item-}</option>
						{-/foreach-}
					</select>
				</td>
			</tr>
			<tr>
				<td>{-#tregadmlist#-}<b style="color:darkred;">*</b></td>
				<td>
					<select id="RegionUserAdmin" name="RegionUserAdmin" class="fixw" tabindex="4">
						<option value=""></option>
{-foreach name=usr key=key item=item from=$usr-}
						<option value="{-$key-}">{-$item-}</option>
{-/foreach-}
					</select></td>
			</tr>
			<tr>
				<td>{-#tregactlist#-}<b>*</b></td>
				<td><input id="RegionActive" name="RegionActive" type="checkbox" checked tabindex="5" /></td>
			</tr>
			<tr>
				<td>{-#tregpublist#-}<b>*</b></td>
				<td><input id="RegionPublic" name="RegionPublic" type="checkbox" tabindex="6" /></td>
			</tr>
			<tr>
				<td colspan="2" align="center">
					<input id="cmd" name="cmd" type="hidden" />
					<input id="RegionId" name="RegionId" type="hidden" />
					<input type="submit" value="{-#bsave#-}" class="line" tabindex="7" />
					<input type="reset" value="{-#bcancel#-}" 
						onClick="$('regionpaaddsect').style.display='none'; uploadMsg('');" class="line" />
				</td>
			</tr>
		</table>
	  </form>
	</div>
{-/if-}
{-*** CHECK Region ID MESSAGES - STATUS SPAN ***-}
{-if $ctl_chkruuid-}
 {-if $cregion-}
 	{-#tvalregid#-}
 {-else-}
 	{-#tinvregid#-}
 {-/if-}
{-/if-}

{-*** INSERT OR UPDATE MESSAGES - STATUS SPAN ***-}
{-if $ctl_admregmess-}
 {-if $ctl_successfromzip-}
	<script type="text/javascript" language="javascript">
	parent.updateList('lst_regionpa', 'region.php', 'cmd=list');
	</script>
 {-/if-}
 {-if $cfunct == 'insert'-}
 	<span style="color:#42929d; font-size:8pt;">{-#tinsert#-}</span>
 {-elseif $cfunct == 'update'-}
	<span style="color:#42929d; font-size:8pt;">{-#tupdate#-}</span>
 {-else-}
  {-#terrinsupd#-}
 {-/if-}
 {-if $csetrole-}
 	{-#tsetrole#-}
 {-else-}
  {-#terrsetrole#-} [{-$errsetrole-}]
 {-/if-}
 {-$regid-}
{-/if-}
