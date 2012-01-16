{-config_load file="$lg.conf" section="grpAdminGeography"-}
{-config_load file="$lg.conf" section="grpGeolevels"-}
<div class="clsGeolevels">
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
					<td class="header GeoLevelLayerFile">
					</td>
					<td class="header GeoLevelLayerCode">
					</td>
					<td class="header GeoLevelLayerName">
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
					<td class="GeoLevelLayerFile">
					</td>
					<td class="GeoLevelLayerCode">
					</td>
					<td class="GeoLevelLayerName">
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div id="divGeolevels_Status">
		<br />
		<a class="button" id="btnGeolevels_Add"><span>{-#msgGeolevels_Add#-}</span></a>
		<br /><br />
		<div class="center">
			<span class="clsGeolevelsStatus" id="msgGeolevels_UpdateOk">{-#msgGeolevels_UpdateOk#-}</span>
			<span class="clsGeolevelsStatus" id="msgGeolevels_UpdateError">{-#msgGeolevels_UpdateError#-}</span>		
		</div>
	</div>
	<table>
		<tr>
			<td valign="top" class="line">
				<div id="divGeolevels_Edit">
					<form id="frmGeolevels_Edit">
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

						<div class="center">
							<a class="button btnSave"><span>{-#msgGeolevels_Save#-}</span></a>
							<a class="button btnCancel"><span>{-#msgGeolevels_Cancel#-}</span></a>
						</div>
					</form>
				</div>
			</td>
			<td valign="top" class="line">
				<div id="divGeocarto_Edit" class="hidden" style="width:100%;min-width:400px;">
					<form id="frmGeocarto">
						<h4>{-#msgGeolevels_GeoCartoSubtitle#-}</h4>
						<input class="GeoLevelId" name="GeoLevelId" type="hidden" value="-1" />
						<table border="0" width="100%">
							<tr>
								<td>
									Code
								</td>
								<td colspan="2">
									<input class="GeoLevelLayerCode" name="GeoLevelLayerCode" type="text" size="30" value="" /><br />
								</td>
							</tr>
							<tr>
								<td>
									Name
								</td>
								<td colspan="2">
									<input class="GeoLevelLayerName" name="GeoLevelLayerName" type="text" size="30" value="" /><br />
								</td>
							</tr>
							<tr class="FileUploader" data-ext="dbf">
								<td>
									DBF File
								</td>
								<td valign="top" style="width:80%;">
									<span class="Filename_DBF Filename" style="width:100%;"></span>
									<input type="hidden" class="Filename" name="Filename_DBF" value="" />
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
									SHP File
								</td>
								<td valign="top" style="width:80%;">
									<span class="Filename_SHP Filename" style="width:100%;"></span>
									<input type="hidden" class="Filename" name="Filename_SHP" value="" />
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
									SHX File
								</td>
								<td valign="top" style="width:80%;">
									<span class="Filename_SHX Filename" style="width:100%;"></span>
									<input type="hidden" class="Filename" name="Filename_SHX" value="" />
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
					</form>
				</div>
			</td>
		</tr>
	</table>
	<div class="hidden">
		<span id="msgGeolevels_UploadChooseFile">{-#msgGeolevels_UploadChooseFile#-}</span>
	</div>
</div>
