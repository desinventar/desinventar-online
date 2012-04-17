<div id="map-win" class="x-hidden">
	<span id="msgViewMapButtonClear" style="display:none;">{-#tclear#-}</span>
	<span id="msgViewMapButtonSend"  style="display:none;">{-#tsend#-}</span>
	<span id="msgViewMapButtonClose" style="display:none;">{-#tclose#-}</span>
	<div class="x-window-header">
		{-#msgViewMap#-}
	</div>
	<div id="map-cfg" class="ViewMapParams mainblock">
		<div id="colorpicker201" class="colorpicker201"></div>
		<form id="CM" method="post" action="">
			<table class="conf">
				<tr valign="top">
					<td>
						<table>
							<tr>
								<td>
									<b>{-#mareaid#-}</b><br />
									<select name="_M+Label" size="4" class="fixw line">
										<option value="NAME">{-#mareashownam#-}</option>
										<option value="CODE">{-#mareashowcod#-}</option>
										<option value="VALUE">{-#mareashowval#-}</option>
										<option value="NONE" selected>{-#mareanotshow#-}</option>
									</select>
									<br /><br />
								</td>
							</tr>
							<tr>
								<td valign="top">
									<table>
										<tr>
											<td valign="top">
												<b>{-#mranlegcol#-}</b>&nbsp; &nbsp; &nbsp; &nbsp;
											</td>
											<td valign="top">
												<!--
												<a class="button hidden" onclick="addRowToTable();"><span>+</span></a>
												<a class="button hidden" onclick="removeRowFromTable();"><span>-</span></a>
												-->
											</td>
										</tr>
										<tr>
											<td colspan="2">
												<input type="hidden" id="txtRangeLabel" value="{-#mbetween#-}" />
												<table id="tbl_range" class="grid">
													<thead>
														<tr>
															<th colspan=2>
																{-#mrange#-}
															</th>
															<th>
																{-#mlegend#-}
															</th>
															<th>
																{-#mcolor#-}
															</th>
														</tr>
													</thead>
													<tbody id="range">
													{-foreach name=rg key=k item=i from=$range-}
														<tr class="clsMapRangeRow">
															<td>
																{-$smarty.foreach.rg.iteration-}
															</td>
															<td>
																<input id="_M+limit[{-$smarty.foreach.rg.iteration-1-}]" 
																	   name="_M+limit[{-$smarty.foreach.rg.iteration-1-}]"
																	   type="text" class="line" size="5" value="{-$i[0]-}"
																	   onBlur="miv={-if $smarty.foreach.rg.iteration > 1-}parseInt($('_M+limit[{-$smarty.foreach.rg.iteration-2-}]').value)+1{-else-}1{-/if-}; $('_M+legend[{-$smarty.foreach.rg.iteration-1-}]').value='{-#mbetween#-} '+ miv +'- '+ this.value"/>
															</td>
															<td>
																<input id="_M+legend[{-$smarty.foreach.rg.iteration-1-}]" 
																	   name="_M+legend[{-$smarty.foreach.rg.iteration-1-}]"
																	   type="text" class="line" size="20" value="{-#mbetween#-} {-$i[1]-}" />
															</td>
															<td>
																<input id="_M+ic[{-$smarty.foreach.rg.iteration-1-}]"
																	   type="text" class="line" size="3" value="" style="background:#{-$i[2]-};"
																	   onclick="showColorGrid2('_M+color[{-$smarty.foreach.rg.iteration-1-}]','_M+ic[{-$smarty.foreach.rg.iteration-1-}]');" />
																<input type="hidden" id="_M+color[{-$smarty.foreach.rg.iteration-1-}]" name="_M+color[{-$smarty.foreach.rg.iteration-1-}]" value="{-$i[2]-}" />
															</td>
														</tr>
													{-/foreach-}
													</tbody>
												</table>
												<table width="100%">
													<tr>
														<td>{-#mcoltransp#-}
															<select name="_M+Transparency" class="line">
																<option value="10">10</option>
																<option value="20">20</option>
																<option value="30">30</option>
																<option value="40">40</option>
																<option value="50">50</option>
																<option value="60">60</option>
																<option value="70" selected>70</option>
																<option value="80">80</option>
																<option value="90">90</option>
																<option value="100">100</option>
															</select>%
														</td>
														<td align="right">
															<a class="button" onClick="genColors();"><span>{-#mcolorgrad#-}</span></a>
														</td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</td>
					<td>
						<b>{-#mrepreselev#-}</b><br />
						<select  class="Geolevel fixw line" id="_M+Type" name="_M+Type" size="3">
							<option></option>
						</select>
						<br /><br />
						<b>{-#mviewfields#-}</b>
						<span id="AuxHaveLabel" class="hidden">{-#tauxhave#-}</span>
						<span id="AuxAffectLabel" class="hidden">{-#tauxaffect#-}</span>
						<span id="RepNumLabel" class="hidden">{-#trepnum#-}</span>
						<br />
						<select class="Field fixw line" id="_M+Field" name="_M+Field" size="8">
							<option></option>
						</select>
						<input type="hidden" id="_M+cmd" name="_M+cmd" value="result" />
						<input type="hidden" id="_M+mapid"  name="_M+mapid" value="" />
						<input type="hidden" id="_M+extent" name="_M+extent" />
						<input type="hidden" id="_M+layers" name="_M+layers" />
						<input type="hidden" id="_M+title"  name="_M+title" />
						<input type="hidden" id="_M+legendtitle" name="_M+legendtitle" value="" />
					</td>
				</tr>
			</table> <!-- table class=conf -->
		</form>
	</div>
</div>
