{-config_load file="$lg.conf" section="di_doc"-}
{-**** INDEX FRAME SECTION ****-}
{-if $ctl_page == 'index'-}
 {-if $ctl_module == 'start'-}
    <h2>Bienvenido a DesInventar</h2>
    Acceso a usuarios: <br />
    Buscar bases de datos: <br />
    Documentaci&oacute;n<br />
 {-elseif $ctl_module == 'metguide'-}
    <h3>{-#ttitle#-}</h3>
    <li><a href="?m=MetGuide&p=intro" target="Contentframe">{-#tintro#-}</a></li>
    <li><a href="?m=MetGuide&p=whatis" target="Contentframe">{-#twhatis#-}</a></li>
    <li><a href="?m=MetGuide&p=aboutdesinv" target="Contentframe">{-#tgenpres#-}</a></li>
    <li><a href="?m=MetGuide&p=regioninfo" target="Contentframe">{-#tregion#-}</a></li>
    <li><a href="?m=MetGuide&p=geography" target="Contentframe">{-#tgeography#-}</a></li>
    <li><a href="?m=MetGuide&p=events" target="Contentframe">{-#tevents#-}</a></li>
    <li><a href="?m=MetGuide&p=causes" target="Contentframe">{-#tcauses#-}</a></li>
    <li><a href="?m=MetGuide&p=extraeffects" target="Contentframe">{-#textraeff#-}</a></li>
    <li><a href="?m=MetGuide&p=datacards" target="Contentframe">{-#tdatacards#-}</a></li>
    <li><a href="?m=MetGuide&p=references" target="Contentframe">{-#treferences#-}</a></li>
 {-else-}
    <h3>{-#thlptitle#-}</h3>
    <li><a href="?m=DesInventarInfo&p=intro" target="Contentframe">{-#thlpintro#-}</a></li>
    <li><a href="?m=DesInventarInfo&p=portal" target="Contentframe">{-#thlpportal#-}</li>
    <ul>
     <li><a href="portal.htm" target="Contentframe">{-#thlppordemo#-}</li>
    </ul>
    <br />
    <li><a href="?m=DesInventarInfo&p=moddi" target="Contentframe">{-#thlpdimod#-}</li>
    <ul>
     <li><a href="region.htm" target="Contentframe">{-#thlpdemreg#-}</li>
     <li><a href="geografia.htm" target="Contentframe">{-#thlpdemgeo#-}</li>
     <li><a href="eventos.htm" target="Contentframe">{-#thlpdemeve#-}</li>
     <li><a href="causas.htm" target="Contentframe">{-#thlpdemcau#-}</li>
     <li><a href="fichas1.htm" target="Contentframe">{-#thlpdemdc#-}</li>
    </ul>
    <br />
    <li><a href="?m=DesInventarInfo&p=moddc" target="Contentframe">{-#thlpdcmod#-}</li>
 {-/if-}
{-**** MAIN FRAME SECTION ****-}
{-elseif $ctl_page == 'main'-}
<html>
    <head>
    </head>
    <frameset cols="25%,75%" title="">
        <frame src="?m={-$ctl_module-}&p=index" name="Indexframe">
        <frame src="?m={-$ctl_module-}&p=intro" name="Contentframe">
        <noframes>INDEX</noframes>
    </frameset>
</html>
{-else-}
 {-if $title-}
    <h4>{-$pagetitle-}</h4>
 {-/if-}
    <p class="justify"><i>{-$pagedesc-}</i></p>
    <p class="justify">{-$pagefull-}</p>
    <hr />
 {-foreach name=eff key=key item=item from=$eff-}
 <b>{-$item[0]-}</b><br />{-$item[2]-}<br /><hr />
 {-/foreach-}
 {-foreach name=sec key=key item=item from=$sec-}
 <b>{-$item[0]-}</b><br />{-$item[2]-}<br /><hr />
 {-/foreach-}
 <br />
 {-foreach name=eve key=key item=item from=$eve-}
 <b>{-$item[0]-}</b><br />{-$item[1]-}<br /><hr />
 {-/foreach-}
 <br />
 {-foreach name=eve key=key item=item from=$cau-}
 <b>{-$item[0]-}</b><br />{-$item[1]-}<br /><hr />
 {-/foreach-}
{-/if-}
