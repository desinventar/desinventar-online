{-foreach name=upa key=key item=item from=$usrpa-}
	<tr>
		<td id="UserId">{-$key-}</td>
		<td>{-$item[2]-}</td>
		<td>{-$item[0]-}</td>
		<td><input type="checkbox" {-if ($item[8] == 1) -} checked{-/if-} disabled /></td>
		<td>
	</tr>
{-/foreach-}
