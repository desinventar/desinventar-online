{-config_load file=`$lg`.conf section="di8_listdb"-}
<table border="0" cellpadding="0" cellspacing="0" style="border: thin solid;" width="100%">
	<tr valign="top">
	<td>
		<h2><u>{-#listdbTitle#-}</u></h2>
		<table border="0" class="grid">
			<tr align="center">
				<td width="100px"><b>{-#listdbCountry#-}</b></td>
				<td><b>{-#listdbRegion#-}</b></td>
				{-if $desinventarUserId!=''-}
					<td><b>{-#listdbStatus#-}</b><br /></td>
				{-/if-}
			</tr>
			{-foreach name=rlist key=key item=item from=$regionlist-}
				<tr>
					<td align="center">{-$item[1]-}</td>
					<td>
						<a href="index.php?r={-$key-}" class="RegionListItem">{-$item[0]-}</a>
						<a href="javascript:void(null)" class="RegionListItem" onClick="javascript:window.open('?r={-$key-}','DI_{-$smarty.foreach.rlist.iteration-}', 
							'width=1020,height=700,left=0,top=0,screenX=0,screenY=0,resizable=no,status=yes,scrollbars=no,toolbar=no');">[-]</a>
					</td>
					{-if $desinventarUserId!=''-}
						<td>{-if $item[2] == 3-}{-#listdbPublic#-}{-else-}{-#listdbPrivate#-}{-/if-}</td>
					{-/if-}
				</tr>
			{-/foreach-}
		</table>
	</td>
	<td>
		<div id="info" width="500px" height="400px" border="1"></div>
	</td>
	</tr>
</table>
