<table width="100%">
	<tr bgcolor="#bbbbbb">
		<td>
			<div id="ResultHeaderButtonToolbar">
				<button id="btnViewData" class="rounded" title="{-#msgViewDataTooltip#-}"><span>{-#msgViewData#-}</span></button>
				{-include file="viewdata_parameters.tpl"-}
				<button id="btnViewMap" class="rounded" title="{-#msgViewMapTooltip#-}"><span>{-#msgViewMap#-}</span></button>
				{-include file="thematicmap_parameters.tpl"-}
				<button id="btnViewGraph" class="rounded" title="{-#msgViewGraphTooltip#-}"><span>{-#msgViewGraph#-}</span></button>
				{-include file="graph_parameters.tpl"-}
				<button id="btnViewStd" class="rounded" title="{-#msgViewStdTooltip#-}"><span>{-#msgViewStd#-}</span></button>
				{-include file="statistic_parameters.tpl"-}
			</div>
			<!-- Database Export Window -->
			{-include file="database_export.tpl"-}
			<!-- Show XML query open window-->
			{-include file="query_open_ext.tpl"-}

			<!-- Show DesInventar (input data) window-->
			{-include file="datacards_ext.tpl"-}

			<!-- Show User login/logout window -->
			{-include file="user_login_ext.tpl"-}
			<!-- User Account Edit Window -->
			{-include file="user_account_ext.tpl"-}

			<!-- Show Database functions window -->
			<div id="divAdminUsersWin" class="x-hidden">
				<div class="x-window-header">
					{-$desinventarUserId-} - {-$role-}
				</div>
				<div id="divAdminUsersContent">
					{-include file="admin_users.tpl"-}
				</div>
			</div>
			<!-- Show Dialog window -->
			<div id="dlg-win" class="x-hidden">
				<div class="x-window-header">
				</div>
				<div id="dlg">
					<table>
						<tr>
							<td>
								<img src="{-$desinventarURL-}/images/di_logo.png" />
							</td>
							<td>
								<p style="font-size: 16pt;" class="center">DesInventar {-$desinventarVersion-}</p>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								{-#tabout#-}<hr />{-#tcopyright#-}
							</td>
						</tr>
					</table>
				</div>
			</div>
		</td>
		<td>
			<span id="frmwait"></span>
			<input id="DCRes" type="hidden" value="" />
			<input id="btnResultSave" type="button" class="bb bsave hidden" title="{-#msgResultSaveTooltip#-}" />
			<div id="btnResultSaveOptions" class="hidden">
				<input type="button" id="btnResultSaveXLS" value="xls" class="bb line" /> | 
				<input type="button" id="btnResultSaveCSV" value="csv" class="bb line" />
				<input type="hidden" id="btnResultShow" value="" />
			</div>
			&nbsp;
			<input id="btnResultPrint" type="button" class="bb bprint hidden" title="{-#msgResultPrintTooltip#-}" />
			&nbsp;
		</td>
	</tr>
</table>
<!-- divRegionInfo -->
{-include file="region_info.tpl"-}

<div id="dcr">
</div>
<iframe class="hidden" name="iframeDownload">
</iframe>
