{-config_load file=`$lg`.conf section="di8_region"-}
{-* ADMINREG: Interface to Edit Portal Admin *-}
{-if $ctl_adminreg-}
	<h2>{-#ttname#-}</h2>
	<br />
	<div class="dwin" style="width:500px; height:150px;">
	 <table id="tblDatabaseList" class="col">
	  <thead>
		<tr>
			<th class="header"><b>{-#tregcntlist#-}</b></th>
			<th class="header"><b>{-#tregnamlist#-}</b></th>
			<th class="header"><b>{-#tregadmlist#-}</b></th>
			<th class="header"><b>{-#tregactlist#-}</b></th>
			<th class="header"><b>{-#tregpublist#-}</b></th>
			<th class="header" id="RegionId"></th>
			<th class="header" id="LangIsoCode"></th>
		</tr>
	  </thead>
	  <tbody id="lst_regionpa">
{-/if-}
{-** ADMINREG: reload region lists **-}
{-if $ctl_reglist-}
{-foreach name=rpa key=key item=item from=$RegionList-}
		<tr>
			<td id="CountryIso">{-$item.CountryIso-}</td>
			<td id="RegionLabel">{-$item.RegionLabel-}</td>
			<td id="RegionUserAdmin">{-$item.UserId_AdminRegion-}</td>
			<td id="RegionActive"><input type="checkbox" {-if ($item.RegionActive == 1) -} checked {-/if-} disabled /></td>
			<td id="RegionPublic"><input type="checkbox" {-if ($item.RegionPublic == 1) -} checked {-/if-} disabled /></td>
			<td id="RegionId">{-$key-}</td>
			<td id="LangIsoCode">{-$item.LangIsoCode-}</td>
		</tr>
{-/foreach-}
{-/if-}
{-* Continue with adminreg.. *-}
{-if $ctl_adminreg-}
	  </tbody>
	 </table>
	</div>
	<br />
	<input id="btnDatabaseEditAdd" type="button" value="{-#baddoption#-}"  />
	<span id="regionpastatusmsg" class="dlgmsg"></span><br />
	<div id="divDatabaseEditResult">
	</div>
	<div id="regionpaaddsect" style="display:none">
  	  <form id="frmDatabaseEdit">
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
				<td><span id="lblRegionId">RegionId</span></td>
				<td><input id="RegionId" name="RegionId" type="text" maxlength="50" class="line fixw" tabindex="2" /></td>
			</tr>
			<tr>
				<td>{-#tregnamlist#-}<b style="color:darkred;">*</b></td>
				<td><input id="RegionLabel" name="RegionLabel" type="text" maxlength="200" class="line fixw" tabindex="3" /></td>
			</tr>
			<tr>
				<td>{-$dic.DBLangIsoCode[0]-}<b style="color:darkred;">*</b></td>
				<td>
					<select id="LangIsoCode" name="LangIsoCode" {-$ro-} class="line fixw" tabindex="4">
						{-foreach name=LanguageList key=key item=item from=$LanguageList-}
							<option value="{-$key-}">{-$item-}</option>
						{-/foreach-}
					</select>
				</td>
			</tr>
			<tr>
				<td>{-#tregadmlist#-}<b style="color:darkred;">*</b></td>
				<td>
					<select id="RegionUserAdmin" name="RegionUserAdmin" class="fixw" tabindex="5">
						<option value=""></option>
{-foreach name=usr key=key item=item from=$usr-}
						<option value="{-$key-}">{-$item-}</option>
{-/foreach-}
					</select></td>
			</tr>
			<tr>
				<td>{-#tregactlist#-}<b>*</b></td>
				<td><input id="RegionActive" name="RegionActive" type="checkbox" checked tabindex="6" /></td>
			</tr>
			<tr>
				<td>{-#tregpublist#-}<b>*</b></td>
				<td><input id="RegionPublic" name="RegionPublic" type="checkbox" tabindex="7" /></td>
			</tr>
			<tr>
				<td colspan="2" align="center">
					<input type="hidden" id="cmd" name="cmd" />
					<input type="hidden" id="RegionStatus" name="RegionStatus" />
					<input type="submit" value="{-#bsave#-}" class="line" tabindex="8" />
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
