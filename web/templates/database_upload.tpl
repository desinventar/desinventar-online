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
	<span class="clsAdminDatabaseUploadStatusMsg" id="msgAdminDatabaseUploadDBUpdated">{-#msgAdminDatabaseUploadDBUpdated#-}</span>
	<span class="clsAdminDatabaseUploadStatusMsg" id="msgAdminDatabaseUploadUpdateError">{-#msgAdminDatabaseUploadUpdateError#-}</span>
</div>

<div id="divAdminDatabaseUploadParameters" style="display:none;">
	<form id="frmAdminDatabaseUpload">
		<input type="hidden" id="RegionId_Prev" value="" />
		<input type="hidden" id="RegionLabel_Prev" value="" />
		<input type="hidden" id="Filename" name="Filename" value="" />
		<input type="hidden" id="DBExist" name="DBExist" value="" />
		<table>
		<tr>
			<td>
				<br />
			</td>
			<td>
				<span id="spanAdminDatabaseUploadNew"   ><input type="radio" id="radioAdminDatabaseUploadOptionNew"    class="radioAdminDatabaseUploadOption" name="Mode" value="NEW" checked />{-#msgAdminDatabaseUploadDatabaseNew#-}<br /></span>
				<span id="spanAdminDatabaseUploadClone" ><input type="radio" id="radioAdminDatabaseUploadOptionClone"  class="radioAdminDatabaseUploadOption" name="Mode" value="CLONE"       />{-#msgAdminDatabaseUploadDatabaseClone#-}<br /></span>
				<span id="spanAdminDatabaseUploadUpdate"><input type="radio" id="radioAdminDatabaseUploadOptionUpdate" class="radioAdminDatabaseUploadOption" name="Mode" value="UPDATE"      />{-#msgAdminDatabaseUploadDatabaseUpdate#-}<br /></span>
			</td>
		</tr>
		<tr>
			<td align="right">
				{-#msgAdminDatabaseUploadRegionId#-} :
			</td>
			<td>
				<input type="text" id="RegionId" name="RegionId" class="line fixw" size="40" /><br />
			</td>
		</tr>
		<tr>
			<td align="right">
				{-#msgAdminDatabaseUploadRegionLabel#-} :
			</td>
			<td>
				<input type="text" id="RegionLabel" name="RegionLabel" class="line fixw" size="80" /><br />
			</td>
		</tr>
		<tr>
			<td align="right">
				{-#msgAdminDatabaseUploadLangIsoCode#-} :
			</td>
			<td>
				<span id="spanLangIsoCode"></span>
			</td>
		</tr>
		<tr>
			<td align="right">
				{-#msgAdminDatabaseUploadCountryIso#-} :
			</td>
			<td>
				<span id="spanCountryIso"></span>
			</td>
		</tr>
		<tr>
			<td colspan="2" align="right">
				<input type="submit" id="btnAdminDatabaseUploadSubmit" class="line" value="Aceptar" />
				<input type="button" id="btnAdminDatabaseUploadCancel" class="line" value="Cancelar" />
			</td>				
		</tr>
		</table>
	</form>
</div>
