{-config_load file="eng.conf"-}
{-config_load file=`$lang`.conf section="portal"-}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>DesInventar8 - Portal</title>
	<!-- CSS Styles -->
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link rel="stylesheet" href="{-$desinventarURLPortal-}/css/desinventar.css?version={-$jsversion-}" TYPE="text/css" />
	<link rel="stylesheet" href="{-$desinventarURLPortal-}/css/portal.css?version={-$jsversion-}" TYPE="text/css" />
	{-include file="../../web/templates/jquery.tpl" confdir="../../web/conf/"-}
	<script type="text/javascript" src="{-$desinventarURL-}/include/menu.js"></script>
	<script type="text/javascript" src="{-$desinventarURL-}/include/md5.js"></script>
	<script type="text/javascript" src="{-$desinventarURL-}/js/user_login.js?version={-$jsversion-}"></script>
	<link type="text/css" rel="stylesheet" href="{-$desinventarURL-}/include/clickmenu/clickmenu.css" />
	<script type="text/javascript" src="{-$desinventarURL-}/include/clickmenu/jquery.clickmenu.js"></script>
	<script type="text/javascript" src="{-$desinventarURLPortal-}/js/main.js?version={-$jsversion-}"></script>
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
			<td style="background:url({-$desinventarURLPortal-}/images/bg1.gif) 0px -1px"><img src="{-$desinventarURLPortal-}/images/e1.gif" width=16 height=74 border=0>
			</td>
			<td>
				<!-- Header Table -->
				<table width=807 border=0 cellpadding=0 cellspacing=0>
					<tr>
						<td colspan="2">
							<img src="{-$desinventarURLPortal-}/images/10.gif" width="807" height="15">
						</td>
					</tr>
					<tr valign="top">
						<td style="background:url({-$desinventarURLPortal-}/images/dionl.jpg);width:462px;height:92px">
							<div id="version">
							</div>
							<div id="slogan">
								{-#di_slogan#-}
							</div>
						</td>
						<td>
							<img src="{-$desinventarURLPortal-}/images/11.jpg" width=345 height=92 border=0>
						</td>
					</tr>
				</table>
				<!-- Pages and Language Menu -->
				<table width=807 border=0 cellpadding=0 cellspacing=0>
					<tr valign=top>
						<td width=182>
							<img src="{-$desinventarURLPortal-}/images/21.gif" width=182 height=52>
						</td>
						<td width=585 style="background:url({-$desinventarURLPortal-}/images/22.gif);" align="right">
							<span id="dostat" style="color: #e1ac00; float:left; font-size:8pt;">
							</span>
							<div align="right">
								<img src="{-$desinventarURLPortal-}/images/23.jpg" width="305" height="13">
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
							<img src="{-$desinventarURLPortal-}/images/24.jpg" width=40 height=52 border=0>
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
								<tr><td bgcolor="white">
									<center>
										{-include file="logo_isdr.tpl" -}
										{-include file="logo_siapad.tpl" -}
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
											<center><a id="regionlink" href="#"><img id="regionbutton" src="{-$desinventarURLPortal-}/images/b_desinventar3.jpg" border="0"></a></center>
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
												<img id="btnMainWindow2" src="{-$desinventarURLPortal-}/images/b_desinventar3.jpg" border="0">
											</a>
										</td>
									</tr>
									<tr>
										<td>
											{-include file="block_southamerica.tpl"-}
										</td>
									</tr>
								</table>
							</div>
							<div class="portalcontent" id="UserContent">
								{-include file="../../web/templates/user_login.tpl" confdir="../../web/conf/"-}
							</div>
						</td> <!-- CENTRO -->
						<td class="der"><img src="{-$desinventarURLPortal-}/images/bgder.gif" width=32 height=5>
						</td>
					</tr>
				</table>
			</td>
			<td style="background:url({-$desinventarURLPortal-}/images/bg2.gif) 0px -1px"><img src="{-$desinventarURLPortal-}/images/e2.gif" width=16 height=74 border=0>
			</td>
		</tr>
		<tr valign=bottom>
			<td style="background:url({-$desinventarURLPortal-}/images/bg1.gif) 0px -1px">
				<img src="{-$desinventarURLPortal-}/images/e3.gif" width="16" height=85 border=0>
			</td>
			<td>
				<table width=807 border=0 cellpadding=0 cellspacing=0 style="background:url({-$desinventarURLPortal-}/images/40_1.gif) left bottom no-repeat">
					<tr>
						<td width="182"><img src="{-$desinventarURLPortal-}/images/31_1.gif" width="182" height="22" border=0>
						</td>
						<td style="background:url({-$desinventarURLPortal-}/images/32_1.gif)"><img src="{-$desinventarURLPortal-}/images/32_1.gif" width="1" height="22" border=0>
						</td>
						<td width="40"><img src="{-$desinventarURLPortal-}/images/33_1.gif" width="40" height="22" border=0>
						</td>
					</tr>
					<tr>
						<td colspan="3">
							<div align="right" style="margin-right:30px;">
								{-include file="logo_predecan.tpl"-}
								<br />
								{-include file="logo_osso.tpl"-}
							</div>
						
							<!--<img src="{-$desinventarURLPortal-}/images/logosinst.png" width=730 height=63 border=0 vspace=15 usemap="#logosinst"> -->
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
				<img src="{-$desinventarURLPortal-}/images/40.gif" width=807 height=15 border="0">
			</td>
			<td style="background:url({-$desinventarURLPortal-}/images/bg2.gif) 0px -1px"><img src="{-$desinventarURLPortal-}/images/e4.gif" width="16" height=85 border=0>
			</td>
		</tr>
	</table>
	<div>
	{-include file="../../web/templates/desinventarinfo.tpl" confdir="../../web/conf/"-}
	</div>
</body>
</html>
