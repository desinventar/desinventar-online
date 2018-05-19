{-config_load file="$lg.conf" section="grpRegionInfo"-}
{-if $ctl_adminreg-}
<form id="frmDatabaseInfo" name="infofrm" method="post" action="{-$desinventarURL-}/info.php" target="ifinfo">
  <div class="region-info-edit">
    <div class="region-info-edit-top">
      {-foreach name=sett key=key item=item from=$sett-}
      {-assign var="inf" value="DB$key"-}
      {-assign var="tabind" value="`$tabind+1`"-}
        <div class="label region-info-edit-label" data-id="{-$key-}" title="{-$dic.$inf[2]-}">
          {-$dic.$inf[0]-}
        </div>
        <div class="field">
          {-if $item[1] == "DATE"-}
            <input id="{-$key-}" name="{-$key-}" type="text" class="line" style="width:120px;" value="{-$item[0]-}" tabindex="{-$tabind-}"/>
          {-elseif $item[1] == "NUMBER"-}
            <input id="{-$key-}" name="{-$key-}" type="text" class="line" style="width:40px;" value="{-$item[0]-}" tabindex="{-$tabind-}"/>
          {-elseif $item[1] == "TEXT"-}
            <input id="{-$key-}" name="{-$key-}" type="text" class="line" style="width:300px;" value="{-$item[0]-}" tabindex="{-$tabind-}"/>
          {-/if-}
        </div>
      {-/foreach-}
      <div class="buttons">
        <input name="_REG" type="hidden" value="{-$reg-}" />
        <input id="_infocmd" name="cmd" value="cmdDBInfoUpdate" type="hidden" />
        <input type="submit" value="{-#bsave#-}"  class="save line"/>
        <input type="reset" value="{-#bcancel#-}"  onclick="mod='info'; uploadMsg('');" class="cancel line" />
        <br />
        <div id="ifinfo" style="height:30px; width:350px;"></div>
      </div>
    </div>
    <div class="region-info-edit-bottom">
      {-foreach name=info key=LangIsoCode item=RegionFields from=$info-}
        <div class="region-info-edit-info">
          <h3>{-$languageLabels.$LangIsoCode-}</h3>
          {-foreach name=iitt key=key item=item from=$RegionFields-}
            {-assign var="inf" value="DB$key"-}
            {-assign var="tabind" value="`$tabind+1`"-}
            <div class="region-info-edit-row">
              <div>
                <div class="info region-info-edit-label">
                  {-$dic.$inf[0]-}
                  {-if $item[1] == "TEXT"-}
                    <span class="region-info-edit-label-expand">&#x2195;</span>
                  {-/if-}
                </div>
              </div>
              <div class="region-info-edit-field">
                {-if $item[1] == "TEXT"-}
                  <textarea class="region-info-edit-text" id="RegionInfo[{-$LangIsoCode-}][{-$key-}]" name="RegionInfo[{-$LangIsoCode-}][{-$key-}]" tabindex="{-$tabind-}"
                  >{-$item[0]-}</textarea>
                {-elseif $item[1] == "VARCHAR"-}
                  <input class="region-info-edit-input" id="RegionInfo[{-$LangIsoCode-}][{-$key-}]" name="RegionInfo[{-$LangIsoCode-}][{-$key-}]" type="text" class="line"
                  value="{-$item[0]-}" tabindex="{-$tabind-}"/>
                {-/if-}
              </div>
            </div>
          {-/foreach-}
        </div>
      {-/foreach-}
    </div>
  </div>
</form>
{-/if-}
{-** REGION INFO AND ERRORS MESSAGES **-}
{-if $ctl_msgupdinfo-}
 {-#msgupdinfo#-}
{-elseif $ctl_errupdinfo-}
 {-#terror#-}[{-$updstatinfo-}]: {-#errupdinfo#-}
{-/if-}
{-** ROLE INFO AND ERRORS MESSAGES **-}
{-if $ctl_msgupdrole-}
 {-#msgupdrole#-}
{-elseif $ctl_errupdrole-}
 {-#terror#-}[{-$updstatrole-}]: {-#errupdrole#-}
{-/if-}
{-** LOG INFO AND ERRORS MESSAGES **-}
{-if $ctl_msginslog-}
 {-#msginslog#-}
{-elseif $ctl_errinslog-}
 {-#terror#-}[{-$insstatlog-}]: {-#errinslog#-}
{-elseif $ctl_msgupdlog-}
 {-#msgupdlog#-}
{-elseif $ctl_errupdlog-}
 {-#terror#-}[{-$updstatlog-}]: {-#errupdlog#-}
{-/if-}
