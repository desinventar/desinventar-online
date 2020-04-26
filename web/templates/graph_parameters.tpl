{-config_load file="$lg.conf" section="grpGraphParams"-}
<!-- BEGIN GRAPHIC PARAMETERS -->
<div id="divGraphParameters" class="x-hidden">
  <span id="msgViewGraphButtonClear" style="display:none;">{-#tclear#-}</span>
  <span id="msgViewGraphButtonSend"  style="display:none;">{-#tsend#-}</span>
  <span id="msgViewGraphButtonClose" style="display:none;">{-#tclose#-}</span>
  <div class="x-window-header">
    {-#msgViewGraph#-}
  </div>
  <div id="grp-cfg" class="ViewGraphParams mainblock">
    <form id="frmGraphParams" method="post" action="#">
      <table class="conf">
      <tr class="top">
        <td colspan=3 class="center">
          <b>{-#gopttitle#-}</b><input type="text" name="prmGraph[Title]" class="line fixw" />
          <!--<b>{-#goptsubtit#-}</b><br />-->
        </td>
      </tr>
      <tr class="top">
        <td id="tdGraphParamAxis1" class="right">
          <u>{-#gveraxis#-} 1:</u><br />
          <b><span data-helptext="{-$dic.GraphField[2]-}">{-$dic.GraphField[0]-}</span></b><br />
          <input id="prmGraphFieldLabel0" name="prmGraph[FieldLabel][0]" type="hidden" value="" />
          <select class="line" id="prmGraphField0" name="prmGraph[Field][0]" data-helptext="{-$dic.GraphField[2]-}">
            <option value="D.DisasterId||" selected="selected">{-$dic.GraphDisasterId_[0]-}</option>
          {-foreach name=ef1 key=k item=i from=$ef1-}
            <option value="D.{-$k-}Q|>|-1">{-$i[0]-}</option>
            <option value="D.{-$k-}|=|-1">{-#tauxhave#-} {-$i[0]-}</option>
          {-/foreach-}
          {-foreach name=ef2 key=k item=i from=$ef2-}
            <option value="D.{-$k-}|>|-1">{-$i[0]-}</option>
          {-/foreach-}
          {-foreach name=ef3 key=k item=i from=$ef3-}
            <option value="D.{-$k-}|>|-1">{-$i[0]-}</option>
          {-/foreach-}
          {-foreach name=sec key=k item=i from=$sec-}
            <option value="D.{-$k-}|=|-1">{-#tauxaffect#-} {-$i[0]-}</option>
          {-/foreach-}
            <option disabled>___</option>
          {-foreach name=eef item=i from=$EEFieldList-}
            {-if $i['type'] == "INTEGER" || $i['type'] == "DOUBLE"-}
            <option value="E.{-$i['id']-}|>|-1">{-$i['name']-}</option>
            {-/if-}
          {-/foreach-}
          </select>
          <br />
          <b data-helptext="{-$dic.GraphScale[2]-}">{-$dic.GraphScale[0]-}</b><br />
          <select id="prmGraphScale0" name="prmGraph[Scale][0]" data-helptext="{-$dic.GraphScale[2]-}" class="line">
            <option value="textint" selected>{-#gscalin#-}</option>
            <option value="textlog">{-#gscalog#-}</option>
          </select>
          <br />
          <b data-helptext="{-$dic.GraphShow[2]-}">{-$dic.GraphShow[0]-}</b><br />
          <select id="prmGraphData0" name="prmGraph[Data][0]" data-helptext="{-$dic.GraphShow[2]-}" class="line">
            <option value="VALUE">{-#gshwval#-}</option>
            <option id="_G+D_perc" value="PERCENT" disabled>{-#gshwperce#-}</option>
            <option id="_G+D_none" value="NONE" selected>{-#gshwnone#-}</option>
          </select>
          <br />
          <b data-helptext="{-$dic.GraphMode[2]-}">{-$dic.GraphMode[0]-}</b><br/>
          <select id="prmGraphMode0" name="prmGraph[Mode][0]" data-helptext="{-$dic.GraphMode[2]-}" class="line">
            <option id="prmGraphModeNormal0"      value="NORMAL" selected>{-#gmodnormal#-}</option>
            <option id="prmGraphModeCummulative0" value="CUMMULATIVE">{-#gmodaccumul#-}</option>
            <option id="prmGraphModeStacked0"     value="STACKED" disabled>{-#gmodovercome#-}</option>
          </select>
          <br />
          <b>{-#gtendline#-}</b><br/>
          <input class="TendencyLabel0" name="prmGraph[TendencyLabel][0]" type="hidden" value="" />
          <select id="prmGraphTendency0" name="prmGraph[Tendency][0]" class="line">
            <option value="" selected></option>
            <option value="LINREG">{-#glinearreg#-}</option>
          </select>
        </td>
        <td id="tdGraphParamCenter" class="center">
          <table border="1" style="width:100%;height:100%;">
          <tr class="middle">
            <td class="center">
              <select id="prmGraphKind" name="prmGraph[Kind]" size="3"
                data-helptext="{-$dic.GraphKind[2]-}" class="line">
                <option value="BAR" selected>{-#gkndbars#-}</option>
                <option id="_G+K_line" value="LINE">{-#gkndlines#-}</option>
                <option id="_G+K_pie" value="PIE" disabled>{-#gkndpie#-}</option>
              </select>
              <br /><br />
              <select id="prmGraphFeel" name="prmGraph[Feel]" size="2" data-helptext="{-$dic.GraphFeel[2]-}" class="line">
                <option class="2D" value="2D">{-#gfee2d#-}</option>
                <option class="3D" value="3D" selected>{-#gfee3d#-}</option>
              </select>
            </td>
          </tr>
          </table>
        </td>
        <td id="tdGraphParamAxis2">
          <div id="divVerticalAxis2">
            <u>{-#gveraxis#-} 2:</u><br />
            <b data-helptext="{-$dic.GraphField[2]-}">{-$dic.GraphField[0]-}</b><br />
            <input id="prmGraphFieldLabel1" type="hidden" name="prmGraph[FieldLabel][1]" value="" />
            <select id="prmGraphField1" name="prmGraph[Field][1]" size="1" data-helptext="{-$dic.GraphField[2]-}"
               class="line">
              <option value="" selected></option>
              <option value="D.DisasterId||">{-$dic.GraphDisasterId_[0]-}</option>
              {-foreach name=ef1 key=k item=i from=$ef1-}
                <option value="D.{-$k-}Q|>|-1">{-$i[0]-}</option>
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
              {-foreach name=eef key=k item=i from=$EEFieldList-}
                {-if $i['type'] == "INTEGER" || $i['type'] == "DOUBLE"-}
                  <option value="E.{-$i['id']-}|>|-1">{-$i['name']-}</option>
                {-/if-}
              {-/foreach-}
            </select>
            <br />
            <b data-helptext="{-$dic.GraphScale[2]-}">{-$dic.GraphScale[0]-}</b><br />
            <select id="prmGraphScale1" name="prmGraph[Scale][1]" class="disabled line" disabled data-helptext="{-$dic.GraphScale[2]-}">
              <option value="int" selected>{-#gscalin#-}</option>
              <option value="log">{-#gscalog#-}</option>
            </select>
            <br />
            <b data-helptext="{-$dic.GraphShow[2]-}">{-$dic.GraphShow[0]-}</b><br />
            <select id="prmGraphData1" name="prmGraph[Data][1]" class="disabled line" disabled data-helptext="{-$dic.GraphShow[2]-}">
              <option value="VALUE">{-#gshwval#-}</option>
              <option id="_G+D_perc2" value="PERCENT" disabled>{-#gshwperce#-}</option>
              <option id="_G+D_none2" value="NONE" selected>{-#gshwnone#-}</option>
            </select>
            <br />
            <b data-helptext="{-$dic.GraphMode[2]-}">{-$dic.GraphMode[0]-}</b><br />
            <select id="prmGraphMode1" name="prmGraph[Mode][1]" class="disabled line" disabled data-helptext="{-$dic.GraphMode[2]-}">
              <option id="prmGraphModeNormal1"      value="NORMAL" selected>{-#gmodnormal#-}</option>
              <option id="prmGraphModeCummulative1" value="CUMMULATIVE">{-#gmodaccumul#-}</option>
              <option id="prmGraphModeStacked1"     value="STACKED" disabled>{-#gmodovercome#-}</option>
            </select>
          </div>
        </td>
      </tr>
      <tr>
        <td></td>
        <td>
          <table style="height:100%;">
          <tr>
            <td colspan="2" class="center">
              <p><u>{-#ghoraxis#-}:</u></p>
            </td>
          </tr>
          <tr>
            <td>
              <b>{-#ghistogram#-}</b>
            </td>
            <td>
              <span class="HistogramByGeography hidden">{-$dic.GraphHisGeoTemporal[0]-}</span>
              <select class="TypeHistogram line" id="prmGraphTypeHistogram" name="prmGraphTypeHistogram"  data-helptext="{-$dic.GraphType[2]-}">
                <option value="" disabled></option>
                <option value="{-$smarty.const.GRAPH_HISTOGRAM_TEMPORAL-}">{-$dic.GraphHisTemporal[0]-}</option>
                <option value="{-$smarty.const.GRAPH_HISTOGRAM_EVENT-}">{-$dic.GraphHisEveTemporal[0]-}</option>
                <option value="{-$smarty.const.GRAPH_HISTOGRAM_CAUSE-}">{-$dic.GraphHisCauTemporal[0]-}</option>
                <option class="Geolevel" value="100">Level0/Temporal</option>
              </select>
            </td>
          </tr>
          <tr>
            <td>
              <b data-helptext="{-$dic.GraphPeriod[2]-}">{-$dic.GraphPeriod[0]-}</b>
            </td>
            <td>
              <select id="prmGraphPeriod" name="prmGraph[Period]" class="line" data-helptext="{-$dic.GraphPeriod[2]-}">
                <option value=""></option>
                <option value="YEAR" selected>{-#gperannual#-}</option>
                <option value="YMONTH">{-#gpermonth#-}</option>
                <option value="YWEEK">{-#gperweek#-}</option>
                <option value="YDAY">{-#gperday#-}</option>
              </select>
            </td>
          </tr>
          <tr>
            <td colspan="2">
              <b data-helptext="{-$dic.GraphSeaHistogram[2]-}">{-#GHISTOANNUAL#-}</b>
              <select id="prmGraphStat" name="prmGraph[Stat]" class="line" data-helptext="{-$dic.GraphSeaHistogram[2]-}">
                <option value=""></option>
                <option value="DAY">{-#gseaday#-}</option>
                <option value="WEEK">{-#gseaweek#-}</option>
                <option value="MONTH">{-#gseamonth#-}</option>
              </select>
            </td>
          </tr>
          <tr>
            <td>
              <b>{-#gcomparative#-}</b>
            </td>
            <td>
              <span class="ComparativeByGeography hidden">{-$dic.GraphComByGeography[0]-}</span>
              <select class="TypeComparative line" id="prmGraphTypeComparative" name="prmGraphTypeComparative" data-helptext="{-$dic.GraphType[2]-}">
                <option value="" disabled></option>
                <option value="{-$smarty.const.GRAPH_COMPARATIVE_EVENT-}">{-$dic.GraphComByEvents[0]-}</option>
                <option value="{-$smarty.const.GRAPH_COMPARATIVE_CAUSE-}">{-$dic.GraphComByCauses[0]-}</option>
                <option class="Geolevel">Comparative by Geography Level0</option>
              </select>
            </td>
          </tr>
          </table>
        </td>
        <td></td>
      </tr>
      </table>
      <input type="hidden" id="prmGraphCommand"    name="prmGraph[Command]"  value="result" />
      <input type="hidden" id="prmGraphType"       name="prmGraph[Type]"     value="" />
      <input type="hidden" id="prmGraphSubType"    name="prmGraph[SubType]" value="" />
      <input type="hidden" id="prmGraphMonthNames" name="prmGraph[MonthNames]" value="{-#msgMonth_01#-},{-#msgMonth_02#-},{-#msgMonth_03#-},{-#msgMonth_04#-},{-#msgMonth_05#-},{-#msgMonth_06#-},{-#msgMonth_07#-},{-#msgMonth_08#-},{-#msgMonth_09#-},{-#msgMonth_10#-},{-#msgMonth_11#-},{-#msgMonth_12#-}" />
    </form>
  </div>
</div>
