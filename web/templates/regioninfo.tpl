<!-- Show Region Info -->
<table border=0 style="width:100%; font-family:Lucida Grande, Verdana; font-size:10px;">
<tr>
	<td valign="center"><img src="region.php?r={-$RegionInfo.RegionId-}&view=logo"></td>
	<td valign="top">
		<h2>{-$RegionInfo.RegionId-}</h2>
		{-if $period[0] != "" && $period[1] != ""-}
			{-#tperiod#-}: {-$period[0]-} - {-$period[1]-}<br /> 
		{-/if-}
		{-#trepnum#-}: {-$dtotal-}<br />
		{-#tlastupd#-}: {-$lstupd-}<br />
	</td>
</tr>
<tr>
	<td colspan=2 align="center">
		<b>{-#tinactive#-}</b><br>
	</td>
</tr>
<tr>
	<td colspan="2">
		<div id="info" style="height:300px" class="dwin" align="justify">
		{-foreach name=info key=k item=i from=$RegionInfo-}
			{-if $i != ""-}
				<b>{-$k-}</b><br>{-$i-}<br>
			{-/if-}
		{-/foreach-}
		</div>
	</td>
</tr>
</table>
