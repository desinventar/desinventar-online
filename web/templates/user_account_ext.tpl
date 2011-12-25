{-config_load file="$lg.conf" section="grpUserAccount"-}
<!-- Show Database functions window -->
<div id="divUserAccountWin" class="x-hidden">
	<div class="x-window-header">
		{-#msgUserAccount_Title#-}
	</div>
	<div id="divUserAccountContent">
		<form id="frmUserAccountChangePasswd">
			<table class="grid">
				<tr>
					<td>
						<b style="color:darkred;">{-#msgUserAccount_OldPasswd#-}</b>
					</td>
					<td>
						<input type="password" id="fldUserAccountPasswd" name="User[UserPasswd]" size="25" maxlength="25" class="line" />
					</td>
				</tr>
				<tr>
					<td>
						<b style="color:darkred;">{-#msgUserAccount_NewPasswd#-}</b>
					</td>
					<td>
						<input type="password" id="fldUserAccountPasswd2" name="User[UserPasswd2]" size="25" maxlength="25" class="line" />
					</td>
				</tr>
				<tr>
					<td>
						<b style="color:darkred;">{-#msgUserAccount_NewPasswd2#-}</b>
					</td>
					<td>
						<input type="password" id="fldUserAccountPasswd3" name="User[UserPasswd3]" size="25" maxlength="25" class="line" />
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<div class="center">
							<a class="button" id="btnUserAccountChangePasswdSubmit"><span>{-#msgUserAccount_ChangePasswdSave#-}</span></a>
							<a class="button" id="btnUserAccountChangePasswdCancel"><span>{-#msgUserAccount_ChangePasswdCancel#-}</span></a>
						</div>
					</td>
				</tr>
				<tr>
					<td colspan="2" align="center">
						<br />
					</td>
				</tr>
				<tr>
					<td colspan="2" align="center">
						<span class="clsUserAccountChangePasswdStatus" id="msgUserAccountEmptyFields">{-#msgUserAccountEmptyFields#-}</span>
						<span class="clsUserAccountChangePasswdStatus" id="msgUserAccountPasswdDoNotMatch">{-#msgUserAccountPasswdDoNotMatch#-}</span>
						<span class="clsUserAccountChangePasswdStatus" id="msgUserAccountInvalidPasswd">{-#msgUserAccountInvalidPasswd#-}</span>
						<span class="clsUserAccountChangePasswdStatus" id="msgUserAccountPasswdUpdated">{-#msgUserAccountPasswdUpdated#-}</span>
					</td>
				</tr>
			</table>
		</form>
	</div>
</div>
