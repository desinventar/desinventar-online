{-config_load file="$lg.conf" section="di8_index"-}
{-config_load file="$lg.conf" section="di8_region"-}
{-** REGIONINFO: Show Full Region Information **-}
<div id="divRegionInfo">
	<table border="0">
		<tr>
			<td>
				<div id="divRegionLogo">
					<img src="{-$desinventarURL-}/images/di_logo2.png" />
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
			<td colspan="2"><hr>
				<table border=0 style="width:100%; font-family:Lucida Grande, Verdana; font-size:10px;">
					<tr>
						<td>
							<div id="info" style="width:500px" class="dwin" align="justify">
								<span class="RegionInfoTitle">{-#msgInfoGeneral#-}</span><br />
								<span class="RegionInfoText" id="txtInfoGeneral"></span><br />
								<br />
								<span class="RegionInfoTitle">{-#msgInfoCredits#-}</span><br />
								<span class="RegionInfoText" id="txtInfoCredits"></span><br />
								<br />
								<span class="RegionInfoTitle">{-#msgInfoSources#-}</span><br />
								<span class="RegionInfoText" id="txtInfoSources"></span><br />
								<br />
								<span class="RegionInfoTitle">{-#msgInfoSynopsis#-}</span><br />
								<span class="RegionInfoText" id="txtInfoSynopsis"></span><br />
							</div>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</div>
