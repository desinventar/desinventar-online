{-config_load file="$lg.conf" section="grpDatacard"-}
<div id="divDatacard" class="Datacard mainblock">
	<table class="width100">
		<tr>
			<td class="top left headerLeft">
				<input type="button" id="btnDatacardNew"    class="DatacardCmdButton bb bnew"    title="{-#tnewtitle#-}: {-#tnewdesc#-}" value=" "    />
				<input type="button" id="btnDatacardEdit"   class="DatacardCmdButton bb bupd"    title="{-#tupdtitle#-}: {-#tupddesc#-}" value=" "   />
				<input type="button" id="btnDatacardSave"   class="DatacardCmdButton bb bsave"   title="{-#tsavtitle#-}: {-#tsavdesc#-}" value=" "   />
				<input type="button" id="btnDatacardCancel" class="DatacardCmdButton bb bcancel" title="{-#tcantitle#-}: {-#tcandesc#-}" value=" " />
				<span  class="DatacardCmdButton"> &nbsp;&nbsp;|&nbsp;&nbsp;</span>
				<input type="button" id="btnDatacardPrint"  class="DatacardCmdButton bb bprint"  title="{-#mprint#-}" value=" " />
				&nbsp;&nbsp;|&nbsp;&nbsp;
				<input type="button" id="btnDatacardGotoFirst" class="DatacardNavButton bb line" value="<<" title="{-#bfirst#-}" />
				<input type="button" id="btnDatacardGotoPrev"  class="DatacardNavButton bb line" value="<"  title="{-#bprev#-}"  />
				<input type="button" id="btnDatacardGotoNext"  class="DatacardNavButton bb line" value=">"  title="{-#bnext#-}"  />
				<input type="button" id="btnDatacardGotoLast"  class="DatacardNavButton bb line" value=">>" title="{-#blast#-}"  />
				&nbsp;&nbsp;|&nbsp;&nbsp;
				{-#msgDatacardSerialFind#-}
				<input type="text"   id="txtDatacardFind" class="DatacardCmdFind line" style="width:60px;" />
				<input type="button" id="btnDatacardFind" class="DatacardCmdFind bb bfind" title="{-#tooltipDatacardFind#-}" value=" " />
			</td>
			<td class="top right headerRight" colspan="2">
				<div id="divDatacardStatusMsg" class="status" style="display:none;">
					<span class="datacardStatusMsg" id="msgDatacardDuplicatedSerial">{-#msgDatacardDuplicatedSerial#-}</span>
					<span class="datacardStatusMsg" id="msgDatacardStartNew">{-#tmsgnewcard#-}</span>
					<span class="datacardStatusMsg" id="msgDatacardFill">{-#tmsgnewcardfill#-}</span>
					<span class="datacardStatusMsg" id="msgDatacardIsLocked">{-#tdconuse#-}</span>
					<span class="datacardStatusMsg" id="msgDatacardFieldsError">{-#errmsgfrm#-}</span>
					<span class="datacardStatusMsg" id="msgDatacardFound">{-#msgDatacardFound#-}</span>
					<span class="datacardStatusMsg" id="msgDatacardNotFound">{-#msgDatacardNotFound#-}</span>
					<span class="datacardStatusMsg" id="msgDatacardInvalidIntegerNumber">{-#msgDatacardInvalidIntegerNumber#-}</span>
					<span class="datacardStatusMsg" id="msgDatacardInvalidFloatNumber">{-#msgDatacardInvalidFloatNumber#-}</span>
					<span class="datacardStatusMsg" id="msgDatacardWithoutStatus">{-#msgDatacardWithoutStatus#-}</span>
					<span class="datacardStatusMsg" id="msgDatacardInvalidStatus">{-#msgDatacardInvalidStatus#-}</span>
					<span class="datacardStatusMsg" id="msgDatacardWithoutSource">{-#msgDatacardWithoutSource#-}</span>
					<span class="datacardStatusMsg" id="msgDatacardInvalidGeography">{-#msgDatacardInvalidGeography#-}</span>
					<span class="datacardStatusMsg" id="msgDatacardWithoutEffects">{-#msgDatacardWithoutEffects#-}</span>
					<span class="datacardStatusMsg" id="msgDatacardOutsideOfPeriod">{-#msgDatacardOutsideOfPeriod#-}</span>
					<span class="datacardStatusMsg" id="msgDatacardNetworkError">{-#msgDatacardNetworkError#-}</span>
					<span class="datacardStatusMsg" id="msgDatacardInsertOk">{-#tdccreated#-}</span>
					<span class="datacardStatusMsg" id="msgDatacardUpdateOk">{-#tdcupdated#-}</span>
					<span class="datacardStatusMsg" id="msgDatacardCustom"></span>
				</div>
				<br />
			</td>
		</tr>
		<tr>
			<td class="left">
				<span class="dlgmsg" id="distatusmsg"></span>
				<span class="dlgmsg" id="dostat"></span>
			</td>
			<td class="top left">
				<div id="divRecordStat" style="display:none;">
					{-#tstatpublished#-} <span id="RecordPublished"></span>, {-#tstatready#-} <span id="RecordReady"></span><br />
				</div>
			</td>
			<td class="top right">
				<div id="divRecordNavigationInfo" style="display:none;">
					<span id="RecordNumber"></span>/<span id="RecordCount"></span><br />
				</div>
			</td>
		</tr>
	</table>
	<form id="DICard" action="cards.php" method="post" target="dic">
		<input type="hidden" id="DisasterId"         name="DisasterId" value="" />
		<input type="hidden" id="RecordAuthor"       name="RecordAuthor" value="" />
		<input type="hidden" id="RecordCreation"     name="RecordCreation" />
		<input type="hidden" id="PrevDisasterSerial" name="PrevDisasterSerial" value="" />
		<input type="hidden" id="DatacardCommand"    name="DatacardCommand" value="" />
		<input type="hidden" id="Status"             name="Status" value="" />
		{-counter assign="MyTabIndex" start="1"-}
		<table border="1" class="Datacard">
			<!-- DATACARD INFORMATION SECTION -->
			<tr>
				<td class="top columnLeft">
					&nbsp;
				</td>
				<td style="border-color:#000000;">
					<table class="grid">
						<tr class="top">
							<td title="{-$LabelsDisaster.DisasterBeginTime[1]-}">
								{-#msgDatacard_DisasterBeginTime#-}<b class="required">*</b><br />
								<input id="DisasterBeginTime0" name="DisasterBeginTime[0]" style="width:36px;" type="text"
									class="line inputInteger" tabindex="{-$MyTabIndex-}" maxlength="4" onFocus="showtip('{-$LabelsDisaster.DisasterBeginTime[2]-}', '#d4baf6')" />
								{-counter-}
								<input id="DisasterBeginTime1" name="DisasterBeginTime[1]" style="width:18px;" type="text" 
									class="line inputInteger" tabindex="{-$MyTabIndex-}" maxlength="2" onFocus="showtip('{-$LabelsDisaster.DisasterBeginTime[2]-}', '#d4baf6')" />
								{-counter-}
								<input id="DisasterBeginTime2" name="DisasterBeginTime[2]" style="width:18px;" type="text"
									class="line inputInteger" tabindex="{-$MyTabIndex-}"  maxlength="2" onFocus="showtip('{-$LabelsDisaster.DisasterBeginTime[2]-}', '#d4baf6')" />
								{-counter-}
							</td>
							<td title="{-$LabelsDisaster.DisasterSource[1]-}" >
								{-#msgDatacard_DisasterSource#-}<b class="required">*</b><br />
								<input id="DisasterSource" name="DisasterSource" type="text" size="50"
									class="line inputText" tabindex="{-$MyTabIndex-}" onFocus="showtip('{-$LabelsDisaster.DisasterSource[2]-}', '#d4baf6')" />
								{-counter-}
							</td>
							<td>
								{-#msgDatacard_RecordStatus#-}<b class="required">*</b><br />
								<select name="RecordStatus" id="RecordStatus"  title="{-$LabelsRecord1.RecordStatus[1]-}"
									class="line RecordStatus" tabindex="{-$MyTabIndex-}" onFocus="showtip('{-$LabelsRecord1.RecordStatus[1]-}', '')">
									<option value=""></option>
									<option value="PUBLISHED">{-#tstatpublished#-}</option>
									<option value="READY"    >{-#tstatready#-}</option>
									<option value="DRAFT"    >{-#tstatdraft#-}</option>
									<option value="TRASH"    >{-#tstatrash#-}</option>
									<option value="DELETED"  >{-#tstatdeleted#-}</option>
								</select>
								{-counter-}
							</td>
							<td title="{-$LabelsDisaster.DisasterSerial[1]-}">
								{-#msgDatacard_DisasterSerial#-}<b class="required">*</b><br />
								<input id="DisasterSerial" name="DisasterSerial" type="text" size="15" 
									class="line inputAlphaNumber" tabindex="{-$MyTabIndex-}" maxlength="50" onFocus="showtip('{-$LabelsDisaster.DisasterSerial[2]-}', '#d4baf6')" />
								{-counter-}
								<a href="#" id="linkDatacardSuggestSerial"><img src="{-$desinventarURL-}/images/reload.jpg" alt="" />
								</a>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<!-- GEOGRAPHY SECTION -->
			<tr>
				<td class="top columnLeft">
					<span title="<b>{-#mgeography#-}</b><br />{-$dmg.MetGuidegeography[2]-}">
						<img src="{-$desinventarURL-}/images/di_geotag.png" alt="" />
					</span>
				</td>
				<td>
					<table class="grid">
						<tr class="top">
							<td >
								<table>
									<tr>
										<td class="top" title="{-$LabelsDisaster.DisasterGeographyId[1]-}">
											<b>{-#msgDatacard_DisasterGeographyId#-}</b><b class="required">*</b>
											<br />
											<input id="GeographyId" name="GeographyId" type="hidden" />
										</td>
										<td class="top" title="{-$LabelsDisaster.DisasterGeographyId[1]-}">
											<table class="tblGeography">
												<tr>
													<td>
														<span class="GeoLevelId"></span> - <span class="GeoLevelName"></span><br />
													</td>
													<td>
														<select class="GeoLevelSelect line" id="GeoLevel" tabindex="{-$MyTabIndex-}" disabled>
															<option></option>
														</select>
													</td>
												</tr>
											</table>
										</td>
									</tr>
								</table>
							</td>
							<td title="{-$LabelsDisaster.DisasterSiteNotes[1]-}">
								{-#msgDatacard_DisasterSiteNotes#-}<br />
								<textarea id="DisasterSiteNotes" name="DisasterSiteNotes" style="height: 40px;" cols="25" maxlength="512"
									class="inputText" tabindex="{-$MyTabIndex-}" 
									onFocus="showtip('{-$LabelsDisaster.DisasterSiteNotes[2]-}', '#d4baf6')"></textarea>
								{-counter-}
							</td>
							<td>
								<span title="{-#msgDatacard_InputDoubleTooltip#-}">
									{-#msgDatacard_DisasterLatitude#-}<br />
									<input id="DisasterLatitude" name="DisasterLatitude" type="text" size="10" maxlength="10" value="0.0"
										class="line inputDouble" tabindex="{-$MyTabIndex-}" onFocus="showtip('{-$LabelsDisaster.DisasterLatitude[2]-}', '#d4baf6')" />
								</span>
								{-counter-}
								<br />
								<span title="{-#msgDatacard_InputDoubleTooltip#-}">
									{-#msgDatacard_DisasterLongitude#-}
									<br />
									<input id="DisasterLongitude" name="DisasterLongitude" type="text" size="10" maxlength="10" value="0.0"
										class="line inputDouble" tabindex="{-$MyTabIndex-}" onFocus="showtip('{-$LabelsDisaster.DisasterLongitude[2]-}', '#d4baf6')" />
								</span>
								{-counter-}
								<br />
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<!-- EFFECTS SECTION--> 
			<tr>
				<td class="top columnLeft">
					<a href="#" id="linkDatacardShowEffectsBasic">
						<span title="<b>{-#tbaseffects#-}</b><br />{-$dmg.MetGuidedatacards[2]-}">
							<img id="efimg" src="{-$desinventarURL-}/images/di_efftag.png" alt="" />
						</span>
					</a>
					<br /><br />
					<a href="#" id="linkDatacardShowEffectsAditional">
						<span title="<b>{-#textraeffect#-}</b><br />{-$dmg.MetGuideextraeffects[2]-}">
							<img id="eeimg" src="{-$desinventarURL-}/images/di_eeftag.png" alt="" />
						</span>
					</a>
				</td>
				<td class="top">
					<div class="dwin" style="height:300px;">
						<div class="divDatacardEffects" id="divDatacardEffectsBasic">
						<!-- BEG BASIC EFFECTS -->
						<table class="grid">
							<tr class="top">
								<td>
									<b>{-#teffects#-}</b><br />
									<table class="EffectList EffectListPeople grid width100">
										{-foreach $ef1 as $key => $value-}
											<tr>
												<td class="right">
													<span class="label" title="{-$value[1]-}">{-$value[0]-}</span>
												</td>
												<td>
													<select class="value line clsEffectNumeric" id="{-$key-}" name="{-$key-}" data-helptext="{-$value[2]-}" style="width:120px;" >
														<option class="small" value="-1">{-#teffhav#-}</option>
														<option class="small" value="0" selected>{-#teffhavnot#-}</option>
														<option class="small" value="-2">{-#teffdontknow#-}</option>
													</select>
												</td>
											</tr>
										{-/foreach-}
									</table> 
								</td>
								<td>
									<table>
										<tr>
											<td colspan="2" class="top">
												<b>{-#tsectors#-}</b><br />
											</td>
										</tr>
										<tr>
											<td class="top">
												<!-- BEGIN Table Sectors -->
												<table class="EffectList EffectListSector grid width100">
													{-foreach $sec as $key => $value -}
														<tr>
															<td class="right">
																<span class="label" title="{-$value[1]-}">{-$value[0]-}</span>
															</td>
															<td>
																<select class="value line clsEffectSector" id="{-$key-}" name="{-$key-}" data-helptext="{-$value[2]-}" style="width:120px;">
																	<option class="small" value="-1">{-#teffhav#-}</option>
																	<option class="small" value="0" selected>{-#teffhavnot#-}</option>
																	<option class="small" value="-2">{-#teffdontknow#-}</option>
																</select>
															</td>
														</tr>
													{-/foreach-}
												</table>
											</td>
											<td class="top">
												<!-- BEGIN Table Effects over $$ -->
												<table class="EffectList EffectListLosses1 grid">
													{-foreach $ef2 as $key => $value -}
														<tr>
															<td class="top">
																<span class="label" title="{-#msgDatacard_InputDoubleTooltip#-}">{-$value[0]-}</span>
																<br />
																<input class="value line inputDouble" id="{-$key-}" name="{-$key-}" type="text" size="11" maxlength="15" value="0" data-helptext="{-$value[2]-}" />
															</td>
														</tr>
													{-/foreach-}
												</table>
											</td>
										</tr>
									</table>
								</td>
								<td class="top">
									<b>{-#tlosses#-}</b><br />
									<!-- BEGIN Table Effects over Affected -->
									<table class="EffectList EffectListLosses2 grid">
										<tr class="EffectLossesValueLocal">
											<td>
												<span class="label" title="{-#msgDatacard_InputDoubleTooltip#-}"></span>
												<br />
												<input class="value line inputDouble clsEffectDouble"  type="text" size="11" maxlength="15" value="0"
													id="EffectLossesValueLocal" name="EffectLossesValueLocal" />
											</td>
										</tr>
										<tr class="EffectLossesValueUSD">
											<td>
												<span class="label" title="{-#msgDatacard_InputDoubleTooltip#-}"></span>
												<br />
												<input class="value line inputDouble clsEffectDouble" type="text" size="11" maxlength="15" value="0"
													id="EffectLossesValueUSD" name="EffectLossesValueUSD" />
											</td>
										</tr>
									</table>
									<table class="EffectList EffectListOther grid">
										<tr class="EffectOtherLosses">
											<td>
												<span class="label" title=""></span>
												<br />
												<textarea class="value inputText"  maxlength="2048" cols="25" style="height:30px;"
													id="EffectOtherLosses" name="EffectOtherLosses"></textarea>
											</td>
										</tr>
										<tr class="EffectNotes">
											<td>
												<span class="label" title=""></span>
												<br />
												<textarea class="value inputText"  maxlength="2048" cols="25" style="height:70px;"
													id="EffectNotes" name="EffectNotes"></textarea>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
						</div>
						<!-- BEG EXTRA EFFECTS FIELDS -->
						<div class="divDatacardEffects dwin" id="divDatacardEffectsAdditional" style="display:none;">
							<table class="EffectListAdditional grid">
								<tr>
									<td>
										<div class="EffectAdditional" style="display:none;">
											<span class="label"></span>
											<br />
											<input class="value line inputText"    type="text" size="30" value="" />
											<input class="value line inputInteger" type="text" size="30" value="0" />
											<input class="value line inputDouble"  type="text" size="30" value="0" />
										</div>
									</td>
									<td>
									</td>
									<td>
									</td>
								</tr>
							</table>
						</div>
						<!-- END EXTRA EFFECTS FIELDS -->
					</div>
				</td>
			</tr>
			<!-- BEGIN EVENT SECTION -->
			<tr style="border:1px solid #ff0;">
				<td class="top columnLeft">
					<span title="<b>{-#mevents#-}</b><br />{-$dmg.MetGuideevents[2]-}">
						<img src="{-$desinventarURL-}/images/di_evetag.png" alt="" />
					</span>
				</td>
				<td>
					<table class="grid">
						<tr class="top">
							<td title="{-$LabelsEvent.EventName[1]-}">
								{-#msgDatacard_EventName#-}<b class="required">*</b><br />
								<select class="EventId line" id="EventId" name="EventId"  tabindex="{-$MyTabIndex-}">
									<option value=""></option>
								</select>
								{-counter-}
							</td>
							<td title="{-$LabelsEvent.EventMagnitude[1]-}">
								{-#msgDatacard_EventMagnitude#-}<br />
								<input id="EventMagnitude" name="EventMagnitude" type="text" size="5" 
									class="line inputText" tabindex="{-$MyTabIndex-}" onFocus="showtip('{-$LabelsEvent.EventMagnitude[2]-}', 'lightblue')" />
								{-counter-}
							</td>
							<td title="{-$LabelsEvent.EventDuration[1]-}">
								{-#msgDatacard_EventDuration#-}<br />
								<input id="EventDuration" name="EventDuration" type="text" size="5" maxlength="8"
									class="line inputInteger" tabindex="{-$MyTabIndex-}" onFocus="showtip('{-$LabelsEvent.EventDuration[2]-}', 'lightblue')" />
								{-counter-}
							</td>
							<td title="{-$LabelsEvent.EventNotes[1]-}">
								{-#msgDatacard_EventNotes#-}<br />
								<input type="text" id="EventNotes" name="EventNotes" style="width: 350px;" 
									class="line inputText" tabindex="{-$MyTabIndex-}" onFocus="showtip('{-$LabelsEvent.EventNotes[2]-}', 'lightblue')" />
								{-counter-}
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<!-- BEG CAUSE SECTION -->
			<tr style="border:1px solid #ffffc0;">
				<td class="top columnLeft">
					<span title="<b>{-#mcauses#-}</b><br />{-$dmg.MetGuidecauses[2]-}">
						<img src="{-$desinventarURL-}/images/di_cautag.png" alt="" />
					</span>
				</td>
				<td>
					<table class="grid">
						<tr>
							<td title="{-$LabelsCause.CauseName[1]-}">
								{-#msgDatacard_CauseName#-}<b class="required">*</b><br />
								<select class="CauseId line" id="CauseId" name="CauseId" tabindex="{-$MyTabIndex-}">
									<option value=""></option>
								</select>
								{-counter-}
							</td>
							<td title="{-$LabelsCause.CauseNotes[1]-}">
								{-#msgDatacard_CauseNotes#-}<br />
								<input type="text" id="CauseNotes" name="CauseNotes" style="width: 450px;" 
									class="line inputText" tabindex="{-$MyTabIndex-}" onFocus="showtip('{-$LabelsCause.CauseNotes[2]-}', '#ffffc0')" />
								{-counter-}
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</form>
	<div id="divDatacardParameter" style="display:none;">
		<input type="hidden" id="cardsRecordNumber"  value="0" />
		<input type="hidden" id="cardsRecordCount" value="0" />
		<input type="hidden" id="cardsRecordSource" value="" />
	</div>
</div>
