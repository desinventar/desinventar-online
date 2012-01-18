{-config_load file="$lg.conf" section="grpMenuUser"-}
<div id="divUserAccount">
	<div class="UserChangePasswd">
		<form class="UserChangePasswd">
			<table class="grid">
				<tr>
					<td>
						<b style="color:darkred;">{-#toldpassword#-}</b>
					</td>
					<td>
						<input type="password" class="UserPasswd line" name="User[UserPasswd]" size="25" maxlength="25" />
					</td>
				</tr>
				<tr>
					<td>
						<b style="color:darkred;">{-#tnewpassword#-}</b>
					</td>
					<td>
						<input type="password" class="UserPasswd2 line" name="User[UserPasswd2]" size="25" maxlength="25" />
					</td>
				</tr>
				<tr>
					<td>
						<b style="color:darkred;">{-#tnewpassword2#-}</b>
					</td>
					<td>
						<input type="password" class="UserPasswd3 line" name="User[UserPasswd3]" size="25" maxlength="25" />
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<div class="center">
							<a class="button btnSubmit"><span>{-#bsave#-}</span></a>
							<a class="button btnCancel"><span>{-#bcancel#-}</span></a>
						</div>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<div class="center">
							<span class="status" id="msgEmptyFields">{-#erremptyfields#-}</span>
							<span class="status" id="msgPasswdDoNotMatch">{-#errnomatch#-}</span>
							<span class="status" id="msgInvalidPasswd">{-#errbadpasswd#-}</span>
							<span class="status" id="msgPasswdUpdated">{-#msgupdatesucc#-}</span>
						</div>
					</td>
				</tr>
			</table>
		</form>
	</div>
</div>
