{-config_load file=`$lg`.conf section="dc_querydesign"-}
{-if $ctl_show-}
<?xml version="1.0" encoding="UTF-8" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8; no-cache" />
  <title>{-#ttitle#-} -{-$regname-}-</title>
  <link rel="stylesheet" href="../css/desinventar.css" type="text/css">
  <link rel="stylesheet" href="../css/checktree.css" type="text/css">
  <link rel="stylesheet" href="../css/accordion.css" TYPE="text/css">
  <script type="text/javascript" src="../include/prototype.js"></script>
  <script type="text/javascript" src="../include/diadmin.js.php"></script>
  <script type="text/javascript" src="../include/checktree.js"></script>
  <script type="text/javascript" src="../include/wd.js"></script>
  <script type="text/javascript" src="../include/accordion.js"></script>
  <!-- ExtJS 2.0.1 -->
  <link rel="stylesheet" type="text/css" href="/extJS/resources/css/ext-all.css"/>
  <link rel="stylesheet" type="text/css" href="/extJS/resources/css/xtheme-gray.css"/>
  <script type="text/javascript" src="/extJS/adapter/ext/ext-base.js"></script>
  <script type="text/javascript" src="/extJS/ext-all.js"></script>
  <script type="text/javascript">
    // DI8 - Layout, buttons and internal windows - UI DesConsultar module
    Ext.onReady(function()
    {
      Ext.QuickTips.init();
      var mfile = new Ext.menu.Menu({
        id: 'fileMenu',
        items: [
//            {  text: '{-#mlang#-}',      handler: onMenuItem  },
            {  text: '{-#mprint#-}',     handler: onMenuItem  },
            {  text: '{-#mquit#-}',      handler: onMenuItem  }]
      });
      var mquery = new Ext.menu.Menu({
        id: 'queryMenu',
        items: [
            {  text: '{-#mgotoqd#-}',    handler: onMenuItem  },
            {  text: '{-#mnewsearch#-}', handler: onMenuItem } ]
      });
      var mhelp = new Ext.menu.Menu({
        id: 'helpMenu',
        items: [
            {  text: '{-#mgotodoc#-}',    handler: onMenuItem  },
            {  text: '{-#hmoreinfo#-}',		handler: onMenuItem  },
            {  text: '{-#motherdoc#-}',		handler: onMenuItem  },
            {  text: '{-#mabout#-}',     	handler: onMenuItem } ]
      });
      var tb = new Ext.Toolbar();
      tb.render('toolbar');
      tb.add(      {text:  '{-#mfile#-}',   menu:  mfile  });
      tb.add('-', {text:  '{-#msearch#-}',  menu: mquery });
      tb.add('-', {text:  '{-#mhelp#-}',    menu: mhelp });
      //
      function onMenuItem(item){
        var w = Ext.getCmp('westm');
        switch (item.text) {
          case "{-#mprint#-}":
            window.print();
          break;
          case "{-#mquit#-}":
            self.close();
          break;
          case "{-#mnewsearch#-}":
            w.show();
{-foreach name=ef1 key=key item=item from=$ef1-}
            if ($('{-$key-}').checked) enadisEff('{-$key-}', false);
{-/foreach-}
            $('DC').reset();
{-if $ctl_showmap-}
						fmp.clearAreas();
{-/if-}
          break;
          case "{-#mgotoqd#-}":
            if (w.isVisible())
            	w.collapse(); //hide()
            else
            	w.expand(); //show()
          break;
          case "{-#mabout#-}":
            alert("{-#tabout#-}");
          break;
          case "{-#mgotodoc#-}":
            $('ifr').src = "../region.php?r={-$reg-}&cmd=info{-if $isvreg-}&v=true{-/if-}";
          break;
          case "{-#motherdoc#-}":
            $('ifr').src = "../doc/LoNuevoEnDesInventar.pdf";
          break;
          case "{-#hmoreinfo#-}":
            runWin('../doc/?m=metguide', 'doc');
          break;
        }
      }
      // layout
      var viewport = new Ext.Viewport({
        layout:'border',
        items:[ {
            region:'north',
            height: 30,
            contentEl: 'north'
          },{
            region: 'south',
            id: 'southm',
            split: false,
            title: '{-#tmguidedef#-}',
            height: 80,
            minSize: 100,
            maxSize: 200,
            margins: '0 0 0 0',
            contentEl: 'south',
            collapsible: true
          }, new Ext.Panel({
            region: 'center',
            id: 'centerm',
//            title: '{-#tsubtitle2#-}',
            contentEl: 'container'
          }),{
            region: 'west',
            id: 'westm',
            split: false,
            width: 300,
            title: '{-#tsubtitle#-}',
            autoScroll: true,
            margins:'0 2 0 0',
            collapsible: true,
            contentEl: 'west'
          }]
      });
      // ==> Results Configuration Windows
      // Data
      var datw;
      var datb = Ext.get('dat-btn');
      datb.on('click', function(){
        if(!datw){  
          datw = new Ext.Window({
            el:'dat-win',  layout:'fit',  width:600, height:400, 
            closeAction:'hide', plain: true, animCollapse: false,
            items: new Ext.Panel({
                contentEl: 'dat-cfg', autoScroll: true }),
            buttons: [{
                text:'{-#tclean#-}',
                handler: function() {
                    $('CD').reset();
                }
              },{
                text:'{-#tsend#-}',
                handler: function() {
                    if (sendList("result")) {
                      $('DCRes').value = "D";
                      datw.hide();
                    }
                    else
                      alert("{-#derrmsgfrm#-}");
                }
              },{
                text:'{-#tclose#-}',
                handler: function(){
                    datw.hide(); }
              }]
          });
        }
        datw.show(this);
      });
      // Stadistic
      var stdw;
      var stdb = Ext.get('std-btn');
      stdb.on('click', function() {
        if(!stdw) {
          stdw = new Ext.Window({
            el:'std-win',  layout:'fit',  width:600, height:400, 
            closeAction:'hide', plain: true, animCollapse: false,
            items: new Ext.Panel({
                contentEl: 'std-cfg', autoScroll: true }),
            buttons: [{
                text:'{-#tclean#-}',
                handler: function() {
                    $('CS').reset(); }
              },{
                text:'{-#tsend#-}',
                handler: function() {
                    if (sendStadistic("result")) {
                      $('DCRes').value = "S";
                      stdw.hide();
                    }
                    else
                      alert("{-#serrmsgfrm#-}");
                }
              },{
                text:'{-#tclose#-}',
                handler: function(){
                    stdw.hide(); }
              }]
          });
        }
        stdw.show(this);
      });
      // Graphic
      var grpw;
      var grpb = Ext.get('grp-btn');
      grpb.on('click', function() {
        if(!grpw) {
          grpw = new Ext.Window({
            el:'grp-win',  layout:'fit',  width:600, height:400, 
            closeAction:'hide', plain: true, animCollapse: false,
            items: new Ext.Panel({
                contentEl: 'grp-cfg', autoScroll: true }),
            buttons: [{
                text:'{-#tclean#-}',
                handler: function() {
                    $('CG').reset(); }
              },{
                text:'{-#tsend#-}',
                handler: function() {
                    sendGraphic("result");
                    $('DCRes').value = "G";
                    grpw.hide(); }
              },{
                text:'{-#tclose#-}',
                handler: function(){
                    grpw.hide(); }
              }]
          });
        }
        grpw.show(this);
      });
      // Map
      var mapw;
      var mapb = Ext.get('map-btn');
      mapb.on('click', function() {
        if(!mapw) {
          mapw = new Ext.Window({
            el:'map-win',  layout:'fit',  width:600, height:400, 
            closeAction:'hide', plain: true, animCollapse: false,
            items: new Ext.Panel({
                contentEl: 'map-cfg', autoScroll: true }),
            buttons: [{
                text:'{-#tclean#-}',
                handler: function() {
                    $('CM').reset(); }
              },{
                text:'{-#tsend#-}',
                handler: function() {
                		setfocus('_M+limit[0]');
                    sendMap("result");
                    $('DCRes').value = "M";
                    mapw.hide(); 
                }
              },{
                text:'{-#tclose#-}',
                handler: function(){
                    mapw.hide(); }
              }]
          });
        }
        mapw.show(this);
      });
      // quicktips
      Ext.apply(Ext.QuickTips.getQuickTip(), {
        maxWidth: 200, minWidth: 100, showDelay: 50, trackMouse: true });
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
      var d = $('_DIDesc');
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
      }
      else {
        $('o'+ id).style.display = 'none';
        disab($(id +'[0]'));
        disab($(id +'[1]'));
        disab($(id +'[2]'));
      }
    }
    function disabAxis2() {
    	$('_G+Field2').value = "";
    	disab($('_G+Field2'));
    	disab($('_G+Scale2'));
    	disab($('_G+Data2'));
    	disab($('_G+Mode2'));
    }
    function enabAxis2() {
    	enab($('_G+Field2'));
    	enab($('_G+Scale2'));
    	enab($('_G+Data2'));
    	enab($('_G+Mode2'));
    }
    function grpSelectbyType(fld) {
      var grp = $(fld).value;
      // Comparatives
      if (grp == "D.EventId" || grp == "D.CauseId" ||
          grp.substr(0,21) == "D.DisasterGeographyId") {
        disab($('_G+K_line'));
        disabAxis2();
        enab($('_G+K_pie'));
        $('_G+Kind').value = "PIE";
        $('_G+Period').value = "";
        disab($('_G+Period'));
        $('_G+Stat').value = "";
        disab($('_G+Stat'));
        disab($('_G+Scale'));
        disab($('_G+M_accu'));
        disab($('_G+M_over'));
        enab($('_G+D_perc'));
      }
      else {
      	enab($('_G+K_line'));
      	disab($('_G+K_pie'));
        $('_G+Kind').value = "BAR";
        enab($('_G+Period'));
        enab($('_G+Stat'));
        $('_G+Period').value = 'YEAR';
        enab($('_G+Scale'));
        var histt = $(fld).value;
        if (histt.substr(19, 1) == "|") {
        	disabAxis2();
        	disab($('_G+M_accu'));
        	enab($('_G+M_over'));
        }
        else {
        	enabAxis2();
        	enab($('_G+M_accu'));
        	disab($('_G+M_over'));
        }
        disab($('_G+D_perc'));
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
    // selection map functions
    function showMap(lev) {
    	$('smap').style.visibility = 'visible';
    }
    function hideMap() {
    	$('smap').style.visibility = 'hidden';
    }
    function setSelMap(code, gid, opc) {
{-if $ctl_showmap-}
			showMap(0);
    	fmp.selectArea(code, '');
{-else-}
    	if (opc)
    		setgeo(gid);
    	else
    		unsetgeo(gid);
{-/if-}
    }
    function setgeo(k) {
      // Find and fill childs
      $('itree' + k).style.display = 'block';
      updateList('itree' + k, 'index.php', 'r={-$reg-}&cmd=glist&GeographyId=' + k); 
    }
    function unsetgeo(k) {
    	// clean childs first
    	$('itree' + k).innerHTML = '';
    	$('itree' + k).style.display = 'none';
    }
    function saveRes(cmd) {
      switch ($('DCRes').value) {
        case 'D':
          sendList(cmd);
        break;
        case 'M':
          sendMap(cmd);
        break;
        case 'G':
          sendGraphic(cmd);
        break;
        case 'S':
          sendStadistic(cmd);
        break;
      }
    }
    function sendList(cmd) {
      if ($('_D+Field[]').length > 0) {
        $('_D+cmd').value = cmd;
        selectall('_D+Field[]');
        var ob = $('_D+Field[]');
        var mystr = "";
        for (i=0; i < ob.length; i++)
          mystr += ob[i].value + ",";
        mystr += "D.DisasterId";
        $('_D+FieldH').value = mystr;
        combineForms('DC', 'CD');
        var w = Ext.getCmp('westm');
        w.collapse(); //hide()
        var s = Ext.getCmp('southm');
        s.collapse();
        $('DC').action='data.php';
        $('DC').submit();
        hideMap();
        return true;
      }
      else
        return false;
    }
    function sendMap(cmd) {
      //$('frmwait').innerHTML = waiting;
      $('_M+cmd').value = cmd;
      combineForms('DC', 'CM');
      var w = Ext.getCmp('westm');
      w.collapse(); // hide()
      var s = Ext.getCmp('southm');
      s.collapse();
      $('DC').action='thematicmap.php';
      $('DC').submit();
      hideMap();
    }
    function sendGraphic(cmd) {
      $('_G+cmd').value = cmd;
      combineForms('DC', 'CG');
      var w = Ext.getCmp('westm');
      w.collapse(); //hide()
      var s = Ext.getCmp('southm');
      s.collapse();
      $('DC').action='graphic.php';
      $('DC').submit();
      hideMap();
    }
    function sendStadistic(cmd) {
      if ($('_S+Firstlev').value != "" && $('_S+Field[]').length > 0) {
        $('_S+cmd').value = cmd;
        selectall('_S+Field[]');
        var ob = $('_S+Field[]');
        var mystr = "D.DisasterId||";
        for (i=0; i < ob.length; i++)
          mystr += "," + ob[i].value;
        $('_S+FieldH').value = mystr;
        combineForms('DC', 'CS');
        var w = Ext.getCmp('westm');
        w.collapse();//hide()
        var s = Ext.getCmp('southm');
        s.collapse();
        $('DC').action='stadistic.php';
        $('DC').submit();
        hideMap();
        return true;
      }
      else
        return false;
    }
    //var g{-$reg-} = new CheckTree('g{-$reg-}');
  </script>
  <!--
   <link rel="stylesheet" href="../css/tabquery.css" TYPE="text/css">
    //document.write('<style type="text/css">.tabber{display:none;}<\/style>');
    <script type="text/javascript" src="../include/tabber.js"></script>-->
  <!-- MAP extra -->
  <script type="text/javascript" src="../include/palette.js"></script>
  <script type="text/javascript">
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
    // remove from table
    function removeRowFromTable() {
      var tbl = $('tbl_range');
      var lastRow = tbl.rows.length;
      if (lastRow > 2) tbl.deleteRow(lastRow - 1);
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
            dest.add(opt, pto);  }
          catch(ex) {
            dest.add(opt, i);    }
        }
      }
    }
  </script>
  <script type="text/javascript" src="../include/listMan.js"></script>
</head>

<body>
 <div id="north">
    <div id="toolbar"></div>
 </div>
 <div id="container">
   <table border="0" cellpadding="0" cellspacing="0" width="100%">
     <tr bgcolor="#bbbbbb">
       <td width="260px">
       	<b>{-#tsubtitle2#-} =></b>
<!--       	<img src="../images/collapse.png" onClick="var w = Ext.getCmp('westm'); w.show();">-->
       </td>
       <td>
<!--
        SECTION : DATA CONFIGURATION
        ============================
-->
        <input type="button" id="dat-btn" value="{-#bdata#-}" ext:qtip="{-#tdatamsg#-}"
           class="btn" {-if $isvreg-}style="visibility:hidden;"{-/if-}>
        <div id="dat-win" class="x-hidden">
          <div class="x-window-header">{-#bdata#-}</div>
          <div id="dat-cfg">
            <form id="CD" method="POST">
              {-#sresxpage#-}
              <select id="_D+SQL_LIMIT" name="_D+SQL_LIMIT">
                <option value="20">20</option>
                <option value="50">50</option>
                <option value="100" selected>100</option>
                <option value="200">200</option>
              </select>
              <br><br>
              <table>
                <tr>
                  <td><b>{-#savailfields#-}</b><br>
                   <select id="_D+sel1[]" size="8" style="width:220px;" multiple>
 {-foreach name=sst1 key=key item=item from=$sda1-}
                    <option value="D.{-$item-}">{-$dc2.$item[0]-}</option>
 {-/foreach-}
 										<option disabled>---</option>
 {-foreach name=sst2 key=key item=item from=$exteffel-}
                    <option value="E.{-$key-}">{-$item[0]-}</option>
 {-/foreach-}
                   </select><br>
                   <input type="button" value="{-#balls#-}" onclick="selectall('_D+sel1[]');" class="line">
                   <input type="button" value="{-#bnone#-}" onclick="selectnone('_D+sel1[]');" class="line">
                  </td>
                  <td align="center" valign="middle" style="width:20px;">
                   <input type="button" value="--&gt;" onclick="moveOptions($('_D+sel1[]'), $('_D+Field[]'));" class="line">
                   <br><br><br>
                   <input type="button" value="&lt;--" onclick="moveOptions($('_D+Field[]'), $('_D+sel1[]'));" class="line">
                  </td>
                  <td><b>{-#sviewfields#-}</b><br>
                   <select id="_D+Field[]" size="8" style="width:220px;" multiple>
 {-foreach name=sst key=key item=item from=$sda-}
  {-if $item != "D.DisasterId"-}
                    <option value="D.{-$item-}">{-$dc2.$item[0]-}</option>
  {-/if-}
 {-/foreach-}
                   </select><br>
                   <input type="button" value="{-#balls#-}" onclick="selectall('_D+Field[]');" class="line">
                   <input type="button" value="{-#bnone#-}" onclick="selectnone('_D+Field[]');" class="line">
                  </td>
                  <td style="width:20px;" align="center">
                   <input type="button" value="&uarr;&uarr;" onclick="top('_D+Field[]');" class="line"><br>
                   <input type="button" value="&uarr;" onclick="upone('_D+Field[]');" class="line"><br>
                   <input type="button" value="&darr;" onclick="downone('_D+Field[]');" class="line"><br>
                   <input type="button" value="&darr;&darr;" onclick="bottom('_D+Field[]');" class="line"><br>
                  </td>
                </tr>
              </table>
              <br><br>
              <b>{-#dorderby#-}</b><br>
              <select id="_D+SQL_ORDER" name="_D+SQL_ORDER" class="fixw" size="5">
                <option value="D.DisasterBeginTime, V.EventName, G.GeographyName" selected>{-#ddeg#-}</option>
                <option value="D.DisasterBeginTime, D.DisasterGeographyId, V.EventName">{-#ddge#-}</option>
                <option value="G.GeographyName, V.EventName, D.DisasterBeginTime">{-#dged#-}</option>
                <option value="V.EventName, D.DisasterBeginTime, G.GeographyName">{-#dedg#-}</option>
                <option value="D.DisasterSerial">{-#dserial#-}</option>
                <option value="D.RecordCreation">{-#dcreation#-}</option>
                <option value="D.RecordLastUpdate">{-#dlastupd#-}</option>
              </select>
              <input type="hidden" id="_D+FieldH" name="_D+Field" value="">
              <input type="hidden" id="_D+cmd" name="_D+cmd" value="result">
            </form>
          </div>
        </div>
<!--
        SECTION : THEMATICMAP CONFIGURATION
        ====================================
-->
        <input type="button" id="map-btn" ext:qtip="{-#tthematicmsg#-}"
           value="{-#bthematic#-}" class="btn"{-if !$ctl_showmap-} style="display:none;"{-/if-}>
        <div id="map-win" class="x-hidden">
          <div class="x-window-header">{-#bthematic#-}</div>
          <div id="map-cfg">
          	<div id="colorpicker201" class="colorpicker201"></div>
            <form id="CM" method="POST">
              <table class="conf">
                <tr valign="top"><td>
                  <b>{-#mareaid#-}</b>
                  <br>
                  <select name="_M+Label" size="4" class="fixw">
                    <option value="NAME">{-#mareashownam#-}</option>
                    <option value="CODE">{-#mareashowcod#-}</option>
                    <option value="VALUE">{-#mareashowval#-}</option>
                    <option value="NONE" selected>{-#mareanotshow#-}</option>
                  </select>
                  <br><br>
                  <b>{-#mranlegcol#-}</b>&nbsp; &nbsp; &nbsp; &nbsp;
<!-- IE Not found.. -->
                  <input type="button" value="+" onclick="addRowToTable();" class="line">
                  <input type="button" value="-" onclick="removeRowFromTable();" class="line">
                  <br>
                  <table border="0" id="tbl_range" class="grid">
                   <thead>
                    <th colspan=2>{-#mrange#-}</th><th>{-#mlegend#-}</th><th>{-#mcolor#-}</th>
                   </thead>
                   <tbody id="range">
 {-foreach name=rg key=k item=i from=$range-}
                    <tr>
                    <td>{-$smarty.foreach.rg.iteration-}</td>
                    <td><input type="text" id="_M+limit[{-$smarty.foreach.rg.iteration-1-}]" class="line"
                          name="_M+limit[{-$smarty.foreach.rg.iteration-1-}]" size="5" value="{-$i[0]-}"
                          onBlur="miv={-if $smarty.foreach.rg.iteration > 1-}parseInt($('_M+limit[{-$smarty.foreach.rg.iteration-2-}]').value)+1{-else-}1{-/if-}; $('_M+legend[{-$smarty.foreach.rg.iteration-1-}]').value='{-#mbetween#-} '+ miv +'- '+ this.value"></td>
                    <td><input type="text" id="_M+legend[{-$smarty.foreach.rg.iteration-1-}]" class="line"
                          name="_M+legend[{-$smarty.foreach.rg.iteration-1-}]" size="20" value="{-#mbetween#-} {-$i[1]-}"></td>
                    <td><input type="text" id="_M+ic[{-$smarty.foreach.rg.iteration-1-}]" 
                          size="3" value="" style="background:#{-$i[2]-};" class="line"
                          onclick="showColorGrid2('_M+color[{-$smarty.foreach.rg.iteration-1-}]','_M+ic[{-$smarty.foreach.rg.iteration-1-}]');">
                        <input type="hidden" id="_M+color[{-$smarty.foreach.rg.iteration-1-}]" 
                          name="_M+color[{-$smarty.foreach.rg.iteration-1-}]" value="{-$i[2]-}"></td>
                    </tr>
 {-/foreach-}
                   </tbody>
                  </table>
                </td><td>
                  <b>{-#mrepreselev#-}</b><br>
                  <select id="_M+Type" name="_M+Type" size="3" class="fixw">
 {-foreach name=mgel key=k item=i from=$mgel-}
                    <option value="{-$k-}|D.DisasterGeographyId|" {-if $smarty.foreach.mgel.iteration==1-}selected{-/if-}>{-$i[0]-}</option>
 {-/foreach-}
                  </select>
                  <br><br>
                  <b>{-#mviewfields#-}</b><br>
                  <select id="_M+Field" name="_M+Field" size="8" class="fixw">
                    <option value="D.DisasterId||" selected>{-#trepnum#-}</option>
 {-foreach name=ef1 key=k item=i from=$ef1-}
                    <option value="D.{-$k-}|>|-1">{-$i[0]-}</option>
                    <option value="D.{-$k-}|=|-1">{-#tauxhave#-} {-$i[0]-}</option>
 {-/foreach-}
 {-foreach name=ef2 key=k item=i from=$ef2-}
                    <option value="D.{-$k-}|>|-1">{-$i[0]-}</option>
 {-/foreach-}
 {-foreach name=ef3 key=k item=i from=$ef3-}
                    <option value="D.{-$k-}|>|-1">{-$i[0]-}</option>
 {-/foreach-}
 {-foreach name=ef3 key=k item=i from=$sec-}
                    <option value="D.{-$k-}|=|-1">{-#tauxaffect#-} {-$i[0]-}</option>
 {-/foreach-}
 										<option disabled>---</option>
 {-foreach name=eef key=k item=i from=$exteffel-}
  {-if $i[2] == "INTEGER" || $i[2] == "DOUBLE"-}
                    <option value="E.{-$k-}|>|-1">{-$i[0]-}</option>
  {-/if-}
 {-/foreach-}
                  </select>
                  <input type="hidden" id="_M+cmd" name="_M+cmd" value="result">
                </td></tr>
              </table>
            </form>
          </div>
        </div>
<!--     END MAP SECTION -->
<!--
        SECTION : GRAPHIC CONFIGURATION
        ==============================
-->
        <input type="button" id="grp-btn" value="{-#bgraphic#-}" ext:qtip="{-#tgraphicmsg#-}"
           class="btn" {-if $isvreg-}style="visibility:hidden;"{-/if-}>
        <div id="grp-win" class="x-hidden">
          <div class="x-window-header">{-#bgraphic#-}</div>
          <div id="grp-cfg">
            <form id="CG" method="POST">
            	<table class="conf" cellpadding=0 cellspacing=0>
            		<tr valign="bottom">
            			<td colspan=3 align="center">
            				<table>
            					<tr>
            						<td>
            							<b>{-#gopttitle#-}</b><br>
            							<input type="text" name="_G+Title" class="line fixw">
            						</td>
            						<td>
            							<b>{-#goptsubtit#-}</b><br>
            							<input type="text" name="_G+Title2" class="line fixw">
            						</td>
            					</tr>
            				</table>
            				<hr>
            			</td>
            		</tr>
            		<tr valign="bottom">
            			<td align="right">
            				<p align="center"><u>{-#gveraxis#-} 1:</u></p>
            				<b onMouseOver="showtip('{-$dic.GraphField[2]-}');">{-$dic.GraphField[0]-}</b><br>
            				<select id="_G+Field" name="_G+Field" onMouseOver="showtip('{-$dic.GraphField[2]-}');">
            					<option value="D.DisasterId||" selected>{-$dic.GraphDisasterId_[0]-}</option>
 {-foreach name=ef1 key=k item=i from=$ef1-}
                    	<option value="D.{-$k-}|>|-1">{-$i[0]-}</option>
                    	<option value="D.{-$k-}|=|-1">{-#tauxhave#-} {-$i[0]-}</option>
 {-/foreach-}
 {-foreach name=ef2 key=k item=i from=$ef2-}
                    	<option value="D.{-$k-}|>|-1">{-$i[0]-}</option>
 {-/foreach-}
 {-foreach name=ef3 key=k item=i from=$ef3-}
                    	<option value="D.{-$k-}|>|-1">{-$i[0]-}</option>
 {-/foreach-}
 {-foreach name=ef3 key=k item=i from=$sec-}
                    	<option value="D.{-$k-}|=|-1">{-#tauxaffect#-} {-$i[0]-}</option>
 {-/foreach-}
 											<option disabled>___</option>
 {-foreach name=eef key=k item=i from=$exteffel-}
  {-if $i[2] == "INTEGER" || $i[2] == "DOUBLE"-}
                    	<option value="E.{-$k-}|>|-1">{-$i[0]-}</option>
  {-/if-}
 {-/foreach-}
                  	</select>
                  	<br>
                  	<b onMouseOver="showtip('{-$dic.GraphScale[2]-}');">{-$dic.GraphScale[0]-}</b><br>
                  	<select id="_G+Scale" name="_G+Scale" onMouseOver="showtip('{-$dic.GraphScale[2]-}');">
                  		<option value="textint" selected>{-#gscalin#-}</option>
                  		<option value="textlog">{-#gscalog#-}</option>
                  	</select>
                  	<br>
                  	<b onMouseOver="showtip('{-$dic.GraphShow[2]-}');">{-$dic.GraphShow[0]-}</b><br>
                  	<select id="_G+Data" name="_G+Data" onMouseOver="showtip('{-$dic.GraphShow[2]-}');">
                  		<option value="VALUE">{-#gshwval#-}</option>
                  		<option id="_G+D_perc" value="PERCENT" disabled>{-#gshwperce#-}</option>
                  		<option id="_G+D_none" value="NONE" selected>{-#gshwnone#-}</option>
                  	</select>
                  	<br>
                  	<b onMouseOver="showtip('{-$dic.GraphMode[2]-}');">{-$dic.GraphMode[0]-}</b><br>
            				<select id="_G+Mode" name="_G+Mode" onMouseOver="showtip('{-$dic.GraphMode[2]-}');">
                  		<option value="NORMAL" selected>{-#gmodnormal#-}</option>
                  		<option id="_G+M_accu" value="ACCUMULATE">{-#gmodaccumul#-}</option>
                  		<option id="_G+M_over" value="OVERCOME" disabled>{-#gmodovercome#-}</option>
                  	</select>
                  </td>
            			<td align="center">
            				<table border=1 width="120px" height="120px">
            					<tr><td align="center">
            					<!--<b onMouseOver="showtip('{-$dic.GraphKind[2]-}');">{-$dic.GraphKind[0]-}</b><br>-->
            						<select id="_G+Kind" name="_G+Kind" size="3" 
            								onMouseOver="showtip('{-$dic.GraphKind[2]-}');">
            							<option value="BAR" selected>{-#gkndbars#-}</option>
            							<option id="_G+K_line" value="LINE">{-#gkndlines#-}</option>
            							<option id="_G+K_pie" value="PIE" disabled>{-#gkndpie#-}</option>
            						</select>
            						<br><br>
            					<!--<b onMouseOver="showtip('{-$dic.GraphFeel[2]-}');">{-$dic.GraphFeel[0]-}</b><br>-->
            						<select id="_G+Feel" name="_G+Feel" size="2" onMouseOver="showtip('{-$dic.GraphFeel[2]-}');">
            							<option value="2D">{-#gfee2d#-}</option>
            							<option value="3D" selected>{-#gfee3d#-}</option>
            						</select>
            					</td></tr>
            				</table>
            			</td>
            			<td>
            				<p align="center"><u>{-#gveraxis#-} 2:</u></p>
            				<b onMouseOver="showtip('{-$dic.GraphField[2]-}');">{-$dic.GraphField[0]-}</b><br>
            				<select id="_G+Field2" name="_G+Field2" size="1" onMouseOver="showtip('{-$dic.GraphField[2]-}');"
            						onChange="enab($('_G+Scale2')); enab($('_G+Data2')); enab($('_G+Mode2'));">
            					<option value="" selected></option>
            					<option value="D.DisasterId||">{-$dic.GraphDisasterId_[0]-}</option>
 {-foreach name=ef1 key=k item=i from=$ef1-}
                    	<option value="D.{-$k-}|>|-1">{-$i[0]-}</option>
                    	<option value="D.{-$k-}|=|-1">{-#tauxhave#-} {-$i[0]-}</option>
 {-/foreach-}
 {-foreach name=ef2 key=k item=i from=$ef2-}
                    	<option value="D.{-$k-}|>|-1">{-$i[0]-}</option>
 {-/foreach-}
 {-foreach name=ef3 key=k item=i from=$ef3-}
                    	<option value="D.{-$k-}|>|-1">{-$i[0]-}</option>
 {-/foreach-}
 {-foreach name=ef3 key=k item=i from=$sec-}
                    	<option value="D.{-$k-}|=|-1">{-#tauxaffect#-} {-$i[0]-}</option>
 {-/foreach-}
 											<option disabled>___</option>
 {-foreach name=eef key=k item=i from=$exteffel-}
  {-if $i[2] == "INTEGER" || $i[2] == "DOUBLE"-}
                    	<option value="E.{-$k-}|>|-1">{-$i[0]-}</option>
  {-/if-}
 {-/foreach-}
                  	</select>
                  	<br>
                  	<b onMouseOver="showtip('{-$dic.GraphScale[2]-}');">{-$dic.GraphScale[0]-}</b><br>
                  	<select id="_G+Scale2" name="_G+Scale2" disabled class="disabled" onMouseOver="showtip('{-$dic.GraphScale[2]-}');">
                  		<option value="int" selected>{-#gscalin#-}</option>
                  		<option value="log">{-#gscalog#-}</option>
                  	</select>
                  	<br>
                  	<b onMouseOver="showtip('{-$dic.GraphShow[2]-}');">{-$dic.GraphShow[0]-}</b><br>
                  	<select id="_G+Data2" name="_G+Data2" disabled class="disabled" onMouseOver="showtip('{-$dic.GraphShow[2]-}');">
                  		<option value="VALUE">{-#gshwval#-}</option>
                  		<option id="_G+D_perc2" value="PERCENT" disabled>{-#gshwperce#-}</option>
                  		<option id="_G+D_none2" value="NONE" selected>{-#gshwnone#-}</option>
                  	</select>
                  	<br>
                  	<b onMouseOver="showtip('{-$dic.GraphMode[2]-}');">{-$dic.GraphMode[0]-}</b><br>
            				<select id="_G+Mode2" name="_G+Mode2" disabled class="disabled" onMouseOver="showtip('{-$dic.GraphMode[2]-}');">
                  		<option value="NORMAL" selected>{-#gmodnormal#-}</option>
                  		<option id="_G+M_accu2" value="ACCUMULATE">{-#gmodaccumul#-}</option>
                  		<option id="_G+M_over2" value="OVERCOME" disabled>{-#gmodovercome#-}</option>
                  	</select>
            			</td>
            		</tr>
            		<tr>
            			<td colspan=3 align="center">
            				<p><u>{-#ghoraxis#-}:</u></p>
            				<b onMouseOver="showtip('{-$dic.GraphType[2]-}');">{-$dic.GraphType[0]-}</b><br>
            				<select id="_G+Type" name="_G+Type" onChange="grpSelectbyType('_G+Type');" 
            						onMouseOver="showtip('{-$dic.GraphType[2]-}');" size="4" class="fixw">
            					<optgroup label="{-#ghistogram#-}">
                      <option value="D.DisasterBeginTime" selected>{-$dic.GraphHisTemporal[0]-}</option>
                      <option value="D.DisasterBeginTime|D.EventId">{-$dic.GraphHisEveTemporal[0]-}</option>
{-foreach name=glev key=k item=i from=$glev-}
                      <option value="D.DisasterBeginTime|D.DisasterGeographyId_{-$k-}">{-$i[0]-} {-$dic.GraphHisGeoTemporal[0]-}</option>
{-/foreach-}
                      <option value="D.DisasterBeginTime|D.CauseId">{-$dic.GraphHisCauTemporal[0]-}</option>
                      </optgroup>
                      <optgroup label="{-#gcomparative#-}">
                      <option value="D.EventId">{-$dic.GraphComByEvents[0]-}</option>
                      <option value="D.CauseId">{-$dic.GraphComByCauses[0]-}</option>
{-foreach name=glev key=k item=i from=$glev-}
                      <option value="D.DisasterGeographyId_{-$k-}">{-$dic.GraphComByGeography[0]-} {-$i[0]-}</option>
{-/foreach-}
                    	</optgroup>
                    </select>
                    <br>
                    <table border=0 cellpadding=0 cellspacing=0 align="center">
                    <tr><td>
                    	<b onMouseOver="showtip('{-$dic.GraphPeriod[2]-}');">{-$dic.GraphPeriod[0]-}</b><br>
                    	<select id="_G+Period" name="_G+Period" onChange="$('_G+Stat').value = '';"
                    			onMouseOver="showtip('{-$dic.GraphPeriod[2]-}');">
                    	<option value=""></option>
                    	<option value="YEAR" selected>{-#gperannual#-}</option>
                    	<option value="YMONTH">{-#gpermonth#-}</option>
                    	<option value="YWEEK">{-#gperweek#-}</option>
                    	<option value="YDAY">{-#gperday#-}</option>
                    	</select>
                    </td><td>
                    	<b onMouseOver="showtip('{-$dic.GraphSeaHistogram[2]-}');">{-#GHISTOANNUAL#-}</b><br>
                    	<select id="_G+Stat" name="_G+Stat" onChange="$('_G+Period').value = '';"
                    			onMouseOver="showtip('{-$dic.GraphSeaHistogram[2]-}');">
                    	<option value=""></option>
                    	<option value="DAY">{-#gseaday#-}</option>
                    	<option value="WEEK">{-#gseaweek#-}</option>
                    	<option value="MONTH">{-#gseamonth#-}</option>
                    	</select>
                    </td></tr>
                    </table>
            			</td>
            		</tr>
            	</table>
            	<input type="hidden" id="_G+cmd" name="_G+cmd" value="result">
<!--
              <table class="conf">
                <tr valign="top">
                 <td>
                  <b>{-#gopttitle#-}</b><br>
                  <input type="text" name="_G+Title" class="line fixw">
                  <br>
                  <b>{-#goptsubtit#-}</b><br>
                  <input type="text" name="_G+Title2" class="line fixw">
                  <br><br>
                  <b onMouseOver="showtip('{-$dic.GraphType[2]-}');">{-$dic.GraphType[0]-}</b><br>
                  <select id="_G+Type" name="_G+Type" size="6" class="fixw"
                      onChange="grpSelectbyType('_G+Type');" onMouseOver="showtip('{-$dic.GraphType[2]-}');">
                    <optgroup label="{-#ghistogram#-}">
                      <option value="D.DisasterBeginTime" selected>{-$dic.GraphHisTemporal[0]-}</option>
                      <option value="D.DisasterBeginTime|D.EventId">{-$dic.GraphHisEveTemporal[0]-}</option>
{-foreach name=glev key=k item=i from=$glev-}
                      <option value="D.DisasterBeginTime|D.DisasterGeographyId_{-$k-}">{-$i[0]-} {-$dic.GraphHisGeoTemporal[0]-}</option>
{-/foreach-}
                      <option value="D.DisasterBeginTime|D.CauseId">{-$dic.GraphHisCauTemporal[0]-}</option>
                    </optgroup>
                    <optgroup label="{-#gcomparative#-}">
                      <option value="D.EventId">{-$dic.GraphComByEvents[0]-}</option>
                      <option value="D.CauseId">{-$dic.GraphComByCauses[0]-}</option>
{-foreach name=glev key=k item=i from=$glev-}
                      <option value="D.DisasterGeographyId_{-$k-}">{-$dic.GraphComByGeography[0]-} {-$i[0]-}</option>
{-/foreach-}
                    </optgroup>
                  </select>
                  <br><br>
                  <b onMouseOver="showtip('{-$dic.GraphPeriod[2]-}');">{-$dic.GraphPeriod[0]-}</b><br>
                  <select id="_G+Period" name="_G+Period" class="fixw"
                  		onMouseOver="showtip('{-$dic.GraphPeriod[2]-}');">
                    <option value=""></option>
                    <option value="YEAR" selected>{-#gperannual#-}</option>
                    <option value="YMONTH">{-#gpermonth#-}</option>
                    <option value="YWEEK">{-#gperweek#-}</option>
                    <option value="YDAY">{-#gperday#-}</option>
                  </select>
                  <br>
                  <b onMouseOver="showtip('{-$dic.GraphSeaHistogram[2]-}');">{-#GHISTOANNUAL#-}</b><br>
                  <select id="_G+Stat" name="_G+Stat" class="fixw"
                  		onMouseOver="showtip('{-$dic.GraphSeaHistogram[2]-}');">
                  	<option value=""></option>
                    <option value="DAY">{-#gseaday#-}</option>
                    <option value="WEEK">{-#gseaweek#-}</option>
                    <option value="MONTH">{-#gseamonth#-}</option>
                  </select>
                 </td>
                 <td style="width:240px">
                  <b onMouseOver="showtip('{-$dic.GraphScale[2]-}');">{-$dic.GraphScale[0]-}</b><br>
                  <select id="_G+Scale" name="_G+Scale" class="fixw"
                  		onMouseOver="showtip('{-$dic.GraphScale[2]-}');">
                    <option value="textint" selected>{-#gscalin#-}</option>
                    <option value="textlog">{-#gscalog#-}</option>
                  </select>
                  <br>
                  <b onMouseOver="showtip('{-$dic.GraphFeel[2]-}');">{-$dic.GraphFeel[0]-}</b><br>
                  <select id="_G+Feel" name="_G+Feel" class="fixw"
                  		onMouseOver="showtip('{-$dic.GraphFeel[2]-}');">
                    <option value="2D">{-#gfee2d#-}</option>
                    <option value="3D" selected>{-#gfee3d#-}</option>
                  </select>
                  <br>
                  <b onMouseOver="showtip('{-$dic.GraphKind[2]-}');">{-$dic.GraphKind[0]-}</b><br>
                  <select id="_G+Kind" name="_G+Kind" class="fixw"
                  		onMouseOver="showtip('{-$dic.GraphKind[2]-}');">
                    <option value="BAR" selected>{-#gkndbars#-}</option>
                    <option id="_G+K_line" value="LINE">{-#gkndlines#-}</option>
                    <option id="_G+K_pie" value="PIE" disabled>{-#gkndpie#-}</option>
                  </select>
                  <br>
                  <b onMouseOver="showtip('{-$dic.GraphMode[2]-}');">{-$dic.GraphMode[0]-}</b><br>
                  <select id="_G+Mode" name="_G+Mode" class="fixw"
                  		onMouseOver="showtip('{-$dic.GraphMode[2]-}');">
                    <option value="NORMAL" selected>{-#gmodnormal#-}</option>
                    <option id="_G+M_accu" value="ACCUMULATE">{-#gmodaccumul#-}</option>
                    <option id="_G+M_over" value="OVERCOME" disabled>{-#gmodovercome#-}</option>
                  </select>
                  <br>
                  <b onMouseOver="showtip('{-$dic.GraphShow[2]-}');">{-$dic.GraphShow[0]-}</b><br>
                  <select id="_G+Data" name="_G+Data" class="fixw"
                  		onMouseOver="showtip('{-$dic.GraphShow[2]-}');">
                    <option value="VALUE" >{-#gshwval#-}</option>
                    <option id="_G+D_perc" value="PERCENT" disabled>{-#gshwperce#-}</option>
                    <option id="_G+D_none" value="NONE" selected>{-#gshwnone#-}</option>
                  </select>
                  <br><br>
                  <b onMouseOver="showtip('{-$dic.GraphField[2]-}');">{-$dic.GraphField[0]-}</b><br>
                  <select id="_G+Field" name="_G+Field" size="5" class="fixw"
                  		onMouseOver="showtip('{-$dic.GraphField[2]-}');">
                    <option value="D.DisasterId||" selected>{-$dic.GraphDisasterId_[0]-}</option>
 {-foreach name=ef1 key=k item=i from=$ef1-}
                    <option value="D.{-$k-}|>|0">{-$i[0]-}</option>
                    <option value="D.{-$k-}|=|-1">{-#tauxhave#-} {-$i[0]-}</option>
 {-/foreach-}
 {-foreach name=ef2 key=k item=i from=$ef2-}
                    <option value="D.{-$k-}|>|0">{-$i[0]-}</option>
 {-/foreach-}
 {-foreach name=ef3 key=k item=i from=$ef3-}
                    <option value="D.{-$k-}|>|0">{-$i[0]-}</option>
 {-/foreach-}
 {-foreach name=ef3 key=k item=i from=$sec-}
                    <option value="D.{-$k-}|=|-1">{-#tauxaffect#-} {-$i[0]-}</option>
 {-/foreach-}
 										<option disabled>___</option>
 {-foreach name=eef key=k item=i from=$exteffel-}
  {-if $i[2] == "INTEGER" || $i[2] == "DOUBLE"-}
                    <option value="E.{-$k-}|>|0">{-$i[0]-}</option>
  {-/if-}
 {-/foreach-}
                  </select>
                  <input type="hidden" id="_G+cmd" name="_G+cmd" value="result">
                </td></tr>
              </table>
-->
             </form>
          </div>
        </div>
<!--    END GRAPHIC SECTION  -->
<!--
        SECTION : STADISTIC CONFIGURATION
        ==============================
-->
        <input type="button" id="std-btn" value="{-#bstadistic#-}" ext:qtip="{-#tstadisticmsg#-}"
           class="btn" {-if $isvreg-}style="visibility:hidden;"{-/if-}>
        <div id="std-win" class="x-hidden">
          <div class="x-window-header">{-#bstadistic#-}</div>
          <div id="std-cfg">
            <form id="CS" method="POST">
            	<table border="0" width="100%">
            		<tr>
            			<td>
            				{-#sresxpage#-}
            				<select id="_S+SQL_LIMIT" name="_S+SQL_LIMIT">
            				 <option value="20">20</option>
            				 <option value="50">50</option>
            				 <option value="100" selected>100</option>
            				 <option value="200">200</option>
            				</select>
            			</td>
            			<td>
            				Ver geografía:
            				<select id="_S+showgeo" name="_S+showgeo">
            				 <option value="NAME">Solo Nombre</option>
            				 <option value="CODE">Solo Código</option>
            				 <option value="CODENAME">Código | Nombre</option>
            				</select>
            			</td>
            		</tr>
            	</table>
              <br><br>
              <b>{-#stotallevels#-}</b>
              <br>
              <table>
                <tr valign="top">
                  <td><b>{-$std.StadistFirstlev[0]-}</b><br>
                   <select id="_S+Firstlev" name="_S+Firstlev" size="8" style="width:180px;"
                       onChange="setTotalize('_S+Firstlev', '_S+Secondlev'); setTotalize('_S+Secondlev', '_S+Thirdlev');">
  {-foreach name=glev key=k item=i from=$glev-}
                    <option value="{-$k-}|D.DisasterGeographyId">
                    {-assign var="ln" value=StadistDisasterGeographyId_$k-}{-$std.$ln[0]-}</option>
  {-/foreach-}
                    <option value="|D.EventId">{-$std.StadistEventName[0]-}</option>
                    <option value="YEAR|D.DisasterBeginTime">{-$std.StadistDisasterBeginTime_YEAR[0]-}</option>
                    <option value="MONTH|D.DisasterBeginTime">{-$std.StadistDisasterBeginTime_MONTH[0]-}</option>
                    <option value="|D.CauseId">{-$std.StadistCauseName[0]-}</option>
                   </select>
                  </td>
                  <td><b>{-$std.StadistSeclev[0]-}</b><br>
                   <select id="_S+Secondlev" name="_S+Secondlev" size="8" 
                       onChange="setTotalize('_S+Secondlev', '_S+Thirdlev');" style="width:180px;">
                   </select>
                  </td>
                  <td><b>{-$std.StadistThirlev[0]-}</b><br>
                   <select id="_S+Thirdlev" name="_S+Thirdlev" size="8" style="width:180px;">
                   </select>
                  </td>
                </tr>
              </table>
              <br>
              <table>
                <tr>
                  <td><b>{-#savailfields#-}</b><br>
                   <select id="_S+sel1[]" size="6" style="width:220px;" multiple>
<!--                    <option value="D.{-$key-}|=|-1">{-#tauxhave#-} {-$item[1]-}</option>-->
  {-foreach name=sst1 key=key item=item from=$sst1-}
                    <option value="D.{-$item[0]-}">{-$item[1]-}</option>
  {-/foreach-}
 										<option disabled>---</option>
 {-foreach name=eef key=key item=item from=$exteffel-}
  {-if $item[2] == "INTEGER" || $item[2] == "DOUBLE"-}
                    <option value="E.{-$key-}|>|-1">{-$item[0]-}</option>
  {-/if-}
 {-/foreach-}
                   </select>
                   <br>
                   <input type="button" value="{-#balls#-}" onclick="selectall('_S+sel1[]');" class="line">
                   <input type="button" value="{-#bnone#-}" onclick="selectnone('_S+sel1[]');" class="line">
                  </td>
                  <td align="center" valign="middle" style="width:20px;">
                   <input type="button" value="--&gt;" onclick="moveOptions($('_S+sel1[]'), $('_S+Field[]'));" class="line">
                   <br><br><br>
                   <input type="button" value="&lt;--" onclick="moveOptions($('_S+Field[]'), $('_S+sel1[]'));" class="line">
                  </td>
                  <td><b>{-#sviewfields#-}</b><br>
                   <select id="_S+Field[]" size="6" style="width:220px;" multiple>
  {-foreach name=sst key=key item=item from=$sst-}
                    <option value="D.{-$item[0]-}">{-$item[1]-}</option>
  {-/foreach-}
                   </select><br>
                   <input type="button" value="{-#balls#-}" onclick="selectall('_S+Field[]');" class="line">
                   <input type="button" value="{-#bnone#-}" onclick="selectnone('_S+Field[]');" class="line">
                  </td>
                  <td style="width:20px;" align="center">
                   <input type="button" value="&uArr;" onclick="top('_S+Field[]');" class="line"><br>
                   <input type="button" value="&uarr;" onclick="upone('_S+Field[]');" class="line"><br>
                   <input type="button" value="&darr;" onclick="downone('_S+Field[]');" class="line"><br>
                   <input type="button" value="&dArr;" onclick="bottom('_S+Field[]');" class="line"><br>
                  </td>
                </tr>
              </table>
              <input type="hidden" id="_S+FieldH" name="_S+Field" value="">
              <input type="hidden" id="_S+cmd" name="_S+cmd" value="result">
            </form>
          </div>
        </div>
<!--    END STADISTIC SECTION  -->
       </td>
       <td>
         <span id="frmwait"></span>
         <input id="DCRes" type="hidden" value="">
         <input type="button" class="line" style="width:20px; height:20px; background-image:url(../images/saveicon.png);"
             onClick="if($('DCRes').value != '') saveRes('export');" ext:qtip="{-#bsavemsg#-}">&nbsp;&nbsp;
         <input type="button" class="line" style="width:20px; height:20px; background-image:url(../images/printicon.png);"
             onClick="frames['ifr'].focus(); frames['ifr'].print();" ext:qtip="{-#bprintmsg#-}">&nbsp;&nbsp;
       </td>
     </tr>
   </table>
<!--   SHOW RESULTS  -->
  <div id="querydetails" style="height:40px;" class="dwin"></div>
  <div id="smap" style="position:absolute; left:0px; top:15px;">
  	<iframe name="fmp" id="fmp" frameborder="0" style="height:600px; width:700px;" {-if $ctl_showmap-} 
  src='/cgi-bin/mapserv?map={-$path-}/{-$reg-}/region.map&qlayer=admin00&mode=nquery&searchmap=true&mapsize=500+500&mapext={-$x1-}+{-$y1-}+{-$x2-}+{-$y2-}'{-/if-}>
  	</iframe>
  </div>
  <iframe name="ifr" id="ifr" frameborder="0" style="height:550px; width:960px;" 
  		src="../region.php?r={-$reg-}&cmd=info{-if $isvreg-}&v=true{-/if-}">
  </iframe>
 </div>

<!--
        SECTION : QUERY DESIGN 
        ======================
-->
 <div id="west">
  <!-- BEG DI8 QUERY FORM -->
  <form id="DC" method="POST" target="ifr">
  <input type="hidden" id="_REG" name="_REG" value="{-$reg-}">
 {-if $isvreg-}<input type="hidden" id="_VREG" name="_VREG" value="true">{-/if-}
  <dl class="accordion">
 {-if !$isvreg-}
    <!-- BEGIN GEOGRAPHY SECTION -->
    <!-- Select from Map testing ... 'selectionmap.php' -->
    <dt onClick="showMap(0);">{-#mgeosection#-}</dt>
    <dd>
  {-foreach name=glev key=k item=i from=$glev-}
      <span class="dlgmsg" onMouseOver="showtip('{-$i[1]-}');">{-$i[0]-}</span> |
  {-/foreach-}
      <div style="height: 280px;" class="dwin" ext:qtip="{-#thlpquery#-}">
 {-/if-}
{-/if-}
{-*** Display geography lists.. ***-}
{-if $ctl_glist-}
        <ul id="tree-g{-$reg-}" class="checktree">
 {-foreach name=geol key=key item=item from=$geol-}
          <li id="show-g{-$key-}">
            <input type="checkbox" id="{-$key-}" name="D:DisasterGeographyId[]" value="{-$key-}"
                onClick="setSelMap('{-$item[0]-}', '{-$key-}', this.checked);">
            <label for="{-$key-}">{-$item[1]-}</label>
            <span id="itree{-$key-}"></span>
          </li>
 {-/foreach-}
        </ul>
{-/if-}
{-if $ctl_show-}
 {-if !$isvreg-}
      </div>
      <span id="testid"></span>
      <b onMouseOver="showtip('{-$dis.DisasterSiteNotes[2]-}');">{-$dis.DisasterSiteNotes[0]-}</b><br>
      <textarea id="DisasterSiteNotes" name="D:DisasterSiteNotes" style="width:220px; height: 40px;"
              onFocus="showtip('{-$dis.DisasterSiteNotes[2]-}');"></textarea>
    </dd>
 {-/if-}
    <!-- BEGIN EVENT SECTION -->
    <dt onClick="hideMap();">{-#mevesection#-}</dt>
    <dd>
      <span class="dlgmsg" ext:qtip="{-#thlpquery#-}">{-#tcntclick#-}</span><br>
      <select name="D:EventId[]" multiple style="width: 250px; height: 200px;">
 {-foreach name=eve key=key item=item from=$evepredl-}
        <option value="{-$key-}" onMouseOver="showtip('{-$item[1]-}');">{-$item[0]-}</option>
 {-/foreach-}
        <option disabled>----</option>
 {-foreach name=eve key=key item=item from=$eveuserl-}
        <option value="{-$key-}" onMouseOver="showtip('{-$item[1]-}');">{-$item[0]-}</option>
 {-/foreach-}
      </select>
      <br><br>
      <b onMouseOver="showtip('{-$eve.EventDuration[2]-}');">{-$eve.EventDuration[0]-}</b><br>
      <input id="EventDuration" name="D:EventDuration" type="text" class="line fixw"
          onFocus="showtip('{-$eve.EventDuration[2]-}');">
      <br>
      <b onMouseOver="showtip('{-$eve.EventNotes[2]-}');">{-$eve.EventNotes[0]-}</b><br>
      <textarea id="EventNotes" name="D:EventNotes" style="width:250px; height:40px;"
          onFocus="showtip('{-$eve.EventNotes[2]-}');"></textarea>
    </dd>
    <!-- BEGIN CAUSE SECTION -->
    <dt onClick="hideMap();">{-#mcausection#-}</dt>
    <dd>
      <span class="dlgmsg" ext:qtip="{-#thlpquery#-}">{-#tcntclick#-}</span><br>
      <select name="D:CauseId[]" multiple style="width: 250px; height: 280px;">
 {-foreach name=cau key=key item=item from=$caupredl-}
        <option value="{-$key-}" onMouseOver="showtip('{-$item[1]-}');">{-$item[0]-}</option>
 {-/foreach-}
        <option disabled>----</option>
 {-foreach name=mycau key=key item=item from=$cauuserl-}
        <option value="{-$key-}" onMouseOver="showtip('{-$item[1]-}');">{-$item[0]-}</option>
 {-/foreach-}
      </select>
      <br><br>
      <b onMouseOver="showtip('{-$cau.CauseNotes[2]-}');">{-$cau.CauseNotes[0]-}</b><br>
      <textarea name="D:CauseNotes" style="width:250px; height: 40px;"
          onFocus="showtip('{-$cau.CauseNotes[2]-}');"></textarea>
    </dd>
    <!-- BEGIN EFFECTS SECTION -->
    <dt onClick="hideMap();">{-#meffsection#-}</dt>
    <dd>
      <b>{-#ttitegp#-}</b><br>
      <div style="width: 265px; height: 130px;" class="dwin" ext:qtip="{-#thlpquery#-}">
      <table border=0 cellpadding=0 cellspacing=0>
{-foreach name=ef1 key=key item=item from=$ef1-}
			 <tr><td valign="top">
        <input type="checkbox" onFocus="showtip('{-$item[2]-}');" id="{-$key-}"
            onclick="enadisEff('{-$key-}', this.checked);">
        <label for="{-$key-}" onMouseOver="showtip('{-$item[2]-}');">{-$item[0]-}</label>
        <span id="o{-$key-}" style="display:none">
         <select id="{-$key-}[0]" name="D:{-$key-}[0]" onChange="showeff(this.value, 'x{-$key-}', 'y{-$key-}');" 
              class="small" disabled>
          <option class="small" value="-1">{-#teffhav#-}</option>
          <option class="small" value="0">{-#teffhavnot#-}</option>
          <option class="small" value="-2">{-#teffdontknow#-}</option>
          <option class="small" value=">=">{-#teffmajor#-}</option>
          <option class="small" value="<=">{-#teffminor#-}</option>
          <option class="small" value="=">{-#teffequal#-}</option>
          <option class="small" value="-3">{-#teffbetween#-}</option>
         </select>
         <span id="x{-$key-}" style="display:none"><br>
          <input type="text" id="{-$key-}[1]" name="D:{-$key-}[1]" size="3" value="1" class="line">
         </span>
         <span id="y{-$key-}" style="display:none">
          {-#tand#-} <input type="text" id="{-$key-}[2]" name="D:{-$key-}[2]" size="3" value="10" class="line">
         </span>
         <select id="{-$key-}[3]" id="{-$key-}[3]" name="D:{-$key-}[3]" class="small">
          <option class="small" value="AND" checked>{-#tand#-}</option>
          <option class="small" value="OR">{-#tor#-}</option>
         </select>
        </span>
       </td></tr>
{-/foreach-}
			</table>
      </div><br>
      <!-- SECTORS -->
      <b>{-#ttiteis#-}</b><br>
      <div style="width: 265px; height: 50px;" class="dwin">
      <table border=0 cellpadding=0 cellspacing=0>
{-foreach name=sec key=key item=item from=$sec-}
			 <tr><td valign="top">
        <input type="checkbox" onFocus="showtip('{-$item[2]-}');" id="{-$key-}"
        	onclick="{-foreach name=sc2 key=k item=i from=$item[3]-}enadisEff('{-$k-}', this.checked); {-/foreach-}enadisEff('{-$key-}', this.checked);">
        <label for="{-$key-}" onMouseOver="showtip('{-$item[2]-}');">{-$item[0]-}</label>
        <span id="o{-$key-}" style="display:none">
         <select id="{-$key-}[0]" name="D:{-$key-}[0]" class="small" disabled>
          <option class="small" value=""></option>
          <option class="small" value="-1">{-#teffhav#-}</option>
          <option class="small" value="0">{-#teffhavnot#-}</option>
          <option class="small" value="-2">{-#teffdontknow#-}</option>
         </select>
         <select id="{-$key-}[3]" id="{-$key-}[3]" name="D:{-$key-}[3]" class="small">
          <option class="small" value="AND" checked>{-#tand#-}</option>
          <option class="small" value="OR">{-#tor#-}</option>
         </select>
 {-foreach name=sc2 key=k item=i from=$item[3]-}
         <span id="o{-$k-}" style="display:none">
          <br>{-$i-}
          <select id="{-$k-}[0]" name="D:{-$k-}[0]" onChange="showeff(this.value, 'x{-$k-}', 'y{-$k-}');" 
              class="small" disabled>
           <option class="small" value=""></option>
           <option class="small" value=">=">{-#teffmajor#-}</option>
           <option class="small" value="<=">{-#teffminor#-}</option>
           <option class="small" value="=">{-#teffequal#-}</option>
           <option class="small" value="-3">{-#teffbetween#-}</option>
          </select>
          <span id="x{-$k-}" style="display:none">
           <input type="text" id="{-$k-}[1]" name="D:{-$k-}[1]" size="3" value="1" class="line">
          </span>
          <span id="y{-$k-}" style="display:none">
           {-#tand#-} <input type="text" id="{-$k-}[2]" name="D:{-$k-}[2]" size="3" value="10" class="line">
          </span>
          <select id="{-$k-}[3]" id="{-$k-}[3]" name="D:{-$k-}[3]" class="small">
           <option class="small" value="AND" checked>{-#tand#-}</option>
           <option class="small" value="OR">{-#tor#-}</option>
          </select><br>
         </span>
 {-/foreach-}
        </span>
       </td></tr>
{-/foreach-}
			</table>
  		</div><br>
      <b>{-#ttitloss#-}</b><br>
{-foreach name=ef3 key=k item=i from=$ef3-}
			<input type="checkbox" onFocus="showtip('{-$i[2]-}');" id="{-$k-}"
            onclick="enadisEff('{-$k-}', this.checked);">
      <label for="{-$k-}" onMouseOver="showtip('{-$i[2]-}');">{-$i[0]-}</label>
      <span id="o{-$k-}" style="display:none">
      	<select id="{-$k-}[0]" name="D:{-$k-}[0]" onChange="showeff(this.value, 'x{-$k-}', 'y{-$k-}');" 
						class="small" disabled>
					<option class="small" value=""></option>
					<option class="small" value=">=">{-#teffmajor#-}</option>
          <option class="small" value="<=">{-#teffminor#-}</option>
          <option class="small" value="=">{-#teffequal#-}</option>
          <option class="small" value="-3">{-#teffbetween#-}</option>
        </select>
        <span id="x{-$k-}" style="display:none"><br>
					<input type="text" id="{-$k-}[1]" name="D:{-$k-}[1]" size="5" value="1" class="line">
				</span>
				<span id="y{-$k-}" style="display:none">
					{-#tand#-} <input type="text" id="{-$k-}[2]" name="D:{-$k-}[2]" size="5" value="10" class="line">
				</span>
				<select id="{-$key-}[3]" id="{-$key-}[3]" name="D:{-$key-}[3]" class="small">
					<option class="small" value="AND" checked>{-#tand#-}</option>
					<option class="small" value="OR">{-#tor#-}</option>
				</select>
			</span><br>
{-/foreach-}
{-foreach name=ef4 key=k item=i from=$ef4-}
      <b onMouseOver="showtip('{-$i[2]-}');">{-$i[0]-}</b><br>
      <input type="text" id="{-$k-}" name="D:{-$k-}" class="fixw line"
          onFocus="showtip('{-$i[2]-}');"><br>
{-/foreach-}
    </dd>
    <!-- BEGIN EXTRAEFFECTS SECTION
    <dt>{-#mextsection#-}</dt>
    <dd>
      <div style="width: 235px; height: 300px;" class="dwin" ext:qtip="{-#thlpquery#-}">
      <table border=0 cellpadding=0 cellspacing=0>
{-foreach name=eef key=key item=item from=$exteffel-}
			 <tr><td valign="top">
 {-if $item[2] == "INTEGER" || $item[2] == "DOUBLE"-}
        <input type="checkbox" onFocus="showtip('{-$item[1]-}');" id="{-$key-}"
            onclick="enadisEff('{-$key-}', this.checked);">
        <label for="{-$key-}" onMouseOver="showtip('{-$item[1]-}');">{-$item[0]-}</label>
        <span id="o{-$key-}" style="display:none">
      	 <select id="{-$key-}[0]" name="E:{-$key-}[0]" onChange="showeff(this.value, 'x{-$key-}', 'y{-$key-}');" 
						class="small" disabled>
					<option class="small" value=""></option>
					<option class="small" value=">=">{-#teffmajor#-}</option>
          <option class="small" value="<=">{-#teffminor#-}</option>
          <option class="small" value="=">{-#teffequal#-}</option>
          <option class="small" value="-3">{-#teffbetween#-}</option>
         </select>
         <span id="x{-$key-}" style="display:none"><br>
          <input type="text" id="{-$key-}[1]" name="E:{-$key-}[1]" size="3" value="1" class="line">
         </span>
         <span id="y{-$key-}" style="display:none">
          {-#tand#-} <input type="text" id="{-$key-}[2]" name="E:{-$key-}[2]" size="3" value="10" class="line">
         </span>
         <select id="{-$key-}[3]" id="{-$key-}[3]" name="E:{-$key-}[3]" class="small">
          <option class="small" value="AND" checked>{-#tand#-}</option>
          <option class="small" value="OR">{-#tor#-}</option>
         </select>
        </span>
 {-/if-}
 {-if $item[2] == "TEXT"-}
 				{-$item[0]-}<br>
 				<input type="text" id="{-$key-}" name="E:{-$key-}" style="width: 220px;" class="line"
 						onFocus="showtip('{-$item[1]-}');"></input><br>
 {-/if-}
       </td></tr>
{-/foreach-}
			</table>
      </div><br>
    </dd>-->
    <!-- BEGIN DATETIME SECTION -->
    <dt onClick="hideMap();">{-#mdcsection#-}</dt>
    <dd class="default">
      <div style="height: 360px;">
        <b onMouseOver="showtip('{-$dis.DisasterBeginTime[2]-}');">{-#tdate#-}</b>
        <span class="dlgmsg">{-#tdateformat#-}</span>
        <br>
        <table border="0">
          <tr>
            <td><b>{-#ttitsince#-}:</b></td>
            <td><input type="text" id="iniyear" name="D:DisasterBeginTime[]" size=4 maxlength=4 
            				class="line" value="{-$yini-}">
                <input type="text" id="inimonth" name="D:DisasterBeginTime[]" size=2 maxlength=2 class="line">
                <input type="text" id="iniday" name="D:DisasterBeginTime[]" size=2 maxlength=2 class="line">
            </td>
          </tr>
          <tr>
            <td><b>{-#ttituntil#-}:</b></td>
            <td><input type="text" id="endyear" name="D:DisasterEndTime[]" size=4 maxlength=4 
            				class="line" value="{-$yend-}">
                <input type="text" id="endmonth" name="D:DisasterEndTime[]" size=2 maxlength=2 class="line">
                <input type="text" id="endday" name="D:DisasterEndTime[]" size=2 maxlength=2 class="line">
            </td>
          </tr>
        </table>
        <br>
         <b onMouseOver="showtip('{-$dis.DisasterSource[2]-}');">{-$dis.DisasterSource[0]-}</b><br>
         <textarea id="DisasterSource" name="D:DisasterSource" style="width:220px; height: 40px;"
              onFocus="showtip('{-$dis.DisasterSource[2]-}');"></textarea>
  {-if $ctl_user-}
        <br><br>
        <b onMouseOver="showtip('');">{-#tdcstatus#-}</b>
        <br>
        <select name="D:RecordStatus[]" multiple class="fixw">
          <option value="PUBLISHED" selected>{-#tdcpublished#-}</option>
          <option value="READY" selected>{-#tdcready#-}</option>
          <option value="DRAFT">{-#tdcdraft#-}</option>
          <option value="TRASH">{-#tdctrash#-}</option>
        </select>
  {-else-}
        <input type="hidden" name="D:RecordStatus" value="PUBLISHED">
  {-/if-}
        <br>
        <b onMouseOver="showtip('{-#tserialmsg#-}');">{-#tserial#-}</b>
        <input type="radio" name="D:DisasterSerial_" value="" checked>{-#tinclude#-} &nbsp;&nbsp;&nbsp;
        <input type="radio" name="D:DisasterSerial_" value="NOT">{-#texclude#-}<br>
        <input type="text" name="D:DisasterSerial" class="line fixw">
      </div>
    </dd>
    <!-- END DATETIME SECTION -->
  </dl>
  </form>
 </div> <!-- id = west-->
 <!-- END DI8 QUERY FORM -->
 
 <!-- BEG HELP SECTION -->
 <div id="south">
  <textarea id="_DIDesc" wrap="hard" class="hlp" readonly style="width:80%; height:30px;">{-#tdescinfo#-}</textarea>
  <a href="javascript:void(null)" onClick="runWin('../doc/?m=metguide', 'doc');"
  	class="dlgmsg" style="font-size: 8pt;">{-#hmoreinfo#-}</a>
 </div>
 <!-- END HELP SECTION -->
</body>
</html>
{-/if-}
