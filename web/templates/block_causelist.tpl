{-foreach name=cau key=key item=item from=$caupredl-}
	<option value="{-$key-}" onMouseOver="showtip('{-$item[1]-}');" {-if $item[3]-}selected{-/if-}>{-$item[0]-}</option>
{-/foreach-}
	<option disabled>----</option>
{-foreach name=mycau key=key item=item from=$cauuserl-}
	<option value="{-$key-}" onMouseOver="showtip('{-$item[1]-}');" {-if $item[3]-}selected{-/if-}>{-$item[0]-}</option>
{-/foreach-}

