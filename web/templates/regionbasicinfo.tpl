{-config_load file=`$lg`.conf section="di8_index"-}
{-config_load file=`$lg`.conf section="di8_region" -}

<h2>{-$RegionInfo.RegionLabel-}</h2>
<table>
{-if $RegionInfo.PeriodBeginDate != "" && $RegionInfo.PeriodEndDate != ""-}
<tr>
<td>{-#tperiod#-}:</td><td> {-$RegionInfo.PeriodBeginDate-} - {-$RegionInfo.PeriodEndDate-}<br /> </td>
</tr>
{-/if-}
<tr>
<td>{-#trepnum# -}:</td><td>{-$RegionInfo.NumDatacards-}<br /></td>
</tr>
<tr>
<td>{-#tlastupd#-}:</td><td>{-$RegionInfo.RegionLastUpdate-}<br /></td>
</tr>
</table>

