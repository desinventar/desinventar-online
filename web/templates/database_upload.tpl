{-config_load file="$lg.conf" section="grpAdminDatabaseUpload"-}
<div style="margin:10px;">
	<div id="divAdminDatabaseUploadControl">
		<table border="0">
		<tr>
			<td colspan="2">
				{-#msgAdminDatabaseUploadMsg1#-}
			</td>
		</tr>
		<tr >
			<td align="right" valign="bottom">
				<span ext:qtip="{-#msgAdminDatabaseUploadFileSpec#-}">{-#msgAdminDatabaseUploadFilename#-} :</span>
			</td>
			<td colspan="2" valign="top" ext:qtip="{-#msgAdminDatabaseUploadFileSpec#-}">
				<input type="text"   id="txtAdminDatabaseUploadFilename" value="" size="35" ext:qtip="{-#msgAdminDatabaseUploadFileSpec#-}" />
				<input type="hidden" id="txtAdminDatabaseUploadId" value="" />
			</td>
		</tr>
		<tr>
			<td>
				<br />
			</td>
			<td ext:qtip="{-#msgAdminDatabaseUploadFileSpec#-}">
				<div id="prgAdminDatabaseUploadProgressBar" style="width:120px;height:6px;background-color:#dddddd">
					<div id="prgAdminDatabaseUploadProgressMark" style="width:0px;height:6px;background-color:#0000ff">
					</div>
				</div>
			</td>
			<td>
				<div id="divFileUploaderControl" style="display:block;">
				</div>
				<a class="button" id="btnAdminDatabaseUploadCancel"><span>{-#msgAdminDatabaseUploadCancel#-}</span></a>
			</td>
		</tr>
		</table>
	</div>

	<div id="divAdminDatabaseUploadParameters" style="display:none;">
		<h4>{-#msgAdminDatabaseUploadConfirmReplace#-}</h4><br />
		<table width="100%">
		<tr>
			<td align="right" width="25%">
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
				<div class="center">
					<input type="hidden" id="txtAdminDatabaseUploadFilename" value="" />
					<br />
					<a class="button" id="btnAdminDatabaseUploadReplace"><span>{-#msgAdminDatabaseUploadButtonReplace#-}</span></a>
					<a class="button" id="btnAdminDatabaseUploadReplaceCancel"><span>{-#msgAdminDatabaseUploadButtonCancel#-}</span></a>
				</div>
			</td>				
		</tr>
		</table>
	</div>

	<div id="divAdminDatabaseUploadStatusMsg">
		<input type="hidden" id="msgAdminDatabaseUploadComplete"   value="{-#msgAdminDatabaseUploadComplete#-}" />
		<input type="hidden" id="msgAdminDatabaseUploadChooseFile" value="{-#msgAdminDatabaseUploadChooseFile#-}" />
		<span class="clsAdminDatabaseUploadStatusMsg" id="msgAdminDatabaseUploadUpdateOk">{-#msgAdminDatabaseUploadUpdateOk#-}</span>
		<span class="clsAdminDatabaseUploadStatusMsg" id="msgAdminDatabaseUploadErrorOnUpload">{-#msgAdminDatabaseUploadErrorOnUpload#-}</span>
		<span class="clsAdminDatabaseUploadStatusMsg" id="msgAdminDatabaseUploadUpdateError">{-#msgAdminDatabaseUploadUpdateError#-}</span>
		<span class="clsAdminDatabaseUploadStatusMsg" id="msgAdminDatabaseUploadWaitForUpdate">{-#msgAdminDatabaseUploadWaitForUpdate#-}<img src="{-$desinventarURL-}/images/loading.gif" alt="" /></span>
	</div>
</div>
