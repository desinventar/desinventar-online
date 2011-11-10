<div id="dat-win" class="x-hidden">
	<span id="msgViewDataButtonClear" style="display:none;">{-#tclear#-}</span>
	<span id="msgViewDataButtonSend"  style="display:none;">{-#tsend#-}</span>
	<span id="msgViewDataButtonClose" style="display:none;">{-#tclose#-}</span>
<div class="x-window-header">{-#bdata#-}
</div>
<div id="dat-cfg">
	<form id="CD" method="POST">
		{-#sresxpage#-}
		<select id="_D+SQL_LIMIT" name="_D+SQL_LIMIT" class="line">
			<option value="20">20</option>
			<option value="50">50</option>
			<option value="100" selected>100</option>
			<option value="200">200</option>
		</select>
		<br /><br />
		<table>
			<tr>
				<td>
					<b>{-#savailfields#-}</b><br />
					<select id="_D+sel1[]" size="8" style="width:220px;" multiple class="line">
						{-foreach name=sst1 key=key item=item from=$sda1-}
							<option value="D.{-$item-}">{-$dc2.$item[0]-}</option>
						{-/foreach-}
						<option disabled>---</option>
						{-foreach name=sst2 key=key item=item from=$EEFieldList-}
							<option value="E.{-$key-}">{-$item[0]-}</option>
						{-/foreach-}
					</select><br />
					<input type="button" value="{-#balls#-}" onclick="selectall('_D+sel1[]');" class="line" />
					<input type="button" value="{-#bnone#-}" onclick="selectnone('_D+sel1[]');" class="line" />
				</td>
				<td align="center" valign="middle" style="width:20px;">
					<input type="button" value="&rarr;" onclick="moveOptions($('_D+sel1[]'), $('_D+Field[]'));" class="line" />
					<br /><br /><br />
					<input type="button" value="&larr;" onclick="moveOptions($('_D+Field[]'), $('_D+sel1[]'));" class="line" />
				</td>
				<td>
					<b>{-#sviewfields#-}</b><br>
					<select id="_D+Field[]" size="8" style="width:220px;" multiple class="line">
						{-foreach name=sst key=key item=item from=$sda-}
							{-if $item != "D.DisasterId"-}
								<option value="D.{-$item-}">{-$dc2.$item[0]-}</option>
							{-/if-}
						{-/foreach-}
					</select><br/>
					<input type="button" value="{-#balls#-}" onclick="selectall('_D+Field[]');" class="line" />
					<input type="button" value="{-#bnone#-}" onclick="selectnone('_D+Field[]');" class="line" />
				</td>
				<td style="width:20px;" align="center">
					<input type="button" value="&uarr;&uarr;" onclick="top('_D+Field[]');" class="line" /><br/>
					<input type="button" value="&uarr;" onclick="upone('_D+Field[]');" class="line" /><br/>
					<input type="button" value="&darr;" onclick="downone('_D+Field[]');" class="line" /><br/>
					<input type="button" value="&darr;&darr;" onclick="bottom('_D+Field[]');" class="line" /><br/>
				</td>
			</tr>
		</table>
		<br/><br/>
		<b>{-#txtOrderBy#-}</b><br/>
		<select id="_D+SQL_ORDER" name="_D+SQL_ORDER" class="fixw line" size="5">
			<option value="D.DisasterBeginTime, V.EventName, G.GeographyFQName" selected>{-#txtOrderByDateEventGeography#-}</option>
			<option value="D.DisasterBeginTime, D.GeographyId, V.EventName">{-#txtOrderByDateGeographyEvent#-}</option>
			<option value="G.GeographyFQName, V.EventName, D.DisasterBeginTime">{-#txtOrderByGeographyEventDate#-}</option>
			<option value="V.EventName, D.DisasterBeginTime, G.GeographyFQName">{-#txtOrderByEventDateGeography#-}</option>
			<option value="D.DisasterSerial">{-#txtOrderBySerial#-}</option>
			<option value="D.RecordCreation">{-#txtOrderByRecordCreation#-}</option>
			<option value="D.RecordUpdate">{-#txtOrderByRecordUpdate#-}</option>
		</select>
		<input type="hidden" id="_D+FieldH" name="_D+Field" value="" />
		<input type="hidden" id="_D+cmd" name="_D+cmd" value="result" />
		<input type="hidden" id="_D+saveopt" name="_D+saveopt" value="" />
	</form>
</div>
</div>
