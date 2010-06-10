{-*** LOGIN SUCESSFULL - USER MENU ***-}
<span class="txt"><b>{-#tuser#-}: {-$user-}</b></span>&nbsp;&nbsp;
<a href="javascript:void(null)" onclick="updateList('pagecontent', '{-$desinventarURL-}/user.php', 'cmd=welcome');">{-#tmyregions#-}</a> |
<a href="javascript:void(null)" onclick="mod='userpa'; updateList('pagecontent', '{-desinventarURL-}/user.php', 'cmd=viewpref');">{-#tconfigacc#-}</a> |
<a href="javascript:void(null)" onclick="updateUserBar('{-$desinventarURL-}/user.php', 'logout', '', '');">{-#tclosesess#-}</a>
