{-config_load file=`$lg`.conf section="di8_input"-}
<?xml version="1.0" encoding="UTF-8" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8; no-cache" />
	<title>{-#ttitle#-} [{-$regname-}] | {-$usr-} - {-$dicrole-}</title>
	<link rel="stylesheet" href="css/desinventar.css?version={-$jsversion-}" type="text/css"/>
	{-include file="jquery.tpl" -}
	<script type="text/javascript" src="include/prototype.js"></script>
	<script type="text/javascript" src="include/combo-box.js"></script>
	<script type="text/javascript" src="js/diadmin.js?version={-$jsversion-}"></script>
	<script type="text/javascript" src="js/cards.js?version={-$jsversion-}"></script>
	<script type="text/javascript" language="javascript">

		function gotocard(opc) {
			bUpdate = getDatacardUpdatePerm(jQuery('#prmUserRole').val());
			RegionId = jQuery('#prmRegionId').val();
			switch (opc) {
				case "first":
					bFound = requestDatacard('getDisasterIdFirst', $('DisasterId').value);
					if (bUpdate) {
						disenabutton($('btnDatacardEdit'), false);
					}
					disenabutton($('prev'), true);
					disenabutton($('next'), false);
				break;
				case "prev":
					bFound = requestDatacard('getDisasterIdPrev', jQuery('#prmRecordNumber').val());
					if (bFound == false) {
						alert('{-#tcardnot#-}');
					}
					if (bUpdate) {
						disenabutton($('btnDatacardEdit'), false);
					}
					disenabutton($('next'), false);
				break;
				case "next":
					bFound = requestDatacard('getDisasterIdNext', jQuery('#prmRecordNumber').val());
					if (bFound == false) {
						alert('{-#tcardnot#-}');
					}
					if (bUpdate) {
						disenabutton($('btnDatacardEdit'), false);
					}
					disenabutton($('prev'), false);
				break;
				case "last":
					bFound = requestDatacard('getDisasterIdLast', $('DisasterId').value);
					if (bUpdate) {
						disenabutton($('btnDatacardEdit'), false);
					}
					disenabutton($('prev'), false);
					disenabutton($('next'), true);
				break;
			}
		}
		function setActive() {
			updateList('dostat', 'cards.php', 'u=1');
		}
		function onSubmitBtn(btn) {
			displayDatacardStatusMsg('');
			$('dic').src="about:blank";
			switch (btn) {
				case "cardsav":
					var bContinue = true;
					var cmd = jQuery('#_CMD').val();
					var DisasterSerial = jQuery('#DisasterSerial').val();
					var PrevDisasterSerial = jQuery('#PrevDisasterSerial').val();
					jQuery.post('cards.php',
						{'cmd'            : 'existDisasterSerial',
						 'RegionId'       : '{-$reg-}',
						 'DisasterSerial' : DisasterSerial
						},
						function(data) {
							bContinue = true;
							if ( (cmd == 'insertDICard') && (data.DisasterSerial != '') ) {
								// Serial of new datacard already exists...
								//alert('Disaster Serial already exists...');
								bContinue = false;
							}
							if (cmd == 'updateDICard') {
								if ( (DisasterSerial != PrevDisasterSerial) && (data.DisasterSerial != '') ) {
									// Edited Serial exists in database...
									//alert('Disaster Serial is duplicated...');
									bContinue = false;
								}
							}
							if (bContinue == false) {
								displayDatacardStatusMsg('msgDuplicatedDisasterSerial');
							}
							if (bContinue) {
								var fl = new Array('DisasterSerial', 'DisasterBeginTime0', 'DisasterSource', 
													'geolev0', 'EventId', 'CauseId', 'RecordStatus');
								if (checkForm(fl, "{-#errmsgfrm#-}")) {
									uploadMsg('');
									$('DICard').submit();
									DisableEnableForm($('DICard'), true);
									changeOptions(btn);
									// clear Help text area
									showtip('','#ffffff');
								}
							}
						},'json'
					);
				break;
				case "cardcln":
					$('DICard').reset();
					$('lev0').innerHTML='';
					uploadMsg('');
					jQuery('#DisasterBeginTime0').focus();
				break;
				case "cardcan":
					jQuery('#DisasterBeginTime0').val('');
					jQuery('#DisasterBeginTime1').val('');
					jQuery('#DisasterBeginTime2').val('');
					updateList('distatusmsg', 'cards.php', 'r={-$reg-}&cmd=chkrelease&DisasterId='+ $('DisasterId').value);
					DisableEnableForm($('DICard'), true);
					uploadMsg("{-#tmsgnewcard#-}");
					changeOptions(btn);
					// clear Help text area
					showtip('','#ffffff');
				break;
				case "cardfnd":
				break;
			}
			return true;
		}
		
		jQuery(document).ready(function() {
			// Hide StatusMessages
			displayDatacardStatusMsg('');
			jQuery('#divDatacardStatusMsg').show();
			// Hide window's parameters
			jQuery('#divDatacardParameter').hide();
			
			DisableEnableForm($('DICard'), true);
			changeOptions();
			UserRole = jQuery('#prmUserRole').val();
			if (UserRole != '') {
				uploadMsg("{-#tmsgnewcard#-}");
			}

			jQuery('#DisasterBeginTime0').blur(function() {
				cmd = jQuery('#_CMD').val();
				if (cmd == 'insertDICard') {
					if (jQuery(this).val() != '') {
						requestDatacard('getNextSerial', jQuery(this).val());
					}
				}
			});
			
			jQuery('#DisasterBeginTime1').blur(function() {
				if (parseInt(jQuery(this).val(),10) < 1 || 
				    parseInt(jQuery(this).val(),10) > 12 ) {
				    	jQuery(this).val('');
				}
			});

			jQuery('#DisasterBeginTime2').blur(function() {
				if (parseInt(jQuery(this).val(),10) < 1 || 
				    parseInt(jQuery(this).val(),10) > 31 ) {
				    	jQuery(this).val('');
				}
			});
			
			jQuery('#btnDatacardNew').click(function() {
				doDatacardNew();
				return false;
			});
			
			jQuery('#btnDatacardEdit').click(function() {
				doDatacardEdit();
				return false;
			});
			
			// Create periodic task to keep session alive...
			var pe = new PeriodicalExecuter(setActive, 60);
		});
			
	</script>

	<style type="text/css">
		.bnew {
			background-image: url(images/newicon.png) !important;
			background-repeat: no-repeat; background-position: top center; width: 22px; height: 22px;
		}
		.bupd {
			background-image: url(images/updateicon.png) !important;
			background-repeat: no-repeat; background-position: top center; width: 22px; height: 22px;
		}
		.bcancel {
			background-image: url(images/cancelicon.png) !important; 
			background-repeat: no-repeat; background-position: top center; width: 22px; height: 22px;
		}
		.bclean {
			background-image: url(images/cleanicon.png) !important; 
			background-repeat: no-repeat; background-position: top center; width: 22px; height: 22px;
		}
		.bfind {
			background-image: url(images/findicon.png) !important;
			background-repeat: no-repeat; background-position: top center; width: 22px; height: 22px;
		}
		.bsave {
			background-image: url(images/saveicon.png) !important;
			background-repeat: no-repeat; background-position: top center; width: 22px; height: 22px;
		}
		.bprint {
			background-image: url(images/printicon.png) !important;
			background-repeat: no-repeat; background-position: top center; width: 22px; height: 22px;
		}
	</style>
</head>

<body>
	<script type="text/javascript" src="include/wz_tooltip.js"></script>
	<!-- BEG DI8 FORM CARD -->
	<table width="900px" border="0" cellpadding="0" cellspacing="0" >
		<tr valign="middle">
			<td width="450px" rowspan="2">
				{-if $ctl_validrole-}
					<input type="button" id="btnDatacardNew"    class="bb bnew" onmouseover="Tip('{-#tnewtitle#-}: {-#tnewdesc#-}')" onmouseout="UnTip()" />
					<input type="button" id="btnDatacardEdit" class="bb bupd" onmouseover="Tip('{-#tupdtitle#-}: {-#tupddesc#-}')" onmouseout="UnTip()" />
					<input type="button" id="cardsav" class="bb bsave" onmouseover="Tip('{-#tsavtitle#-}: {-#tsavdesc#-}')" 
						onmouseout="UnTip()" onClick="onSubmitBtn('cardsav');" />
					<input type="button" id="cardcln" class="bb bclean" onmouseover="Tip('{-#tclntitle#-}: {-#tclndesc#-}')" 
						onmouseout="UnTip()" onClick="onSubmitBtn('cardcln');" />
					<input type="button" id="cardcan" class="bb bcancel" onmouseover="Tip('{-#tcantitle#-}: {-#tcandesc#-}')" 
						onmouseout="UnTip()" onClick="onSubmitBtn('cardcan');" />
				{-/if-}
				<input type="button" id="cardprn" class="bb bprint" onmouseover="Tip('{-#mprint#-}')" 
					onmouseout="UnTip()" onClick="window.print();" />
				&nbsp;&nbsp;|&nbsp;&nbsp;
				<input type="button" id="first" value="<<" class="bb line" onmouseover="Tip('{-#bfirst#-}')" 
					onmouseout="UnTip()" onClick="gotocard('first')" />
				<input type="button" id="prev"  value="<" class="bb line" onmouseover="Tip('{-#bprev#-}')" 
					onmouseout="UnTip()" onClick="gotocard('prev')" />
				<input type="button" id="next"  value=">" class="bb line" onmouseover="Tip('{-#bnext#-}')" 
					onmouseout="UnTip()" onClick="gotocard('next')" />
				<input type="button" id="last"  value=">>" class="bb line" onmouseover="Tip('{-#blast#-}')" 
					onmouseout="UnTip()" onClick="gotocard('last')" />
				&nbsp;&nbsp;|&nbsp;&nbsp;
				{-$dis.DisasterSerial[0]-}
				<input type="text" id="fndserial" style="width:60px;" class="line"
					onKeyDown="if(event.keyCode==13) requestDatacard('getDisasterIdFromSerial', $('fndserial').value);" />
				<input type="button" id="cardfnd" class="bb bfind" onmouseover="Tip('{-#texptitle#-}')" onmouseout="UnTip()" 
					onClick="if($('fndserial').value !='') requestDatacard('getDisasterIdFromSerial', $('fndserial').value); 
						else alert('{-#bexpsearch#-}: {-#texpdesc#-}')" />
				<br />
				
				<span class="dlgmsg" id="distatusmsg"></span>
				<span class="dlgmsg" id="dostat"></span>
			</td>
			<td align="right" width="450px">
				<iframe name="dic" id="dic" frameborder="0" style="width:100%; height:28px;" src="about:blank"></iframe>
			</td>
		</tr>
		<tr>
			<td align="left" valign="top" width="450px">
				<div id="divDatacardStatusMsg" style="display:none;">
					<span class="datacardStatusMsg" id="msgDuplicatedDisasterSerial">{-#msgDuplicatedDisasterSerial#-}</span>
					<span class="datacardStatusMsg" id="msgDatacardFill">{-#tmsgnewcardfill#-}</span>
					<span class="datacardStatusMsg" id="msgDatacardIsLocked">{-#tdconuse#-}</span>
				</div>
				<br />
			</td>
		</tr>
	</table>
	<form id="DICard" action="cards.php" method="POST" target="dic">
		<input type="hidden" name="_REG" id="_REG" value="{-$reg-}">
		<input type="hidden" name="DisasterId" id="DisasterId" value="">
		<input type="hidden" name="RecordAuthor" id="RecordAuthor" value="{-$usr-}">
		<input type="hidden" name="RecordCreation" id="RecordCreation">
		<input type="hidden" name="_CMD" id="_CMD" value="">
		<input type="hidden" name="PrevDisasterSerial~~" id="PrevDisasterSerial" value="">
		<table border="1" cellspacing="8" width="900px">
			<!-- DATACARD INFORMATION SECTION -->
			<tr>
				<td width="30px" style="border:0px;" valign="top">
					&nbsp;
				</td>
				<td style="border-color:#000000;">
					<table class="grid">
						<tr valign="top">
							<td onmouseover="Tip('{-$dis.DisasterBeginTime[1]-}')" onmouseout="UnTip()">
								{-$dis.DisasterBeginTime[0]-}<b style="color:darkred;">*</b><br />
								<input id="DisasterBeginTime0" name="DisasterBeginTime[0]" style="width:36px;" class="line"
									tabindex="1" type="text" maxlength="4" onFocus="showtip('{-$dis.DisasterBeginTime[2]-}', '#d4baf6')"
									onkeypress="return blockChars(event, this.value, 'integer:4');" />
								<input id="DisasterBeginTime1" name="DisasterBeginTime[1]" style="width:18px;" class="line"
									tabindex="2" type="text" maxlength="2" onFocus="showtip('{-$dis.DisasterBeginTime[2]-}', '#d4baf6')"
									onkeypress="return blockChars(event, this.value, 'integer:2');" />
								<input id="DisasterBeginTime2" name="DisasterBeginTime[2]" style="width:18px;" class="line"
									tabindex="3" type="text" maxlength="2" onFocus="showtip('{-$dis.DisasterBeginTime[2]-}', '#d4baf6')"
									onkeypress="return blockChars(event, this.value, 'integer:2');" />
							</td>
							<td onmouseover="Tip('{-$dis.DisasterSource[1]-}')" onmouseout="UnTip()">
								{-$dis.DisasterSource[0]-}<b style="color:darkred;">*</b><br />
								<input id="DisasterSource" name="DisasterSource" size="50" class="line" type="text" 
									onkeypress="return blockChars(event, this.value, 'text:');"
									tabindex="4" onFocus="showtip('{-$dis.DisasterSource[2]-}', '#d4baf6')" />
							</td>
							<td>
								{-#tstatus#-}<b style="color:darkred;">*</b><br />
								<select name="RecordStatus" id="RecordStatus" tabindex="5" class="line"
										onFocus="showtip('{-$rc1.RecordStatus[1]-}', '')">
									<option value=""></option>
									<option value="PUBLISHED" {-if $role == "USER"-}disabled{-/if-}>{-#tstatpublished#-}</option>
									<option value="READY">{-#tstatready#-}</option>
									<option value="DRAFT">{-#tstatdraft#-}</option>
									<option value="TRASH">{-#tstatrash#-}</option>
									<option value="DELETED" {-if $role == "USER"-}disabled{-/if-}>{-#tstatdeleted#-}</option>
								</select>
							</td>
							<td onmouseover="Tip('{-$dis.DisasterSerial[1]-}')" onmouseout="UnTip()">
								{-$dis.DisasterSerial[0]-}<b style="color:darkred;">*<br />
								<input id="DisasterSerial" name="DisasterSerial" type="text" size="15" class="line"
									tabindex="6" maxlength="50" onFocus="showtip('{-$dis.DisasterSerial[2]-}', '#d4baf6')"
									onkeypress="return blockChars(event, this.value, 'alphanumber:');" />
								<a href="javascript:void(0);" onClick="if(!$('DisasterSerial').disabled) requestDatacard('getNextSerial', 
									$('DisasterBeginTime0').value);"><img src="images/reload.jpg" border="0" />
								</a>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<!-- GEOGRAPHY SECTION -->
			<tr>
				<td width="30px" style="border:0px;" valign="top">
					<img src="images/di_geotag.png" 
						onmouseover="Tip('<b>{-#mgeography#-}</b><br />{-$dmg.MetGuidegeography[2]-}', BGCOLOR, '#d4baf6', SHADOW, true, WIDTH, 400)" 
						onmouseout="UnTip()" />
				</td>
				<td>
					<table class="grid">
						<tr valign="top">
							<td onmouseover="Tip('{-$dis.DisasterGeographyId[1]-}')" onmouseout="UnTip()">
								{-$dis.DisasterGeographyId[0]-}<b style="color:darkred;">*</b><br />
								<input id="GeographyId" name="GeographyId" type="hidden" />
								<span id="lst_geo" class="geodiv" style="width: 180px; height: 30px;">
									{-include file="cards_geolist.tpl"-}
									{-assign var="tabind" value="10"-}
								</span>
								<br />
							</td>
							{-assign var="tabind" value="`$tabind+1`"-}
							<td onmouseover="Tip('{-$dis.DisasterSiteNotes[1]-}')" onmouseout="UnTip()">
								{-$dis.DisasterSiteNotes[0]-}<br />
								<textarea id="DisasterSiteNotes" name="DisasterSiteNotes" style="height: 40px;" cols="25"
									onkeypress="return blockChars(event, this.value, 'text:');"
									tabindex="{-$tabind-}" onFocus="showtip('{-$dis.DisasterSiteNotes[2]-}', '#d4baf6')"></textarea>
							</td>
							<td>
								{-assign var="tabind" value="`$tabind+1`"-}
								<span onmouseover="Tip('{-$dis.DisasterLatitude[1]-}')" onmouseout="UnTip()">
								{-$dis.DisasterLatitude[0]-}<br />
								<input id="DisasterLatitude" name="DisasterLatitude" type="text" size="10" class="line"
									tabindex="{-$tabind-}" onFocus="showtip('{-$dis.DisasterLatitude[2]-}', '#d4baf6')"
									onkeypress="return blockChars(event, this.value, 'double:10');" />
								</span>
								<br />
								{-assign var="tabind" value="`$tabind+1`"-}
								<span onmouseover="Tip('{-$dis.DisasterLongitude[1]-}')" onmouseout="UnTip()">
									{-$dis.DisasterLongitude[0]-}
									<br />
									<input id="DisasterLongitude" name="DisasterLongitude" type="text" size="10" class="line"
										tabindex="{-$tabind-}" onFocus="showtip('{-$dis.DisasterLongitude[2]-}', '#d4baf6')"
										onkeypress="return blockChars(event, this.value, 'double:10');" />
								</span>
								<br />
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<!-- EFFECTS SECTION--> 
			<tr>
				<td width="30px" valign="top" style="border:0px;">
					<a href="javascript:void(null)" onClick="switchEff('effbas')">
						<img id="efimg" src="images/di_efftag.png" border=0
							onmouseover="Tip('<b>{-#tbaseffects#-}</b><br />{-$dmg.MetGuidedatacards[2]-}', BGCOLOR, '#f1bd41', SHADOW, true, WIDTH, 400)"
							onmouseout="UnTip()">
					</a>
					<br /><br />
					<a href="javascript:void(null)" onClick="switchEff('effext')">
						<img id="eeimg" src="images/di_eeftag.png" border=0 
							onmouseover="Tip('<b>{-#textraeffect#-}</b><br />{-$dmg.MetGuideextraeffects[2]-}', BGCOLOR, '#f1bd41', SHADOW, true, WIDTH, 400)" 
							onmouseout="UnTip()">
					</a>
					<!-- usemap="#efx"
					<map id="efx" name="efx">
						<area shape="rect" coords="4,4,20,115" href="javascript:void(null)" onClick="switchEff('effbas')">
						<area shape="rect" coords="4,130,20,240" href="javascript:void(null)" onClick="switchEff('effext')">
					</map>-->
				</td>
				<td valign="top">
					<!-- BEG BASIC EFFECTS -->
					<table class="grid" id="effbas">
						<tr valign="top">
							<td>
								<b align="left">{-#teffects#-}</b><br />
								<table width="100%" class="grid">
									<!-- BEGIN Table Effects over People-->
									{-foreach name=ef1 key=key item=item from=$ef1-}
										{-assign var="tabind" value="`$tabind+1`"-}
										<tr>
											<td align="right">
												<span onmouseover="Tip('{-$item[1]-}')" onmouseout="UnTip()">{-$item[0]-}</span>
											</td>
											<td>
												<select id="{-$key-}" name="{-$key-}" style="width:120px;" class="line" tabindex="{-$tabind-}" onKeyPress="edit(event);" onFocus="showtip('{-$item[2]-}', '#f1bd41');" 
													onBlur="this.editing=false; if(parseInt(this.value) == 0) { this.value = '0'; }">
													<option class="small" value="-1">{-#teffhav#-}</option>
													<option class="small" value="0" selected>{-#teffhavnot#-}</option>
													<option class="small" value="-2">{-#teffdontknow#-}</option>
												</select>
											</td>
										</tr>
									{-/foreach-}
								</table> 
							</td>
							<td>
								<b align="center">{-#tsectors#-}</b><br />
								<table width="100%" class="grid">
									<!-- BEGIN Table Sectors -->
									{-foreach name=sec key=key item=item from=$sec-}
										{-assign var="tabind" value="`$tabind+1`"-}
										<tr>
											<td align="right"><span onmouseover="Tip('{-$item[1]-}')" onmouseout="UnTip()">{-$item[0]-}</span>
											</td>
											<td>
												<select id="{-$key-}" name="{-$key-}" style="width:120px;" tabindex="{-$tabind-}" 
													class="line" onFocus="showtip('{-$item[2]-}', '#f1bd41')">
													<option class="small" value="-1">{-#teffhav#-}</option>
													<option class="small" value="0" selected>{-#teffhavnot#-}</option>
													<option class="small" value="-2">{-#teffdontknow#-}</option>
												</select>
											</td>
										</tr>
									{-/foreach-}
								</table>
							</td>
							<td>
								<br />
								<!-- BEGIN Table Effects over Affected -->
								{-foreach name=ef3 key=key item=item from=$ef3-}
									{-assign var="tabind" value="`$tabind+1`"-}
									<span onmouseover="Tip('{-$item[1]-}')" onmouseout="UnTip()">
										{-$item[0]-}<br />
										<input id="{-$key-}" name="{-$key-}" type="text" size="7" 
											onBlur="if(parseInt(this.value) > 0) { $('{-$sc3[$key]-}').value='-1';} if(parseInt(this.value) ==0) { $('{-$sc3[$key]-}').value='0';}"
											onFocus="showtip('{-$item[2]-}', '#f1bd41')" tabindex="{-$tabind-}" class="line"
											onkeypress="return blockChars(event, this.value, 'double:10');" value="0" />
									</span>
									<br />
								{-/foreach-}
							</td>
							<td valign="top">
								<b align="right">{-#tlosses#-}</b><br />
								<!-- BEGIN Table Effects over $$ -->
								{-foreach name=ef2 key=key item=item from=$ef2-}
									{-assign var="tabind" value="`$tabind+1`"-}
									<span onmouseover="Tip('{-$item[1]-}')" onmouseout="UnTip()">
										{-$item[0]-}<br />
										<input id="{-$key-}" name="{-$key-}" type="text" size="11" tabindex="{-$tabind-}" class="line"
											onFocus="showtip('{-$item[2]-}', '#f1bd41');" value="0" 
											onkeypress="return blockChars(event, this.value, 'double:');" />
									</span>
									<br />
								{-/foreach-}
								{-foreach name=ef4 key=key item=item from=$ef4-}
									{-assign var="tabind" value="`$tabind+1`"-}
									<span onmouseover="Tip('{-$item[1]-}')" onmouseout="UnTip()">
										{-$item[0]-}<br />
										<textarea id="{-$key-}" name="{-$key-}" cols="25" style="height: {-if $key=='EffectNotes'-}70{-else-}30{-/if-}px;"
											onBlur="if(this.value != '') { $('SectorOther').value='-1'; }"
											onkeypress="return blockChars(event, this.value, 'text:');"
											onFocus="showtip('{-$item[2]-}', '#f1bd41')" tabindex="{-$tabind-}">
										</textarea>
									</span>
									<br />
								{-/foreach-}
							</td>
						</tr>
					</table>
					<!-- BEG EXTRA EFFECTS FIELDS -->
					<table class="grid" id="effext" style="display:none;">
						<tr>
							<td>
								<br />
							</td>
						</tr>
						{-assign var="tabeef" value="200"-}
						{-foreach name=eefl key=key item=item from=$eefl-}
							{-assign var="tabeef" value="`$tabeef+1`"-}
							{-if ($smarty.foreach.eefl.iteration - 1) % 3 == 0-}
								<tr>
							{-/if-}
									<td onmouseover="Tip('{-$item[1]-}')" onmouseout="UnTip()">
										{-$item[0]-}<br />
										<input type="text" id="{-$key-}" name="{-$key-}" size="30" class="line" tabindex="{-$tabeef-}"
											onFocus="showtip('{-$item[1]-}', '#f1bd41')" onkeypress="return blockChars(event, this.value, 'text:');" />
									</td>
							{-if ($smarty.foreach.eefl.iteration ) % 3 == 0-}
								</tr>
							{-/if-}
						{-/foreach-}
					</table>
				</td>
			</tr>
			<!-- BEG EVENT SECTION -->
			<tr style="border:1px solid #ff0;">
				<td width="30px" valign="top" style="border:0px;">
					<img src="images/di_evetag.png" 
					onmouseover="Tip('<b>{-#mevents#-}</b><br />{-$dmg.MetGuideevents[2]-}', BGCOLOR, 'lightblue', SHADOW, true, WIDTH, 400)" 
					onmouseout="UnTip()">
				</td>
				<td>
					<table class="grid">
						<tr valign="top">
							<td onmouseover="Tip('{-$eve.EventName[1]-}')" onmouseout="UnTip()">
								{-assign var="tabind" value="`$tabind+1`"-}
								{-$eve.EventName[0]-}<b style="color:darkred;">*</b><br />
								<select id="EventId" name="EventId" style='width: 180px;' tabindex="{-$tabind-}"
									class="line" onFocus="showtip('{-$eve.EventName[2]-}', 'lightblue')">
									<option value=""></option>
									{-foreach name=eln key=key item=item from=$evel-}
										<option value="{-$key-}" onKeyPress="showtip('{-$item[1]-}', 'lightblue')" 
											onMouseOver="showtip('{-$item[1]-}', 'lightblue')">{-$item[0]-}</option>
									{-/foreach-}
								</select>
							</td>
							<td onmouseover="Tip('{-$eve.EventMagnitude[1]-}')" onmouseout="UnTip()">
								{-assign var="tabind" value="`$tabind+1`"-}
								{-$eve.EventMagnitude[0]-}<br />
								<input id="EventMagnitude" name="EventMagnitude" type="text" size="5" tabindex="{-$tabind-}" 
									class="line" onFocus="showtip('{-$eve.EventMagnitude[2]-}', 'lightblue')"
									onkeypress="return blockChars(event, this.value, 'text:');" />
							</td>
							<td onmouseover="Tip('{-$eve.EventDuration[1]-}')" onmouseout="UnTip()">
								{-assign var="tabind" value="`$tabind+1`"-}
								{-$eve.EventDuration[0]-}<br />
								<input id="EventDuration" name="EventDuration" type="text" size="5" tabindex="{-$tabind-}" 
									class="line" onFocus="showtip('{-$eve.EventDuration[2]-}', 'lightblue')"
									onkeypress="return blockChars(event, this.value, 'integer:');" />
							</td>
							<td onmouseover="Tip('{-$eve.EventNotes[1]-}')" onmouseout="UnTip()">
								{-assign var="tabind" value="`$tabind+1`"-}
								{-$eve.EventNotes[0]-}<br />
								<input type="texto" id="EventNotes" name="EventNotes" style="width: 350px;" class="line"
									tabindex="{-$tabind-}" onFocus="showtip('{-$eve.EventNotes[2]-}', 'lightblue')"
									onkeypress="return blockChars(event, this.value, 'text:');" />
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<!-- BEG CAUSE SECTION -->
			<tr style="border:1px solid #ffffc0;">
				<td width="30px" valign="top" style="border:0px;">
					<img src="images/di_cautag.png" 
						onmouseover="Tip('<b>{-#mcauses#-}</b><br />{-$dmg.MetGuidecauses[2]-}', BGCOLOR, '#ffffc0', SHADOW, true, WIDTH, 400)" 
						onmouseout="UnTip()">
				</td>
				<td>
					<table class="grid">
						<tr>
							<td onmouseover="Tip('{-$cau.CauseName[1]-}')" onmouseout="UnTip()">
								{-assign var="tabind" value="`$tabind+1`"-}
								{-$cau.CauseName[0]-}<b style="color:darkred;">*</b><br />
								<select id="CauseId" name="CauseId" style='width: 180px;' class="line" 
									tabindex="{-$tabind-}" onFocus="showtip('{-$cau.CauseName[2]-}', '#ffffc0')">
									<option value=""></option>
									{-foreach name=cln key=key item=item from=$caul-}
										<option value="{-$key-}" onMouseOver="showtip('{-$item[1]-}', '#ffffc0')">{-$item[0]-}</option>
									{-/foreach-}
								</select>
							</td>
							<td onmouseover="Tip('{-$cau.CauseNotes[1]-}')" onmouseout="UnTip()">
								{-assign var="tabind" value="`$tabind+1`"-}
								{-$cau.CauseNotes[0]-}<br />
								<input type="text" id="CauseNotes" name="CauseNotes" style="width: 450px;" class="line"
									onkeypress="return blockChars(event, this.value, 'text:');" tabindex="{-$tabind-}" 
									onFocus="showtip('{-$cau.CauseNotes[2]-}', '#ffffc0')" />
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</form>
	<!-- END DI8 FORM CARD -->
	<div id="divDatacardParameter">
		<input type="hidden" id="prmRegionId" value="{-$reg-}">
		<input type="hidden" id="prmUserRole" value="{-$role-}">
		<input type="hidden" id="prmRecordNumber" value="0">
	</div>
</body>
</html>
