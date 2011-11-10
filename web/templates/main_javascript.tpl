<script type="text/javascript">
	/*
	var	w;
	var	s;
	*/

	// Layout, buttons and internal windows - UI DesConsultar module
	//Ext.onReady(function()
	function initializeExt()
	{
		/*
		// ==> Results Configuration Windows
		// Data
		var datw;
		var datb = Ext.get('dat-btn');
		if (datb != null)
		{
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
								{text: jQuery('#msgViewDataButtonClear').text(),
									handler: function() {
										$('CD').reset();
										} //handler
								},
								{text: jQuery('#msgViewDataButtonSend').text(),
									handler: function() {
										if (sendList("result")) {
											$('DCRes').value = "D";
											datw.hide();
											$('bsave').style.visibility = 'visible';
											$('bprint').style.visibility = 'visible';
										} else {
											console.debug('Error while executing function ViewData');
										}
									} //handler
								},
								{text: jQuery('#msgViewDataButtonClose').text(),
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
		}
		// Statistics
		var stdw;
		var stdb = Ext.get('std-btn');
		if (stdb != null)
		{
			stdb.on('click', function() {
				if (validateQueryDefinition()) {
					if (!stdw) {
						stdw = new Ext.Window({
							el:'std-win',  layout:'fit',  width:600, height:400, 
							closeAction:'hide', plain: true, animCollapse: false,
							items: new Ext.Panel({contentEl: 'std-cfg', autoScroll: true }),
							buttons: [
								{text: jQuery('#msgViewStdButtonClear').text(),
									handler: function() {
										$('CS').reset();
									}
								},
								{text: jQuery('#msgViewStdButtonSend').text(),
									handler: function() {
										if (sendStatistic("result")) {
											$('DCRes').value = "S";
											stdw.hide();
											$('bsave').style.visibility = 'visible';
											$('bprint').style.visibility = 'visible';
										} else {
											console.debug('Error while executing ViewStd function');
										}
									} //handler
								},
								{text: jQuery('#msgViewStdButtonClose').text(),
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
		}
		// Graphic
		var grpw;
		var grpb = Ext.get('grp-btn');
		if (grpb != null)
		{
			grpb.on('click', function() {
				if (validateQueryDefinition()) {
					if (!grpw) {
						grpw = new Ext.Window({
							el:'divGraphParameters',  layout:'fit',  width:750, height:420, 
							closeAction:'hide', plain: true, animCollapse: false,
							items: new Ext.Panel({contentEl: 'grp-cfg', autoScroll: true }),
							buttons: [
								{text: jQuery('#msgViewGraphButtonClear').text(),
									handler: function() {
										$('CG').reset();
									}
								},
								{text: jQuery('#msgViewGraphButtonSend').text(),
									handler: function() {
										sendGraphic('result');
										$('DCRes').value = "G";
										grpw.hide();
										$('bsave').style.visibility = 'visible';
										$('bprint').style.visibility = 'visible';
									}
								},
								{text: jQuery('#msgViewGraphButtonClose').text(),
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
		}
		// Map
		var map; // Map Object
		var mapw;
		var mapb = Ext.get('map-btn');
		if (mapb != null)
		{
			mapb.on('click', function() {
				if (validateQueryDefinition()) {
					if (!mapw) {
						mapw = new Ext.Window({
							el:'map-win',  layout:'fit',  width:650, height:400, 
							closeAction:'hide', plain: true, animCollapse: false,
							items: new Ext.Panel({contentEl: 'map-cfg', autoScroll: true }),
							buttons: [
								{text: jQuery('#msgViewMapButtonClear').text(),
									handler: function() {
										$('CM').reset();
									}
								},
								{text: jQuery('#msgViewMapButtonSend').text(),
									handler: function() {
										if (sendMap("result")) {
											$('DCRes').value = "M";
											mapw.hide();
											$('bsave').style.visibility = 'visible';
											$('bprint').style.visibility = 'visible';
										} else {
											console.debug('Error while executing function ViewMap');
										}
									}
								},
								{text: jQuery('#msgViewMapButtonClose').text(),
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
		}

		*/

	}
	//); // Ext.onReady()
	// end ExtJS object

	function setTotalize(lnow, lnext)
	{
		var sour = $(lnow);
		var dest = $(lnext);
		// clean dest list
		for (var i = dest.length - 1; i>=0; i--)
		{
			dest.remove(i);
		}
		for (var i=0; i < sour.length; i++)
		{
			if (!sour[i].selected)
			{
				var opt = document.createElement('option');
				opt.value = sour[i].value;
				opt.text = sour[i].text;
				var pto = dest.options[i];
				try
				{
					dest.add(opt, pto);
				}
				catch(ex)
				{
					dest.add(opt, i);
				}
			}
		} //for
	} //function
	
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
	
	// Find all Effects fields enable by saved query
	window.onload = function()
	{
		// select optimal height in results frame
		//varhgt = screen.height * 360 / 600;
		//$('dcr').style = "height:"+ hgt + "px;"
		{-foreach name=ef1 key=k item=i from=$ef1-}
			{-assign var="ff" value="D_$k"-}
			{-if $qd.$ff[0] != ''-}
				enadisEff('{-$k-}', true);
				showeff('{-$qd.$ff[0]-}', 'x{-$k-}', 'y{-$k-}');
			{-/if-}
		{-/foreach-}
		{-foreach name=sec key=k item=i from=$sec-}
			{-assign var="sc" value="D_$k"-}
			{-if $qd.$sc[0] != ''-}
				{-foreach name=sc2 key=k2 item=i2 from=$i[3]-}
					{-assign var="ff" value="D_$k2"-}
					{-if $qd.$ff[0] != ''-}
						enadisEff('{-$k2-}', true);
						showeff('{-$qd.$ff[0]-}', 'x{-$k2-}', 'y{-$k2-}');
					{-/if-}
				{-/foreach-}
				enadisEff('{-$k-}', true);
			{-/if-}
		{-/foreach-}
		{-foreach name=ef3 key=k item=i from=$ef3-}
			{-assign var="ff" value="D_$k"-}
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
	//var geotree = new CheckTree('geotree');
</script>
