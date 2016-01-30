{-config_load file="$lang.conf" section="portal"-}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>DesInventar Online Edition</title>
	<!-- CSS Styles -->
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link rel="stylesheet" href="{-$desinventarURLPortal-}/css/portal.css?version={-$jsversion-}" type="text/css" />
	<link rel="stylesheet" href="{-$desinventarURL-}/css/desinventar.css?version={-$jsversion-}" type="text/css" />

	{-include file="../../web/templates/jquery.tpl" confdir="../../web/conf/"-}

	<script type="text/javascript" src="{-$desinventarURL-}/external/prototype.js"></script>
	<script type="text/javascript" src="{-$desinventarURL-}/external/menu.js"></script>
	<script type="text/javascript" src="{-$desinventarURL-}/external/md5.js"></script>

	<link type="text/css" rel="stylesheet" href="{-$desinventarURL-}/include/clickmenu/clickmenu.css" />
	<script type="text/javascript" src="{-$desinventarURL-}/include/clickmenu/jquery.clickmenu.js"></script>

	<script type="text/javascript" src="{-$desinventarURL-}/js/common.js?version={-$jsversion-}"></script>
	<script type="text/javascript" src="{-$desinventarURL-}/js/dboperations.js?version={-$jsversion-}"></script>
	<script type="text/javascript" src="{-$desinventarURL-}/js/user_login.js?version={-$jsversion-}"></script>
	<script type="text/javascript" src="{-$desinventarURLPortal-}/js/main.js?version={-$jsversion-}"></script>
	<script type="text/javascript" language="javascript">
		jQuery(document).ready(function() {
			onReadyPortal();
			onReadyUserLogin();
		});
	</script>
</head>
<body>
	<table width="839" border="0" cellpadding="0" cellspacing="0" align="center" style="margin-top:4px">
		<tr valign="top">
			<td style="background:url({-$desinventarURLPortal-}/images/bg1.gif) 0px -1px">
			<img src="{-$desinventarURLPortal-}/images/e1.gif" alt="" width="16" height="74" border="0" />
			</td>
			<td>
				<!-- Header Table -->
				<table width="807" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td colspan="2">
							<img src="{-$desinventarURLPortal-}/images/10.gif" alt="" width="807" height="15" />
						</td>
					</tr>
					<tr valign="top">
						<td style="background:url({-$desinventarURLPortal-}/images/dionl.jpg);width:462px;height:92px">
							<div id="divSlogan">
								{-#di_slogan#-}
							</div>
							<div id="divVersion">
								{-#txtVersion#-} : <span id="txtVersion"></span>
							</div>
						</td>
						<td>
							<img src="{-$desinventarURLPortal-}/images/11.jpg" alt="" width="345" height="92" border="0" />
						</td>
					</tr>
				</table>
				<!-- Pages and Language Menu -->
				<table width="807" border="0" cellpadding="0" cellspacing="0">
					<tr valign="top">
						<td width="182">
							<img src="{-$desinventarURLPortal-}/images/21.gif" width="182" height="52" alt="" />
						</td>
						<td width="585" style="background:url({-$desinventarURLPortal-}/images/22.gif);" align="right">
							<span id="dostat" style="color: #e1ac00; float:left; font-size:8pt;">
							</span>
							<div align="right">
								<img src="{-$desinventarURLPortal-}/images/23.jpg" width="305" height="13" alt="" />
							</div>
							<table border="0" width="100%">
								<tr>
									<td align="right" width="50%">
									</td>
									<td align="right">
										<table border="0" cellpadding="0" cellspacing="0" class="menu" style="margin-top:1px">
											<tr>
												<td>
													<ul id="MainMenu">
														<li>
															<a href="#" class="MenuItem" id="mnuShowMap">{-#tstartpage#-}</a>
														</li>
														<li>{-#tlang#-}
															<ul>
																<li><a href="?lang=eng">English</a></li>
																<li><a href="?lang=spa">Español</a></li>
																<li><a href="?lang=por">Português</a></li>
																<li><a href="?lang=fre">Français</a></li>
															</ul>
														</li>
													</ul>
												</td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
						</td>
						<td width="40">
							<img src="{-$desinventarURLPortal-}/images/24.jpg" width="40" height="52" border="0" alt="" />
						</td>
					</tr>
				</table>
				<!-- Left Menu/Icons -->
				<table width="807" border="0" cellpadding="0" cellspacing="0">
					<tr valign="top">
						<td class="izq">
							<!-- Regions -->
							<table bgcolor="#CF9D15" border="0" cellpadding="0" cellspacing="0">
								<tr>
									<td>
										{-include file="menu_user.tpl"-}
									</td>
								</tr>
								<tr>
									<td>
										{-include file="menu_dblist_portal.tpl"-}
										{-include file="menu_dblist_gar2009.tpl"-}
										{-include file="menu_dblist_gar2011.tpl"-}
									</td>
								</tr>
								<tr><td><img src="{-$desinventarURLPortal-}/images/25.gif" width="150" height="52" alt="" /></td></tr>
							</table>
							<!-- References -->
							<table bgcolor="#CF9D15" border="0" cellpadding="0" cellspacing="0" align="center">
								<tr><td bgcolor="white">
									<center>
										{-include file="logo_isdr.tpl"-}
										{-include file="logo_siapad.tpl"-}
										{-include file="logo_gar.tpl"-}
									</center>
								</td></tr>
							</table>
						</td>
						<td class="centro">
							{-include file="../../web/templates/region_list.tpl"-}
							<div class="contentBlock" id="pageinfo">
								<table width="100%">
									<tr>
										<td colspan="2" align="right">
											<div></div>
											<div style="float:right;">
												<a class="regionlink" href="#"><img id="regionbutton" src="{-$desinventarURLPortal-}/images/b_desinventar3.jpg" border="0" alt="" /></a>
												<br />
												<center>
													<a class="regionlink" href="#">{-#msgRegionOpenDB#-}</a>
												</center>
											</div>
										</td>
									</tr>
									<tr>
										<td align="left">
											{-include file="../../web/templates/region_info.tpl"-}
										</td>
									</tr>
								</table>
							</div>
							<div class="contentBlock" id="pagemap">
								{-include file="block_southamerica.tpl"-}
								{-include file="block_gar2009.tpl"-}
								{-include file="block_gar2011.tpl"-}
							</div>
						</td>
						<td class="der" width="10"><img src="{-$desinventarURLPortal-}/images/bgder.gif" width="32" height="5" alt="" />
						</td>
					</tr>
				</table>
			</td>
			<td style="background:url({-$desinventarURLPortal-}/images/bg2.gif) 0px -1px"><img src="{-$desinventarURLPortal-}/images/e2.gif" width="16" height="74" border="0" alt="" />
			</td>
		</tr>
		<tr valign="bottom">
			<td style="background:url({-$desinventarURLPortal-}/images/bg1.gif) 0px -1px">
				<img src="{-$desinventarURLPortal-}/images/e3.gif" width="16" height="85" border="0" alt="" />
			</td>
			<td>
				<table width="807" border="0" cellpadding="0" cellspacing="0" style="background:url({-$desinventarURLPortal-}/images/40_1.gif) left bottom no-repeat">
					<tr>
						<td width="182"><img src="{-$desinventarURLPortal-}/images/31_1.gif" width="182" height="22" border="0" alt="" />
						</td>
						<td style="background:url({-$desinventarURLPortal-}/images/32_1.gif)"><img src="{-$desinventarURLPortal-}/images/32_1.gif" width="1" height="22" border="0" alt="" />
						</td>
						<td width="40"><img src="{-$desinventarURLPortal-}/images/33_1.gif" width="40" height="22" border="0" alt="" />
						</td>
					</tr>
					<tr>
						<td colspan="3">
							<div align="right" style="margin-right:30px;">
								{-include file="logo_predecan.tpl"-}
								<br />
								{-include file="logo_osso.tpl"-}
							</div>
						</td>
					</tr>
				</table>
				<img src="{-$desinventarURLPortal-}/images/40.gif" width="807" height="15" border="0" alt="" />
			</td>
			<td style="background:url({-$desinventarURLPortal-}/images/bg2.gif) 0px -1px"><img src="{-$desinventarURLPortal-}/images/e4.gif" width="16" height="85" border="0" alt="" />
			</td>
		</tr>
	</table>
	<div>
		{-include file="../../web/templates/desinventarinfo.tpl" confdir="../../web/conf/"-}
		<div id="desinventarPortalInfo" style="display:none;">
			<input type="hidden" id="desinventarPortalType" value="{-$desinventarPortalType-}" />
		</div>
	</div>
</body>
</html>
