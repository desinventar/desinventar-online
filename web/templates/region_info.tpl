{-config_load file="$lg.conf" section="grpMainStrings"-}
{-config_load file="$lg.conf" section="grpMenuRegion"-}
<div id="divRegionInfo" class="RegionInfo contentBlock" style="display:none;">
	<table border="0">
		<tr>
			<td>
				<div id="divRegionLogo">
					<img src="{-$desinventarURL-}/images/desinventar_logo.png" />
				</div>
			</td>
			<td>
				<h2><span id="txtRegionLabel"></span></h2>
				<table>
					<tr>
						<td>
							{-#RegionDatabasePeriod#-}:
						</td>
						<td>
							<span id="txtRegionPeriod"></span><br />
						</td>
					</tr>
					<tr>
						<td>
							{-#trepnum#-}:
						</td>
						<td>
							<span id="txtRegionNumberOfRecords"></span><br />
						</td>
					</tr>
					<tr>
						<td>
							{-#tlastupd#-}:
						</td>
						<td>
							<span id="txtRegionLastUpdate"></span><br />
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<hr />
				<table border=0 style="width:100%; font-family:Lucida Grande, Verdana; font-size:10px;">
					<tr>
						<td>
							<div class="InfoGeneral">
								<span class="title">{-#msgInfoGeneral#-}</span>
								<br />
								<span class="text"></span><br />
								<br />
							</div>
							<div class="InfoCredits">
								<span class="title">{-#msgInfoCredits#-}</span>
								<br />
								<span class="text"></span>
								<br />
								<br />
							</div>
							<div class="InfoSources">
								<span class="title">{-#msgInfoSources#-}</span>
								<br />
								<span class="text"></span><br />
								<br />
							</div>
							<div class="InfoSynopsis">
								<span class="title">{-#msgInfoSynopsis#-}</span>
								<br />
								<span class="text"></span><br />
							</div>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</div>
