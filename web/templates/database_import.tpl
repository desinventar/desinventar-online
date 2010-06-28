{-config_load file=`$lg`.conf section="di8_DBImport"-}
<div class="contentBlock" id="divDatabaseImport">
	<h2>{-#msgDBImportTitle#-}</h2>
	<div id="divDBImportControl">
		<table border="0">
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
				<input type="button" id="btnDBImportCancel" class="bb2" value="{-#msgDBImportCancel#-}" />
			</td>
		</tr>
		<tr>
			<td colspan="3">
				<input type="radio" name="radbtnDBImportOption" value="NEW" checked />Crear nueva base de datos<br />
				<input type="radio" name="radbtnDBImportOption" value="OVERWRITE" />SobreEscribir base de datos existente<br />
			</td>
		</tr>
		</table>
	</div>
</div>
