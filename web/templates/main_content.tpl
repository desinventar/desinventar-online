<!-- divDatabaseConfiguration - Database Configuration Parameters -->
{-include file="main_dbconfig.tpl" -}

<!-- divQueryResults - Results of queries -->
{-include file="main_queryresults.tpl" -}

<div class="contentBlock" id="divDatabaseList" >
	{-include file="header_simple.tpl" -}
	{-include file="showlistdb.tpl" -}
</div>

<!-- divDatabaseBackup -->
{-include file="database_backup.tpl" -}

<!-- divDatabaseImport -->
{-include file="database_import.tpl" -}

<!-- Import datacards-->
<div class="contentBlock" id="divDatacardsImport" style="display:none;">
</div>


