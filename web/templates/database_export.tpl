{-config_load file="$lg.conf" section="grpDatabaseExport"-}
<div id="divDatabaseExportWin" class="x-hidden">
  <div class="x-window-header">
    {-#msgDatabaseExportTitle#-}
  </div>
  <div id="divDatabaseExportContent" style="margin:10px;">
    <h4>
      <span id="txtDatabaseExportRegionLabel"></span>
    </h4>
    <br />
    <span id="divDatabaseExportProgress" class="clsDatabaseExport" style="display:none;">
      {-#msgDatabaseExportWait#-} &nbsp;&nbsp;<br />
    </span>
    <img id="imgDatabaseExportWait" alt="" src="{-$desinventarURL-}/images/loading.gif" style="display:none;" />
    <span id="divDatabaseExportResults" class="clsDatabaseExport" style="display:none;">
      {-#msgDatabaseExportComplete#-}
    </span>
    <span id="divDatabaseExportError" class="clsDatabaseExport" style="display:none;">
      {-#msgDatabaseExportError#-}
    </span>
    <br />
  </div>
</div>
<div style="display:none;">
  <span id="msgDatabaseExportButtonSend">{-#msgDatabaseExportButtonSend#-}</span>
  <span id="msgDatabaseExportButtonClose">{-#msgDatabaseExportButtonClose#-}</span>
  <input type="hidden" id="fldDatabaseExportSave"  value="1" />
  <input type="hidden" id="fldDatabaseExportImage" value="{-$desinventarURL-}/images/loading.gif" />
</div>
