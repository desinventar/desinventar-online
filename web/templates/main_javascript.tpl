<script type="text/javascript">
		function onMenuItem(item) {
			switch (item.id) {
				case "mnuRegionInfo":
					var RegionId = '{-$reg-}';
					if (RegionId != '') {
						$('dcr').src = "index.php?cmd=getRegionFullInfo&r={-$reg-}";
						$('bsave').style.visibility = 'hidden';
						$('bprint').style.visibility = 'hidden';
					}
				break;
				case "mnuUserLogin":
					//updateUserBar('user.php', '', '', '');
					usrw.show();
				break;
				case "mnuUserLogout":
					doUserLogout();
				break;
				case "mnuUserEditAccount":
					jQuery("#dbl").load('user.php?cmd=changepasswd',function() { onReadyUserChangePasswd('dbl-win'); });
					dblw.show();
				break;
				{-foreach name=lglst key=key item=item from=$lglst-}
					case "{-$key-}":
						window.location = "index.php?r={-$reg-}&lang={-$key-}";
					break;
				{-/foreach-}
				case "mfilprn":
					window.print();
				break;
				case "mfilqit":
					self.close();
				break;
				// query menu
				case "mqrygoq":
					w = Ext.getCmp('westm');
					jQuery('.contentBlock').hide();
					{-if $ctl_noregion-}
						jQuery('#divQueryResults').hide();
						w.hide();
					{-else-}
						jQuery('#divQueryResults').show();
						w.show();
					{-/if-}
					if (w.isVisible()) {
						w.collapse(); //hide()
					} else {
						w.expand(); //show()
					}
				break;
				case "mqrynew":
					// Just reload the current region window...(need a better solution!!)
					window.location = "index.php?r={-$reg-}";
				break;
				case "mqrysav":
					saveQuery();
				break;
				case "mnuQueryOpen":
					var qryw;
					if (!qryw) {
						qryw = new Ext.Window({
							el:'qry-win',  layout:'fit',  width:300, height:200, 
							closeAction:'hide', plain: true, animCollapse: false,
							items: new Ext.Panel({
							contentEl: 'qry-cfg', autoScroll: true }),
							buttons: [{
								text:'{-#tclose#-}',
								handler: function() {
									qryw.hide();
								}
							}]
						});
					}
					qryw.show(this);
				break;
				// Datacards Menu Items
				case "mcrdins":
					//hideQueryDesign();
					//jQuery('.contentBlock').hide();
					//jQuery('#divDatacardsShow').show();
					difw.show();
				break;
				case "mnuDatacardImport":
					hideQueryDesign();
					jQuery('.contentBlock').hide();
					jQuery('#divDatacardsImport').show();
					updateList('divDatacardsImport', 'import.php', 'r={-$reg-}');
				break;
				case "mnuDatabaseBackup":
					/*
					hideQueryDesign();
					jQuery('.contentBlock').hide();
					jQuery('#divDatabaseBackup').show();
					*/
					window.location = "index.php?cmd=getRegionBackup&r={-$reg-}";
				break;
				case "mcrdcfg":
					hideQueryDesign();
					jQuery('.contentBlock').hide();
					jQuery('#divDatabaseConfiguration').show();
					jQuery('#tabDatabaseConfiguration').show();
				break;
				// databases menu
				case "mdbsfnd":
					updateList('dbl', 'index.php', 'cmd=listdb');
					dblw.show();
				break;
				case "mnuUserAdmin":
					//updateList('dbl', 'user.php', 'cmd=adminusr', 'onReadyUserAdmin');
					jQuery("#dbl").load('user.php?cmd=adminusr',function() { onReadyUserAdmin(); });
					dblw.show();
				break;
				case "mdbsadm":
					updateList('dbl', 'region.php', 'cmd=adminreg');
					dblw.show();
				break;
				// help menu
				case "mabo":
					dlgw.show();
				break;
				case "mwww":
					window.open('http://www.desinventar.org', '', '');
				break;
				case "mmtg":
					window.open('http://www.desinventar.org/{-if $lg == "spa"-}es/metodologia{-else-}en/methodology{-/if-}/', '', '');
					//runWin('doc.php?m=metguide', 'doc');
				break;
				case "mdoc":
					window.open('http://www.desinventar.org/{-if $lg == "spa"-}es{-else-}en{-/if-}/software', '', '');
				break;
			} //switch
		} //function
		
		function hideQueryDesign() {
			// Hide Query Design Panel
			w = Ext.getCmp('westm');
			w.hide();
			w.collapse();
		}

	var	w;
	var	s;
	var difw;
	var usrw;
	var dblw;
	var dlgw;
	// DI8 - Layout, buttons and internal windows - UI DesConsultar module
	Ext.onReady(function() {
		// Initialize User Login Form
		onReadyUserLogin();
		
		jQuery('body').bind('UserLoggedIn',function() {
			// When the user completes the login procedure, reload the current page...
			 window.location.reload(false);
		});

		jQuery('body').bind('UserLoggedOut',function() {
			// When the user logouts, reload the current page...
			 window.location.reload(false);
		});
		
		setTimeout(function() {
			Ext.get('loading').remove();
			Ext.get('loading-mask').fadeOut({remove:true});
		}, 250);
		Ext.QuickTips.init();
		// User functions Window
		if (!usrw) {
			usrw = new Ext.Window({
				el:'usr-win', layout:'fit', x:300, y:100, width:500, height:300, 
				closeAction:'hide', plain: true, animCollapse: false,
				items: new Ext.Panel({ contentEl: 'usr', autoScroll: true })
			});
		}
		
		// Search databases window
		if (!dblw) {
			dblw = new Ext.Window({
				el:'dbl-win', layout:'fit', x:200, y:100, width:600, height:450, 
				closeAction:'hide', plain: true, animCollapse: false,
				items: new Ext.Panel({ contentEl: 'dbl', autoScroll: true })
			});
		}
		
		// Dialog window
		if (!dlgw) {
			dlgw = new Ext.Window({
				el:'dlg-win', layout:'fit', x:350, y:200, width:300, height:150, 
				closeAction:'hide', plain: true, animCollapse: false,
				items: new Ext.Panel({ contentEl: 'dlg', autoScroll: true })
			});
		}
		
		// DesInventar (input form) Window
		if (!difw) {
			difw = new Ext.Window({
				el:'dif-win', layout:'fit', 
				x: 65, y: 0, width:960, height:638, 
				closeAction:'hide', plain: true, animCollapse: false,
				items: new Ext.Panel({ contentEl: 'dif-cfg', autoScroll: true })
			});
			difw.on('hide',function() {
				showtip('');					
			});
		}
		
		// Main menu
		var muser = new Ext.menu.Menu({
			id: 'userMenu',
			items: [
				{-if $userid != ""-}
					{id: 'mnuUserEditAccount', text: '{-#tconfigacc#-}', handler: onMenuItem },
					{id: 'mnuUserLogout', text: '{-#tclosesess#-}', handler: onMenuItem }, 
				{-else-}
					{id: 'mnuUserLogin', text: '{-#benter#-}', handler: onMenuItem }, 
				{-/if-}
				'-',
				{ text: '{-#mlang#-}',
					menu: {
						id: 'langSubMenu',
						items: [
							{-foreach name=lglst key=key item=item from=$lglst-}
								{id: '{-$key-}', text: '{-$item[0]-}', handler: onMenuItem},
							{-/foreach-}
							'-'
						]
					}
				},
				{id: 'mfilqit',  text: '{-#mquit#-}', handler: onMenuItem  }
			]
		});
		
		var mquery = new Ext.menu.Menu({
			id: 'queryMenu',
			items: [
				{-if !$ctl_noregion-}
					{id:'mqrygoq', text: '{-#mgotoqd#-}',	handler: onMenuItem  },
					{id:'mqrynew', text: '{-#mnewsearch#-}',handler: onMenuItem  },
					{id:'mqrysav', text: '{-#msavequery#-}',handler: onMenuItem  },
				{-/if-}
				{id:'mnuQueryOpen', text: '{-#mopenquery#-}',handler: onMenuItem  }
			]
		});
		
		var mcards = new Ext.menu.Menu({
			id: 'cardsMenu',
			items: [
				{id:'mcrdins', text: '{-#minsert#-}',	handler: onMenuItem  },
				{-if $role == "SUPERVISOR" || $role == "ADMINREGION"-}
					{id:'mnuDatacardImport', text: '{-#mimport#-}',	handler: onMenuItem  },
					{id:'mnuDatabaseBackup', text: '{-#mbackdb#-}',	handler: onMenuItem  },
				{-/if-}
				{-if $role == "OBSERVER" || $role == "ADMINREGION"-}
					{id:'mcrdcfg', text: '{-#mconfig#-}',	handler: onMenuItem  },
				{-/if-}
				'-'
			]
		});
		
		var mbases = new Ext.menu.Menu({
			id: 'basesMenu',
			items: [
				{id:'mdbsfnd', text: '{-#mdbfind#-}',	handler: onMenuItem  }, //search Databases
				{-if $userid == "root"-}
					{id:'mnuUserAdmin', text: '{-#tadminusrs#-}',	handler: onMenuItem  }, //admin Users
					{id:'mdbsadm', text: '{-#tadminregs#-}',	handler: onMenuItem  }, //admin Databases
				{-/if-}
				'-'
			]
		});
		
		var mhelp = new Ext.menu.Menu({
			id: 'helpMenu',
			style: { overflow: 'visible' },
			items: [
				{id:'mwww', text: '{-#mwebsite#-}',	handler: onMenuItem  },
				{id:'mmtg', text: '{-#hmoreinfo#-}', handler: onMenuItem  },
				{id:'mdoc', text: '{-#hotherdoc#-}', handler: onMenuItem  },
				{id:'mnuRegionInfo', text: '{-#hdbinfo#-}', handler: onMenuItem  },
				{id:'mabo', text: '{-#mabout#-}', handler: onMenuItem  }
			]
		});
		
		var tb = new Ext.Toolbar();
		tb.render('toolbar');
		tb.add('-', {id: 'musr', text: '{-#tuser#-}{-if $userid != ""-}: <b>{-$userid-}</b>{-/if-}', menu: muser });
		tb.add('-', {id: 'mqry', text: '{-#msearch#-}',		menu: mquery });
		{-if ($role == "USER" || $role == "SUPERVISOR" || $role == "OBSERVER" || $role == "ADMINREGION")-}
			tb.add('-', {id: 'minp', text: '{-#mdcsection#-}',	menu: mcards });
		{-/if-}
		tb.add('-', {id: 'mdbs', text: '{-#mdatabases#-}',	menu: mbases });
		tb.add('-', {id: 'mhlp', text: '{-#mhelp#-}',			menu: mhelp  });
		tb.add('->',{id: 'mnuRegionInfo', text: '[{-$regname-}]', 		handler: onMenuItem });
		tb.add('->',{id: 'mwww', text: '<img src="images/di_logo4.png">', handler: onMenuItem });

		// layout
		var viewport = new Ext.Viewport({
			layout:'border',
			items:[
				{ region:'north',
					height: 30,
					contentEl: 'north'
				},
				{ region: 'south',
					id: 'southm',
					split: false,
					title: '{-#tmguidedef#-}',
					height: 80,
					minSize: 100,
					maxSize: 200,
					margins: '0 0 0 0',
					contentEl: 'south',
					collapsible: true
				},
				new Ext.Panel({
					region: 'center',
					id: 'centerm',
					//title: '{-#tsubtitle2#-}',
					contentEl: 'container',
					autoScroll: true
				})
				{-if !$ctl_noregion-}
					,
					{ region: 'west',
						id: 'westm',
						split: false,
						width: 350,
						title: '{-#tsubtitle#-}',
						autoScroll: true,
						margins:'0 2 0 0',
						collapsible: true,
						contentEl: 'west'
					}
				{-/if-}
			]
		}); //viewport
		
		// ==> Results Configuration Windows
		// Data
		var datw;
		var datb = Ext.get('dat-btn');
		datb.on('click', function() {
			if (validateQueryDefinition()) {
				if (!datw) {
					datw = new Ext.Window({
						el:'dat-win',
						layout:'fit',
						width:600,
						height:400, 
						closeAction:'hide',
						plain: true,
						animCollapse: false,
						items: new Ext.Panel({contentEl: 'dat-cfg', autoScroll: true }),
						buttons: [
							{text:'{-#tclean#-}',
								handler: function() {
									$('CD').reset();
									} //handler
							},
							{text:'{-#tsend#-}',
								handler: function() {
									if (sendList("result")) {
										$('DCRes').value = "D";
										datw.hide();
										$('bsave').style.visibility = 'visible';
										$('bprint').style.visibility = 'visible';
									} else {
										alert("{-#derrmsgfrm#-}");
									}
								} //handler
							},
							{text:'{-#tclose#-}',
								handler: function() {
									datw.hide();
								} //handler
							}
						] //button
					});
				}
				datw.show(this);
			}
		}); // data window
		
		// Statistics
		var stdw;
		var stdb = Ext.get('std-btn');
		stdb.on('click', function() {
			if (validateQueryDefinition()) {
				if (!stdw) {
					stdw = new Ext.Window({
						el:'std-win',  layout:'fit',  width:600, height:400, 
						closeAction:'hide', plain: true, animCollapse: false,
						items: new Ext.Panel({contentEl: 'std-cfg', autoScroll: true }),
						buttons: [
							{text:'{-#tclean#-}',
								handler: function() {
									$('CS').reset();
								}
							},
							{text:'{-#tsend#-}',
								handler: function() {
									if (sendStatistic("result")) {
										$('DCRes').value = "S";
										stdw.hide();
										$('bsave').style.visibility = 'visible';
										$('bprint').style.visibility = 'visible';
									} else {
										alert("{-#serrmsgfrm#-}");
									}
								} //handler
							},
							{text:'{-#tclose#-}',
								handler: function() {
									stdw.hide();
								}
							}
						]
					});
				}
				stdw.show(this);
			}
		}); // statistics
		
		// Graphic
		var grpw;
		var grpb = Ext.get('grp-btn');
		grpb.on('click', function() {
			if (validateQueryDefinition()) {
				if (!grpw) {
					grpw = new Ext.Window({
						el:'grp-win',  layout:'fit',  width:750, height:420, 
						closeAction:'hide', plain: true, animCollapse: false,
						items: new Ext.Panel({contentEl: 'grp-cfg', autoScroll: true }),
						buttons: [
							{text:'{-#tclean#-}',
								handler: function() {
									$('CG').reset();
								}
							},
							{text:'{-#tsend#-}',
								handler: function() {
									sendGraphic("result");
									$('DCRes').value = "G";
									grpw.hide();
									$('bsave').style.visibility = 'visible';
									$('bprint').style.visibility = 'visible';
								}
							},
							{text:'{-#tclose#-}',
								handler: function() {
									grpw.hide();
								}
							}
						]
					});
				}
				grpw.show(this);
			}
		}); // Graphics
		
		// Map
		var mapw;
		var mapb = Ext.get('map-btn');
		mapb.on('click', function() {
			if (validateQueryDefinition()) {
				if (!mapw) {
					mapw = new Ext.Window({
						el:'map-win',  layout:'fit',  width:650, height:400, 
						closeAction:'hide', plain: true, animCollapse: false,
						items: new Ext.Panel({contentEl: 'map-cfg', autoScroll: true }),
						buttons: [
							{text:'{-#tclean#-}',
								handler: function() {
									$('CM').reset();
								}
							},
							{text:'{-#tsend#-}',
								handler: function() {
									setfocus('_M+limit[0]');
									if (sendMap("result")) {
										$('DCRes').value = "M";
										mapw.hide();
										$('bsave').style.visibility = 'visible';
										$('bprint').style.visibility = 'visible';
									} else {
										alert("{-#serrmsgfrm#-}");
									}
								}
							},
							{text:'{-#tclose#-}',
								handler: function() {
									mapw.hide();
								}
							}
						]
					});
				}
				mapw.show(this);
			}
		}); // Map
		
		// quicktips
		Ext.apply(
			Ext.QuickTips.getQuickTip(), {
				maxWidth: 200, minWidth: 100, showDelay: 50, trackMouse: true
			});
		});
		
		// end ExtJS object

		function disab(field) {
			field.disabled = true;
			field.className = "disabled";
		}
		
		function enab(field) {
			field.disabled = false;
			field.className = "";
		}
		
		function showtip(tip) {
			var d = parent.document.getElementById('_DIDesc');
			d.style.backgroundColor = '#ffffff';
			d.value = tip;
		}
		
		// Effects options
		function showeff(val, x, y) {
			if (val == ">=" || val == "<=" || val == "=" || val == "-3") {
				$(x).style.display = 'inline';
				if (val == "-3")
					$(y).style.display = 'inline';
				else
					$(y).style.display = 'none';
			}
			if (val == "" || val == "0" || val == "-1" || val == "-2") {
				$(x).style.display = 'none';
				$(y).style.display = 'none';
			}
		}
		
		function enadisEff(id, chk) {
			if (chk) {
				$('o'+ id).style.display = 'inline';
				enab($(id +'[0]'));
				enab($(id +'[1]'));
				enab($(id +'[2]'));
			} else {
				$('o'+ id).style.display = 'none';
				disab($(id +'[0]'));
				disab($(id +'[1]'));
				disab($(id +'[2]'));
			}
		}
		
		function disabAxis2() {
			jQuery('#graphParamField2').val('');
			disab($('graphParamField2'));
			disab($('_G+Scale2'));
			disab($('_G+Data2'));
			disab($('_G+Mode2'));
		}
		
		function enabAxis2() {
			enab($('graphParamField2'));
			enab($('_G+Scale2'));
			enab($('_G+Data2'));
			enab($('_G+Mode2'));
		}
		
		function grpSelectbyType(fld) {
			var grp = $(fld).value;
			if (grp == "D.EventId" || grp == "D.CauseId" || grp.substr(0,13) == "D.GeographyId") {
				// Comparatives
				disabAxis2();
				enab($('_G+K_pie'));
				$('_G+Kind').value = "PIE";
				$('graphParamPeriod').value = "";
				disab($('graphParamPeriod'));
				$('graphParamStat').value = "";
				disab($('graphParamStat'));
				disab($('_G+Scale'));
				disab($('_G+M_accu'));
				disab($('_G+M_over'));
				enab($('_G+D_perc'));
			} else { 
				// Histograms
				disab($('_G+K_pie'));
				$('_G+Kind').value = "BAR";
				enab($('graphParamPeriod'));
				$('graphParamPeriod').value = 'YEAR';
				enab($('graphParamStat'));
				enab($('_G+Scale'));
				var histt = $(fld).value;
				if (histt.substr(19, 1) == "|") {
					disabAxis2();
					disab($('_G+M_accu'));
					enab($('_G+M_over'));
				} else {
					enabAxis2();
					enab($('_G+M_accu'));
					disab($('_G+M_over'));
				}
				disab($('_G+D_perc'));
			}
			if (fld == "_G+TypeH") {
				$('_G+TypeC').value = "";
			}
			if (fld == "_G+TypeC") {
				$('_G+TypeH').value = "";
			}
			$('_G+Type').value = grp;
			// For other graphics different from Temporal Histogram, the second variable should be disabled
			if (grp != 'D.DisasterBeginTime') {
				jQuery('#graphParamField2').removeAttr('disabled');
				jQuery('#graphParamField2').val('');
				jQuery('#graphParamField2').attr('disabled',true);
			}
		} //function
		
		function grpSelectbyKind() {
			comp = $('_G+TypeC').value;
			if ($('_G+Kind').value == "BAR" || $('_G+Kind').value == "LINE" || ($('_G+Kind').value != "PIE" &&
			   (comp == "D.EventId" || comp == "D.CauseId" || comp.substr(0,13) == "D.GeographyId"))) {
				 enabAxis2();
				 enab($('_G+M_accu'));
				 disab($('_G+M_over'));
				 enab($('_G+Scale'));
			} else {
				disabAxis2();
				disab($('_G+M_accu'));
				disab($('_G+Scale'));
			}
		} //function
		
		// forms management
		function combineForms(dcf, ref) {
			var dc = $(dcf);
			var rf = $(ref).elements;
			var ih = null;
			for (i=0; i < rf.length; i++) {
				if (rf[i].disabled == false) {
					ih = document.createElement("input");
					ih.type   = "hidden";
					ih.value  = rf[i].value;
					ih.name   = rf[i].name;
					dc.appendChild(ih);
				}
			}
		}
		
		function setSelMap(code, gid, opc) {
			if (opc) {
				// Find and fill childs
				$('itree-' + gid).style.display = 'block';
				updateList('itree-' + gid, 'index.php', 'r={-$reg-}&cmd=glist&GeographyId=' + gid);
			} else {
				// clean childs first
				$('itree-' + gid).innerHTML = '';
				$('itree-' + gid).style.display = 'none';
			}
		}
		
		function saveRes(cmd, typ) {
			if($('DCRes').value != '') {
				switch ($('DCRes').value) {
					case 'D':
						$('_D+saveopt').value = typ;
						sendList(cmd);
					break;
					case 'M':
						// SaveMap to PNG Format
						sendMap(cmd);
					break;
					case 'G':
						sendGraphic(cmd);
					break;
					case 'S':
						$('_S+saveopt').value = typ;
						sendStatistic(cmd);
					break;
				} //switch
			}
		} //function
		
		function sendList(cmd) {
			if ($('_D+Field[]').length > 0) {
				w = Ext.getCmp('westm');
				$('_D+cmd').value = cmd;
				selectall('_D+Field[]');
				var ob = $('_D+Field[]');
				var mystr = "";
				for (i=0; i < ob.length; i++) {
					mystr += ob[i].value + ",";
				}
				mystr += "D.DisasterId";
				$('_D+FieldH').value = mystr;
				combineForms('DC', 'CD');
				w.collapse();
				$('DC').action='data.php';
				//$('DC').submit();
				jQuery('#DC').submit();
				//hideMap();
				return true;
			} else {
				return false;
			}
		}
		
		function sendMap(cmd) {
			if ($('_M+Type').length > 0) {
				w = Ext.getCmp('westm');
				//$('frmwait').innerHTML = waiting;
				$('_M+cmd').value = cmd;
				if (cmd == "export") {
					// to export image save layers and extend..
					var mm = dcr.map;
					var extent = mm.getExtent();
					//extent.transform(mm.prj1, mm.prj2);
					var layers = mm.layers;
					var activelayers = [];
					for (i in layers) {
						if (layers[i].getVisibility() && layers[i].calculateInRange() && !layers[i].isBaseLayer) {
							activelayers[activelayers.length] = layers[i].params['LAYERS'];
						}
					}
					$('_M+extent').value = [extent.left,extent.bottom,extent.right,extent.top].join(',');
					$('_M+layers').value = activelayers;
					myMap = window.parent.frames['dcr'].document.getElementById('MapTitle');
					$('_M+title').value = myMap.value;
				}
				combineForms('DC', 'CM');
				w.collapse(); // hide()
				$('DC').action='thematicmap.php';
				$('DC').submit();
				//hideMap();
				return true;
			} else {
				return false;
			}
		} //function
		
		function sendGraphic(cmd) {
			w = Ext.getCmp('westm');
			$('_G+cmd').value = cmd;
			combineForms('DC', 'CG');
			w.collapse(); //hide()
			$('DC').action='graphic.php';
			$('DC').submit();
			//hideMap();
		}
		
		function sendStatistic(cmd) {
			if ($('_S+Firstlev').value != "" && $('_S+Field[]').length > 0) {
				w = Ext.getCmp('westm');
				$('_S+cmd').value = cmd;
				selectall('_S+Field[]');
				var ob = $('_S+Field[]');
				var mystr = "D.DisasterId||";
				for (i=0; i < ob.length; i++) 
					mystr += "," + ob[i].value;
				$('_S+FieldH').value = mystr;
				combineForms('DC', 'CS');
				w.collapse();//hide()
				$('DC').action='statistic.php';
				$('DC').submit();
				//hideMap();
				return true;
			} else {
				return false;
			}
		} //function
		
		function saveQuery() {
			selectall('_D+Field[]');
			combineForms('DC', 'CD');
			combineForms('DC', 'CM');
			combineForms('DC', 'CG');
			selectall('_S+Field[]');
			combineForms('DC', 'CS');
			$('_CMD').value='savequery';
			$('DC').action='index.php';
			$('DC').submit();
			return true;
		}
		
		function addRowToTable() {
			var tbl = $('tbl_range');
			var lastRow = tbl.rows.length;
			// if there's no header row in the table, then iteration = lastRow + 1
			var iteration = lastRow;
			var row = tbl.insertRow(lastRow);
			var cellBeg = row.insertCell(0);
			var textNode = document.createTextNode(iteration + 1);
			cellBeg.appendChild(textNode);
			// left cell
			var cellLeft = row.insertCell(1);
			var lim = document.createElement("input");
			lim.setAttribute('type', 'text');
			lim.setAttribute('size', '5');
			lim.setAttribute('class', 'line');
			lim.setAttribute('name', '_M+limit['+ iteration +']');
			lim.setAttribute('id', '_M+limit['+ iteration +']');
			lim.setAttribute('onBlur', "miv=parseInt($('_M+limit["+ iteration -1+"]').value)+1; $('_M+legend["+ iteration +"]').value='{-#mbetween#-} '+ miv +' - '+ this.value;");
			cellLeft.appendChild(lim);
			// center cell
			var cellCenter = row.insertCell(2);
			var leg = document.createElement('input');
			leg.setAttribute('type', 'text');
			leg.setAttribute('size', '20');
			leg.setAttribute('class', 'line');
			leg.setAttribute('name', '_M+legend['+ iteration +']');
			leg.setAttribute('id', '_M+legend['+ iteration +']');
			cellCenter.appendChild(leg);
			// right cell
			var cellRight = row.insertCell(3);
			var ic = document.createElement('input');
			ic.setAttribute('type', 'text');
			ic.setAttribute('size', '3');
			ic.setAttribute('class', 'line');
			ic.setAttribute('id', '_M+ic['+ iteration +']');
			ic.setAttribute('style', 'background:#00ff00;');
			ic.setAttribute('onClick', "showColorGrid2('_M+color["+ iteration +"]','_M+ic["+ iteration +"]');");
			cellRight.appendChild(ic);
			var col = document.createElement('input');
			col.setAttribute('type', 'hidden');
			col.setAttribute('name', '_M+color['+ iteration +']');
			col.setAttribute('id', '_M+color['+ iteration +']');
			col.setAttribute('value', '00ff00;');
			cellRight.appendChild(col);
		}
		
		function removeRowFromTable() {
			var tbl = $('tbl_range');
			var lastRow = tbl.rows.length;
			if (lastRow > 2)
				tbl.deleteRow(lastRow - 1);
		}
		
		function setTotalize(lnow, lnext) {
			var sour = $(lnow);
			var dest = $(lnext);
			// clean dest list
			for (var i = dest.length - 1; i>=0; i--) {
				dest.remove(i);
			}
			for (var i=0; i < sour.length; i++) {
				if (!sour[i].selected) {
					var opt = document.createElement('option');
					opt.value = sour[i].value;
					opt.text = sour[i].text;
					var pto = dest.options[i];
					try {
						dest.add(opt, pto);
					} catch(ex) {
						dest.add(opt, i);
					}
				}
			} //for
		} //function
		
		function dechex(dec) {
			var Char_hexadecimales = "0123456789ABCDEF";
			var low = dec % 16;
			var high = (dec - low)/16;
			hex = "" + Char_hexadecimales.charAt(high) + Char_hexadecimales.charAt(low);
			return hex;
		}
		
		function hexdec(hex) {
			return parseInt(hex,16);
		}
		
		function genColors() {
			var tbl = $('tbl_range');
			var cnt = tbl.rows.length - 1;
			var a = $('_M+color[0]').value;
			var z = $('_M+color['+ cnt +']').value;
			var a1 = hexdec(a.substring(1,3));	var z1 = hexdec(z.substring(1,3));
			var a2 = hexdec(a.substring(3,5));	var z2 = hexdec(z.substring(3,5));
			var a3 = hexdec(a.substring(5,7));	var z3 = hexdec(z.substring(5,7));
			var m1 = ((z1 - a1) / cnt);
			var m2 = ((z2 - a2) / cnt);
			var m3 = ((z3 - a3) / cnt);
			for (i=1; i <= cnt; i++) {
				h1 = dechex(a1 + (m1 * i));
				h2 = dechex(a2 + (m2 * i));
				h3 = dechex(a3 + (m3 * i));
				val = "#" + h1 + h2 + h3;
				$('_M+color['+ i + ']').value = val;
				$('_M+ic['+ i + ']').style.backgroundColor = val;
			} //for
		}
		
		function setAdvQuery(value, ope) {
			$('CusQry').value += value + ' ';
			switch (ope) {
				case 'text':
					disab($('<'));
					disab($('>'));
					enab($('='));  $('=').value = "= ''";
					enab($('<>')); $('<>').value = "<> ''";
					enab($("LIKE '%%'"));
					disab($('=-1')); disab($('=0')); disab($('=-2'));
				break;
				case 'date':
					enab($('<')); $('<').value = "< ''";
					enab($('>')); $('>').value = "> ''";
					enab($('=')); $('=').value = "= ''";
					enab($('<>')); $('<>').value = "<> ''";
					enab($("LIKE '%%'"));
					disab($('=-1')); disab($('=0')); disab($('=-2'));
				break;
				case 'number':
					enab($('<')); $('<').value = "< ";
					enab($('>')); $('>').value = "> ";
					enab($('=')); $('=').value = "= ";
					enab($('<>'));$('<>').value = "<> ";
					disab($("LIKE '%%'"));
					enab($('=-1')); enab($('=0')); enab($('=-2'));
				break;
				case 'boolean':
					disab($('<'));
					disab($('>'));
					disab($('='));
					disab($('<>'));
					disab($("LIKE '%%'"));
					enab($('=-1')); enab($('=0')); enab($('=-2'));
				break;
			} //switch
		} //function
		
		function printRes() {
			if (CheckIsIE() == true) {
				document.dcr.focus();
				document.dcr.print();
			} else {
				window.frames['dcr'].focus();
				window.frames['dcr'].print();
			}
		}
		
		// Find all Effects fields enable by saved query
		window.onload = function() {
			// select optimal height in results frame
			//varhgt = screen.height * 360 / 600;
			//$('dcr').style = "height:"+ hgt + "px;"
			{-foreach name=ef1 key=k item=i from=$ef1-}
				{-assign var="ff" value=D_$k-}
				{-if $qd.$ff[0] != ''-}
					enadisEff('{-$k-}', true);
					showeff('{-$qd.$ff[0]-}', 'x{-$k-}', 'y{-$k-}');
				{-/if-}
			{-/foreach-}
			{-foreach name=sec key=k item=i from=$sec-}
				{-assign var="sc" value=D_$k-}
				{-if $qd.$sc[0] != ''-}
					{-foreach name=sc2 key=k2 item=i2 from=$i[3]-}
						{-assign var="ff" value=D_$k2-}
						{-if $qd.$ff[0] != ''-}
							enadisEff('{-$k2-}', true);
							showeff('{-$qd.$ff[0]-}', 'x{-$k2-}', 'y{-$k2-}');
						{-/if-}
					{-/foreach-}
					enadisEff('{-$k-}', true);
				{-/if-}
			{-/foreach-}
			{-foreach name=ef3 key=k item=i from=$ef3-}
				{-assign var="ff" value=D_$k-}
				{-if $qd.$ff[0] != ''-}
					enadisEff('{-$k-}', true);
					showeff('{-$qd.$ff[0]-}', 'x{-$k-}', 'y{-$k-}');
				{-/if-}
			{-/foreach-}
			{-foreach name=geol key=k item=i from=$geol-}
				{-if $i[3]-}
					setSelMap('{-$i[0]-}', '{-$k-}', true);
				{-/if-}
			{-/foreach-}
		} //function
		
		document.write('<style type="text/css">.tabber{display:none;}<\/style>');
		
		var tabberOptions = {
			'onClick': function(argsObj) {
				var t = argsObj.tabber;
				var i = argsObj.index;
				var div = this.tabs[i].div; /* The tab content div */
				/* Display a loading message */
				div.innerHTML = waiting;
				switch (i) {
					case 0 :
						myAjax = new Ajax.Updater(div, 'info.php', {method:'get', parameters:'r={-$reg-}'});
					break;
					case 1 :
						myAjax = new Ajax.Updater(div, 'geolevel.php', {method:'get', parameters:'r={-$reg-}'});
					break;
					case 2 :
						myAjax = new Ajax.Updater(div, 'geography.php', {method:'get', parameters:'r={-$reg-}'});
					break;
					case 3 :
						myAjax = new Ajax.Updater(div, 'events.php', {method:'get', parameters:'r={-$reg-}'});
					break;
					case 4 :
						myAjax = new Ajax.Updater(div, 'causes.php', {method:'get', parameters:'r={-$reg-}'});
					break;
					case 5 :
						myAjax = new Ajax.Updater(div, 'extraeffects.php', {method:'get', parameters:'r={-$reg-}'});
					break;
				} //switch
			},
			'onLoad': function(argsObj) {
				/* Load the first tab */
				argsObj.index = 0;
				this.onClick(argsObj);
			}
		}
		
		/* selection map functions
		function showMap() {
			$('smap').style.visibility = 'visible';
		}
		function hideMap() {
			$('smap').style.visibility = 'hidden';
		}*/
		
		var geotree = new CheckTree('geotree');
		//var g{-$reg-} = new CheckTree('g{-$reg-}');
</script>
