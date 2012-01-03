{-config_load file="$lg.conf" section="grpMenuUser"-}
<h2>{-#tuserprefer#-}</h2>
<br />
<table id="tblUserList" style="width:550px;height:180px;" class="col dwin">
<thead>
	<tr>
		<th class="header"><b>{-#tuser#-}</b></th>
		<th class="header"><b>{-#tname#-}</b></th>
		<th class="header"><b>{-#temail#-}</b></th>
		<th class="header"><b>{-#tactive#-}</b></th>
	</tr>
	</thead>
	<tbody id="lst_userpa">
		{-foreach name=upa key=key item=item from=$usrpa-}
			<tr>
				<td id="UserId">{-$key-}</td>
				<td>{-$item[2]-}</td>
				<td>{-$item[0]-}</td>
				<td><input type="checkbox" {-if ($item[8] == 1) -} checked{-/if-} disabled /></td>
				<td>
			</tr>
		{-/foreach-}
	</tbody>
</table>
