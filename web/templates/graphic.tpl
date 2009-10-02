{-config_load file=`$lg`.conf section="dc_graphic"-}
{-if $ctl_showres-}
<?xml version="1.0" encoding="UTF-8" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
	<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8; no-cache" />
	<META HTTP-EQUIV="expires" CONTENT="Wed, 09 Aug 2000 08:21:57 GMT">
	<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
	<META HTTP-EQUIV="Cache-Control" CONTENT="no-cache">
	<script type="text/javascript">
	window.onload = function() {
		var qrydet = parent.document.getElementById('querydetails');
		var qdet = "";
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
	<link rel="stylesheet" href="css/desinventar.css" type="text/css"/>
 </head>
 <body>
  <p align="right">{-#trepnum#-}: {-$NumRecords-}</p>
  <img src="{-$image-}">
 </body>
</html>
{-else-}
{-#tnodata#-}
{-/if-}
