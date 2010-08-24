	var mod = "";
	var opt = "";
	var reg = "";
	var waiting = "<img src='loading.gif'>";

	function uploadMsg(msg) {
		if (mod != "") {
			var mydiv = $(mod + 'statusmsg');
			mydiv.innerHTML = msg;
		}
	}
	
	function updateList(div, url, pars, callback) {
		jQuery('#' + div).load(url, pars, function(response, status, xhr) {
			onReadyDatabaseAdmin();
		});
	}

	function updateUserBar(url, cmd, user, pass) {
		jQuery.post(url,
			{cmd      : cmd,
			 userid   : user,
			 password : pass
			},
			function(data) {
				jQuery('#pagecontent').html(data);
				if (cmd == "login") {
					updateList('pagecontent', url, 'cmd=welcome');
				} else if (cmd == "logout") {
					window.location.reload(false);
				}				
			}
		);
	} //function

	function sendData (r, url, pars, val) {
		reg = r;
		opt = val;
		if (mod != "") {
			$(mod + 'addsect').style.display = 'none';
		}
		pars = pars + '&t=' + new Date().getTime();
		var myAjax = new Ajax.Request( url, {
			method: 'get', parameters: pars,
			onLoading: function(request) {
				window.style.cursor = "wait";
				uploadMsg(waiting);
			},
			onComplete: showResponse
		} );
	} //function

	function showResponse (originalRequest) {
		var newData = originalRequest.responseText;
		uploadMsg(newData);
		switch(mod) {
			case "regionpa":
				updateList('lst_regionpa', 'region.php', 'cmd=list');
			break;
			case "userpa":
				updateList('lst_userpa', 'user.php', 'cmd=list');
			break;
			case "event":
				if (opt == "1")
					updateList('lst_evepred', 'events.php', 'r='+ reg +'&cmd=list&predef=1');
				else
					updateList('lst_eveuser', 'events.php', 'r='+ reg +'&cmd=list&predef=0');
				updateList('qevelst', 'index.php', 'r='+ reg +'&cmd=evelst');
			break;
			case "cause":
				if (opt == "1")
					updateList('lst_caupred', 'causes.php', 'r='+ reg +'&cmd=list&predef=1');
				else
					updateList('lst_cauuser', 'causes.php', 'r='+ reg +'&cmd=list&predef=0');
				updateList('qcaulst', 'index.php', 'r='+ reg +'&cmd=caulst');
			break;
			case "role":
				updateList('lst_role', 'info.php', 'r='+ reg +'&rolecmd=list');
			break;
			case "log":
				updateList('lst_log', 'info.php', 'r='+ reg +'&logcmd=list');
			break;
			case "lev":
				updateList('lst_lev', 'geolevel.php', 'r='+ reg +'&levcmd=list');
			break;
			case "geo":
				updateList('lst_ageo', 'geography.php', 'r='+ reg +'&geocmd=list&GeographyId=');
				updateList('qgeolst', 'index.php', 'r='+ reg +'&cmd=geolst');
			break;
			case "extraeff":
				updateList('lst_eef', 'extraeffects.php', 'r='+ reg +'&cmd=list');
			break;
			default:
			break;
		}
	}

	function setEveCau(id, name, desc, active, is_pred, module) {
		mod = module;
		$(mod + 'addsect').style.display = 'block';
		uploadMsg('');
		if (module == "event") {
			$('aEventName').value = name;
			$('aEventDesc').value = desc;
			if (active == "1")
				$('aEventActive').checked = true;
			else
				$('aEventActive').checked = false;
			if (is_pred == "1")
				$('aEventDesc').disabled = true;
			else
				$('aEventDesc').disabled = false;
			$('aEventPreDefined').value = is_pred;
			$('aEventId').value = id;
		}
		else if (module == "cause") {
			$('aCauseName').value = name;
			$('aCauseDesc').value = desc;
			if (active == "1")
				$('aCauseActive').checked = true;
			else
				$('aCauseActive').checked = false;
			if (is_pred == "1")
				$('aCauseDesc').disabled = true;
			else
				$('aCauseDesc').disabled = false;
			$('aCausePreDefined').value = is_pred;
			$('aCauseId').value = id;
		}
	}

	function setRolLog (key, val, module) {
		mod = module;
		$(mod + 'addsect').style.display = 'block';
		if (module == "role") {
			$('UserId').value = key;
			$('AuthAuxValue').value = val;
		}
		else if (module == "log") {
			$('DBLogType').value = key;
			$('DBLogNotes').value = val;
		}
	}
	
	function setLevGeo (key, val, val2, val3, ly1, ly2, ly3, module) {
		mod = module;
		$(mod + 'addsect').style.display = 'block';
		if (module == "lev") {
			$('GeoLevelId').value = key;
			$('GeoLevelName').value = val;
			$('GeoLevelDesc').value = val2;
			/*
			if ($('GeoLevelLayerFile').length == 0) {
				$('chkmap').checked = false;
				$('shwmap').style.display = 'none';
			}
			else {
				$('chkmap').checked = true;
				$('shwmap').style.display = 'block';
			}*/
			//$('GeoLevelLayerFile').value = ly1;
			$('GeoLevelLayerCode').value = ly2;
			$('GeoLevelLayerName').value = ly3;
		}
		else if (module == "geo") {
			$('aGeographyId').value = key;
			$('aGeographyCode').value = val;
			$('aGeographyName').value = val2;
			if (val3 == "1")
				$('aGeographyActive').checked = true;
			else
				$('aGeographyActive').checked = false;
		}
	}

	function setadmingeo(reg, k, l) {
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
			updateList('alev' + l, 'geography.php', 'r='+ reg +'&geocmd=list&GeographyId=' + v[0]);
		}
	}
	
	function setUserPA (login, name, email, pass, cnt, city, active) {
		mod = "userpa";
		$(mod + 'addsect').style.display = 'block';
		$('UserId').value = login;
		$('UserFullName').value = name;
		$('UserEMail').value = email;
		$('UserPasswd').value = pass;
		$('UserCountry').value = cnt;
		$('UserCity').value = city;
		if (active == "1")
			$('UserActive').checked = true;
		else
			$('UserActive').checked = false;
	}

	function setExtraEff (id, label, def, type, size, active, public) {
		mod = "extraeff";
		$(mod + 'addsect').style.display = 'block';
		$('EEFieldId').value = id;
		$('EEFieldLabel').value = label;
		$('EEFieldDesc').value = def;
		$('EEFieldType').value = type;
		$('EEFieldSize').value = size;
		if (active == "1")
			$('EEFieldActive').checked = true;
		else
			$('EEFieldActive').checked = false;
		if (public == "1")
			$('EEFieldPublic').checked = true;
		else
			$('EEFieldPublic').checked = false;
	}

	/*** MANAGE MODULES WINDOWS  ***/
/*	var diwin = null;
	var docw = null;*/
	var winopt = 'width=1020,height=700,left=0,top=0,screenX=0,screenY=0,resizable=no,scrollbars=no,status=no,toolbar=no';
/*
	function runWin(url, name) {
		var w	= 1020;
		var h	= 700;
		if (name == "doc")
			w = 800;
		if (win != null && !win.closed && win.location)
			win.opener = self;
		else {
			if (CheckIsIE())
				win = window.open(url, name);
			else
				win = window.open(url, name, options);
		}
		if (window.focus) win.focus();
		return false;
	}

	function endWin(name) {
		if (name == 'desinventar' && !diwin.closed && diwin.location)
			diwin.close();
		else if (name == 'doc' && !docw.closed && docw.location)
			docw.close();
		return false;
	}

	function chkWin(name) {
		if (name == 'desinventar' && !diwin.closed && diwin.location)
			return true;
		else if (name == 'doc' && !docw.closed && docw.location)
			return true;
		return false;
	}
*/
	/******* MANAGE FORMS ********/
	function setfocus(a_field_id) {
		$(a_field_id).focus();
	}

	function checkForm(prmForm, prmFieldList, errmsg) {
		var bReturn = true;
		jQuery.each(prmFieldList, function(index, value) {
			var selector = '#' + prmForm + ' #' + value;
			if (jQuery(selector).val().length < 1) {
				jQuery(selector).highlight();
				bReturn = false;
			} //if
		}); //.each
		return bReturn;
	} //function

	function getForm(fobj) {
		var str = '';
		var ft = ''; 
		var fv = ''; 
		var fn = ''; 
		var els = ''; 
		for(var i = 0;i < fobj.elements.length;i++) {
			els = fobj.elements[i];
			ft = els.title;
			fv = els.value;
			fn = els.name;
			switch(els.type) {
				case "text": case "hidden": case "password": case "textarea":
					// is it a required field?
					if (encodeURI(ft) == "required" && encodeURI(fv).length < 1) {
						els.focus();
						return false;
					}
					str += fn + "=" + encodeURI(fv) + "&";
				break;   
				case "checkbox":
				case "radio":
					if(els.checked) str += fn + "=" + encodeURI(fv) + "&";
				break;
				case "select-one":
					str += fn + "=" +
					els.options[els.selectedIndex].value + "&";
				break;
			} // switch
		} // for
		str = str.substr(0,(str.length - 1));
		return str;  
	}

	// Block characters according to type
	function blockChars(e, value, type) {
		var key = window.event ? e.keyCode : e.which;

		// 2010-08-19 (jhcaiced) Accept values in numeric keypad
		if (key >= 96 && key <= 105) {
			key = key - 48;
		}
		var keychar = String.fromCharCode(key);
		var opt = type.split(":"); // 0=type; 1=minlength; 2=minval-maxval
			// Accept keys: backspace, tab, shift, ctrl, insert, delete
			//        pagedown, pageup, rows
		var spckey = (key==8 || key==9 || key==17 || key==20 ||
		              key==45 || key==46 || (key>=33 && key<=40) || key==0);
		var chk = true;
		var val = true; // validate characters
			// Check max length
		if (value.length >= parseInt(opt[1]))
			var len = false;
		else
			var len = true;
			// Check datatype
		switch (opt[0]) {
			case "date" :
				reg = /^\d{4}-\d{0,2}-\d{0,2}$/;
				chk = reg.test(keychar);
			break;
			case "alphanumber" :
				reg = /^[a-z]|[A-Z]|[0-9]|[-_+.]+/; 
				chk = reg.test(keychar);
				break;
			case "integer" :
				reg = /\d/;
				chk = reg.test(keychar);
				break;
			case "double" :
				reg = /^[-+]?[0-9]|[.]+$/;
				chk = reg.test(keychar);
				break;
			default:;
		}
		// Block special characters: (like !@#$%^&'*" etc)
		val = !(key == 34 || key == 39 || key == 92 || key == 13 || key == 16)
		//alert ("Key: "+ key +" - "+ chk +" "+ len +" "+ spckey +" ");
	  return (val && ((chk && len) || spckey));
	}

	function onlyText(e) {
		var keynum;
		var keychar;
		var numcheck;
		if(window.event) { // IE
			keynum = e.keyCode;
		}
		else if(e.which) { // Netscape/Firefox/Opera
			keynum = e.which;
		}
		keychar = String.fromCharCode(keynum);
		numcheck = /\d/;
		return !numcheck.test(keychar);
	}

	function onlyNumber(e) {
		var keynum;
		var keychar;
		if(window.event) { // IE
			keynum = e.keyCode;
		}
		else if(e.which) { // Netscape/Firefox/Opera
			keynum = e.which;
		}
		if (e.keyCode < 48 || e.keyCode > 57)
			return false;
		return true;
	}

	
	function getGeoItems(reg, geoid, l, lev, src) {
		if (src == "DATA") {
			div = window.parent.frames['dif'].document.getElementById('lev'+ l);
			ele = window.parent.frames['dif'].document.getElementById('geolev'+ l);
		}
		else {
			div = $('lev'+ l);
			ele = $('geolev'+ l);
		}
		geo = geoid.substr(0, (l+1) * 5);
		for (var w=0; w < ele.length; w++) {
			if (ele.options[w].value == geo)
				ele.selectedIndex = w ;
		}
		if (l < lev) {
			var lsAjax = new Ajax.Updater( div, 'cards.php', {
				method: 'get', parameters: 'r='+ reg +'&cmd=list&GeographyId='+ geo + '&t=' + new Date().getTime(),
				onComplete: function(request)	{
					getGeoItems(reg, geoid, l+1, lev, src);
				}
			} );
		}
	}

	function showinfo(mydiv) {
		if ($(mydiv).style.display == 'none')
			$(mydiv).style.display = 'block';
		else
			$(mydiv).style.display = 'none';
	}

	function CheckIsIE() {
		if (navigator.appName.toUpperCase() == 'MICROSOFT INTERNET EXPLORER')
			return true;
		else
			return false;
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
		if (cmd == 'result') {
			jQuery('#prmCommand').val('cmdGridShow');
		} else {
			jQuery('#prmCommand').val('cmdGridSave');
		}
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
			jQuery('#DC').submit();
			//hideMap();
			return true;
		} else {
			return false;
		}
	}
	
	function sendMap(cmd) {
		jQuery('#prmCommand').val('cmdMapShow');
		if ($('_M+Type').length > 0) {
			w = Ext.getCmp('westm');
			//$('frmwait').innerHTML = waiting;
			$('_M+cmd').value = cmd;
			if (cmd == "export") {
				jQuery('#prmCommand').val('cmdMapSave');
				// to export image save layers and extend..
				var dcr = document.getElementById('dcr');
				var mm = map;
				//var mm = dcr.map;
				var extent = mm.getExtent();
				//extent.transform(mm.prj1, mm.prj2);
				var layers = mm.layers;
				var activelayers = [];
				for (i in layers) {
					if (layers[i].visibility && layers[i].calculateInRange() && !layers[i].isBaseLayer) {
						activelayers[activelayers.length] = layers[i].params['LAYERS'];
					}
				}
				$('_M+extent').value = [extent.left,extent.bottom,extent.right,extent.top].join(',');
				$('_M+layers').value = activelayers;
				//myMap = window.parent.frames['dcr'].document.getElementById('MapTitle');
				myMap = jQuery('#MapTitle');
				$('_M+title').value = myMap.val();
			}
			combineForms('DC', 'CM');
			w.collapse(); // hide()
			$('DC').action='thematicmap.php';
			jQuery('#DC').submit();
			//hideMap();
			return true;
		} else {
			return false;
		}
	} //function
	
	function sendGraphic(cmd) {
		if (cmd == 'result') {
			jQuery('#prmCommand').val('cmdGraphShow');
		} else {
			jQuery('#prmCommand').val('cmdGraphSave');
		}
		w = Ext.getCmp('westm');
		jQuery('#prmGraphCommand').val(cmd);
		combineForms('DC', 'CG');
		w.collapse(); //hide()
		$('DC').action='graphic.php';
		jQuery('#DC').submit();
		//hideMap();
	}
	
	function sendStatistic(cmd) {
		if (cmd == 'result') {
			jQuery('#prmCommand').val('cmdStatShow');
		} else {
			jQuery('#prmCommand').val('cmdStatSave');
		}
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
			jQuery('#DC').submit();
			//hideMap();
			return true;
		} else {
			return false;
		}
	} //function
	
	function saveQuery() {
		jQuery('#prmCommand').val('cmdQuerySave');
		selectall('_D+Field[]');
		combineForms('DC', 'CD');
		combineForms('DC', 'CM');
		combineForms('DC', 'CG');
		selectall('_S+Field[]');
		combineForms('DC', 'CS');
		jQuery('#_CMD').val('savequery');
		$('DC').action='index.php?r=' + jQuery('#desinventarRegionId').val();
		jQuery('#DC').submit();
		return true;
	}

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
			updateList('itree-' + gid, 'index.php', 'r=' + jQuery('#desinventarRegionId').val() + '&cmd=glist&GeographyId=' + gid);
		} else {
			// clean childs first
			$('itree-' + gid).innerHTML = '';
			$('itree-' + gid).style.display = 'none';
		}
	}
	
