{-config_load file="$lg.conf" section="grpDatabaseDelete"-}
<div id="divDatabaseDeleteWin" class="x-hidden">
	<div class="x-window-header">
		{-#msgDatabaseDelete_Title#-}
	</div>
	<div id="divDatabaseDeleteContent">
		<div class="DatabaseDelete">
			<br />
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
			<br />
			<div class="center">
				<a class="button Ok"><span>{-#msgDatabaseDelete_Ok#-}</span></a>
				<a class="button Cancel"><span>{-#msgDatabaseDelete_Cancel#-}</span></a>
			</div>
		</div>
	</div>
</div>
