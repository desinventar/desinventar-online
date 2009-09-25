{-foreach key=key item=item from=$regionlist-}
	<a href="javascript:void(null)" onClick="javascript:window.open('{-$request_uri-}?r={-$key-}','{-$key-}', winopt);">{-$item-}</a><br />
{-/foreach-}
