{-foreach name=rlist key=key item=item from=$regionlist-}
	<a href="javascript:void(null)" onClick="javascript:window.open('{-$request_uri-}?r={-$key-}','DI_{-$smarty.foreach.rlist.iteration-}', 
		'width=1020,height=700,left=0,top=0,screenX=0,screenY=0,resizable=no,scrollbars=no,status=no,toolbar=no');">{-$item-}</a><br />
{-/foreach-}
