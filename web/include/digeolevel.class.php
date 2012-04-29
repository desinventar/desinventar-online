<script language="php">
/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
*/

class DIGeoLevel extends DIRecord
{
	public static $def = array(
		array(
			'name' => 'GeoLevelId',
			'type' => 'INTEGER',
			'pk' => 1
		),
		array(
			'name' => 'LangIsoCode',
			'type' => 'VARCHAR',
			'size' => 3,
			'pk' => 1
		),
		array(
			'name' => 'RegionId',
			'type' => 'VARCHAR',
			'size' => 50
		),
		array(
			'name' => 'GeoLevelName',
			'type' => 'VARCHAR',
			'size' => 50,
			'default' => '---'
		),
		array(
			'name' => 'GeoLevelDesc',
			'type' => 'TEXT'
		),
		array(
			'name' => 'GeoLevelActive',
			'type' => 'INTEGER',
			'default' => 0
		),
		array(
			'name' => 'RecordCreation',
			'type' => 'DATETIME'
		),
		array(
			'name' => 'RecordSync',
			'type' => 'DATETIME'
		),
		array(
			'name' => 'RecordUpdate',
			'type' => 'DATETIME'
		)
	);
	public function __construct($prmSession)
	{
		$this->sTableName   = "GeoLevel";
		$this->sPermPrefix  = "GEOLEVEL";
		/*
		$this->sFieldKeyDef = "GeoLevelId/INTEGER," .
		                      "LangIsoCode/STRING";
		$this->sFieldDef    = "RegionId/STRING," .
		                      "GeoLevelName/STRING," .
		                      "GeoLevelDesc/STRING," .  
		                      "GeoLevelActive/INTEGER," .
		                      "RecordCreation/DATETIME," .
		                      "RecordSync/DATETIME," .
		                      "RecordUpdate/DATETIME";
		*/
		parent::__construct($prmSession);
		$this->set("GeoLevelActive", 1);

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
			if ($num_args >= 4)
			{
				$prmGeoLevelName = func_get_arg(3);
				$this->set('GeoLevelName', $prmGeoLevelName);
			}
			$this->load();
		}
	} // __construct

	public function getCreateTable()
	{
		$pk = '';
		$query = '';
		foreach(static::$def as $field)
		{
			if ($query != '') { $query .=', '; }
			$query .= $field['name'];
			$type = $field['type'];
			if ($type == 'VARCHAR')
			{
				$type .= '(' . $field['size'] . ')';
			}
			$query .= ' '. $type;
			if (isset($field['default']))
			{
				$query .= ' DEFAULT ';
				if ($field['type'] == 'VARCHAR') { $query.= "'"; }
				$query .= $field['default'];
				if ($field['type'] == 'VARCHAR') { $query.= "'"; }
			}
			if (isset($field['pk']))
			{
				if ($pk != '') { $pk .= ','; }
				$pk .= $field['name'];
			}
		}
		$query = 'CREATE TABLE ' . $this->sTableName . ' (' . $query;
		if ($pk != '')
		{
			$query .= ', PRIMARY KEY(' . $pk . ')';
		}
		$query .= ')';
		return $query;
	} #getCreateTable

	public function getMaxGeoLevel()
	{
		$iMaxVal = -1;
		$sQuery = 'SELECT GeoLevelId FROM GeoLevel WHERE LangIsoCode="' . $this->get('LangIsoCode') . '" ORDER BY GeoLevelId';
		foreach($this->q->dreg->query($sQuery, PDO::FETCH_ASSOC) as $row)
		{
			$iMaxVal = $row['GeoLevelId'];
		}
		return $iMaxVal;
	} // function

	public function validateCreate()
	{
		$iReturn = 1;
		$iReturn = $this->validateNotNull(-31, 'GeoLevelId');
		if ($iReturn > 0)
		{
			$iReturn = $this->validatePrimaryKey(-32);
		}
		return $iReturn;
	}

	public function validateUpdate($bStrict)
	{
		$iReturn = parent::validateUpdate($bStrict);
		$iReturn = $this->validateNotNull(-33, 'GeoLevelName');
		if ($iReturn > 0)
		{
			$iReturn = $this->validateUnique(-34, 'GeoLevelName', true);
		}
		$this->status->status = $iReturn;
		return $iReturn;
	}

	public function importFromCSV($cols, $values)
	{
		$iReturn = parent::importFromCSV($cols, $values);
		$this->set('GeoLevelId', $values[0]);
		$this->set('GeoLevelName', $values[1]);
		if ($iReturn > 0)
		{
			$this->status->status = $iReturn;
		}
		return $iReturn;
	} //function
} //class

</script>
