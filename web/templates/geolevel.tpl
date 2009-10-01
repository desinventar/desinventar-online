{-config_load file=`$lg`.conf section="di8_geography"-}
{-if $ctl_admingeo-}
	<b onMouseOver="showtip('{-$dic.DBGeoLev[2]-}');">{-$dic.DBGeoLev[0]-}</b><br />
	<div class="dwin" style="width:600px; height:80px;">
		<table width="100%" class="grid">
			<thead>
				<tr>
					<td class="header" onMouseOver="showtip('{-$dic.DBLevName[2]-}');">
						<b>{-$dic.DBLevName[0]-}</b></td>
					<td class="header" onMouseOver="showtip('{-$dic.DBLevDesc[2]-}');">
						<b>{-$dic.DBLevDesc[0]-}</b></td>
				</tr>
			</thead>
			<tbody id="lst_lev">
{-/if-}
{-if $ctl_levlist-}
{-foreach name=levl key=key item=item from=$levl-}
				<tr class="{-if ($smarty.foreach.levl.iteration - 1) % 2 == 0-}normal{-else-}under{-/if-}"
						onMouseOver="Element.addClassName(this, 'highlight');" onMouseOut="Element.removeClassName(this, 'highlight');"
						onClick="setLevGeo('{-$key-}','{-$item[0]-}','{-$item[1]-}','','{-$item[2]-}','{-$item[3]-}','{-$item[4]-}','lev'); 
											$('levcmd').value='update';">
					<td>{-$key-}:{-$item[0]-}</td>
					<td>{-$item[1]|truncate:150-}</td>
				</tr>
{-/foreach-}
{-/if-}
{-if $ctl_admingeo-}
			</tbody>
		</table>
	</div>
	<br /><br />
	<input id="add" type="button" value="{-#baddoption#-}" class="line"
		onclick="setLevGeo('','','','','','','','lev'); $('levcmd').value='insert';" />
	<span id="levstatusmsg" class="dlgmsg"></span>
	<br /><br />
	<div id="levaddsect" style="display:none; width:600px;">
		<form name="levfrm" id="levfrm" method="GET" 
			action="javascript: var s=$('levfrm').serialize(); sendData('{-$reg-}','geolevel.php', s, '');"
			onSubmit="javascript: var a=new Array('GeoLevelName','GeoLevelDesc'); return(checkForm(a, '{-#errmsgfrmlev#-}'));">
			<a class="info" href="javascript:void(null)" onMouseOver="showtip('{-$dic.DBLevName[2]-}')">
			{-$dic.DBLevName[0]-}<b style="color:darkred;">*</b><span>{-$dic.DBLevName[1]-}</span></a><br />
			<input id="GeoLevelName" name="GeoLevelName" type="text" {-$ro-} tabindex="1" class="line" style="width:400px;"
					onBlur="updateList('levstatusmsg', 'geolevel.php', 'r={-$reg-}&levcmd=chkname&GeoLevelId='+ $('GeoLevelId').value +'&GeoLevelName='+ $('GeoLevelName').value);"
					onFocus="showtip('{-$dic.DBLevName[2]-}')" />
			<br /><br />
			<a class="info" href="javascript:void(null)" onMouseOver="showtip('{-$dic.DBLevDesc[2]-}')">
			{-$dic.DBLevDesc[0]-}<b style="color:darkred;">*</b><span>{-$dic.DBLevDesc[1]-}</span></a><br />
			<textarea id="GeoLevelDesc" name="GeoLevelDesc" {-$ro-} tabindex="2" style="width:500px;"
					onFocus="showtip('{-$dic.DBLevDesc[2]-}')"></textarea>
			<br /><br />
			<a class="info" href="javascript:void(null)" onMouseOver="showtip('{-$dic.DBLevHaveMap[2]-}')">
			{-$dic.DBLevHaveMap[0]-}<b>*</b><span>{-$dic.DBLevHaveMap[1]-}</span></a>
			<input type="checkbox" id="chkmap" name="chkmap" tabindex="3" 
					onClick="$('shwmap').style.display = this.checked ? 'block' : 'none';" />
			<div id="shwmap" style="display:none;">
				<a class="info" href="javascript:void(null)" onMouseOver="showtip('{-$dic.DBLevLayerFile[2]-}')">
				{-$dic.DBLevLayerFile[0]-}<span>{-$dic.DBLevLayerFile[1]-}</span></a><br />
				<input id="GeoLevelLayerFile" name="GeoLevelLayerFile" type="text" {-$ro-} class="line" style="width:500px;"
						tabindex="4" onFocus="showtip('{-$dic.DBLevLayerFile[2]-}')" />
				<br /><br />
				<a class="info" href="javascript:void(null)" onMouseOver="showtip('{-$dic.DBLevLayerCode[2]-}')">
				{-$dic.DBLevLayerCode[0]-}<span>{-$dic.DBLevLayerCode[1]-}</span></a><br />
				<input id="GeoLevelLayerCode" name="GeoLevelLayerCode" type="text" {-$ro-} class="line" style="width:500px;"
   					tabindex="5" onFocus="showtip('{-$dic.DBLevColCode[2]-}')" />
				<br /><br />
				<a class="info" href="javascript:void(null)" onMouseOver="showtip('{-$dic.DBLevLayerName[2]-}')">
				{-$dic.DBLevLayerName[0]-}<span>{-$dic.DBLevLayerName[1]-}</span></a><br />
				<input id="GeoLevelLayerName" name="GeoLevelLayerName" type="text" {-$ro-} class="line" style="width:500px;"
					tabindex="6" onFocus="showtip('{-$dic.DBLevLayerName[2]-}')" />
				<br /><br />
			</div>
			<p align="center">
				<input id="r" name="r" type="hidden" value="{-$reg-}" />
				<input id="GeoLevelId" name="GeoLevelId" type="hidden" />
				<input id="levcmd" name="levcmd" type="hidden" />
				<input type="submit" value="{-#bsave#-}" {-$ro-} class="line" />
				<input type="reset" value="{-#bcancel#-}" class="line" onClick="$('levaddsect').style.display='none'; mod='lev'; uploadMsg('');" {-$ro-} />
			</p>
		</form>
	</div>
{-/if-}

{-** INFO AND ERRORS MESSAGES **-}
{-if $ctl_msginslev-}
 {-#msginslev#-}
{-elseif $ctl_errinslev-}
 {-#terror#-}[{-$insstatlev-}]: {-#errinslev#-}
{-elseif $ctl_msgupdlev-}
 {-#msgupdlev#-}
{-elseif $ctl_errupdlev-}
 {-#terror#-}[{-$updstatlev-}]: {-#errupdlev#-}
{-/if-}
{-*** CHECK Level/Geography-Availability MESSAGES - STATUS SPAN ***-}
{-if $ctl_chkname-}
 {-if !$chkname-}
 	 - {-#errchkname#-}
 {-/if-}
{-/if-}
{-if $ctl_chkstatus-}
 {-if !$chkstatus-}
 	 - {-#errchkstatus#-}
 {-/if-}
{-/if-}
