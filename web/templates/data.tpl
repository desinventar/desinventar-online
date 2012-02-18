{-config_load file="$lg.conf" section="dc_data"-}
{-config_load file="$lg.conf" section="dc_qdetails"-}
{-if $ctl_showres-}
	<table width="920" class="grid">
		<tr>
			<td colspan="3">
				<div style="height:40px;" class="dwin">
					{-foreach key=k item=i from=$qdet-}
						{-if $k == "GEO"-}<b>{-#geo#-}:</b> {-$i-}; {-/if-}
						{-if $k == "EVE"-}<b>{-#eve#-}:</b> {-$i-}; {-/if-}
						{-if $k == "CAU"-}<b>{-#cau#-}:</b> {-$i-}; {-/if-}
						{-if $k == "EFF"-}<b>{-#eff#-}:</b> {-$i-}; {-/if-}
						{-if $k == "BEG"-}<b>{-#beg#-}:</b> {-$i-}; {-/if-}
						{-if $k == "END"-}<b>{-#end#-}:</b> {-$i-}; {-/if-}
						{-if $k == "SOU"-}<b>{-#sou#-}:</b> {-$i-}; {-/if-}
						{-if $k == "SER"-}<b>{-#ser#-}:</b> {-$i-}; {-/if-}
					{-/foreach-}
				</div>
			</td>
		</tr>
		<tr>
			<td>
				{-#tpage#-}
				<input type="text" id="DataCurPage" size="2" value="1" class="line"  />
				&nbsp; {-#msgData_PageOf#-} &nbsp; {-$NumberOfPages-}
				&nbsp;&nbsp;|&nbsp;&nbsp;
				<a class="button" id="btnGridGotoFirstPage"><span>&lt;&lt;</span></a>
				<a class="button" id="btnGridGotoPrevPage"><span>&lt;</span></a>
				<a class="button" id="btnGridGotoNextPage"><span>&gt;</span></a>
				<a class="button" id="btnGridGotoLastPage"><span>&gt;&gt;</span></a>
			</td>
			<td align="center">
				<span id="datstatusmsg" class="dlgmsg"></span>
			</td>
			<td align="right">
				{-#trepnum#-}: {-$tot-}
			</td>
		</tr>
	</table>
	<table width="930" class="col">
		<thead>
			<tr>
				<th class="header">{-#trow#-}</th>
				{-foreach name=sel key=key item=item from=$sel-}
					{-strip-}
						{-if $item != "DisasterId"-}
							<th class="header">{-$dk.$item-}
							</th>
						{-/if-}
					{-/strip-}
				{-/foreach-}
			</tr>
		</thead>
		<tbody id="tblDataRows">
{-/if-}
{-*** SHOW RESULT LIST: PAGING ***-}
{-if $ctl_dislist-}
			{-foreach name=dl key=key item=item from=$dislist-}
				<tr class="ViewData">
					<td>
						<a href="#" class="linkGridGotoCard" 
							disasterid="{-$item.DisasterId-}"
							rowindex="{-$smarty.foreach.dl.iteration-}">{-$offset+$smarty.foreach.dl.iteration-}</a>
					</td>
					{-foreach name=sel key=k item=i from=$sel-}
						{-strip-}
							{-if $i != "DisasterId"-}
								<td {-if $i=="DisasterSerial" || $i=="DisasterBeginTime" || $i=="EventName" || $i=="GeographyFQName" || 
								         $i=="DisasterSiteNotes" || $i=="DisasterSource" || $i=="EffectNotes" || $i=="EffectOtherLosses" || $i=="CauseName" || $i=="CauseNotes"-}
								         class="GridCellText"
									{-else-}
										class="GridCellNumber"
									{-/if-}>
									{-if $i=="EffectNotes" || $i=="EffectOtherLosses" || $i=="EventNotes" || $i=="CauseNotes"-}
										<div class="dwin" style="width:200px; height: 40px;">{-$item[$i]-}
										</div>
									{-elseif $i=="DisasterSource" || $i=="DisasterSiteNotes"-}
										<div class="dwin" style="width:150px; height: 40px;">{-$item[$i]-}
										</div>
									{-elseif $item[$i] == -1-}
										<input type="checkbox" checked disabled />
									{-elseif $item[$i] == -2-}?
									{-else-}
										{-$item[$i]-}
									{-/if-}
								</td>
							{-/if-}
						{-/strip-}
					{-/foreach-}
				</tr>
			{-/foreach-}
{-/if-}
{-if $ctl_showres-}
		</tbody>
	</table>
	<div style="display:none;">
		<input type="hidden" id="prmDataPageUpdate"  value="0"                   />
		<input type="hidden" id="prmDataPageNumber"  value="1"                   />
		<input type="hidden" id="prmDataPageRecords" value="0"                   />
		<input type="hidden" id="prmDataPageSize"    value="{-$RecordsPerPage-}" />
		<input type="hidden" id="prmDataPageCount"   value="{-$NumberOfPages-}"  />
		<input type="hidden" id="prmDataQueryDef"    value="{-$sql-}"            />
		<input type="hidden" id="prmDataFieldList"   value="{-$fld-}"            />
	</div>
	<div id="divDataQueryDetails" style="display:none;">
		{-foreach key=k item=i from=$qdet-}
			{-if $k == "GEO"-}<b>{-#geo#-}:</b> {-$i-}; {-/if-}
			{-if $k == "EVE"-}<b>{-#eve#-}:</b> {-$i-}; {-/if-}
			{-if $k == "CAU"-}<b>{-#cau#-}:</b> {-$i-}; {-/if-}
			{-if $k == "EFF"-}<b>{-#eff#-}:</b> {-$i-}; {-/if-}
			{-if $k == "BEG"-}<b>{-#beg#-}:</b> {-$i-}; {-/if-}
			{-if $k == "END"-}<b>{-#end#-}:</b> {-$i-}; {-/if-}
			{-if $k == "SOU"-}<b>{-#sou#-}:</b> {-$i-}; {-/if-}
			{-if $k == "SER"-}<b>{-#ser#-}:</b> {-$i-}; {-/if-}
		{-/foreach-}
	</div>
{-/if-}
