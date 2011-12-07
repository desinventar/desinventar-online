<div id="desinventarInfo" style="display:none;">
	<input type="hidden" id="desinventarURL"           value="{-$desinventarURL-}" />
	<input type="hidden" id="desinventarURLPortal"     value="{-$desinventarURLPortal-}" />
	<input type="hidden" id="desinventarPortalType"    value="{-$desinventarPortalType-}" />
	<input type="hidden" id="desinventarLang"          value="{-$desinventarLang-}" />
	<input type="hidden" id="desinventarVersion"       value="{-$desinventarVersion-}" />
	<input type="hidden" id="desinventarUserId"        value="{-$desinventarUserId-}" /> 
	<input type="hidden" id="desinventarUserFullName"  value="{-$desinventarUserFullName-}" />
	<input type="hidden" id="desinventarUserRole"      value="{-$desinventarUserRole|default:NONE-}" />
	<input type="hidden" id="desinventarUserRoleValue" value="{-$desinventarUserRoleValue|default:0-}" />
	<input type="hidden" id="desinventarModule"        value="{-$desinventarModule-}" />
	<input type="hidden" id="desinventarRegionId"      value="{-$desinventarRegionId-}" />
	<input type="hidden" id="desinventarRegionLabel"   value="{-$desinventarRegionLabel-}" />
	<input type="hidden" id="desinventarHasInternet"   value="{-$desinventarHasInternet-}" />
	<input type="hidden" id="desinventarOpenLayersURL" value="{-$desinventarOpenLayersURL-}" />

	<input type="hidden" id="optionUseRemoteMaps"  value="{-$appOptions.UseRemoteMaps-}" />

	<select id="desinventarLanguageList" style="display:none;">
		{-if count($LanguageList) > 0-}
			{-foreach name=LanguageList key=key item=item from=$LanguageList-}
				<option value="{-$key-}">{-$item-}</option>
			{-/foreach-}
		{-else-}
			<option></option>
		{-/if-}
	</select>
	<select id="desinventarCountryList" style="display:none;">
		{-if count($CountryList) > 0-}
			{-foreach name=CountryList key=key item=item from=$CountryList-}
				<option value="{-$key-}">{-$item-}</option>
			{-/foreach-}
		{-else-}
			<option></option>
		{-/if-}		
	</select>
</div>
