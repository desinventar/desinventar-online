{-config_load file=`$lg`.conf section="dc_statistic"-}
{-config_load file=`$lg`.conf section="dc_qdetails"-}
{-if $ctl_showres-}
	<table width="920px" class="grid">
		<tr>
			<td colspan="3">
				<div class="dwin" style="height:40px;">
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
				<input type="text" id="StatCurPage" size="2" value="1" class="line" />
				&nbsp; {-#tnumof#-} &nbsp;{-$last-}
				&nbsp;&nbsp;|&nbsp;&nbsp;
				<input type="button" id="btnStatGotoFirstPage" value="<<" class="line" />
				<input type="button" id="btnStatGotoPrevPage"  value="<"  class="line" />
				<input type="button" id="btnStatGotoNextPage"  value=">"  class="line" />
				<input type="button" id="btnStatGotoLastPage"  value=">>" class="line" />
			</td>
			<td align="center">
				<span id="stdstatusmsg" class="dlgmsg"></span>
			</td>
			<td align="right">
				{-#tsumnum#-}: {-$cou-} | {-#trepnum#-}: {-$tot-}
			</td>
		</tr>
	</table>
	<table width="930px" height="95%" class="col">
		<thead>
			<tr>
				<th class="header">{-#trow#-}
				</th>
				{-foreach name=sel key=key item=item from=$sel-}
					<th class="header">
						<table cellpadding=0 cellspacing=0 border=0>
							<tr>
								<td>
									<a href="#" class="linkStatOrderColumn" altfield="{-$item-}" ordertype="ASC"><img src="images/asc.gif" border=0></a>
								</td>
								<td>
									{-if $item =="DisasterId_"-}{-#trepnum#-}{-elseif $item != "DisasterId"-}{-$dk.$item-}{-/if-}
								</td>
								<td>
									<a href="#" class="linkStatOrderColumn" altfield="{-$item-}" ordertype="DESC"><img src="images/desc.gif" border=0></a>
								</td>
							</tr>
						</table>
					</th>
				{-/foreach-}
			</tr>
			<tr>
				<th style="border: thin solid; text-align: right;">{-#ttotals#-}
				</th>
				{-foreach name=sel key=key item=item from=$sel-}
					{-if $item != "DisasterId"-}
						<th style="border: thin solid; text-align: right;">
							{-if $item != $gp[0] && $item != $gp[1] && $item != $gp[2]-} {-$dlt.$item-}{-/if-}
						</th>
					{-/if-}
				{-/foreach-}
			</tr>
		</thead>
		<tbody id="tblStatRows">
{-/if-}
			{-*** SHOW RESULT LIST: PAGING ***-}
{-if $ctl_dislist-}
			{-foreach name=dl key=key item=item from=$dislist-}
				<tr class="normal">
					<td>{-$offset+$smarty.foreach.dl.iteration-}
					</td>
					{-strip-}
						{-foreach name=sel key=k item=i from=$sel-}
							{-if $i != "DisasterId"-}
								<td {-if $i=="GeographyId_0" || $i=="GeographyId_1" || $i=="GeographyId_2" || 
								         $i=="EventName" || $i=="CauseName" -}
										class="GridCellText"
									{-else-}
										class="GridCellNumber"
									{-/if-}>{-$item[$i]-}
								</td>
							{-/if-}
						{-/foreach-}
					{-/strip-}
				</tr>
			{-/foreach-}
{-/if-}
{-if $ctl_showres-}
		</tbody>
	</table>
	<div style="display:none;">
		<input type="hidden" id="prmStatRegionId"       value="{-$reg-}"  />
		<input type="hidden" id="prmStatRecordsPerPage" value="{-$rxp-}"  />
		<input type="hidden" id="prmStatNumberOfPages"  value="{-$last-}" />
		<input type="hidden" id="prmStatQueryDef"       value="{-$sql-}"  />
		<input type="hidden" id="prmStatFieldList"      value="{-$fld-}"  />
		<input type="hidden" id="prmStatGeography"      value="{-$geo-}"  />
	</div>
{-/if-}
