{-config_load file="$lang.conf" section="grpDatabaseFind"-}
<div class="contentBlock hidden" id="divRegionList">
  <span class="databaseListTitle">{-#msgDatabaseFind_Title#-}</span>
  <br />
  <div id="divDatabaseFindList">
    <span class="databaseTitle" id="title_ADMINREGION">{-#msgDatabaseFind_RoleADMINREGION#-}</span>
    <table class="databaseList"  id="list_ADMINREGION">
      <tr>
        <td class="RegionId hidden">
        </td>
        <td>
          <a class="RegionLink" href=""><span class="RegionLabel"></span></a>
        </td>
        <td class="RegionDelete hidden">
          <a href="">[{-#msgDatabaseFind_Delete#-}]</a>
        </td>
      </tr>
    </table>
    <span class="databaseTitle" id="title_SUPERVISOR">{-#msgDatabaseFind_RoleSUPERVISOR#-}</span>
    <table class="databaseList"  id="list_SUPERVISOR">
      <tr>
        <td class="RegionId hidden">
        </td>
        <td>
          <a class="RegionLink" href=""><span class="RegionLabel"></span></a>
        </td>
        <td class="RegionDelete hidden">
          <a href="">[{-#msgDatabaseFind_Delete#-}]</a>
        </td>
      </tr>
    </table>
    <span class="databaseTitle" id="title_USER">{-#msgDatabaseFind_RoleUSER#-}</span>
    <table class="databaseList"  id="list_USER">
      <tr>
        <td class="RegionId hidden">
        </td>
        <td>
          <a class="RegionLink" href=""><span class="RegionLabel"></span></a>
        </td>
        <td class="RegionDelete hidden">
          <a href="">[{-#msgDatabaseFind_Delete#-}]</a>
        </td>
      </tr>
    </table>
    <span class="databaseTitle" id="title_OBSERVER">{-#msgDatabaseFind_RoleOBSERVER#-}</span>
    <table class="databaseList"  id="list_OBSERVER">
      <tr>
        <td class="RegionId hidden">
        </td>
        <td>
          <a class="RegionLink" href=""><span class="RegionLabel"></span></a>
        </td>
        <td class="RegionDelete hidden">
          <a href="">[{-#msgDatabaseFind_Delete#-}]</a>
        </td>
      </tr>
    </table>
    <span class="databaseTitle" id="title_NONE">{-#msgDatabaseFind_RoleNONE#-}</span>
    <table class="databaseList"  id="list_NONE">
      <tr>
        <td class="RegionId hidden">
        </td>
        <td>
          <a class="RegionLink" href=""><span class="RegionLabel"></span></a>
        </td>
        <td class="RegionDelete hidden">
          <a href="">[{-#msgDatabaseFind_Delete#-}]</a>
        </td>
      </tr>
    </table>
  </div>
  <div id="divDatabaseFindError" style="display:none;margin:20px;">
    <h3>{-#msgDatabaseFind_NoDatabases#-}</h3>
  </div>
</div>
