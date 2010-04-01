<?xml version="1.0" encoding="UTF-8" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<body>
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
	</body>
</html>
