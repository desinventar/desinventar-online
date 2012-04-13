{-config_load file="$lg.conf" section="grpDatabaseEdit"-}
<div class="DatabaseEdit">
	<form id="frmDatabaseEdit" class="DatabaseEdit" action="">
		<table class="grid">
			<tr>
				<td>
					{-#msgDatabaseEditCountryIso#-}
				</td>
				<td>
					<select id="fldDatabaseEdit_CountryIso" name="Database[CountryIso]" class="fixw" tabindex="2">
						<option value=""></option>
					</select>
				</td>
			</tr>
			<tr class="RegionId">
				<td>
				</td>
				<td>
					<input id="fldDatabaseEdit_RegionId" name="Database[RegionId]" type="text" maxlength="50" class="RegionId line fixw" tabindex="1" />
				</td>
			</tr>
			<tr>
				<td>
					{-#msgDatabaseEditRegionLabel#-}
				</td>
				<td>
					<input id="fldDatabaseEdit_RegionLabel" name="Database[RegionLabel]" type="text" maxlength="200" class="line fixw" tabindex="3" />
				</td>
			</tr>
			<tr>
				<td>
					{-#msgDatabaseEditLangIsoCode#-}
				</td>
				<td>
					<select id="fldDatabaseEdit_LangIsoCode" name="Database[LangIsoCode]" class="line fixw" tabindex="4">
					</select>
				</td>
			</tr>
			<tr id="trDatabaseEdit_RegionActive">
				<td>
					{-#msgDatabaseEditRegionActive#-}
				</td>
				<td>
					<input id="fldDatabaseEdit_RegionActive" type="checkbox" checked tabindex="5" title="{-#msgDatabaseEditRegionActiveTooltip#-}"/>
				</td>
			</tr>
			<tr>
				<td>
					{-#msgDatabaseEditRegionPublic#-}
				</td>
				<td>
					<input id="fldDatabaseEdit_RegionPublic" type="checkbox" tabindex="6" title="{-#msgDatabaseEditRegionPublicTooltip#-}" />
				</td>
			</tr>
			<tr>
				<td colspan="2" align="center">
					<input id="fldDatabaseEdit_RegionStatus" type="hidden" name="Database[RegionStatus]" value="0" />
				</td>
			</tr>
		</table>
	</form>
</div>
