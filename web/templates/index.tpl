{-config_load file=`$lg`.conf section="di8_index"-}
{-config_load file=`$lg`.conf section="di8_user"-}
<?xml version="1.0" encoding="UTF-8" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8; no-cache" />
	<title>{-#ttitle#-} | {-$regname-}</title>
	<link rel="stylesheet" href="css/desinventar.css?version={-$jsversion-}" type="text/css">
	<script type="text/javascript" src="include/prototype.js"></script>
	<!-- jQuery -->
	{-include file="jquery.tpl" -}
	
	<script type="text/javascript" src="include/listMan.js"></script>
	<script type="text/javascript" src="include/md5.js"></script>
	<script type="text/javascript" src="include/checktree.js"></script>
	<script type="text/javascript" src="include/wd.js"></script>
	<script type="text/javascript" src="include/accordion.js"></script>
	<script type="text/javascript" src="include/palette.js"></script>

	<script type="text/javascript" src="js/diadmin.js?version={-$jsversion-}"></script>
	<script type="text/javascript" src="js/jquery.snippets.js?version={-$jsversion-}"></script>
	<script type="text/javascript" src="js/user.js?version={-$jsversion-}"></script>
	<script type="text/javascript" src="js/user_login.js?version={-$jsversion-}"></script>
	<script type="text/javascript" src="js/desconsultar.js?version={-$jsversion-}"></script>

	<!-- ExtJS -->
	<link rel="stylesheet" href="css/checktree.css" type="text/css"/>
	<link rel="stylesheet" href="css/accordion.css" type="text/css"/>
	<link rel="stylesheet" type="text/css" href="/extJS/resources/css/ext-all.css"/>
	<link rel="stylesheet" type="text/css" href="/extJS/resources/css/xtheme-gray.css"/>
	<script type="text/javascript" src="/extJS/adapter/ext/ext-base.js"></script>
	<script type="text/javascript" src="/extJS/ext-all.js"></script>
	{-include file="main_javascript.tpl" -}
	{-include file="main_css.tpl" -}
	<script type="text/javascript" src="include/tabber.js"></script>
	<script type="text/javascript">

		function doKeepSessionAlive() {
			var refreshTime = 60 * 1000; // in milliseconds, so 10 minutes
			window.setInterval( function() {
			var url = 'cards.php?u=1&t=' + new Date().getTime();
			jQuery.get(url);
			}, refreshTime);
		}
		
		jQuery(document).ready(function() {
			//Keep Session Open
			doKeepSessionAlive();
			
			jQuery('#divDatacardsShow').hide();
			jQuery('#divDatabaseBackup').hide();
			
			jQuery("#divDatabaseList").hide();
			{-if $reg==""-}
				/* Hide button toolbar if no region is selected */
				jQuery("#ResultHeaderText").hide();
				jQuery("#ResultHeaderButtonToolbar").hide();
			{-/if-}
			{-if $ctl_mainpage -}
				jQuery("#dcr").hide();
				jQuery("#divDatabaseList").show();
			{-/if-}
			// 2010-02-21 (jhcaiced) This jQuery calls ensures that the Period and Stat
			// parameters are not empty at the same time.
			jQuery('#graphParamPeriod').change(function() {
				var Value = jQuery(this).val();
				if (Value != '') {
					jQuery('#graphParamStat').val('');
				} else {
					jQuery('#graphParamStat').val('MONTH');
				}
			});
			jQuery('#graphParamStat').change(function() {
				var Value = jQuery(this).val();
				if (Value != '') {
					jQuery('#graphParamPeriod').val('');
				} else {
					jQuery('#graphParamPeriod').val('YEAR');
				}
			})
		});
	</script>
</head>
<body>
	<div id="loading-mask"></div>
	<div id="loading">
		<div class="loading-indicator">Loading...</div>
	</div>
	<div id="north">
		<div id="toolbar"></div>
	</div>
	<div id="container">
		<!-- -Configuration -->
		<div class="contentBlock" id="divDatabaseConfiguration">
			<div id="tabDatabaseConfiguration" style="display:none;" class="tabber">
				<div class="tabbertab"><h2>{-#mreginfo#-}</h2><p></p></div>
				<div class="tabbertab"><h2>{-#mgeolevel#-}</h2><p></p></div>
				<div class="tabbertab"><h2>{-#mgeography#-}</h2><p></p></div>
				<div class="tabbertab"><h2>{-#mevents#-}</h2><p></p></div>
				<div class="tabbertab"><h2>{-#mcauses#-}</h2><p></p></div>
				<div class="tabbertab"><h2>{-#meeffects#-}</h2><p></p></div>
			</div>
		</div>
		
		<!-- Import datacards-->
		<div class="contentBlock" id="divDatacardsImport" style="display:none;"></div>

		<!-- Datacard Edit Window-->
		<div class="contentBlock" id="divDatacardsShow">
			<!-- <div class="x-window-header">{-#mdcsection#-} | {-$userid-} - {-$role-}</div> -->
			<!--
			<div id="dif-cfg" style="text-align:center;">
				<iframe name="dif" id="dif" frameborder="0" height="600px;" width="100%" src="cards.php?r={-$reg-}"></iframe>
			</div>
			-->
		</div>
		
		<!-- Results of queries -->
		<div class="contentBlock" id="divQueryResults" style="display:block">
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr bgcolor="#bbbbbb">
					<td width="200px">
						<div id="ResultHeaderText"><b>{-#tsubtitle2#-} &rarr;</b></div>
					</td>
					<td align="center">
						<div id="ResultHeaderButtonToolbar">
							<!--	SECTION : DATA CONFIGURATION ============================ -->
							<button id="dat-btn" class="rounded" ext:qtip="{-#tdatamsg#-}"><span>{-#bdata#-}</span></button>
							<div id="dat-win" class="x-hidden">
							{-include file="viewdata_parameters.tpl" -}
							</div> <!-- dat-win -->
							<!--	END DATA SECTION -->
							<!--	SECTION : THEMATICMAP CONFIGURATION -->
							<button id="map-btn" class="rounded" ext:qtip="{-#tthematicmsg#-}"><span>{-#bthematic#-}</span></button>
							<div id="map-win" class="x-hidden">
							{-include file="thematicmap_parameters.tpl" -}
							</div> <!-- map-win -->
							<!--	END MAP SECTION -->
							<!--	BEGIN GRAPHIC CONFIGURATION -->
							{-include file="graph_parameters.tpl"-}
							<!--	END GRAPHIC SECTION  -->
							<!--	SECTION : STATISTIC CONFIGURATION -->
							<button id="std-btn" class="rounded" ext:qtip="{-#tstatisticmsg#-}"><span>{-#bstatistic#-}</span></button>
							<div id="std-win" class="x-hidden">
							{-include file="statistic_parameters.tpl" -}
							</div> <!-- std-win -->
							<!--	END STATISTIC SECTION  -->
						</div>
						<!-- Show XML query open window-->
						<div id="qry-win" class="x-hidden">
							<div class="x-window-header">{-#mopenquery#-}</div>
							<div id="qry-cfg" style="text-align:center;"><!-- ?r={-$reg-}-->
								<form id="openquery" enctype="multipart/form-data" action="index.php?r={-$reg-}" method="POST">
									<br /><br />
									<input type="hidden" name="cmd" value="openquery" />
									<input type="hidden" name="MAX_FILE_SIZE" value="100000" />
									<input type="file" id="ofile" name="qry" onChange="$('openquery').submit();"/>
								</form>
							</div>
						</div>
						<!-- Show DesInventar (input data) window-->
						<div id="dif-win" class="x-hidden">
							<div class="x-window-header">{-#mdcsection#-} | {-$userid-} - {-$role-}</div>
							<div id="dif-cfg" style="text-align:center;">
								<iframe name="dif" id="dif" frameborder="0" height="600px;" width="100%" src="cards.php?r={-$reg-}"></iframe>
							</div>
						</div>
						<!-- Show User login/logout window -->
						<div id="usr-win" class="x-hidden">
							<div class="x-window-header">{-$userid-} - {-$role-}</div>
							<div id="usr">
								{-include file="user_login.tpl" -}
								<br />
								<a href="javascript:void(null)" class="rememberpasswordlink" onclick="updateList('passlost', 'user.php', 'cmd=passlost');">{-#tpasslost#-}</a>
								<div id="passlost"></div>
							</div>
						</div>
						<!-- Show Database functions window -->
						<div id="dbl-win" class="x-hidden">
							<div class="x-window-header">{-$userid-} - {-$role-}</div>
							<div id="dbl"></div>
						</div>
						<!-- Show Dialog window -->
						<div id="dlg-win" class="x-hidden">
							<div class="x-window-header"></div>
							<div id="dlg">
								<table border="0">
									<tr>
										<td><img src="images/di_logo.png">
										</td>
										<td><p style="font-size: 16pt;" align="center">DesInventar {-$version-}</p>
										</td>
									</tr>
									<tr>
										<td colspan="2">{-#tabout#-}<hr />{-#tcopyright#-}
										</td>
									</tr>
								</table>
							</div>
						</div>
					</td>
					<td>
						<span id="frmwait"></span>
						<input id="DCRes" type="hidden" value="" />
						<input id="bsave" type="button" class="bb bsave"   style="visibility: hidden;"
							onMouseOver="if($('DCRes').value == 'D' || $('DCRes').value == 'S') $('saveopt').style.display='block';"
							onClick="saveRes('export', '');" ext:qtip="{-#bsavemsg#-}" />
						<span id="saveopt" style="position:absolute; display: none" 
							onMouseOver="$('saveopt').style.display='block';" onMouseOut="setTimeout('$(\'saveopt\').style.display=\'none\';', 2000);">
							<input type="button" value="xls" class="bb line" onClick="saveRes('export', 'xls')" /> | 
							<input type="button" value="csv" class="bb line" onClick="saveRes('export', 'csv')" />
						</span>
						&nbsp;&nbsp;
						<input id="bprint" type="button" class="bb bprint" style="visibility: hidden;"	
							onClick="printRes();" ext:qtip="{-#bprintmsg#-}" />&nbsp;&nbsp;
					</td>
				</tr>
				<tr>
					<td colspan="3">
						<div id="querydetails" style="height:40px;" class="dwin"></div>
						<!--  <div id="smap" style="position:absolute; left:0px; top:20px; visibility:hidden;">[<a href="javascript:void(0);" onClick="hideMap();">X</a>]<br></div>-->
					</td>
				</tr>
			</table>
			<iframe id="dcr" name="dcr" frameborder="0" scrolling="auto" height="550px" width="100%" src="?cmd=getRegionFullInfo&r={-$reg-}"></iframe>
		</div> <!-- end div id=divQueryResults -->
		<div class="contentBlock" id="divDatabaseList" >
			{-include file="header_simple.tpl" -}
			{-include file="showlistdb.tpl" -}
		</div> <! id="divDatabaseList" -->
		<div class="contentBlock" id="divDatabaseBackup">
			Realizar Backup de Base de Datos
		</div>
	</div><!-- END div id=container-->
	
	<!-- SECTION : QUERY DESIGN -->
	<div id="west">
		<!-- BEG DI8 QUERY FORM -->
		<form id="DC" method="POST" target="dcr">
			<input type="hidden" id="_REG" name="_REG" value="{-$reg-}" />
			<input type="hidden" id="_CMD" name="_CMD" />
			<input type="hidden" id="prmMinYear" name="prmMinYear" value="{-$yini-}" />
			<input type="hidden" id="prmMaxYear" name="prmMaxYear" value="{-$yend-}" />
			<dl class="accordion">
				<!-- BEGIN GEOGRAPHY SECTION -->
				<!-- Select from Map testing ... 'selectionmap.php' -->
				<dt>{-#mgeosection#-}</dt>
				<dd>
					{-foreach name=glev key=k item=i from=$glev-}
						<span class="dlgmsg" onMouseOver="showtip('{-$i[1]-}');">{-$i[0]-}</span> |
					{-/foreach-}
					<div id="qgeolst" style="height: 280px;" class="dwin" ext:qtip="{-#thlpquery#-}">
						{-assign var="maintree" value="true"-}
						{-* Show Geography List *-}
						{-include file="main_glist.tpl" -}
					</div>
					
					<b onMouseOver="showtip('{-$dis.DisasterSiteNotes[2]-}');">{-$dis.DisasterSiteNotes[0]-}</b>
					<select name="D_DisasterSiteNotes[0]" class="small line">
						<option class="small" value="AND" {-if $qd.D_DisasterSiteNotes[0] == 'AND'-}selected{-/if-}>{-#tand#-}</option>
						<option class="small" value="OR"  {-if $qd.D_DisasterSiteNotes[0] == 'OR'-}selected{-/if-}>{-#tor#-}</option>
					</select>
					<br/>
					<textarea id="DisasterSiteNotes" name="D_DisasterSiteNotes[1]" style="width:220px; height: 40px;"
						onFocus="showtip('{-$dis.DisasterSiteNotes[2]-}');">{-$qd.D_DisasterSiteNotes[1]-}
					</textarea>
				</dd>
				
				<!-- BEGIN EVENT SECTION -->
				<dt>{-#mevesection#-}</dt>
				<dd>
					<span class="dlgmsg" ext:qtip="{-#thlpquery#-}">{-#tcntclick#-}</span><br>
					<select id="qevelst" name="D_EventId[]" multiple style="width: 250px; height: 200px;" class="line">
						{-include file="main_eventlist.tpl" -}
					</select>
					<br /><br />
					<b onMouseOver="showtip('{-$eve.EventDuration[2]-}');">{-$eve.EventDuration[0]-}</b><br />
					<input id="EventDuration" name="D_EventDuration" type="text" class="line fixw"
						onFocus="showtip('{-$eve.EventDuration[2]-}');" value="{-$qd.D_EventDuration-}" />
					<br />
					<b onMouseOver="showtip('{-$eve.EventNotes[2]-}');">{-$eve.EventNotes[0]-}</b>
					<select name="D_EventNotes[0]" class="small line">
						<option class="small" value="AND" {-if $qd.D_EventNotes[0] == 'AND'-}selected{-/if-}>{-#tand#-}</option>
						<option class="small" value="OR"  {-if $qd.D_EventNotes[0] == 'OR'-}selected{-/if-}>{-#tor#-}</option>
					</select><br />
					<textarea id="EventNotes" name="D_EventNotes[1]" style="width:250px; height:40px;"
						onFocus="showtip('{-$eve.EventNotes[2]-}');">{-$qd.D_EventNotes[1]-}
					</textarea>
				</dd>
					
				<!-- BEGIN CAUSE SECTION -->
				<dt>{-#mcausection#-}</dt>
				<dd>
					<span class="dlgmsg" ext:qtip="{-#thlpquery#-}">{-#tcntclick#-}</span><br>
					<select id="qcaulst" name="D_CauseId[]" multiple style="width: 250px; height: 200px;" class="line">
						{-include file="main_causelist.tpl" -}
					</select>
					<br /><br />
					<b onMouseOver="showtip('{-$cau.CauseNotes[2]-}');">{-$cau.CauseNotes[0]-}</b>
					<select name="D_CauseNotes[0]" class="small line">
						<option class="small" value="AND" {-if $qd.D_CauseNotes[0] == 'AND'-}selected{-/if-}>{-#tand#-}</option>
						<option class="small" value="OR"  {-if $qd.D_CauseNotes[0] == 'OR'-}selected{-/if-}>{-#tor#-}</option>
					</select><br />
					<textarea name="D_CauseNotes[1]" style="width:250px; height: 40px;"
						onFocus="showtip('{-$cau.CauseNotes[2]-}');">{-$qd.D_CauseNotes[1]-}
					</textarea>
				</dd>
				
				<!-- BEGIN QUERY EFFECTS SECTION -->
				<dt>{-#meffsection#-}</dt>
				<dd>
					<b>{-#ttitegp#-}</b><br />
					<div style="height: 100px;" class="dwin" ext:qtip="{-#thlpquery#-}">
						<table border="0" cellpadding="0" cellspacing="0">
							{-foreach name=ef1 key=key item=item from=$ef1-}
								{-assign var="ff" value=D_$key-}
								<tr>
									<td valign="top">
										<input type="checkbox" onFocus="showtip('{-$item[2]-}');" id="{-$key-}"
											onclick="enadisEff('{-$key-}', this.checked);" {-if $qd.$ff[0] != ''-}checked{-/if-} />
										<label for="{-$key-}" onMouseOver="showtip('{-$item[2]-}');">{-$item[0]-}</label>
										<span id="o{-$key-}" style="display:none">
											<select id="{-$key-}[0]" name="D_{-$key-}[0]" class="small line" disabled
												onChange="showeff(this.value, 'x{-$key-}', 'y{-$key-}');">
												<option class="small" value="-1" {-if $qd.$ff[0] == '-1'-}selected{-/if-}>{-#teffhav#-}</option>
												<option class="small" value="0"  {-if $qd.$ff[0] == '0'-}selected{-/if-}>{-#teffhavnot#-}</option>
												<option class="small" value="-2" {-if $qd.$ff[0] == '-2'-}selected{-/if-}>{-#teffdontknow#-}</option>
												<option class="small" value=">=" {-if $qd.$ff[0] == '>='-}selected{-/if-}>{-#teffmajor#-}</option>
												<option class="small" value="<=" {-if $qd.$ff[0] == '<='-}selected{-/if-}>{-#teffminor#-}</option>
												<option class="small" value="="  {-if $qd.$ff[0] == '='-}selected{-/if-}>{-#teffequal#-}</option>
												<option class="small" value="-3" {-if $qd.$ff[0] == '-3'-}selected{-/if-}>{-#teffbetween#-}</option>
											</select>
											<span id="x{-$key-}" style="display:none"><br>
												<input type="text" id="{-$key-}[1]" name="D_{-$key-}[1]" size="3" class="line"
													value="{-if $qd.$ff[1] != ''-}{-$qd.$ff[1]-}{-else-}1{-/if-}" />
											</span>
											<span id="y{-$key-}" style="display:none">{-#tand#-}
												<input type="text" id="{-$key-}[2]" name="D_{-$key-}[2]" size="3" class="line"
													value="{-if $qd.$ff[1] != ''-}{-$qd.$ff[2]-}{-else-}10{-/if-}" />
											</span>
											<select id="{-$key-}[3]" id="{-$key-}[3]" name="D_{-$key-}[3]" class="small line">
												<option class="small" value="AND" {-if $qd.$ff[3] == 'AND'-}selected{-/if-}>{-#tand#-}</option>
												<option class="small" value="OR"  {-if $qd.$ff[3] == 'OR'-}selected{-/if-}>{-#tor#-}</option>
											</select>
										</span>
									</td>
								</tr>
							{-/foreach-}
						</table>
					</div>
					<br />
					
					<!-- SECTORS -->
					<b>{-#ttiteis#-}</b><br />
					<div style="height: 80px;" class="dwin">
						<table border="0" cellpadding="0" cellspacing="0">
							{-foreach name=sec key=key item=item from=$sec-}
								{-assign var="ff" value=D_$key-}
								<tr>
									<td valign="top">
										<input type="checkbox" onFocus="showtip('{-$item[2]-}');" id="{-$key-}"
											onclick="{-foreach name=sc2 key=k item=i from=$item[3]-}enadisEff('{-$k-}', this.checked);{-/foreach-}enadisEff('{-$key-}', this.checked);"
											{-if $qd.$ff[0] != ''-}checked{-/if-} />
										<label for="{-$key-}" onMouseOver="showtip('{-$item[2]-}');">{-$item[0]-}</label>
										<span id="o{-$key-}" style="display:none">
											<select id="{-$key-}[0]" name="D_{-$key-}[0]" class="small line" disabled>
												<option class="small" value="-1" selected>{-#teffhav#-}</option>
												<option class="small" value="0"  {-if $qd.$ff[0] == '0'-}selected{-/if-}>{-#teffhavnot#-}</option>
												<option class="small" value="-2" {-if $qd.$ff[0] == '-2'-}selected{-/if-}>{-#teffdontknow#-}</option>
											</select>
											<select id="{-$key-}[3]" id="{-$key-}[3]" name="D_{-$key-}[3]" class="small line">
												<option class="small" value="AND" {-if $qd.$ff[3] == 'AND'-}selected{-/if-}>{-#tand#-}</option>
												<option class="small" value="OR"  {-if $qd.$ff[3] == 'OR'-}selected{-/if-}>{-#tor#-}</option>
											</select>
											{-foreach name=sc2 key=k item=i from=$item[3]-}
												{-assign var="ff" value=D_$k-}
												<span id="o{-$k-}" style="display:none">
													<br />{-$i-}
													<select id="{-$k-}[0]" name="D_{-$k-}[0]" onChange="showeff(this.value, 'x{-$k-}', 'y{-$k-}');" 
														class="small line" disabled>
														<option class="small" value=" "></option>
														<option class="small" value=">=" {-if $qd.$ff[0] == '>='-}selected{-/if-}>{-#teffmajor#-}</option>
														<option class="small" value="<=" {-if $qd.$ff[0] == '<='-}selected{-/if-}>{-#teffminor#-}</option>
														<option class="small" value="="  {-if $qd.$ff[0] == '='-}selected{-/if-}>{-#teffequal#-}</option>
														<option class="small" value="-3" {-if $qd.$ff[0] == '-3'-}selected{-/if-}>{-#teffbetween#-}</option>
													</select>
													<span id="x{-$k-}" style="display:none">
														<input type="text" id="{-$k-}[1]" name="D_{-$k-}[1]" size="3" class="line"
															value="{-if $qd.$ff[1] != ''-}{-$qd.$ff[1]-}{-else-}1{-/if-}" />
													</span>
													<span id="y{-$k-}" style="display:none">{-#tand#-}
														<input type="text" id="{-$k-}[2]" name="D_{-$k-}[2]" size="3" class="line"
															value="{-if $qd.$ff[1] != ''-}{-$qd.$ff[2]-}{-else-}10{-/if-}" />
													</span>
													<select id="{-$k-}[3]" id="{-$k-}[3]" name="D_{-$k-}[3]" class="small line">
														<option class="small" value="AND" {-if $qd.$ff[3] == 'AND'-}selected{-/if-}>{-#tand#-}</option>
														<option class="small" value="OR"  {-if $qd.$ff[3] == 'OR'-}selected{-/if-}>{-#tor#-}</option>
													</select><br />
												</span>
											{-/foreach-}
										</span>
									</td>
								</tr>
							{-/foreach-}
						</table>
					</div>
					<br />
					
					<!-- Losses -->
					<b>{-#ttitloss#-}</b><br />
					{-foreach name=ef3 key=k item=i from=$ef3-}
						{-assign var="ff" value=D_$k-}
						<input type="checkbox" onFocus="showtip('{-$i[2]-}');" id="{-$k-}"
							onclick="enadisEff('{-$k-}', this.checked);" {-if $qd.$ff[0] != ''-}checked{-/if-} />
						<label for="{-$k-}" onMouseOver="showtip('{-$i[2]-}');">{-$i[0]-}</label>
						<span id="o{-$k-}" style="display:none">
							<select id="{-$k-}[0]" name="D_{-$k-}[0]" onChange="showeff(this.value, 'x{-$k-}', 'y{-$k-}');" class="small line" disabled>
								<option class="small" value=" "></option>
								<option class="small" value=">=" {-if $qd.$ff[0] == '>='-}selected{-/if-}>{-#teffmajor#-}</option>
								<option class="small" value="<=" {-if $qd.$ff[0] == '<='-}selected{-/if-}>{-#teffminor#-}</option>
								<option class="small" value="="  {-if $qd.$ff[0] == '='-}selected{-/if-}>{-#teffequal#-}</option>
								<option class="small" value="-3" {-if $qd.$ff[0] == '-3'-}selected{-/if-}>{-#teffbetween#-}</option>
							</select>
							<span id="x{-$k-}" style="display:none"><br />
								<input type="text" id="{-$k-}[1]" name="D_{-$k-}[1]" size="5" class="line"
									value="{-if $qd.$ff[1] != ''-}{-$qd.$ff[1]-}{-else-}1{-/if-}" />
							</span>
							<span id="y{-$k-}" style="display:none">{-#tand#-}
								<input type="text" id="{-$k-}[2]" name="D_{-$k-}[2]" size="5" class="line" 
									value="{-if $qd.$ff[1] != ''-}{-$qd.$ff[2]-}{-else-}10{-/if-}" />
							</span>
							<select id="{-$key-}[3]" name="D_{-$key-}[3]" class="small line">
								<option class="small" value="AND" {-if $qd.$ff[3] == 'AND'-}selected{-/if-}>{-#tand#-}</option>
								<option class="small" value="OR"  {-if $qd.$ff[3] == 'OR'-}selected{-/if-}>{-#tor#-}</option>
							</select>
						</span>
						<br />
					{-/foreach-}
					{-foreach name=ef4 key=k item=i from=$ef4-}
						{-assign var="ff" value=D_$k-}
						<b onMouseOver="showtip('{-$i[2]-}');">{-$i[0]-}</b><br />
						<input type="text" id="{-$k-}" name="D_{-$k-}" class="fixw line" value="{-$qd.$ff[1]-}" onFocus="showtip('{-$i[2]-}');" />
						<br />
					{-/foreach-}
				</dd>
				<!-- END QUERY EFFECTS SECTION -->
				
				<!-- Begin EEField Section -->
				<dt>{-#mextsection#-}</dt>
				<dd>
					<div style="width: 300px; height: 300px;" class="dwin" ext:qtip="{-#thlpquery#-}">
						<table border=0 cellpadding=0 cellspacing=0>
							{-foreach name=eef key=key item=item from=$EEFieldList-}
								<tr>
									<td valign="top">
										{-if $item[2] == "INTEGER" || $item[2] == "DOUBLE"-}
											<input type="checkbox" onFocus="showtip('{-$item[1]-}');" id="{-$key-}" 
												onclick="enadisEff('{-$key-}', this.checked);" />
											<label for="{-$key-}" onMouseOver="showtip('{-$item[1]-}');">{-$item[0]-}</label>
											<span id="o{-$key-}" style="display:none">
												<select id="{-$key-}[0]" name="EEFieldQuery[{-$key-}][Operator]" onChange="showeff(this.value, 'x{-$key-}', 'y{-$key-}');" 
													class="small" disabled>
													<option class="small" value=""></option>
													<option class="small" value=">=">{-#teffmajor#-}</option>
													<option class="small" value="<=">{-#teffminor#-}</option>
													<option class="small" value="=">{-#teffequal#-}</option>
													<option class="small" value="-3">{-#teffbetween#-}</option>
												</select>
												<input type="hidden" name="EEFieldQuery[{-$key-}][Type]" value="{-$item[2]-}">
												<span id="x{-$key-}" style="display:none"><br />
													<input type="text" id="{-$key-}[1]" name="EEFieldQuery[{-$key-}][Value1]" size="3" value="1" class="line" />
												</span>
												<span id="y{-$key-}" style="display:none">
													{-#tand#-} <input type="text" id="{-$key-}[2]" name="EEFieldQuery[{-$key-}][Value2]" size="3" value="10" class="line">
												</span>
												<!--
												<select id="{-$key-}[3]" id="{-$key-}[3]" name="EEFieldQuery[{-$key-}][BetweenOperator]" class="small">
													<option class="small" value="AND" checked>{-#tand#-}</option>
													<option class="small" value="OR">{-#tor#-}</option>
												</select>
												-->
											</span>
										{-/if-}
										{-if $item[2] == "TEXT"-}
											{-$item[0]-}<br />
											<input type="text" id="{-$key-}" name="EEFieldQuery[{-$key-}][Value]" style="width: 290px;" class="line"
												onFocus="showtip('{-$item[1]-}');" /><br />
											<input type="hidden" name="EEFieldQuery[{-$key-}][Type]" value="{-$item[2]-}">
										{-/if-}
									</td>
								</tr>
							{-/foreach-}
						</table>
					</div>
					<br />
				</dd>
				<!-- END EEField Section -->
				
				<!-- BEGIN DATETIME SECTION -->
				<dt>{-#mdcsection#-}</dt>
				<dd class="default">
					<div style="height: 250px;">
						<b onMouseOver="showtip('{-$dis.DisasterBeginTime[2]-}');">{-#tdate#-}</b>
						<span class="dlgmsg">{-#tdateformat#-}</span><br />
						<table border="0">
							<tr>
								<td><b>{-#ttitsince#-}:</b></td>
								<td>
									<input type="text" id="queryBeginYear" name="D_DisasterBeginTime[]" size=4 maxlength=4 class="line" 
										value="{-if $qd.D_DisasterBeginTime[0] != ''-}{-$qd.D_DisasterBeginTime[0]-}{-else-}{-$yini-}{-/if-}" />
									<input type="text" id="queryBeginMonth" name="D_DisasterBeginTime[]" size=2 maxlength=2 class="line"
										value="{-$qd.D_DisasterBeginTime[1]-}" />
									<input type="text" id="queryBeginDay" name="D_DisasterBeginTime[]" size=2 maxlength=2 class="line"
										value="{-$qd.D_DisasterBeginTime[2]-}" />
								</td>
							</tr>
							<tr>
								<td><b>{-#ttituntil#-}:</b></td>
								<td>
									<input type="text" id="queryEndYear" name="D_DisasterEndTime[]" size=4 maxlength=4 class="line" 
										value="{-if $qd.D_DisasterEndTime[0] != ''-}{-$qd.D_DisasterEndTime[0]-}{-else-}{-$yend-}{-/if-}" />
									<input type="text" id="queryEndMonth" name="D_DisasterEndTime[]" size=2 maxlength=2 class="line"
										value="{-$qd.D_DisasterEndTime[1]-}" />
									<input type="text" id="queryEndDay" name="D_DisasterEndTime[]" size=2 maxlength=2 class="line"
										value="{-$qd.D_DisasterEndTime[2]-}" />
								</td>
							</tr>
						</table>
						<br />
						<b onMouseOver="showtip('{-$dis.DisasterSource[2]-}');">{-$dis.DisasterSource[0]-}</b>
						<select name="D_DisasterSource[0]" class="small line">
							<option class="small" value="AND" {-if $qd.D_DisasterSource[0] == 'AND'-}selected{-/if-}>{-#tand#-}</option>
							<option class="small" value="OR"  {-if $qd.D_DisasterSource[0] == 'OR'-}selected{-/if-}>{-#tor#-}</option>
						</select>
						<br />
						<textarea id="DisasterSource" name="D_DisasterSource[1]" style="width:220px; height:40px;"
							onFocus="showtip('{-$dis.DisasterSource[2]-}');">{-$qd.D_DisasterSource[1]-}
						</textarea>
						
						{-if $ctl_user-}
							<br />
							<b onMouseOver="showtip('');">{-#tdcstatus#-}</b><br />
							<select name="D_RecordStatus[]" multiple class="fixw line">
								<option value="PUBLISHED" selected>{-#tdcpublished#-}</option>
								<option value="READY" selected>{-#tdcready#-}</option>
								<option value="DRAFT">{-#tdcdraft#-}</option>
								<option value="TRASH">{-#tdctrash#-}</option>
							</select>
						{-else-}
							<input type="hidden" name="D_RecordStatus" value="PUBLISHED" />
						{-/if-}
						<br />
						<b onMouseOver="showtip('{-#tserialmsg#-}');">{-#tserial#-}</b>
						<select name="D_DisasterSerial[0]" class="small line">
							<option class="small" value=""  {-if $qd.D_DisasterSerial[0] == ''-}selected{-/if-}>{-#tonly#-}</option>
							<option class="small" value="NOT" {-if $qd.D_DisasterSerial[0] == 'NOT'-}selected{-/if-}>{-#texclude#-}</option>
							<option class="small" value="INCLUDE" {-if $qd.D_DisasterSerial[0] == 'INCLUDE'-}selected{-/if-}>{-#tinclude#-}</option>
						</select>
						<br />
						<input type="text" name="D_DisasterSerial[1]" class="line fixw" value="{-$qd.D_DisasterSerial[1]-}" />
					</div>
				</dd>
				<!-- END DATETIME SECTION -->
				
				<!-- BEGIN CUSTOMQUERY SECTION -->
				<dt>{-#madvsection#-}</dt>
				<dd alignt="left">
					<textarea id="CusQry" name="__CusQry" style="width:300px; height:45px;" onFocus="showtip('');">{-$qd.__CusQry-}</textarea>
					<br />
					<span class="dlgmsg">{-#tadvqryhelp#-}</span>
					<br />
					<table border="0" width="100%">
						<tr valign="top">
							<td>
								<div style="height:180px" class="dwin">
									<input type="button" class="CustomQueryListItem" value="{-$dis.DisasterSerial[0]-}" onClick="setAdvQuery('DisasterSerial', 'text')" /><br />
									<input type="button" class="CustomQueryListItem" value="{-$dis.DisasterBeginTime[0]-}" onClick="setAdvQuery('DisasterBeginTime', 'date')" /><br />
									<input type="button" class="CustomQueryListItem" value="{-$dis.DisasterSiteNotes[0]-}" onClick="setAdvQuery('DisasterSiteNotes', 'text')" /><br />
									<input type="button" class="CustomQueryListItem" value="{-$eve.EventDuration[0]-}" onClick="setAdvQuery('EventDuration', 'text')" /><br />
									<input type="button" class="CustomQueryListItem" value="{-$eve.EventNotes[0]-}" onClick="setAdvQuery('EventNotes', 'text')" /><br />
									<input type="button" class="CustomQueryListItem" value="{-$cau.CauseNotes[0]-}" onClick="setAdvQuery('CauseNotes', 'text')" /><br />
									<input type="button" class="CustomQueryListItem" value="{-$rc2.RecordAuthor[0]-}" onClick="setAdvQuery('RecordAuthor', 'text')" /><br />
									<input type="button" class="CustomQueryListItem" value="{-$rc2.RecordCreation[0]-}" onClick="setAdvQuery('RecordCreation','date')" /><br />
									<input type="button" class="CustomQueryListItem" value="{-$rc2.RecordUpdate[0]-}" onClick="setAdvQuery('RecordUpdate','date')" /><br />
									<hr />
									{-foreach name=ef1 key=key item=item from=$ef1-}
										<input type="button" class="CustomQueryListItem" value="{-$item[0]-}" onClick="setAdvQuery('{-$key-}','number')" /><br />
									{-/foreach-}
									<hr />
									{-foreach name=sec key=key item=item from=$sec-}
										<input type="button" class="CustomQueryListItem" value="{-$item[0]-}" onClick="setAdvQuery('{-$key-}','boolean')" /><br />
									{-/foreach-}
									<hr />
									{-foreach name=ef3 key=key item=item from=$ef3-}
										<input type="button" class="CustomQueryListItem" value="{-$item[0]-}" onClick="setAdvQuery('{-$key-}','number')" /><br />
									{-/foreach-}
									<hr />
									{-foreach name=ef4 key=key item=item from=$ef4-}
										<input type="button" class="CustomQueryListItem" value="{-$item[0]-}" onClick="setAdvQuery('{-$key-}','text')" /><br />
									{-/foreach-}
									<hr />
									{-foreach name=eef key=key item=item from=$EEFieldList-}
										<input type="button" class="CustomQueryListItem" value="{-$item[0]-}" onClick="setAdvQuery('{-$key-}','date')" /><br />
									{-/foreach-}
								</div>
							</td>
							<td align="center">
								<input type="button" id="<" value="<" class="disabled" disabled 
									onClick="$('CusQry').value += this.value; $('CusQry').focus();" onMouseOver="showtip('{-#taqlessthan#-}');" />
								<input type="button" id=">" value=">" class="disabled" disabled 
									onClick="$('CusQry').value += this.value; $('CusQry').focus();" onMouseOver="showtip('{-#taqgreathan#-}');" />
								<input type="button" id="=" value="=" class="disabled" disabled 
									onClick="$('CusQry').value += this.value; $('CusQry').focus();" onMouseOver="showtip('{-#taqequalto#-}');" /><br />
								<input type="button" id="<>" value="<>" class="disabled" disabled 
									onClick="$('CusQry').value += this.value; $('CusQry').focus();" onMouseOver="showtip('{-#taqnoteqto#-}');" />
								<input type="button" id="LIKE '%%'" value="{-#tlike#-}" class="disabled" disabled 
									onClick="$('CusQry').value += this.id; $('CusQry').focus();" onMouseOver="showtip('{-#taqlike#-}');" />
								<input type="button" id="=-1" value="{-#teffhav#-}" class="disabled" disabled 
									onClick="$('CusQry').value += this.id; $('CusQry').focus();" onMouseOver="showtip('{-#taqwere#-}');" />
								<input type="button" id="=0" value="{-#teffhavnot#-}" class="disabled" disabled 
									onClick="$('CusQry').value += this.id; $('CusQry').focus();" onMouseOver="showtip('{-#taqwerent#-}');" />
								<input type="button" id="=-2" value="{-#teffdontknow#-}" class="disabled" disabled 
									onClick="$('CusQry').value += this.id; $('CusQry').focus();" onMouseOver="showtip('{-#taqdntknow#-}');" />
								<br />
								<input type="button" value=" (" onClick="$('CusQry').value += this.value;" />
								<input type="button" value=") " onClick="$('CusQry').value += this.value;" />
								<input type="button" value=" AND " onClick="$('CusQry').value += this.value;" onMouseOver="showtip('{-#taqandopt#-}')" />
								<input type="button" value=" OR " onClick="$('CusQry').value += this.value;" onMouseOver="showtip('{-#taqoropt#-}')" />
								<br /><br />
								<input type="button" value="{-#tclean#-}" onClick="$('CusQry').value = '';" />
							</td>
						</tr>
					</table>
				</dd>
				<!-- BEGIN CUSTOMQUERY SECTION -->
			</dl>
		</form> <!-- id="DC" -->
		<!-- END DI8 QUERY FORM -->
	</div> <!-- id = west-->

	<!-- BEG HELP SECTION -->
	<div id="south">
		<textarea id="_DIDesc" wrap="hard" class="hlp" readonly style="width:80%; height:30px;">{-#tdescinfo#-}
		</textarea>
		<a href="javascript:void(null)" onClick="window.open('doc.php?m=metguide', 'doc', winopt);"
			class="dlgmsg" style="font-size: 8pt;">{-#hmoreinfo#-}</a>
	</div>
	<!-- END HELP SECTION -->
	{-include file="desinventarinfo.tpl"-}
</body>
</html>
