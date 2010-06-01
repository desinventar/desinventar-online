{-config_load file=`$lg`.conf section="dc_data"-}
{-config_load file=`$lg`.conf section="dc_qdetails"-}
{-if $ctl_showres-}
	<table width="920px" class="grid">
		<tr>
			<td>
				{-#tpage#-}
				<input type="text" id="DataCurPage" size="2" value="1" class="line"  />
				&nbsp; {-#tnumof#-} &nbsp; {-$NumberOfPages-}
				&nbsp;&nbsp;|&nbsp;&nbsp;
				<input type="button" id="btnGridGotoFirstPage" value="<<" class="line" />
				<input type="button" id="btnGridGotoPrevPage"  value="<"  class="line" />
				<input type="button" id="btnGridGotoNextPage"  value=">"  class="line" />
				<input type="button" id="btnGridGotoLastPage"  value=">>" class="line" />
			</td>
			<td align="center">
				<span id="datstatusmsg" class="dlgmsg"></span>
			</td>
			<td align="right">
				{-#trepnum#-}: {-$tot-}
			</td>
		</tr>
	</table>
	<table width="930px" class="col">
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
				<tr class="normal" 
					onClick="Element.addClassName(this, 'highlight');" ondblClick="Element.removeClassName(this, 'highlight');">
					<td>
						<a href="#" class="linkGridGotoCard" disasterid="{-$item.DisasterId-}">{-$offset+$smarty.foreach.dl.iteration-}</a>
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
		<input type="hidden" id="prmDataRegionId"       value="{-$RegionId-}"       />
		<input type="hidden" id="prmDataUserRole"       value="{-$UserRole-}"       />
		<input type="hidden" id="prmDataUserRoleValue"  value="{-$UserRoleValue-}"  />
		<input type="hidden" id="prmDataRecordsPerPage" value="{-$RecordsPerPage-}" />
		<input type="hidden" id="prmDataNumberOfPages"  value="{-$NumberOfPages-}"  />
		<input type="hidden" id="prmDataQueryDef"       value="{-$sql-}"            />
		<input type="hidden" id="prmDataFieldList"      value="{-$fld-}"            />
	</div>
{-/if-}
