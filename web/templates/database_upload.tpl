{-config_load file="$lg.conf" section="grpDatabaseUpload"-}
<div style="margin:10px;">
	<div id="divDatabaseUploadControl">
		<table border="0">
		<tr>
			<td colspan="2" title="{-#msgDatabaseUploadFileSpec#-}">
				{-#msgDatabaseUploadMsg1#-}
			</td>
		</tr>
		<tr >
			<td align="right" valign="bottom" title="{-#msgDatabaseUploadFileSpec#-}" >
				<span style="display:none;">{-#msgDatabaseUploadFilename#-} :</span>
				<input type="text"   id="txtDatabaseUploadFilename" value="" size="35" />
				<input type="hidden" id="txtDatabaseUploadId" value="" />
			</td>
			<td valign="top">
				<div id="divFileUploaderControl" style="display:block;">
				</div>
				<a class="button" id="btnDatabaseUploadCancel"><span>{-#msgDatabaseUploadCancel#-}</span></a>
			</td>
		</tr>
		<tr>
			<td title="{-#msgDatabaseUploadFileSpec#-}">
				<div id="prgDatabaseUploadProgressBar" style="width:100%;height:15px;background-color:#dddddd">
					<div id="prgDatabaseUploadProgressMark" style="width:0px;height:15px;background-color:#3bb3c2">
					</div>
				</div>
			</td>
			<td>
				<br />
			</td>
		</tr>
		</table>
	</div>

	<div id="divDatabaseUploadParameters" style="display:none;">
		<h4>
			<span class="clsDatabaseUploadType" id="txtDatabaseUploadConfirmReplace">{-#msgDatabaseUploadConfirmReplace#-}</span>
			<span class="clsDatabaseUploadType" id="txtDatabaseUploadConfirmCopy">{-#msgDatabaseUploadConfirmCopy#-}</span>
		</h4>
		<table width="100%">
		<tr>
			<td align="right" width="25%">
				{-#msgDatabaseUploadRegionId#-} :
			</td>
			<td width="50%">
				<span id="txtDatabaseUploadRegionId"></span><br />
			</td>
		</tr>
		<tr>
			<td align="right">
				{-#msgDatabaseUploadRegionLabel#-} :
			</td>
			<td>
				<span id="txtDatabaseUploadRegionLabel"></span><br />
			</td>
		</tr>
		<tr>
			<td align="right">
				{-#msgDatabaseUploadCountryIso#-} :
			</td>
			<td>
				<span id="txtDatabaseUploadCountryIso"></span>
			</td>
		</tr>
		<tr>
			<td align="right">
				{-#msgDatabaseUploadLastUpdate#-} :
			</td>
			<td>
				<span id="txtDatabaseUploadRegionLastUpdate"></span>
			</td>
		</tr>
		<tr id="trDatabaseUploadNumberOfRecords">
			<td align="right">
				{-#msgDatabaseUploadNumberOfRecords#-} :
			</td>
			<td>
				<span id="txtDatabaseUploadNumberOfRecords"></span>
			</td>
		</tr>
		<tr>
			<td colspan="2" align="center">
				<div class="center">
					<input type="hidden" id="txtDatabaseUploadFilename" value="" />
					<br />
					<div class="clsDatabaseUploadButtons">
						<a class="button clsDatabaseUploadType" id="btnDatabaseUploadCopy"><span>{-#msgDatabaseUploadButtonCopy#-}</span></a>
						<a class="button clsDatabaseUploadType" id="btnDatabaseUploadReplace"><span>{-#msgDatabaseUploadButtonReplace#-}</span></a>
						<a class="button" id="btnDatabaseUploadReplaceCancel"><span>{-#msgDatabaseUploadButtonCancel#-}</span></a>
					</div>
				</div>
			</td>				
		</tr>
		</table>
	</div>

	<div id="divDatabaseUploadStatusMsg" class="center">
		<input type="hidden" id="msgDatabaseUploadComplete"   value="{-#msgDatabaseUploadComplete#-}" />
		<input type="hidden" id="msgDatabaseUploadChooseFile" value="{-#msgDatabaseUploadChooseFile#-}" />
		<span class="clsDatabaseUploadStatusMsg" id="msgDatabaseUploadUpdateOk">{-#msgDatabaseUploadUpdateOk#-}</span>
		<span class="clsDatabaseUploadStatusMsg" id="msgDatabaseUploadErrorOnUpload"><b>{-#msgDatabaseUploadErrorOnUpload#-}</b></span>
		<span class="clsDatabaseUploadStatusMsg" id="msgDatabaseUploadUpdateError"><b>{-#msgDatabaseUploadUpdateError#-}</b></span>
		<span class="clsDatabaseUploadStatusMsg" id="msgDatabaseUploadWaitForUpload">{-#msgDatabaseUploadWaitForUpload#-}<img src="{-$desinventarURL-}/images/loading.gif" alt="" /></span>
		<span class="clsDatabaseUploadStatusMsg" id="msgDatabaseUploadWaitForUpdate">{-#msgDatabaseUploadWaitForUpdate#-}<img src="{-$desinventarURL-}/images/loading.gif" alt="" /></span>
		<span class="clsDatabaseUploadStatusMsg" id="msgDatabaseUploadWaitForCopy">{-#msgDatabaseUploadWaitForCopy#-}<img src="{-$desinventarURL-}/images/loading.gif" alt="" /></span>
	</div>
</div>
