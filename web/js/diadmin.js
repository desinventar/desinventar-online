/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
*/

	var mod = "";
	var opt = "";
	var reg = "";

	function uploadMsg(msg)
	{
		if (mod != '')
		{
			var mydiv = $(mod + 'statusmsg');
			mydiv.innerHTML = msg;
		}
	}
	
	function updateList(div, url, pars, callback)
	{
		jQuery('#' + div).load(url, pars, function(response, status, xhr) {
			// Hide first two columns (EventId,EventPredefined)
			jQuery('td:nth-child(1)','#tblEventListUser,#tblEventListPredef').hide();
			jQuery('td:nth-child(2)','#tblEventListUser,#tblEventListPredef').hide();
			// Hide first two columns (CauseId,CausePredefined)
			jQuery('td:nth-child(1)','#tblCauseListUser,#tblCauseListPredef').hide();
			jQuery('td:nth-child(2)','#tblCauseListUser,#tblCauseListPredef').hide();
		});
	}

	function updateUserBar(url, cmd, user, pass)
	{
		jQuery.post(url,
			{
				cmd      : cmd,
				userid   : user,
				password : pass
			},
			function(data)
			{
				jQuery('#pagecontent').html(data);
				if (cmd == "login")
				{
					updateList('pagecontent', url, 'cmd=welcome');
				}
				else if (cmd == "logout")
				{
					window.location.reload(false);
				}				
			}
		);
	} //function

	function setEveCau(id, name, desc, active, is_pred, module)
	{
		mod = module;
		$(mod + 'addsect').style.display = 'block';
		uploadMsg('');
		if (module == "event")
		{
			$('EventName').value = name;
			$('aEventDesc').value = desc;
			if (active == "1")
			{
				$('aEventActive').checked = true;
			}
			else
			{
				$('aEventActive').checked = false;
			}
			if (is_pred == "1")
			{
				$('aEventDesc').disabled = true;
			}
			else
			{
				$('aEventDesc').disabled = false;
			}
			$('aEventPredefined').value = is_pred;
			$('aEventId').value = id;
		}
		else if (module == "cause")
		{
			$('aCauseName').value = name;
			$('aCauseDesc').value = desc;
			if (active == "1")
			{
				$('aCauseActive').checked = true;
			}
			else
			{
				$('aCauseActive').checked = false;
			}
			if (is_pred == "1")
			{
				$('aCauseDesc').disabled = true;
			}
			else
			{
				$('aCauseDesc').disabled = false;
			}
			$('aCausePredefined').value = is_pred;
			$('aCauseId').value = id;
		}
	}

	function setRolLog (key, val, module)
	{
		mod = module;
		$(mod + 'addsect').style.display = 'block';
		if (module == "role")
		{
			$('UserId').value = key;
			$('AuthAuxValue').value = val;
		}
		else if (module == "log")
		{
			$('DBLogType').value = key;
			$('DBLogNotes').value = val;
		}
	}
	
	function setLevGeo(key, val, val2, val3, ly1, ly2, ly3, module)
	{
		mod = module;
		$(mod + 'addsect').style.display = 'block';
		if (module == "lev")
		{
			$('GeoLevelId').value = key;
			$('GeoLevelName').value = val;
			$('GeoLevelDesc').value = val2;
			$('GeoLevelLayerCode').value = ly2;
			$('GeoLevelLayerName').value = ly3;
		}
		else if (module == "geo")
		{
			$('aGeographyId').value = key;
			$('aGeographyCode').value = val;
			$('aGeographyName').value = val2;
		}
	}

	function setadmingeo(reg, k, l)
	{
		reg = jQuery('#desinventarRegionId').val();
		var v = k.split("|");
		mod = 'geo';
		uploadMsg('');
		if (v[0] == -1)
		{
			setLevGeo('','','',1,'','','','geo');
			if (l == 0)
			{
				$('aGeoParentId').value = '';
			}
			$('alev' + l).style.display = "none";
		}
		else if (v[0] == -2)
		{
			$('geoaddsect').style.display = 'none';
		}
		else
		{
			setLevGeo(v[0],v[1],v[2],v[3],'','','','geo');
			$('aGeoParentId').value = v[0];
			updateList('alev' + l, jQuery('#desinventarURL').val() + '/geography.php', 'r='+ reg +'&cmd=list&GeographyId=' + v[0]);
		}
	} //function
	
	function setUserPA(login, name, email, pass, cnt, city, active)
	{
		mod = "userpa";
		$(mod + 'addsect').style.display = 'block';
		$('UserId').value = login;
		$('UserFullName').value = name;
		$('UserEMail').value = email;
		$('UserPasswd').value = pass;
		$('UserCountry').value = cnt;
		$('UserCity').value = city;
		if (active == "1")
		{
			$('UserActive').checked = true;
		}
		else
		{
			$('UserActive').checked = false;
		}
	}

	var winopt = 'width=1020,height=700,left=0,top=0,screenX=0,screenY=0,resizable=no,scrollbars=no,status=no,toolbar=no';

	function setfocus(a_field_id)
	{
		$(a_field_id).focus();
	}

	function getForm(fobj)
	{
		var str = '';
		var ft = ''; 
		var fv = ''; 
		var fn = ''; 
		var els = ''; 
		for(var i = 0;i < fobj.elements.length;i++)
		{
			els = fobj.elements[i];
			ft = els.title;
			fv = els.value;
			fn = els.name;
			switch(els.type)
			{
				case "text": case "hidden": case "password": case "textarea":
					// is it a required field?
					if (encodeURI(ft) == "required" && encodeURI(fv).length < 1)
					{
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

	function getGeoItems(reg, geoid, l, lev, src)
	{
		if (src == "DATA")
		{
			div = window.parent.frames['dif'].document.getElementById('lev'+ l);
			ele = window.parent.frames['dif'].document.getElementById('geolev'+ l);
		}
		else
		{
			div = $('lev'+ l);
			ele = $('geolev'+ l);
		}
		geo = geoid.substr(0, (l+1) * 5);
		for (var w=0; w < ele.length; w++)
		{
			if (ele.options[w].value == geo)
				ele.selectedIndex = w ;
		}
		if (l < lev)
		{
			var lsAjax = new Ajax.Updater( div, jQuery('#desinventarURL').val() + '/cards.php',
				{
					method: 'get', 
					parameters: 'r='+ reg +'&cmd=list&GeographyId='+ geo + '&t=' + new Date().getTime(),
					onComplete: function(request) {
						getGeoItems(reg, geoid, l+1, lev, src);
					}
				});
		}
	}

	function showinfo(mydiv)
	{
		if ($(mydiv).style.display == 'none')
		{
			$(mydiv).style.display = 'block';
		}
		else
		{
			$(mydiv).style.display = 'none';
		}
	}

	function CheckIsIE()
	{
		if (navigator.appName.toUpperCase() == 'MICROSOFT INTERNET EXPLORER')
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function saveRes(cmd, typ)
	{
		if($('DCRes').value != '')
		{
			switch ($('DCRes').value)
			{
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
	
	function sendList(cmd)
	{
		if (cmd == 'result')
		{
			jQuery('#prmQueryCommand').val('cmdGridShow');
		}
		else
		{
			jQuery('#prmQueryCommand').val('cmdGridSave');
		}
		if ($('_D+Field[]').length > 0)
		{
			$('_D+cmd').value = cmd;
			selectall('_D+Field[]');
			var ob = $('_D+Field[]');
			var mystr = "";
			for (i=0; i < ob.length; i++)
			{
				mystr += ob[i].value + ",";
			}
			mystr += "D.DisasterId";
			$('_D+FieldH').value = mystr;
			combineForms('frmMainQuery', 'CD');
			Ext.getCmp('westm').show();
			Ext.getCmp('westm').collapse();
			$('frmMainQuery').action=jQuery('#desinventarURL').val() + '/data.php';
			jQuery('#frmMainQuery').attr('target','dcr');
			if (cmd != 'result')
			{
				jQuery('#frmMainQuery').attr('target', 'iframeDownload');
			}
			jQuery('#frmMainQuery').submit();
			//hideMap();
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function sendMap(cmd)
	{
		jQuery('#prmQueryCommand').val('cmdMapShow');
		if ($('_M+Type').length > 0)
		{
			$('_M+cmd').value = cmd;
			if (cmd == "export")
			{
				jQuery('#prmQueryCommand').val('cmdMapSave');

				// to export image save layers and extend..
				var dcr = document.getElementById('dcr');
				var mm = map;
				//var mm = dcr.map;
				var extent = mm.getExtent();
				var layers = mm.layers;
				var activelayers = [];
				for (i in layers)
				{
					if (layers[i].visibility && layers[i].calculateInRange() && !layers[i].isBaseLayer)
					{
						activelayers[activelayers.length] = layers[i].params['LAYERS'];
					}
				}
				
				jQuery('form.MapSave').attr('action', jQuery('#desinventarURL').val() + '/thematicmap.php');
				jQuery('form.MapSave').attr('target', 'iframeDownload');
				jQuery('form.MapSave input.Extent').val([extent.left,extent.bottom,extent.right,extent.top].join(','));
				jQuery('form.MapSave input.Layers').val(activelayers);
				jQuery('form.MapSave input.Id').val(jQuery('#prmMapId').val());
				jQuery('form.MapSave input.Title').val(jQuery('#MapTitle').val());
				jQuery('form.MapSave').trigger('submit');
			}
			else
			{
				combineForms('frmMainQuery', 'CM');
				Ext.getCmp('westm').show();
				Ext.getCmp('westm').collapse();
				$('frmMainQuery').action=jQuery('#desinventarURL').val() + '/thematicmap.php';
				jQuery('#frmMainQuery').attr('target','dcr');
				if (cmd != 'result')
				{
					jQuery('#frmMainQuery').attr('target', 'iframeDownload');
				}
				jQuery('#frmMainQuery').submit();
				//hideMap();
			}
			return true;
		}
		else
		{
			return false;
		}
	} //function
	
	function sendGraphic(cmd)
	{
		if (cmd == 'result')
		{
			jQuery('#prmQueryCommand').val('cmdGraphShow');
		}
		else
		{
			jQuery('#prmQueryCommand').val('cmdGraphSave');
		}
		jQuery('#prmGraphCommand').val(cmd);
		jQuery('#frmGraphParams input.TendencyLabel0').val(jQuery('#frmGraphParams #prmGraphTendency0 option:selected').text());
		jQuery('#frmGraphParams #prmGraphFieldLabel0').val(jQuery('#frmGraphParams #prmGraphField0 option:selected').text());
		jQuery('#frmGraphParams #prmGraphFieldLabel1').val(jQuery('#frmGraphParams #prmGraphField1 option:selected').text());
		
		combineForms('frmMainQuery', 'frmGraphParams');
		Ext.getCmp('westm').show();
		Ext.getCmp('westm').collapse();
		$('frmMainQuery').action=jQuery('#desinventarURL').val() + '/graphic.php';
		jQuery('#frmMainQuery').attr('target','dcr');

		if (cmd != 'result')
		{
			jQuery('#frmMainQuery').attr('target', 'iframeDownload');
		}
		jQuery('#frmMainQuery').submit();
		//hideMap();
	}
	
	function sendStatistic(cmd)
	{
		if (cmd == 'result')
		{
			jQuery('#prmQueryCommand').val('cmdStatShow');
		}
		else
		{
			jQuery('#prmQueryCommand').val('cmdStatSave');
		}
		if ( jQuery('#fldStatParam_FirstLev').val() != "" && $('fldStatFieldSelect').length > 0)
		{
			$('_S+cmd').value = cmd;
			//selectall('fldStatFieldSelect');
			var field      = 'D.DisasterId||';
			var fieldlabel = jQuery('#txtStatRecords').text();
			jQuery('#fldStatFieldSelect option').each(function() {
				field      += ',' + jQuery(this).val();
				fieldlabel += ',' + jQuery(this).text();
			});
			jQuery('#fldStatField').val(field);
			jQuery('#fldStatFieldLabel').val(fieldlabel);
			
			jQuery('#frmStatParams td.StatGroup').each(function() {
				jQuery('input', this).val(jQuery('select option:selected',this).text());
			});
			combineForms('frmMainQuery', 'frmStatParams');
			Ext.getCmp('westm').show();
			Ext.getCmp('westm').collapse();
			$('frmMainQuery').action=jQuery('#desinventarURL').val() + '/statistic.php';
			jQuery('#frmMainQuery').attr('target','dcr');
			if (cmd != 'result')
			{
				jQuery('#frmMainQuery').attr('target', 'iframeDownload');
			}
			jQuery('#frmMainQuery').submit();
			//hideMap();
			return true;
		}
		else
		{
			return false;
		}
	} //function
	
	function saveQuery()
	{
		jQuery('#prmQueryCommand').val('cmdQuerySave');
		selectall('_D+Field[]');
		combineForms('frmMainQuery', 'CD');
		combineForms('frmMainQuery', 'CM');
		combineForms('frmMainQuery', 'frmGraphParams');
		selectall('fldStatFieldSelect');
		combineForms('frmMainQuery', 'frmStatParams');
		jQuery('#_CMD').val('savequery');
		$('frmMainQuery').action=jQuery('#desinventarURL').val() + '/?r=' + jQuery('#desinventarRegionId').val();
		jQuery('#frmMainQuery').attr('target', 'iframeDownload');
		jQuery('#frmMainQuery').submit();
		return true;
	}

	function disab(field)
	{
		if (field != null)
		{
			field.disabled = true;
			field.className = "disabled";
		}
	}
	
	function enab(field)
	{
		if (field != null)
		{
			field.disabled = false;
			field.className = "";
		}
	}
	
	// Effects options
	function showeff(val, x, y)
	{
		if (val == ">=" || val == "<=" || val == "=" || val == "-3")
		{
			$(x).style.display = 'inline';
			if (val == "-3")
			{
				$(y).style.display = 'inline';
			}
			else
			{
				$(y).style.display = 'none';
			}
		}
		if (val == "" || val == "0" || val == "-1" || val == "-2")
		{
			$(x).style.display = 'none';
			$(y).style.display = 'none';
		}
	}
	
	function enadisEff(id, chk)
	{
		if (chk)
		{
			$('o'+ id).style.display = 'inline';
			enab($(id +'[0]'));
			enab($(id +'[1]'));
			enab($(id +'[2]'));
		}
		else
		{
			$('o'+ id).style.display = 'none';
			disab($(id +'[0]'));
			disab($(id +'[1]'));
			disab($(id +'[2]'));
		}
	}
	
	// forms management
	function combineForms(dcf, ref)
	{
		var dc = $(dcf);
		var rf = $(ref).elements;
		var ih = null;
		for (i=0; i < rf.length; i++)
		{
			if (rf[i].disabled == false)
			{
				ih = document.createElement("input");
				ih.type   = "hidden";
				ih.value  = rf[i].value;
				ih.name   = rf[i].name;
				dc.appendChild(ih);
			}
		}
	}
	
	function setAdvQuery(value, ope)
	{
		$('QueryCustom').value += value + ' ';
		switch (ope)
		{
			case 'text':
				disab($('<'));
				disab($('>'));
				enab($('='));  $('=').value = "=''";
				enab($('<>')); $('<>').value = "<>''";
				enab($("LIKE '%%'"));
				disab($('=-1')); disab($('=0')); disab($('=-2'));
			break;
			case 'date':
				enab($('<')); $('<').value = "<''";
				enab($('>')); $('>').value = ">''";
				enab($('=')); $('=').value = "=''";
				enab($('<>')); $('<>').value = "<>''";
				enab($("LIKE '%%'"));
				disab($('=-1')); disab($('=0')); disab($('=-2'));
			break;
			case 'number':
				enab($('<')); $('<').value = "<";
				enab($('>')); $('>').value = ">";
				enab($('=')); $('=').value = "=";
				enab($('<>'));$('<>').value = "<>";
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

	function printRes()
	{
		window.print();
	}
	
