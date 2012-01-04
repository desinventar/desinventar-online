{-config_load file="$lg.conf" section="grpAdminGeography"-}
{-config_load file="$lg.conf" section="grpGeolevels"-}
<div class="clsGeolevels">
	<b>{-#msgGeolevels_Title#-}</b><br />
	<div class="dwin" style="width:100%;height:100px;">
		<table id="tblGeolevels_List" class="grid">
			<thead>
				<tr>
					<td class="header GeoLevelId">
						<b>Id</b>
					</td>
					<td class="header GeoLevelName">
						<b>{-#msgGeolevels_GeoLevelName#-}</b>
					</td>
					<td class="header GeoLevelDesc">
						<b>{-#msgGeolevels_GeoLevelDesc#-}</b>
					</td>
					<td class="header GeoLevelActive">
						<b>{-#msgGeolevels_GeoLevelActive#-}</b>
					</td>
				</tr>
			</thead>
			<tbody id="tbodyGeolevels_List">
				<tr style="display:none;">
					<td class="GeoLevelId">
					</td>
					<td class="GeoLevelName">
					</td>
					<td class="GeoLevelDesc">
					</td>
					<td class="GeoLevelActive">
						<input type="checkbox" disabled="disabled" />						
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<br />
	<a class="button" id="btnGeolevels_Add"><span>{-#msgGeolevels_Add#-}</span></a>
	<br /><br />
	<div class="center">
		<span class="clsGeolevelsStatus" id="msgGeolevels_UpdateOk">{-#msgGeolevels_UpdateOk#-}</span>
		<span class="clsGeolevelsStatus" id="msgGeolevels_UpdateError">{-#msgGeolevels_UpdateError#-}</span>		
	</div>
	<div id="divGeolevels_Edit">
		<form id="frmGeolevels_Edit">
			<input id="fldGeolevels_GeoLevelId" name="GeoLevelId" type="hidden" value="-1" />

			{-#msgGeolevels_GeoLevelName#-}<b style="color:darkred;">*</b>
			<br />
			<input id="fldGeolevels_GeoLevelName" name="GeoLevelName" 
				type="text" tabindex="1" class="line" style="width:400px;" />
			<br /><br />

			{-#msgGeolevels_GeoLevelDesc#-}<b style="color:darkred;">*</b>
			<br />
			<textarea id="fldGeolevels_GeoLevelDesc" name="GeoLevelDesc"
				tabindex="2" style="width:400px;"></textarea>
			<br />

			<input id="fldGeolevels_GeoLevelActive" name="GeoLevelActive" type="hidden" value="0" />
			<span id="txtGeolevels_GeoLevelActive">{-#msgGeolevels_GeoLevelActive#-}</span>
			<input id="fldGeolevels_GeoLevelActiveCheckbox" type="checkbox" tabindex="3" />
			<br />

			<div class="center">
				<a class="button" id="btnGeolevels_Save"><span>Save</span></a>
				<a class="button" id="btnGeolevels_Cancel"><span>Cancel</span></a>
			</div>
			<table class="grid" style="display:none;">
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
