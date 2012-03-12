<div id="desinventarInfo" class="desinventarInfo" style="display:none;">
	<input type="hidden" id="desinventarURL"             value="{-$desinventarURL-}" />
	<input type="hidden" id="desinventarURLPortal"       value="{-$desinventarURLPortal-}" />
	<input type="hidden" id="desinventarPortalType"      value="{-$desinventarPortalType-}" />
	<input type="hidden" id="desinventarLang"            value="{-$desinventarLang-}" />
	<input type="hidden" id="desinventarVersion"         value="{-$desinventarVersion-}" />

	<input type="hidden" id="desinventarUserId"          value="{-$desinventarUserId-}" /> 
	<input type="hidden" id="desinventarUserFullName"    value="{-$desinventarUserFullName-}" />
	<input type="hidden" id="desinventarUserRole"        value="{-$desinventarUserRole|default:NONE-}" />
	<input type="hidden" id="desinventarUserRoleValue"   value="{-$desinventarUserRoleValue|default:0-}" />

	<input type="hidden" id="desinventarModule"          value="{-$desinventarModule-}" />
	<input type="hidden" id="desinventarRegionId"        value="{-$desinventarRegionId-}" />
	<input type="hidden" id="desinventarRegionLabel"     value="{-$desinventarRegionLabel-}" />
	<input type="hidden" id="desinventarCountryIso"      value="{-$desinventarCountryIso-}" />
	<input type="hidden" id="desinventarCountryName"     value="{-$desinventarCountryName-}" />
	<input type="hidden" id="desinventarLastUpdate"      value="{-$desinventarLastUpdate-}" />
	<input type="hidden" id="desinventarNumberOfRecords" value="{-$desinventarNumberOfRecords-}" />
	
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
	<div class="EffectList hidden">
		{-foreach $ef1 as $key => $value-}
			<div class="EffectPeople Effect" data-field="{-$key-}">
				<span class="field">{-$key-}</span>
				<span class="label">{-$value[0]-}</span>
				<span class="tooltip">{-$value[1]-}</span>
				<span class="helptext">{-$value[2]-}</span>
			</div>
		{-/foreach-}
		{-foreach $sec as $key => $value-}
			<div class="EffectSector Effect" data-field="{-$key-}">
				<span class="field">{-$key-}</span>
				<span class="label">{-$value[0]-}</span>
				<span class="tooltip">{-$value[1]-}</span>
				<span class="helptext">{-$value[2]-}</span>
			</div>
		{-/foreach-}
	</div>
</div>
