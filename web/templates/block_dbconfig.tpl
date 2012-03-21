	<div id="DBConfig_tabs">
		<ul>
			<li><a class="classDBConfig_tabs" href="#DBConfig_tabs-1"
				cmd="cmdDBInfoEdit" data-url="{-$desinventarURL-}/info.php">{-#mreginfo#-}</a>
			</li>
			<li title="{-#msgGeolevels_Tooltip#-}">
				<a class="classDBConfig_tabs" href="#DBConfig_Geolevels" cmd="">{-#msgDBConfig_Geolevels#-}</a>
			</li>
			<li title="{-#msgGeography_Tooltip#-}">
				<a class="classDBConfig_tabs" href="#DBConfig_Geography" cmd="">{-#mgeography#-}</a>
			</li>
			<li>
				<a class="classDBConfig_tabs" href="#DBConfig_Events" cmd="" >{-#msgDBConfig_Events#-}</a>
			</li>
			<li>
				<a class="classDBConfig_tabs" href="#DBConfig_Causes" cmd="" >{-#mcauses#-}</a>
			</li>
			<li>
				<a class="classDBConfig_tabs" href="#DBConfig_tabs-6" cmd="cmdDBInfoEEField" data-url="{-$desinventarURL-}/extraeffects.php">{-#meeffects#-}</a>
			</li>
			<li>
				<a class="classDBConfig_tabs" href="#DBConfig_Users" cmd="" >{-#msgDBConfig_RolesAndDiffusion#-}</a>
			</li>
		</ul>
		<div id="DBConfig_tabs-1">
			<div class="helptext hidden">
			</div>
			<div class="content">
			</div>
		</div>
		<div id="DBConfig_Geolevels">
			<div class="helptext hidden">{-#msgGeolevels_HelpText#-}</div>
			<div class="content">
				{-include file="database_geolevels.tpl"-}
			</div>
		</div>
		<div id="DBConfig_Geography">
			<div class="helptext hidden">{-#msgGeolevels_HelpText#-}</div>
			<div class="content">
				{-include file="database_geography.tpl"-}
			</div>
		</div>
		<div id="DBConfig_Events">
			<div class="helptext hidden"></div>
			<div class="content">
				{-include file="database_events.tpl"-}
			</div>
		</div>
		<div id="DBConfig_Causes">
			<div class="helptext hidden"></div>
			<div class="content">
				{-include file="database_causes.tpl"-}
			</div>
		</div>
		<div id="DBConfig_tabs-5">
			<div class="helptext hidden">
			</div>
			<div class="content">
			</div>
		</div>
		<div id="DBConfig_tabs-6">
			<div class="helptext hidden">
			</div>
			<div class="content">
			</div>
		</div>
		<div id="DBConfig_Users">
			<div class="helptext hidden">
			</div>
			<div class="content">
				{-include file="database_users.tpl"-}
			</div>
		</div>
	</div>
