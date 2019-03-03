{-config_load file="$lg.conf" section="dc_graphic"-}
{-config_load file="$lg.conf" section="dc_qdetails"-}
{-if $ctl_showres-}
  <div class="dwin" style="height:40px;">
    {-foreach key=k item=i from=$qdet-}
      {-if $k == "GEO"-}<b>{-#geo#-}:</b> {-$i-}; {-/if-}
      {-if $k == "EVE"-}<b>{-#eve#-}:</b> {-$i-}; {-/if-}
      {-if $k == "CAU"-}<b>{-#cau#-}:</b> {-$i-}; {-/if-}
      {-if $k == "EFF"-}<b>{-#eff#-}:</b> {-$i-}; {-/if-}
      {-if $k == "BEG"-}<b>{-#beg#-}:</b> {-$i-}; {-/if-}
      {-if $k == "END"-}<b>{-#end#-}:</b> {-$i-}; {-/if-}
      {-if $k == "SOU"-}<b>{-#sou#-}:</b> {-$i-}; {-/if-}
      {-if $k == "SER"-}<b>{-#ser#-}:</b> {-$i-}; {-/if-}
    {-/foreach-}
  </div>
  <p class="right">{-#trepnum#-}: <span id="viewGraphRecordCount">{-$NumRecords-}</span></p>
  <img id="viewGraphImg" src="{-$image-}">
{-else-}
  {-#tnodata#-}
{-/if-}
