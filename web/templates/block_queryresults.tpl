	<table border="0" cellpadding="0" cellspacing="0" width="100%">
		<tr bgcolor="#bbbbbb">
			<td width="200px">
				<div id="ResultHeaderText"><b>{-#tsubtitle2#-} &rarr;</b></div>
			</td>
			<td align="center">
				<div id="ResultHeaderButtonToolbar">
					<button id="btnViewData" class="rounded" ext:qtip="{-#tdatamsg#-}"><span>{-#bdata#-}</span></button>
					{-include file="viewdata_parameters.tpl"-}
					<button id="btnViewMap" class="rounded" ext:qtip="{-#tthematicmsg#-}"><span>{-#bthematic#-}</span></button>
					{-include file="thematicmap_parameters.tpl"-}
					<button id="btnViewGraph" class="rounded" ext:qtip="{-#tgraphicmsg#-}"><span>{-#bgraphic#-}</span></button>
					{-include file="graph_parameters.tpl"-}
					<button id="btnViewStd" class="rounded" ext:qtip="{-#tstatisticmsg#-}"><span>{-#bstatistic#-}</span></button>
					{-include file="statistic_parameters.tpl"-}
				</div>
				<!-- Database Export Window -->
				{-include file="database_export.tpl"-}
				<!-- Show XML query open window-->
				<div id="qry-win" class="x-hidden">
					<div class="x-window-header">
						{-#mopenquery#-}
					</div>
					<div id="qry-cfg" style="text-align:center;">
						<form id="openquery" enctype="multipart/form-data" action="{-$desinventarURL-}/?r={-$reg-}" method="POST">
							<br /><br />
							<input type="hidden" name="cmd" value="openquery" />
							<input type="hidden" name="MAX_FILE_SIZE" value="100000" />
							<input type="file" id="ofile" name="qry" onChange="$('openquery').submit();"/>
						</form>
					</div>
					<span id="msgQueryOpenButtonClose" style="display:none;">{-#tclose#-}</span>
				</div>
				<!-- Show DesInventar (input data) window-->
				<div id="divDatacardWindow" class="x-hidden">
					<div class="x-window-header">
						{-#mdcsection#-} | {-$desinventarUserId-} - {-$role-}
					</div>
					<div id="dif-cfg" style="text-align:center;">
						<div name="dif" id="dif" frameborder="0" height="600px;" width="100%">
							{-include file="datacards.tpl"-}
						</div>
					</div>
				</div>
				<!-- Show Database functions window -->
				<div id="dbl-win" class="x-hidden">
					<div class="x-window-header">
						{-$desinventarUserId-} - {-$role-}
					</div>
					<div id="dbl">
					</div>
				</div>
			</td>
			<td>
				<span id="frmwait"></span>
				<input id="DCRes" type="hidden" value="" />
				<input id="bsave" type="button" class="bb bsave"   style="visibility: hidden;"
					onMouseOver="if($('DCRes').value == 'D' || $('DCRes').value == 'S') $('saveopt').style.display='block';"
					onClick="saveRes('export', '');" ext:qtip="{-#bsavemsg#-}" />
				<span id="saveopt" style="position:absolute; display: none" 
					onMouseOver="$('saveopt').style.display='block';" onMouseOut="setTimeout('$(\'saveopt\').style.display=\'none\';', 2000);">
					<input type="button" value="xls" class="bb line" onClick="saveRes('export', 'xls')" /> | 
					<input type="button" value="csv" class="bb line" onClick="saveRes('export', 'csv')" />
				</span>
				&nbsp;&nbsp;
				<input id="bprint" type="button" class="bb bprint" style="visibility: hidden;"	
					onClick="printRes();" ext:qtip="{-#bprintmsg#-}" />&nbsp;&nbsp;
			</td>
		</tr>
	</table>
	{-include file="region_info.tpl"-}
	<div id="dcr">
	</div>
