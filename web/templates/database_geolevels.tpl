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
								<td align="right">
									SHP File
								</td>
								<td valign="top">
									<span class="Filename" data-ext="shp" style="width:100%;"></span>
									<br />
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<div class="ProgressBar" style="width:100%;height:15px;background-color:#dddddd">
										<div class="ProgressMark" style="width:0px;height:15px;background-color:#3bb3c2">
										</div>
									</div>
								</td>
								<td>
									<br />
								</td>
							</tr>
							<tr>
								<td valign="top" colspan="2" style="width:100%;">
									<input type="hidden" class="UploadId" value="" />
									<div id="divGeolevels_FileUploaderControl" style="display:block;">
									</div>
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
