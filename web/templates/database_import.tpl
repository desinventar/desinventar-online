{-config_load file="$lg.conf" section="grpAdminImport"-}
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
			<input type="button" id="btnDBImportSelectFile" />
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
			<a class="button" id="btnDBImportCancelUpload"><span>{-#msgDBImportCancel#-}</span></a>
		</td>
	</tr>
	</table>
</div>
<div id="divDBImportStatusMsg">
	<span class="DBImportStatusMsg" id="msgDBImportUploadOk">{-#msgDBImportUploadOk#-}</span>
	<span class="DBImportStatusMsg" id="msgDBImportUploadError">{-#msgDBImportUploadError#-}</span>
	<span class="DBImportStatusMsg" id="msgDBImportFileError">{-#msgDBImportFileError#-}</span>
	<span class="DBImportStatusMsg" id="msgDBImportDBUpdated">{-#msgDBImportDBUpdated#-}</span>
	<span class="DBImportStatusMsg" id="msgDBImportUpdateError">{-#msgDBImportUpdateError#-}</span>
</div>
<div id="divDBImportParameters" style="display:none;">
	<form id="frmDBImport">
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
				<span id="spanDBImportNew"   ><input type="radio" id="radioDBImportOptionNew"    class="radioDBImportOption" name="Mode" value="NEW" checked />{-#msgDBImportDatabaseNew#-}<br /></span>
				<span id="spanDBImportClone" ><input type="radio" id="radioDBImportOptionClone"  class="radioDBImportOption" name="Mode" value="CLONE"       />{-#msgDBImportDatabaseClone#-}<br /></span>
				<span id="spanDBImportUpdate"><input type="radio" id="radioDBImportOptionUpdate" class="radioDBImportOption" name="Mode" value="UPDATE"      />{-#msgDBImportDatabaseUpdate#-}<br /></span>
			</td>
		</tr>
		<tr>
			<td align="right">
				{-#msgDBImportRegionId#-} :
			</td>
			<td>
				<input type="text" id="RegionId" name="RegionId" class="line fixw" size="40" /><br />
			</td>
		</tr>
		<tr>
			<td align="right">
				{-#msgDBImportRegionLabel#-} :
			</td>
			<td>
				<input type="text" id="RegionLabel" name="RegionLabel" class="line fixw" size="80" /><br />
			</td>
		</tr>
		<tr>
			<td align="right">
				{-#msgDBImportLangIsoCode#-} :
			</td>
			<td>
				<span id="spanLangIsoCode"></span>
			</td>
		</tr>
		<tr>
			<td align="right">
				{-#msgDBImportCountryIso#-} :
			</td>
			<td>
				<span id="spanCountryIso"></span>
			</td>
		</tr>
		<tr>
			<td colspan="2" align="right">
				<a class="button" id="btnDBImportSubmit"><span>Aceptar</span></a>
				<a class="button" id="btnDBImportCancel"><span>Cancelar</span></a>
			</td>				
		</tr>
		</table>
	</form>
</div>
