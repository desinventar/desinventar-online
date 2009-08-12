{-config_load file=`$lg`.conf section="di8_input"-}
{-*** SHOWING EFFECTS ***-}
{-if $ctl_effects-}
<?xml version="1.0" encoding="UTF-8" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8; no-cache" />
	<title>{-#ttitle#-} -{-$regname-}- {-$usr-} | {-$dicrole-}</title>
	<link rel="stylesheet" href="../css/desinventar.css" type="text/css"/>
	<link rel="stylesheet" href="../css/desinput.css" type="text/css"/>
	<script type="text/javascript" src="../include/prototype.js"></script>
	<script type="text/javascript" src="../include/combo-box.js"></script>
	<script type="text/javascript" src="../include/diadmin.js.php"></script>
	<!--	<link rel="stylesheet" href="../css/tabeffect.css" type="text/css">
	<script type="text/javascript" src="../include/tabber.js"></script> -->
	<script type="text/javascript" language="javascript">
		var mod = "di";
		function hidediv(myDiv) {
			$(myDiv).style.visibility = 'hidden';
		}
		function showdiv(myDiv) {
			$(myDiv).style.visibility = 'visible';
		}
		function showtip(tip, clr) {
			var d = $('_DIDesc');
			d.style.backgroundColor = clr;
			d.value = tip;
		}
		// Display Geography in form and search; k=geoid, l=0, desc='', opc=''
		function setgeo(k, l, desc, opc) {
			if (opc == "search") {
				var fld = '_DisasterGeographyId';
				var lev = '_lev'+ l;
				var op = '&opc='+ opc;
			}
			else {
				var fld = 'DisasterGeographyId';
				var lev = 'lev'+ l;
				var op = '';
			}
			if (k.length >= 5) {
				$(fld).value = k;
				updateList(lev, 'index.php', 'r={-$reg-}&cmd=list&GeographyId='+ k + op);
			}
			else if (k == '') {
				showtip(desc, '#d4baf6');
				val = $(fld).value;
				$(fld).value = val.substr(0, val.length - 5);
				$(lev).innerHTML = '';
			}
		}
		/*
		 * Submits the form below through AJAX and then calls the ajax_response function)
		  * @param   object  disasid   Disaster ID
		  *
		function findByDIId(data) {
				var qres = $('qresults');
				qres.cursor = "wait";
				var submitTo = '../desinventar/?data=' + data + '&action=findDIId';
				http('POST', submitTo, setDICard, document.DICard);
//		$('_UpdBut').className = "btn";
{-if $ro == ""-}
//		$('_UpdBut').disabled = false;
{-/if-}
//		uploadMsg("{-#tmsgeditcard#-}");
				qres.cursor = "default";
		}
		 *	Called when ajax data has been retrieved
			* @param   object  data   Javascript (JSON) data object received
			*                         through ajax call
			*
		function setDICard(data) {
			var elems = parent.document.DICard.elements;
			for  (var i=0; i < elems.length; i++) {
				myname = elems[i].name + "";
				if (myname.substring(0,1) != "_") {
					if (data != null) {
						eval("value = data." + elems[i].name + ";");
						if (myname.substring(0,19) == "DisasterGeographyId") {
							var nextlv = parseInt(myname.substring(19,20)) + 1;
//						getGeoItems(value, nextlv, false);
//						alert("Val: " + value + " lev: " + nextlv + " elem: " + elems[i].value);
						}
						setElementValue(elems[i], value);
					}
					else
						setElementValue(elems[i], '');
				}
			}
		}*/
		function setadmingeo(k, l) {
			var v = k.split("|");
			mod = 'geo';
			uploadMsg('');
			if (v[0] == -1) {
				setLevGeo('','','',1,'','','','geo');
				if (l == 0)
					$('aGeoParentId').value = '';
				$('geocmd').value='insert';
				$('alev' + l).style.display = "none";
			}
			else if (v[0] == -2) 
				$('geoaddsect').style.display = 'none';
			else {
				setLevGeo(v[0],v[1],v[2],v[3],'','','','geo');
				$('aGeoParentId').value = v[0];
				$('geocmd').value='update';
				updateList('alev' + l, 'geography.php', 'r={-$reg-}&geocmd=list&GeographyId=' + v[0]);
			}
		}
		function DisableEnableForm (xForm, disab) {
			objElems = xForm.elements;
			var myname = "";
			var mysty = "";
			if (disab)
				col = "#eee";
			else
				col = "#fff";
			for (i=0; i < objElems.length; i++) {
				myname = objElems[i].name + "";
				if (myname.substring(0,1) != "_") {
					objElems[i].disabled = disab;
					objElems[i].style.backgroundColor = col;
				}
			}
		}
		function switchEff(section) {
			if (section == 'effext') {
				$('eeimg').src="../images/di_efftag.png";
				$('efimg').src="../images/di_eeftag.png";
				$('effbas').style.display='none';
				$('effext').style.display='block';
			}
			if (section == 'effbas') {
				$('efimg').src="../images/di_efftag.png";
				$('eeimg').src="../images/di_eeftag.png";
				$('effbas').style.display='block';
				$('effext').style.display='none';
			}
		}
		function setActive () {
			updateList('dostat', '', 'u=1');
		}
		window.onload = function() {
			DisableEnableForm($('DICard'), true);
			uploadMsg("{-#tmsgnewcard#-}");
			var pe = new PeriodicalExecuter(setActive, 60);
		}
/*		window.onunload = function() {
			updateList('distatusmsg', '', 'r={-$reg-}&cmd=chkrelease&DisasterId='+ $('DisasterId').value);
		}
		document.write('<style type="text/css">.tabber{display:none;}<\/style>');*/
		function onSubmitBtn(btn) {
			$('dic').src="about:blank";
			switch (btn) {
				case "{-#bnew#-}":
					DisableEnableForm($('DICard'), false);
					setfocus('DisasterBeginTime[0]');
					$('DisasterId').value='';
					$('DICard').reset();
					$('_CMD').value = 'insertDICard';
					uploadMsg("{-#tmsgnewcardfill#-}");
					$('cardnew').disable();
					$('cardsav').enable();
					$('cardupd').disable();
					$('cardcln').enable();
					$('cardcan').enable();
					$('cardfnd').disable();
				break;
				case "{-#bupdate#-}":
					// check if DC is onused
					var lsAjax = new Ajax.Updater('distatusmsg', '', {
						method: 'get', parameters: 'r={-$reg-}&cmd=chklocked&DisasterId='+ $('DisasterId').value,
						onComplete: function(request) {
							var res = request.responseText;
							if (res.substr(0,8) == "RESERVED") {
								DisableEnableForm($('DICard'), false);
								setfocus('DisasterBeginTime[0]');
								$('_CMD').value = 'updateDICard';
								uploadMsg("{-#tmsgeditcardfill#-}");
								$('cardnew').disable();
								$('cardsav').enable();
								$('cardupd').disable();
								$('cardcan').enable();
								$('cardfnd').disable();
							}
							else 
								uploadMsg("{-#tdconuse#-}");
						}
					} );
				break;
				case "{-#bsave#-}":
					var fl = new Array('DisasterSerial', 'DisasterBeginTime[0]', 'DisasterSource', 
												'geolev0', 'EventId', 'CauseId', 'RecordStatus');
					if (checkForm(fl, "{-#errmsgfrm#-}")) {
						var lsAjax = new Ajax.Updater('distatusmsg', '', {
							method: 'get', parameters: 'r={-$reg-}&cmd=chkdiserial&DisasterSerial='+ 
								$('DisasterSerial').value + '&DisasterId='+ $('DisasterId').value,
							onComplete: function(request) {
								uploadMsg('');
								var res = request.responseText;
								// disabled check serial exists
								//if (res.substr(0,4) == "FREE") {
									$('DICard').submit();
									DisableEnableForm($('DICard'), true);
									$('cardnew').enable();
									$('cardsav').disable();
									$('cardupd').disable();
									$('cardcln').disable();
									$('cardcan').disable();
									$('cardfnd').enable();
//								}
//								else
//									alert("{-#tdisererr#-}");
							}
						} );
					}
				break;
				case "{-#bclean#-}":
					$('DICard').reset();
					$('lev0').innerHTML='';
					uploadMsg('');
					setfocus('DisasterBeginTime[0]');
				break;
				case "{-#bcancel#-}":
					updateList('distatusmsg', '', 'r={-$reg-}&cmd=chkrelease&DisasterId='+ $('DisasterId').value);
					$('DICard').reset();
					DisableEnableForm($('DICard'), true);
					$('lev0').innerHTML='';
					$('cardsav').disable();
					$('cardcln').disable();
					$('cardcan').disable();
					$('cardnew').enable();
					$('cardfnd').enable();
					uploadMsg("{-#tmsgnewcard#-}");
				break;
				case "{-#bexpsearch#-}":
				break;
			}
			return true;
		}
	</script>
	<style type="text/css">
		.bnew { background-image: url(../images/newicon.png) !important; }
		.bupd { background-image: url(../images/updateicon.png) !important; }
		.bsave { background-image: url(../images/saveicon.png) !important; }
		.bprint { background-image: url(../images/printicon.png) !important; }
		.bcancel { background-image: url(../images/cancelicon.png) !important; }
		.bfind { background-image: url(../images/findicon.png) !important; }
	</style>
</head>

<body>
<!-- BEG DI8 FORM CARD -->
	<div id="container" style="overflow:scroll;">
		<table width="900px">
			<tr>
				<td>
					<input type="button" id="cardnew" value="{-#bnew#-}" onClick="onSubmitBtn(this.value);" {-$ro-}>
					<input type="button" id="cardupd" value="{-#bupdate#-}" onClick="onSubmitBtn(this.value);" {-$ro-}>
					<input type="button" id="cardsav" value="{-#bsave#-}" onClick="onSubmitBtn(this.value);" {-$ro-}>
					<input type="button" id="cardcln" value="{-#bclean#-}" onClick="onSubmitBtn(this.value);" {-$ro-}>
					<input type="button" id="cardcan" value="{-#bcancel#-}" onClick="onSubmitBtn(this.value);" {-$ro-}>
					<input type="button" id="cardfnd" value="{-#bexpsearch#-}" onClick="onSubmitBtn(this.value);" {-$ro-}>
					&nbsp;&nbsp;|&nbsp;&nbsp;
					<input type="button" value="<<" onClick="setDICard('{-$reg-}', {-$fst-}, '');" {-$ro-}>
					<input type="button" value="<" disabled {-$ro-}>
					<span class="dlgmsg" id="dostat"></span>
					<input type="button" value=">" disabled {-$ro-}>
					<input type="button" value=">>" onClick="setDICard('{-$reg-}', {-$lst-}, '');" {-$ro-}>
					<br><span class="dlgmsg" id="distatusmsg"></span>
				</td>
				<td align="right">
					<iframe name="dic" id="dic" frameborder="1" style="height:30px; width:220px;" src="about:blank"></iframe>
				</td>
			</tr>
		</table>
		<form id="DICard" action="index.php" method="POST" target="dic">
			<input type="hidden" name="_REG" id="_REG" value="{-$reg-}">
			<input type="hidden" name="DisasterId" id="DisasterId" value="">
			<input type="hidden" name="RecordAuthor" id="RecordAuthor" value="{-$usr-}">
			<input type="hidden" name="RecordCreation" id="RecordCreation">
			<input type="hidden" name="_CMD" id="_CMD" value="">
			<table border="1" cellspacing="8" width="900px">
				<!-- DATACARD INFORMATION SECTION -->
				<tr>
					<td width="30px" style="border:0px;" valign="top">
						&nbsp;
					</td>
					<td style="border-color:#000000;">
						<table class="grid">
							<tr valign="top">
								<td ext:qtip="{-$dis.DisasterBeginTime[1]-}">
									{-$dis.DisasterBeginTime[0]-}<b style="color:darkred;">*</b><br>
									<input id="DisasterBeginTime[0]" name="DisasterBeginTime[0]" style="width:36px;" class="line"
										tabindex="1" type="text" maxlength="4" onFocus="showtip('{-$dis.DisasterBeginTime[2]-}', '#d4baf6')"
										onkeypress="return blockChars(event, this.value, 'integer:4');" 
										onBlur="if($('DisasterSerial').value == '') $('DisasterSerial').value = this.value + '-';">
									<input id="DisasterBeginTime[1]" name="DisasterBeginTime[1]" style="width:18px;" class="line"
										tabindex="2" type="text" maxlength="2" onFocus="showtip('{-$dis.DisasterBeginTime[2]-}', '#d4baf6')"
										onkeypress="return blockChars(event, this.value, 'integer:2');" 
										onBlur="if (parseInt($('DisasterBeginTime[1]').value,10) < 1 || 
																parseInt($('DisasterBeginTime[1]').value,10) > 12 ) $('DisasterBeginTime[1]').value = '';">
									<input id="DisasterBeginTime[2]" name="DisasterBeginTime[2]" style="width:18px;" class="line"
										tabindex="3" type="text" maxlength="2" onFocus="showtip('{-$dis.DisasterBeginTime[2]-}', '#d4baf6')"
										onkeypress="return blockChars(event, this.value, 'integer:2');"
										onBlur="if (parseInt($('DisasterBeginTime[2]').value,10) < 1 || 
																parseInt($('DisasterBeginTime[2]').value,10) > 31 ) $('DisasterBeginTime[2]').value = '';">
								</td>
								<td ext:qtip="{-$dis.DisasterSource[1]-}">
									{-$dis.DisasterSource[0]-}<b style="color:darkred;">*</b><br>
									<input id="DisasterSource" name="DisasterSource" size="50" class="line" type="text" 
										onkeypress="return blockChars(event, this.value, 'text:');"
										tabindex="4" onFocus="showtip('{-$dis.DisasterSource[2]-}', '#d4baf6')">
								</td>
								<td>
									{-#tstatus#-}<b style="color:darkred;">*</b><br>
									<select name="RecordStatus" id="RecordStatus" tabindex="5" class="line"
											onFocus="showtip('{-$rc1.RecordStatus[1]-}', '')">
										<option value=""></option>
{-if $ctl_rcsl-}
										<option value="PUBLISHED">{-#tstatpublished#-}</option>
{-/if-}
										<option value="READY">{-#tstatready#-}</option>
										<option value="DRAFT">{-#tstatdraft#-}</option>
										<option value="TRASH">{-#tstatrash#-}</option>
{-if $ctl_rcsl-}
										<option value="DELETED">{-#tstatdeleted#-}</option>
{-/if-}
									</select>
								</td>
								<td ext:qtip="{-$dis.DisasterSerial[1]-}">
									{-$dis.DisasterSerial[0]-}<b style="color:darkred;">*</b><br>
									<input id="DisasterSerial" name="DisasterSerial" type="text" size="15" class="line"
										tabindex="6" maxlength="50" onFocus="showtip('{-$dis.DisasterSerial[2]-}', '#d4baf6')"
										onkeypress="return blockChars(event, this.value, 'alphanumber:');">
<!--									  onBlur="updateList('distatusmsg', '', 'r={-$reg-}&cmd=chkdiserial&DisasterSerial='+ 
													$('DisasterSerial').value + '&DisasterId='+ $('DisasterId').value);">-->
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td width="30px" style="border:0px;" valign="top">
						<img src="../images/di_geotag.png" ext:qtip="<b>{-#mgeography#-}</b><br>{-$dmg.MetGuidegeography[2]-}">
					</td>
					<td>
						<table class="grid">
							<tr valign="top">
								<td ext:qtip="{-$dis.DisasterGeographyId[1]-}">
									{-$dis.DisasterGeographyId[0]-}<b style="color:darkred;">*</b><br>
									<input id="DisasterGeographyId" name="DisasterGeographyId" type="hidden">
									<span id="lst_geo" class="geodiv" style="width: 180px; height: 30px;">
{-/if-}
{-*** PRINT LEVEL ITEMS ACCORDING WITH SELECTION - USED TO SELECTION AND SEARCH ***-}
{-if $ctl_geolist-}
 {-if $lev <= $levmax-}
										{-$lev-}- {-$levname[0]-}:
										<select onChange="setgeo(this.options[this.selectedIndex].value, {-$lev-},'{-$levname[1]-}','{-$opc-}');" 
												autoComplete="true" style="width:180px; background-Color:#eee;" tabindex="7" id="geolev{-$lev-}"
												onFocus="showtip('{-$dis.DisasterGeographyId[2]-}', '#d4baf6')" disabled>
											<option value="" style="text-align:center;">--</option>
 {-foreach name=geol key=key item=item from=$geol-}
  {-if $item[2]-}
											<option value="{-$key-}">{-$item[1]-}</option>
  {-/if-}
 {-/foreach-}
										</select><br><span id="lev{-$lev-}"></span>
 {-/if-}
{-/if-}
{-*** CONTINUE SHOWING DATACARD ***-}
{-if $ctl_effects-}
{-assign var="tabind" value="10"-}
									</span>
									<br>
								</td>
{-assign var="tabind" value="`$tabind+1`"-}
								<td ext:qtip="{-$dis.DisasterSiteNotes[1]-}">
									{-$dis.DisasterSiteNotes[0]-}<br>
									<textarea id="DisasterSiteNotes" name="DisasterSiteNotes" style="height: 40px;" cols="25"
										onkeypress="return blockChars(event, this.value, 'text:');"
										tabindex="{-$tabind-}" onFocus="showtip('{-$dis.DisasterSiteNotes[2]-}', '#d4baf6')"></textarea>
								</td>
								<td>
{-assign var="tabind" value="`$tabind+1`"-}
									<span ext:qtip="{-$dis.DisasterLatitude[1]-}">
									{-$dis.DisasterLatitude[0]-}<br>
									<input id="DisasterLatitude" name="DisasterLatitude" type="text" size="10" class="line"
											tabindex="{-$tabind-}" onFocus="showtip('{-$dis.DisasterLatitude[2]-}', '#d4baf6')"
											onkeypress="return blockChars(event, this.value, 'double:10');">
									</span><br>
{-assign var="tabind" value="`$tabind+1`"-}
									<span ext:qtip="{-$dis.DisasterLongitude[1]-}">
									{-$dis.DisasterLongitude[0]-}<br>
									<input id="DisasterLongitude" name="DisasterLongitude" type="text" size="10" class="line"
											tabindex="{-$tabind-}" onFocus="showtip('{-$dis.DisasterLongitude[2]-}', '#d4baf6')"
											onkeypress="return blockChars(event, this.value, 'double:10');">
									</span></a>
								</td>
							</tr>
						</table>
					</td>
				</tr>
      	<!-- END DATACARD --> 
				<tr>
					<td width="30px" valign="top" style="border:0px;">
						<a href="javascript:void(null)" onClick="switchEff('effbas')">
						 <img id="efimg" src="../images/di_efftag.png" border=0
						 			ext:qtip="<b>{-#tbaseffects#-}</b><br>{-$dmg.MetGuidedatacards[2]-}"></a>
						<br><br>
						<a href="javascript:void(null)" onClick="switchEff('effext')">
						 <img id="eeimg" src="../images/di_eeftag.png" border=0 
						 			ext:qtip="<b>{-#textraeffect#-}</b><br>{-$dmg.MetGuideextraeffects[2]-}"></a>
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
                  <b align="left">{-#teffects#-}</b><br>
                  <table width="100%" class="grid">
                  	<!-- BEGIN Table Effects over People-->
{-foreach name=ef1 key=key item=item from=$ef1-}
{-assign var="tabind" value="`$tabind+1`"-}
                    <tr>
                      <td align="right"><span ext:qtip="{-$item[1]-}">{-$item[0]-}</span></td>
                      <td>
                        <select id="{-$key-}" name="{-$key-}" style="width:120px;" tabindex="{-$tabind-}"
                            onKeyPress="edit(event);" onFocus="showtip('{-$item[2]-}', '#f1bd41');" 
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
                  <b align="center">{-#tsectors#-}</b><br>
                  <table width="100%" class="grid"> <!-- BEGIN Table Sectors -->
{-foreach name=sec key=key item=item from=$sec-}
{-assign var="tabind" value="`$tabind+1`"-}
                    <tr>
                      <td align="right"><span ext:qtip="{-$item[1]-}">{-$item[0]-}</span></td>
                      <td>
                        <select id="{-$key-}" name="{-$key-}" style="width:120px;" tabindex="{-$tabind-}" 
                            onFocus="showtip('{-$item[2]-}', '#f1bd41')">
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
                  <br> <!-- BEGIN Table Effects over Affected -->
{-foreach name=ef3 key=key item=item from=$ef3-}
{-assign var="tabind" value="`$tabind+1`"-}
                  <span ext:qtip="{-$item[1]-}">
                  {-$item[0]-}<br>
                  <input id="{-$key-}" name="{-$key-}" type="text" size="7" onBlur="
                        if(parseInt(this.value) > 0) { $('{-$sc3[$key]-}').value='-1';}
                        if(parseInt(this.value) ==0) { $('{-$sc3[$key]-}').value='0';}"
                      onFocus="showtip('{-$item[2]-}', '#f1bd41')" tabindex="{-$tabind-}" class="line"
                      onkeypress="return blockChars(event, this.value, 'double:10');" value="0">
                  </span><br>
{-/foreach-}
                </td>
                <td valign="top">
                  <b align="right">{-#tlosses#-}</b><br> <!-- BEGIN Table Effects over $$ -->
{-foreach name=ef2 key=key item=item from=$ef2-}
{-assign var="tabind" value="`$tabind+1`"-}
                  <span ext:qtip="{-$item[1]-}">
                  {-$item[0]-}<br>
                  <input id="{-$key-}" name="{-$key-}" type="text" size="11" tabindex="{-$tabind-}" class="line"
                      onFocus="showtip('{-$item[2]-}', '#f1bd41');" value="0"
                      onkeypress="return blockChars(event, this.value, 'double:');">
                  </span><br>
{-/foreach-}
{-foreach name=ef4 key=key item=item from=$ef4-}
{-assign var="tabind" value="`$tabind+1`"-}
                  <span ext:qtip="{-$item[1]-}">
                  {-$item[0]-}<br>
                  <textarea id="{-$key-}" name="{-$key-}" cols="25" style="height: {-if $key=='EffectNotes'-}70{-else-}30{-/if-}px;"
                      onBlur="if(this.value != '') { $('SectorOther').value='-1'; }" 
                      onkeypress="return blockChars(event, this.value, 'text:');"
                      onFocus="showtip('{-$item[2]-}', '#f1bd41')" tabindex="{-$tabind-}"></textarea>
                  </span><br>
{-/foreach-}
                </td>
              </tr>
            </table>
            <!-- BEG EXTRA EFFECTS FIELDS -->
            <table class="grid" id="effext" style="display:none;">
              <tr><td><br></td></tr>
{-assign var="tabeef" value="200"-}
{-foreach name=eefl key=key item=item from=$eefl-}
{-assign var="tabeef" value="`$tabeef+1`"-}
{-if ($smarty.foreach.eefl.iteration - 1) % 3 == 0-}
              <tr>
{-/if-}
               <td ext:qtip="{-$item[1]-}">{-$item[0]-}<br>
                <input type="text" id="{-$key-}" name="{-$key-}" size="30" class="line" tabindex="{-$tabeef-}"
                	onFocus="showtip('{-$item[1]-}', '#f1bd41')" onkeypress="return blockChars(event, this.value, 'text:');"></td>
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
          	<img src="../images/di_evetag.png" ext:qtip="<b>{-#mevents#-}</b><br>{-$dmg.MetGuideevents[2]-}">
          </td>
          <td>
            <table class="grid">
              <tr valign="top">
                <td ext:qtip="{-$eve.EventName[1]-}">
{-assign var="tabind" value="`$tabind+1`"-}
                  {-$eve.EventName[0]-}<b style="color:darkred;">*</b><br>
                  <select id="EventId" name="EventId" style='width: 180px;' tabindex="{-$tabind-}"
                      onFocus="showtip('{-$eve.EventName[2]-}', 'lightblue')">
                    <option value=""></option>
{-foreach name=eln key=key item=item from=$evel-}
                    <option value="{-$key-}" onKeyPress="showtip('{-$item[1]-}', 'lightblue')" 
                        onMouseOver="showtip('{-$item[1]-}', 'lightblue')">{-$item[0]-}</option>
{-/foreach-}
                  </select>
                </td>
                <td ext:qtip="{-$eve.EventMagnitude[1]-}">
{-assign var="tabind" value="`$tabind+1`"-}
                  {-$eve.EventMagnitude[0]-}<br>
                  <input id="EventMagnitude" name="EventMagnitude" type="text" size="5" tabindex="{-$tabind-}" 
                  		class="line" onFocus="showtip('{-$eve.EventMagnitude[2]-}', 'lightblue')"
                  		onkeypress="return blockChars(event, this.value, 'text:');">
                </td>
                <td ext:qtip="{-$eve.EventDuration[1]-}">
{-assign var="tabind" value="`$tabind+1`"-}
                  {-$eve.EventDuration[0]-}<br>
                  <input id="EventDuration" name="EventDuration" type="text" size="5" tabindex="{-$tabind-}" 
                  		class="line" onFocus="showtip('{-$eve.EventDuration[2]-}', 'lightblue')"
                  		onkeypress="return blockChars(event, this.value, 'integer:');">
                </td>
                <td ext:qtip="{-$eve.EventNotes[1]-}">
{-assign var="tabind" value="`$tabind+1`"-}
                  {-$eve.EventNotes[0]-}<br>
                  <input type="texto" id="EventNotes" name="EventNotes" style="width: 350px;" class="line"
                  		tabindex="{-$tabind-}" onFocus="showtip('{-$eve.EventNotes[2]-}', 'lightblue')"
                      onkeypress="return blockChars(event, this.value, 'text:');">
                </td>
              </tr>
            </table>
          </td>
        </tr>
        <!-- BEG CAUSE SECTION -->
        <tr style="border:1px solid #ffffc0;">
          <td width="30px" valign="top" style="border:0px;">
          	<img src="../images/di_cautag.png" ext:qtip="<b>{-#mcauses#-}</b><br>{-$dmg.MetGuidecauses[2]-}">
          </td>
          <td>
            <table class="grid">
              <tr>
                <td ext:qtip="{-$cau.CauseName[1]-}">
{-assign var="tabind" value="`$tabind+1`"-}
                  {-$cau.CauseName[0]-}<b style="color:darkred;">*</b><br>
                  <select id="CauseId" name="CauseId" style='width: 180px;' class="line" 
                      tabindex="{-$tabind-}" onFocus="showtip('{-$cau.CauseName[2]-}', '#ffffc0')">
                    <option value=""></option>
{-foreach name=cln key=key item=item from=$caul-}
                    <option value="{-$key-}" onMouseOver="showtip('{-$item[1]-}', '#ffffc0')">{-$item[0]-}</option>
{-/foreach-}
                  </select>
                </td>
                <td ext:qtip="{-$cau.CauseNotes[1]-}">
{-assign var="tabind" value="`$tabind+1`"-}
                  {-$cau.CauseNotes[0]-}<br>
                  <input type="texto" id="CauseNotes" name="CauseNotes" style="width: 450px;" class="line"
                  		onkeypress="return blockChars(event, this.value, 'text:');" tabindex="{-$tabind-}" 
                      onFocus="showtip('{-$cau.CauseNotes[2]-}', '#ffffc0')"></textarea>
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
	 </form>
	</div>
<!-- END DI8 FORM CARD -->

<!-- BEG HELP SECTION -->
 <div id="south">
     <textarea id="_DIDesc" wrap="hard" class="hlp" readonly style="width:80%; height:30px;"></textarea>
      <a href="javascript:void(null)" onClick="runWin('../doc/?m=metguide', 'doc');"
      	class="dlgmsg" style="font-size: 8pt;">{-#hmoreinfo#-}</a>
 </div>
<!-- END HELP SECTION -->

 </body>
</html>
{-/if-}
{-if $ctl_result-}
<?xml version="1.0" encoding="UTF-8" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<body>
 <table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td style="font-family:arial,tahoma,helvetica,cursive; font-size:11px; color:#dbab28;">
		 {-if $statusmsg == 'duplicate'-}<b>{-#tdcerror#-}:</b> {-#tdisererr#-}
		 {-elseif $statusmsg == 'insertok'-} {-#tdccreated#-} (Serial={-$diserial-})
		 {-elseif $statusmsg == 'updateok'-} {-#tdcupdated#-} (Serial={-$diserial-})
		 {-else-}{-$statusmsg-}{-/if-}
		</td>
		<td style="font-family:arial,tahoma,helvetica,cursive; font-size:10px; color:#000000;">
		 {-#tstatpublished#-} {-$dipub-}, {-#tstatready#-} {-$direa-}</td>
	</tr>
 </table>
</body>
</html>
{-/if-}
{-if $ctl_updater-}
<span style="width:10px; height:10px; position:absolute; left:0; top:0; background-color:{-$stat-};"></span>
{-/if-}
