						<div class="x-window-header">{-#bstatistic#-}</div>
						<div id="std-cfg">
							<form id="CS" method="POST">
								<table border="0" width="100%">
									<tr>
										<td>{-#sresxpage#-}
											<select id="_S+SQL_LIMIT" name="_S+SQL_LIMIT" class="line">
												<option value="20">20</option>
												<option value="50">50</option>
												<option value="100" selected>100</option>
												<option value="200">200</option>
											</select>
										</td>
										<td>{-#mgeosection#-}:
											<select id="_S+showgeo" name="_S+showgeo" class="line">
												<option value="NAME">{-#mareashownam#-}</option>
												<option value="CODE">{-#mareashowcod#-}</option>
												<option value="CODENAME">Code | Name</option>
											</select>
										</td>
									</tr>
								</table>
								<br />
								<b>{-#stotallevels#-}</b>
								<br />
								<table>
									<tr valign="top">
										<td><b>{-$std.StatisticFirstlev[0]-}</b><br />
											<select id="_S+Firstlev" name="_S+Firstlev" size="8" style="width:180px;" class="line" 
												onChange="setTotalize('_S+Firstlev', '_S+Secondlev'); setTotalize('_S+Secondlev', '_S+Thirdlev');">
												{-foreach name=glev key=k item=i from=$glev-}
													{-assign var="ln" value=StatisticGeographyId_$k-}
													<option value="{-$k-}|D.GeographyId" {-if $smarty.foreach.glev.first-}selected{-/if-}>{-$std.$ln[0]-}</option>
												{-/foreach-}
												<option value="|D.EventId">{-$std.StatisticEventName[0]-}</option>
												<option value="YEAR|D.DisasterBeginTime">{-$std.StatisticDisasterBeginTime_YEAR[0]-}</option>
												<option value="MONTH|D.DisasterBeginTime">{-$std.StatisticDisasterBeginTime_MONTH[0]-}</option>
												<option value="|D.CauseId">{-$std.StatisticCauseName[0]-}</option>
											</select>
										</td>
										<td><b>{-$std.StatisticSecondlev[0]-}</b><br/>
											<select id="_S+Secondlev" name="_S+Secondlev" size="8" style="width:180px;" class="line"
												onChange="setTotalize('_S+Secondlev', '_S+Thirdlev');">
											</select>
										</td>
										<td><b>{-$std.StatisticThirdlev[0]-}</b><br />
											<select id="_S+Thirdlev" name="_S+Thirdlev" size="8" style="width:180px;" class="line">
											</select>
										</td>
									</tr>
								</table>
								<br />
								<table>
									<tr>
										<td><b>{-#savailfields#-}</b><br>
											<select id="_S+sel1[]" size="6" style="width:220px;" multiple class="line">
												{-foreach name=ef1 key=k item=i from=$ef1-}
													<option value="D.{-$k-}|S|-1">{-#tauxhave#-} {-$i[0]-}</option>
												{-/foreach-}
												{-foreach name=ef2 key=k item=i from=$ef2-}
													<option value="D.{-$k-}|>|-1">{-$i[0]-}</option>
												{-/foreach-}
												{-foreach name=ef3 key=k item=i from=$ef3-}
													<option value="D.{-$k-}|>|-1">{-$i[0]-}</option>
												{-/foreach-}
												{-foreach name=ef3 key=k item=i from=$sec-}
													<option value="D.{-$k-}|S|-1">{-#tauxaffect#-} {-$i[0]-}</option>
												{-/foreach-}
												<option disabled>---</option>
												{-foreach name=eef key=k item=i from=$EEFieldList-}
													{-if $i[2] == "INTEGER" || $i[2] == "DOUBLE"-}
														<option value="E.{-$k-}|>|-1">{-$i[0]-}</option>
													{-/if-}
												{-/foreach-}
											</select>
											<br />
											<input type="button" value="{-#balls#-}" onclick="selectall('_S+sel1[]');" class="line" />
											<input type="button" value="{-#bnone#-}" onclick="selectnone('_S+sel1[]');" class="line" />
										</td>
										<td align="center" valign="middle" style="width:20px;">
											<input type="button" value="&rarr;" onclick="moveOptions($('_S+sel1[]'), $('_S+Field[]'));" class="line" />
											<br /><br /><br />
											<input type="button" value="&larr;" onclick="moveOptions($('_S+Field[]'), $('_S+sel1[]'));" class="line" />
										</td>
										<td><b>{-#sviewfields#-}</b><br>
											<select id="_S+Field[]" size="6" style="width:220px;" multiple class="line">
												{-foreach name=ef1 key=k item=i from=$ef1-}
													<option value="D.{-$k-}Q|>|-1">{-$i[0]-}</option>
												{-/foreach-}
											</select>
											<br />
											<input type="button" value="{-#balls#-}" onclick="selectall('_S+Field[]');" class="line" />
											<input type="button" value="{-#bnone#-}" onclick="selectnone('_S+Field[]');" class="line" />
										</td>
										<td style="width:20px;" align="center">
											<input type="button" value="&uArr;" onclick="top('_S+Field[]');" class="line" /><br />
											<input type="button" value="&uarr;" onclick="upone('_S+Field[]');" class="line" /><br />
											<input type="button" value="&darr;" onclick="downone('_S+Field[]');" class="line" /><br />
											<input type="button" value="&dArr;" onclick="bottom('_S+Field[]');" class="line" /><br />
										</td>
									</tr>
								</table>
								<input type="hidden" id="_S+FieldH" name="_S+Field" value="" />
								<input type="hidden" id="_S+cmd" name="_S+cmd" value="result" />
								<input type="hidden" id="_S+saveopt" name="_S+saveopt" value="" />
							</form>
						</div>
