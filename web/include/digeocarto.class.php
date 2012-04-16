<script language="php">
/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
*/

class DIGeoCarto extends DIRecord
{
	public function __construct($prmSession)
	{
		$this->sTableName   = "GeoCarto";
		$this->sPermPrefix  = "GEOLEVEL";
		$this->sFieldKeyDef = "GeoLevelId/INTEGER," .
		                      "GeographyId/STRING," .
		                      "LangIsoCode/STRING";
		$this->sFieldDef    = "RegionId/STRING," .
		                      "GeoLevelLayerFile/STRING," .
		                      "GeoLevelLayerName/STRING," .
		                      "GeoLevelLayerCode/STRING," .
		                      "RecordCreation/DATETIME," .
		                      "RecordSync/DATETIME," .
		                      "RecordUpdate/DATETIME";
		parent::__construct($prmSession);

		$num_args = func_num_args();
		if ($num_args >= 2)
		{
			$prmGeoLevelId = func_get_arg(1);
			$this->set('GeoLevelId', $prmGeoLevelId);
			if ($num_args >= 3)
			{
				$prmLangIsoCode = func_get_arg(2);
				$this->set('LangIsoCode', $prmLangIsoCode);
			}
			$this->load();
		}
	} // __construct

	public function getDBFFilename()
	{
		$filename = VAR_DIR . '/database/' . $this->RegionId . '/' . $this->get('GeoLevelLayerFile') . '.dbf';
		return $filename;
	}
} #class

</script>
