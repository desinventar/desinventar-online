{-config_load file="$lg.conf" section="grpDatabaseUpload"-}
<div id="divDatabaseUploadWin" class="x-hidden">
    <div class="x-window-header">
        {-#msgDatabaseUploadTitle#-}
    </div>
    <div id="divDatabaseUploadContent">
        {-include file="database_upload.tpl"-}
    </div>
</div>
<div style="display:none;">
    <span id="msgDatabaseUploadButtonClose">{-#msgDatabaseUploadButtonClose#-}</span>
    <input type="hidden" id="fldDatabaseUploadSave"  value="1" />
    <input type="hidden" id="fldDatabaseUploadImage" value="{-$desinventarURL-}/images/loading.gif" />
</div>
