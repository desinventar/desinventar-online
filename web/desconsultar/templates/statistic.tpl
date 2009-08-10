{-config_load file=`$lg`.conf section="dc_statistic"-}
{-if $ctl_showres-}
<?xml version="1.0" encoding="UTF-8" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8; no-cache" />
	<title>{-#ttitle#-} -{-$regname-}-</title>
	<link rel="stylesheet" href="../css/desinventar.css" type="text/css"/>
	<script type="text/javascript" src="../include/prototype.js"></script>
	<script type="text/javascript" src="../include/diadmin.js.php"></script>
	<script type="text/javascript">
	window.onload = function() {
		var qrydet = parent.document.getElementById('querydetails');
			var qdet = "=> ";
{-foreach key=k item=i from=$qdet-}
 {-if $k == "GEO"-}qdet += "<b>{-#geo#-}:</b> {-$i-}";{-/if-}
 {-if $k == "EVE"-}qdet += "<b>{-#eve#-}:</b> {-$i-}";{-/if-}
 {-if $k == "CAU"-}qdet += "<b>{-#cau#-}:</b> {-$i-}";{-/if-}
 {-if $k == "EFF"-}qdet += "<b>{-#eff#-}:</b> {-$i-}";{-/if-}
 {-if $k == "BEG"-}qdet += "<b>{-#beg#-}:</b> {-$i-}";{-/if-}
 {-if $k == "END"-}qdet += "<b>{-#end#-}:</b> {-$i-}";{-/if-}
 {-if $k == "SOU"-}qdet += "<b>{-#sou#-}:</b> {-$i-}";{-/if-}
 {-if $k == "SER"-}qdet += "<b>{-#ser#-}:</b> {-$i-}";{-/if-}
{-/foreach-}
			qrydet.innerHTML = qdet;
	}
 </script>
 </head>
 <body>
	<table width="920px" class="grid">
	 <tr>
	  <td>
	  	{-#tpage#-}
	  	<input type="text" id="pp" size="2" value="1" class="line"
				onKeyDown="if(event.keyCode==13){ mod='std'; updateList('lst_dis', 'statistic.php', 'r={-$reg-}&page='+ this.value +'&rxp={-$rxp-}&sql={-$sql-}&fld={-$fld-}&geo={-$geo-}');}"
				onkeypress="return blockChars(event, this.value, 'integer:');">
			&nbsp; {-#tnumof#-} &nbsp;
			<a href="javascript:void(null)" 
				onclick="mod='std'; updateList('lst_dis', 'statistic.php', 'r={-$reg-}&page={-$last-}&rxp={-$rxp-}&sql={-$sql-}&fld={-$fld-}&geo={-$geo-}');">{-$last-}</a>
		</td>
		<td align="center">
			<span id="stdstatusmsg" class="dlgmsg"></span>
		</td>
		<td align="right">
			{-#tsumnum#-}: {-$cou-} | {-#trepnum#-}: {-$tot-}
		</td>
	 </tr>
	</table>
  <table width="930px" height="95%" class="col">
	 <thead>
	  <tr>
	 	 <th class="header">{-#trow#-}</th>
	{-foreach name=sel key=key item=item from=$sel-}
 {-strip-}
     <th class="header">
     <a href="javascript:void(null)" onclick="mod='std'; updateList('lst_dis', 'statistic.php', 
   				'r={-$reg-}&page='+ $('pp').value +'&rxp={-$rxp-}&sql={-$sql-}&fld={-$fld-}&ord={-$item-}&geo={-$geo-}');">
{-if $item =="DisasterId_"-} {-#trepnum#-} {-elseif $item != "DisasterId"-} {-$dk.$item-} {-/if-}
     </a>
     </th>
 {-/strip-}
	{-/foreach-}
    </tr>
    <tr>
     <th style="border: thin solid;">{-#ttotals#-}</th>
	{-foreach name=sel key=key item=item from=$sel-}
 {-strip-}
  {-if $item != "DisasterId"-}
	   <th style="border: thin solid;">
   {-if $item != $gp[0] && $item != $gp[1] && $item != $gp[2]-}
   	 {-$dlt.$item-}
   {-/if-}</th>
  {-/if-}
 {-/strip-}
	{-/foreach-}
    </tr>
	 </thead>
	 <tbody id="lst_dis">
{-/if-}
{-*** SHOW RESULT LIST: PAGING ***-}
{-if $ctl_dislist-}
{-foreach name=dl key=key item=item from=$dislist-}
    <tr class="{-if ($smarty.foreach.dl.iteration - 1) % 2 == 0-}normal{-else-}under{-/if-}" 
    	onClick="Element.addClassName(this, 'highlight');" ondblClick="Element.removeClassName(this, 'highlight');">
     <td>{-$offset+$smarty.foreach.dl.iteration-}</td>
 {-strip-}{-foreach name=sel key=k item=i from=$sel-}
  {-if $i != "DisasterId"-}<td>{-$item[$i]-}</td>{-/if-}
 {-/foreach-}{-/strip-}
    </tr>
{-/foreach-}
{-/if-}
{-if $ctl_showres-}
	 </tbody>
	</table>
	<!--{-$sqt-}-->
 </body>
</html>
{-/if-}
