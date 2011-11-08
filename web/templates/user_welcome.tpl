{-*** WELCOME PAGE TO LOGGED USER WITH ROLES AND OPTIONS - CONTENT ***-}
<h2>{-#thello#-} {-$fullname-},</h2>
{-** Show lists with roles and regions acccess **-}
{-if $ctl_portalperms-}
	<b>{-#tadminperms#-}: </b><br />
	<ul class="">
		<li><a href="javascript:void(null)" onclick="updateList('pagecontent', jQuery('#desinventarURL').val() + '/region.php', 'cmd=adminreg');">{-#mnuDatabaseAdmin#-}</a></li>
		<li><a href="javascript:void(null)" onclick="updateList('pagecontent', jQuery('#desinventarURL').val() + '/user.php', 'cmd=adminusr');onReadyUserAdmin();">{-#mnuUserAdmin#-}</a></li>
	</ul>
	<br /><hr /><br />
{-/if-}
{-#tyourrol#-}<br /><br />
<table border="0">
	{-if $radm-}
		{-foreach name=radm key=key item=item from=$radm-}
			<tr>
				<td><b>{-#tadminof#-}</b>
				</td>
				<td><a href="javascript:void(null)" onclick="parent.window.location = jQuery('#desinventarURL').val() + '/index.php?r={-$key-}'">{-$item-}</a>
				</td>
			</tr>
		{-/foreach-}
	{-/if-}
	{-if $robs-}
		{-foreach name=robs key=key item=item from=$robs-}
			<tr>
				<td><b>{-#tobservof#-}</b>
				</td>
				<td><a href="javascript:void(null)" onclick="parent.window.location = jQuery('#desinventarURL').val() + '/index.php?r={-$key-}'">{-$item-}</a>
				</td>
			</tr>
		{-/foreach-}
	{-/if-}
	{-if $rsup-}
		{-foreach name=rsup key=key item=item from=$rsup-}
			<tr>
				<td><b>{-#tsupervof#-}</b>
				</td>
				<td><a href="javascript:void(null)" onclick="parent.window.location = jQuery('#desinventarURL').val() + '/index.php?r={-$key-}'">{-$item-}</a>
				</td>
			</tr>
		{-/foreach-}
	{-/if-}
	{-if $rusr-}
		{-foreach name=rusr key=key item=item from=$rusr-}
			<tr>
				<td><b>{-#tuserof#-}</b>
				</td>
				<td><a href="javascript:void(null)" onclick="parent.window.location = jQuery('#desinventarURL').val() + '/index.php?r={-$key-}'">{-$item-}</a>
				</td>
			</tr>
		{-/foreach-}
	{-/if-}
</table><br />
