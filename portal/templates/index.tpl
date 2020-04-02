{-config_load file="$lang.conf" section="portal"-}
{-config_load file="$lang.conf" section="region_list"-}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>DesInventar Online Edition {-$majorversion-}</title>
	<!-- CSS Styles -->
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link rel="stylesheet" href="{-$desinventarURLPortal-}/css/portal.css?version={-$jsversion-}" type="text/css" />
	<link rel="stylesheet" href="{-$desinventarURLPortal-}/css/desinventar.css?version={-$jsversion-}" type="text/css" />

	{-include file="jquery.tpl"-}

	<link type="text/css" rel="stylesheet" href="{-$desinventarURLPortal-}/external/clickmenu/clickmenu.css" />
	<script type="text/javascript" src="{-$desinventarURLPortal-}/external/clickmenu/jquery.clickmenu.js"></script>
	<script type="text/javascript" src="{-$desinventarURLPortal-}/scripts/bundle.js?version={-$jsversion-}"></script>
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
								{-#txtVersion#-} : <span id="txtVersion">{-$jsversion-}</span>
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
									<td align="left" width="50%">
										<span id="txtUserInfo"></span>
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
																<li><span class="change-language" data-language="eng">English</span></li>
																<li><span class="change-language" data-language="spa">Español</span></li>
																<li><span class="change-language" data-language="por">Português</span></li>
																<li><span class="change-language" data-language="fre">Français</span></li>
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
								<tr>
									<td bgcolor="white">
									</td>
								</tr>
							</table>
						</td>
						<td class="centro">
							<div class="contentBlock" id="regionBlock" style="display:none;">
								<div>
									<span class="databaseListTitle">{-#txtAvailableDB#-}</span>
									<br />
									<h3><span id="title_COUNTRY"></span></h3>
								</div>
								{-include file="region_list.tpl"-}
								<div class="contentRegionBlock" id="pageinfo" style="display:none;">
									<table width="100%">
										<tr>
											<td colspan="2" align="right">
												<div style="float:right;">
													<a class="regionlink" href="#" target="_blank" rel="noopener"><img id="regionbutton" src="{-$desinventarURLPortal-}/images/b_desinventar3.jpg" border="0" alt="" /></a>
													<br />
													<center>
														<a class="regionlink" href="#" target="_blank" rel="noopener">{-#msgRegionOpenDB#-}</a>
													</center>
												</div>
											</td>
										</tr>
										<tr>
											<td align="left">
												{-include file="region_info.tpl"-}
											</td>
										</tr>
									</table>
								</div>
							</div>
							<div class="contentBlock" id="pagemap" style="display:none">
								{-include file="block_covid-19_col.tpl"-}
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
							{-include file="portal_footer.tpl"-}
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
		{-include file="desinventarinfo.tpl"-}
		<div id="desinventarPortalInfo" style="display:none;">
			<input type="hidden" id="desinventarPortalType" value="{-$desinventarPortalType-}" />
		</div>
	</div>
</body>
</html>
