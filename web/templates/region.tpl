{-config_load file="$lg.conf" section="grpMenuRegion"-}
<h2>{-#ttname#-}</h2>
<div id="divAdminDatabaseList" style="display:none;">
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
	<input id="btnAdminDatabaseNew" type="button" value="{-#baddoption#-}" class="line" style="display:none;" />
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

	<div id="divAdminDatabaseEdit" class="clsAdminDatabase dwin" style="display:none">
	</div>
	<div id="divAdminDatabaseExport" class="clsAdminDatabase" style="display:none;">
	</div>
	<div id="divAdminDatabaseImport" class="clsAdminDatabase" style="display:none;">
	</div>
</div>


