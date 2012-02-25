{-config_load file="$lg.conf" section="grpAdminGeography"-}
<div class="Geography" style="width:100%;max-width:600px;">
	<h3>{-#msgGeography_Title#-}</h3>
	<div class="GeographyListHeader">
		<table>
			<tr>
				<td style="display:none;">
					<span class="title"></span><br />
					<select class="GeographyListHeader" style="width:120px;" data-GeoLevelId="">
						<option></option>
					</select>
				</td>
			</tr>
		</table>
	</div>
	<div class="GeographyList line" style="height:200px;">
		<table class="grid">
			<thead>
				<tr>
					<td class="Id">
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
	<div class="GeographyEdit" style="display:block; width:500px;">
		<h3>{-#msgGeography_Edit#-}</h3>
		<form class="GeographyEdit" method="post" action="">
			{-#msgGeography_Code#-}<b style="color:darkred;">*</b>
			<input class="GeographyCode" name="GeographyCode" type="text" 
				class="line" tabindex="1" style="width:400px;" />
			<br />
			<br />
			{-#msgGeography_Name#-}<b style="color:darkred;">*</b>
			<input class="GeographyName" name="GeographyName" type="text"
				class="line" tabindex="2" style="width:400px;" />
			<br />
			<br />
			
			{-#msgGeography_Active#-}
			<input class="GeographyActive" type="hidden" name="GeographyActive" value="1" />
			<input class="GeographyActiveCheckbox" type="checkbox" tabindex="3" />
			<br />
			<br />
			<input class="GeographyId" name="GeographyId" type="hidden" value="" />
			<input class="GeoParentId" name="GeoParentId" type="hidden" value="" />
			<div class="center">
				<a class="button"><span>{-#msgGeography_Save#-}</span></a>
				<a class="button"><span>{-#msgGeography_Cancel#-}</span></a>
			</p>
		</form>
	</div>
</div>