{-config_load file="$lg.conf" section="di8_region"-}
<h2>{-#ttname#-}</h2>
<div id="divDatabaseAdminList">
	<div class="dwin" style="width:100%;height:200px;">
		<table id="tblDatabaseList" class="col">
			<thead>
				<tr>
					<th class="header">
						<b>{-#tregcntlist#-}</b>
					</th>
					<th class="header">
						<b>{-#tregnamlist#-}</b>
					</th>
					<th class="header">
						<b>{-#tregadmlist#-}</b>
					</th>
					<th class="header">
						<b>{-#tregactlist#-}</b>
					</th>
					<th class="header">
						<b>{-#tregpublist#-}</b>
					</th>
					<th class="header" id="RegionId" style="display:none;">
					</th>
					<th class="header" id="LangIsoCode" style="display:none;">
					</th>
				</tr>
			</thead>
			<tbody id="lst_regionpa">
				<tr style="display:none;">
					<td class="CountryIso">
					</td>
					<td class="RegionLabel" style="width:100%;">
					</td>
					<td>
						<span class="RegionAdminUserFullName"></span>/<span class="RegionAdminUserId"></span>
					</td>
					<td>
						<input class="RegionActive" type="checkbox" disabled />
					</td>
					<td>
						<input class="RegionPublic" type="checkbox" disabled />
					</td>
					<td class="RegionId"            style="display:none;">
					</td>
					<td class="LangIsoCode"         style="display:none;">
					</td>
				</tr>
			</tbody>
		</table>
	</div>	
	<input id="btnDatabaseAdminNew"    type="button" value="{-#baddoption#-}" style="display:none;" />
</div>

<div id="divDatabaseAdminUpdate" style="display:none;">
	<span class="RegionLabel"></span><br />
	<input id="btnDatabaseAdminEdit"   class="clsDatabaseAdminButton" type="button" value="Edit" />
	<input id="btnDatabaseAdminExport" class="clsDatabaseAdminButton" type="button" value="Export" />
	<input id="btnDatabaseAdminImport" class="clsDatabaseAdminButton" type="button" value="Import" />
	<br />
</div>

<div id="divDatbaseAdminEdit" style="display:none">
	<form id="frmDatabaseEdit">
		<table class="grid">
			<tr>
				<td>
					{-#tregcntlist#-}<b style="color:darkred;">*</b>
				</td>
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
				<td>
					<span id="lblRegionId">RegionId</span>
				</td>
				<td>
					<input id="RegionId" name="RegionId" type="text" maxlength="50" class="line fixw" tabindex="2" />
				</td>
			</tr>
			<tr>
				<td>
					{-#tregnamlist#-}<b style="color:darkred;">*</b>
				</td>
				<td>
					<input id="RegionLabel" name="RegionLabel" type="text" maxlength="200" class="line fixw" tabindex="3" />
				</td>
			</tr>
			<tr>
				<td>
					{-$dic.DBLangIsoCode[0]-}<b style="color:darkred;">*</b>
				</td>
				<td>
					<select id="LangIsoCode" name="LangIsoCode" {-$ro-} class="line fixw" tabindex="4">
						{-foreach name=LanguageList key=key item=item from=$LanguageList-}
							<option value="{-$key-}">{-$item-}</option>
						{-/foreach-}
					</select>
				</td>
			</tr>
			<tr>
				<td>
					{-#tregadmlist#-}<b style="color:darkred;">*</b>
				</td>
				<td>
					<select id="RegionUserAdmin" name="RegionUserAdmin" class="fixw" tabindex="5">
						<option value=""></option>
						{-foreach name=usr key=key item=item from=$usr-}
							<option value="{-$key-}">{-$item-}</option>
						{-/foreach-}
					</select>
				</td>
			</tr>
			<tr>
				<td>
					{-#tregactlist#-}<b>*</b>
				</td>
				<td>
					<input id="RegionActive" name="RegionActive" type="checkbox" checked tabindex="6" />
				</td>
			</tr>
			<tr>
				<td>
					{-#tregpublist#-}<b>*</b>
				</td>
				<td>
					<input id="RegionPublic" name="RegionPublic" type="checkbox" tabindex="7" />
				</td>
			</tr>
			<tr>
				<td colspan="2" align="center">
					<input type="hidden" id="cmd" name="cmd" />
					<input type="hidden" id="RegionStatus" name="RegionStatus" />
					<input type="submit" value="{-#bsave#-}" class="line" tabindex="8" />
					<input type="reset" value="{-#bcancel#-}" onClick="$('regionpaaddsect').style.display='none'; uploadMsg('');" class="line" />
				</td>
			</tr>
		</table>
	</form>
</div>

