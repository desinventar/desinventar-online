{-config_load file="$lg.conf" section="di8_region"-}
<h2>{-#ttname#-}</h2>
<div id="divAdminDatabaseList">
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
			<tbody id="tbodyDatabaseList">
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
	<input id="btnAdminDatabaseNew"    type="button" value="{-#baddoption#-}" style="display:none;" />
</div>

<div id="divAdminDatabaseUpdate" style="display:none;">
	<h3><span class="RegionLabel"></span></h3>
	<span class="RegionId"></span>
	<br />
	<input id="btnAdminDatabaseEdit"   class="clsAdminDatabaseButton" type="button" value="Edit" />
	<input id="btnAdminDatabaseExport" class="clsAdminDatabaseButton" type="button" value="Export" />
	<input id="btnAdminDatabaseImport" class="clsAdminDatabaseButton" type="button" value="Import" />
	<br />
	<a id="btnAdminDatabaseSelect" title="Select a new database from list">[Select another database]</a>

	<div id="divAdminDatabaseEdit" class="dwin" style="display:none">
		<form id="frmRegionEdit">
			<table class="grid">
				<tr>
					<td>
						{-#tregcntlist#-}<b style="color:darkred;">*</b>
					</td>
					<td>
						<select id="frmRegionEdit_CountryIso" name="Region[CountryIso]" class="fixw" tabindex="1">
							<option value=""></option>
						</select>
					</td>
				</tr>
				<tr>
					<td>
						<span id="lblRegionId">RegionId</span>
					</td>
					<td>
						<input id="frmRegionEdit_RegionId" name="Region[RegionId]" type="text" maxlength="50" class="line fixw" tabindex="2" />
					</td>
				</tr>
				<tr>
					<td>
						{-#tregnamlist#-}<b style="color:darkred;">*</b>
					</td>
					<td>
						<input id="frmRegionEdit_RegionLabel" name="Region[RegionLabel]" type="text" maxlength="200" class="line fixw" tabindex="3" />
					</td>
				</tr>
				<tr>
					<td>
						{-$dic.DBLangIsoCode[0]-}<b style="color:darkred;">*</b>
					</td>
					<td>
						<select id="frmRegionEdit_LangIsoCode" name="Region[LangIsoCode]" {-$ro-} class="line fixw" tabindex="4">
						</select>
					</td>
				</tr>
				<tr>
					<td>
						{-#tregactlist#-}<b>*</b>
					</td>
					<td>
						<input id="frmRegionEdit_RegionActive" name="" type="checkbox" checked tabindex="6" />
					</td>
				</tr>
				<tr>
					<td>
						{-#tregpublist#-}<b>*</b>
					</td>
					<td>
						<input id="frmRegionEdit_RegionPublic" name="" type="checkbox" tabindex="7" />
					</td>
				</tr>
				<!--
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
				-->
				<tr>
					<td colspan="2" align="center">
						<input id="frmRegionEdit_RegionStatus" type="hidden" name="Region[RegionStatus]" value="0" />
						<input id="frmRegionEdit_Cmd"          type="hidden" name="cmd" />
						<input id="frmRegionEdit_Submit"       type="submit" value="{-#bsave#-}"   class="line" tabindex="8" />
						<input id="frmRegionEdit_Cancel"       type="reset"  value="{-#bcancel#-}" class="line" tabindex="9" />
						
					</td>
				</tr>
			</table>
		</form>
	</div>
</div>


