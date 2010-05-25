{-config_load file="eng.conf"-}
{-config_load file=`$lang`.conf section="portal"-}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>DesInventar8 - Portal</title>
	<!-- CSS Styles -->
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link rel="stylesheet" href="css/desinventar.css" TYPE="text/css" />
	<link rel="stylesheet" href="css/portal.css" TYPE="text/css" />
	{-include file="jquery.tpl" -}
	<script type="text/javascript" src="{-$desinventarURL-}/include/menu.js"></script>
	<script type="text/javascript" src="{-$desinventarURL-}/include/md5.js"></script>
	<script type="text/javascript" src="{-$desinventarURL-}/js/user_login.js?version={-$jsversion-}"></script>
	<link type="text/css" rel="stylesheet" href="{-$desinventarURL-}/include/clickmenu/clickmenu.css" />
	<script type="text/javascript" src="{-$desinventarURL-}/include/clickmenu/jquery.clickmenu.js"></script>
	<script type="text/javascript" src="js/main.js?version={-$jsversion-}"></script>
	<script type="text/javascript" language="javascript">
		jQuery(document).ready(function() {
			onReadyPortal();
			onReadyUserLogin();
		});
	</script>
</head>
<body>
	<table width=839 border=0 cellpadding=0 cellspacing=0 align=center style="margin-top:4px">
		<tr valign=top>
			<td style="background:url(images/bg1.gif) 0px -1px"><img src="images/e1.gif" width=16 height=74 border=0>
			</td>
			<td>
				<!-- Header Table -->
				<table width=807 border=0 cellpadding=0 cellspacing=0>
					<tr>
						<td colspan="2">
							<img src="images/10.gif" width="807" height="15">
						</td>
					</tr>
					<tr valign="top">
						<td style="background:url(images/dionl.jpg);width:462px;height:92px">
							<div id="version">
							</div>
							<div id="slogan">
								{-#di_slogan#-}
							</div>
						</td>
						<td>
							<img src="images/11.jpg" width=345 height=92 border=0>
						</td>
					</tr>
				</table>
				<!-- Pages and Language Menu -->
				<table width=807 border=0 cellpadding=0 cellspacing=0>
					<tr valign=top>
						<td width=182>
							<img src="images/21.gif" width=182 height=52>
						</td>
						<td width=585 style="background:url(images/22.gif);" align="right">
							<span id="dostat" style="color: #e1ac00; float:left; font-size:8pt;">
							</span>
							<div align="right">
								<img src="images/23.jpg" width="305" height="13">
							</div>
							<table border="0" width="100%">
								<tr>
									<td align="left">
										<span id="txtUserFullName"></span>
										<span id="txtUserId"></span>
									</td>
									<td align="right">
										<table border=0 cellpadding=0 cellspacing=0 class="menu" style="margin-top:1px">
											<tr>
												<td>
													<ul id="MainMenu">
														<li><a href="#" class="MenuItem" id="mnuShowMap">{-#tstartpage#-}</a>
														</li>
														<li>{-#tlang#-}
															<ul>
																<li><a href="?lang=eng">English</a></li>
																<li><a href="?lang=spa">Español</a></li>
																<li><a href="?lang=por">Português</a></li>
																<li><a href="?lang=fre">Français</a></li>
															</ul>
														</li>
													<ul>
												</td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
						</td>
						<td width=40>
							<img src="images/24.jpg" width=40 height=52 border=0>
						</td>
					</tr>
				</table>
				<!-- Left Menu/Icons -->
				<table width=807 border=0 cellpadding=0 cellspacing=0>
					<tr valign="top">
						<td class="izq">
							<!-- Regions -->
							{- include file="menu.tpl" -}
							<!-- References -->
							<table bgcolor="#CF9D15" border=0 cellpadding=0 cellspacing=0 align="center">
								<tr><td bgcolor="white"><center>
									<a href="http://www.preventionweb.net/gar09/" target="_blank"><img src="images/logos/{-$isdrlogo-}" border="0" width="140"></a><br /><br />
									<a href="http://www.georiesgo.net" target="_blank"><img src="images/logos/banner_SIAPAD.jpg" border="0"></a><br /><br />
									<a href="http://www.redbivapad.org.pe/" target="_blank"><img src="images/logos/banner_BIVAPAD.jpg" border="0"></a><br /><br />
									<a href="http://www.desaprender.org/" target="_blank"><img src="images/logos/banner_DESAPRENDER.gif" border="0"></a>
									</center>
								</td></tr>
							</table>
						</td>
						<td class="centro">
							<div class="portalcontent" id="regionlist">
								<span class="databaseTitle" id="regiontitle_COUNTRY"></span>
								<ul class="databaseList" id="regionlist_COUNTRY"></ul>
								<span class="databaseTitle" id="regiontitle_ADMINREGION">{-#txtRoleADMINREGION#-}</span>
								<ul class="databaseList" id="regionlist_ADMINREGION"></ul>
								<span class="databaseTitle" id="regiontitle_SUPERVISOR">{-#txtRoleSUPERVISOR#-}</span>
								<ul class="databaseList" id="regionlist_SUPERVISOR"></ul>
								<span class="databaseTitle" id="regiontitle_USER">{-#txtRoleUSER#-}</span>
								<ul class="databaseList" id="regionlist_USER"></ul>
								<span class="databaseTitle" id="regiontitle_OBSERVER">{-#txtRoleOBSERVER#-}</span>
								<ul class="databaseList" id="regionlist_OBSERVER"></ul>
								<span class="databaseTitle" id="regiontitle_NONE">{-#txtRoleNONE#-}</span>
								<ul class="databaseList" id="regionlist_NONE"></ul>
							</div>
							<div class="portalcontent" id="pageinfo">
								<table>
									<tr>
										<td valign="top">
											<img id="regionlogo" width="80">
										</td>
										<td align="left">
											<div id="regionbasicinfo"></div>
										</td>
									</tr>
									<tr>
										<td colspan="2">
											<center><a id="regionlink" href="#"><img id="regionbutton" src="images/b_desinventar3.jpg" border="0"></a></center>
											<div id="regiontechinfo"></div>
										</td>
									</tr>
								</table>
							</div>
							<div class="portalcontent" id="pagemap">
								<table>
									<tr>
										<td align="right">
											<a href="#" id="btnMainWindow">
												<img id="btnMainWindow2" src="images/b_desinventar3.jpg" border="0">
											</a>
										</td>
									</tr>
									<tr>
										<td>
											<img id="imgCAN" src="images/{-$imgCANMap-}" alt="" usemap="#srcan" style="border-style:none" />
											<map id="srcan" name="srcan">
												<area shape="poly" coords="173,39,175,41,155,62,155,69,193,105,216,105,215,142,221,152,219,153,214,145,189,145,189,152,193,152,197,156,186,157,186,162,190,167,190,200,180,200,186,192,186,184,161,184,138,161,113,161,98,145,114,129,114,97,105,86,116,74,121,78,137,62,137,55,144,55,145,51,130,36,75,37,75,24,127,25,145,47,162,48" 
													href="#" alt="COL" title="{-#tcountryCOL#-}" />
												<area shape="poly"  coords="176,42,171,49,172,59,166,67,174,74,182,66,177,63,177,53,190,40,205,56,225,55,243,37,243,30,301,30,301,41,297,42,244,42,227,58,230,63,241,63,245,56,265,58,284,77,284,92,274,102,279,108,263,125,251,126,242,116,238,121,245,127,245,137,253,137,232,157,226,157,215,143,216,105,193,105,155,70,155,62" 
													href="#" alt="VEN" title="{-#tcountryVEN#-}" />
												<area shape="poly" coords="105,154,94,164,72,141,27,140,27,153,73,153,89,167,84,172,84,188,97,188,82,202,93,213,97,215,104,207,104,197,111,197,134,175,134,161,113,161" 
													href="#" alt="ECU" title="{-#tcountryECU#-}" />
												<area shape="poly" coords="86,207,79,214,94,231,94,246,95,249,77,266,47,265,47,277,75,277,75,274,97,252,137,321,163,321,185,343,199,330,199,282,193,271,181,271,182,254,160,255,150,240,188,200,178,200,186,192,185,185,161,184,138,161,134,162,134,175,111,196,104,198,104,208,96,215" 
													href="#" alt="PER" title="{-#tcountryPER#-}" />
												<area shape="poly" coords="195,271,211,272,224,258,238,258,238,278,247,287,259,288,270,299,283,298,283,325,307,326,306,336,312,343,313,355,306,361,299,354,277,354,264,367,264,386,246,387,245,392,227,381,215,395,208,395,208,373,200,365,206,359,197,350,161,386,124,386,124,373,159,373,163,377,194,347,188,341,199,330,200,282" 
													href="#" alt="BOL" title="{-#tcountryBOL#-}" />
												<area shape="default" nohref="nohref" alt="" />
											</map>
										</td>
									</tr>
								</table>
							</div>
							<div class="portalcontent" id="UserContent">
								{-include file="../../web/templates/user_login.tpl" confdir="../../web/conf/"-}
							</div>
						</td> <!-- CENTRO -->
						<td class="der"><img src="images/bgder.gif" width=32 height=5>
						</td>
					</tr>
				</table>
			</td>
			<td style="background:url(images/bg2.gif) 0px -1px"><img src="images/e2.gif" width=16 height=74 border=0>
			</td>
		</tr>
		<tr valign=bottom>
			<td style="background:url(images/bg1.gif) 0px -1px">
				<img src="images/e3.gif" width="16" height=85 border=0>
			</td>
			<td>
				<table width=807 border=0 cellpadding=0 cellspacing=0 style="background:url(images/40_1.gif) left bottom no-repeat">
					<tr>
						<td width="182"><img src="images/31_1.gif" width="182" height="22" border=0>
						</td>
						<td style="background:url(images/32_1.gif)"><img src="images/32_1.gif" width="1" height="22" border=0>
						</td>
						<td width="40"><img src="images/33_1.gif" width="40" height="22" border=0>
						</td>
					</tr>
					<tr>
						<td colspan="3">
							<div align="right" style="margin-right:30px;">
								<img src="images/logos/can.jpg" border=0><br />
								{-#tcredits#-}<br />
								<a href="http://www.osso.org.co" target="_blank"><img src="images/logos/corposso.png" border=0></a>
								<a href="http://www.desenredando.org" target="_blank"><img src="images/logos/lared.png" border=0></a><br />
								{-#tcopyright#-}
							</div>
						
							<!--<img src="images/logosinst.png" width=730 height=63 border=0 vspace=15 usemap="#logosinst"> -->
							<!--
							<map id="logosinst" name="logosinst">
								<area shape="rect" alt="Comunidad Andina" coords="1,3,124,52" href="http://www.caprade.org" target="_blank"/>
								<area shape="rect" alt="PREDECAN" coords="133,4,213,62" href="http://www.comunidadandina.org/predecan/" target="_blank"/>
								<area shape="rect" alt="Comunidad Andina" coords="222,1,477,53" href="http://www.comunidadandina.org" target="_blank"/>
								<area shape="rect" alt="CorpOSSO" coords="485,2,610,53" href="http://www.osso.org.co" target="_blank"/>
								<area shape="rect" alt="LARED" coords="616,3,730,52" href="http://www.desenredando.org" target="_blank"/>
								<area shape="default" nohref="nohref" alt="" />
							</map>
							-->
						</td>
					</tr>
				</table>
				<img src="images/40.gif" width=807 height=15 border="0">
			</td>
			<td style="background:url(images/bg2.gif) 0px -1px"><img src="images/e4.gif" width="16" height=85 border=0>
			</td>
		</tr>
	</table>
	<div>
	{-include file="../../web/templates/desinventarinfo.tpl" confdir="../../web/conf/"-}
	</div>
</body>
</html>
