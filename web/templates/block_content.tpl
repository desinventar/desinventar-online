<!-- divDatabaseHeader -->
<div class="contentBlock" id="divLoading">
</div>
<div class="contentBlock" id="divDatabasePrivate" style="display:none;">
	{-include file="database_private.tpl"-}
</div>

<div class="contentBlock" id="divDatabaseConfiguration" style="display:none;">
	{-include file="block_dbconfig.tpl"-}
</div>

<!-- divQueryResults - Results of queries -->
<div class="contentBlock" id="divQueryResults" style="display:none;">
	{-include file="block_queryresults.tpl"-}
</div> <!-- end div id=divQueryResults -->

<!-- "divRegionList" -->
{-include file="region_list.tpl"-}
<!-- Database Upload -->
{-include file="database_upload_ext.tpl"-}
<!-- Database Create -->
{-include file="database_create_ext.tpl"-}

<!-- Database set UserAdmin -->
{-include file="userperm_admin_ext.tpl"-}

<!-- Import datacards-->
<div class="contentBlock" id="divDatacardsImport" style="display:none;">
</div>

<div class="contentBlock" id="divAdminDatabase" style="display:none;">
	{-include file="region.tpl"-}
</div>
