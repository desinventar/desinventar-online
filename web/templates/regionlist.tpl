{-foreach key=key item=item from=$regionlist-}
	<a href="javascript:void(null)" onClick="window.open('{-$request_uri-}?r={-$key-}','{-$key-}',
		'width=1020,height=700,left=0,top=0,screenX=0,screenY=0,toolbar=no,status=no,scrollbars=no,resizable=no');">{-$item-}</a><br />
{-/foreach-}
