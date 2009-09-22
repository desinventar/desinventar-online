<script type="text/javascript">
	// personalization List menu..
	function displayList(elem) {
		lst = 7;
		for (i=1; i <= lst; i++) {
			if (i == elem)
				$("#sect"+ i).show();
			else
				$("#sect"+ i).hide();
		}
	} //function
</script>

<table bgcolor="#CF9D15" border=0 cellpadding=0 cellspacing=0>
	<tr>
		<td>
			<!-- ANDEAN SUBREGION -->
			<table width="133" border=0 cellpadding=0 cellspacing=0 align=center>
			<tr>
				<td><img src="images/p_paises1.gif" width=5 height=16></td>
				<td width="32px" bgcolor="#fcc700">{-#di_databases#-}</td>
				<td><img src="images/p_paises3.gif" width=73 height=16></td>
			</tr>
			<tr><td colspan="3"><img src="images/p_ini.gif" width="133" height="5"></td></tr>
			<tr>
				<td class="paisel" colspan="3">
					<a href="javascript:void(null);" onClick="displayList('6');">{-#di_andeansubregion#-}</a>
				</td>
			</tr>
			<tr><td colspan="3"><img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
			</table>
			<table width="133" border=0 cellpadding=0 cellspacing=0 align=center id="sect6">
			<tr>
				<td class="pais">&nbsp;&nbsp;&nbsp; >
					<a href="javascript:void(null);" onclick="updateDatabaseList('BOL',true);">{-#country_BOL#-}</a>
				</td>
			</tr>
			<tr><td><img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
			<tr>
				<td class="pais">&nbsp;&nbsp;&nbsp; >
					<a href="javascript:void(null);" onclick="updateDatabaseList('COL',true);">{-#tcountryCOL#-}</a>
				</td>
			</tr>
			<tr><td colspan="3"><img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
			<tr>
				<td class="pais">&nbsp;&nbsp;&nbsp; >
					<a href="javascript:void(null);" onclick="updateDatabaseList('ECU',true);">{-#tcountryECU#-}</a>
				</td>
			</tr>
			<tr><td colspan="3"><img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
			<tr>
				<td class="pais" colspan="3">&nbsp;&nbsp;&nbsp; >
					<a href="javascript:void(null);" onclick="updateDatabaseList('PER', true);">Perú</a>
				</td>
			</tr>
			<tr><td colspan="3"><img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
			<tr>
				<td class="pais" colspan="3">&nbsp;&nbsp;&nbsp; >
					<a href="javascript:void(null);" onclick="updateDatabaseList('VEN',true);">Venezuela</a>
				</td>
			</tr>
			<tr><td colspan="3"><img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
			</table>
			
			<!-- ASIA-->
			<table width="133" border=0 cellpadding=0 cellspacing=0 align=center>
			<tr>
				<td class="paisel">
					<a href="javascript:void(null);" onClick="displayList(1);">{-#treg1#-}</a>
				</td>
			</tr>
			<tr><td><img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
			</table>
			<table width="133" border=0 cellpadding=0 cellspacing=0 align=center id="sect1" style="display:none;">
			<tr>
				<td class="pais" colspan="3">&nbsp;&nbsp;&nbsp; >
					<a href="javascript:void(null);" onclick="updateDatabaseList('IND');">India - Orissa</a>
				</td>
			</tr>
			<tr><td colspan="3"><img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
			</table>
			
			<!-- CENTRAL AMERICA -->
			<table width="133" border=0 cellpadding=0 cellspacing=0 align=center>
			<tr>
				<td class="paisel">
					<a href="javascript:void(null);" onClick="displayList(2);">{-#treg2#-}</a>
				</td>
			</tr><tr><td><img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
			</table>
			<table width="133" border=0 cellpadding=0 cellspacing=0 align=center id="sect2" style="display:none;">
			<tr>
				<td class="pais" colspan="3">&nbsp;&nbsp;&nbsp; >
					<a href="javascript:void(null);" onclick="updateDatabaseList('CRI');">Costa Rica</a>
				</td>
			</tr>
			<tr><td colspan="3"><img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
			<tr>
				<td class="pais" colspan="3">&nbsp;&nbsp;&nbsp; >
					<a href="javascript:void(null);" onclick="updateDatabaseList('SLV');">El Salvador</a>
				</td>
			</tr>
			<tr><td colspan="3"><img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
			<tr>
				<td class="pais" colspan="3">&nbsp;&nbsp;&nbsp; >
					<a href="javascript:void(null);" onclick="updateDatabaseList('PAN');">Panamá</a>
				</td>
			</tr>
			<tr><td colspan="3"><img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
			</table>
			
			<!-- SOUTHERN CONE -->
			<table width="133" border=0 cellpadding=0 cellspacing=0 align=center>
			<tr>
				<td class="paisel">
					<a href="javascript:void(null);" onClick="displayList(3);">{-#treg3#-}</a>
				</td>
			</tr>
			<tr><td><img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
			</table>
			<table width="133" border=0 cellpadding=0 cellspacing=0 align=center id="sect3" style="display:none;">
			<tr>
				<td class="pais" colspan="3">&nbsp;&nbsp;&nbsp; >
					<a href="javascript:void(null);" onclick="updateDatabaseList('ARG',true);">Argentina</a>
				</td>
			</tr>
			<tr><td colspan="3"><img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
			<tr>
				<td class="pais" colspan="3">&nbsp;&nbsp;&nbsp; >
					<a href="javascript:void(null);" onclick="updateDatabaseList('PAR');">Paraguay</a>
				</td>
			</tr>
			<tr><td colspan="3"><img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
			</table>
			
			<!-- GREAT AMERICAN CHACO -->
			<table width="133" border=0 cellpadding=0 cellspacing=0 align=center>
			<tr>
				<td class="paisel">
					<a href="javascript:void(null);" onClick="displayList(4);">{-#treg4#-}</a>
				</td>
			</tr>
			<tr><td><img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
			</table>
			<table width="133" border=0 cellpadding=0 cellspacing=0 align=center id="sect4" style="display:none;">
			<tr>
				<td class="pais" colspan="3">&nbsp;&nbsp;&nbsp; >
					<a href="javascript:void(null);" onclick="showDatabaseInfo('GCARG');">Argentina</a>
				</td>
			</tr>
			<tr><td colspan="3"><img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
			<tr>
				<td class="pais" colspan="3">&nbsp;&nbsp;&nbsp; >
					<a href="javascript:void(null);" onclick="showDatabaseInfo('GCBOL');">{-#tcountryBOL#-}</a>
				</td>
			</tr>
			<tr><td colspan="3"><img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
			<tr>
				<td class="pais" colspan="3">&nbsp;&nbsp;&nbsp; >
					<a href="javascript:void(null);" onclick="showDatabaseInfo('GCPAR');">Paraguay</a>
				</td>
			</tr>
			<tr>
				<td colspan="3"><img src="images/p_sep.gif" width=133 height=6 border=0>
			</td>
			</tr>
			</table>
			
			<!-- NORTH AMERICA -->
			<table width="133" border=0 cellpadding=0 cellspacing=0 align=center>
			<tr>
				<td class="paisel">
					<a href="javascript:void(null);"  onClick="displayList(5);">{-#treg5#-}</a>
				</td>
			</tr>
			<tr><td><img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
			</table>
			<table width="133" border=0 cellpadding=0 cellspacing=0 align=center id="sect5" style="display:none;">
			<tr>
				<td class="pais" colspan="3">&nbsp;&nbsp;&nbsp; >
					<a href="javascript:void(null);" onclick="showDatabaseInfo('MEXICO');">México</a>
				</td>
			</tr>
			<tr><td colspan="3"><img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
			</table>
			
			<!-- CITIES -->
			<table width="133" border=0 cellpadding=0 cellspacing=0 align=center>
			<tr>
				<td class="paisel">
					<a href="javascript:void(null);" onClick="displayList(7);">{-#treg7#-}</a>
				</td>
			</tr>
			</table>
			<table width="133" border=0 cellpadding=0 cellspacing=0 align=center id="sect7" style="display:none;">
			<tr>
				<td class="pais" colspan="3">&nbsp;&nbsp;&nbsp; >
					<a href="javascript:void(null);" onclick="showDatabaseInfo('COLCALI');">Cali - {-#tcountryCOL#-}</a>
				</td>
			</tr>
			<tr><td colspan="3"><img src="images/p_sep.gif" width=133 height=6 border=0></td></tr>
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
					<a href="javascript:void(null)" onclick="showDatabaseInfo('CAN)">{-#treg6#-}</a>
				</td>
			</tr>
			<tr><td colspan="3"><img src="images/p_fin.gif" width="133" height="5"></td></tr>
			<tr><td colspan="3"><img src="images/p_ini.gif" width="133" height="5"></td></tr>
			<tr>
				<td class="pais" colspan="3">
					<a href="javascript:void(null)" onclick="showDatabaseInfo('GRANCHACO')">{-#treg8#-}</a>
				</td>
			</tr>
			<tr><td colspan="3"><img src="images/p_fin.gif" width="133" height="5"></td></tr>
			</table>
		</td>
	</tr>
	<tr><td><img src="images/25.gif" width=150 height=52></td></tr>
</table>

