<?php
/*
  DesInventar - http://www.desinventar.org
  (c) Corporacion OSSO
*/
namespace DesInventar\Legacy;

class DIObject
{
    const ERR_NO_ERROR = 1;
    const ERR_DEFAULT_ERROR = -1;

    protected static $def = array();
    public $sFieldKeyDef = '';
    public $sFieldDef = '';
    public $status = null;

    public $oOldField;
    public $oField;
    public $oFieldType;

    protected $RegionId = '';

    public function __construct($prmSession)
    {
        $this->session  = $prmSession;
        if ($this->session) {
            $this->RegionId = $this->session->RegionId;
        }
        $num_args = func_num_args();
        if ($num_args >= 1) {
            $this->session = func_get_arg(0);
            if ($num_args >= 3) {
                $this->sFieldKeyDef = func_get_arg(1);
                $this->sFieldDef    = func_get_arg(2);
            }
        }
        $this->oField = array();
        $this->oField['info'] = array();
        $this->oFieldType=array();
        $this->initializeFields();
        $this->createFields($this->sFieldKeyDef);
        $this->createFields($this->sFieldDef);
        $this->set('RegionId', $this->RegionId);
        $LangIsoCode = 'eng';
        if ($this->session && $this->session->q->RegionId != 'core') {
            $LangIsoCode = $this->session->RegionLangIsoCode; //getDBInfoValue('LangIsoCode');
        }
        $this->set('LangIsoCode', $LangIsoCode);
        $this->set('RecordCreation', gmdate('c'));
        $this->set('RecordUpdate', gmdate('c'));

        $this->status = new DIStatus();
    }

    public function initializeFields($LangIsoCode = '')
    {
        if (count(static::$def) > 0) {
            $sFieldKeyDef = '';
            $sFieldDef = '';
            foreach (static::$def as $field_name => $field) {
                $field_new = $field_name . '/' . $field['type'];
                if (isset($field['pk'])) {
                    if ($sFieldKeyDef != '') {
                        $sFieldKeyDef .= ',';
                    }
                    $sFieldKeyDef .= $field_new;
                } else {
                    if ($sFieldDef != '') {
                        $sFieldDef .= ',';
                    }
                    $sFieldDef .= $field_new;
                }
            }
            $this->sFieldKeyDef = $sFieldKeyDef;
            $this->sFieldDef = $sFieldDef;
        }
    }

    public function createFields($prmFieldDef, $LangIsoCode = '')
    {
        if ($LangIsoCode == '') {
            $obj = &$this->oField['info'];
        } else {
            $obj = &$this->oField[$LangIsoCode];
        }
        $sFields = preg_split('#,#', $prmFieldDef);
        foreach ($sFields as $sKey => $sValue) {
            $oItem = preg_split('#/#', $sValue);
            $sFieldName = $oItem[0];
            $sFieldType = $oItem[1];
            $this->oFieldType[$sFieldName] = $sFieldType;
            if ($sFieldType == 'STRING') {
                $obj[$sFieldName] = '';
            }
            if ($sFieldType == 'VARCHAR') {
                $obj[$sFieldName] = '';
            }
            if ($sFieldType == 'TEXT') {
                $obj[$sFieldName] = '';
            }
            if ($sFieldType == 'DATETIME') {
                $obj[$sFieldName] = '';
            }
            if ($sFieldType == 'DATE') {
                $obj[$sFieldName] = '';
            }
            if ($sFieldType == 'INTEGER') {
                $obj[$sFieldName] = 0;
            }
            if ($sFieldType == 'FLOAT') {
                $obj[$sFieldName] = 0.0;
            }
            if ($sFieldType == 'DOUBLE') {
                $obj[$sFieldName] = 0.0;
            }
            if ($sFieldType == 'CURRENCY') {
                $obj[$sFieldName] = 0.0;
            }
            if ($sFieldType == 'BOOLEAN') {
                $obj[$sFieldName] = 1;
            }
        }
    }

    public function fieldDefToArray($fieldDef)
    {
        $fields = [];
        foreach (explode(',', $fieldDef) as $field) {
            list($name, $type) = explode('/', $field);
            $fields[] = ['name' => $name, 'type' => $type];
        }
        return $fields;
    }

    public function get($prmKey, $LangIsoCode = '')
    {
        $Value = '';
        try {
            if ($LangIsoCode == '') {
                $LangIsoCode = 'info';
            }
            if (array_key_exists($prmKey, $this->oField[$LangIsoCode])) {
                $Value = $this->oField[$LangIsoCode][$prmKey];
            }
        } catch (Exception $e) {
            showErrorMsg(debug_backtrace(), $e, '');
        }
        return $Value;
    }

    public function getType($prmKey)
    {
        try {
            return $this->oFieldType[$prmKey];
        } catch (Exception $e) {
            showErrorMsg(debug_backtrace(), $e, '');
        }
    }

    public function set($prmKey, $prmValue, $LangIsoCode = '')
    {
        if ($LangIsoCode == '') {
            $obj = &$this->oField['info'];
        } else {
            $obj = &$this->oField[$LangIsoCode];
        }
        $iReturn = self::ERR_DEFAULT_ERROR;
        if (isset($obj[$prmKey])) {
            $sValue = $prmValue;

            $sFieldType = $this->oFieldType[$prmKey];
            if ($sFieldType == 'STRING') {
                // Remove special chars...
                $sValue = trim($sValue);
                // Remove Double Quotes to prevent failures in SQL Queries
                $sValue = preg_replace('/"/', '', $sValue);
            }
            if ($sFieldType == 'BOOLEAN') {
                if (strtolower($sValue) . '' == 'on') {
                    $sValue = 1;
                }
                if (strtolower($sValue) . '' == 'true') {
                    $sValue = 1;
                }
                if (strtolower($sValue) . '' == 'off') {
                    $sValue = 0;
                }
                if (strtolower($sValue) . '' == 'false') {
                    $sValue = 0;
                }
                if ($sValue . '' == '') {
                    $sValue = 0;
                }
                if ($sValue . '' == '1') {
                    $sValue = 1;
                }
                if ($sValue . '' == '0') {
                    $sValue = 0;
                }
            }
            if (($sFieldType == 'INTEGER') ||
                ($sFieldType == 'DOUBLE' ) ||
                ($sFieldType == 'FLOAT'  ) ||
                ($sFieldType == 'CURRENCY')) {
                if ($sValue == '') {
                    $sValue = 0;
                }
            }
            $obj[$prmKey] = $sValue;
            $iReturn = self::ERR_NO_ERROR;
        }
        return $iReturn;
    }

    public function setFromArray($prmArray)
    {
        $iReturn = self::ERR_NO_ERROR;
        foreach ($prmArray as $sKey => $sValue) {
            $this->set($sKey, $sValue);
        }
        return $iReturn;
    }

    public function existField($prmField, $section = 'info')
    {
        return array_key_exists($prmField, $this->oField[$section]);
    }

    public static function padNumber($iNumber, $iLen)
    {
        $sNumber = '' . $iNumber;
        while (strlen($sNumber) < $iLen) {
            $sNumber = '0' . $sNumber;
        }
        return $sNumber;
    }

    public function getFieldList()
    {
        $i = 0;
        $Value = '';
        foreach (array_keys($this->oField['info']) as $Field) {
            if ($i>0) {
                $Value .= ',';
            }
            $Value .= $Field;
            $i++;
        }
        return $Value;
    }

    public function importFromCSV($cols, $values)
    {
        return ERR_NO_ERROR;
    }
}
