{-config_load file="$lg.conf" section="grpEditDatacard"-}
<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td style="font-family:arial,tahoma,helvetica,cursive; font-size:11px; color:#dbab28;">
			{-if $statusmsg == 'duplicate'-}<b>{-#tdcerror#-}:</b> {-#tdisererr#-}
			{-elseif $statusmsg == 'insertok'-} {-#tdccreated#-} (Serial={-$diserial-})
			{-elseif $statusmsg == 'updateok'-} {-#tdcupdated#-} (Serial={-$diserial-})
			{-else-}{-$statusmsg-}{-/if-}
		</td>
		<td style="font-family:arial,tahoma,helvetica,cursive; font-size:10px; color:#000000;">
			{-#tstatpublished#-} {-$dipub-}, {-#tstatready#-} {-$direa-}
		</td>
	</tr>
</table>
