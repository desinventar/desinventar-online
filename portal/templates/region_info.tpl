{-config_load file="$lg.conf" section="region_info"-}
{-** REGIONINFO: Show Full Region Information **-}
<div id="divRegionInfo">
	<table border="0">
		<tr>
			<td>
				<div id="divRegionLogo">
					<img src="{-$desinventarURLPortal-}/images/di_logo2.png" />
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
							<span id="txtRegionNumDatacards"></span><br />
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
			<td colspan="2"><hr />
				<table border=0 style="width:100%; font-family:Lucida Grande, Verdana; font-size:10px;">
					<tr>
						<td>
							<div id="divInfoGeneral" class="RegionInfo">
								<div class="RegionInfoTitle" id="Title">{-#msgInfoGeneral#-}</div>
								<div class="RegionInfoText"  id="Text"></div>
							</div>
							<div id="divInfoCredits" class="RegionInfo">
								<div class="RegionInfoTitle" id="Title">{-#msgInfoCredits#-}</div>
								<div class="RegionInfoText"  id="Text"></div>
								<br />
							</div>
							<div id="divInfoSources" class="RegionInfo">
								<div class="RegionInfoTitle" id="Title">{-#msgInfoSources#-}</div>
								<div class="RegionInfoText"  id="Text"></div>
								<br />
							</div>
							<div id="divInfoSynopsis" class="RegionInfo">
								<div class="RegionInfoTitle" id="Title">{-#msgInfoSynopsis#-}</div>
								<div class="RegionInfoText"  id="Text"></div>
							</div>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</div>
