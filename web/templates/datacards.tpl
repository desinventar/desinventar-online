{-config_load file=`$lg`.conf section="di8_input"-}
	<!-- BEG DI8 FORM CARD -->
	<table width="900px" border="0" cellpadding="0" cellspacing="0" >
		<tr valign="top">
			<td align="left" width="450px">
				<input type="button" id="btnDatacardNew"    class="DatacardCmdButton bb bnew"    ext:qtip="{-#tnewtitle#-}: {-#tnewdesc#-}" />
				<input type="button" id="btnDatacardEdit"   class="DatacardCmdButton bb bupd"    ext:qtip="{-#tupdtitle#-}: {-#tupddesc#-}" />
				<input type="button" id="btnDatacardSave"   class="DatacardCmdButton bb bsave"   ext:qtip="{-#tsavtitle#-}: {-#tsavdesc#-}" />
				<input type="button" id="btnDatacardCancel" class="DatacardCmdButton bb bcancel" ext:qtip="{-#tcantitle#-}: {-#tcandesc#-}" />
				&nbsp;&nbsp;|&nbsp;&nbsp;
				<input type="button" id="btnDatacardPrint"  class="DatacardCmdButton bb bprint"  ext:qtip="{-#mprint#-}" />
				&nbsp;&nbsp;|&nbsp;&nbsp;
				<input type="button" id="btnDatacardGotoFirst" class="DatacardNavButton bb line" value="<<" ext:qtip="{-#bfirst#-}" />
				<input type="button" id="btnDatacardGotoPrev"  class="DatacardNavButton bb line" value="<"  ext:qtip="{-#bprev#-}"  />
				<input type="button" id="btnDatacardGotoNext"  class="DatacardNavButton bb line" value=">"  ext:qtip="{-#bnext#-}"  />
				<input type="button" id="btnDatacardGotoLast"  class="DatacardNavButton bb line" value=">>" ext:qtip="{-#blast#-}"  />
				&nbsp;&nbsp;|&nbsp;&nbsp;
				{-$dis.DisasterSerial[0]-}
				<input type="text"   id="txtDatacardFind" class="DatacardCmdFind line" style="width:60px;" />
				<input type="button" id="btnDatacardFind" class="DatacardCmdFind bb bfind" ext:qtip="{-#texptitle#-}" />
			</td>
			<td align="right" width="450px" colspan="2">
				<div id="divDatacardStatusMsg" style="display:none;">
					<span class="datacardStatusMsg" id="msgDatacardDuplicatedSerial">{-#msgDuplicatedDisasterSerial#-}</span>
					<span class="datacardStatusMsg" id="msgDatacardStartNew">{-#tmsgnewcard#-}</span>
					<span class="datacardStatusMsg" id="msgDatacardFill">{-#tmsgnewcardfill#-}</span>
					<span class="datacardStatusMsg" id="msgDatacardIsLocked">{-#tdconuse#-}</span>
					<span class="datacardStatusMsg" id="msgDatacardFieldsError">{-#errmsgfrm#-}</span>
					<span class="datacardStatusMsg" id="msgDatacardFound">{-#msgDatacardFound#-}</span>
					<span class="datacardStatusMsg" id="msgDatacardNotFound">{-#msgDatacardNotFound#-}</span>
					<span class="datacardStatusMsg" id="msgDatacardInvalidStatus">{-#msgDatacardInvalidStatus#-}</span>
					<span class="datacardStatusMsg" id="msgDatacardInvalidGeography">{-#msgDatacardInvalidGeography#-}</span>
					<span class="datacardStatusMsg" id="msgDatacardInsertOk">{-#tdccreated#-}</span>
					<span class="datacardStatusMsg" id="msgDatacardUpdateOk">{-#tdcupdated#-}</span>
					<span class="datacardStatusMsg" id="msgDatacardStatus">{-$statusmsg-}</span>
				</div>
			</td>
		</tr>
		<tr>
			<td align="left" width="450px">
				<span class="dlgmsg" id="distatusmsg"></span>
				<span class="dlgmsg" id="dostat"></span>
			</td>
			<td align="left" valign="top" width="350px">
				<div id="divRecordStat" style="display:none;">
					{-#tstatpublished#-} <span id="RecordPublished"></span>, {-#tstatready#-} <span id="RecordReady"></span>
				</div>
			</td>
			<td align="right" valign="top" width="100px">
				<div id="divRecordNavigationInfo" style="display:none;">
					<span id="RecordNumber"></span>/<span id="RecordCount"></span>
				</div>
			</td>
		</tr>
	</table>
	<form id="DICard" action="cards.php" method="POST" target="dic">
		<input type="hidden" id="DisasterId"         name="DisasterId" value="" />
		<input type="hidden" id="RecordAuthor"       name="RecordAuthor" value="" />
		<input type="hidden" id="RecordCreation"     name="RecordCreation" />
		<input type="hidden" id="_CMD"               name="_CMD" value="" />
		<input type="hidden" id="PrevDisasterSerial" name="PrevDisasterSerial" value="" />
		<input type="hidden" id="DatacardCommand"    name="DatacardCommand" valu="" />
		<table border="1" cellspacing="8" width="900px">
			<!-- DATACARD INFORMATION SECTION -->
			<tr>
				<td width="30px" style="border:0px;" valign="top">
					&nbsp;
				</td>
				<td style="border-color:#000000;">
					<table class="grid">
						<tr valign="top">
							<td ext:qtip="{-$dis.DisasterBeginTime[1]-}" />
								{-$dis.DisasterBeginTime[0]-}<b style="color:darkred;">*</b><br />
								<input id="DisasterBeginTime0" name="DisasterBeginTime[0]" style="width:36px;" type="text"
									class="line inputInteger" tabindex="1" maxlength="4" onFocus="showtip('{-$dis.DisasterBeginTime[2]-}', '#d4baf6')" />
								<input id="DisasterBeginTime1" name="DisasterBeginTime[1]" style="width:18px;" type="text" 
									class="line inputInteger" tabindex="2" maxlength="2" onFocus="showtip('{-$dis.DisasterBeginTime[2]-}', '#d4baf6')" />
								<input id="DisasterBeginTime2" name="DisasterBeginTime[2]" style="width:18px;" type="text"
									class="line inputInteger" tabindex="3"  maxlength="2" onFocus="showtip('{-$dis.DisasterBeginTime[2]-}', '#d4baf6')" />
							</td>
							<td ext:qtip="{-$dis.DisasterSource[1]-}" />
								{-$dis.DisasterSource[0]-}<b style="color:darkred;">*</b><br />
								<input id="DisasterSource" name="DisasterSource" type="text" size="50"
									class="line inputText" tabindex="4" onFocus="showtip('{-$dis.DisasterSource[2]-}', '#d4baf6')" />
							</td>
							<td>
								{-#tstatus#-}<b style="color:darkred;">*</b><br />
								<select name="RecordStatus" id="RecordStatus"  ext:qtip="{-$rc1.RecordStatus[1]-}"
									class="line" tabindex="5" onFocus="showtip('{-$rc1.RecordStatus[1]-}', '')">
									<option value=""></option>
									<option value="PUBLISHED">{-#tstatpublished#-}</option>
									<option value="READY"    >{-#tstatready#-}</option>
									<option value="DRAFT"    >{-#tstatdraft#-}</option>
									<option value="TRASH"    >{-#tstatrash#-}</option>
									<option value="DELETED"  >{-#tstatdeleted#-}</option>
								</select>
							</td>
							<td ext:qtip="{-$dis.DisasterSerial[1]-}" />
								{-$dis.DisasterSerial[0]-}<b style="color:darkred;">*<br />
								<input id="DisasterSerial" name="DisasterSerial" type="text" size="15" 
									class="line inputAlphaNumber" tabindex="6" maxlength="50" onFocus="showtip('{-$dis.DisasterSerial[2]-}', '#d4baf6')" />
								<a href="#" id="linkDatacardSuggestSerial"><img src="images/reload.jpg" border="0" />
								</a>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<!-- GEOGRAPHY SECTION -->
			<tr>
				<td width="30px" style="border:0px;" valign="top">
					<img src="images/di_geotag.png" ext:qtip="<b>{-#mgeography#-}</b><br />{-$dmg.MetGuidegeography[2]-}" />
				</td>
				<td>
					<table class="grid">
						<tr valign="top">
							<td >
								<table>
									<tr>
										<td valign="top" ext:qtip="{-$dis.DisasterGeographyId[1]-}">
											{-$dis.DisasterGeographyId[0]-}<b style="color:darkred;">*</b>
											<br />
											<input id="GeographyId" name="GeographyId" type="hidden" size="25" />
											<div style="display:none;">
											<br />
											{-foreach key=key item=GeoLevel from=$GeoLevelList name=GeoLevelList-}
												<span class="GeographyItemInfo" id="GeographyItemId{-$GeoLevel.GeoLevelId-}">GeographyItem{-$GeoLevel.GeoLevelId-}</span>
												<span class="GeographyItemInfo" id="GeographyItemValue{-$GeoLevel.GeoLevelId-}">GeographyItem{-$GeoLevel.GeoLevelId-}</span><br />
											{-/foreach-}
											</div>
										</td>
										<td valign="top" ext:qtip="{-$dis.DisasterGeographyId[1]-}">
											<table>
											{-counter assign=MyIndex start=7-}
											{-foreach key=key item=GeoLevel from=$GeoLevelList name=GeoLevelList-}
												<tr>
													<td>
														{-$GeoLevel.GeoLevelId-} - {-$GeoLevel.GeoLevelName-}<br />
													</td>
													<td>
														<select id="GeoLevel{-$key-}" level="{-$key-}" tabindex="{-$MyIndex-}" autoComplete="true" style="width:180px; background-Color:#eee;" 
															class="GeoLevelSelect line" onFocus="showtip('{-$dis.GeographyId[2]-}', '#d4baf6')">
															<option></option>
															{-if $key == 0 -}
																{-foreach key=GeographyKey item=GeographyItem from=$GeoLevelItems -}
																		<option value="{-$GeographyItem.GeographyId-}">{-$GeographyItem.GeographyName-}</option>
																{-/foreach-}
															{-/if-}
														</select>
														{-counter-}
													</td>
												</tr>
											{-/foreach-}
											</table>
										</td>
									</tr>
								</table>
							</td>
							{-assign var="tabind" value="`$tabind+1`"-}
							<td ext:qtip="{-$dis.DisasterSiteNotes[1]-}" />
								{-$dis.DisasterSiteNotes[0]-}<br />
								<textarea id="DisasterSiteNotes" name="DisasterSiteNotes" style="height: 40px;" cols="25"
									class="inputText" tabindex="{-$tabind-}" 
									onFocus="showtip('{-$dis.DisasterSiteNotes[2]-}', '#d4baf6')"></textarea>
							</td>
							<td>
								{-assign var="tabind" value="`$tabind+1`"-}
								<span ext:qtip="{-$dis.DisasterLatitude[1]-}" />
								{-$dis.DisasterLatitude[0]-}<br />
								<input id="DisasterLatitude" name="DisasterLatitude" type="text" size="10" maxlength="10"
									class="line inputDouble" tabindex="{-$tabind-}" onFocus="showtip('{-$dis.DisasterLatitude[2]-}', '#d4baf6')" />
								</span>
								<br />
								{-assign var="tabind" value="`$tabind+1`"-}
								<span ext:qtip="{-$dis.DisasterLongitude[1]-}">
									{-$dis.DisasterLongitude[0]-}
									<br />
									<input id="DisasterLongitude" name="DisasterLongitude" type="text" size="10" maxlength="10" 
										class="line inputDouble" tabindex="{-$tabind-}" onFocus="showtip('{-$dis.DisasterLongitude[2]-}', '#d4baf6')" />
								</span>
								<br />
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<!-- EFFECTS SECTION--> 
			<tr>
				<td width="30px" valign="top" style="border:0px;">
					<a href="#" id="linkDatacardShowEffectsBasic">
						<img id="efimg" src="images/di_efftag.png" border=0
							ext:qtip="<b>{-#tbaseffects#-}</b><br />{-$dmg.MetGuidedatacards[2]-}" />
					</a>
					<br /><br />
					<a href="#" id="linkDatacardShowEffectsAditional">
						<img id="eeimg" src="images/di_eeftag.png" border=0 
							ext:qtip="<b>{-#textraeffect#-}</b><br />{-$dmg.MetGuideextraeffects[2]-}" />
					</a>
				</td>
				<td valign="top">
					<div class="divDatacardEffects" id="divDatacardEffectsBasic">
					<!-- BEG BASIC EFFECTS -->
					<table class="grid">
						<tr valign="top">
							<td>
								<b align="left">{-#teffects#-}</b><br />
								<table width="100%" class="grid">
									<!-- BEGIN Table Effects over People-->
									{-foreach name=ef1 key=key item=item from=$ef1-}
										{-assign var="tabind" value="`$tabind+1`"-}
										<tr>
											<td align="right">
												<span ext:qtip="{-$item[1]-}">{-$item[0]-}</span>
											</td>
											<td>
												<select id="{-$key-}" name="{-$key-}" 
													class="line clsEffectNumeric" tabindex="{-$tabind-}" style="width:120px;" onFocus="showtip('{-$item[2]-}', '#f1bd41');" >
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
								<b align="center">{-#tsectors#-}</b><br />
								<table width="100%" class="grid">
									<!-- BEGIN Table Sectors -->
									{-foreach name=sec key=key item=item from=$sec-}
										{-assign var="tabind" value="`$tabind+1`"-}
										<tr>
											<td align="right"><span ext:qtip="{-$item[1]-}">{-$item[0]-}</span>
											</td>
											<td>
												<select id="{-$key-}" name="{-$key-}" style="width:120px;" 
													class="line clsEffectSector" tabindex="{-$tabind-}" onFocus="showtip('{-$item[2]-}', '#f1bd41')">
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
								<br />
								<!-- BEGIN Table Effects over Affected -->
								{-foreach name=ef3 key=key item=item from=$ef3-}
									{-assign var="tabind" value="`$tabind+1`"-}
									<span ext:qtip="{-$item[1]-}">
										{-$item[0]-}<br />
										<input id="{-$key-}" name="{-$key-}" type="text" size="7" maxlength="10" altfield="{-$sc3[$key]-}"
											class="line inputDouble clsEffectDouble" tabindex="{-$tabind-}" value="0" onFocus="showtip('{-$item[2]-}', '#f1bd41')" />
									</span>
									<br />
								{-/foreach-}
							</td>
							<td valign="top">
								<b align="right">{-#tlosses#-}</b><br />
								<!-- BEGIN Table Effects over $$ -->
								{-foreach name=ef2 key=key item=item from=$ef2-}
									{-assign var="tabind" value="`$tabind+1`"-}
									<span ext:qtip="{-$item[1]-}">
										{-$item[0]-}<br />
										<input id="{-$key-}" name="{-$key-}" type="text" size="11" maxlength="15"
											class="line inputDouble" tabindex="{-$tabind-}" value="0" 
											onFocus="showtip('{-$item[2]-}', '#f1bd41');" />
									</span>
									<br />
								{-/foreach-}
								{-foreach name=ef4 key=key item=item from=$ef4-}
									{-assign var="tabind" value="`$tabind+1`"-}
									<span ext:qtip="{-$item[1]-}">
										{-$item[0]-}<br />
										<textarea id="{-$key-}" name="{-$key-}" cols="25" style="height: {-if $key=='EffectNotes'-}70{-else-}30{-/if-}px;"
											class="inputText" onFocus="showtip('{-$item[2]-}', '#f1bd41')" tabindex="{-$tabind-}"></textarea>
									</span>
									<br />
								{-/foreach-}
							</td>
						</tr>
					</table>
					</div>
					<!-- BEG EXTRA EFFECTS FIELDS -->
					<div class="divDatacardEffects" id="divDatacardEffectsAdditional" style="display:none;">
					<table class="grid">
						<tr>
							<td>
								<br />
							</td>
						</tr>
						{-assign var="tabeef" value="200"-}
						{-foreach name=eefl key=key item=item from=$EEFieldList-}
							{-assign var="tabeef" value="`$tabeef+1`"-}
							{-if ($smarty.foreach.eefl.iteration - 1) % 3 == 0-}
								<tr>
							{-/if-}
								<td ext:qtip="{-$item[1]-}">
									{-$item[0]-}<br />
									
									{-assign var="inputClass" value="inputText" -}
									{-if $item[2] == "INTEGER" -} 
										{-assign var="inputClass" value="inputInteger" -}
									{-/if-}
									{-if $item[2] == "CURRENCY" -} 
										{-assign var="inputClass" value="inputDouble" -}
									{-/if-}
									<input type="text" id="{-$key-}" name="{-$key-}" size="30"
										class="line {-$inputClass-}" tabindex="{-$tabeef-}"
										onFocus="showtip('{-$item[1]-}', '#f1bd41')" />
								</td>
							{-if ($smarty.foreach.eefl.iteration ) % 3 == 0-}
								</tr>
							{-/if-}
						{-/foreach-}
					</table>
					</div>
					<!-- END EXTRA EFFECTS FIELDS -->
				</td>
			</tr>
			<!-- BEGIN EVENT SECTION -->
			<tr style="border:1px solid #ff0;">
				<td width="30px" valign="top" style="border:0px;">
					<img src="images/di_evetag.png" 
					ext:qtip="<b>{-#mevents#-}</b><br />{-$dmg.MetGuideevents[2]-}" />
				</td>
				<td>
					<table class="grid">
						<tr valign="top">
							<td ext:qtip="{-$eve.EventName[1]-}">
								{-assign var="tabind" value="`$tabind+1`"-}
								{-$eve.EventName[0]-}<b style="color:darkred;">*</b><br />
								<select id="EventId" name="EventId" style='width: 180px;' tabindex="{-$tabind-}"
									class="line" onFocus="showtip('{-$eve.EventName[2]-}', 'lightblue')">
									<option value=""></option>
									{-foreach name=eln key=key item=item from=$EventList-}
										<option value="{-$key-}" onKeyPress="showtip('{-$item[1]-}', 'lightblue')" 
											onMouseOver="showtip('{-$item[1]-}', 'lightblue')">{-$item[0]-}</option>
									{-/foreach-}
								</select>
							</td>
							<td ext:qtip="{-$eve.EventMagnitude[1]-}">
								{-assign var="tabind" value="`$tabind+1`"-}
								{-$eve.EventMagnitude[0]-}<br />
								<input id="EventMagnitude" name="EventMagnitude" type="text" size="5" 
									class="line inputText" tabindex="{-$tabind-}" onFocus="showtip('{-$eve.EventMagnitude[2]-}', 'lightblue')" />
							</td>
							<td ext:qtip="{-$eve.EventDuration[1]-}">
								{-assign var="tabind" value="`$tabind+1`"-}
								{-$eve.EventDuration[0]-}<br />
								<input id="EventDuration" name="EventDuration" type="text" size="5" maxlength="8"
									class="line inputInteger" tabindex="{-$tabind-}" onFocus="showtip('{-$eve.EventDuration[2]-}', 'lightblue')" />
							</td>
							<td ext:qtip="{-$eve.EventNotes[1]-}">
								{-assign var="tabind" value="`$tabind+1`"-}
								{-$eve.EventNotes[0]-}<br />
								<input type="texto" id="EventNotes" name="EventNotes" style="width: 350px;" 
									class="line inputText" tabindex="{-$tabind-}" onFocus="showtip('{-$eve.EventNotes[2]-}', 'lightblue')" />
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<!-- BEG CAUSE SECTION -->
			<tr style="border:1px solid #ffffc0;">
				<td width="30px" valign="top" style="border:0px;">
					<img src="images/di_cautag.png" ext:qtip="<b>{-#mcauses#-}</b><br />{-$dmg.MetGuidecauses[2]-}" />
				</td>
				<td>
					<table class="grid">
						<tr>
							<td ext:qtip="{-$cau.CauseName[1]-}">
								{-assign var="tabind" value="`$tabind+1`"-}
								{-$cau.CauseName[0]-}<b style="color:darkred;">*</b><br />
								<select id="CauseId" name="CauseId" style='width: 180px;' class="line" 
									tabindex="{-$tabind-}" onFocus="showtip('{-$cau.CauseName[2]-}', '#ffffc0')">
									<option value=""></option>
									{-foreach name=cln key=key item=item from=$CauseList-}
										<option value="{-$key-}" onMouseOver="showtip('{-$item[1]-}', '#ffffc0')">{-$item[0]-}</option>
									{-/foreach-}
								</select>
							</td>
							<td ext:qtip="{-$cau.CauseNotes[1]-}">
								{-assign var="tabind" value="`$tabind+1`"-}
								{-$cau.CauseNotes[0]-}<br />
								<input type="text" id="CauseNotes" name="CauseNotes" style="width: 450px;" 
									class="line inputText" tabindex="{-$tabind-}" onFocus="showtip('{-$cau.CauseNotes[2]-}', '#ffffc0')" />
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</form>
	<!-- END DI8 FORM CARD -->
	<div id="divDatacardParameter" style="display:none;">
		<input type="hidden" id="cardsRecordNumber"  value="0" />
		<input type="hidden" id="cardsRecordCount" value="0" />
		<input type="hidden" id="cardsRecordSource" value="" />
	</div>
