<div id="dat-win" class="x-hidden">
	<span id="msgViewDataButtonClear" style="display:none;">{-#tclear#-}</span>
	<span id="msgViewDataButtonSend"  style="display:none;">{-#tsend#-}</span>
	<span id="msgViewDataButtonClose" style="display:none;">{-#tclose#-}</span>
<div class="x-window-header">{-#bdata#-}
</div>
<div id="dat-cfg">
	<form id="CD" method="post" action="">
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
					<a class="button" onclick="selectall('_D+sel1[]');"><span>{-#balls#-}</span></a>
					<a class="button" onclick="selectnone('_D+sel1[]');"><span>{-#bnone#-}</span></a>
				</td>
				<td align="center" valign="middle" style="width:20px;">
					<a class="button" onclick="moveOptions($('_D+sel1[]'), $('_D+Field[]'));"><span>&rarr;</span></a>
					<br /><br /><br />
					<a class="button" onclick="moveOptions($('_D+Field[]'), $('_D+sel1[]'));"><span>&larr;</span></a>
				</td>
				<td>
					<b>{-#sviewfields#-}</b><br />
					<select id="_D+Field[]" size="8" style="width:220px;" multiple class="line">
						{-foreach name=sst key=key item=item from=$sda-}
							{-if $item != "D.DisasterId"-}
								<option value="D.{-$item-}">{-$dc2.$item[0]-}</option>
							{-/if-}
						{-/foreach-}
					</select><br/>
					<a class="button" onclick="selectall('_D+Field[]');"><span>{-#balls#-}</span></a>
					<a class="button" onclick="selectnone('_D+Field[]');"><span>{-#bnone#-}</span></a>
				</td>
				<td style="width:20px;" align="center">
					<div class="center">
						<a class="button" onclick="top('_D+Field[]');"    ><span>&uarr;&uarr;</span></a><br/>
						<a class="button" onclick="upone('_D+Field[]');"  ><span>&uarr;</span></a><br/>
						<a class="button" onclick="downone('_D+Field[]');"><span>&darr;</span></a><br/>
						<a class="button" onclick="bottom('_D+Field[]');" ><span>&darr;&darr;</span></a><br/>
					</div>
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
