{-config_load file="$lg.conf" section="grpDatabaseDelete"-}
<div id="divDatabaseDeleteWin" class="x-hidden">
	<div class="x-window-header">
		{-#msgDatabaseDelete_Title#-}
	</div>
	<div id="divDatabaseDeleteContent">
		<div class="DatabaseDelete">
			{-#msgDatabaseDelete_Subtitle#-}<br />
			<table class="center">
				<tr>
					<td>
						<span class="bold">{-#msgDatabaseDelete_RegionId#-} : </span>						
					</td>
					<td>
						<span class="RegionId"></span><br />
					</td>
				</tr>
				<tr>
					<td>
						<span class="bold">{-#msgDatabaseDelete_RegionLabel#-} : </span>
					</td>
					<td>
						<span class="RegionLabel"></span><br />
					</td>
				</tr>
			</table>
			<div class="center status">
				<span class="status StatusOk">{-#msgDatabaseDelete_StatusOk#-}</span>
				<span class="status StatusError">{-#msgDatabaseDelete_StatusError#-}</span>
				<br />
			</div>
			<div class="center buttons">
				<a class="button buttonOk"><span>{-#msgDatabaseDelete_Ok#-}</span></a>
				<a class="button buttonCancel"><span>{-#msgDatabaseDelete_Cancel#-}</span></a>
				<a class="button buttonClose"><span>{-#msgDatabaseDelete_Close#-}</span></a>
			</div>
			<div class="hidden">
				<input class="HasDeleted" type="hidden" value="" />
			</div>
		</div>
	</div>
</div>
