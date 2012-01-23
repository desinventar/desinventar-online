{-config_load file="$lang.conf" section="grpDatabaseFind"-}
<div class="contentBlock" id="divRegionList" class="hidden">
	<span class="databaseListTitle">{-#msgDatabaseFind_Title#-}</span>
	<br />
	<div id="divDatabaseFindList">
		<span class="databaseTitle" id="title_COUNTRY"></span>
		<ul   class="databaseList"  id="list_COUNTRY"><li></li></ul>
		<span class="databaseTitle" id="title_ADMINREGION">{-#msgDatabaseFind_RoleADMINREGION#-}</span>
		<ul   class="databaseList"  id="list_ADMINREGION"><li></li></ul>
		<span class="databaseTitle" id="title_SUPERVISOR">{-#msgDatabaseFind_RoleSUPERVISOR#-}</span>
		<ul   class="databaseList"  id="list_SUPERVISOR"><li></li></ul>
		<span class="databaseTitle" id="title_USER">{-#msgDatabaseFind_RoleUSER#-}</span>
		<ul   class="databaseList"  id="list_USER"><li></li></ul>
		<span class="databaseTitle" id="title_OBSERVER">{-#msgDatabaseFind_RoleOBSERVER#-}</span>
		<ul   class="databaseList"  id="list_OBSERVER"><li></li></ul>
		<span class="databaseTitle" id="title_NONE">{-#msgDatabaseFind_RoleNONE#-}</span>
		<ul   class="databaseList"  id="list_NONE"><li></li></ul>
	</div>
	<div id="divDatabaseFindError" style="display:none;margin:20px;">
		<h3>{-#msgDatabaseFind_NoDatabases#-}</h3>
	</div>
</div>
