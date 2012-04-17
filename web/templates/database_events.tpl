{-config_load file="$lg.conf" section="grpDatabaseEvents"-}
<div class="clsDatabaseEvents">
	<b title="{-$dic.DBEvent[2]-}">{-#msgDatabaseEvents_CustomEventTitle#-}</b>
	<br />
	<div id="divDatabaseEvents_EventListCustom" class="dwin" style="width:100%; height:100px;">
		<table class="grid" id="tblDatabaseEvents_EventListCustom">
			<thead>
				<tr>
					<td style="display:none;">
						Id
					</td>
					<td style="display:none;">
						Predefined
					</td>
					<td class="header" title="{-$dic.DBEvePersonName[2]-}">
						<b>{-$dic.DBEvePersonName[0]-}</b>
					</td>
					<td class="header" title="{-$dic.DBEvePersonDef[2]-}" style="width:70%;">
						<b>{-$dic.DBEvePersonDef[0]-}</b>
					</td>
					<td class="header" title="{-$dic.DBEveActive[2]-}">
						<b>{-$dic.DBEveActive[0]-}</b>
					</td>
				</tr>
			</thead>
			<tbody id="tbodyDatabaseEvents_EventListCustom">
				<tr style="display:none;">
					<td class="EventId"></td>
					<td class="EventPredefined"></td>
					<td class="EventName"></td>
					<td class="EventDesc" style="width:70%;"></td>
					<td class="EventActive top center"><input type="checkbox" disabled="disabled" /></td>
				</tr>
			</tbody>
		</table>
	</div>
	<br />
	<div id="divDatabaseEvents_EventListDefault" class="dwin" style="width:100%; height:100px;">
		<table id="tblDatabaseEvents_EventListDefault" width="100%" class="grid">
			<thead>
				<tr>
					<td style="display:none;">
						Id
					</td>
					<td style="display:none;">
						Predefined
					</td>
					<td class="header" title="{-$dic.DBEvePredefName[2]-}">
						<b>{-$dic.DBEvePredefName[0]-}</b>
					</td>
					<td class="header" title="{-$dic.DBEvePredefDef[2]-}" style="width:70%;">
						<b>{-$dic.DBEvePredefDef[0]-}</b>
					</td>
					<td class="header" title="{-$dic.DBEveActive[2]-}">
						<b>{-$dic.DBEveActive[0]-}</b>
					</td>
				</tr>
			</thead>
			<tbody id="tbodyDatabaseEvents_EventListDefault">
				<tr style="display:none;">
					<td class="EventId"></td>
					<td class="EventPredefined"></td>
					<td class="EventName"></td>
					<td class="EventDesc" style="width:70%;"></td>
					<td class="EventActive"><input type="checkbox" /></td>
				</tr>
			</tbody>
		</table>
	</div>
	<br />
	<div>
		<a class="button" id="btnDatabaseEvents_Add"><span>{-#msgDatabaseEvents_Add#-}</span></a>
		<br />
		<br />
		<span class="clsDatabaseEventsStatus" id="msgDatabaseEvents_UpdateOk">{-#msgDatabaseEvents_UpdateOk#-}</span>
		<span class="clsDatabaseEventsStatus" id="msgDatabaseEvents_UpdateError">{-#msgDatabaseEvents_UpdateError#-}</span>
		<span class="clsDatabaseEventsStatus" id="msgDatabaseEvents_ErrorEmtpyFields">{-#msgDatabaseEvents_ErrorEmptyFields#-}</span>
		<span class="clsDatabaseEventsStatus" id="msgDatabaseEvents_ErrorDuplicateName">{-#msgDatabaseEvents_ErrorDuplicateName#-}</span>
		<span class="clsDatabaseEventsStatus" id="msgDatabaseEvents_ErrorCannotDelete">{-#msgDatabaseEvents_ErrorCannotDelete#-}</span>
		<br />
	</div>
	<br />
	<div id="divDatabaseEvents_Edit" style="display:none;width:500px;">
		<form id="frmDatabaseEvents_Edit" action="#">
			<table width="80%">
				<tr>
					<td>
						<span class="Predefined">{-#msgEvents_Predefined#-}</span>
						<span class="Custom">{-#msgEvents_Custom#-}</span>
						<b style="color:darkred;">*</b>
						<br />
						<input id="fldDatabaseEvents_EventName" name="EventName" type="text" class="line" maxlength="40" style="width:500px;" tabindex="1"
							title="{-$dic.DBEvePersonName[2]-}" />
						<br /><br />
						{-$dic.DBEvePersonDef[0]-}<b style="color:darkred;">*</b><br />
						<textarea id="fldDatabaseEvents_EventDesc" name="EventDesc" class="line" rows="4" style="width:500px;" tabindex="2" 
							title="{-$dic.DBEvePersonDef[2]-}"></textarea>
						<br /><br />
						{-$dic.DBEveActive[0]-}
						<input id="fldDatabaseEvents_EventActive" name="EventActive" type="hidden" value="0" />
						<input id="fldDatabaseEvents_EventActiveCheckbox" type="checkbox" 
							title="{-$dic.DBEveActive[2]-}" tabindex="3" />
					</td>
				</tr>
			</table>
			<br /><br />
			<input id="fldDatabaseEvents_EventId"         name="EventId"         type="hidden" />
			<input id="fldDatabaseEvents_EventPredefined" name="EventPredefined" type="hidden" />
			<div class="center">
				<a class="button" id="btnDatabaseEvents_Save"   tabindex="4"><span>{-#msgDatabaseEvents_Save#-}</span></a>
				<a class="button" id="btnDatabaseEvents_Cancel" tabindex="5"><span>{-#msgDatabaseEvents_Cancel#-}</span></a>
			</div>
		</form>
	</div>
</div>
