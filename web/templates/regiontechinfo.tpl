{-config_load file=`$lg`.conf section="dc_querydesign"-}
{-config_load file=`$lg`.conf section="di8_region" -}
<!-- Show Region Info -->
<table border=0 style="width:100%; font-family:Lucida Grande, Verdana; font-size:10px;">
<tr>
	<td>
		<div id="info" style="width:500px" class="dwin" align="justify">
		{-foreach name=info key=k item=i from=$RegionInfo-}
			{-if substr($k,0,4) == "Info" -}
			{-if $i != ""-}
				<b>{-$Labels.$k[0]-}</b><br>{-$i-}<br>
			{-/if-}
			{-/if-}
		{-/foreach-}
		</div>
	</td>
</tr>
</table>
