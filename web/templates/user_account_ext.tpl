{-config_load file="$lg.conf" section="grpMenuUser"-}
<div id="divUserAccountWindow" class="x-hidden">
  <div class="x-window-header">
    {-$desinventarUserId-} - {-$role-}
  </div>
  <div id="divUserAccountContent">
    {-include file="user_account.tpl"-}
  </div>
</div>
