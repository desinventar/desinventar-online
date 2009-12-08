{-** REGIONINFO: Show Full Region Information **-}
{-if $ctl_showRegionInfo-}
	<table border="0">
	<tr>
		<td>
			<img src="index.php?cmd=getRegionLogo&RegionId={-$reg-}">
		</td>
		<td>
		{-include file="regionbasicinfo.tpl"-}
		</td>
	</tr>
	<tr>
		<td colspan="2"><hr>
		{-include file="regiontechinfo.tpl"-}
		</td>
	</tr>
	</table>
{-/if-}
