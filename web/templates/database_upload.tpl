{-config_load file="$lg.conf" section="grpDatabaseUpload"-}
<div class="DatabaseUpload mainblock" style="margin:10px;">
	<div id="divDatabaseUploadControl">
		<table>
		<tr>
			<td colspan="2" title="{-#msgDatabaseUploadFileSpec#-}">
				{-#msgDatabaseUploadMsg1#-}
			</td>
		</tr>
		<tr >
			<td class="bottom right" title="{-#msgDatabaseUploadFileSpec#-}" >
				<span style="display:none;">{-#msgDatabaseUploadFilename#-} :</span>
				<input type="text"   id="txtDatabaseUploadFilename" value="" size="35" />
				<input type="hidden" id="txtDatabaseUploadId" value="" />
			</td>
			<td class="top">
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
			<span class="clsDatabaseUploadType" id="txtDatabaseUploadConfirmStart">{-#msgDatabaseUploadConfirmStart#-}</span>
			<span class="clsDatabaseUploadType" id="txtDatabaseUploadConfirmReplace">{-#msgDatabaseUploadConfirmReplace#-}</span>
			<span class="clsDatabaseUploadType" id="txtDatabaseUploadConfirmCopy">{-#msgDatabaseUploadConfirmCopy#-}</span>
		</h4>
		<table width="100%">
		<tr>
			<td class="right" width="25%">
				{-#msgDatabaseUploadRegionId#-} :
			</td>
			<td width="50%">
				<span id="txtDatabaseUploadRegionId"></span><br />
			</td>
		</tr>
		<tr>
			<td class="right">
				{-#msgDatabaseUploadRegionLabel#-} :
			</td>
			<td>
				<span id="txtDatabaseUploadRegionLabel"></span><br />
			</td>
		</tr>
		<tr>
			<td class="right">
				{-#msgDatabaseUploadCountryIso#-} :
			</td>
			<td>
				<span id="txtDatabaseUploadCountryIso"></span>
			</td>
		</tr>
		<tr>
			<td class="right">
				{-#msgDatabaseUploadLastUpdate#-} :
			</td>
			<td>
				<span id="txtDatabaseUploadRegionLastUpdate"></span>
			</td>
		</tr>
		<tr id="trDatabaseUploadNumberOfRecords">
			<td class="right">
				{-#msgDatabaseUploadNumberOfRecords#-} :
			</td>
			<td>
				<span id="txtDatabaseUploadNumberOfRecords"></span>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="center">
				<div class="center">
					<br />
					<div class="clsDatabaseUploadButtons">
						<a class="button clsDatabaseUploadType" id="btnDatabaseUploadStart"><span>{-#msgDatabaseUploadButtonStart#-}</span></a>
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
		<input type="hidden" id="fldDatabaseUploadMode" value="" />
		<input type="hidden" id="msgDatabaseUploadCopyComplete"   value="{-#msgDatabaseUploadCopyComplete#-}" />
		<input type="hidden" id="msgDatabaseUploadReplaceComplete"   value="{-#msgDatabaseUploadReplaceComplete#-}" />
		<input type="hidden" id="msgDatabaseUploadChooseFile" value="{-#msgDatabaseUploadChooseFile#-}" />
		<span class="clsDatabaseUploadStatusMsg" id="msgDatabaseUploadUpdateOk">{-#msgDatabaseUploadUpdateOk#-}</span>
		<span class="clsDatabaseUploadStatusMsg" id="msgDatabaseUploadErrorOnUpload"><b>{-#msgDatabaseUploadErrorOnUpload#-}</b></span>
		<span class="clsDatabaseUploadStatusMsg" id="msgDatabaseUploadErrorNoInfo"><b>{-#msgDatabaseUploadErrorNoInfo#-}</b></span>
		<span class="clsDatabaseUploadStatusMsg" id="msgDatabaseUploadUpdateError"><b>{-#msgDatabaseUploadUpdateError#-}</b></span>
		<span class="clsDatabaseUploadStatusMsg" id="msgDatabaseUploadWaitForUpload">{-#msgDatabaseUploadWaitForUpload#-}<img src="{-$desinventarURL-}/images/loading.gif" alt="" /></span>
		<span class="clsDatabaseUploadStatusMsg" id="msgDatabaseUploadWaitForUpdate">{-#msgDatabaseUploadWaitForUpdate#-}<img src="{-$desinventarURL-}/images/loading.gif" alt="" /></span>
		<span class="clsDatabaseUploadStatusMsg" id="msgDatabaseUploadWaitForCopy">{-#msgDatabaseUploadWaitForCopy#-}<img src="{-$desinventarURL-}/images/loading.gif" alt="" /></span>
	</div>
</div>
