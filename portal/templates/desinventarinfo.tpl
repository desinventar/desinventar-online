<div id="desinventarInfo" style="display:none;">
	<input type="hidden" id="desinventarURL"           value="{-$desinventarURL-}" />
	<input type="hidden" id="desinventarURLPortal"     value="{-$desinventarURLPortal-}" />
	<input type="hidden" id="desinventarLang"          value="{-$lang-}" />
	<input type="hidden" id="desinventarUserId"        value="{-$desinventarUserId-}" />
	<input type="hidden" id="desinventarUserFullName"  value="{-$desinventarUserFullName-}" />

	<input type="hidden" id="desinventarRegionId"      value="" />
	<select id="desinventarLanguageList" style="display:none;">
		{-if count($LanguageList) > 0-}
			{-foreach name=LanguageList key=key item=item from=$LanguageList-}
				<option value="{-$key-}">{-$item-}</option>
			{-/foreach-}
		{-else-}
			<option>-</option>
		{-/if-}
	</select>
	<select id="desinventarCountryList" style="display:none;">
		{-if count($CountryList) > 0-}
			{-foreach name=CountryList key=key item=item from=$CountryList-}
				<option value="{-$key-}">{-$item-}</option>
			{-/foreach-}
		{-else-}
			<option>-</option>
		{-/if-}
	</select>
</div>
