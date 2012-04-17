{-config_load file="$lg.conf" section="grpMainStrings"-}
<div id="std-win" class="x-hidden">
	<span id="msgViewStdButtonClear" style="display:none;">{-#tclear#-}</span>
	<span id="msgViewStdButtonSend"  style="display:none;">{-#tsend#-}</span>
	<span id="msgViewStdButtonClose" style="display:none;">{-#tclose#-}</span>
	<div class="x-window-header">
		{-#msgViewStd#-}
	</div>
	<div id="std-cfg" class="ViewStatParams mainblock">
		<form id="frmStatParams" method="post" action="">
			<table>
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
				<tr class="top">
					<td class="StatGroup">
						<b>{-$std.StatisticFirstlev[0]-}</b><br />
						<input class="label" type="hidden" name="options[grouplabel][0]" value="" />
						<select class="StatlevelFirst value line" id="fldStatParam_FirstLev" name="options[group][0]" size="8" style="width:180px;"  >
							<option></option>
						</select>
						<span id="ViewStatParamsLabelEvent" class="hidden">{-$std.StatisticEventName[0]-}</span>
						<span id="ViewStatParamsLabelYear"  class="hidden">{-$std.StatisticDisasterBeginTime_YEAR[0]-}</span>
						<span id="ViewStatParamsLabelMonth" class="hidden">{-$std.StatisticDisasterBeginTime_MONTH[0]-}</span>
						<span id="ViewStatParamsLabelCause" class="hidden">{-$std.StatisticCauseName[0]-}</span>
					</td>
					<td class="StatGroup">
						<b>{-$std.StatisticSecondlev[0]-}</b><br/>
						<input class="label" type="hidden" name="options[grouplabel][1]" value="" />
						<select class="value line" id="fldStatParam_SecondLev" name="options[group][1]" size="8" style="width:180px;" >
						</select>
					</td>
					<td class="StatGroup">
						<b>{-$std.StatisticThirdlev[0]-}</b><br />
						<input class="label" type="hidden" name="options[grouplabel][2]" value="" />
						<select class="value line" id="fldStatParam_ThirdLev" name="options[group][2]" size="8" style="width:180px;">
						</select>
					</td>
				</tr>
			</table>
			<br />
			<table>
				<tr>
					<td>
						<b>{-#savailfields#-}</b>
						<span id="StatLabelAuxHave"       class="hidden">{-#tauxhave#-}</span>
						<span id="StatLabelAuxAffect"     class="hidden">{-#tauxaffect#-}</span>
						<span id="StatLabelEventDuration" class="hidden">{-#msgViewStd_EventDuration#-}</span>
						<br />
						<select class="FieldsAvailable line" id="_S+sel1[]" size="6" style="width:220px;" multiple="multiple">
							<option></option>
						</select>
						<br />
						<a class="button" onclick="selectall('_S+sel1[]');"><span>{-#balls#-}</span></a>
						<a class="button" onclick="selectnone('_S+sel1[]');"><span>{-#bnone#-}</span></a>
					</td>
					<td class="middle center" style="width:20px;">
						<a class="button" onclick="moveOptions($('_S+sel1[]'), $('fldStatFieldSelect'));"><span>&rarr;</span></a>
						<br /><br /><br />
						<a class="button" onclick="moveOptions($('fldStatFieldSelect'), $('_S+sel1[]'));"><span>&larr;</span></a>
					</td>
					<td><b>{-#sviewfields#-}</b><br />
						<input type="hidden" id="fldStatField" name="options[field]" value="" />
						<input type="hidden" id="fldStatFieldLabel" name="options[fieldlabel]" value="" />
						<select class="FieldsShow line" id="fldStatFieldSelect" size="6" style="width:220px;" multiple="multiple">
							<option></option>
						</select>
						<br />
						<a class="button" onclick="selectall('fldStatFieldSelect');"><span>{-#balls#-}</span></a>
						<a class="button" onclick="selectnone('fldStatFieldSelect');"><span>{-#bnone#-}</span></a>
					</td>
					<td style="width:20px;" align="center">
						<a class="button" onclick="top('fldStatFieldSelect');"><span>&uArr;</span></a><br />
						<a class="button" onclick="upone('fldStatFieldSelect');"><span>&uarr;</span></a><br />
						<a class="button" onclick="downone('fldStatFieldSelect');"><span>&darr;</span></a><br />
						<a class="button" onclick="bottom('fldStatFieldSelect');"><span>&dArr;</span></a><br />
					</td>
				</tr>
			</table>
			<input type="hidden" id="_S+cmd" name="_S+cmd" value="result" />
			<input type="hidden" id="_S+saveopt" name="_S+saveopt" value="" />
		</form>
		<div class="hidden">
			<span id="txtStatRecords">{-#trepnum#-}</span>
		</div>
	</div>
</div>
