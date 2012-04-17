<div class="QueryDesign mainblock">
<span id="msgQueryDesignTitle" class="hidden">{-#tsubtitle#-}</span>
<span id="msgQueryDesignTooltip" class="hidden">{-#thlpquery#-}</span>
<form class="QueryDesign" id="frmMainQuery" method="post" action="#" target="dcr">
	<input type="hidden" class="RegionId" id="_REG" name="_REG" value="" />
	<input type="hidden" class="MinYear"  id="prmQueryMinYear" name="prmQuery[ConstMinYear]" value="" />
	<input type="hidden" class="MaxYear"  id="prmQueryMaxYear" name="prmQuery[ConstMaxYear]" value="" />
	<input type="hidden" id="_CMD" name="_CMD" />
	<input type="hidden" id="prmQueryCommand"      name="prmQuery[Command]"      value="DEFAULT" />
	<dl class="accordion">
		<!-- BEGIN GEOGRAPHY SECTION -->
		<!-- Select from Map testing ... 'selectionmap.php' -->
		<dt>{-#mgeosection#-}</dt>
		<dd>
			<input type="hidden" name="QueryGeography[OP]" value="AND" />
			<div class="GeolevelsHeader">
				<table>
					<tr>
						<td style="display:none;">
							<span class="dlgmsg withHelpOver"></span> |
						</td>
					</tr>
				</table>
			</div>
			<div id="qgeolst" class="GeographyList dwin" style="height:280px;overflow:auto;">
				<ul id="tree-geotree" class="list mainlist checktree">
					<li class="item">
						<input class="checkbox" type="checkbox" name="D_GeographyId[]" value="" />
						<span class="label"></span>
						<ul class="list"></ul>
					</li>
				</ul>
			</div>
			<span class="fieldLabel withHelpOver" data-help="{-#msgDatacard_DisasterSiteNotes_Helptext#-}">{-#msgDatacard_DisasterSiteNotes#-}</span>
			<br/>
			<input type="hidden" name="D_DisasterSiteNotes[0]" />
			<textarea class="inputText withHelpFocus" id="DisasterSiteNotes" name="D_DisasterSiteNotes[1]" style="width:220px; height: 40px;"
				data-help="{-#msgDatacard_DisasterSiteNotes_Helptext#-}"></textarea>
		</dd>
		
		<!-- BEGIN EVENT SECTION -->
		<dt>{-#mevesection#-}</dt>
		<dd>
			<input type="hidden" name="QueryEvent[OP]" value="AND" />
			<span class="dlgmsg">{-#tcntclick#-}</span>
			<br />
			<select class="Event line" id="qevelst" name="D_EventId[]" multiple style="width:100%; height: 200px;">
			</select>
			<br /><br />
			<span class="fieldLabel withHelpOver" data-help="{-#msgDatacard_EventDuration_Helptext#-}">{-#msgDatacard_EventDuration#-}</span>
			<br />
			<input id="EventDuration" name="D_EventDuration" type="text" class="line fixw withHelpFocus"
				data-help="{-#msgDatacard_EventDuration_Helptext#-}" value="" />
			<br />
			<span class="fieldLabel withHelpOver" data-help="{-#msgDatacard_EventNotes_Helptext#-}">{-#msgDatacard_EventNotes#-}</span>
			<br />
			<textarea id="EventNotes" name="D_EventNotes[1]" style="width:250px; height:40px;"
				class="inputText withHelpFocus" data-help="{-#msgDatacard_EventNotes_Helptext#-}"></textarea>
		</dd>
			
		<!-- BEGIN CAUSE SECTION -->
		<dt>{-#mcausection#-}</dt>
		<dd>
			<input type="hidden" name="QueryCause[OP]" value="AND" />
			<span class="dlgmsg">{-#tcntclick#-}</span><br />
			<select class="Cause line" id="qcaulst" name="D_CauseId[]" multiple style="width: 250px; height: 200px;">
			</select>
			<br />
			<span class="fieldLabel withHelpOver" data-help="{-#msgDatacard_CauseNotes_Helptext#-}">{-#msgDatacard_CauseNotes#-}</span>
			<br />
			<textarea name="D_CauseNotes[1]" style="width:250px; height: 40px;"
				class="inputText withHelpFocus" data-help="{-#msgDatacard_CauseNotes_Helptext#-}"></textarea>
		</dd>
		
		<!-- BEGIN QUERY EFFECTS SECTION -->
		<dt>{-#meffsection#-}</dt>
		<dd>
			<p class="right">{-#msgOperator#-}
			<select name="QueryEffect[OP]" class="dlgmsg small line">
				<option class="small" value="AND">{-#tand#-}</option>
				<option class="small" value="OR" >{-#tor#-}</option>
			</select>
			</p>
			<b>{-#ttitegp#-}</b><br />
			<div class="EffectPeopleList dwin" style="height: 100px;">
				<table class="EffectPeopleList EffectList">
					<tr style="display:none;">
						<td class="top">
							<div class="EffectPeople" data-field="">
								<input class="checkbox" type="checkbox" value="" />
								<span class="label"></span>
								<span class="options hidden">
									<select class="operator small line" name="value[0]">
										<option class="small" value="-1">{-#teffhav#-}</option>
										<option class="small" value="0" >{-#teffhavnot#-}</option>
										<option class="small" value="-2">{-#teffdontknow#-}</option>
										<option class="small" value=">=">{-#teffmajor#-}</option>
										<option class="small" value="<=">{-#teffminor#-}</option>
										<option class="small" value="=" >{-#teffequal#-}</option>
										<option class="small" value="-3">{-#teffbetween#-}</option>
									</select>
									<span class="firstvalue">
										<input class="line" type="text" name="value[1]" size="3" value="" />
									</span>
									<span class="lastvalue">{-#tand#-}
										<input class="line" type="text" name="value[2]" size="3" value="" />
									</span>
								</span>
							</div>
						</td>
					</tr>
				</table>
			</div>
			<br />
			
			<!-- SECTORS -->
			<b>{-#ttiteis#-}</b><br />
			<div style="height: 80px;" class="dwin">
				<table class="EffectSectorList EffectList">
					<tr style="display:none;">
						<td class="top">
							<div class="EffectSector" data-field="">
								<input class="checkbox" type="checkbox" value="" />
								<span class="label"></span>
								<span class="options hidden">
									<select class="operator small line" name="value[0]">
										<option class="small" value="-1">{-#teffhav#-}</option>
										<option class="small" value="0" >{-#teffhavnot#-}</option>
										<option class="small" value="-2">{-#teffdontknow#-}</option>
									</select>
								</span>
							</div>
						</td>
					</tr>
				</table>
			</div>
			<br />
			
			<!-- Losses -->
			<b>{-#ttitloss#-}</b><br />
				<table class="EffectListLosses2 EffectList">
					<tr style="display:none;">
						<td class="top">
							<div class="EffectLosses2" data-field="">
								<input class="checkbox" type="checkbox" value="" />
								<span class="label"></span>
								<span class="options hidden">
									<select class="operator small line" name="value[0]">
										<option class="small" value=">=">{-#teffmajor#-}</option>
										<option class="small" value="<=">{-#teffminor#-}</option>
										<option class="small" value="=" >{-#teffequal#-}</option>
										<option class="small" value="-3">{-#teffbetween#-}</option>
									</select>
									<span class="firstvalue">
										<input class="line" type="text" name="value[1]" size="5" value="" />
									</span>
									<span class="lastvalue">{-#tand#-}
										<input class="line" type="text" name="value[2]" size="5" value="" />
									</span>
								</span>
							</div>
						</td>
					</tr>
				</table>
			<br />
			<span class="fieldLabel withHelpOver"  data-help="{-#msgDatacard_EffectOtherLosses_Helptext#-}" title="{-#msgDatacard_EffectOtherLosses_Tooltip#-}">{-#msgDatacard_EffectOtherLosses#-}</span>
			<br />
			<input class="fixw line withHelpFocus" data-help="{-#msgDatacard_EffectOtherLosses_Helptext#-}" type="text" id="EffectOtherLosses" name="D_EffectOtherLosses"  value="" />
			<br />
			<span class="fieldLabel withHelpOver"  data-help="{-#msgDatacard_EffectNotes_Helptext#-}" title="{-#msgDatacard_EffectNotes_Tooltip#-}">{-#msgDatacard_EffectNotes#-}</span>
			<br />
			<input class="fixw line withHelpFocus" data-help="{-#msgDatacard_EffectNotes_Helptext#-}" type="text" id="EffectNotes" name="D_EffectNotes"  value="" />
			<br />
		</dd>
		<!-- END QUERY EFFECTS SECTION -->
		
		<!-- Begin EEField Section -->
		<dt>{-#mextsection#-}</dt>
		<dd>
			<p class="right">{-#msgOperator#-}
			<select name="QueryEEField[OP]" class="dlgmsg small line">
				<option class="small" value="AND">{-#tand#-}</option>
				<option class="small" value="OR" >{-#tor#-}</option>
			</select>
			</p>
			<div style="height: 300px;" class="dwin">
				<table class="EffectAdditionalList EffectList">
					<tr style="display:none;">
						<td class="top">
							<div class="EffectAdditional" data-field="">
								<input class="type" type="hidden" name="value[Type]" value="" />
								<div class="Effect EffectNumeric">
									<input class="checkbox" type="checkbox" />
									<span class="label"></span>
									<span class="options hidden">
										<select class="operator small line" name="value[0]">
											<option class="small" value=""></option>
											<option class="small" value=">=">{-#teffmajor#-}</option>
											<option class="small" value="<=">{-#teffminor#-}</option>
											<option class="small" value="=">{-#teffequal#-}</option>
											<option class="small" value="-3">{-#teffbetween#-}</option>
										</select>
										<input type="hidden" name="value[Type]" value="" />
										<span class="firstvalue">
											<input class="line" type="text" name="value[1]" size="3" value="1" />
										</span>
										<span class="lastvalue">{-#tand#-}
											<input class="line" type="text" name="value[2]" size="3" value="10" />
										</span>
									</span>
								</div>
								<div class="Effect EffectText">
									<span class="label"></span><br />
									<input class="text line" type="text" name="value[Text]" style="width: 290px;" />
								</div>
							</div>
						</td>
					</tr>
				</table>
			</div>
		</dd>
		<!-- END EEField Section -->
		
		<!-- BEGIN DATETIME SECTION -->
		<dt class="QueryDatacard">{-#mdcsection#-}</dt>
		<dd class="default">
			<div style="height: 250px;">
				<span class="fieldLabel withHelpOver" data-help="{-#msgDatacard_DisasterBeginTime_Helptext#-}">{-#msgQueryDesign_DateRange#-}</span>
				<span class="dlgmsg withHelpOver" data-help="{-#msgDatacard_DisasterBeginTime_Helptext#-}">{-#msgQueryDesign_DateFormat#-}</span><br />
				<table>
					<tr>
						<td>
							<span class="fieldLabel withHelpOver" data-help="{-#msgDatacard_DisasterBeginTime_Helptext#-}">{-#ttitsince#-}:</span>
						</td>
						<td>
							<input class="line withHelpFocus queryBeginYear" data-help="{-#msgDatacard_DisasterBeginTime_Helptext#-}"
								type="text" id="queryBeginYear" name="D_DisasterBeginTime[]" size="4" maxlength="4" value="" />
							<input class="line withHelpFocus" data-help="{-#msgDatacard_DisasterBeginTime_Helptext#-}" 
								type="text" id="queryBeginMonth" name="D_DisasterBeginTime[]" size="2" maxlength="2" value="" />
							<input class="line withHelpFocus" data-help="{-#msgDatacard_DisasterBeginTime_Helptext#-}" 
								type="text" id="queryBeginDay" name="D_DisasterBeginTime[]" size="2" maxlength="2" value="" />
						</td>
					</tr>
					<tr>
						<td>
							<span class="fieldLabel withHelpOver" data-help="{-#msgDatacard_DisasterBeginTime_Helptext#-}">{-#ttituntil#-}:</span>
						</td>
						<td>
							<input class="line withHelpFocus queryEndYear" data-help="{-#msgDatacard_DisasterBeginTime_Helptext#-}" 
								type="text" id="queryEndYear" name="D_DisasterEndTime[]" size="4" maxlength="4" value="" />
							<input class="line withHelpFocus" data-help="{-#msgDatacard_DisasterBeginTime_Helptext#-}"
								type="text" id="queryEndMonth" name="D_DisasterEndTime[]" size="2" maxlength="2" value="" />
							<input class="line withHelpFocus" data-help="{-#msgDatacard_DisasterBeginTime_Helptext#-}" 
								type="text" id="queryEndDay" name="D_DisasterEndTime[]" size="2" maxlength="2" value="" />
						</td>
					</tr>
				</table>
				<br />
				<span class="fieldLabel withHelpOver" data-help="{-#msgDatacard_DisasterSource_Helptext#-}">{-#msgDatacard_DisasterSource#-}</span>
				<br />
				<select name="D_DisasterSource[0]" class="dlgmsg small line">
					<option class="small" value="AND">{-#tand#-}</option>
					<option class="small" value="OR" >{-#tor#-}</option>
				</select>
				<br />
				<textarea id="txtDisasterSource" name="D_DisasterSource[1]" style="width:220px; height:40px;" 
					class="inputText withHelpFocus" data-help="{-#msgDatacard_DisasterSource_Helptext#-}"></textarea>
				<br />
				<div id="divQueryRecordStatus">
					<span class="fieldLabel">{-#tdcstatus#-}</span>
					<br />
					<select id="fldQueryRecordStatus" name="D_RecordStatus[]" multiple class="fixw line">
						<option value="PUBLISHED" selected>{-#tdcpublished#-}</option>
						<option value="READY"     selected>{-#tdcready#-}    </option>
						<option value="DRAFT"             >{-#tdcdraft#-}    </option>
						<option value="TRASH"             >{-#tdctrash#-}    </option>
						<option value="DELETED"           >{-#tdcdeleted#-}  </option>
					</select>
				<br />
				</div>
				<span class="fieldLabel withHelpOver" data-help="{-#msgDatacard_DisasterSerial_Helptext#-}">{-#tserial#-}</span>
				<select name="D_DisasterSerial[0]" class="small line">
					<option class="small" value=""       >{-#tonly#-}</option>
					<option class="small" value="NOT"    >{-#texclude#-}</option>
					<option class="small" value="INCLUDE">{-#tinclude#-}</option>
				</select>
				<br />
				<input  class="line fixw withHelpFocus" data-help="{-#msgDatacard_DisasterSerial_Helptext#-}" 
					type="text" name="D_DisasterSerial[1]" value="" />
			</div>
		</dd>
		<!-- END DATETIME SECTION -->
		
		<!-- BEGIN CUSTOMQUERY SECTION -->
		<dt>{-#madvsection#-}</dt>
		<dd>
			<textarea id="QueryCustom" name="QueryCustom" 
				style="width:300px; height:45px;" class="inputText"></textarea>
			<br />
			<span class="dlgmsg">{-#tadvqryhelp#-}</span>
			<br />
			<table class="QueryCustom">
				<tr class="top">
					<td>
						<div class="list dwin" style="height:180px;">
							<div class="field" data-field="" data-type="" style="display:none;">
								<input type="button" class="ListItem" value="Q" />
								<br />
							</div>
						</div>
						<div class="defaultlist hidden">
							<span data-field="DisasterSerial"    data-type="text">{-#msgDatacard_DisasterSerial#-}</span>
							<span data-field="DisasterBeginTime" data-type="date">{-#msgDatacard_DisasterBeginTime#-}</span>
							<span data-field="DisasterSiteNotes" data-type="text">{-#msgDatacard_DisasterSiteNotes#-}</span>
							<span data-field="EventDuration"     data-type="text">{-#msgDatacard_EventDuration#-}</span>
							<span data-field="EventNotes"        data-type="text">{-#msgDatacard_EventNotes#-}</span>
							<span data-field="CauseNotes"        data-type="text">{-#msgDatacard_CauseNotes#-}</span>
							<span data-field="RecordAuthor"      data-type="text">{-#msgDatacard_RecordAuthor#-}</span>
							<span data-field="RecordCreation"    data-type="date">{-#msgDatacard_RecordCreation#-}</span>
							<span data-field="RecordUpdate"      data-type="date">{-#msgDatacard_RecordUpdate#-}</span>
						</div>
					</td>
					<td class="center">
						<input type="button" id="<" value="<" class="disabled" disabled 
							onClick="$('QueryCustom').value += this.value; $('QueryCustom').focus();" onMouseOver="showtip('{-#taqlessthan#-}');" />
						<input type="button" id=">" value=">" class="disabled" disabled 
							onClick="$('QueryCustom').value += this.value; $('QueryCustom').focus();" onMouseOver="showtip('{-#taqgreathan#-}');" />
						<input type="button" id="=" value="=" class="disabled" disabled 
							onClick="$('QueryCustom').value += this.value; $('QueryCustom').focus();" onMouseOver="showtip('{-#taqequalto#-}');" />
						<br />
						<input type="button" id="<>" value="<>" class="disabled" disabled 
							onClick="$('QueryCustom').value += this.value; $('QueryCustom').focus();" onMouseOver="showtip('{-#taqnoteqto#-}');" />
						<input type="button" id="LIKE '%%'" value="{-#tlike#-}" class="disabled" disabled 
							onClick="$('QueryCustom').value += this.id; $('QueryCustom').focus();" onMouseOver="showtip('{-#taqlike#-}');" />
						<input type="button" id="=-1" value="{-#teffhav#-}" class="disabled" disabled 
							onClick="$('QueryCustom').value += this.id; $('QueryCustom').focus();" onMouseOver="showtip('{-#taqwere#-}');" />
						<input type="button" id="=0" value="{-#teffhavnot#-}" class="disabled" disabled 
							onClick="$('QueryCustom').value += this.id; $('QueryCustom').focus();" onMouseOver="showtip('{-#taqwerent#-}');" />
						<input type="button" id="=-2" value="{-#teffdontknow#-}" class="disabled" disabled 
							onClick="$('QueryCustom').value += this.id; $('QueryCustom').focus();" onMouseOver="showtip('{-#taqdntknow#-}');" />
						<br />
						<input type="button" value=" (" onClick="$('QueryCustom').value += this.value;" />
						<input type="button" value=") " onClick="$('QueryCustom').value += this.value;" />
						<input type="button" value=" AND " onClick="$('QueryCustom').value += this.value;" onMouseOver="showtip('{-#taqandopt#-}')" />
						<input type="button" value=" OR " onClick="$('QueryCustom').value += this.value;" onMouseOver="showtip('{-#taqoropt#-}')" />
						<br /><br />
						<input type="button" value="{-#tclear#-}" onClick="$('QueryCustom').value = '';" />
					</td>
				</tr>
			</table>
		</dd>
		<!-- BEGIN CUSTOMQUERY SECTION -->
	</dl>
</form> <!-- id="DC" -->
</div>
