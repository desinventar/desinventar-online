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
	
	function updateList(div, url, pars) {
		var lsAjax = new Ajax.Updater( div, url, {
			method: 'get', parameters: pars,
			onLoading: function(request) {
				//$(div).style.cursor = "wait";
				if (mod == "")	$(div).innerHTML = waiting;
				//else  uploadMsg(waiting);
			},
			onComplete: function(request)	{
				//$(div).style.cursor = "";
				if (mod == "")  $('div').innerHTML = "";
				//else  uploadMsg('');
			}
		} );
	}

	function updateUserBar(url, cmd, user, pass) {
		var pars = 'cmd=' + cmd + '&userid=' + user + '&password=' + pass;
		var upd = true;
		var rbAjax = new Ajax.Updater('rightcontent', url, {
			method: 'get', parameters: pars,
			onComplete: function(request) {
				if (cmd == "login")
					updateList('pagecontent', url, 'cmd=welcome');
				else if (cmd == "logout")
					window.location.reload(false);
			},
			onFailure: 	function(request) {
				$('rightcontent').innerHTML = "{-#hlostconn#-}";
			}
		});
	}
	function sendData (r, url, pars, val) {
		reg = r;
		opt = val;
		//alert(pars);
		if (mod != "")
			$(mod + 'addsect').style.display = 'none';
		var myAjax = new Ajax.Request( url, {
			method: 'get', parameters: 'r='+ r +'&'+ pars,
			onLoading: function(request) {
				window.style.cursor = "wait";
				uploadMsg(waiting);
			},
			onComplete: showResponse
		} );
	}
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
				/* alert(newData);
				if (newData == "REGISTERED") {
					alert('Se ha enviado un correo con la clave');
					window.location = "";
				}*/
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
			if (is_pred == "1") {
				$('aEventDesc').disabled = true;
				$('aEventDesc2').value = desc;
			}
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
			if (is_pred == "1") {
				$('aCauseDesc').disabled = true;
				$('aCauseDesc2').value = desc;
			}
			else
				$('aCauseDesc').disabled = false;
			$('aCausePreDefined').value = is_pred;
			$('aCauseId').value = id;
		}
	}

	function setRegionPA (id, cnt, name, lang, admin, active, pub) {
		mod = "regionpa";
		$(mod + 'addsect').style.display = 'block';
		$('RegionId').value = id;
		$('CountryIso').value = cnt;
		$('RegionLabel').value = name;
		$('LangIsoCode').value = lang;
		$('RegionUserAdmin').value = admin;
		if (active == "1")
			$('RegionActive').checked = true;
		else
			$('RegionActive').checked = false;
		if (pub == "1")
			$('RegionPublic').checked = true;
		else
			$('RegionPublic').checked = false;
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
			if ($('GeoLevelLayerFile').length == 0) {
				$('chkmap').checked = false;
				$('shwmap').style.display = 'none';
			}
			else {
				$('chkmap').checked = true;
				$('shwmap').style.display = 'block';
			}
			$('GeoLevelLayerFile').value = ly1;
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

	function checkForm(fl, errmsg) {
		var field;
		var color = "";
		var status = true;
		for (i=0; i<fl.length; i++) {
			field = $(fl[i]);
			switch(field.type) {
				case "text":	case "hidden":	case "password":	case "textarea":
					// is it a required field?
					if (encodeURI(field.value).length < 1) {
						status = false;
						if (!status)
						  field.style.backgroundColor = "#f1c7c7";
					}
				break;   
				case "select-one":
					if (encodeURI(field.options[field.selectedIndex].value).length < 1) {
						status = false;
						if (!status)
						  field.style.backgroundColor = "#f1c7c7";
					}
				break;
			}
		}
		if (!status)
			alert(errmsg);
		return status;
	}

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
						alert('-' + fn + '- is a required field, please complete.');
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
		var keychar = String.fromCharCode(key);
		var opt = type.split(":"); // 0=type; 1=minlength; 2=minval-maxval
			// Accept keys: backspace, tab, shift, cntl, insert, delete
			//        pagedown, pageup, rows
		var spckey = (key==8 || key==9 || key==17 || key==20 ||
		              key==45 || key==46 || (key>=33 && key<=40) || key==0);
		var chk = true;
		var val = true; // validate characters
			// Check max length
		if (value.lenght >= parseInt(opt[1]))
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

	/**** SET DATACARD FORM  *****/
	function setElementValue(formElement, value) {
		switch(formElement.type) {
			case 'undefined': return;
			case 'radio': formElement.checked = value; break;
			case 'checkbox': formElement.checked = value; break;
			case 'select-one': 
				var unk = true;
				for (var w=0; w < formElement.length; w++) {
					if (formElement.options[w].value == value) {
						formElement.selectedIndex = w ;
						unk = false;
					}
				}
				if (unk)
					formElement[3]=new Option(value, value, false, true);
			break;
			case 'select-multiple':
				for(var x=0; x < formElement.length; x++)
					formElement[x].selected = value[x];
			break;
			default: 
				formElement.value = value; 
			break;
		}
	}

	function setDICardfromId(reg, did, src) {
		var lsAjax = new Ajax.Request('cards.php', {
			method: 'get', parameters: 'r='+ reg +'&DisasterId='+ did,
			onLoading: function(request) {
				if (src == "DATA")
					dostat = window.parent.frames['dif'].document.getElementById('dostat');
				else
					dostat = $('dostat');
				dostat.innerHTML = waiting;
			},
			onSuccess: function(request) {
				var res = request.responseText;
				var json = eval('(' + res + ')');
				setDICard(reg, json, src);
				dostat.innerHTML = "";
				return true;
			}
		} );
		return false;
	}
	
	function setDICard(reg, arr, src) {
		var diform = null;
		if (src == "DATA")
			diform = window.parent.frames['dif'].document.getElementById('DICard');
		else
			diform = $('DICard');
		var objElems = diform.elements; // DICard is DesInventar form..
		for (i=0; i < objElems.length; i++) {
			if (objElems[i].id != "geolev0") {
				setElementValue(objElems[i], arr[objElems[i].id]);
			}
			else {
				geo = arr['GeographyId'];
				getGeoItems(reg, arr['GeographyId'], 0, geo.length / 5, src);
			}
		}
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
		//alert("GEO: "+ geo +"- L: "+l +"-LEV: "+lev);
		if (l < lev) {
			var lsAjax = new Ajax.Updater( div, 'cards.php', {
				method: 'get', parameters: 'r='+ reg +'&cmd=list&GeographyId='+ geo,
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
