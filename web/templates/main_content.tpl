<!-- divDatabaseHeader -->
<!-- divDatabaseConfiguration - Database Configuration Parameters -->
{-include file="main_dbconfig.tpl"-}

<!-- divQueryResults - Results of queries -->
{-include file="main_queryresults.tpl"-}

<!-- "divRegionList" -->
{-include file="region_list.tpl"-}

<!-- divDatabaseBackup -->
{-include file="database_export.tpl"-}

<!-- divDatabaseImport -->
{-include file="database_import.tpl"-}

<!-- divDatabaseEdit -->
{-include file="database_edit.tpl"-}

<!-- Import datacards-->
<div class="contentBlock" id="divDatacardsImport" style="display:none;">
</div>

<div class="contentBlock" id="divAdminDatabase" style="display:none;">
	{-include file="region.tpl"-}
</div>