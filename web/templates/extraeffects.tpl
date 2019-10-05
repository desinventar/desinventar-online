{-config_load file="$lg.conf" section="grpAdminExtraEffects"-}
{-** EXTRAEFFECTS: Interface to Edit ExtraEffects.. **-}
{-if $ctl_admineef-}
<!-- FIELDS -->
    <b class="show-help" data-tooltip="{-$dic.DBExtraEffect[2]-}">{-$dic.DBExtraEffect[0]-}</b><br />
    <div class="dwin" style="width:600px; height:120px;">
        <table class="width100 grid database-admin-eefield-list">
            <thead>
                <tr>
                    <td class="header show-help" data-tooltip="{-$dic.DBEEFieldLabel[2]-}">
                        <b>{-$dic.DBEEFieldLabel[0]-}</b></td>
                    <td class="header show-help" data-tooltip="{-$dic.DBEEFieldDesc[2]-}">
                        <b>{-$dic.DBEEFieldDesc[0]-}</b></td>
                </tr>
            </thead>
            <tbody id="lst_eef">
{-/if-}
{-if $ctl_eeflist-}
 {-foreach name=eef key=key item=item from=$eef-}
                <tr class="extra-effect {-if ($smarty.foreach.eef.iteration - 1) % 2 == 0-}normal{-else-}under{-/if-}"
                  data-id="{-$item['id']-}"
                  data-name="{-$item['name']-}"
                  data-description="{-$item['description']-}"
                  data-type="{-$item['type']-}"
                  data-size="{-$item['size']-}"
                  data-active="{-$item['isActive']-}"
                  data-public="{-$item['isPublic']-}"
                >
                  <td>{-$item['name']-}</td>
                  <td>{-$item['description']-}</td>
                </tr>
 {-/foreach-}
{-/if-}
{-if $ctl_admineef-}
            </tbody>
        </table>
    </div>
    <br /><br />
    <a class="button" id="btnEEFieldAdd"><span>{-#baddoption#-}</span></a>
    <br />
    <div id="divEEFieldStatusMsg">
        <span class="msgEEFieldStatus" id="msgEEFieldStatusOk"    style="display:none;">{-#msgupdeef#-}</span>
        <span class="msgEEFieldStatus" id="msgEEFieldStatusError" style="display:none;">{-#terror#-}[{-$updstateef-}]: {-#errupdeef#-}</span>
        <br />
    </div>
    <br /><br />
    <div id="extraeffaddsect" style="display:none; width:600px;">
        <form name="eeffrm" id="frmEEFieldEdit" method="post">
            {-$dic.DBEEFieldLabel[0]-}<b style="color:darkred;">*</b><br />
            <input type="text" id="EEFieldLabel" name="EEField[EEFieldLabel]" class="line clsValidateField show-help" style="width:500px;"
                tabindex="1" data-tooltip="{-$dic.DBEEFieldLabel[2]-}" />
            <br /><br />
            {-$dic.DBEEFieldDesc[0]-}<b style="color:darkred;">*</b><br />
            <textarea id="EEFieldDesc" name="EEField[EEFieldDesc]" style="width:500px;" class="clsValidateField show-help"
                tabindex="2" data-tooltip="{-$dic.DBEEFieldDesc[2]-}"></textarea>
            <br /><br />
            {-$dic.DBEEFieldType[0]-}<b style="color:darkred;">*</b><br />
            <select id="EEFieldType" name="EEField[EEFieldType]" class="line clsValidateField show-help" style="width:500px;"
                tabindex="3" data-tooltip="{-$dic.DBEEFieldType[2]-}">
                <option value=""></option>
                <option value="INTEGER">{-#typeinteger#-}</option>
                <option value="CURRENCY">{-#typefloat#-}</option>
                <option value="DATE">{-#typedate#-}</option>
                <option value="TEXT">{-#typetext#-}</option>
            </select>
            <br /><br />
            {-$dic.DBEEFieldActive[0]-}
            <input type="checkbox" id="EEFieldActive" name="EEField[EEFieldActive]" class="show-help"
                tabindex="4" data-tooltip="{-$dic.DBEEFieldActive[2]-}" />
            <br /><br />
            {-$dic.DBEEFieldPublic[0]-}
            <input type="checkbox" id="EEFieldPublic" name="EEField[EEFieldPublic]" class="show-help"
                tabindex="5" data-tooltip="{-$dic.DBEEFieldPublic[2]-}" />
            <br /> <br />
            <p class="center" style="width:500px;">
                <input id="RegionId" name="RegionId" type="hidden" value="{-$reg-}" />
                <input id="EEFieldSize" name="EEField[EEFieldSize]" value="100" type="hidden" />
                <input id="EEFieldId" name="EEField[EEFieldId]" type="hidden" />
                <input id="EEFieldCmd" name="cmd" type="hidden" />
                <input type="submit" value="{-#bsave#-}"   class="line" id="btnSave" tabindex="6" />
                <input type="reset"  value="{-#bcancel#-}" class="line" id="btnCancel" tabindex="7" id="btnEEFieldReset" />
            </p>
        </form>
    </div>
{-/if-}

{-** INFO AND ERRORS MESSAGES **-}
{-if $ctl_msgupdeef-}
 {-#msgupdeef#-}
{-elseif $ctl_errupdeef-}
 {-#terror#-}[{-$updstateef-}]: {-#errupdeef#-}
{-/if-}
