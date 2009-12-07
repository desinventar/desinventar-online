{-config_load file=`$lg`.conf section="di8_listdb"-}
	<h4>{-#listdbTitle#-}:</h4><br />
	<table border="0" class="grid">
		<tr align="center">
			<td><b>{-#listdbCountry#-}</b></td>
			<td><b>{-#listdbRegion#-}</b></td>
			<td colspan="2"><b>{-#listdbStatus#-}</b></td>
		</tr>
		{-foreach name=rlist key=key item=item from=$regionlist-}
			<tr>
				<td>{-$item[1]-}</td>
				<td><a href="index.php?r={-$key-}">{-$item[0]-}</a></td>
				<td>{-if $item[2] == 3-}{-#listdbPublic#-}{-else-}{-#listdbPrivate#-}{-/if-}</td>
				<td>{-$item[3]-}</td>
			</tr>
		{-/foreach-}
	</table>
