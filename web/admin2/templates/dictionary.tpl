<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>

<title>DesInventar Web - Dictionary</title>

<style type="text/css">
 body {
  padding: 0; margin: 0;
  font: 80% "Trebuchet MS", verdana, helvetica, arial, sans-serif;
  color: black; background: white;
 }
 input { font-size:12px; }
 div#commentForm {
  margin: 0px 20px 0px 20px; display: none; background-color:#FFFFFF;
 }
 textarea {font-size:12px; }
</style>

<script type="text/javascript" src="ajax.js">
</script>

<script language="JavaScript" type="text/javascript">
/**Ajax Request (Submits the form below through AJAX
 *               and then calls the ajax_response function)
 * @param   object  data   Data Argument to evaluate
 * @param   object  action Action to execute..
 */
function ajax_request(action) {
  if (document.getElementById) {
  grp = document.getElementById('LGName');
  lbl = document.getElementById('LabelName');
  lng = document.getElementById('LangID');
  }
  else if (document.all) {
  grp = document.all['LGName'];
  lbl = document.all['LabelName'];
  lng = document.all['LangID'];
  }
  else if (document.layers) {
  grp = document.layers['LGName'];
  lbl = document.layers['LabelName'];
  lng = document.layers['LangID'];
  }
  var data = grp.options[grp.selectedIndex].value + '|' +
       lbl.options[lbl.selectedIndex].value + '|' +
       lng.options[lng.selectedIndex].value;
  if (lbl.options[lbl.selectedIndex].value != '' &&
      lng.options[lng.selectedIndex].value != '') {
    var submitTo = 'dictionary.php?data=' + data + '&action=' + action ;
    //location.href = submitTo; //uncomment if you need for debugging
    http('POST', submitTo, ajax_response, document.myLabel);
  }
}

/**Ajax Response (Called when ajax data has been retrieved)
 * @param   object  data   Javascript (JSON) data object received
 *                         through ajax call
 */
function ajax_response(data) {
  var form = document.myLabel;
  for(var i=3; i < form.elements.length - 3; i++) {
    if (data != null) {
      eval("value = data." + form.elements[i].id + ";");
      form.elements[i].value = value;
    }
    else {
      form.elements[i].value = '';
    }
  }
}
</script>

<script type="text/javascript" src="../include/DynamicOptionList.js">
</script>
<script language="JavaScript">
  var grouplabel = new DynamicOptionList();
  grouplabel.addDependentFields("LGName","LabelName");

{-foreach name=grp key=key item=item from=$grp-}
  grouplabel.forValue("{-$key-}").addOptions(""
  {-foreach name=item key=key2 item=item2 from=$item-}
   ,"{-$item2-}"
  {-/foreach-}
  );
{-/foreach-}
  grouplabel.selectFirstOption = false;

function toggleLayer(whichLayer) {
  if (document.getElementById) {
    // this is the way the standards work
    var style2 = document.getElementById(whichLayer).style;
    style2.display = style2.display? "":"block";
  }
  else if (document.all) {
    // this is the way old msie versions work
    var style2 = document.all[whichLayer].style;
    style2.display = style2.display? "":"block";
  }
  else if (document.layers) {
    // this is the way nn4 works
    var style2 = document.layers[whichLayer].style;
    style2.display = style2.display? "":"block";
  }
}

</script>

</head>

<body onLoad="initDynamicOptionLists();">

<table border=1>
 <tr>
  <td>
   <table>
  <tr>
   <td style="background-color: #acb386; width: 200px;">
    <p align="center">
    <a class="commentLink" title="insert a new language" href="javascript:toggleLayer('commentForm');">Insert Language</a>
    <div id="commentForm">
     <form action="" method="post">
      Code (ISO967) <input type="text" name="LangID" style="width:30px;"><br>
      Name <input type="text" name="LangName" style="width:100px;"><br>
      English Name <input type="text" name="LangNameEN" style="width:100px;"><br>
      Notes <input type="text" name="LangNotes" style="width:100px;"><br>
      <input type="hidden" name="LangAdmin" value="wwwmngr@desinventar.org">
      <input type="hidden" name="command" value="addlang">
      <input type="submit" value="Accept">
     </form>
    </div>
    </p>

    <form name="myLabel" action="" method="post">
    DicGroup<br>
    <select name="LGName" id="LGName">
    {-foreach name=grp key=key item=item from=$grp-}
      <option value="{-$key-}">{-$key-}</option>
    {-/foreach-}
    </select>
    <br><br>
    DicLabel<br>
    <select name="LabelName" id="LabelName" size=10
        onChange="ajax_request('loadLabel');">
      <script>grouplabel.printOptions("LabelName")</script>
    </select>
    <br>
   </td>
  </tr>
   </table>
  </td>
  <td>
   <table>
    <tr>
   <td style="background-color: #b39b86; ">
    <h3 align="center">DesInventar Dictionary and Traduction Tool</h3>
   </td>
  </tr>
  <tr>
  <td style="background-color: #f5ffbf; padding: 10px;">
  <table border="0">
   <tr>
    <td>Language</td>
    <td>
      <select name="LangID" id="LangID"
        onChange="ajax_request('loadLabel');">
      <option value=""></option>
    {-foreach name=lng key=key item=item from=$lng-}
      <option value="{-$key-}">{-$item[0]-}</option>
    {-/foreach-}
    </select>
    </td>
   </tr>
   <tr>
    <td><acronym title="Asynchronous Javascript And XML">Traduction</acronym></td>
    <td><input type="text" id="DicTranslation" name="DicTranslation" size=30><span></span></td>
   </tr>
   <tr>
    <td>Usage mode</td>
    <td><input type="text" id="DicTechHelp" name="DicTechHelp" size=40></td>
   </tr>
   <tr>
    <td>Description</td>
    <td><textarea id="DicBasDesc" name="DicBasDesc" cols=40></textarea></td>
   </tr>
   <tr>
    <td>Full text</td>
    <td><textarea id="DicFullDesc" name="DicFullDesc" cols=40></textarea></td>
   </tr>
  </table>
  <input type="hidden" name="command" value="addtraduction">
  <input type="submit" value="Accept"> <input type="reset">
  </form>
  </td>
  </tr>
  <tr>
   <td style="text-align:right; background-color: #b39b86;">
    {-$sm-}
   </td>
  </tr>
   </table>
  </td>
 </tr>
</table>


</body>
</html>
