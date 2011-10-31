{-config_load file="$lg.conf" section="di8_region"-}
<form id="frmRegionEdit">
	<table class="grid">
		<tr>
			<td>
				{-#tregcntlist#-}<b style="color:darkred;">*</b>
			</td>
			<td>
				<select id="frmRegionEdit_CountryIso" name="Region[CountryIso]" class="fixw" tabindex="1">
					<option value=""></option>
				</select>
			</td>
		</tr>
		<tr>
			<td>
				<span id="lblRegionId">RegionId</span>
			</td>
			<td>
				<input id="frmRegionEdit_RegionId" name="Region[RegionId]" type="text" maxlength="50" class="line fixw" tabindex="2" />
			</td>
		</tr>
		<tr>
			<td>
				{-#tregnamlist#-}<b style="color:darkred;">*</b>
			</td>
			<td>
				<input id="frmRegionEdit_RegionLabel" name="Region[RegionLabel]" type="text" maxlength="200" class="line fixw" tabindex="3" />
			</td>
		</tr>
		<tr>
			<td>
				{-$dic.DBLangIsoCode[0]-}<b style="color:darkred;">*</b>
			</td>
			<td>
				<select id="frmRegionEdit_LangIsoCode" name="Region[LangIsoCode]" {-$ro-} class="line fixw" tabindex="4">
				</select>
			</td>
		</tr>
		<tr>
			<td>
				{-#tregactlist#-}<b>*</b>
			</td>
			<td>
				<input id="frmRegionEdit_RegionActive" name="" type="checkbox" checked tabindex="6" />
			</td>
		</tr>
		<tr>
			<td>
				{-#tregpublist#-}<b>*</b>
			</td>
			<td>
				<input id="frmRegionEdit_RegionPublic" name="" type="checkbox" tabindex="7" />
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
				<input id="frmRegionEdit_RegionStatus" type="hidden" name="Region[RegionStatus]" value="0" />
				<input id="frmRegionEdit_Cmd"          type="hidden" name="cmd" />
				<input id="frmRegionEdit_Submit"       type="submit" value="{-#bsave#-}"   class="line" tabindex="8" />
				<input id="frmRegionEdit_Cancel"       type="reset"  value="{-#bcancel#-}" class="line" tabindex="9" />
				
			</td>
		</tr>
	</table>
</form>
