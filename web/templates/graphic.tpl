{-config_load file=`$lg`.conf section="dc_graphic"-}
{-config_load file=`$lg`.conf section="dc_qdetails"-}
{-if $ctl_showres-}
	<p align="right">{-#trepnum#-}: {-$NumRecords-}</p>
	<img src="{-$image-}" border="0">
{-else-}
{-#tnodata#-}
{-/if-}
