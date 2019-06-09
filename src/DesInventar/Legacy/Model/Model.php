<?php
namespace DesInventar\Legacy\Model;

use \Exception;

class Model
{
    protected const ERR_NO_ERROR = 1;
    protected const ERR_DEFAULT_ERROR = -1;
    protected const ERR_UNKNOWN_ERROR = -1;

    protected static $def = array();
    public $sFieldKeyDef = '';
    public $sFieldDef = '';
    public $status = null;

    public $oOldField;
    public $oField;
    public $oFieldType;

    protected $RegionId = '';
    protected $session = null;

    public function __construct($prmSession)
    {
        $this->session  = $prmSession;
        if ($this->session && isset($this->session->q)) {
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
        if ($this->session && isset($this->session->q) && $this->session->q->RegionId != 'core') {
            $LangIsoCode = $this->session->RegionLangIsoCode; //getDBInfoValue('LangIsoCode');
        }
        $this->set('LangIsoCode', $LangIsoCode);
        $this->set('RecordCreation', gmdate('c'));
        $this->set('RecordUpdate', gmdate('c'));

        $this->status = new Status();
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

    protected function explodeField($fieldDef, $separator)
    {
        $fields = preg_split('#' . $separator . '#', $fieldDef);
        if ($fields === false) {
            $fields = [];
        }
        return $fields;
    }

    protected function explodeFieldList($fieldList)
    {
        return $this->explodeField($fieldList, ',');
    }

    protected function explodeFieldDef($fieldDef)
    {
        $values = $this->explodeField($fieldDef, '/');
        if (empty($values)) {
            return [
                0 => '',
                1 => ''
            ];
        }
        return $values;
    }

    public function createFields($prmFieldDef, $LangIsoCode = '')
    {
        if ($LangIsoCode == '') {
            $obj = &$this->oField['info'];
        } else {
            $obj = &$this->oField[$LangIsoCode];
        }
        foreach ($this->explodeFieldList($prmFieldDef) as $sValue) {
            $oItem = $this->explodeFieldDef($sValue);
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
            if (in_array($sFieldType, ['INTEGER', 'SECTOR'])) {
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
        if ($LangIsoCode == '') {
            $LangIsoCode = 'info';
        }
        if (array_key_exists($prmKey, $this->oField[$LangIsoCode])) {
            return $this->oField[$LangIsoCode][$prmKey];
        }
        return false;
    }

    public function getType($prmKey)
    {
        if (isset($this->oFieldType[$prmKey])) {
            return $this->oFieldType[$prmKey];
        }
        return null;
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
            if (in_array($sFieldType, ['INTEGER', 'SECTOR', 'DOUBLE', 'FLOAT', 'CURRENCY'])) {
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
}
