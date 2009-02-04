{-config_load file=`$lg`.conf section="di8_web"-}
{-**** SHOW UPDATER TO CONNECTION  (TOPMENU) ****-}
{-if $ctl_updater-}
{-$stat-}
{-**** SHOW PAGES (CONTENT) ****-}
{-elseif $ctl_pages-}
 {-if $page == "MainPageDI8" || $page == "MainPagewhatis" || $page == "MainPagecredits" || $page == "MainPagecontactus"-}
 	{-$menu.$page[2]-}
 {-elseif $page == "subregcan"-}
<!--  <span style="position:relative; top: 253px; left: 180px; width:50px; height:10px;">
  	<img src="images/b_desconsultar1.jpg"></span>-->
	<img src="images/subreg_can.jpg" alt="" usemap="#srcan" style="border-style:none" />
	<map id="srcan" name="srcan">
		<area shape="poly" alt="Colombia" coords="173,39,175,41,155,62,155,69,193,105,216,105,215,142,221,152,219,153,214,145,189,145,189,152,193,152,197,156,186,157,186,162,190,167,190,200,180,200,186,192,186,184,161,184,138,161,113,161,98,145,114,129,114,97,105,86,116,74,121,78,137,62,137,55,144,55,145,51,130,36,75,37,75,24,127,25,145,47,162,48" 
					href="javascript:void(null);" onclick="updateList('pagecontent', 'region.php', 'c=COL')" title="Colombia" />
		<area shape="poly" alt="Venezuela" coords="176,42,171,49,172,59,166,67,174,74,182,66,177,63,177,53,190,40,205,56,225,55,243,37,243,30,301,30,301,41,297,42,244,42,227,58,230,63,241,63,245,56,265,58,284,77,284,92,274,102,279,108,263,125,251,126,242,116,238,121,245,127,245,137,253,137,232,157,226,157,215,143,216,105,193,105,155,70,155,62" 
					href="javascript:void(null);" onclick="updateList('pagecontent', 'region.php', 'c=VEN')" title="Venezuela" />
		<area shape="poly" alt="Ecuador" coords="105,154,94,164,72,141,27,140,27,153,73,153,89,167,84,172,84,188,97,188,82,202,93,213,97,215,104,207,104,197,111,197,134,175,134,161,113,161" 
					href="javascript:void(null);" onclick="updateList('pagecontent', 'region.php', 'c=ECU')" title="Ecuador" />
		<area shape="poly" alt="Peru" coords="86,207,79,214,94,231,94,246,95,249,77,266,47,265,47,277,75,277,75,274,97,252,137,321,163,321,185,343,199,330,199,282,193,271,181,271,182,254,160,255,150,240,188,200,178,200,186,192,185,185,161,184,138,161,134,162,134,175,111,196,104,198,104,208,96,215" 
					href="javascript:void(null);" onclick="updateList('pagecontent', 'region.php', 'c=PER')" title="Peru" />
		<area shape="poly" alt="Bolivia" coords="195,271,211,272,224,258,238,258,238,278,247,287,259,288,270,299,283,298,283,325,307,326,306,336,312,343,313,355,306,361,299,354,277,354,264,367,264,386,246,387,245,392,227,381,215,395,208,395,208,373,200,365,206,359,197,350,161,386,124,386,124,373,159,373,163,377,194,347,188,341,199,330,200,282" 
					href="javascript:void(null);" onclick="updateList('pagecontent', 'region.php', 'c=BOL')" title="Bolivia" />
		<area shape="default" nohref="nohref" alt="" />
	</map>
 {-/if-}
{-**** DEFAULT MAINPAGE (PORTAL) ****-}
{-else-}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link rel="stylesheet" href="css/desinventar.css" TYPE="text/css">
	<link rel="stylesheet" href="css/portal.css" TYPE="text/css">
	<script type="text/javascript" src="include/prototype.js"></script>
	<script type="text/javascript" src="include/diadmin.js.php"></script>
<!--
	<script type="text/javascript" src="include/wd.js"></script>
	<script type="text/javascript" src="include/accordion.js"></script>
-->
	<script type="text/javascript" src="include/menu.js"></script>
	<script type="text/javascript" language="javascript">
	function updateUserBar(url, cmd, user, pass) {
		var pars = 'cmd=' + cmd + '&username=' + user + '&password=' + pass;
		var upd = true;
		if (cmd == "logout" && chkWin('desinventar')) {
			if (confirm("{-#talerdiwin#-}"))
				endWin('desinventar');
			else
				upd = false;
		}
		if (upd) {
			var rbAjax = new Ajax.Updater('rightcontent', url, {
				method: 'get', parameters: pars,
				onComplete: function(request) {
					if (cmd == "login")		updateList('pagecontent', url, 'cmd=welcome');
					if (cmd == "logout")	updateList('pagecontent', '', 'p=MainPageDI8');
				},
				onFailure: 	function(request) {
					$('rightcontent').innerHTML = "{-#hlostconn#-}";
				}
			});
		}
	}
	window.onload = function() {
		updateList('pagecontent', '', 'p=subregcan');
		updateUserBar('user.php', '{-$cmd-}', '', '');
	}
	window.onunload = function() {
		// Close Modules windows
		endWin('desinventar');
		endWin('desconsultar');
		endWin('doc');
		//updateContentBar('user.php', 'end', '', '');
	}
	</script>
	<title>DesInventar {-#tdiversion#-}</title>
</head>

<body>
<table width=839 border=0 cellpadding=0 cellspacing=0 align=center style="margin-top:4px">
<tr valign=top>
 <td style="background:url(images/bg1.gif) 0px -1px"><img src="images/e1.gif" width=16 height=74 border=0></td>
 <td>
	<table width=807 border=0 cellpadding=0 cellspacing=0>
	 <tr><td colspan="2"><img src="images/10.gif" width="807" height="15"></td></tr>
	 <tr valign="top">
		<td style="background:url(images/dionl.jpg);width:462px;height:92px">
			<div id="version">{-$DIver-}</div>
			<div id="slogan">{-#tdititle#-}</div>
		</td>
		<td><img src="images/11.jpg" width=345 height=92 border=0></td>
	 </tr>
	</table>
	<!-- menu hor -->
	<table width=807 border=0 cellpadding=0 cellspacing=0>
	<tr valign=top><td width=182><img src="images/21.gif" width=182 height=52></td>
		<td width=585 style="background:url(images/22.gif);" align="right">
			<span id="dostat" style="color: #e1ac00; float:left; font-size:8pt;"></span>
			<div align="right"><img src="images/23.jpg" width="305" height="13"></div>
			<table border=0 cellpadding=0 cellspacing=0 class="menu" style="margin-top:1px">
			 <tr>
			 	<td class="sel"><a href="javascript:void(null);" onclick="updateList('pagecontent','','p=subregcan')">{-#tstartpage#-}</a></td>
{-foreach name=menu key=key item=item from=$menu-}
				<td class="sel"><a href="javascript:void(null);" onclick="updateList('pagecontent','','p={-$key-}')">{-$item[0]-}</a></td>
{-/foreach-}
				<td><a href="javascript:void(null);" onMouseover="dropdownmenu(this, event, 'idioma')">{-#tlang#-}</a>
					<div id="idioma" class="submenu">
						<a href="?lang=en">English</a>
						<a href="?lang=es">Español</a>
						<a href="?lang=pt">Português</a>
<!--				<a href="?lang=fr">Français*</a> -->
					</div>
				</td>
			 </tr>
			</table>
		</td>
		<td width=40><img src="images/24.jpg" width=40 height=52 border=0></td>
	 </tr>
	</table>
	<!-- fin menu hor -->
	<table width=807 border=0 cellpadding=0 cellspacing=0>
	 <tr valign="top">
		<td class="izq">
		<!-- User's menu -->
		<table bgcolor="#CF9D15" border=0 cellpadding=0 cellspacing=0>
			<tr><td>
				<div class="olvido" id="usr" style="float:right; width:100px;">
					<a href="javascript:void(null);" 
						onclick="$('rightcontent').style.display='block'; $('usr').style.display='none';">{-#tusers#-}</a>
				</div>
				<div id="rightcontent" style="display:none;"></div>
			</td></tr>
			<tr><td>
				<img src="images/c_sep.gif" width=150 height=5 vspace=8>
			</td></tr>
		</table>
		<!-- Regions -->
 {-include file='menu.tpl'-}
		<!-- References -->
		<table bgcolor="#CF9D15" border=0 cellpadding=0 cellspacing=0 align="center">
			<tr><td bgcolor="white">
				<a href="http://www.siapad.net" target="_blank"><img src="images/logos/banner_SIAPAD.jpg" border="0"></a><br><br>
				<a href="http://www.redbivapad.org.pe/" target="_blank"><img src="images/logos/banner_BIVAPAD.jpg" border="0"></a><br><br>
				<a href="http://www.desaprender.org/" target="_blank"><img src="images/logos/banner_DESAPRENDER.gif" border="0"></a>
			</td></tr>
		</table>
		<!-- end left-menu -->
		</td>
		<td class="centro">{-$error-}
			<div id="pagecontent"></div>
		</td>
		<td class="der"><img src="images/bgder.gif" width=32 height=5></td>
	 </tr>
	</table>
 </td>
 <td style="background:url(images/bg2.gif) 0px -1px"><img src="images/e2.gif" width=16 height=74 border=0></td>
</tr>
<tr valign=bottom>
 <td style="background:url(images/bg1.gif) 0px -1px"><img src="images/e3.gif" width="16" height=85 border=0></td>
 <td>
	<table width=807 border=0 cellpadding=0 cellspacing=0 style="background:url(images/40_1.gif) left bottom no-repeat">
	<tr><td width="182"><img src="images/31_1.gif" width="182" height="22" border=0></td>
		<td style="background:url(images/32_1.gif)"><img src="images/32_1.gif" width="1" height="22" border=0></td>
		<td width="40"><img src="images/33_1.gif" width="40" height="22" border=0></td>
	</tr>
	<tr><td colspan="3">
		<div align="right" style="margin-right:30px;">
			<img src="images/logos/can.jpg" border=0><br>
			{-#tcredits#-}<br>
			<img src="images/logos/osso_lared.jpg" border=0><br>
			{-#tcopyright#-}
		</div>
<!--<img src="images/logosinst.png" width=730 height=63 border=0 vspace=15 usemap="#logosinst">
		<map id="logosinst" name="logosinst">
			<area shape="rect" alt="Comunidad Andina" coords="1,3,124,52" href="http://www.caprade.org" target="_blank"/>
			<area shape="rect" alt="PREDECAN" coords="133,4,213,62" href="http://www.comunidadandina.org/predecan/" target="_blank"/>
			<area shape="rect" alt="Comunidad Andina" coords="222,1,477,53" href="http://www.comunidadandina.org" target="_blank"/>
			<area shape="rect" alt="CorpOSSO" coords="485,2,610,53" href="http://www.osso.org.co" target="_blank"/>
			<area shape="rect" alt="LARED" coords="616,3,730,52" href="http://www.desenredando.org" target="_blank"/>
			<area shape="default" nohref="nohref" alt="" />
		</map>-->
	</td></tr>
	</table>
	<img src="images/40.gif" width=807 height=15 border="0">
 </td>
 <td style="background:url(images/bg2.gif) 0px -1px"><img src="images/e4.gif" width="16" height=85 border=0></td>
</tr>
</table>
</body>
</html>
{-/if-}
