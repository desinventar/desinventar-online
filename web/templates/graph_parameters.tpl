<!-- BEGIN GRAPHIC PARAMETERS -->
<button id="grp-btn" class="rounded" ext:qtip="{-#tgraphicmsg#-}"><span>{-#bgraphic#-}</span></button>
<div id="divGraphParameters" class="x-hidden">
	<div class="x-window-header">{-#bgraphic#-}</div>
	<div id="grp-cfg">
		<form id="CG" method="POST">
			<table class="conf" cellpadding=1 cellspacing=1>
			<tr valign="top">
				<td colspan=3 align="center">
					<b>{-#gopttitle#-}</b><input type="text" name="prmGraph[Title]" class="line fixw" />
					<!--<b>{-#goptsubtit#-}</b><br>-->
				</td>
			</tr>
			<tr valign="top">
				<td id="tdGraphParamAxis1" align="right">
					<u>{-#gveraxis#-} 1:</u><br>
					<b onMouseOver="showtip('{-$dic.GraphField[2]-}');">{-$dic.GraphField[0]-}</b><br>
					<select id="prmGraphField0" name="prmGraph[Field][0]" onMouseOver="showtip('{-$dic.GraphField[2]-}');" class="line">
						<option value="D.DisasterId||" selected>{-$dic.GraphDisasterId_[0]-}</option>
					{-foreach name=ef1 key=k item=i from=$ef1-}
						<option value="D.{-$k-}Q|>|-1">{-$i[0]-}</option>
						<option value="D.{-$k-}|=|-1">{-#tauxhave#-} {-$i[0]-}</option>
					{-/foreach-}
					{-foreach name=ef2 key=k item=i from=$ef2-}
						<option value="D.{-$k-}|>|-1">{-$i[0]-}</option>
					{-/foreach-}
					{-foreach name=ef3 key=k item=i from=$ef3-}
						<option value="D.{-$k-}|>|-1">{-$i[0]-}</option>
					{-/foreach-}
					{-foreach name=sec key=k item=i from=$sec-}
						<option value="D.{-$k-}|=|-1">{-#tauxaffect#-} {-$i[0]-}</option>
					{-/foreach-}
						<option disabled>___</option>
					{-foreach name=eef key=k item=i from=$EEFieldList-}
						{-if $i[2] == "INTEGER" || $i[2] == "DOUBLE"-}
						<option value="E.{-$k-}|>|-1">{-$i[0]-}</option>
						{-/if-}
					{-/foreach-}
					</select>
					<br />
					<b onMouseOver="showtip('{-$dic.GraphScale[2]-}');">{-$dic.GraphScale[0]-}</b><br />
					<select id="prmGraphScale0" name="prmGraph[Scale][0]" onMouseOver="showtip('{-$dic.GraphScale[2]-}');" class="line">
						<option value="textint" selected>{-#gscalin#-}</option>
						<option value="textlog">{-#gscalog#-}</option>
					</select>
					<br />
					<b onMouseOver="showtip('{-$dic.GraphShow[2]-}');">{-$dic.GraphShow[0]-}</b><br />
					<select id="prmGraphData0" name="prmGraph[Data][0]" onMouseOver="showtip('{-$dic.GraphShow[2]-}');" class="line">
						<option value="VALUE">{-#gshwval#-}</option>
						<option id="_G+D_perc" value="PERCENT" disabled>{-#gshwperce#-}</option>
						<option id="_G+D_none" value="NONE" selected>{-#gshwnone#-}</option>
					</select>
					<br />
					<b onMouseOver="showtip('{-$dic.GraphMode[2]-}');">{-$dic.GraphMode[0]-}</b><br/>
					<select id="prmGraphMode0" name="prmGraph[Mode][0]" onMouseOver="showtip('{-$dic.GraphMode[2]-}');" class="line">
						<option value="NORMAL" selected>{-#gmodnormal#-}</option>
						<option id="_G+M_accu" value="ACCUMULATE">{-#gmodaccumul#-}</option>
						<option id="_G+M_over" value="OVERCOME" disabled>{-#gmodovercome#-}</option>
					</select>
					<br />
					<b>{-#gtendline#-}</b><br/>
					<select id="prmGraphTendency0" name="prmGraph[Tendency][0]" class="line">
						<option value="" selected></option>
						<option value="LINREG">{-#glinearreg#-}</option>
					</select>
				</td>
				<td id="tdGraphParamCenter" align="center">
					<table border="1" width="100%" height="100%">
					<tr valign="center">
						<td align="center">
							<!--<b onMouseOver="showtip('{-$dic.GraphKind[2]-}');">{-$dic.GraphKind[0]-}</b><br>-->
							<select id="prmGraphKind" name="prmGraph[Kind]" size="3"
								onMouseOver="showtip('{-$dic.GraphKind[2]-}');" class="line">
								<option value="BAR" selected>{-#gkndbars#-}</option>
								<option id="_G+K_line" value="LINE">{-#gkndlines#-}</option>
								<option id="_G+K_pie" value="PIE" disabled>{-#gkndpie#-}</option>
							</select>
							<br /><br />
							<!--<b onMouseOver="showtip('{-$dic.GraphFeel[2]-}');">{-$dic.GraphFeel[0]-}</b><br>-->
							<select id="prmGraphFeel" name="prmGraph[Feel]" size="2" onMouseOver="showtip('{-$dic.GraphFeel[2]-}');" class="line">
								<option value="2D">{-#gfee2d#-}</option>
								<option value="3D" selected>{-#gfee3d#-}</option>
							</select>
						</td>
					</tr>
					</table>
				</td>
				<td id="tdGraphParamAxis2">
					<div id="divVerticalAxis2">
						<u>{-#gveraxis#-} 2:</u><br />
						<b onMouseOver="showtip('{-$dic.GraphField[2]-}');">{-$dic.GraphField[0]-}</b><br />
						<select id="prmGraphField1" name="prmGraph[Field][1]" size="1" onMouseOver="showtip('{-$dic.GraphField[2]-}');"
							onChange="enab($('prmGraphScale1')); enab($('prmGraphData1')); enab($('prmGraphMode1'));" class="line">
							<option value="" selected></option>
							<option value="D.DisasterId||">{-$dic.GraphDisasterId_[0]-}</option>
							{-foreach name=ef1 key=k item=i from=$ef1-}
								<option value="D.{-$k-}Q|>|-1">{-$i[0]-}</option>
								<option value="D.{-$k-}|=|-1">{-#tauxhave#-} {-$i[0]-}</option>
							{-/foreach-}
							{-foreach name=ef2 key=k item=i from=$ef2-}
								<option value="D.{-$k-}|>|-1">{-$i[0]-}</option>
							{-/foreach-}
							{-foreach name=ef3 key=k item=i from=$ef3-}
								<option value="D.{-$k-}|>|-1">{-$i[0]-}</option>
							{-/foreach-}
							{-foreach name=ef3 key=k item=i from=$sec-}
								<option value="D.{-$k-}|=|-1">{-#tauxaffect#-} {-$i[0]-}</option>
							{-/foreach-}
							<option disabled>___</option>
							{-foreach name=eef key=k item=i from=$EEFieldList-}
								{-if $i[2] == "INTEGER" || $i[2] == "DOUBLE"-}
									<option value="E.{-$k-}|>|-1">{-$i[0]-}</option>
								{-/if-}
							{-/foreach-}
						</select>
						<br />
						<b onMouseOver="showtip('{-$dic.GraphScale[2]-}');">{-$dic.GraphScale[0]-}</b><br />
						<select id="prmGraphScale1" name="prmGraph[Scale][1]" class="disabled line" disabled
							onMouseOver="showtip('{-$dic.GraphScale[2]-}');">
							<option value="int" selected>{-#gscalin#-}</option>
							<option value="log">{-#gscalog#-}</option>
						</select>
						<br />
						<b onMouseOver="showtip('{-$dic.GraphShow[2]-}');">{-$dic.GraphShow[0]-}</b><br />
						<select id="prmGraphData1" name="prmGraph[Data][1]" class="disabled line" disabled 
							onMouseOver="showtip('{-$dic.GraphShow[2]-}');">
							<option value="VALUE">{-#gshwval#-}</option>
							<option id="_G+D_perc2" value="PERCENT" disabled>{-#gshwperce#-}</option>
							<option id="_G+D_none2" value="NONE" selected>{-#gshwnone#-}</option>
						</select>
						<br />
						<b onMouseOver="showtip('{-$dic.GraphMode[2]-}');">{-$dic.GraphMode[0]-}</b><br />
						<select id="prmGraphMode1" name="prmGraph[Mode][1]" class="disabled line" disabled
							onMouseOver="showtip('{-$dic.GraphMode[2]-}');">
							<option value="NORMAL" selected>{-#gmodnormal#-}</option>
							<option id="_G+M_accu2" value="ACCUMULATE">{-#gmodaccumul#-}</option>
							<option id="_G+M_over2" value="OVERCOME" disabled>{-#gmodovercome#-}</option>
						</select>
					</div>
				</td>
			</tr>
			<tr>
				<td></td>
				<td>
					<table border=0 height="100%">
					<tr>
						<td colspan="2" align="center">
							<p><u>{-#ghoraxis#-}:</u></p>
						</td>
					</tr>
					<tr>
						<td>
							<b>{-#ghistogram#-}</b>
						</td>
						<td>
							<select id="prmGraphTypeHistogram" name="prmGraphTypeHistogram" class="line"
								onMouseOver="showtip('{-$dic.GraphType[2]-}');">
								<option value="" disabled></option>
								<option value="D.DisasterBeginTime">{-$dic.GraphHisTemporal[0]-}</option>
								<option value="D.DisasterBeginTime|D.EventId">{-$dic.GraphHisEveTemporal[0]-}</option>
								{-foreach name=glev key=k item=i from=$glev-}
								<option value="D.DisasterBeginTime|D.GeographyId_{-$k-}">{-$i[0]-} {-$dic.GraphHisGeoTemporal[0]-}</option>
								{-/foreach-}
								<option value="D.DisasterBeginTime|D.CauseId">{-$dic.GraphHisCauTemporal[0]-}</option>
							</select>
						</td>
					</tr>
					<tr>
						<td>
							<b onMouseOver="showtip('{-$dic.GraphPeriod[2]-}');">{-$dic.GraphPeriod[0]-}
						</td>
						<td>
							<select id="prmGraphPeriod" name="prmGraph[Period]" class="line"
								onMouseOver="showtip('{-$dic.GraphPeriod[2]-}');">
								<option value=""></option>
								<option value="YEAR" selected>{-#gperannual#-}</option>
								<option value="YMONTH">{-#gpermonth#-}</option>
								<option value="YWEEK">{-#gperweek#-}</option>
								<option value="YDAY">{-#gperday#-}</option>
							</select>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<b onMouseOver="showtip('{-$dic.GraphSeaHistogram[2]-}');">{-#GHISTOANNUAL#-}</b>
							<select id="prmGraphStat" name="prmGraph[Stat]" class="line"
								onMouseOver="showtip('{-$dic.GraphSeaHistogram[2]-}');">
								<option value=""></option>
								<option value="DAY">{-#gseaday#-}</option>
								<option value="WEEK">{-#gseaweek#-}</option>
								<option value="MONTH">{-#gseamonth#-}</option>
							</select>
						</td>
					</tr>
					<tr>
						<td>
							<b>{-#gcomparative#-}</b>
						</td>
						<td>
							<select id="prmGraphTypeComparative" name="prmGraphTypeComparative" class="line"
								onMouseOver="showtip('{-$dic.GraphType[2]-}');">
								<option value="" disabled></option>
								<option value="D.EventId">{-$dic.GraphComByEvents[0]-}</option>
								<option value="D.CauseId">{-$dic.GraphComByCauses[0]-}</option>
								{-foreach name=glev key=k item=i from=$glev-}
								<option value="D.GeographyId_{-$k-}">{-$dic.GraphComByGeography[0]-} {-$i[0]-}</option>
								{-/foreach-}
							</select>
						</td>
					</tr>
					</table>
				</td>
				<td></td>
			</tr>
			</table>
			<input type="hidden" id="_G+cmd" name="_G+cmd" value="result" />
			<input type="hidden" id="prmGraphType" name="prmGraph[Type]"     value="" />
			<input type="hidden" id="prmGraphVar"  name="prmGraph[Variable]" value="D.DisasterBeginTime" />
		</form>
	</div>
</div>
