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
						<a href="javascript:void(null);" onclick="updateList('pagecontent', 'region.php', 'r=IND_2009-07-06_212617');">India - Orissa</a>
					</td></tr>
					<tr><td colspan="3"><img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
<!--					<tr><td class="pais" colspan="3">&nbsp;&nbsp;&nbsp; >
						<a href="javascript:void(null);" onclick="updateList('pagecontent', 'region.php', 'r=IND_2009-07-06_211810');">India - Tamil Nadu</a>
					</td></tr>
					<tr><td colspan="3"><img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
					<tr><td class="pais" colspan="3">&nbsp;&nbsp;&nbsp; >
						<a href="javascript:void(null);" onclick="updateList('pagecontent', 'region.php', 'r=IRN_2009-07-06_212554');">Iran</a>
					</td></tr>
					<tr><td colspan="3"><img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
					<tr><td class="pais" colspan="3">&nbsp;&nbsp;&nbsp; >
						<a href="javascript:void(null);" onclick="updateList('pagecontent', 'region.php', 'r=NPL_2009-07-06_212745');">Nepal</a>
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
						<a href="javascript:void(null);" onclick="updateList('pagecontent', 'region.php', 'r=CRI_2009-07-06_212038');">Costa Rica</a>
					</td></tr>
					<tr><td colspan="3"><img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
					<tr><td class="pais" colspan="3">&nbsp;&nbsp;&nbsp; >
						<a href="javascript:void(null);" onclick="updateList('pagecontent', 'region.php', 'r=SLV_2009-07-06_211925');">El Salvador</a>
					</td></tr>
					<tr><td colspan="3"><img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
					<tr><td class="pais" colspan="3">&nbsp;&nbsp;&nbsp; >
						<a href="javascript:void(null);" onclick="updateList('pagecontent', 'region.php', 'r=PAN_2009-07-06_212740');">Panamá</a>
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
						<a href="javascript:void(null);" onclick="updateList('pagecontent', 'region.php', 'r=PAR_2009-07-06_211703');">Paraguay</a>
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
						<a href="javascript:void(null);" onclick="updateList('pagecontent', 'region.php', 'r=ARG_2009-07-06_211738');">Argentina</a>
					</td></tr>
					<tr><td colspan="3"><img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
					<tr><td class="pais" colspan="3">&nbsp;&nbsp;&nbsp; >
						<a href="javascript:void(null);" onclick="updateList('pagecontent', 'region.php', 'r=BOL_2009-07-06_212314');">Bolivia</a>
					</td></tr>
					<tr><td colspan="3"><img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
					<tr><td class="pais" colspan="3">&nbsp;&nbsp;&nbsp; >
						<a href="javascript:void(null);" onclick="updateList('pagecontent', 'region.php', 'r=PAR_2009-07-06_212813');">Paraguay</a>
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
						<a href="javascript:void(null);" onclick="updateList('pagecontent', 'region.php', 'r=MEX_2009-07-06_212055');">México</a>
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
						<a href="javascript:void(null);" onclick="updateList('pagecontent', 'region.php', 'r=COL_2009-07-06_211922');">Cali - Colombia</a>
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
						<a href="javascript:void(null)" onclick="updateList('pagecontent', 'region.php', 'r=DESINVENTAR')">{-#treg6#-}</a>
					</td></tr>
					<tr><td colspan="3"><img src="images/p_fin.gif" width="133" height="5"></td></tr>
					<tr><td colspan="3"><img src="images/p_ini.gif" width="133" height="5"></td></tr>
					<tr><td class="pais" colspan="3">
						<a href="javascript:void(null)" onclick="updateList('pagecontent', 'region.php', 'r=DESINVENTAR')">{-#treg8#-}</a>
					</td></tr>
					<tr><td colspan="3"><img src="images/p_fin.gif" width="133" height="5"></td></tr>
				</table>
			</td></tr>
			<tr><td><img src="images/25.gif" width=150 height=52></td></tr>
		</table>
