	{-foreach name=eve key=key item=item from=$evepredl-}
		<option value="{-$key-}" onMouseOver="showtip('{-$item[1]-}');" {-if $item[3]-}selected{-/if-}>{-$item[0]-}</option>
	{-/foreach-}
		<option disabled>----</option>
	{-foreach name=eve key=key item=item from=$eveuserl-}
		<option value="{-$key-}" onMouseOver="showtip('{-$item[1]-}');" {-if $item[3]-}selected{-/if-}>{-$item[0]-}</option>
	{-/foreach-}
