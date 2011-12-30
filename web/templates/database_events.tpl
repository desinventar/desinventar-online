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
					<td class="EventId"         style="display:none;"></td>
					<td class="EventPredefined" style="display:none;"></td>
					<td class="EventName"></td>
					<td class="EventDesc" style="width:70%;"></td>
					<td class="EventActive" style="text-align:center;"><input type="checkbox" /></td>
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
	<a class="button" id="btnDatabaseEvents_Add"><span>{-#msgDatabaseEvents_Add#-}</span></a>
	<br />
	<br />
	<div id="divDatabaseEvents_Edit" style="display:none">
		<form id="frmDatabaseEvents_Edit">
			<input id="fldDatabaseEvents_EventId" name="Event[EventId]" type="hidden" />
			<br />
			{-$dic.DBEvePersonName[0]-}<b style="color:darkred;">*</b>
			<br />
			<input id="fldDatabaseEvents_EventName" name="Event[EventName]" type="text" class="line" maxlength="40" style="width:500px;" tabindex="1"
				onBlur="updateList('eventstatusmsg', jQuery('#desinventarURL').val() + '/events.php', 'r={-$reg-}&cmd=chkname&EventId='+ $('aEventId').value +'&EventName='+ $('EventName').value);"
				title="{-$dic.DBEvePersonName[2]-}" />
			<br /><br />
			{-$dic.DBEvePersonDef[0]-}<b style="color:darkred;">*</b><br />
			<textarea id="fldDatabaseEvents_EventDesc" name="Event[EventDesc]" class="line" rows="2" style="width:500px;" tabindex="2" 
				title="{-$dic.DBEvePersonDef[2]-}"></textarea>
			<br /><br />
			{-$dic.DBEveActive[0]-}
			<input id="fldDatabaseEvents_EventActive" name="Event[EventActive]" type="checkbox" {-$ro-} 
				title="{-$dic.DBEveActive[2]-}" tabindex="3" />
			<br /><br />
			<input id="fldDatabaseEvents_EventPredefined" name="Event[EventPredefined]" type="hidden" />
			<div class="center">
				<a class="button" id="btnEventEditSend"   tabindex="4"><span>{-#msgDatabaseEvents_Save#-}</span></a>
				<a class="button" id="btnEventEditCancel" tabindex="5"><span>{-#msgDatabaseEvents_Cancel#-}</span></a>
			</div>
		</form>
	</div>
	<div>
		<span class="clsDatabaseEventsStatus" id="btnDatabaseEvents_InsertOk">{-#msgDatabaseEvents_InsertOk#-}</span>
		<span class="clsDatabaseEventsStatus" id="btnDatabaseEvents_InsertError">{-#msgDatabaseEvents_InsertError#-}</span>
		<span class="clsDatabaseEventsStatus" id="btnDatabaseEvents_UpdateOk">{-#msgDatabaseEvents_UpdateOk#-}</span>
		<span class="clsDatabaseEventsStatus" id="btnDatabaseEvents_UpdateError">{-#msgDatabaseEvents_UpdateError#-}</span>
		<span class="clsDatabaseEventsStatus" id="btnDatabaseEvents_ErrorEmtpyFields">{-#msgDatabaseEvents_ErrorEmptyFields#-}</span>
		<span class="clsDatabaseEventsStatus" id="btnDatabaseEvents_ErrorDuplicateName">{-#msgDatabaseEvents_ErrorDuplicateName#-}</span>
		<span class="clsDatabaseEventsStatus" id="btnDatabaseEvents_ErrorCannotDelete">{-#msgDatabaseEvents_ErrorCannotDelete#-}</span>
		<br />
	</div>
</div>
