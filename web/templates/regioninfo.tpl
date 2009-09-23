{-config_load file=`$lg`.conf section="dc_querydesign"-}
{-config_load file=`$lg`.conf section="di8_region" -}
<!-- Show Region Info -->
<table border=0 style="width:100%; font-family:Lucida Grande, Verdana; font-size:10px;">
<tr>
	<td valign="top">
		<h2>{-$RegionInfo.RegionLabel-}</h2>
		{-if $RegionInfo.PeriodBeginDate != "" && $RegionInfo.PeriodEndDate != ""-}
			{-#tperiod#-}: {-$RegionInfo.PeriodBeginDate-} - {-$RegionInfo.PeriodEndDate-}<br /> 
		{-/if-}
		{-#trepnum#-}: {-$dtotal-}<br />
		{-#tlastupd#-}: {-$RegionInfo.RegionLastUpdate-}<br />
	</td>
</tr>
<tr>
	<td colspan=2 align="center">
		<b>{-#tinactive#-}</b><br>
	</td>
</tr>
<tr>
	<td colspan="2">
		<div id="info" style="height:250px" class="dwin" align="justify">
		{-foreach name=info key=k item=i from=$RegionInfo-}
			{-if substr($k,0,4) == "Info" -}
			{-if $i != ""-}
				<b>{-$k-}</b><br>{-$i-}<br>
			{-/if-}
			{-/if-}
		{-/foreach-}
		</div>
	</td>
</tr>
</table>
