		<script type="text/javascript" language="javascript">
		// personalization List menu..
		function displayList(elem) {
			lst = 7;
			for (i=1; i <= lst; i++) {
				if (i == elem)
					$("sect"+ i).style.display = 'block';
				else
					$("sect"+ i).style.display = 'none';
			}
		}
		</script>
		<table bgcolor="#CF9D15" border=0 cellpadding=0 cellspacing=0>
			<tr><td>
				<!-- ANDIAN SUBREGION -->
				<table width="133" border=0 cellpadding=0 cellspacing=0 align=center>
					<tr><td><img src="images/p_paises1.gif" width=5 height=16></td>
							<td width="32px" bgcolor="#fcc700">{-#tcountries#-}</td>
					<td><img src="images/p_paises3.gif" width=73 height=16></td></tr>
					<tr><td colspan="3">
						<img src="images/p_ini.gif" width="133" height="5"></td></tr>
					<tr><td class="paisel" colspan="3">
						<a href="javascript:void(null);" 
								onClick="displayList('6');">{-#treg6#-}</a></td></tr>
					<tr><td colspan="3">
						<img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
				</table>
				<table width="133" border=0 cellpadding=0 cellspacing=0 align=center id="sect6">
					<tr><td class="pais">&nbsp;&nbsp;&nbsp; >
						<a href="javascript:void(null);" onclick="updateList('pagecontent', 'region.php', 'c=BOL');">Bolivia</a>
					</td></tr>
					<tr><td><img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
					<tr><td class="pais">&nbsp;&nbsp;&nbsp; >
						<a href="javascript:void(null);" onclick="updateList('pagecontent', 'region.php', 'c=COL');">Colombia</a>
					</td></tr>
					<tr><td colspan="3"><img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
					<tr><td class="pais">&nbsp;&nbsp;&nbsp; >
						<a href="javascript:void(null);" onclick="updateList('pagecontent', 'region.php', 'c=ECU');">Ecuador</a>
					</td></tr>
					<tr><td colspan="3"><img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
					<tr><td class="pais" colspan="3">&nbsp;&nbsp;&nbsp; >
						<a href="javascript:void(null);" onclick="updateList('pagecontent', 'region.php', 'c=PER');">Perú</a>
					</td></tr>
					<tr><td colspan="3"><img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
					<tr><td class="pais" colspan="3">&nbsp;&nbsp;&nbsp; >
						<a href="javascript:void(null);" onclick="updateList('pagecontent', 'region.php', 'c=VEN');">Venezuela</a>
					</td></tr>
					<tr><td colspan="3"><img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
				</table>
				<!-- ASIAN-->
				<table width="133" border=0 cellpadding=0 cellspacing=0 align=center>
					<tr><td class="paisel">
						<a href="javascript:void(null);" 
								onClick="displayList(1);">{-#treg1#-}</a></td></tr>
					<tr><td>
						<img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
				</table>
				<table width="133" border=0 cellpadding=0 cellspacing=0 align=center id="sect1" style="display:none;">
					<tr><td class="pais" colspan="3">&nbsp;&nbsp;&nbsp; >
						<a href="javascript:void(null);" onclick="updateList('pagecontent', 'region.php', 'r=IND-1250695040-india_orissa_historic_inventory_of_disasters');">India - Orissa</a>
					</td></tr>
					<tr><td colspan="3"><img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
<!--					<tr><td class="pais" colspan="3">&nbsp;&nbsp;&nbsp; >
						<a href="javascript:void(null);" onclick="updateList('pagecontent', 'region.php', 'r=IND-1248830503-india_tamil_nadu_historic_inventory_of_disasters');">India - Tamil Nadu</a>
					</td></tr>
					<tr><td colspan="3"><img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
					<tr><td class="pais" colspan="3">&nbsp;&nbsp;&nbsp; >
						<a href="javascript:void(null);" onclick="updateList('pagecontent', 'region.php', 'r=IRN-1248830532-iran_historic_inventory_of_disasters');">Iran</a>
					</td></tr>
					<tr><td colspan="3"><img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
					<tr><td class="pais" colspan="3">&nbsp;&nbsp;&nbsp; >
						<a href="javascript:void(null);" onclick="updateList('pagecontent', 'region.php', 'r=NPL-1248830584-nepal_historic_inventory_of_disasters');">Nepal</a>
					</td></tr>
					<tr><td colspan="3"><img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>-->
				</table>
				<!-- CENTER AMERICA -->
				<table width="133" border=0 cellpadding=0 cellspacing=0 align=center>
					<tr><td class="paisel">
						<a href="javascript:void(null);" 
								onClick="displayList(2);">{-#treg2#-}</a></td></tr>
					<tr><td>
						<img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
				</table>
				<table width="133" border=0 cellpadding=0 cellspacing=0 align=center id="sect2" style="display:none;">
					<tr><td class="pais" colspan="3">&nbsp;&nbsp;&nbsp; >
						<a href="javascript:void(null);" onclick="updateList('pagecontent', 'region.php', 'r=CRI-1250694968-costa_rica_inventario_historico_de_desastres');">Costa Rica</a>
					</td></tr>
					<tr><td colspan="3"><img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
					<tr><td class="pais" colspan="3">&nbsp;&nbsp;&nbsp; >
						<a href="javascript:void(null);" onclick="updateList('pagecontent', 'region.php', 'r=SLV-1250695592-el_salvador_inventario_historico_de_desastres');">El Salvador</a>
					</td></tr>
					<tr><td colspan="3"><img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
					<tr><td class="pais" colspan="3">&nbsp;&nbsp;&nbsp; >
						<a href="javascript:void(null);" onclick="updateList('pagecontent', 'region.php', 'r=PAN-1250695231-panama_inventario_de_desastres_sinaproc');">Panamá</a>
					</td></tr>
					<tr><td colspan="3"><img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
				</table>
				<!-- SOUTH CONE -->
				<table width="133" border=0 cellpadding=0 cellspacing=0 align=center>
					<tr><td class="paisel">
						<a href="javascript:void(null);" 
								onClick="displayList(3);">{-#treg3#-}</a></td></tr>
					<tr><td>
						<img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
				</table>
				<table width="133" border=0 cellpadding=0 cellspacing=0 align=center id="sect3" style="display:none;">
					<tr><td class="pais" colspan="3">&nbsp;&nbsp;&nbsp; >
						<a href="javascript:void(null);" onclick="updateList('pagecontent', 'region.php', 'c=ARG');">Argentina</a>
					</td></tr>
					<tr><td colspan="3"><img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
					<tr><td class="pais" colspan="3">&nbsp;&nbsp;&nbsp; >
						<a href="javascript:void(null);" onclick="updateList('pagecontent', 'region.php', 'r=PAR-1250695238-paraguay_inventario_historico_de_desastres');">Paraguay</a>
					</td></tr>
					<tr><td colspan="3"><img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
				</table>
				<!-- BIG CHACO -->
				<table width="133" border=0 cellpadding=0 cellspacing=0 align=center>
					<tr><td class="paisel">
						<a href="javascript:void(null);"
								onClick="displayList(4);">{-#treg4#-}</a></td></tr>
					<tr><td>
						<img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
				</table>
				<table width="133" border=0 cellpadding=0 cellspacing=0 align=center id="sect4" style="display:none;">
					<tr><td class="pais" colspan="3">&nbsp;&nbsp;&nbsp; >
						<a href="javascript:void(null);" onclick="updateList('pagecontent', 'region.php', 'r=ARG-1250695025-argentina_gran_chaco');">Argentina</a>
					</td></tr>
					<tr><td colspan="3"><img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
					<tr><td class="pais" colspan="3">&nbsp;&nbsp;&nbsp; >
						<a href="javascript:void(null);" onclick="updateList('pagecontent', 'region.php', 'r=BOL-1250695036-bolivia_gran_chaco');">Bolivia</a>
					</td></tr>
					<tr><td colspan="3"><img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
					<tr><td class="pais" colspan="3">&nbsp;&nbsp;&nbsp; >
						<a href="javascript:void(null);" onclick="updateList('pagecontent', 'region.php', 'r=PAR-1250695038-paraguay_gran_chaco');">Paraguay</a>
					</td></tr>
					<tr><td colspan="3"><img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
				</table>
				<!-- NORTH AMERICA -->
				<table width="133" border=0 cellpadding=0 cellspacing=0 align=center>
					<tr><td class="paisel">
						<a href="javascript:void(null);" 
								onClick="displayList(5);">{-#treg5#-}</a></td></tr>
					<tr><td>
						<img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
				</table>
				<table width="133" border=0 cellpadding=0 cellspacing=0 align=center id="sect5" style="display:none;">
					<tr><td class="pais" colspan="3">&nbsp;&nbsp;&nbsp; >
						<a href="javascript:void(null);" onclick="updateList('pagecontent', 'region.php', 'r=MEX-1250695136-mexico_inventario_historico_de_desastres');">México</a>
					</td></tr>
					<tr><td colspan="3"><img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
				</table>
				<!-- CITIES -->
				<table width="133" border=0 cellpadding=0 cellspacing=0 align=center>
					<tr><td class="paisel">
						<a href="javascript:void(null);" 
								onClick="displayList(7);">{-#treg7#-}</a></td></tr>
				</table>
				<table width="133" border=0 cellpadding=0 cellspacing=0 align=center id="sect7" style="display:none;">
					<tr><td class="pais" colspan="3">&nbsp;&nbsp;&nbsp; >
						<a href="javascript:void(null);" onclick="updateList('pagecontent', 'region.php', 'r=COL-1250694494-colombia_inventario_desastres_cali_zona_urbana');">Cali - Colombia</a>
					</td></tr>
					<tr><td colspan="3"><img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
				</table>
				<table width="133" border=0 cellpadding=0 cellspacing=0 align=center>
					<tr><td colspan="3"><img src="images/p_fin.gif" width="133" height="5"></td></tr>
				</table>
				<br>
				<table width="133" border=0 cellpadding=0 cellspacing=0 align=center>
					<tr><td><img src="images/p_paises1.gif" width=5 height=16></td>
							<td width="103px" bgcolor="#fcc700">{-#tvirtualreg#-}</td>
							<td><img src="images/p_paises3.gif" width=25 height=16></td></tr>
					<tr><td colspan="3"><img src="images/p_ini.gif" width="133" height="5"></td></tr>
					<tr><td class="pais" colspan="3">
						<a href="javascript:void(null)" onclick="updateList('pagecontent', 'region.php', 'r=DESINV-1249040429-can_subregion_andina')">{-#treg6#-}</a>
					</td></tr>
					<tr><td colspan="3"><img src="images/p_fin.gif" width="133" height="5"></td></tr>
					<tr><td colspan="3"><img src="images/p_ini.gif" width="133" height="5"></td></tr>
					<tr><td class="pais" colspan="3">
						<a href="javascript:void(null)" onclick="updateList('pagecontent', 'region.php', 'r=DESINV-1249126759-subregion_gran_chaco')">{-#treg8#-}</a>
					</td></tr>
					<tr><td colspan="3"><img src="images/p_fin.gif" width="133" height="5"></td></tr>
				</table>
			</td></tr>
			<tr><td><img src="images/25.gif" width=150 height=52></td></tr>
		</table>
