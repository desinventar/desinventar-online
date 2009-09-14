<?php 
switch ($lg) {
	case 'spa':
		$tcountries	= "Regiones";
		$tvirtualreg= "Regiones virtuales";
		$tcredits	= "En alianza estratégica con";
		$treg1		= "Asia";
		$treg2		= "Centro América";
		$treg3		= "Cono Sur";
		$treg4		= "Gran Chaco";
		$treg5		= "Norte América";
		$treg6		= "SubRegión Andina";
		$treg7		= "Ciudades";
		$treg8		= "Gran Chaco Americano";
	break;
	case 'por':
		$tcountries	= "Regiões";
		$tvirtualreg= "Regiões Virtuais";
		$tcredits	= "Em convenio estratégico com";
		$treg1		= "Ásia";
		$treg2		= "América Central";
		$treg3		= "Cone Sul";
		$treg4		= "Grande Chaco Americano";
		$treg5		= "América do Norte";
		$treg6		= "Sub Região Andina";
		$treg7		= "Cidades";
		$treg8		= "Grande Chaco Americano";
	break;
	default:
		$tcountries	= "Regions";
		$tvirtualreg= "Virtual regions";
		$tcredits	= "In strategic alliance with";
		$treg1		= "Asia";
		$treg2		= "Central America";
		$treg3		= "Southern Cone";
		$treg4		= "Great American Chaco";
		$treg5		= "North America";
		$treg6		= "Andean Subregion";
		$treg7		= "Cities";
		$treg8		= "Great American Chaco";
	break;
}
?>
<script type="text/javascript" language="javascript">
// personalization List menu..
function displayList(elem) {
	var ele = null;
	lst = 7;
	for (i=1; i <= lst; i++) {
		ele = document.getElementById("sect"+ i);
		if (i == elem)
			ele.style.display = 'block';
		else
			ele.style.display = 'none';
	}
}
</script>
<table border=0>
	<tr valign="top">
	 <td>
		<!-- Regions -->
		<table border=0 cellpadding=0 cellspacing=0>
			<tr><td>
				<!-- ANDIAN SUBREGION -->
				<table width="133" border=0 cellpadding=0 cellspacing=0 align=center>
					<tr><td><img src="default/images/p_paises1.gif" width=5 height=16></td>
							<td width="32px" bgcolor="#fcc700"><?php echo $tcountries; ?></td>
					<td><img src="default/images/p_paises3.gif" width=73 height=16></td></tr>
					<tr><td colspan="3">
						<img src="default/images/p_ini.gif" width="133" height="5"></td></tr>
					<tr><td class="paisel" colspan="3">
						<a href="javascript:void(null);" 
								onClick="displayList('6');"><?php echo $treg6; ?></a></td></tr>
					<tr><td colspan="3">
						<img src="default/images/p_sep.gif" width=133 height=6 border=0></td></tr>
				</table>
				<table width="133" border=0 cellpadding=0 cellspacing=0 align=center id="sect6">
					<tr><td class="pais">&nbsp;&nbsp;&nbsp; >
						<a href="javascript:void(null);" onclick="updateList('pagecontent', 'region.php', 'c=BOL');">Bolivia</a>
					</td></tr>
					<tr><td><img src="default/images/p_sep.gif" width=133 height=6 border=0></td></tr>
					<tr><td class="pais">&nbsp;&nbsp;&nbsp; >
						<a href="javascript:void(null);" onclick="updateList('pagecontent', 'region.php', 'c=COL');">Colombia</a>
					</td></tr>
					<tr><td colspan="3"><img src="default/images/p_sep.gif" width=133 height=6 border=0></td></tr>
					<tr><td class="pais">&nbsp;&nbsp;&nbsp; >
						<a href="javascript:void(null);" onclick="updateList('pagecontent', 'region.php', 'c=ECU');">Ecuador</a>
					</td></tr>
					<tr><td colspan="3"><img src="default/images/p_sep.gif" width=133 height=6 border=0></td></tr>
					<tr><td class="pais" colspan="3">&nbsp;&nbsp;&nbsp; >
						<a href="javascript:void(null);" onclick="updateList('pagecontent', 'region.php', 'c=PER');">Per&uacute;</a>
					</td></tr>
					<tr><td colspan="3"><img src="default/images/p_sep.gif" width=133 height=6 border=0></td></tr>
					<tr><td class="pais" colspan="3">&nbsp;&nbsp;&nbsp; >
						<a href="javascript:void(null);" onclick="updateList('pagecontent', 'region.php', 'c=VEN');">Venezuela</a>
					</td></tr>
					<tr><td colspan="3"><img src="default/images/p_sep.gif" width=133 height=6 border=0></td></tr>
				</table>
				<!-- ASIAN-->
				<table width="133" border=0 cellpadding=0 cellspacing=0 align=center>
					<tr><td class="paisel">
						<a href="javascript:void(null);" 
								onClick="displayList(1);"><?php echo $treg1; ?></a></td></tr>
					<tr><td>
						<img src="default/images/p_sep.gif" width=133 height=6 border=0></td></tr>
				</table>
				<table width="133" border=0 cellpadding=0 cellspacing=0 align=center id="sect1" style="display:none;">
					<tr><td class="pais" colspan="3">&nbsp;&nbsp;&nbsp; >
						<a href="javascript:void(null);" onclick="updateList('pagecontent', 'region.php', 'r=IND-1250695040-india_orissa_historic_inventory_of_disasters');">India - Orissa</a>
					</td></tr>
					<tr><td colspan="3"><img src="default/images/p_sep.gif" width=133 height=6 border=0></td></tr>
<!--					<tr><td class="pais" colspan="3">&nbsp;&nbsp;&nbsp; >
						<a href="javascript:void(null);" onclick="updateList('pagecontent', 'region.php', 'r=IND-1248830503-india_tamil_nadu_historic_inventory_of_disasters');">India - Tamil Nadu</a>
					</td></tr>
					<tr><td colspan="3"><img src="default/images/p_sep.gif" width=133 height=6 border=0></td></tr>
					<tr><td class="pais" colspan="3">&nbsp;&nbsp;&nbsp; >
						<a href="javascript:void(null);" onclick="updateList('pagecontent', 'region.php', 'r=IRN-1248830532-iran_historic_inventory_of_disasters');">Iran</a>
					</td></tr>
					<tr><td colspan="3"><img src="default/images/p_sep.gif" width=133 height=6 border=0></td></tr>
					<tr><td class="pais" colspan="3">&nbsp;&nbsp;&nbsp; >
						<a href="javascript:void(null);" onclick="updateList('pagecontent', 'region.php', 'r=NPL-1248830584-nepal_historic_inventory_of_disasters');">Nepal</a>
					</td></tr>
					<tr><td colspan="3"><img src="default/images/p_sep.gif" width=133 height=6 border=0></td></tr>-->
				</table>
				<!-- CENTER AMERICA -->
				<table width="133" border=0 cellpadding=0 cellspacing=0 align=center>
					<tr><td class="paisel">
						<a href="javascript:void(null);" 
								onClick="displayList(2);"><?php echo $treg2; ?></a></td></tr>
					<tr><td>
						<img src="default/images/p_sep.gif" width=133 height=6 border=0></td></tr>
				</table>
				<table width="133" border=0 cellpadding=0 cellspacing=0 align=center id="sect2" style="display:none;">
					<tr><td class="pais" colspan="3">&nbsp;&nbsp;&nbsp; >
						<a href="javascript:void(null);" onclick="updateList('pagecontent', 'region.php', 'r=CRI-1250694968-costa_rica_inventario_historico_de_desastres');">Costa Rica</a>
					</td></tr>
					<tr><td colspan="3"><img src="default/images/p_sep.gif" width=133 height=6 border=0></td></tr>
					<tr><td class="pais" colspan="3">&nbsp;&nbsp;&nbsp; >
						<a href="javascript:void(null);" onclick="updateList('pagecontent', 'region.php', 'r=SLV-1250695592-el_salvador_inventario_historico_de_desastres');">El Salvador</a>
					</td></tr>
					<tr><td colspan="3"><img src="default/images/p_sep.gif" width=133 height=6 border=0></td></tr>
					<tr><td class="pais" colspan="3">&nbsp;&nbsp;&nbsp; >
						<a href="javascript:void(null);" onclick="updateList('pagecontent', 'region.php', 'r=PAN-1250695231-panama_inventario_de_desastres_sinaproc');">Panam&aacute;</a>
					</td></tr>
					<tr><td colspan="3"><img src="default/images/p_sep.gif" width=133 height=6 border=0></td></tr>
				</table>
				<!-- SOUTH CONE -->
				<table width="133" border=0 cellpadding=0 cellspacing=0 align=center>
					<tr><td class="paisel">
						<a href="javascript:void(null);" 
								onClick="displayList(3);"><?php echo $treg3; ?></a></td></tr>
					<tr><td>
						<img src="default/images/p_sep.gif" width=133 height=6 border=0></td></tr>
				</table>
				<table width="133" border=0 cellpadding=0 cellspacing=0 align=center id="sect3" style="display:none;">
					<tr><td class="pais" colspan="3">&nbsp;&nbsp;&nbsp; >
						<a href="javascript:void(null);" onclick="updateList('pagecontent', 'region.php', 'c=ARG');">Argentina</a>
					</td></tr>
					<tr><td colspan="3"><img src="default/images/p_sep.gif" width=133 height=6 border=0></td></tr>
					<tr><td class="pais" colspan="3">&nbsp;&nbsp;&nbsp; >
						<a href="javascript:void(null);" onclick="updateList('pagecontent', 'region.php', 'r=PAR-1250695238-paraguay_inventario_historico_de_desastres');">Paraguay</a>
					</td></tr>
					<tr><td colspan="3"><img src="default/images/p_sep.gif" width=133 height=6 border=0></td></tr>
				</table>
				<!-- BIG CHACO 
				<table width="133" border=0 cellpadding=0 cellspacing=0 align=center>
					<tr><td class="paisel">
						<a href="javascript:void(null);"
								onClick="displayList(4);"><?php echo $treg4; ?></a></td></tr>
					<tr><td>
						<img src="default/images/p_sep.gif" width=133 height=6 border=0></td></tr>
				</table>
				<table width="133" border=0 cellpadding=0 cellspacing=0 align=center id="sect4" style="display:none;">
					<tr><td class="pais" colspan="3">&nbsp;&nbsp;&nbsp; >
						<a href="javascript:void(null);" onclick="updateList('pagecontent', 'region.php', 'r=ARG-1250695025-argentina_gran_chaco');">Argentina</a>
					</td></tr>
					<tr><td colspan="3"><img src="default/images/p_sep.gif" width=133 height=6 border=0></td></tr>
					<tr><td class="pais" colspan="3">&nbsp;&nbsp;&nbsp; >
						<a href="javascript:void(null);" onclick="updateList('pagecontent', 'region.php', 'r=BOL-1250695036-bolivia_gran_chaco');">Bolivia</a>
					</td></tr>
					<tr><td colspan="3"><img src="default/images/p_sep.gif" width=133 height=6 border=0></td></tr>
					<tr><td class="pais" colspan="3">&nbsp;&nbsp;&nbsp; >
						<a href="javascript:void(null);" onclick="updateList('pagecontent', 'region.php', 'r=PAR-1250695038-paraguay_gran_chaco');">Paraguay</a>
					</td></tr>
					<tr><td colspan="3"><img src="default/images/p_sep.gif" width=133 height=6 border=0></td></tr>
				</table>-->
				<!-- NORTH AMERICA -->
				<table width="133" border=0 cellpadding=0 cellspacing=0 align=center>
					<tr><td class="paisel">
						<a href="javascript:void(null);" 
								onClick="displayList(5);"><?php echo $treg5; ?></a></td></tr>
					<tr><td>
						<img src="default/images/p_sep.gif" width=133 height=6 border=0></td></tr>
				</table>
				<table width="133" border=0 cellpadding=0 cellspacing=0 align=center id="sect5" style="display:none;">
					<tr><td class="pais" colspan="3">&nbsp;&nbsp;&nbsp; >
						<a href="javascript:void(null);" onclick="updateList('pagecontent', 'region.php', 'r=MEX-1250695136-mexico_inventario_historico_de_desastres');">M&eacute;xico</a>
					</td></tr>
					<tr><td colspan="3"><img src="default/images/p_sep.gif" width=133 height=6 border=0></td></tr>
				</table>
				<!-- CITIES -->
				<table width="133" border=0 cellpadding=0 cellspacing=0 align=center>
					<tr><td class="paisel">
						<a href="javascript:void(null);" 
								onClick="displayList(7);"><?php echo $treg7; ?></a></td></tr>
				</table>
				<table width="133" border=0 cellpadding=0 cellspacing=0 align=center id="sect7" style="display:none;">
					<tr><td class="pais" colspan="3">&nbsp;&nbsp;&nbsp; >
						<a href="javascript:void(null);" onclick="updateList('pagecontent', 'region.php', 'r=COL-1250694494-colombia_inventario_desastres_cali_zona_urbana');">Cali - Colombia</a>
					</td></tr>
					<tr><td colspan="3"><img src="default/images/p_sep.gif" width=133 height=6 border=0></td></tr>
				</table>
				<table width="133" border=0 cellpadding=0 cellspacing=0 align=center>
					<tr><td colspan="3"><img src="default/images/p_fin.gif" width="133" height="5"></td></tr>
				</table>
				<br>
				<table width="133" border=0 cellpadding=0 cellspacing=0 align=center>
					<tr><td><img src="default/images/p_paises1.gif" width=5 height=16></td>
							<td width="103px" bgcolor="#fcc700"><?php echo $tvirtualreg; ?></td>
							<td><img src="default/images/p_paises3.gif" width=25 height=16></td></tr>
					<tr><td colspan="3"><img src="default/images/p_ini.gif" width="133" height="5"></td></tr>
					<tr><td class="pais" colspan="3">
						<a href="javascript:void(null)" onclick="updateList('pagecontent', 'region.php', 
							'r=DESINV-1249040429-can_subregion_andina')"><?php echo $treg6;?></a>
					</td></tr>
					<tr><td colspan="3"><img src="default/images/p_fin.gif" width="133" height="5"></td></tr>
					<tr><td colspan="3"><img src="default/images/p_ini.gif" width="133" height="5"></td></tr>
					<tr><td class="pais" colspan="3">
						<a href="javascript:void(null)" onclick="updateList('pagecontent', 'region.php', 
							'r=DESINV-1249126759-subregion_gran_chaco')"><?php echo $treg8;?></a>
					</td></tr>
					<tr><td colspan="3"><img src="default/images/p_fin.gif" width="133" height="5"></td></tr>
				</table>
			</td></tr>
			<tr><td><img src="default/images/25.gif" width=150 height=52></td></tr>
		</table>
		<br><br>
		<!-- References -->
		<table bgcolor="#CF9D15" border=0 cellpadding=0 cellspacing=0 align="center">
			<tr><td bgcolor="white">
				<a href="http://www.siapad.net" target="_blank"><img src="default/images/banner_SIAPAD.jpg" border="0"></a><br><br>
				<a href="http://www.redbivapad.org.pe/" target="_blank"><img src="default/images/banner_BIVAPAD.jpg" border="0"></a><br><br>
				<a href="http://www.desaprender.org/" target="_blank"><img src="default/images/banner_DESAPRENDER.gif" border="0"></a>
			</td></tr>
		</table>
	 </td>
	 <td>
 <!--  <span style="position:relative; top: 253px; left: 180px; width:50px; height:10px;">
  	<img src="images/b_desconsultar1.jpg"></span>-->
	<img src="default/images/subreg_can.jpg" alt="" usemap="#srcan" style="border-style:none" />
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
	<p align="right">
	<img src="default/images/can.jpg" border=0>
	</p>
	 </td>
	</tr>
</table>
