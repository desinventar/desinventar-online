<script language="php">
#
# DesInventar - http://www.desinventar.org
# (c) 1998-2012 Corporacion OSSO
#

function database_delete($core, $prmRegionId)
{
	$answer = ERR_NO_ERROR;
	$database_dir = VAR_DIR .'/database/'. $prmRegionId;
	rrmdir($database_dir);
	$query = 'DELETE FROM Region WHERE RegionId="' . $prmRegionId . '"';
	$pdo = $core->query($query);
	$query = 'DELETE FROM RegionAuth WHERE RegionId="' . $prmRegionId . '"';
	$pdo = $core->query($query);
	return $answer;
} #database_delete()

</script>
