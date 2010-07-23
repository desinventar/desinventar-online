{-config_load file=`$lg`.conf section="di8_DBImport"-}
<div class="contentBlock" id="divDatabaseImport">
	<h2>{-#msgDBImportTitle#-}</h2>
	<div id="divDBImportControl">
		<table border="0">
		<tr>
			<td colspan="2">
				{-#msgDBImportMsg1#-}
			</td>
		</tr>
		<tr>
			<td align="right" valign="bottom">
				{-#msgDBImportFilename#-} :
			</td>
			<td colspan="2" valign="top">
				<input type="text"   id="txtDBImportFileName" value="" size="50"/>
				<input id="btnDBImportSelectFile" type="button" />
			</td>
		</tr>
		<tr>
			<td>
				<br />
			</td>
			<td>
				<div id="prgDBImportProgressBar" style="width:120px;height:6px;background-color:#dddddd">
					<div id="prgDBImportProgressMark" style="width:0px;height:6px;background-color:#0000ff">
					</div>
				</div>
			</td>
			<td>
				<input type="button" id="btnDBImportCancel" class="bb2" value="{-#msgDBImportCancel#-}" />
			</td>
		</tr>
		</table>
	</div>
	<div id="divDBImportStatusMsg">
		<span class="DBImportStatusMsg" id="msgDBImportUploadOk">{-#msgDBImportUploadOk#-}</span>
		<span class="DBImportStatusMsg" id="msgDBImportUploadError">{-#msgDBImportUploadError#-}</span>
		<span class="DBImportStatusMsg" id="msgDBImportFileError">{-#msgDBImportFileError#-}</span>
		<span class="DBImportStatusMsg" id="msgDBImportDBUpdated">{-#msgDBImportDBUpdated#-}</span>
	</div>
	<div id="divDBImportParameters" style="display:none;">
		<form id="frmDBImport">
			<table>
			<tr>
				<td>
					<br />
				</td>
				<td>
					<input type="radio" name="radbtnDBImportOption" value="NEW" checked />{-#msgDBImportDatabaseNew#-}<br />
					<input type="radio" name="radbtnDBImportOption" value="OVERWRITE" />{-#msgDBImportDatabaseReplace#-}<br />
				</td>
			</tr>
			<tr>
				<td>
					{-#msgDBImportRegionId#-} :
				</td>
				<td>
					<input type="text" id="RegionId"    class="line fixw" size="40" /><br />
				</td>
			</tr>
			<tr>
				<td>
					{-#msgDBImportRegionLabel#-} :
				</td>
				<td>
					<input type="text" id="RegionLabel" class="line fixw" size="80" /><br />
				</td>
			</tr>
			<tr>
				<td>
					{-#msgDBImportLangIsoCode#-} :
				</td>
				<td>
					<span id="spanLangIsoCode"></span>
				</td>
			</tr>
			<tr>
				<td>
					{-#msgDBImportCountryIso#-} :
				</td>
				<td>
					<span id="spanCountryIso"></span>
				</td>
			</tr>
			</table>
		</form>
	</div>
</div>
