<div id="divQueryDesign">
<span id="msgQueryDesignTitle" class="hidden">{-#tsubtitle#-}</span>
<span id="msgQueryDesignTooltip" class="hidden">{-#thlpquery#-}</span>
<form id="frmMainQuery" method="post" action="" target="dcr">
	<input type="hidden" id="_REG" name="_REG" value="{-$reg-}" />
	<input type="hidden" id="_CMD" name="_CMD" />
	<input type="hidden" id="prmQueryCommand"      name="prmQuery[Command]"      value="DEFAULT" />
	<input type="hidden" id="prmQueryMinYear" name="prmQuery[ConstMinYear]" value="{-$yini-}" />
	<input type="hidden" id="prmQueryMaxYear" name="prmQuery[ConstMaxYear]" value="{-$yend-}" />
	<dl class="accordion">
		<!-- BEGIN GEOGRAPHY SECTION -->
		<!-- Select from Map testing ... 'selectionmap.php' -->
		<dt>{-#mgeosection#-}</dt>
		<dd>
			<input type="hidden" name="QueryGeography[OP]" value="AND" />
			{-foreach name=glev key=k item=i from=$glev-}
				<span class="dlgmsg" onMouseOver="showtip('{-$i[1]-}');">{-$i[0]-}</span> |
			{-/foreach-}
			<div id="qgeolst" style="height: 280px;" class="dwin">
				{-assign var="maintree" value="true"-}
				{-* Show Geography List *-}
				{-include file="block_glist.tpl"-}
			</div>
			
			<b onMouseOver="showtip('{-$dis.DisasterSiteNotes[2]-}');">{-$dis.DisasterSiteNotes[0]-}</b>
			<input type="hidden" name="D_DisasterSiteNotes[0]" />
			<br/>
			<textarea id="DisasterSiteNotes" name="D_DisasterSiteNotes[1]" style="width:220px; height: 40px;"
				class="inputText" onFocus="showtip('{-$dis.DisasterSiteNotes[2]-}');">{-$qd.D_DisasterSiteNotes[1]-}</textarea>
		</dd>
		
		<!-- BEGIN EVENT SECTION -->
		<dt>{-#mevesection#-}</dt>
		<dd>
			<input type="hidden" name="QueryEvent[OP]" value="AND" />
			<span class="dlgmsg">{-#tcntclick#-}</span><br />
			<select id="qevelst" name="D_EventId[]" multiple style="width: 250px; height: 200px;" class="line">
				{-foreach name=eve key=key item=item from=$evepredl-}
					<option value="{-$key-}" onMouseOver="showtip('{-$item[1]-}');" {-if $item[3]-}selected{-/if-}>{-$item[0]-}</option>
				{-/foreach-}
					<option disabled>----</option>
				{-foreach name=eve key=key item=item from=$eveuserl-}
					<option value="{-$key-}" onMouseOver="showtip('{-$item[1]-}');" {-if $item[3]-}selected{-/if-}>{-$item[0]-}</option>
				{-/foreach-}
			</select>
			<br /><br />
			<b onMouseOver="showtip('{-$eve.EventDuration[2]-}');">{-$eve.EventDuration[0]-}</b><br />
			<input id="EventDuration" name="D_EventDuration" type="text" class="line fixw"
				onFocus="showtip('{-$eve.EventDuration[2]-}');" value="{-$qd.D_EventDuration-}" />
			<br />
			<b onMouseOver="showtip('{-$eve.EventNotes[2]-}');">{-$eve.EventNotes[0]-}</b>
			<textarea id="EventNotes" name="D_EventNotes[1]" style="width:250px; height:40px;"
				class="inputText" onFocus="showtip('{-$eve.EventNotes[2]-}');">{-$qd.D_EventNotes[1]-}</textarea>
		</dd>
			
		<!-- BEGIN CAUSE SECTION -->
		<dt>{-#mcausection#-}</dt>
		<dd>
			<input type="hidden" name="QueryCause[OP]" value="AND" />
			<span class="dlgmsg">{-#tcntclick#-}</span><br />
			<select id="qcaulst" name="D_CauseId[]" multiple style="width: 250px; height: 200px;" class="line">
				{-include file="block_causelist.tpl"-}
			</select>
			<br />
			<b onMouseOver="showtip('{-$cau.CauseNotes[2]-}');">{-$cau.CauseNotes[0]-}</b>
			<br />
			<textarea name="D_CauseNotes[1]" style="width:250px; height: 40px;"
				class="inputText" onFocus="showtip('{-$cau.CauseNotes[2]-}');">{-$qd.D_CauseNotes[1]-}</textarea>
		</dd>
		
		<!-- BEGIN QUERY EFFECTS SECTION -->
		<dt>{-#meffsection#-}</dt>
		<dd>
			<p align="right">{-#msgOperator#-}
			<select name="QueryEffect[OP]" class="dlgmsg small line">
				<option class="small" value="AND" {-if $qd.QueryEffect[OP] == 'AND'-}selected{-/if-}>{-#tand#-}</option>
				<option class="small" value="OR"  {-if $qd.QueryEffect[OP] == 'OR'-}selected{-/if-}>{-#tor#-}</option>
			</select>
			</p>
			<b>{-#ttitegp#-}</b><br />
			<div style="height: 100px;" class="dwin">
				<table border="0" cellpadding="0" cellspacing="0">
					{-foreach name=ef1 key=key item=item from=$ef1-}
						{-assign var="ff" value="D_$key"-}
						<tr>
							<td valign="top">
								<input type="checkbox" onFocus="showtip('{-$item[2]-}');" id="{-$key-}"
									onclick="enadisEff('{-$key-}', this.checked);" {-if $qd.$ff[0] != ''-}checked{-/if-} />
								<label for="{-$key-}" onMouseOver="showtip('{-$item[2]-}');">{-$item[0]-}</label>
								<span id="o{-$key-}" style="display:none">
									<select id="{-$key-}[0]" name="D_{-$key-}[0]" class="small line" disabled
										onChange="showeff(this.value, 'x{-$key-}', 'y{-$key-}');">
										<option class="small" value="-1" {-if $qd.$ff[0] == '-1'-}selected{-/if-}>{-#teffhav#-}</option>
										<option class="small" value="0"  {-if $qd.$ff[0] == '0'-}selected{-/if-}>{-#teffhavnot#-}</option>
										<option class="small" value="-2" {-if $qd.$ff[0] == '-2'-}selected{-/if-}>{-#teffdontknow#-}</option>
										<option class="small" value=">=" {-if $qd.$ff[0] == '>='-}selected{-/if-}>{-#teffmajor#-}</option>
										<option class="small" value="<=" {-if $qd.$ff[0] == '<='-}selected{-/if-}>{-#teffminor#-}</option>
										<option class="small" value="="  {-if $qd.$ff[0] == '='-}selected{-/if-}>{-#teffequal#-}</option>
										<option class="small" value="-3" {-if $qd.$ff[0] == '-3'-}selected{-/if-}>{-#teffbetween#-}</option>
									</select>
									<span id="x{-$key-}" style="display:none"><br />
										<input type="text" id="{-$key-}[1]" name="D_{-$key-}[1]" size="3" class="line"
											value="{-if $qd.$ff[1] != ''-}{-$qd.$ff[1]-}{-else-}1{-/if-}" />
									</span>
									<span id="y{-$key-}" style="display:none">{-#tand#-}
										<input type="text" id="{-$key-}[2]" name="D_{-$key-}[2]" size="3" class="line"
											value="{-if $qd.$ff[1] != ''-}{-$qd.$ff[2]-}{-else-}10{-/if-}" />
									</span>
								</span>
							</td>
						</tr>
					{-/foreach-}
				</table>
			</div>
			<br />
			
			<!-- SECTORS -->
			<b>{-#ttiteis#-}</b><br />
			<div style="height: 80px;" class="dwin">
				<table border="0" cellpadding="0" cellspacing="0">
					{-foreach name=sec key=key item=item from=$sec-}
						{-assign var="ff" value="D_$key"-}
						<tr>
							<td valign="top">
								<input type="checkbox" onFocus="showtip('{-$item[2]-}');" id="{-$key-}"
									onclick="{-foreach name=sc2 key=k item=i from=$item[3]-}enadisEff('{-$k-}', this.checked);{-/foreach-}enadisEff('{-$key-}', this.checked);"
									{-if $qd.$ff[0] != ''-}checked{-/if-} />
								<label for="{-$key-}" onMouseOver="showtip('{-$item[2]-}');">{-$item[0]-}</label>
								<span id="o{-$key-}" style="display:none">
									<select id="{-$key-}[0]" name="D_{-$key-}[0]" class="small line" disabled>
										<option class="small" value="-1" selected>{-#teffhav#-}</option>
										<option class="small" value="0"  {-if $qd.$ff[0] == '0'-}selected{-/if-}>{-#teffhavnot#-}</option>
										<option class="small" value="-2" {-if $qd.$ff[0] == '-2'-}selected{-/if-}>{-#teffdontknow#-}</option>
									</select>
									{-foreach name=sc2 key=k item=i from=$item[3]-}
										{-assign var="ff" value="D_$k"-}
										<span id="o{-$k-}" style="display:none">
											<br />{-$i-}
											<select id="{-$k-}[0]" name="D_{-$k-}[0]" onChange="showeff(this.value, 'x{-$k-}', 'y{-$k-}');" 
												class="small line" disabled>
												<option class="small" value=" "></option>
												<option class="small" value=">=" {-if $qd.$ff[0] == '>='-}selected{-/if-}>{-#teffmajor#-}</option>
												<option class="small" value="<=" {-if $qd.$ff[0] == '<='-}selected{-/if-}>{-#teffminor#-}</option>
												<option class="small" value="="  {-if $qd.$ff[0] == '='-}selected{-/if-}>{-#teffequal#-}</option>
												<option class="small" value="-3" {-if $qd.$ff[0] == '-3'-}selected{-/if-}>{-#teffbetween#-}</option>
											</select>
											<span id="x{-$k-}" style="display:none">
												<input type="text" id="{-$k-}[1]" name="D_{-$k-}[1]" size="3" class="line"
													value="{-if $qd.$ff[1] != ''-}{-$qd.$ff[1]-}{-else-}1{-/if-}" />
											</span>
											<span id="y{-$k-}" style="display:none">{-#tand#-}
												<input type="text" id="{-$k-}[2]" name="D_{-$k-}[2]" size="3" class="line"
													value="{-if $qd.$ff[1] != ''-}{-$qd.$ff[2]-}{-else-}10{-/if-}" />
											</span>
											<br />
										</span>
									{-/foreach-}
								</span>
							</td>
						</tr>
					{-/foreach-}
				</table>
			</div>
			<br />
			
			<!-- Losses -->
			<b>{-#ttitloss#-}</b><br />
			{-foreach name=ef3 key=k item=i from=$ef3-}
				{-assign var="ff" value="D_$k"-}
				<input type="checkbox" onFocus="showtip('{-$i[2]-}');" id="{-$k-}"
					onclick="enadisEff('{-$k-}', this.checked);" {-if $qd.$ff[0] != ''-}checked{-/if-} />
				<label for="{-$k-}" onMouseOver="showtip('{-$i[2]-}');">{-$i[0]-}</label>
				<span id="o{-$k-}" style="display:none">
					<select id="{-$k-}[0]" name="D_{-$k-}[0]" onChange="showeff(this.value, 'x{-$k-}', 'y{-$k-}');" class="small line" disabled>
						<option class="small" value=" "></option>
						<option class="small" value=">=" {-if $qd.$ff[0] == '>='-}selected{-/if-}>{-#teffmajor#-}</option>
						<option class="small" value="<=" {-if $qd.$ff[0] == '<='-}selected{-/if-}>{-#teffminor#-}</option>
						<option class="small" value="="  {-if $qd.$ff[0] == '='-}selected{-/if-}>{-#teffequal#-}</option>
						<option class="small" value="-3" {-if $qd.$ff[0] == '-3'-}selected{-/if-}>{-#teffbetween#-}</option>
					</select>
					<span id="x{-$k-}" style="display:none"><br />
						<input type="text" id="{-$k-}[1]" name="D_{-$k-}[1]" size="5" class="line"
							value="{-if $qd.$ff[1] != ''-}{-$qd.$ff[1]-}{-else-}1{-/if-}" />
					</span>
					<span id="y{-$k-}" style="display:none">{-#tand#-}
						<input type="text" id="{-$k-}[2]" name="D_{-$k-}[2]" size="5" class="line" 
							value="{-if $qd.$ff[1] != ''-}{-$qd.$ff[2]-}{-else-}10{-/if-}" />
					</span>
				</span>
				<br />
			{-/foreach-}
			{-foreach name=ef4 key=k item=i from=$ef4-}
				{-assign var="ff" value="D_$k"-}
				<b onMouseOver="showtip('{-$i[2]-}');">{-$i[0]-}</b><br />
				<input type="text" id="{-$k-}" name="D_{-$k-}" class="fixw line" value="{-$qd.$ff[1]-}" onFocus="showtip('{-$i[2]-}');" />
				<br />
			{-/foreach-}
		</dd>
		<!-- END QUERY EFFECTS SECTION -->
		
		<!-- Begin EEField Section -->
		<dt>{-#mextsection#-}</dt>
		<dd>
			<p align="right">{-#msgOperator#-}
			<select name="QueryEEField[OP]" class="dlgmsg small line">
				<option class="small" value="AND" {-if $qd.QueryEEField[OP] == 'AND'-}selected{-/if-}>{-#tand#-}</option>
				<option class="small" value="OR"  {-if $qd.QueryEEField[OP] == 'OR'-}selected{-/if-}>{-#tor#-}</option>
			</select>
			</p>
			<div style="height: 300px;" class="dwin">
				<table border=0 cellpadding=0 cellspacing=0>
					{-foreach name=eef key=key item=item from=$EEFieldList-}
						<tr>
							<td valign="top">
								{-if $item[2] == "INTEGER" || $item[2] == "DOUBLE" || $item[2] == "CURRENCY" -}
									<input type="checkbox" onFocus="showtip('{-$item[1]-}');" id="{-$key-}" 
										onclick="enadisEff('{-$key-}', this.checked);" />
									<label for="{-$key-}" onMouseOver="showtip('{-$item[1]-}');">{-$item[0]-}</label>
									<span id="o{-$key-}" style="display:none">
										<select id="{-$key-}[0]" name="EEFieldQuery[{-$key-}][Operator]" onChange="showeff(this.value, 'x{-$key-}', 'y{-$key-}');" 
											class="small" disabled>
											<option class="small" value=""></option>
											<option class="small" value=">=">{-#teffmajor#-}</option>
											<option class="small" value="<=">{-#teffminor#-}</option>
											<option class="small" value="=">{-#teffequal#-}</option>
											<option class="small" value="-3">{-#teffbetween#-}</option>
										</select>
										<input type="hidden" name="EEFieldQuery[{-$key-}][Type]" value="{-$item[2]-}" />
										<span id="x{-$key-}" style="display:none"><br />
											<input type="text" id="{-$key-}[1]" name="EEFieldQuery[{-$key-}][Value1]" size="3" value="1" class="line" />
										</span>
										<span id="y{-$key-}" style="display:none">
											{-#tand#-} <input type="text" id="{-$key-}[2]" name="EEFieldQuery[{-$key-}][Value2]" size="3" value="10" class="line" />
										</span>
									</span>
								{-/if-}
								{-if $item[2] == "TEXT" || $item[2] == "DATE" -}
									{-$item[0]-}<br />
									<input type="text" id="{-$key-}" name="EEFieldQuery[{-$key-}][Value]" style="width: 290px;" class="line"
										onFocus="showtip('{-$item[1]-}');" /><br />
									<input type="hidden" name="EEFieldQuery[{-$key-}][Type]" value="{-$item[2]-}" />
								{-/if-}
							</td>
						</tr>
					{-/foreach-}
				</table>
			</div>
		</dd>
		<!-- END EEField Section -->
		
		<!-- BEGIN DATETIME SECTION -->
		<dt>{-#mdcsection#-}</dt>
		<dd class="default">
			<div style="height: 250px;">
				<b onMouseOver="showtip('{-$dis.DisasterBeginTime[2]-}');">{-#tdate#-}</b>
				<span class="dlgmsg">{-#tdateformat#-}</span><br />
				<table border="0">
					<tr>
						<td><b>{-#ttitsince#-}:</b></td>
						<td>
							<input type="text" id="queryBeginYear" name="D_DisasterBeginTime[]" size=4 maxlength=4 class="line" 
								value="{-if $qd.D_DisasterBeginTime[0] != ''-}{-$qd.D_DisasterBeginTime[0]-}{-else-}{-$yini-}{-/if-}" />
							<input type="text" id="queryBeginMonth" name="D_DisasterBeginTime[]" size=2 maxlength=2 class="line"
								value="{-$qd.D_DisasterBeginTime[1]-}" />
							<input type="text" id="queryBeginDay" name="D_DisasterBeginTime[]" size=2 maxlength=2 class="line"
								value="{-$qd.D_DisasterBeginTime[2]-}" />
						</td>
					</tr>
					<tr>
						<td><b>{-#ttituntil#-}:</b></td>
						<td>
							<input type="text" id="queryEndYear" name="D_DisasterEndTime[]" size=4 maxlength=4 class="line" 
								value="{-if $qd.D_DisasterEndTime[0] != ''-}{-$qd.D_DisasterEndTime[0]-}{-else-}{-$yend-}{-/if-}" />
							<input type="text" id="queryEndMonth" name="D_DisasterEndTime[]" size=2 maxlength=2 class="line"
								value="{-$qd.D_DisasterEndTime[1]-}" />
							<input type="text" id="queryEndDay" name="D_DisasterEndTime[]" size=2 maxlength=2 class="line"
								value="{-$qd.D_DisasterEndTime[2]-}" />
						</td>
					</tr>
				</table>
				<br />
				<b onMouseOver="showtip('{-$dis.DisasterSource[2]-}');">{-$dis.DisasterSource[0]-}</b>
				<select name="D_DisasterSource[0]" class="dlgmsg small line">
					<option class="small" value="AND" {-if $qd.D_DisasterSource[0] == 'AND'-}selected{-/if-}>{-#tand#-}</option>
					<option class="small" value="OR"  {-if $qd.D_DisasterSource[0] == 'OR'-}selected{-/if-}>{-#tor#-}</option>
				</select>
				<br />
				<textarea id="txtDisasterSource" name="D_DisasterSource[1]" style="width:220px; height:40px;" 
					class="inputText" onFocus="showtip('{-$dis.DisasterSource[2]-}');">{-$qd.D_DisasterSource[1]-}</textarea>
				<br />
				<div id="divQueryRecordStatus">
					<b onMouseOver="showtip('');">{-#tdcstatus#-}</b><br />
					<select id="fldQueryRecordStatus" name="D_RecordStatus[]" multiple class="fixw line">
						<option value="PUBLISHED" selected>{-#tdcpublished#-}</option>
						<option value="READY"     selected>{-#tdcready#-}    </option>
						<option value="DRAFT"             >{-#tdcdraft#-}    </option>
						<option value="TRASH"             >{-#tdctrash#-}    </option>
						<option value="DELETED"           >{-#tdcdeleted#-}  </option>
					</select>
				<br />
				</div>
				<b onMouseOver="showtip('{-#tserialmsg#-}');">{-#tserial#-}</b>
				<select name="D_DisasterSerial[0]" class="small line">
					<option class="small" value=""  {-if $qd.D_DisasterSerial[0] == ''-}selected{-/if-}>{-#tonly#-}</option>
					<option class="small" value="NOT" {-if $qd.D_DisasterSerial[0] == 'NOT'-}selected{-/if-}>{-#texclude#-}</option>
					<option class="small" value="INCLUDE" {-if $qd.D_DisasterSerial[0] == 'INCLUDE'-}selected{-/if-}>{-#tinclude#-}</option>
				</select>
				<br />
				<input type="text" name="D_DisasterSerial[1]" class="line fixw" value="{-$qd.D_DisasterSerial[1]-}" />
			</div>
		</dd>
		<!-- END DATETIME SECTION -->
		
		<!-- BEGIN CUSTOMQUERY SECTION -->
		<dt>{-#madvsection#-}</dt>
		<dd>
			<textarea id="QueryCustom" name="QueryCustom" style="width:300px; height:45px;" 
				class="inputText" onFocus="showtip('');">{-$qd.QueryCustom-}</textarea>
			<br />
			<span class="dlgmsg">{-#tadvqryhelp#-}</span>
			<br />
			<table border="0" width="100%">
				<tr valign="top">
					<td>
						<div style="height:180px;" class="dwin">
							<input type="button" class="CustomQueryListItem" value="{-$dis.DisasterSerial[0]-}" onClick="setAdvQuery('DisasterSerial', 'text')" /><br />
							<input type="button" class="CustomQueryListItem" value="{-$dis.DisasterBeginTime[0]-}" onClick="setAdvQuery('DisasterBeginTime', 'date')" /><br />
							<input type="button" class="CustomQueryListItem" value="{-$dis.DisasterSiteNotes[0]-}" onClick="setAdvQuery('DisasterSiteNotes', 'text')" /><br />
							<input type="button" class="CustomQueryListItem" value="{-$eve.EventDuration[0]-}" onClick="setAdvQuery('EventDuration', 'text')" /><br />
							<input type="button" class="CustomQueryListItem" value="{-$eve.EventNotes[0]-}" onClick="setAdvQuery('EventNotes', 'text')" /><br />
							<input type="button" class="CustomQueryListItem" value="{-$cau.CauseNotes[0]-}" onClick="setAdvQuery('CauseNotes', 'text')" /><br />
							<input type="button" class="CustomQueryListItem" value="{-$rc2.RecordAuthor[0]-}" onClick="setAdvQuery('RecordAuthor', 'text')" /><br />
							<input type="button" class="CustomQueryListItem" value="{-$rc2.RecordCreation[0]-}" onClick="setAdvQuery('RecordCreation','date')" /><br />
							<input type="button" class="CustomQueryListItem" value="{-$rc2.RecordUpdate[0]-}" onClick="setAdvQuery('RecordUpdate','date')" /><br />
							<hr />
							{-foreach name=ef1 key=key item=item from=$ef1-}
								<input type="button" class="CustomQueryListItem" value="{-$item[0]-}" onClick="setAdvQuery('{-$key-}','number')" /><br />
							{-/foreach-}
							<hr />
							{-foreach name=sec key=key item=item from=$sec-}
								<input type="button" class="CustomQueryListItem" value="{-$item[0]-}" onClick="setAdvQuery('{-$key-}','boolean')" /><br />
							{-/foreach-}
							<hr />
							{-foreach name=ef3 key=key item=item from=$ef3-}
								<input type="button" class="CustomQueryListItem" value="{-$item[0]-}" onClick="setAdvQuery('{-$key-}','number')" /><br />
							{-/foreach-}
							<hr />
							{-foreach name=ef4 key=key item=item from=$ef4-}
								<input type="button" class="CustomQueryListItem" value="{-$item[0]-}" onClick="setAdvQuery('{-$key-}','text')" /><br />
							{-/foreach-}
							<hr />
							{-foreach name=eef key=key item=item from=$EEFieldList-}
								<input type="button" class="CustomQueryListItem" value="{-$item[0]-}" onClick="setAdvQuery('{-$key-}','date')" /><br />
							{-/foreach-}
						</div>
					</td>
					<td align="center">
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
