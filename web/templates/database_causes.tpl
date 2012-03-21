{-config_load file="$lg.conf" section="grpDatabaseCauses"-}
<div class="clsDatabaseCauses">
	<b title="{-$dic.DBCause[2]-}">{-#msgDatabaseCauses_CustomCauseTitle#-}</b>
	<br />
	<div id="divDatabaseCauses_CauseListCustom" class="dwin" style="width:100%; height:100px;">
		<table class="grid" id="tblDatabaseCauses_CauseListCustom">
			<thead>
				<tr>
					<td style="display:none;">
						Id
					</td>
					<td style="display:none;">
						Predefined
					</td>
					<td class="header" title="{-$dic.DBCauPersonName[2]-}">
						<b>{-$dic.DBCauPersonName[0]-}</b>
					</td>
					<td class="header" title="{-$dic.DBCauPersonDef[2]-}" style="width:70%;">
						<b>{-$dic.DBCauPersonDef[0]-}</b>
					</td>
					<td class="header" title="{-$dic.DBCauActive[2]-}">
						<b>{-$dic.DBCauActive[0]-}</b>
					</td>
				</tr>
			</thead>
			<tbody id="tbodyDatabaseCauses_CauseListCustom">
				<tr style="display:none;">
					<td class="CauseId"></td>
					<td class="CausePredefined"></td>
					<td class="CauseName"></td>
					<td class="CauseDesc" style="width:70%;"></td>
					<td class="CauseActive" style="text-align:center;" valign="top"><input type="checkbox" disabled="disabled" /></td>
				</tr>
			</tbody>
		</table>
	</div>
	<br />
	<div id="divDatabaseCauses_CauseListDefault" class="dwin" style="width:100%; height:100px;">
		<table id="tblDatabaseCauses_CauseListDefault" width="100%" class="grid">
			<thead>
				<tr>
					<td style="display:none;">
						Id
					</td>
					<td style="display:none;">
						Predefined
					</td>
					<td class="header" title="{-$dic.DBCauPredefName[2]-}">
						<b>{-$dic.DBCauPredefName[0]-}</b>
					</td>
					<td class="header" title="{-$dic.DBCauPredefDef[2]-}" style="width:70%;">
						<b>{-$dic.DBCauPredefDef[0]-}</b>
					</td>
					<td class="header" title="{-$dic.DBCauActive[2]-}">
						<b>{-$dic.DBCauActive[0]-}</b>
					</td>
				</tr>
			</thead>
			<tbody id="tbodyDatabaseCauses_CauseListDefault">
				<tr style="display:none;">
					<td class="CauseId"></td>
					<td class="CausePredefined"></td>
					<td class="CauseName"></td>
					<td class="CauseDesc" style="width:70%;"></td>
					<td class="CauseActive"><input type="checkbox" /></td>
				</tr>
			</tbody>
		</table>
	</div>
	<br />
	<div>
		<a class="button" id="btnDatabaseCauses_Add"><span>{-#msgDatabaseCauses_Add#-}</span></a>
		<br />
		<br />
		<span class="clsDatabaseCausesStatus" id="msgDatabaseCauses_UpdateOk">{-#msgDatabaseCauses_UpdateOk#-}</span>
		<span class="clsDatabaseCausesStatus" id="msgDatabaseCauses_UpdateError">{-#msgDatabaseCauses_UpdateError#-}</span>
		<span class="clsDatabaseCausesStatus" id="msgDatabaseCauses_ErrorEmtpyFields">{-#msgDatabaseCauses_ErrorEmptyFields#-}</span>
		<span class="clsDatabaseCausesStatus" id="msgDatabaseCauses_ErrorDuplicateName">{-#msgDatabaseCauses_ErrorDuplicateName#-}</span>
		<span class="clsDatabaseCausesStatus" id="msgDatabaseCauses_ErrorCannotDelete">{-#msgDatabaseCauses_ErrorCannotDelete#-}</span>
		<br />
	</div>
	<br />
	<div id="divDatabaseCauses_Edit" style="display:none;width:500px;">
		<form id="frmDatabaseCauses_Edit" action="">
			<table width="80%">
				<tr>
					<td>
						<span class="Predefined">{-#msgCauses_Predefined#-}</span>
						<span class="Custom">{-#msgCauses_Custom#-}</span>
						<b style="color:darkred;">*</b>
						<br />
						<input id="fldDatabaseCauses_CauseName" name="CauseName" type="text" class="line" maxlength="40" style="width:500px;" tabindex="1"
							title="{-$dic.DBCauPersonName[2]-}" />
						<br /><br />
						{-$dic.DBCauPersonDef[0]-}<b style="color:darkred;">*</b><br />
						<textarea id="fldDatabaseCauses_CauseDesc" name="CauseDesc" class="line" rows="4" style="width:500px;" tabindex="2" 
							title="{-$dic.DBCauPersonDef[2]-}"></textarea>
						<br /><br />
						{-$dic.DBCauActive[0]-}
						<input id="fldDatabaseCauses_CauseActive" name="CauseActive" type="hidden" value="0" />
						<input id="fldDatabaseCauses_CauseActiveCheckbox" type="checkbox" 
							title="{-$dic.DBCauActive[2]-}" tabindex="3" />
					</td>
				</tr>
			</table>
			<br /><br />
			<input id="fldDatabaseCauses_CauseId"         name="CauseId"         type="hidden" />
			<input id="fldDatabaseCauses_CausePredefined" name="CausePredefined" type="hidden" />
			<div class="center">
				<a class="button" id="btnDatabaseCauses_Save"   tabindex="4"><span>{-#msgDatabaseCauses_Save#-}</span></a>
				<a class="button" id="btnDatabaseCauses_Cancel" tabindex="5"><span>{-#msgDatabaseCauses_Cancel#-}</span></a>
			</div>
		</form>
	</div>
</div>
