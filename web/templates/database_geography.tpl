{-config_load file="$lg.conf" section="grpAdminGeography"-}
<div class="Geography" style="width:100%;max-width:600px;">
	<table style="width:100%;">
		<tr>
			<td style="width:100%;">
				<h3>{-#msgGeography_Title#-}</h3>
			</td>
			<td class="right">
				<a class="button Export" title="{-#msgGeography_ExportTooltip#-}"><span>{-#msgGeography_Export#-}</span></a>
				<div class="hidden">
					<form class="Export" action="{-$desinventarURL-}/" method="post" target="iframeDownload">
						<input class="cmd"      name="cmd"      type="hidden" value="cmdGeographyExport" />
						<input class="RegionId" name="RegionId" type="hidden" value="{-$desinventarRegionId-}" />
						<input class="Labels"   name="Labels"   type="hidden"  value="Id" />
					</form>
				</div>					
			</td>			
		</tr>
	</table>
	<div class="ListHeader">
		<span class="helptext">{-#msgGeography_Header1#-}</span>
		<table class="ListHeader">
			<tr>
				<td class="ListHeader" style="display:none;">
					<span class="title"></span><br />
					<select class="ListHeader" style="width:120px;">
						<option></option>
					</select>
				</td>
			</tr>
		</table>
	</div>
	<div>
		<span class="helptext">{-#msgGeography_Header2#-}</span>
		<div class="List line" style="height:200px;overflow:auto;">
			<table class="List grid">
				<thead>
					<tr>
						<td class="GeographyLevel">
							GeographyLevel
						</td>
						<td class="GeographyId">
							Id
						</td>
						<td class="GeographyCode">
							{-#msgGeography_Code#-}
						</td>
						<td class="GeographyName">
							{-#msgGeography_Name#-}
						</td>
						<td class="GeographyActive">
							{-#msgGeography_Active#-}
						</td>
						<td class="GeographyStatus">
							{-#msgGeography_Status#-}
						</td>
					</tr>
				</thead>
				<tbody>
					<tr class="hidden">
						<td class="GeographyLevel">
						</td>
						<td class="Id">
						</td>					
						<td class="GeographyCode">
						</td>
						<td class="GeographyName">
						</td>
						<td class="GeographyActive">
						</td>
						<td class="GeographyStatus">
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<div class="Add">
		<br />
		<a class="button Add"><span>{-#msgGeography_Add#-}</span></a>
		<br />
	</div>
	<div class="Edit" style="display:block; width:500px;">
		<br />
		<h3>{-#msgGeography_Edit#-}</h3>
		<form class="Edit" method="post" action="#">
			{-#msgGeography_Code#-}<b style="color:darkred;">*</b>
			<input class="GeographyCode line" name="GeographyCode" type="text" tabindex="1" style="width:400px;" />
			<br />
			<br />
			{-#msgGeography_Name#-}<b style="color:darkred;">*</b>
			<input class="GeographyName line" name="GeographyName" type="text" tabindex="2" style="width:400px;" />
			<br />
			<br />
			
			{-#msgGeography_Active#-}
			<input class="GeographyActive" type="hidden" name="GeographyActive" value="1" />
			<input class="GeographyActiveCheckbox" type="checkbox" tabindex="3" checked="checked" />
			<br />
			<br />
			<input class="GeographyId" name="GeographyId" type="hidden" value="" />
			<div class="center">
				<a class="button Save"><span>{-#msgGeography_Save#-}</span></a>
				<a class="button Cancel"><span>{-#msgGeography_Cancel#-}</span></a>
			</div>
		</form>
		<div class="Status center" style="width:100%;">
			<br />
			<br />
			<span class="Ok">{-#msgGeography_StatusOk#-}</span>
			<span class="DuplicatedCode">{-#msgGeography_StatusDuplicatedCode#-}</span>
			<span class="WithDatacards">{-#msgGeography_StatusWithDatacards#-}</span>
			<span class="Error">{-#msgGeography_StatusError#-}</span>
		</div>
	</div>
	<div class="hidden">
		<input class="ParentId" type="hidden" value="" />
		<input class="GeoLevelId" type="hidden" value="0" />
		<input class="GeoLevelCount" type="hidden" value="0" />
		<span class="All">{-#msgGeography_All#-}</span>
		<select class="GeographyStatusText">
			<option value="0">{-#msgGeography_Inactive#-}</option>
			<option value="1">{-#msgGeography_Active#-}</option>
			<option value="2">{-#msgGeography_New#-}</option>
			<option value="3">{-#msgGeography_Check#-}</option>
		</select>
	</div>
</div>