{-config_load file="$lg.conf" section="grpDatabaseEdit"-}
<form id="frmDatabaseEdit">
	<input id="fldDatabaseEdit_RegionId" name="Database[RegionId]" type="hidden" maxlength="50" class="line fixw" tabindex="1" />
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
				<select id="fldDatabaseEdit_LangIsoCode" name="Database[LangIsoCode]" {-$ro-} class="line fixw" tabindex="4">
				</select>
			</td>
		</tr>
		<tr>
			<td>
				{-#msgDatabaseEditRegionActive#-}
			</td>
			<td id="trDatabaseEdit_RegionActive">
				<input id="fldDatabaseEdit_RegionActive" name="" type="checkbox" checked tabindex="5" title="{-#msgDatabaseEditRegionActiveTooltip#-}"/>
			</td>
		</tr>
		<tr>
			<td>
				{-#msgDatabaseEditRegionPublic#-}
			</td>
			<td>
				<input id="fldDatabaseEdit_RegionPublic" name="" type="checkbox" tabindex="6" title="{-#msgDatabaseEditRegionPublicTooltip#-}" />
			</td>
		</tr>
		<tr>
			<td colspan="2" align="center">
				<input id="fldDatabaseEdit_RegionStatus" type="hidden" name="Database[RegionStatus]" value="0" />
			</td>
		</tr>
	</table>
</form>
