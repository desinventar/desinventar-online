<table bgcolor="#CF9D15" border=0 cellpadding=0 cellspacing=0>
	<tr>
		<td>
			<table width="133" border=0 cellpadding=0 cellspacing=0 align=center>

			<tr>
				<td colspan="3" width="100%">
					<div id="divUserIsLoggedOut">
						<a href="#" id="linkShowUserLogin" class="menuLink">Acceso a Usuarios</a>
						{-include file="../../web/templates/user_login.tpl" confdir="../../web/conf/"-}						
					</div>
					<div id="divUserIsLoggedIn">
						<a href="#" id="linkUserRegionList" class="menuLink">{-#msgRegionList#-}</a><br />
						<a href="#" id="linkUserLogout" class="menuLink">Logout</a>
					</div>
				</td>
			</tr>
			<tr><td colspan="3"><img src="images/c_sep.gif" width=133 height=6 border=0><br /><br /></td></tr>
			
			<!-- Region List -->
			<tr>
				<td><img src="images/p_paises1.gif" width=5 height=16></td>
				<td width="32px" bgcolor="#fcc700">{-#tcountries#-}</td>
				<td><img src="images/p_paises3.gif" width=73 height=16></td>
			</tr>
			<tr><td colspan="3"><img src="images/p_ini.gif" width="133" height="5"></td></tr>
			<!-- ANDEAN SUBREGION -->
			<tr>
				<td class="paisel" colspan="3">
					<a href="#" class="RegionGroup" alt="6">{-#di_andeansubregion#-}</a>
				</td>
			</tr>
			<tr><td colspan="3"><img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
			</table>
			<table width="133" border=0 cellpadding=0 cellspacing=0 align=center id="sect6">
			<tr>
				<td class="pais">&nbsp;&nbsp;&nbsp; >
					<a href="#" class="RegionList" alt="BOL">{-#tcountryBOL#-}</a>
				</td>
			</tr>
			<tr><td><img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
			<tr>
				<td class="pais">&nbsp;&nbsp;&nbsp; >
					<a href="#" class="RegionList" alt="COL">{-#tcountryCOL#-}</a>
				</td>
			</tr>
			<tr><td colspan="3"><img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
			<tr>
				<td class="pais">&nbsp;&nbsp;&nbsp; >
					<a href="#" class="RegionList" alt="ECU">{-#tcountryECU#-}</a>
				</td>
			</tr>
			<tr><td colspan="3"><img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
			<tr>
				<td class="pais" colspan="3">&nbsp;&nbsp;&nbsp; >
					<a href="#" class="RegionList" alt="PER">{-#tcountryPER#-}</a>
				</td>
			</tr>
			<tr><td colspan="3"><img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
			<tr>
				<td class="pais" colspan="3">&nbsp;&nbsp;&nbsp; >
					<a href="#" class="RegionList" alt="VEN">Venezuela</a>
				</td>
			</tr>
			<tr><td colspan="3"><img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
			</table>
			
			<!-- ASIA-->
			<table width="133" border=0 cellpadding=0 cellspacing=0 align=center>
			<tr>
				<td class="paisel">
					<a href="#" class="RegionGroup" alt="1">{-#treg1#-}</a>
				</td>
			</tr>
			<tr><td><img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
			</table>
			<table width="133" border=0 cellpadding=0 cellspacing=0 align=center id="sect1" style="display:none;">
			<tr>
				<td class="pais" colspan="3">&nbsp;&nbsp;&nbsp; >
					<a href="#" class="RegionItem" alt="IND-1250695040-india_orissa_historic_inventory_of_disasters">India - Orissa</a>
				</td>
			</tr>
			<tr><td colspan="3"><img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
			</table>
			
			<!-- CENTRAL AMERICA -->
			<table width="133" border=0 cellpadding=0 cellspacing=0 align=center>
			<tr>
				<td class="paisel">
					<a href="#" class="RegionGroup" alt="2">{-#treg2#-}</a>
				</td>
			</tr><tr><td><img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
			</table>
			<table width="133" border=0 cellpadding=0 cellspacing=0 align=center id="sect2" style="display:none;">
			<tr>
				<td class="pais" colspan="3">&nbsp;&nbsp;&nbsp; >
					<a href="#" class="RegionItem" alt="CRI-1250694968-costa_rica_inventario_historico_de_desastres">Costa Rica</a>
				</td>
			</tr>
			<tr><td colspan="3"><img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
			<tr>
				<td class="pais" colspan="3">&nbsp;&nbsp;&nbsp; >
					<a href="#" class="RegionList" alt="SLV">El Salvador</a>
				</td>
			</tr>
			<tr><td colspan="3"><img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
			<tr>
				<td class="pais" colspan="3">&nbsp;&nbsp;&nbsp; >
					<a href="#" class="RegionList" alt="GTM">Guatemala</a>
				</td>
			</tr>
			<tr><td colspan="3"><img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
			<tr>
				<td class="pais" colspan="3">&nbsp;&nbsp;&nbsp; >
					<a href="#" class="RegionItem" alt="PAN-1250695231-panama_inventario_de_desastres_sinaproc">Panamá</a>
				</td>
			</tr>
			<tr><td colspan="3"><img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
			</table>
			
			<!-- SOUTHERN CONE -->
			<table width="133" border=0 cellpadding=0 cellspacing=0 align=center>
			<tr>
				<td class="paisel">
					<a href="#" class="RegionGroup" alt="3">{-#treg3#-}</a>
				</td>
			</tr>
			<tr><td><img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
			</table>
			<table width="133" border=0 cellpadding=0 cellspacing=0 align=center id="sect3" style="display:none;">
			<tr>
				<td class="pais" colspan="3">&nbsp;&nbsp;&nbsp; >
					<a href="#" class="RegionList" alt="ARG">Argentina</a>
				</td>
			</tr>
			<tr><td colspan="3"><img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
			<tr>
				<td class="pais" colspan="3">&nbsp;&nbsp;&nbsp; >
					<a href="#" class="RegionItem" alt="CHL-1257983285-chile_inventario_historico_de_desastres">Chile</a>
				</td>
			</tr>
			<tr><td colspan="3"><img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
			<tr>
				<td class="pais" colspan="3">&nbsp;&nbsp;&nbsp; >
					<a href="#" class="RegionList" alt="PAR">Paraguay</a>
				</td>
			</tr>
			<tr><td colspan="3"><img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
			</table>
			
			<!-- GREAT AMERICAN CHACO -->
			<!-- 2009-09-28 (jhcaiced) Removed Big Chaco databases from menu -->
			<!--
			<table width="133" border=0 cellpadding=0 cellspacing=0 align=center>
			<tr>
				<td class="paisel">
					<a href="#" class="RegionGroup" alt="4">{-#treg4#-}</a>
				</td>
			</tr>
			<tr><td><img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
			</table>
			<table width="133" border=0 cellpadding=0 cellspacing=0 align=center id="sect4" style="display:none;">
			<tr>
				<td class="pais" colspan="3">&nbsp;&nbsp;&nbsp; >
					<a href="#" class="RegionItem" alt="ARG-1250695025-argentina_gran_chaco">Argentina</a>
				</td>
			</tr>
			<tr><td colspan="3"><img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
			<tr>
				<td class="pais" colspan="3">&nbsp;&nbsp;&nbsp; >
					<a href="#" class="RegionItem" alt="BOL-1250695036-bolivia_gran_chaco">Bolivia</a>
				</td>
			</tr>
			<tr><td colspan="3"><img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
			<tr>
				<td class="pais" colspan="3">&nbsp;&nbsp;&nbsp; >
					<a href="#" class="RegionItem" alt="PAR-1250695038-paraguay_gran_chaco">Paraguay</a>
				</td>
			</tr>
			<tr>
				<td colspan="3"><img src="images/p_sep.gif" width=133 height=6 border=0>
			</td>
			</tr>
			</table>
			-->
			<!-- NORTH AMERICA -->
			<table width="133" border=0 cellpadding=0 cellspacing=0 align=center>
			<tr>
				<td class="paisel">
					<a href="#" class="RegionGroup" alt="5">{-#treg5#-}</a>
				</td>
			</tr>
			<tr><td><img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
			</table>
			<table width="133" border=0 cellpadding=0 cellspacing=0 align=center id="sect5" style="display:none;">
			<tr>
				<td class="pais" colspan="3">&nbsp;&nbsp;&nbsp; >
					<a href="#" class="RegionItem" alt="MEX-1250695136-mexico_inventario_historico_de_desastres">México</a>
				</td>
			</tr>
			<tr><td colspan="3"><img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
			</table>
			
			<!-- CITIES -->
			<table width="133" border=0 cellpadding=0 cellspacing=0 align=center>
			<tr>
				<td class="paisel">
					<a href="#" class="RegionGroup" alt="7">{-#treg7#-}</a>
				</td>
			</tr>
			</table>
			<table width="133" border=0 cellpadding=0 cellspacing=0 align=center id="sect7" style="display:none;">
			<tr>
				<td class="pais" colspan="3">&nbsp;&nbsp;&nbsp; >
					<a href="#" class="RegionItem" alt="COL-1263347185-armenia_inventario_historico">Armenia (COL)</a>
				</td>
			</tr>
			<tr>
				<td class="pais" colspan="3">&nbsp;&nbsp;&nbsp; >
					<a href="#" class="RegionItem" alt="COL-1250694494-colombia_inventario_desastres_cali_zona_urbana">Cali (COL)</a>
				</td>
			</tr>
			</table>
			<table width="133" border=0 cellpadding=0 cellspacing=0 align=center>
			<tr><td colspan="3"><img src="images/p_fin.gif" width="133" height="5"></td></tr>
			</table>
			<br />
			
			<!-- Virtual Regions -->
			<table width="133" border=0 cellpadding=0 cellspacing=0 align=center>
			<tr>
				<td><img src="images/p_paises1.gif" width=5 height=16></td>
				<td width="103px" bgcolor="#fcc700">{-#tvirtualreg#-}</td>
				<td><img src="images/p_paises3.gif" width=25 height=16></td>
			</tr>
			<tr><td colspan="3"><img src="images/p_ini.gif" width="133" height="5"></td></tr>
			<tr>
				<td class="pais" colspan="3">
					<a href="#" class="RegionItem" alt="DESINV-1249040429-can_subregion_andina">{-#treg6#-}</a>
				</td>
			</tr>
			<tr><td colspan="3"><img src="images/p_fin.gif" width="133" height="5"></td></tr>
			<tr><td colspan="3"><img src="images/p_ini.gif" width="133" height="5"></td></tr>
			<tr>
				<td class="pais" colspan="3">
					<a href="#" class="RegionItem" alt="DESINV-1249126759-subregion_gran_chaco">{-#treg8#-}</a>
				</td>
			</tr>
			<tr><td colspan="3"><img src="images/p_fin.gif" width="133" height="5"></td></tr>
			</table>
		</td>
	</tr>
	<tr><td><img src="images/25.gif" width=150 height=52></td></tr>
</table>
