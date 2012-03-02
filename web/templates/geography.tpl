{-config_load file="$lg.conf" section="grpAdminGeography"-}
{-if $ctl_admingeo-}
	<b onMouseOver="showtip('{-$dic.DBGeoEle[2]-}');">{-$dic.DBGeoEle[0]-}</b><br />
	<div id="lst_ageo" class="dwin" style="width:100%; height:180px;">
{-/if-}
{-if $ctl_geolist-}
 {-if $lev <= $levmax-}
    {-$lev-}:
		<select onChange="setadmingeo('{-$reg-}', this.options[this.selectedIndex].value, {-$lev-});" class="line" style="width:250px;">
			<option value="-2" style="color:blue; text-align:center;">-- {-$levname[0]-} --</option>
			<option value="-1" style="color:green; text-align:center;">-- {-#baddoption#-} --</option>
			{-foreach name=geol key=key item=item from=$geol-}
				<option {-if !$item[2]-} style="color:red;"{-/if-} value="{-$key-}|{-$item[0]-}|{-$item[1]-}|{-$item[2]-}">{-$item[1]-} ({-$item[0]-})</option>
			{-/foreach-}
		</select><br />
		<span id="alev{-$lev-}"></span>
 {-/if-}
{-/if-}
{-if $ctl_admingeo-}
	</div>
	<br />
	<span id="geostatusmsg" class="dlgmsg"></span><br />
	<div id="divDBConfigGeographyStatus">
		<span class="clsDBConfigGeographyStatus" id="msgDBConfigGeographyInsert">{-#msgDBConfigGeographyInsert#-}</span>
		<span class="clsDBConfigGeographyStatus" id="msgDBConfigGeographyUpdate">{-#msgDBConfigGeographyUpdate#-}</span>
		<span class="clsDBConfigGeographyStatus" id="msgDBConfigGeographyError" >{-#msgDBConfigGeographyError#-}</span>
		<br />
	</div>
	<div id="geoaddsect" style="display:none; width:100%;">
		<form id="frmDBConfigGeographyEdit" method="post" action="" >
			<a class="info" href="javascript:void(null)" onMouseOver="showtip('{-$dic.DBEleCode[2]-}');">
			{-$dic.DBEleCode[0]-}<b style="color:darkred;">*</b><span>{-$dic.DBEleCode[1]-}</span></a><br />
			<input id="aGeographyCode" name="data[GeographyCode]" type="text" class="line" tabindex="1" style="width:500px;"
				onBlur="updateList('geostatusmsg', jQuery('#desinventarURL').val() + '/geography.php', 
				'r={-$reg-}&cmd=chkcode&GeographyId='+ $('aGeographyId').value + '&GeographyCode='+ $('aGeographyCode').value);"
				onFocus="showtip('{-$dic.DBEleCode[2]-}');" />
			<br /><br />
			<a class="info" href="javascript:void(null)" onMouseOver="showtip('{-$dic.DBEleName[2]-}');">
			{-$dic.DBEleName[0]-}<b style="color:darkred;">*</b><span>{-$dic.DBEleName[1]-}</span></a><br />
			<input id="aGeographyName" name="data[GeographyName]" type="text" class="line" tabindex="2" style="width:500px;"
				onFocus="showtip('{-$dic.DBEleName[2]-}');" />
			<br /><br />
			<a class="info" href="javascript:void(null)" onMouseOver="showtip('{-$dic.DBEleActive[2]-}');">
			{-$dic.DBEleActive[0]-}<span>{-$dic.DBEleActive[1]-}</span></a>
			<input class="GeographyActive" type="hidden" name="data[GeographyActive]" value="0" />
			<input class="GeographyActiveCheckbox" type="checkbox" checked
				onFocus="showtip('{-$dic.DBEleActive[2]-}');" tabindex="3"
				onClick="if (!this.checked) updateList('geostatusmsg', jQuery('#desinventarURL').val() + '/geography.php', 
					'r={-$reg-}&cmd=chkstatus&GeographyId='+ $('aGeographyId').value);" />
			<br /><br />
			<p align="center" style="width:500px;">
				<input id="RegionId"     name="RegionId"          type="hidden" value="{-$reg-}" />
				<input id="aGeographyId" name="data[GeographyId]" type="hidden" value="" />
				<input id="aGeoParentId" name="data[GeoParentId]" type="hidden" value="" />
				<input id="Cmd"          name="cmd"               type="hidden" value="" />
				<input type="submit" value="{-#bsave#-}" tabindex="4" class="line" />
				<input type="reset" value="{-#bcancel#-}" class="line"
					onClick="$('geoaddsect').style.display='none'; uploadMsg('');" />
			</p>
		</form>
	</div>
	<!--
	<hr /><br />
	<form method="post" action="import.php" target="ifgeo" enctype="multipart/form-data">
		<input type="hidden" name="r" value="{-$reg-}" />
		<input type="hidden" name="cmd" value="upload" />
		<input type="hidden" name="diobj" value="4" />
		<input type="file" id="igeo" name="desinv" class="line" style="width:500px;" />
		<input type="submit" value="Importar" class="line" />
	</form>
	<iframe name="ifgeo" id="ifgeo" frameborder="1" style="height:150px; width:600px;"></iframe>
	-->
{-/if-}
{-** INFO AND ERRORS MESSAGES **-}
{-if $ctl_errinsgeo-}
 {-#terror#-}[{-$insstatgeo-}]: {-#errinsgeo#-}
{-elseif $ctl_errupdgeo-}
 {-#terror#-}[{-$updstatgeo-}]: {-#errupdgeo#-}
{-/if-}
{-*** CHECK Level/Geography-Availability MESSAGES - STATUS SPAN ***-}
{-if $ctl_chkname-}
 {-if !$chkname-}
 	 - {-#errchkname#-}
 {-/if-}
{-/if-}
{-if $ctl_chkcode-}
 {-if !$chkcode-}
 	 - {-#errchkcode#-}
 {-/if-}
{-/if-}
{-if $ctl_chkstatus-}
 {-if !$chkstatus-}
 	 - {-#errchkstatus#-}
 {-/if-}
{-/if-}
