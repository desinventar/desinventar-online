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
				col = "#fff"
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
	</script>
<!--	<link rel="stylesheet" href="../css/tabeffect.css" type="text/css">
	<script type="text/javascript" src="../include/tabber.js"></script> -->
  <!-- ExtJS 2.0.1 -->
  <link rel="stylesheet" type="text/css" href="/extJS/resources/css/ext-all.css"/>
  <link rel="stylesheet" type="text/css" href="/extJS/resources/css/xtheme-gray.css"/>
  <script type="text/javascript" src="/extJS/adapter/ext/ext-base.js"></script>
  <script type="text/javascript" src="/extJS/ext-all.js"></script>
  <script type="text/javascript">
		Ext.onReady(function()
		{
			Ext.QuickTips.init();
			var mfile = new Ext.menu.Menu({
				id: "fileMenu",
				items: [
					{	text: "{-#mprint#-}", handler: onMenuItem },
					{	text: "{-#mquit#-}", handler: onMenuItem } 
				]
			});
			var mconfig = new Ext.menu.Menu({
				id: "configMenu",
				items: [
					{	text: "{-#mreginfo#-}", handler: onConfigItem },
					{	text: "{-#mgeolevel#-}", handler: onConfigItem },
					{	text: "{-#mgeography#-}", handler: onConfigItem },
					{	text: "{-#mevents#-}", handler: onConfigItem },
					{	text: "{-#mcauses#-}", handler: onConfigItem },
					{	text: "{-#mregrol#-}", handler: onConfigItem },
					{	text: "{-#mreglog#-}", handler: onConfigItem },
					{	text: "{-#meeffects#-}", handler: onConfigItem },
					{	text: "{-#mimport#-}", handler: onConfigItem }
				]
			});
			var tb = new Ext.Toolbar();
			tb.render("toolbar");
			tb.add({ text: "{-#mfile#-}", menu: mfile });
{-if $showconfig-}
			tb.add("-", { text:	"{-#mconfig#-}", menu: mconfig });
{-/if-}
			// New button
			tb.add(
				new Ext.Toolbar.Button({
					id: 			"cardnew",
					text: 		"{-#bnew#-}",
					handler: 	onSubmitBtn, 
					tooltip: 	{ text:"{-#tnewdesc#-}", title:"{-#tnewtitle#-}", autoHide:true },
					//style:		"background-color:#bbb;",
{-if $ro == "disabled"-}
					disabled:	true, {-/if-}
					iconCls: 	"bnew"
				}), "-");
			// Update button
			tb.add(
				new Ext.Toolbar.Button({
					id:				"cardupd",
					text: 		"{-#bupdate#-}",
					handler: 	onSubmitBtn, 
					tooltip: 	{ text:"{-#tupddesc#-}", title:"{-#tupdtitle#-}", autoHide:true },
					//style:		"background-color:#bbb;",
					iconCls: 	"bupd",
					disabled:	true
				}), "-");
			// Save button
			tb.add(
				new Ext.Toolbar.Button({
					id:				"cardsav",
					text: 		"{-#bsave#-}",
					handler: 	onSubmitBtn, 
					tooltip: 	{ text:"{-#tsavdesc#-}", title:"{-#tsavtitle#-}", autoHide:true },
					//style:		"background-color:#bbb;",
					iconCls: 	"bsave",
					disabled:	true
				}), "-");
			// Clean button
			tb.add(
				new Ext.Toolbar.Button({
					id:				"cardcln",
					text: 		"{-#bclean#-}",
					handler: 	onSubmitBtn, 
					tooltip: 	{ text:"{-#tclndesc#-}", title:"{-#tclntitle#-}", autoHide:true },
					//style:		"background-color:#bbb;",
					disabled:	true
				}), "-");
			// Cancel button
			tb.add(
				new Ext.Toolbar.Button({
					id:				"cardcan",
					text: 		"{-#bcancel#-}",
					handler: 	onSubmitBtn, 
					tooltip: 	{ text:"{-#tcandesc#-}", title:"{-#tcantitle#-}", autoHide:true },
					//style:		"background-color:#bbb;",
					iconCls: 	"bcancel",
					disabled:	true
				}), "-");
			// Quick Query
/*			tb.addField(
				new Ext.form.TextField({
					id:					"quickf",
					name: 			"DCQuery",
					emptyText:	"{-#bsearchcard#-}...",
					width: 			200,
					iconCls: 	  "bfind",
					disabled: 	false
				}) );*/
			// Find button
			tb.add(
				new Ext.Toolbar.Button({
					id:				"cardfnd",
					text: 		"{-#bexpsearch#-}",
					handler: 	onSubmitBtn, 
					tooltip: 	{ text:"{-#texpdesc#-}", title:"{-#texptitle#-}", autoHide:true },
					//style:		"background-color:#bbb;",
					iconCls: 	"bfind",
					disabled:	false
				}), "-");
			// functions to display feedback
			var qryw;
			var resw;
			function onSubmitBtn(btn) {
				mod = "di";
				$('ifr').src="about:blank";
				switch (btn.text) {
					case "{-#bnew#-}":
						DisableEnableForm($('DICard'), false);
						setfocus('DisasterBeginTime[0]');
						$('DisasterId').value='';
						$('DICard').reset();
						$('_CMD').value = 'insertDICard';
						uploadMsg("{-#tmsgnewcardfill#-}");
						tb.items.get('cardnew').disable();
						tb.items.get('cardsav').enable();
						tb.items.get('cardupd').disable();
						tb.items.get('cardcln').enable();
						tb.items.get('cardcan').enable();
						tb.items.get('cardfnd').disable();
						var qw = Ext.getCmp('qryw');
						var rw = Ext.getCmp('resw');
						try {
							qw.hide();
							rw.hide();
						}
						catch(ex) {}
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
									tb.items.get('cardnew').disable();
									tb.items.get('cardsav').enable();
									tb.items.get('cardupd').disable();
									tb.items.get('cardcan').enable();
									tb.items.get('cardfnd').disable();
									var qw = Ext.getCmp('qryw');
									var rw = Ext.getCmp('resw');
									qw.hide();
									rw.hide();
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
//									if (res.substr(0,4) == "FREE") {
										$('DICard').submit();
										DisableEnableForm($('DICard'), true);
										tb.items.get('cardnew').enable();
										tb.items.get('cardsav').disable();
										tb.items.get('cardupd').disable();
										tb.items.get('cardcln').disable();
										tb.items.get('cardcan').disable();
										tb.items.get('cardfnd').enable();
//									}
//									else
//										alert("{-#tdisererr#-}");
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
						tb.items.get('cardsav').disable();
						tb.items.get('cardcln').disable();
						tb.items.get('cardcan').disable();
						tb.items.get('cardnew').enable();
						tb.items.get('cardfnd').enable();
						uploadMsg("{-#tmsgnewcard#-}");
					break;
					case "{-#bexpsearch#-}":
						if(!qryw) {
              qryw = new Ext.Window({id:'qryw',
                el:'qry-win',  layout:'fit',  width:300, height:270, 
                closeAction:'hide', plain: true, animCollapse: false,
                items: new Ext.Panel({
                    contentEl: 'qry-cfg', autoScroll: true }),
                buttons: [{
                    text:'{-#tclean#-}',
                    handler: function() {
                        $('_lev0').innerHTML='';
                        $('_DisasterGeographyId').value='';
                        $('DIFind').reset();
                    }
                  },{
                    text:'{-#tsend#-}',
                    handler: function() {
                        if(!resw) {
                          resw = new Ext.Window({ id:'resw',
                            el:'res-win',  layout:'fit',  width:950, height:380, 
                            closeAction:'hide', plain: true, animCollapse: false,
                            items: new Ext.Panel({ contentEl: 'res-cfg', autoScroll: true })
                            //,buttons: [{ text:"{-#tclose#-}", handler: function() { resw.hide(); } }]
                          });
                          resw.setPosition(10, 300);
                          qryw.hide();
                        }
                    		resw.show(this);
                        $('DIFind').submit();
{-if $ro != "disabled"-}
												tb.items.get('cardupd').enable(); {-/if-}
                    }
                  },{
                    text:"{-#tclose#-}",
                    handler: function() {
                    		qryw.hide(); 
                    }
                  }]
              });
              qryw.setPosition(650, 30);
						}
						qryw.show(this);
						//uploadMsg("{-#tmsgsearchcards#-}");
					break;
				}
				return true;
			}
			function onMenuItem(item) {
				switch (item.text) {
					case "{-#mprint#-}":
						window.print();
					break;
					case "{-#mquit#-}":
						self.close();
					break;
				}
			}
			function onConfigItem(item) {
				var w = Ext.getCmp('westm');
				w.expand();
				var myurl = null;
				switch (item.text) {
					case "{-#mreginfo#-}":
						myurl = "regioninfo.php?r={-$reg-}";
					break;
					case "{-#mreglog#-}":
						myurl = "regionlog.php?r={-$reg-}";
					break;
					case "{-#mregrol#-}":
						myurl = "regionrol.php?r={-$reg-}";
					break;
					case "{-#mgeolevel#-}":
						myurl = "geolevel.php?r={-$reg-}";
					break;
					case "{-#mgeography#-}":
						myurl = "geography.php?r={-$reg-}";
					break;
					case "{-#mevents#-}":
						myurl = "events.php?r={-$reg-}";
					break;
					case "{-#mcauses#-}":
						myurl = "causes.php?r={-$reg-}";
					break;
					case "{-#meeffects#-}":
						myurl = "extraeffects.php?r={-$reg-}";
					break;
					case "{-#mimport#-}":
						myurl = "import.php?r={-$reg-}";
					break;
				}
				w.load({
					url: myurl,
					text: "{-#mloading#-}"
				});
			}
			var viewport = new Ext.Viewport( {
				layout:'border',
				items:[ {
					region:'north',
					contentEl: 'north',
					height: 30
				},{
					region:'south',
					contentEl: 'south',
					split:false,
					height: 80,
					minSize: 100,
					maxSize: 200,
					collapsible: true,
					title:"{-#thelp#-}",
					margins:'0 0 0 0'
				},{ 
					region: 'center',
					contentEl: 'container',
					autoScroll: true
				}, new Ext.Panel({
					region: 'west',
					id: 'westm',
					title: "{-#mconfig#-}",
					split: false,
					width: 300,
					collapsible: true,
					margins: '0 0 0 5',
					collapseMode: 'mini',
					autoScroll: true
				})
				]
			});
			var w = Ext.getCmp('westm');
			w.collapse();
			// quicktips
			Ext.apply(Ext.QuickTips.getQuickTip(), {
				maxWidth: 200, minWidth: 100, showDelay: 50, trackMouse: true });
		});
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
<!-- HEAD SECTION  onBeforeUnload="return '{-#tcheckquit#-}'"-->
	<div id="north">
		<div id="toolbar"></div>
	</div>
<!-- BEG DI8 FORM CARD -->
	<div id="container" style="overflow:scroll;">
		<table width="900px">
			<tr>
				<td width="300px">
					<span class="dlgmsg" id="distatusmsg"></span>
				</td>
				<td>
					<span class="dlgmsg" id="dostat"></span>
<!--					<input type="button" class="medium" value="<<" onClick="setCard('{-$reg-}', {-$fst-}, '');">
					<input type="button" class="medium" value="<" style="backgroundColor:#eee;" disabled>
					&nbsp;&nbsp;&nbsp;&nbsp;
					<input type="button" class="medium" value=">" style="backgroundColor:#eee;" disabled>
					<input type="button" class="medium" value=">>" onClick="setCard('{-$reg-}', {-$lst-}, '');">-->
				</td>
				<td align="right">
					<iframe name="ifr" id="ifr" frameborder="0" style="height:40px; width:450px;" src="about:blank"></iframe>
				</td>
			</tr>
		</table>
		<form id="DICard" action="index.php" method="POST" target="ifr">
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
												autoComplete="true" style="width: 180px;" tabindex="7" id="geolev{-$lev-}"
												onFocus="showtip('{-$dis.DisasterGeographyId[2]-}', '#d4baf6')">
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
                      onkeypress="return blockChars(event, this.value, 'double:10');">
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
                      onFocus="showtip('{-$item[2]-}', '#f1bd41');"
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
                <td ext:qtip="{-$eve.EventId[1]-}">
{-assign var="tabind" value="`$tabind+1`"-}
                  {-$eve.EventId[0]-}<b style="color:darkred;">*</b><br>
                  <select id="EventId" name="EventId" style='width: 180px;' tabindex="{-$tabind-}"
                      onFocus="showtip('{-$eve.EventId[2]-}', 'lightblue')">
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
                <td ext:qtip="{-$cau.CauseId[1]-}">
{-assign var="tabind" value="`$tabind+1`"-}
                  {-$cau.CauseId[0]-}<b style="color:darkred;">*</b><br>
                  <select id="CauseId" name="CauseId" style='width: 180px;' class="line" 
                      tabindex="{-$tabind-}" onFocus="showtip('{-$cau.CauseId[2]-}', '#ffffc0')">
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

<!-- BEG QUERY OPTIONS -->
<div id="qry-win" class="x-hidden">
	<div class="x-window-header">{-#texptitle#-}</div>
	<div id="qry-cfg">
	<form method="POST" target="iframe" id="DIFind" action="../desconsultar/data.php?r={-$reg-}&opt=singlemode">
   <table border="0" class="grid">
	  <tr id="serial">
	   <td>{-$dis.DisasterSerial[0]-}</td>
	   <td><input id="_DisasterSerial" name="D:DisasterSerial" type="text" size="25" tabindex="101" class="line"
	   		maxlength="50" onFocus="showtip('{-$dis.DisasterSerial[2]-}','')"></td>
	  </tr>
	  <tr>
	   <td>
	   	{-#tsince#-}<br>{-#tuntil#-}
	   </td>
	   <td>
	   	<input type="text" id="DisasterBeginTime[0]" name="D:DisasterBeginTime[0]" size=4 maxlength=4 class="line">
		  <input type="text" id="DisasterBeginTime[1]" name="D:DisasterBeginTime[1]" size=2 maxlength=2 class="line">
		  <input type="text" id="DisasterBeginTime[2]" name="D:DisasterBeginTime[2]" size=2 maxlength=2 class="line">
	   	<br>
	   	<input type="text" id="DisasterEndTime[0]" name="D:DisasterEndTime[0]" size=4 maxlength=4 class="line">
		  <input type="text" id="DisasterEndTime[1]" name="D:DisasterEndTime[1]" size=2 maxlength=2 class="line">
		  <input type="text" id="DisasterEndTime[2]" name="D:DisasterEndTime[2]" size=2 maxlength=2 class="line">
	   </td>
    </tr>
	  <tr valign="top">
	   <td colspan=2>{-$dis.DisasterGeographyId[0]-}<br>
	   	 <input id="_DisasterGeographyId" name="D:DisasterGeographyId[]" type="hidden">
	   	 <div class="geodiv" style="height:60px;">
{-if $lev <= $levmax-}
 				{-$lev-}- {-$levname[0]-}:
 				<select onChange="setgeo(this.options[this.selectedIndex].value,{-$lev-},'','search');"
 						style="width: 120px;" id="_geolev{-$lev-}">
					<option value="" style="text-align:center;">--</option>
 {-foreach name=geol key=key item=item from=$geol-}
  {-if $item[2]-}
  				 <option value="{-$key-}">{-$item[1]-}</option>
  {-/if-}
 {-/foreach-}
  			</select><br>
  			<span id="_lev{-$lev-}"></span>
{-/if-}
			 </div>
	   </td>
	  </tr>
	  <tr>
	   <td>{-$eve.EventId[0]-}</td>
	   <td><select id="_EventId" name="D:EventId" style='width: 120px;' tabindex="110">
	   		<option value=""></option>
{-foreach name=eln key=key item=item from=$evel-}
		  	<option value="{-$key-}">{-$item[0]-}</option>
{-/foreach-}
		  </select>
	   </td>
	  </tr>
	  <tr valign="top">
	   <td>{-#tstatus#-}</td>
	   <td>
			<select name="D:RecordStatus[]" tabindex="{-$tabind-}" multiple style="height:40px;">
				<option value="PUBLISHED">{-#tstatpublished#-}</option>
				<option value="READY">{-#tstatready#-}</option>
				<option value="DRAFT">{-#tstatdraft#-}</option>
				<option value="TRASH">{-#tstatrash#-}</option>
			</select>
	   </td>
	  </tr>
   </table>
   <input type="hidden" name="_D+cmd" value="result">
   <input type="hidden" name="_D+SQL_LIMIT" value="100">
  </form>
	</div>
</div>
<div id="res-win" class="x-hidden">
	<div class="x-window-header">{-#tqueryresults#-}</div>
	<div id="res-cfg" align="center">
		<iframe name="iframe" id="iframe" frameborder="0" style="height:330px; width:910px;"></iframe>
	</div>
</div>
<!-- END QUERY OPTIONS -->
 </body>
</html>
{-/if-}
{-if $ctl_result-}
<?xml version="1.0" encoding="UTF-8" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8; no-cache" />
	<style type="text/css">
		.msg1 {
			font-family:arial,tahoma,helvetica,cursive; font-size:11px; color:#dbab28; }
		.msg2 {
			font-family:arial,tahoma,helvetica,cursive; font-size:10px; color:#000000; }
	</style>
</head>
<body>
<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td class="msg1">
		 {-if $statusmsg == 'duplicate'-}<b>{-#tdcerror#-}:</b> {-#tdisererr#-}
		 {-elseif $statusmsg == 'insertok'-} {-#tdccreated#-} (Serial={-$diserial-})
		 {-elseif $statusmsg == 'updateok'-} {-#tdcupdated#-} (Serial={-$diserial-})
		 {-else-}{-$statusmsg-}{-/if-}
		</td>
		<td class="msg2">{-#tstatpublished#-} {-$dipub-}, {-#tstatready#-} {-$direa-}</td>
	</tr>
</table>
</body>
</html>
{-/if-}
{-if $ctl_updater-}
<span style="width:10px; height:10px; position:absolute; left:0; top:0; background-color:{-$stat-};"></span>
{-/if-}
