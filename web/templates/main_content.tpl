<!-- divDatabaseConfiguration - Database Configuration Parameters -->
{-include file="main_dbconfig.tpl" -}

<!-- Import datacards-->
<div class="contentBlock" id="divDatacardsImport" style="display:none;"></div>

<!-- Datacard Edit Window-->
<div class="contentBlock" id="divDatacardsShow">
</div>

<!-- Results of queries divQueryResults -->
{-include file="main_queryresults.tpl" -}

<div class="contentBlock" id="divDatabaseList" >
	{-include file="header_simple.tpl" -}
	{-include file="showlistdb.tpl" -}
</div> <! id="divDatabaseList" -->
<!-- divDatabaseBackup -->
{-include file="database_backup.tpl" -}
