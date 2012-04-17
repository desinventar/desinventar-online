{-config_load file="$lg.conf" section="grpRegionInfo"-}
{-if $ctl_adminreg-}
<?xml version="1.0" encoding="UTF-8" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8; no-cache" />
</head>
<body>
	<table>
		<tr valign="top">
			<td>
				<fieldset style="padding:5px 5px 5px 5px;">
					<!-- GENERAL REGION INFO SECTION -->
					<form id="frmDatabaseInfo" name="infofrm" method="post" action="{-$desinventarURL-}/info.php" target="ifinfo">
					
						<table>
							<tr>
								<td colspan="2">
									{-foreach name=info key=LangIsoCode item=RegionFields from=$info-}
										<fieldset>
											<legend>
											<a id="Legend_{-$LangIsoCode-}" href="javascript:void(null)" onClick="if($('inf{-$LangIsoCode-}').style.display=='block') 
												$('inf{-$LangIsoCode-}').style.display='none'; else $('inf{-$LangIsoCode-}').style.display='block';">
												<b onMouseOver="showtip('{-$dic.DBRegion[2]-}');">{-$dic.DBRegion[0]-} {-$LangIsoCode-}</b></a>
											</legend>
											<table id="inf{-$LangIsoCode-}" style="display:{-if ($smarty.foreach.info.iteration) == 1-}block{-else-}none{-/if-};">
												{-foreach name=iitt key=key item=item from=$RegionFields-}
													{-assign var="inf" value="DB$key"-}
													{-assign var="tabind" value="`$tabind+1`"-}
													<tr>
														<td align="right">
															<a class="info" href="javascript:void(null)" onMouseOver="showtip('{-$dic.$inf[2]-}')">
															<b style="color:darkred;">{-$dic.$inf[0]-}</b><span>{-$dic.$inf[1]-}</span></a>
														</td>
														<td>
															{-if $item[1] == "TEXT"-}
																<textarea id="RegionInfo[{-$LangIsoCode-}][{-$key-}]" name="RegionInfo[{-$LangIsoCode-}][{-$key-}]"  style="width:350px; height:30px;" tabindex="{-$tabind-}"
																	onFocus="showtip('{-$dic.$inf[2]-}')">{-$item[0]-}</textarea>
															{-elseif $item[1] == "VARCHAR"-}
																<input id="RegionInfo[{-$LangIsoCode-}][{-$key-}]" name="RegionInfo[{-$LangIsoCode-}][{-$key-}]" type="text" class="line" style="width:350px;" 
																value="{-$item[0]-}" tabindex="{-$tabind-}"/>
															{-/if-}
														</td>
													</tr>
												{-/foreach-}
											</table>
										</fieldset>
									{-/foreach-}
								</td>
							</tr>
							{-foreach name=sett key=key item=item from=$sett-}
								{-assign var="inf" value="DB$key"-}
								{-assign var="tabind" value="`$tabind+1`"-}
								<tr>
									<td align="right">
										<a class="info" href="javascript:void(null)" onMouseOver="showtip('{-$dic.$inf[2]-}')">
										<b style="color:darkred;">{-$dic.$inf[0]-}</b><span>{-$dic.$inf[1]-}</span></a>
									</td>
									<td>
										{-if $item[1] == "DATE"-}
											<input id="{-$key-}" name="{-$key-}" type="text" class="line" style="width:120px;" value="{-$item[0]-}" tabindex="{-$tabind-}"/>
										{-elseif $item[1] == "NUMBER"-}
											<input id="{-$key-}" name="{-$key-}" type="text" class="line" style="width:40px;" value="{-$item[0]-}" tabindex="{-$tabind-}"/>
										{-elseif $item[1] == "TEXT"-}
											<input id="{-$key-}" name="{-$key-}" type="text" class="line" style="width:300px;" value="{-$item[0]-}" tabindex="{-$tabind-}"/>
										{-/if-}
									</td>
								</tr>
							{-/foreach-}
							<tr>
								<td colspan="2" align="center">
									<br />
									<input name="_REG" type="hidden" value="{-$reg-}" />
									<input id="_infocmd" name="cmd" value="cmdDBInfoUpdate" type="hidden" />
									<input type="submit" value="{-#bsave#-}"  class="line"/>
									<input type="reset" value="{-#bcancel#-}"  onclick="mod='info'; uploadMsg('');" class="line" />
									<br />
									<iframe name="ifinfo" id="ifinfo" frameborder="0" src="about:blank" style="height:30px; width:350px;"></iframe>
								</td>
							</tr>
						</table>
					</form>
				</fieldset>
			</td>
			<td style="width: 30px;">
			</td>
			<td>
				<!-- LOG RECORDS -->
				<!--
				<fieldset style="padding:5px 5px 5px 5px;">
					<legend>
						<b onMouseOver="showtip('{-$dic.DBLog[2]-}');">{-$dic.DBLog[0]-}</b>
					</legend>
					<div class="dwin" style="width:280px; height:120px;">
						<table width="100%" class="grid">
							<thead>
								<tr>
									<td class="header" onMouseOver="showtip('{-$dic.DBLogType[2]-}');">
										<b>{-$dic.DBLogType[0]-}</b>
									</td>
									<td class="header" onMouseOver="showtip('{-$dic.DBLogNote[2]-}');">
										<b>{-$dic.DBLogNote[0]-}</b>
									</td>
								</tr>
							</thead>
							<tbody id="lst_log">
{-/if-}
{-if $ctl_loglist-}
								{-foreach name=log key=key item=item from=$log-}
									<tr class="{-if ($smarty.foreach.log.iteration - 1) % 2 == 0-}normal{-else-}under{-/if-}"
										onMouseOver="Element.addClassName(this, 'highlight');" onMouseOut="Element.removeClassName(this, 'highlight');"
										onClick="setRolLog('{-$item[0]-}','{-$item[1]-}', 'log'); $('DBLogDate').value='{-$key-}'; $('LogCmd').value='cmdDBInfoLogUpdate';">
										<td>
											{-if $item[0] == "CREDIT"-}			{-$dic.DBLogCredits[0]-}
											{-elseif $item[0] == "METHODOLOGY"-}	{-$dic.DBLogMethodology[0]-}
											{-elseif $item[0] == "MILESTONE"-}		{-$dic.DBLogStaff[0]-}
											{-elseif $item[0] == "SUPPORT"-}		{-$dic.DBLogSupport[0]-}
											{-elseif $item[0] == "DELETED"-}		X
											{-/if-}
										</td>
										<td>
											{-$item[1]|truncate:20-}
										</td>
									</tr>
								{-/foreach-}
{-/if-}
{-if $ctl_adminreg-}
							</tbody>
						</table>
					</div>
					<br />
					<a class="button" id="add" onclick="setRolLog('', '', 'log'); $('LogCmd').value='cmdDBInfoLogInsert';"><span>{-#baddoption#-}</span></a>
					<span id="logstatusmsg" class="dlgmsg"></span>
					<br />
					<div id="logaddsect" style="display:none; width:280px;">
						<form name="logfrm" id="logfrm" method="GET" 
							action="javascript: var s=$('logfrm').serialize(); sendData('{-$reg-}', jQuery('$desinventarURL').val() + '/info.php', s, '');"
							onSubmit="javascript: var a=new Array('DBLogType','DBLogNotes'); return(checkForm('logfrm',a, '{-#errmsgfrmlog#-}'));">
							<a class="info" href="javascript:void(null)" onMouseOver="showtip('{-$dic.DBLogType[2]-}');">
							{-$dic.DBLogType[0]-}<b style="color:darkred;">*</b><span>{-$dic.DBLogType[1]-}</span></a>
							<br />
							<select id="DBLogType" name="DBLogType"  onFocus="showtip('{-$dic.DBLogType[2]-}');" class="line fixw" tabindex="1">
								<option value=""></option>
								<option value="CREDIT" onMouseOver="showtip('{-$dic.DBLogCredits[2]-}');">{-$dic.DBLogCredits[0]-}</option>
								<option value="METHODOLOGY" onMouseOver="showtip('{-$dic.DBLogMethodology[2]-}');">{-$dic.DBLogMethodology[0]-}</option>
								<option value="MILESTONE" onMouseOver="showtip('{-$dic.DBLogStaff[2]-}');">{-$dic.DBLogStaff[0]-}</option>
								<option value="SUPPORT" onMouseOver="showtip('{-$dic.DBLogSupport[2]-}');">{-$dic.DBLogSupport[0]-}</option>
								<option value="DELETED">- X -</option>
							</select>
							<br />
							<br />
							<a class="info" href="javascript:void(null)" onMouseOver="showtip('{-$dic.DBLogNote[2]-}');">
							{-$dic.DBLogNote[0]-}<b style="color:darkred;">*</b><span>{-$dic.DBLogNote[1]-}</span></a><br />
							<textarea id="DBLogNotes" name="DBLogNotes" cols="22"  class="fixw" tabindex="2" 
								onFocus="showtip('{-$dic.DBLogNote[2]-}');"></textarea>
							<br /><br />
							<p align="center" class="fixw">
								<input name="r" type="hidden" value="{-$reg-}" />
								<input id="DBLogDate" name="DBLogDate" type="hidden" />
								<input id="LogCmd" name="cmd" type="hidden" />
								<input type="submit" value="{-#bsave#-}"  class="line" tabindex="3" />
								<input type="reset" value="{-#bcancel#-}" class="line"
									onClick="$('logaddsect').style.display='none'; mod='log'; uploadMsg('');"  />
							</p>
						</form>
					</div>
				</fieldset>
				-->
			</td>
		</tr>
	</table>
</body>
</html>
{-/if-}
{-** REGION INFO AND ERRORS MESSAGES **-}
{-if $ctl_msgupdinfo-}
 {-#msgupdinfo#-}
{-elseif $ctl_errupdinfo-}
 {-#terror#-}[{-$updstatinfo-}]: {-#errupdinfo#-}
{-/if-}
{-** ROLE INFO AND ERRORS MESSAGES **-}
{-if $ctl_msgupdrole-}
 {-#msgupdrole#-} 
{-elseif $ctl_errupdrole-}
 {-#terror#-}[{-$updstatrole-}]: {-#errupdrole#-}
{-/if-}
{-** LOG INFO AND ERRORS MESSAGES **-}
{-if $ctl_msginslog-}
 {-#msginslog#-}
{-elseif $ctl_errinslog-}
 {-#terror#-}[{-$insstatlog-}]: {-#errinslog#-}
{-elseif $ctl_msgupdlog-}
 {-#msgupdlog#-}
{-elseif $ctl_errupdlog-}
 {-#terror#-}[{-$updstatlog-}]: {-#errupdlog#-}
{-/if-}
