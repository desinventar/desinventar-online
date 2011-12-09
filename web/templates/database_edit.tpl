{-config_load file="$lg.conf" section="grpDatabaseEdit"-}
<form id="frmDatabaseEdit">
	<table class="grid">
		<tr>
			<td>
				<span id="lblDatabaseEdit_RegionId">{-#msgDatabaseEditRegionId#-}</span>
			</td>
			<td>
				<span  id="txtDatabaseEdit_RegionId"></span>
				<input id="fldDatabaseEdit_RegionId" name="Database[RegionId]" type="hidden" maxlength="50" class="line fixw" tabindex="1" />
			</td>
		</tr>
		<tr>
			<td>
				{-#msgDatabaseEditCountryIso#-}<b style="color:darkred;">*</b>
			</td>
			<td>
				<select id="fldDatabaseEdit_CountryIso" name="Database[CountryIso]" class="fixw" tabindex="2">
					<option value=""></option>
				</select>
			</td>
		</tr>
		<tr>
			<td>
				{-#msgDatabaseEditRegionLabel#-}<b style="color:darkred;">*</b>
			</td>
			<td>
				<input id="fldDatabaseEdit_RegionLabel" name="Database[RegionLabel]" type="text" maxlength="200" class="line fixw" tabindex="2" />
				<input id="fldDatabaseEdit_RegionLabelPrev" type="hidden" value="" />
			</td>
		</tr>
		<tr>
			<td>
				{-#msgDatabaseEditLangIsoCode#-}<b style="color:darkred;">*</b>
			</td>
			<td>
				<select id="fldDatabaseEdit_LangIsoCode" name="Database[LangIsoCode]" {-$ro-} class="line fixw" tabindex="4">
				</select>
			</td>
		</tr>
		<tr>
			<td>
				{-#msgDatabaseEditRegionActive#-}<b>*</b>
			</td>
			<td>
				<input id="fldDatabaseEdit_RegionActive" name="" type="checkbox" checked tabindex="6" />
			</td>
		</tr>
		<tr>
			<td>
				{-#msgDatabaseEditRegionPublic#-}<b>*</b>
			</td>
			<td>
				<input id="fldDatabaseEdit_RegionPublic" name="" type="checkbox" tabindex="7" />
			</td>
		</tr>
		<!--
		<tr>
			<td>
				{-#tregadmlist#-}<b style="color:darkred;">*</b>
			</td>
			<td>
				<select id="RegionUserAdmin" name="RegionUserAdmin" class="fixw" tabindex="5">
					<option value=""></option>
					{-foreach name=usr key=key item=item from=$usr-}
						<option value="{-$key-}">{-$item-}</option>
					{-/foreach-}
				</select>
			</td>
		</tr>
		-->
		<tr>
			<td colspan="2" align="center">
				<input id="fldDatabaseEdit_RegionStatus" type="hidden" name="Database[RegionStatus]" value="0" />
			</td>
		</tr>
	</table>
</form>
