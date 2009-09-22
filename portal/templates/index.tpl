{-**** SHOW PAGES (CONTENT) ****-}
{-if $ctl_pages-}
 {-if $page == "MainPageDI8" || $page == "MainPagewhatis" || $page == "MainPagecredits" || $page == "MainPagecontactus"-}
 	{-$menu.$page[2]-}
 {-/if-}
{-elseif $ctl_init-}
	<h4>{-#tdbavail#-}</h4>
	<table border=0>
	 <tr valign="top">
	  <td>
 {-foreach name=regions key=key item=item from=$reglst-}
		<li><a href="javascript:void(null);" onclick="updateList('reginfo', 'region.php', 'r={-$key-}');">{-$item-}</a></li>
 {-/foreach-}
	  </td>
	  <td>
		<div id="reginfo"></div>
	  </td>
	 </tr>
	</table>
{-**** DEFAULT MAINPAGE (PORTAL) ****-}
{-else-}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>DesInventar 8</title>
	<link rel="stylesheet" href="css/desinventar.css" TYPE="text/css">
	<link rel="stylesheet" href="css/portal.css" TYPE="text/css">
	<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon" />
	<script type="text/javascript" src="include/prototype.js"></script>
	<script type="text/javascript" src="include/diadmin.js"></script>
	<script type="text/javascript" src="include/menu.js"></script>
	<script type="text/javascript" language="javascript">
	
	window.onload = function() {
		//updateList('pagecontent', '', 'p=init');
		//updateUserBar('user.php', '', '', '');
	}
	window.onunload = function() {
		// Close Modules windows
		endWin('desinventar');
		endWin('desconsultar');
		endWin('doc');
		//updateContentBar('user.php', 'end', '', '');
	}
	</script>
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
			<div id="mode">{-$DImode-}</div>
			<div id="version">{-$DIver-}</div>
			<div id="slogan">{-#tdititle#-}</div>
		</td>
		<td><img src="images/11.jpg" width=345 height=92 border=0></td>
	 </tr>
	</table>
	<!-- menu hor -->
	<table width=807 border=0 cellpadding=0 cellspacing=0>
	 <tr valign=top>
		<td width=32><img src="images/21.jpg" width=32 height=75></td>
		<td width=730 style="background:url(images/22.jpg);" align="left" height=75>
			<table border=0 cellpadding=0 cellspacing=0 width="100%">
			 <tr>
			  <td height=37>
			   <!-- User's menu -->
			   <span class="olvido" id="usr" style="float:left; width:100px;">
				<a href="javascript:void(null);" 
					onclick="$('rightcontent').style.display='block'; $('usr').style.display='none';">{-#tusers#-}</a>
			   </span>
			   <span id="rightcontent" style="display:none;"></span>
				<!--<div align="right"><img src="images/23.jpg" width="305" height="13"></div>-->
			  </td>
			  <td align="right" valign="middle" style="background:url(images/23.jpg); background-repeat:no-repeat;" width="305" height="13">
				<select id="CountryList" onChange="updateList('pagecontent', 'region.php', 'c='+ this.value);"
					style="border: 1px solid #369; font-size: 1.1em; height: 1.5em; vertical-align: middle;">
					<option value=""></option>
{-foreach name=country key=key item=item from=$ctlst-}
					<option value="{-$item-}">{-$item-}</option>
{-/foreach-}
				</select>
				<!--<img src="images/findicon.png">-->
			  </td>
			 </tr>
			 <tr>
			  <td colspan=2 align="center">
			   <table border=0 cellpadding=0 cellspacing=0 class="menu">
			   <tr>
			 	<td class="sel"><a href="javascript:void(null);" onclick="updateList('pagecontent','','p=init')">{-#tstartpage#-}</a></td>
{-foreach name=menu key=key item=item from=$menu-}
				<td class="sel"><a href="javascript:void(null);" onclick="updateList('pagecontent','','p={-$key-}')">{-$item[0]-}</a></td>
{-/foreach-}
				<td class="sel"><a href="javascript:void(null);" onMouseover="dropdownmenu(this, event, 'idioma')">{-#tlang#-}</a>
					<div id="idioma" class="submenu">
{-foreach name=lglst key=key item=item from=$lglst-}
						<a href="?lang={-$key-}">{-$item[0]-}</a>
{-/foreach-}
					</div>
				</td>
			   </tr>
			   </table>
			   </td>
			 </tr>
			</table>
		</td>
		<td width=40><img src="images/24.jpg" width=40 height=75 border=0></td>
	 </tr>
	</table>
	<!-- fin menu hor -->
	<!-- Content Table -->
	<table width=807 border=0 cellpadding=0 cellspacing=0>
	 <tr valign="top">
	  <td class="izq"></td>
	  <td class="centro">
		<div id="pagecontent">
		</div>
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
	 <tr>
	  <td width="32"><img src="images/31_1.jpg" width="32" height="22" border=0></td>
	  <td style="background:url(images/32_1.gif)"><img src="images/32_1.gif" width="1" height="22" border=0></td>
	  <td width="40"><img src="images/33_1.gif" width="40" height="22" border=0></td>
	 </tr>
	 <tr>
	  <td colspan="3">
	   <table border=0 width="100%">
	    <tr>
		 <td align="center"><a href="http://www.desinventar.org" target="desinventar">DesInventar.org</a></td>
		 <td align="right" style="margin-right:50px;">
			<a href="http://www.osso.org.co" target="_blank"><img src="images/corposso.png" border=0></a>
			<a href="http://www.desenredando.org" target="_blank"><img src="images/lared.png" border=0></a>
			<br>{-#tcopyright#-}
		 </td>
		</tr>
	   </table>
	  </td>
	 </tr>
	</table>
	<img src="images/40.gif" width=807 height=15 border="0">
 </td>
 <td style="background:url(images/bg2.gif) 0px -1px"><img src="images/e4.gif" width="16" height=85 border=0></td>
</tr>
</table>
</body>
</html>
{-/if-}
