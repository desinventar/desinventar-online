{-config_load file="$lg.conf" section="grpAdminGeography"-}
{-config_load file="$lg.conf" section="grpDatabaseGeolevels"-}
<div class="clsDatabaseGeolevels">
	<b>{-#msgDatabaseGeolevels_Title#-}</b><br />
	<div class="dwin" style="width:100%;height:100px;">
		<table class="grid">
			<thead>
				<tr>
					<td class="header" style="display:none;">
						<b>Id</b>
					</td>
					<td class="header width40">
						<b>{-#msgDatabaseGeolevels_GeoLelvelName#-}</b>
					</td>
					<td class="header">
						<b>{-#msgDatabaseGeolevels_GeoLevelDesc#-}</b>
					</td>
					<td class="header">
						<b>{-#msgDatabaseGeolevels_GeoLevelActive#-}</b>
					</td>
				</tr>
			</thead>
			<tbody id="tbodyDatabaseGeolevels_List">
				<tr style="display:none;">
					<td class="GeoLevelId" style="display:none;">
					</td>
					<td class="GeoLevelName width40">
					</td>
					<td class="GeoLevelDesc">
					</td>
					<td class="GeoLevelActive">
						<input type="checkbox" />						
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<br />
	<a class="button" id="btnDatabaseGeolevels_Add"><span>{-#msgDatabaseGeolevels_Add#-}</span></a>
	<br /><br />
	<div style="display:block; width:100%;">
		<form id="frmDatabaseGeolevelsEdit">
			{-#msgDatabaseGeolevels_GeoLevelName#-}<b style="color:darkred;">*</b>
			<br />
			<input id="fldDatabaseGeolevels_GeoLevelName" name="GeoLevel[GeoLevelName]" 
				type="text" tabindex="1" class="line" style="width:400px;" />
			<br /><br />
			{-#msgDatabaseGeolevels_GeoLevelDesc#-}<b style="color:darkred;">*</b>
			<br />
			<textarea id="fldDatabaseGeolevels_GeoLevelDesc" name="GeoLevel[GeoLevelDesc]"
				tabindex="2" style="width:400px;"></textarea>
			<br />
			<div class="center">
				<a class="button" id="btnDatabaseGeolevels_Save"><span>Save</span></a>
				<a class="button" id="btnDatabaseGeolevels_Cancel"><span>Cancel</span></a>
			</div>
			<hr />
			<table class="grid">
				<tr>
				<td>
					<a class="info" href="javascript:void(null)" onMouseOver="showtip('{-$dic.DBLevLayerFile[2]-}')">
					{-$dic.DBLevLayerFile[0]-}<span>{-$dic.DBLevLayerFile[1]-}</span></a>
					<input id="GeoLevelLayerFile" name="GeoLevelLayerFile" type="hidden" /><br />
					DBF File <input name="GeoLevelFileDBF" type="file" {-$ro-} tabindex="3" /><br />
					SHP File <input name="GeoLevelFileSHP" type="file" {-$ro-} tabindex="4" /><br />
					SHX File <input name="GeoLevelFileSHX" type="file" {-$ro-} tabindex="5" />
				</td>
				<td>
					<a class="info" href="javascript:void(null)" onMouseOver="showtip('{-$dic.DBLevLayerCode[2]-}')">
					{-$dic.DBLevLayerCode[0]-}<span>{-$dic.DBLevLayerCode[1]-}</span></a><br />
					<input id="GeoLevelLayerCode" name="GeoLevelLayerCode" type="text" {-$ro-} class="line" style="width:150px;"
	   					tabindex="6" onFocus="showtip('{-$dic.DBLevColCode[2]-}')" />
					<br /><br />
					<a class="info" href="javascript:void(null)" onMouseOver="showtip('{-$dic.DBLevLayerName[2]-}')">
					{-$dic.DBLevLayerName[0]-}<span>{-$dic.DBLevLayerName[1]-}</span></a><br />
					<input id="GeoLevelLayerName" name="GeoLevelLayerName" type="text" {-$ro-} class="line" style="width:150px;"
						tabindex="7" onFocus="showtip('{-$dic.DBLevLayerName[2]-}')" />
				</td>
				</tr>
				<tr>
				<td colspan="2">
					<div class="center">
						<input id="_REG" name="_REG" value="{-$reg-}" type="hidden" />
						<input id="GeoLevelId" name="GeoLevelId" type="hidden" />
						<input id="cmd" name="cmd" type="hidden" /><br />
						<input type="submit" value="{-#bsave#-}" {-$ro-} class="line" tabindex="8" />
						<input type="reset" value="{-#bcancel#-}" class="line" 
							onClick="$('levaddsect').style.display='none'; mod='lev'; uploadMsg('');" {-$ro-} />
					</div>
				</td>
				</tr>
			</table>
		</form>
	</div>
</div>
