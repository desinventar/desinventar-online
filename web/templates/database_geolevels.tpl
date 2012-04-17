{-config_load file="$lg.conf" section="grpAdminGeography"-}
{-config_load file="$lg.conf" section="grpGeolevels"-}
<div class="Geolevels mainblock">
	<b>{-#msgGeolevels_Title#-}</b><br />
	<div id="divGeolevels_List" class="dwin">
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
					<td class="header center HasMap">
						{-#msgGeolevels_HasMap#-}
					</td>
					<td class="header GeoLevelLayerFile hidden">
					</td>
					<td class="header GeoLevelLayerCode hidden">
					</td>
					<td class="header GeoLevelLayerName hidden">
					</td>
					<td class="header GeoLevelLayerParentCode hidden">
					</td>
				</tr>
			</thead>
			<tbody id="tbodyGeolevels_List">
				<tr>
					<td class="GeoLevelId">
					</td>
					<td class="GeoLevelName">
					</td>
					<td class="GeoLevelDesc">
					</td>
					<td class="GeoLevelActive">
						<input type="checkbox" disabled="disabled" />						
					</td>
					<td class="HasMap">
						<input type="checkbox" disabled="disabled" />						
					</td>
					<td class="GeoLevelLayerFile hidden">
					</td>
					<td class="GeoLevelLayerCode hidden">
					</td>
					<td class="GeoLevelLayerName hidden">
					</td>
					<td class="GeoLevelLayerParentCode hidden">
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div>
		<br />
		<a class="button" id="btnGeolevels_Add"><span>{-#msgGeolevels_Add#-}</span></a>
		<br /><br />
	</div>
	<div id="divGeolevels_Edit" class="hidden">
		<table>
			<tr>
				<td valign="top" class="line">
					<form id="frmGeolevel">
						<h4>{-#msgGeolevels_GeoLevelSubtitle#-}</h4>
						<input class="GeoLevelId" name="GeoLevelId" type="hidden" value="-1" />

						{-#msgGeolevels_GeoLevelName#-}<b class="required">*</b>
						<br />
						<input class="GeoLevelName line" name="GeoLevelName" type="text" tabindex="1" />
						<br /><br />

						{-#msgGeolevels_GeoLevelDesc#-}<b class="required">*</b>
						<br />
						<textarea class="GeoLevelDesc" name="GeoLevelDesc" tabindex="2"></textarea>
						<br />

						<input class="GeoLevelActive" name="GeoLevelActive" type="hidden" value="1" />
						<span class="GeoLevelActiveLabel">{-#msgGeolevels_GeoLevelActive#-}</span>
						<input class="GeoLevelActiveCheckbox" type="checkbox" tabindex="3" />

						<br />
						<div class="GeocartoEdit">
							<h4>{-#msgGeocarto_Subtitle#-}</h4>
							<table width="100%">
								<tr class="FileUploader" data-ext="dbf">
									<td>
										{-#msgGeocarto_File#-} (DBF)
									</td>
									<td valign="top" style="width:50%;">
										<span class="Filename_DBF uploaded" style="width:100%;"></span>
										<input type="hidden" class="filename" name="filename.DBF" value="" />
										<br />
									</td>
									<td>
										<input type="hidden" class="UploadId_SHP UploadId" value="" />
										<div id="FileUploaderControl_DBF" class="FileUploaderControl" style="display:block;">
										</div>
									</td>
								</tr>
								<tr class="FileUploader" data-ext="shp">
									<td>
										{-#msgGeocarto_File#-} (SHP)
									</td>
									<td valign="top" style="width:50%;">
										<span class="Filename_SHP uploaded" style="width:100%;"></span>
										<input type="hidden" class="filename" name="filename.SHP" value="" />
										<br />
									</td>
									<td>
										<input type="hidden" class="UploadId_SHP UploadId" value="" />
										<div id="FileUploaderControl_SHP" class="FileUploaderControl" style="display:block;">
										</div>
									</td>
								</tr>
								<tr class="FileUploader" data-ext="shx">
									<td>
										{-#msgGeocarto_File#-} (SHX)
									</td>
									<td valign="top" style="width:50%;">
										<span class="Filename_SHX uploaded" style="width:100%;"></span>
										<input type="hidden" class="filename" name="filename.SHX" value="" />
										<br />
									</td>
									<td>
										<input type="hidden" class="UploadId_SHX UploadId" value="" />
										<div id="FileUploaderControl_SHX" class="FileUploaderControl" style="display:block;">
										</div>
									</td>
								</tr>
								<tr>
									<td colspan="3">
										<div class="ProgressBar" style="width:100%;height:15px;background-color:#dddddd">
											<div class="ProgressMark" style="width:0px;height:15px;background-color:#3bb3c2">
											</div>
										</div>
									</td>
								</tr>
								<tr>
									<td colspan="2">
									</td>
									<td valign="top">
										<a class="button btnUploadCancel"><span>{-#msgGeolevels_UploadCancel#-}</span></a>
									</td>
								</tr>
							</table>
							<table width="100%">
								<tr>
									<td>
										<span title="{-#msgGeocarto_NameTooltip#-}">{-#msgGeocarto_Name#-}</span>
									</td>
									<td>
										<select class="GeoLevelLayerName" name="GeoLevelLayerName">
											<option value="">---</option>
										</select>
									</td>
								</tr>
								<tr>
									<td>
										<span title="{-#msgGeocarto_CodeTooltip#-}">{-#msgGeocarto_Code#-}</span>
									</td>
									<td>
										<select class="GeoLevelLayerCode" name="GeoLevelLayerCode">
											<option value="">---</option>
										</select>
									</td>
								</tr>
								<tr>
									<td>
										<span title="{-#msgGeocarto_ParentCodeTooltip#-}">{-#msgGeocarto_ParentCode#-}</span>
									</td>
									<td>
										<select class="GeoLevelLayerParentCode" name="GeoLevelLayerParentCode">
											<option value="">---</option>
										</select>
									</td>
								</tr>
								<tr>
									<td colspan="2" class="center">
										<br />
										<input class="OptionImportGeography" name="option.ImportGeography" type="hidden" value="0" />
										<input class="OptionImportGeographyCheckbox" type="checkbox" />
										<span  class="OptionImportGeographyText" title="{-#msgGeocarto_ImportGeographyTooltip#-}">
											{-#msgGeocarto_ImportGeography#-}
										</span>
									</td>
								</tr>
							</table>
							<br />
						</div>
						<div class="center">
							<a class="button btnSave"><span>{-#msgGeolevels_Save#-}</span></a>
							<a class="button btnCancel"><span>{-#msgGeolevels_Cancel#-}</span></a>
						</div>
						<br />
					</form>
				</td>
			</tr>
		</table>
	</div>
	<div class="status center">
		<span class="status hidden statusUploadOk">{-#msgGeocarto_UploadOk#-}<br /></span>
		<span class="status hidden statusUploadError">{-#msgGeocarto_UploadError#-}<br /></span>
		<span class="status hidden statusUpdateOk">{-#msgGeolevels_UpdateOk#-}<br /></span>
		<span class="status hidden statusUpdateError">{-#msgGeolevels_UpdateError#-}<br /></span>
		<span class="status hidden statusMissingFiles">{-#msgGeocarto_MissingFiles#-} (DBF,SHP,SHX)<br /></span>
		<span class="status hidden statusRequiredFields">{-#msgGeocarto_RequiredFields#-}<br/></span>
		<span class="status hidden statusCreatingGeography">{-#msgGeocarto_CreatingGeography#-} <img src="{-$desinventarURL-}/images/loading.gif" alt="" /><br /></span>
	</div>
	<div class="hidden">
		<span id="msgGeolevels_UploadChooseFile">{-#msgGeolevels_UploadChooseFile#-}</span>
	</div>
</div>
