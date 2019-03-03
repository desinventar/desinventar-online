{-*** REGISTER NEW USER - CONTENT SECTION ***-}
{-config_load file="$lg.conf" section="grpMenuUser"-}
<link rel="stylesheet" href="{-$desinventarURL-}/css/desinventar.css?version={-$jsversion-}" type="text/css">
{-include file="jquery.tpl" -}
<script type="text/javascript" src="{-$desinventarURL-}/external/md5.js"></script>
<script type="text/javascript" src="{-$desinventarURL-}/js/common.js?version={-$jsversion-}"></script>
<script type="text/javascript" src="{-$desinventarURL-}/js/user.js?version={-$jsversion-}"></script>
<script type="text/javascript">
  jQuery(document).ready(function() {
    onReadyCommon();
  });
</script>
<div id="divUserChangePasswd">
  <form id="frmUserChangePasswd">
    <table class="grid">
      <tr>
        <td>
          <b style="color:darkred;">{-#toldpassword#-}</b>
        </td>
        <td>
          <input type="password" id="txtUserPasswd" name="User[UserPasswd]" size="25" maxlength="25" class="line" />
        </td>
      </tr>
      <tr>
        <td>
          <b style="color:darkred;">{-#tnewpassword#-}</b>
        </td>
        <td>
          <input type="password" id="txtUserPasswd2" name="User[UserPasswd2]" size="25" maxlength="25" class="line" />
        </td>
      </tr>
      <tr>
        <td>
          <b style="color:darkred;">{-#tnewpassword2#-}</b>
        </td>
        <td>
          <input type="password" id="txtUserPasswd3" name="User[UserPasswd3]" size="25" maxlength="25" class="line" />
        </td>
      </tr>
      <tr>
        <td colspan="2" class="center">
          <input type="hidden" id="txtUserId" value="{-$UserId-}" />
          <input type="submit" id="btnUserEditSubmit" value="{-#bsave#-}" class="line" />
          <input type="reset"  id="btnUserEditCancel" value="{-#bcancel#-}" class="line" />
        </td>
      </tr>
      <tr>
        <td colspan="2" class="center">
          <div id="divUserChangePasswdMsg" style="display:none">
            <span class="status" id="msgEmptyFields">{-#erremptyfields#-}</span>
            <span class="status" id="msgPasswdDoNotMatch">{-#errnomatch#-}</span>
            <span class="status" id="msgInvalidPasswd">{-#errbadpasswd#-}</span>
            <span class="status" id="msgPasswdUpdated">{-#msgupdatesucc#-}</span>
          </div>
        </td>
      </tr>
    </table>
  </form>
</div>
