<div id="std-win" class="x-hidden">
	<span id="msgViewStdButtonClear" style="display:none;">{-#tclear#-}</span>
	<span id="msgViewStdButtonSend"  style="display:none;">{-#tsend#-}</span>
	<span id="msgViewStdButtonClose" style="display:none;">{-#tclose#-}</span>
	<div class="x-window-header">
		{-#msgViewStd#-}
	</div>
	<div id="std-cfg">
		<form id="frmStatParams" method="post" action="">
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
					<td>
						<b>{-$std.StatisticFirstlev[0]-}</b><br />
						<input type="hidden" class="FirstlevLabel" name="options[FirstlevLabel]" value="" />
						<select class="Firstlev line" id="fldStatParam_FirstLev" name="_S+Firstlev" size="8" style="width:180px;"  >
							{-foreach name=glev key=k item=i from=$glev-}
								{-assign var="ln" value="StatisticGeographyId_$k"-}
								<option value="{-$k-}|D.GeographyId" {-if $smarty.foreach.glev.first-}selected{-/if-}>{-$std.$ln[0]-}</option>
							{-/foreach-}
							<option value="|D.EventId">{-$std.StatisticEventName[0]-}</option>
							<option value="YEAR|D.DisasterBeginTime">{-$std.StatisticDisasterBeginTime_YEAR[0]-}</option>
							<option value="MONTH|D.DisasterBeginTime">{-$std.StatisticDisasterBeginTime_MONTH[0]-}</option>
							<option value="|D.CauseId">{-$std.StatisticCauseName[0]-}</option>
						</select>
					</td>
					<td>
						<b>{-$std.StatisticSecondlev[0]-}</b><br/>
						<input type="hidden" class="SecondlevLabel" name="options[SecondlevLabel]" value="" />
						<select class="Secondlev line" id="fldStatParam_SecondLev" name="_S+Secondlev" size="8" style="width:180px;" >
						</select>
					</td>
					<td>
						<b>{-$std.StatisticThirdlev[0]-}</b><br />
						<input type="hidden" class="ThirdlevLabel" name="options[ThirdlevLabel]" value="" />
						<select class="Thirdlev line" id="fldStatParam_ThirdLev" name="_S+Thirdlev" size="8" style="width:180px;">
						</select>
					</td>
				</tr>
			</table>
			<br />
			<table>
				<tr>
					<td><b>{-#savailfields#-}</b><br />
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
							{-foreach name=eef key=k item=i from=$EEFieldList-}
								{-if $i[2] == "INTEGER" || $i[2] == "DOUBLE"-}
									<option value="E.{-$k-}|>|-1">{-$i[0]-}</option>
								{-/if-}
							{-/foreach-}
						</select>
						<br />
						<a class="button" onclick="selectall('_S+sel1[]');"><span>{-#balls#-}</span></a>
						<a class="button" onclick="selectnone('_S+sel1[]');"><span>{-#bnone#-}</span></a>
					</td>
					<td align="center" valign="middle" style="width:20px;">
						<a class="button" onclick="moveOptions($('_S+sel1[]'), $('_S+Field[]'));"><span>&rarr;</span></a>
						<br /><br /><br />
						<a class="button" onclick="moveOptions($('_S+Field[]'), $('_S+sel1[]'));"><span>&larr;</span></a>
					</td>
					<td><b>{-#sviewfields#-}</b><br />
						<select id="_S+Field[]" size="6" style="width:220px;" multiple class="line">
							{-foreach name=ef1 key=k item=i from=$ef1-}
								<option value="D.{-$k-}Q|>|-1">{-$i[0]-}</option>
							{-/foreach-}
						</select>
						<br />
						<a class="button" onclick="selectall('_S+Field[]');"><span>{-#balls#-}</span></a>
						<a class="button" onclick="selectnone('_S+Field[]');"><span>{-#bnone#-}</span></a>
					</td>
					<td style="width:20px;" align="center">
						<a class="button" onclick="top('_S+Field[]');"><span>&uArr;</span></a><br />
						<a class="button" onclick="upone('_S+Field[]');"><span>&uarr;</span></a><br />
						<a class="button" onclick="downone('_S+Field[]');"><span>&darr;</span></a><br />
						<a class="button" onclick="bottom('_S+Field[]');"><span>&dArr;</span></a><br />
					</td>
				</tr>
			</table>
			<input type="hidden" id="_S+FieldH" name="_S+Field" value="" />
			<input type="hidden" id="_S+cmd" name="_S+cmd" value="result" />
			<input type="hidden" id="_S+saveopt" name="_S+saveopt" value="" />
		</form>
	</div>
</div>
