<div id="qry-win" class="x-hidden">
	<div class="x-window-header">
		{-#mopenquery#-}
	</div>
	<div id="qry-cfg" class="center">
		<form id="openquery" enctype="multipart/form-data" action="{-$desinventarURL-}/?r={-$reg-}" method="post">
			<br /><br />
			<input type="hidden" name="cmd" value="openquery" />
			<input type="hidden" name="MAX_FILE_SIZE" value="100000" />
			<input type="file" id="ofile" name="qry" onChange="$('openquery').submit();"/>
		</form>
	</div>
	<span id="msgQueryOpenButtonClose" style="display:none;">{-#tclose#-}</span>
</div>
