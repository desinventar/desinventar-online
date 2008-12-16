{-config_load file=`$lg`.conf section="di8_reginfo"-}
{-** REGIONINFO: Interface to Edit Info over Region.. **-}
{-if $ctl_adminreg-}
	<b onMouseOver="showtip('{-$dic.DBRegion[2]-}');">{-$dic.DBRegion[0]-}</b>
	<br><br>
	<!-- GENERAL REGION INFO SECTION -->
	<form name="infofrm" id="infofrm" method="GET"
		action="javascript: var s=$('infofrm').serialize(); mod='info'; sendData('{-$reg-}', 'regioninfo.php', s, '');"
	  onSubmit="javascript: var a=new Array('RegionDesc'); return(checkForm(a, '{-#errmsgfrm#-}'));">
		<input id="r" name="r" type="hidden" value="{-$reg-}">
		<input id="RegionLangCode" name="RegionLangCode" type="hidden" value="es">
<!--
	  <a class="info" href="javascript:void(null)" onMouseOver="showtip('{-$dic.DBPeriodBeginDate[2]-}')">
	  {-$dic.DBPeriodBeginDate[0]-}<span>{-$dic.DBPeriodBeginDate[1]-}</span></a><br>
	  <input type="text" id="PeriodBeginDate" name="PeriodBeginDate" value="{-$info[2]-}" class="line fixw"
	  		maxlength="10" {-$ro-} onFocus="showtip('{-$dic.DBPeriodBeginDate[2]-}')" tabindex="2">
	  <br><br>
	  <a class="info" href="javascript:void(null)" onMouseOver="showtip('{-$dic.DBPeriodEndDate[2]-}')">
	  {-$dic.DBPeriodEndDate[0]-}<span>{-$dic.DBPeriodEndDate[1]-}</span></a><br>
	  <input type="text" id="PeriodEndDate" name="PeriodEndDate" value="{-$info[3]-}" class="line fixw"
	  		maxlength="10" {-$ro-} onFocus="showtip('{-$dic.DBPeriodEndDate[2]-}')" tabindex="3">
	  <br><br>
	  <a class="info" href="javascript:void(null)" onMouseOver="showtip('{-$dic.DBOptionOutOfPeriod[2]-}')">
	  {-$dic.DBOptionOutOfPeriod[0]-}<span>{-$dic.DBOptionOutOfPeriod[1]-}</span></a>
	  <input type="checkbox" id="OptionOutOfPeriod" name="OptionOutOfPeriod" tabindex="4" 
	  		{-$ro-} {-if ($info[4] == 1) -} checked {-/if-} onFocus="showtip('{-$dic.DBOptionOutOfPeriod[2]-}')">
	  <br><br>
-->
		<input type="hidden" id="PeriodBeginDate" name="PeriodBeginDate">
		<input type="hidden" id="PeriodEndDate" name="PeriodEndDate">
		<input type="hidden" id="OptionOutOfPeriod" name="OptionOutOfPeriod">
	  <a class="info" href="javascript:void(null)" onMouseOver="showtip('{-$dic.DBRegionDesc[2]-}')">
	  <b style="color:darkred;">{-$dic.DBRegionDesc[0]-}</b><span>{-$dic.DBRegionDesc[1]-}</span></a><br>
	  <textarea id="RegionDesc" name="RegionDesc" rows="6" cols="30" {-$ro-} tabindex="5" 
	  		onFocus="showtip('{-$dic.DBRegionDesc[2]-}')">{-$info[0]-}</textarea>
	  <br><br>
	  <a class="info" href="javascript:void(null)" onMouseOver="showtip('{-$dic.DBRegionDescEN[2]-}')">
	  {-$dic.DBRegionDescEN[0]-}<span>{-$dic.DBRegionDescEN[1]-}</span></a><br>
	  <textarea id="RegionDescEN" name="RegionDescEN" rows="6" cols="30" {-$ro-} tabindex="6" 
	  		onFocus="showtip('{-$dic.DBRegionDescEN[2]-}')">{-$info[1]-}</textarea><br>
	  <a class="info" href="javascript:void(null)" onMouseOver="showtip('{-$dic.DBLimitMinX[2]-}')">
	  {-$dic.DBLimitMinX[0]-}<span>{-$dic.DBLimitMinX[1]-}</span></a>
	  <input id="GeoLimitMinX" name="GeoLimitMinX" type="text" size="5" value="{-$info[6]-}" tabindex="7" class="line"
	  		onFocus="showtip('{-$dic.DBLimitMinX[2]-}')">
	  &nbsp;&nbsp;&nbsp;&nbsp;
    <a class="info" href="javascript:void(null)" onMouseOver="showtip('{-$dic.DBLimitMinY[2]-}')">
    {-$dic.DBLimitMinY[0]-}<span>{-$dic.DBLimitMinY[1]-}</span></a>
    <input id="GeoLimitMinY" name="GeoLimitMinY" type="text" size="5" value="{-$info[7]-}" tabindex="8" class="line"
    		onFocus="showtip('{-$dic.DBLimitMinY[2]-}')"><br>
    <a class="info" href="javascript:void(null)" onMouseOver="showtip('{-$dic.DBLimitMaxX[2]-}')">
    {-$dic.DBLimitMaxX[0]-}<span>{-$dic.DBLimitMaxX[1]-}</span></a>
    <input id="GeoLimitMaxX" name="GeoLimitMaxX" type="text" size="5" value="{-$info[8]-}" tabindex="9" class="line"
    		onFocus="showtip('{-$dic.DBLimitMaxX[2]-}')">
    &nbsp;&nbsp;&nbsp;&nbsp;
    <a class="info" href="javascript:void(null)" onMouseOver="showtip('{-$dic.DBLimitMaxY[2]-}')">
    		{-$dic.DBLimitMaxY[0]-}<span>{-$dic.DBLimitMaxY[1]-}</span></a>
    <input id="GeoLimitMaxY" name="GeoLimitMaxY" type="text" size="5" value="{-$info[9]-}" tabindex="10" class="line"
    		onFocus="showtip('{-$dic.DBLimitMaxY[2]-}')">
    <br><br>
    <input id="RegionLabel" name="RegionLabel" value="{-$info[5]-}" type="hidden">
    <input id="infocmd" name="infocmd" value="update" type="hidden">
    <span id="infoaddsect"></span>
    <input type="submit" value="{-#bsave#-}" {-$ro-} class="line">
    <input type="reset" value="{-#bcancel#-}" {-$ro-} onclick="mod='info'; uploadMsg('');" class="line">
    <br>
    <span id="infostatusmsg" class="dlgmsg"></span>
	</form>
{-/if-}

{-** INFO AND ERRORS MESSAGES **-}
{-if $ctl_msgupdinfo-}
 {-#msgupdinfo#-}
{-elseif $ctl_errupdinfo-}
 {-#terror#-}[{-$updstatinfo-}]: {-#errupdinfo#-}
{-/if-}
