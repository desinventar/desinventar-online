
{-foreach key=key item=item from=$regionlist-}
	<a href="{-$request_uri-}?r={-$key-}">{-$item-}</a><br />
{-/foreach-}


