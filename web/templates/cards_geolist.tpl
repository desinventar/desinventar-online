{-if $lev <= $levmax-}
	{-$lev-}- {-$levname[0]-}:
	<select onChange="setgeo(this.options[this.selectedIndex].value, {-$lev-},'{-$levname[1]-}','{-$opc-}');" 
		style="width:180px; background-Color:#eee;" tabindex="7" id="geolev{-$lev-}"
		class="line" onFocus="showtip('{-$dis.GeographyId[2]-}', '#d4baf6')">
		<option value="" style="text-align:center;">--</option>
		{-foreach name=geol key=key item=item from=$geol-}
			{-if $item[2]-}
				<option value="{-$key-}">{-$item[1]-}</option>
			{-/if-}
		{-/foreach-}
	</select>
	<br />
	<span id="lev{-$lev-}">
	</span>
{-/if-}
