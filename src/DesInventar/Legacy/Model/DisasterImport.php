<?php
namespace DesInventar\Legacy\Model;

use DesInventar\Common\Util;

class DisasterImport extends Disaster
{
    protected $importFieldDef = '';

    public function __construct($session, $disasterId, $importFieldDef)
    {
        parent::__construct($session, $disasterId);
        $this->importFieldDef = $importFieldDef;
    }

    public function importFromArray($values)
    {
        foreach ($this->importFieldDef as $field) {
            $this->importValueFromArray($field['name'], $values, $field['index']);
        }
    }

    public function importValueFromArray($fieldName, $values, $indexes)
    {
        $type = $this->getType($fieldName);
        if (!is_array($indexes)) {
            $indexes = [ $indexes ];
        }
        $value = null;
        foreach ($indexes as $index) {
            if (isset($values[$index]) && strlen((string)$values[$index])> 0) {
                $value = $this->filterValue($values[$index]);
                break;
            }
        }
        switch ($type) {
            case 'RAW':
                $value = trim($value);
                break;
            case 'VALUE':
            case 'INTEGER':
            case 'DOUBLE':
            case 'CURRENCY':
            case 'BOOLEAN':
                $value = $this->valueToDIField($value);
                break;
            case 'SECTOR':
                $value = $this->sectorToDIField($value);
                break;
            case 'STRING':
            case 'DATE':
            default:
                $value = $this->stringToDIField($value);
                break;
        }
        $this->set($fieldName, $value);
        return $value;
    }

    public function stringToDIField($prmValue)
    {
        $util = new Util();
        $prmValue = $this->filterValue($prmValue);
        $prmValue = trim($prmValue);

        if ($prmValue === '0') {
            $prmValue = '';
        }
        return $prmValue;
    }

    public function sectorToDIField($prmValue)
    {
        $value = $this->valueToDIField($prmValue);
        if ($value > 0) {
            $value = -1;
        }
        return $value;
    }

    public function currencyToDIField($prmValue)
    {
        $util = new Util();
        $isCurrency = $prmValue[0] === '$';
        $prmValue = $this->filterValue($prmValue);
        $lastDotIndex = strrpos($prmValue, '.');
        if ($lastDotIndex === strlen($prmValue) - 3) {
            $prmValue = substr($prmValue, 0, $lastDotIndex) . ';' . substr($prmValue, $lastDotIndex + 1);
        }
        if ($isCurrency) {
            $prmValue = $util->replaceChars('/\./', '', $prmValue);
        }
        $prmValue = $util->replaceChars('/;/', '.', $prmValue);
        $prmValue = trim($prmValue);
        if (is_numeric($prmValue)) {
            return $prmValue;
        }
        return 0;
    }

    public function valueToDIField($prmValue)
    {
        $util = new Util();
        $value = '';
        $prmValue = $this->filterValue($prmValue);
        $prmValue = $util->replaceChars('/;/', '', $prmValue);
        $prmValue = trim($prmValue);
        if (is_numeric($prmValue)) {
            return $prmValue;
        }
        switch (strtolower($prmValue)) {
            case '':
            case 'no hubo':
            case 'no':
            case 'pen':
            case 'onpd':
            case 'pp':
            case 'a':
            case 'ver':
            case 'pend':
            case 'puentes':
            case 'nd':
            case '?':
            case 'n':
            case 'p':
                $value = 0;
                break;
            case 'hubo':
            case 'Hubo':
            case 'x':
            case 'si':
            case 'si(1)':
            case 'si (1)':
            case 'si (2)':
            case 's':
            case 'yes':
                $value = -1;
                break;
            case 'sin':
            case 'sd':
            case 'no se sabe':
                $value = 0; // -2
                break;
            default:
                $value = '';
                break;
        }
        return $value;
    }

    public function filterValue($prmValue)
    {
        $util = new Util();
        $prmValue = $util->replaceChars('/\"/', '', $prmValue);
        $prmValue = $util->replaceChars('/\$/', '', $prmValue);
        $prmValue = $util->replaceChars('/,/', '.', $prmValue);
        $prmValue = trim($prmValue);
        return $prmValue;
    }
}
