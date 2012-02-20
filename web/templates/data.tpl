{-config_load file="$lg.conf" section="dc_data"-}
{-config_load file="$lg.conf" section="dc_qdetails"-}
{-if $ctl_showres-}
	<table width="920" class="grid">
		<tr>
			<td colspan="3">
				<div style="height:40px;" class="dwin">
					{-foreach $qdet as $key => $value-}
						{-if $key == "GEO"-}<b>{-#geo#-}:</b> {-$value-}; {-/if-}
						{-if $key == "EVE"-}<b>{-#eve#-}:</b> {-$value-}; {-/if-}
						{-if $key == "CAU"-}<b>{-#cau#-}:</b> {-$value-}; {-/if-}
						{-if $key == "EFF"-}<b>{-#eff#-}:</b> {-$value-}; {-/if-}
						{-if $key == "BEG"-}<b>{-#beg#-}:</b> {-$value-}; {-/if-}
						{-if $key == "END"-}<b>{-#end#-}:</b> {-$value-}; {-/if-}
						{-if $key == "SOU"-}<b>{-#sou#-}:</b> {-$value-}; {-/if-}
						{-if $key == "SER"-}<b>{-#ser#-}:</b> {-$value-}; {-/if-}
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
				{-foreach $data_header as $header-}
					{-if $item != "DisasterId"-}
						<th class="header">
							{-$header.label-}
						</th>
					{-/if-}
				{-/foreach-}
			</tr>
		</thead>
		<tbody id="tblDataRows">
{-/if-}
{-*** SHOW RESULT LIST: PAGING ***-}
{-if $ctl_dislist-}
			{-foreach $dislist as $row-}
				<tr class="ViewData">
					<td>
						<a href="#" class="linkGridGotoCard" 
							disasterid="{-$row.DisasterId-}"
							rowindex="{-$row@iteration-}">{-$offset+$row@iteration-}</a>
					</td>
					{-foreach $sel as $field_id-}
						{-if $field_id != "DisasterId"-}
							{-if $data_header[$field_id].type=='CHECKBOX'-}
								<td class="center middle">
									{-if $row[$field_id]!=0-}
										<input type="checkbox" disabled="disabled" checked="checked" />
									{-else-}
										<input type="checkbox" disabled="disabled" />
									{-/if-}
								</td>
							{-else-}
								{-$cellClass=""-}
								{-if $field_id=="DisasterSerial" || $field_id=="DisasterBeginTime" || $field_id=="EventName" || $field_id=="GeographyFQName" || 
									 $field_id=="DisasterSiteNotes" || $field_id=="DisasterSource" || $field_id=="EffectNotes" || $field_id=="EffectOtherLosses" || $field_id=="CauseName" || $field_id=="CauseNotes"-}
								 	{-$cellClass="GridCellText"-}
								{-else-}
									{-$cellClass="GridCellNumber"-}
								{-/if-}
									{-if $field_id=="EffectNotes" || $field_id=="EffectOtherLosses" || $field_id=="EventNotes" || $field_id=="CauseNotes"-}
										<td class="{-$cellClass-}">
											<div class="dwin" style="width:200px; height: 40px;">{-$row[$field_id]-}
											</div>
										</td>
									{-elseif $field_id=="DisasterSource" || $field_id=="DisasterSiteNotes"-}
										<td class="{-$cellClass-}">
											<div class="dwin" style="width:150px; height: 40px;">
												{-$row[$field_id]-}
											</div>
										</td>
									{-elseif $row[$field_id]==-1-}
										<td class="center">
											<div>
												<input type="checkbox" disabled="disabled" checked="checked" />
											</div>
										</td>
									{-elseif $row[$field_id]==-2-}?
									{-else-}
										<td class="{-$cellClass-}">
											<div>{-$row[$field_id]-}</div>
										</td>
									{-/if-}
								</td>
							{-/if-}
						{-/if-}
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
{-/if-}
