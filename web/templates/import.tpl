{-config_load file="$lg.conf" section="grpAdminImport"-}
{-** IMPORT: Interface for import datacards into region. **-}
<?xml version="1.0" encoding="UTF-8" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8; no-cache" />
  {-include file="jquery.tpl"-}
  <script type="text/javascript" src="{-$desinventarURL-}/js/common.js?version={-$jsversion-}"></script>
  <script type="text/javascript" src="{-$desinventarURL-}/js/import.js?version={-$jsversion-}"></script>
  <script type="text/javascript">
    jQuery(document).ready(function() {
      onReadyCommon();
      onReadyImport();
    });
  </script>
</head>
<body>
  <div class="divImport" id="divImportSelectFile">
    {-* Show select CSV file interface *-}
    <p class="fixw">
      <form id="import" method="post" action="import.php" target="importres" enctype="multipart/form-data">
        <input type="hidden" name="_REG" value="{-$RegionId-}" />
        <input type="hidden" name="cmd" value="upload" />
        <input type="hidden" name="diobj" value="5" />
        <input type="file" id="ieff" name="desinv" class="fixw" /> <!--  onChange="sendForm();"-->
        <input type="submit" value="Ok" />
      </form>
    </p>
  </div>
  <div class="divImport" id="divImportSelectColumns">
    <br />
    <iframe name="importres" id="importres" frameborder="1" src="about:blank" style="height:400px; width:830px;"></iframe>
    {-* Show import interface to assign specific fields *-}
    <form method="post" action="import.php">
      <input type="hidden" name="cmd" value="import" />
      <input type="hidden" name="FileName" value="{-$FileName-}" />
      <input type="submit" value="{-#tsend#-}" class="line" />
      <br />
      <table style="font-size: 11px;">
      <tr>
        {-foreach name=fld key=k item=i from=$fld-}
          {-assign var="nxt" value="`$smarty.foreach.fld.iteration+1`"-}
          <td>
            <!--   <input type="checkbox" onclick="enadisField('col{-$smarty.foreach.fld.iteration-}', 'col{-$nxt-}', this.checked);" checked>
            onChange="fillColumn('col{-$smarty.foreach.fld.iteration-}', 'col{-$nxt-}', true);"-->
            <select id="col{-$smarty.foreach.fld.iteration-}" name="col{-$smarty.foreach.fld.iteration-}">
              <option value="{-$k-}">{-$i-}</option>
            </select>
          </td>
        {-/foreach-}
      </tr>
      {-foreach name=csv key=k item=i from=$csv-}
        <tr>
          {-foreach name=cs2 key=ky item=it from=$i-}
            <td>
              {-$it-}
            </td>
          {-/foreach-}
        </tr>
      {-/foreach-}
      </table>
    </form>
  </div>
  <div class="divImport" id="divImportResults">
    {-* Show import results *-}
    <br />
    {-#tfound1#-} {-$msg.ErrorCount-} {-#tfound2#-}<br />
    <table style="font-size:11px;">
      <tr>
        <td>
          {-#tfile#-}
        </td>
        <td>
          Detalles
        </td>
      </tr>
      {-foreach name=res key=key item=it from=$res-}
        {-if $it[0] == "ERROR"-}
          <tr>
            <td bgcolor="red">
              {-$it[1]-}
            </td>
            <td>
              {-$it[3]-} | {-$it[4]-}
            </td>
          </tr>
        {-elseif $it[0] == "WARNING"-}
          <tr>
            <td bgcolor="yellow">
              {-$it[1]-}
            </td>
            <td>
              {-$it[3]-} | {-$it[4]-}
            </td>
          </tr>
        {-else-}
          <tr>
            <td>
              {-$it[0]-}
            </td>
            <td>
              {-$it[2]-}
            </td>
          </tr>
        {-/if-}
      {-/foreach-}
    </table>
  </div>
  <div class="divImport" id="divImportStatus">
    <span class="status" id="msgSuccess">
      <b>{-#tsuccess#-}</b>
    </span>
    <span class="status" id="msgFail">
      <b>{-#tfail#-}</b>
    </span>
    <span class="status" id="msgError">
      {-#tfound1#-} {-#tfound2#-}:<br /><b>{-$error-}..</b><br />
    </span>
  </div>
</body>
</html>
