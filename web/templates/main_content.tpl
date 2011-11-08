<!-- divDatabaseHeader -->
<!-- divDatabaseConfiguration - Database Configuration Parameters -->
{-include file="main_dbconfig.tpl"-}

<!-- divQueryResults - Results of queries -->
{-include file="main_queryresults.tpl"-}

<!-- "divRegionList" -->
{-include file="region_list.tpl"-}

<div class="contentBlock" id="divDatabaseExport" style="display:none;">
	{-include file="database_export.tpl"-}
</div>

<div class="contentBlock" id="divDatabaseImport" style="display:none;">
	{-include file="database_import.tpl"-}
</div>

<!-- Import datacards-->
<div class="contentBlock" id="divDatacardsImport" style="display:none;">
</div>

<div class="contentBlock" id="divAdminDatabase" style="display:none;">
	{-include file="region.tpl"-}
</div>