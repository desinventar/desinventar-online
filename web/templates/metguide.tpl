{-config_load file="$lg.conf" section="di_doc"-}
<!DOCTYPE HTML>
<html>
  <head>
    <meta charset="UTF-8" />
    <title>DesInventar - {-#metguide_Title#-}</title>
    <script type="text/javascript" src="/jquery/jquery.min.js"></script>
    <script type="text/javascript">
      jQuery(document).ready(function() {
        jQuery('a.page').on('click', function(event) {
          jQuery('div.page').hide();
          jQuery('div.page[id="' + jQuery(this).attr('id') + '"]').show();
          event.stopPropagation();
        });
        jQuery('a.page#intro').trigger('click');
      });
    </script>
  </head>
  <body>
    <table width="100%">
      <tr>
        <td class="top" colspan="2">
          <h3 class="center">{-#metguide_Title#-}</h3>
        </td>
      </tr>
      <tr>
        <td valign="top" style="width:180px;">
          <li><a class="page" href="#" id="intro">{-#tintro#-}</a></li>
          <li><a class="page" href="#" id="whatis">{-#twhatis#-}</a></li>
          <li><a class="page" href="#" id="aboutdesinv">{-#tgenpres#-}</a></li>
          <li><a class="page" href="#" id="regioninfo">{-#tregion#-}</a></li>
          <li><a class="page" href="#" id="geography">{-#tgeography#-}</a></li>
          <li><a class="page" href="#" id="events">{-#tevents#-}</a></li>
          <li><a class="page" href="#" id="causes">{-#tcauses#-}</a></li>
          <li><a class="page" href="#" id="extraeffects">{-#textraeff#-}</a></li>
          <li><a class="page" href="#" id="datacards">{-#tdatacards#-}</a></li>
          <li><a class="page" href="#" id="references">{-#treferences#-}</a></li>
        </td>
        <td valign="top">
          {-foreach $metguide as $key => $page-}
            <div class="page" id="{-$key-}" style="display:none;">
              <h4>{-$page.DictTranslation-}</h4>
              <p class="justify"><i>{-$page.DictBasDesc-}</i></p>
              <p class="justify">{-$page.DictFullDesc-}</p>
              {-if $key=='events'-}
                <hr />
                {-foreach $EventListDefault as $EventId => $Event-}
                  <b>{-$Event.EventName-}</b><br />
                  <span>{-$Event.EventDesc-}</span><br />
                  <br />
                {-/foreach-}
              {-/if-}
              {-if $key=='causes'-}
                <hr />
                {-foreach $CauseListDefault as $CauseId => $Cause-}
                  <b>{-$Cause.CauseName-}</b><br />
                  <span>{-$Cause.CauseDesc-}</span><br />
                  <br />
                {-/foreach-}
              {-/if-}
              {-if $key=='datacards'-}
                <hr />
                {-foreach $EffectList as $EffectId => $Effect-}
                  <b>{-$Effect.0-}</b><br />
                  <span>{-$Effect.2-}</span><br />
                  <br />
                {-/foreach-}
              {-/if-}
            </div>
          {-/foreach-}
        </td>
      </tr>
    </table>
  </body>
</html>
