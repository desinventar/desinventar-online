<div id="map-win" class="x-hidden">
	<span id="msgViewMapButtonClear" style="display:none;">{-#tclear#-}</span>
	<span id="msgViewMapButtonSend"  style="display:none;">{-#tsend#-}</span>
	<span id="msgViewMapButtonClose" style="display:none;">{-#tclose#-}</span>
	<div class="x-window-header">{-#bthematic#-}</div>
	<div id="map-cfg">
		<div id="colorpicker201" class="colorpicker201"></div>
		<form id="CM" method="POST">
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
												<a class="button" onclick="addRowToTable();"><span>+</span></a>
												<a class="button" onclick="removeRowFromTable();"><span>-</span></a>
											</td>
										</tr>
										<tr>
											<td colspan="2">
												<input type="hidden" id="txtRangeLabel" value="{-#mbetween#-}" />
												<table border="0" id="tbl_range" class="grid">
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
												<table border="0" width="100%">
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
						<select id="_M+Type" name="_M+Type" size="3" class="fixw line">
							{-foreach name=mgel key=k item=i from=$mgel-}
								<option value="{-$k-}|D.GeographyId|" {-if $smarty.foreach.mgel.iteration==1-}selected{-/if-}>{-$i[0]-}</option>
							{-/foreach-}
						</select>
						<br /><br />
						<b>{-#mviewfields#-}</b><br />
						<select id="_M+Field" name="_M+Field" size="8" class="fixw line">
							<option value="D.DisasterId||" selected>{-#trepnum#-}</option>
							{-foreach name=ef1 key=k item=i from=$ef1-}
								<option value="D.{-$k-}Q|>|-1">{-$i[0]-}</option>
								<option value="D.{-$k-}|=|-1">{-#tauxhave#-} {-$i[0]-}</option>
							{-/foreach-}
							{-foreach name=ef2 key=k item=i from=$ef2-}
								<option value="D.{-$k-}|>|-1">{-$i[0]-}</option>
							{-/foreach-}
							{-foreach name=ef3 key=k item=i from=$ef3-}
								<option value="D.{-$k-}|>|-1">{-$i[0]-}</option>
							{-/foreach-}
							{-foreach name=ef3 key=k item=i from=$sec-}
								<option value="D.{-$k-}|=|-1">{-#tauxaffect#-} {-$i[0]-}</option>
							{-/foreach-}
							<option disabled>---</option>
							{-foreach name=eef key=k item=i from=$EEFieldList-}
								{-if $i[2] == "INTEGER" || $i[2] == "DOUBLE"-}
									<option value="E.{-$k-}|>|-1">{-$i[0]-}</option>
								{-/if-}
							{-/foreach-}
						</select>
						<input type="hidden" id="_M+cmd" name="_M+cmd" value="result" />
						<input type="hidden" id="_M+extent" name="_M+extent" />
						<input type="hidden" id="_M+layers" name="_M+layers" />
						<input type="hidden" id="_M+title"  name="_M+title" />
					</td>
				</tr>
			</table> <!-- table class=conf -->
		</form>
	</div>
</div>
