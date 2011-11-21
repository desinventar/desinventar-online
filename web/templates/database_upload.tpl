{-config_load file="$lg.conf" section="grpAdminDatabaseUpload"-}
<div id="divAdminDatabaseUploadControl">
	<table border="0">
	<tr>
		<td colspan="2">
			{-#msgAdminDatabaseUploadMsg1#-}
		</td>
	</tr>
	<tr>
		<td align="right" valign="bottom">
			{-#msgAdminDatabaseUploadFilename#-} :
		</td>
		<td colspan="2" valign="top">
			<input type="text"   id="txtAdminDatabaseUploadFilename" value="" size="50"/>
			<input type="hidden" id="txtAdminDatabaseUploadId" value="" />
		</td>
	</tr>
	<tr>
		<td>
			<br />
		</td>
		<td>
			<div id="prgAdminDatabaseUploadProgressBar" style="width:120px;height:6px;background-color:#dddddd">
				<div id="prgAdminDatabaseUploadProgressMark" style="width:0px;height:6px;background-color:#0000ff">
				</div>
			</div>
			<span id="prgAdminDatabaseUploadPercent"></span>
		</td>
		<td>
			<input type="button" id="btnAdminDatabaseUploadCancel" class="button line" value="{-#msgAdminDatabaseUploadCancel#-}" />
		</td>
	</tr>
	</table>
</div>

<div id="divFileUploaderControl" style="display:none;">
</div>

<div id="divAdminDatabaseUploadStatusMsg">
	<span class="clsAdminDatabaseUploadStatusMsg" id="msgAdminDatabaseUploadUploadOk">{-#msgAdminDatabaseUploadUploadOk#-}</span>
	<span class="clsAdminDatabaseUploadStatusMsg" id="msgAdminDatabaseUploadUploadError">{-#msgAdminDatabaseUploadUploadError#-}</span>
	<span class="clsAdminDatabaseUploadStatusMsg" id="msgAdminDatabaseUploadFileError">{-#msgAdminDatabaseUploadFileError#-}</span>
	<span class="clsAdminDatabaseUploadStatusMsg" id="msgAdminDatabaseUploadUpdateError">{-#msgAdminDatabaseUploadUpdateError#-}</span>
</div>

<div id="divAdminDatabaseUploadParameters" style="display:none;">
	<hr size="1" />
	<h4>{-#msgAdminDatabaseUploadConfirmReplace#-}</h4><br />
	<table width="100%">
	<tr>
		<td align="right" width="50%">
			{-#msgAdminDatabaseUploadRegionId#-} :
		</td>
		<td width="50%">
			<span id="txtAdminDatabaseUploadRegionId"></span><br />
		</td>
	</tr>
	<tr>
		<td align="right">
			{-#msgAdminDatabaseUploadRegionLabel#-} :
		</td>
		<td>
			<span id="txtAdminDatabaseUploadRegionLabel"></span><br />
		</td>
	</tr>
	<tr>
		<td align="right">
			{-#msgAdminDatabaseUploadLangIsoCode#-} :
		</td>
		<td>
			<span id="txtAdminDatabaseUploadLangIsoCode"></span>
		</td>
	</tr>
	<tr>
		<td align="right">
			{-#msgAdminDatabaseUploadCountryIso#-} :
		</td>
		<td>
			<span id="txtAdminDatabaseUploadCountryIso"></span>
		</td>
	</tr>
	<tr>
		<td colspan="2" align="center">
			<form id="frmAdminDatabaseUpload">
				<input type="hidden" id="RegionId_Prev" value="" />
				<input type="hidden" id="RegionLabel_Prev" value="" />
				<input type="hidden" id="Filename" name="Filename" value="" />
				<input type="hidden" id="DBExist" name="DBExist" value="" />
				<br />
				<input type="button" id="btnAdminDatabaseUploadReplace" class="line" value="{-#msgAdminDatabaseUploadButtonReplace#-}" />
				<input type="button" id="btnAdminDatabaseUploadCancel" class="line" value="{-#msgAdminDatabaseUploadButtonCancel#-}"  />
			</form>
		</td>				
	</tr>
	</table>
</div>
