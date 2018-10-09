	<div id="DBConfig_tabs">
		<ul>
			<li>
				<a
					class="classDBConfig_tabs"
					href="#DBConfig_Info"
					data-cmd="cmdDBInfoEdit"
					data-url="{-$desinventarURL-}/info.php"
					data-id="DBConfig_Info"
				>{-#mreginfo#-}</a>
			</li>
			<li title="{-#msgGeolevels_Tooltip#-}">
				<a class="classDBConfig_tabs" href="#DBConfig_Geolevels" data-id="DBConfig_GeoLevels">{-#msgDBConfig_Geolevels#-}</a>
			</li>
			<li title="{-#msgGeography_Tooltip#-}">
				<a class="classDBConfig_tabs" href="#DBConfig_Geography">{-#mgeography#-}</a>
			</li>
			<li>
				<a class="classDBConfig_tabs" href="#DBConfig_Events">{-#msgDBConfig_Events#-}</a>
			</li>
			<li>
				<a class="classDBConfig_tabs" href="#DBConfig_Causes">{-#mcauses#-}</a>
			</li>
			<li>
				<a
					class="classDBConfig_tabs"
					href="#DBConfig_tabs-6"
					data-cmd="cmdDBInfoEEField"
					data-url="{-$desinventarURL-}/extraeffects.php"
					data-id="DBConfig_AdditionalEffects"
				>{-#meeffects#-}</a>
			</li>
			<li>
				<a
					class="classDBConfig_tabs"
					href="#DBConfig_Users"
					data-id="DBConfig_Users"
				>{-#msgDBConfig_RolesAndDiffusion#-}</a>
			</li>
		</ul>
		<div id="DBConfig_Info">
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
