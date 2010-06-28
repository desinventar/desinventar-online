{-config_load file=`$lg`.conf section="di8_DBEdit"-}
<div class="contentBlock" id="divDBEdit">
	<p id="txtDBEditInfo"></p>
	<form id="frmDBEdit">
		<table>
		<tr>
			<td>
				{-#msgDBEditRegionId#-}:
			</td>
			<td>
				<input type="text" id="RegionId"    class="line fixw" size="40" /><br />
			</td>
		</tr>
		<tr>
			<td>
				{-#msgDBEditRegionLabel#-}:
			</td>
			<td>
				<input type="text" id="RegionLabel" class="line fixw" size="80" /><br />
			</td>
		</tr>
		<tr>
			<td>
				{-#msgDBEditLangIsoCode#-}:
			</td>
			<td>
				<span id="spanLangIsoCode"></span>
			</td>
		</tr>
		<tr>
			<td>
				{-#msgDBEditCountryIso#-}:
			</td>
			<td>
				<span id="spanCountryIso"></span>
			</td>
		</tr>
		</table>
	</form>
</div>
