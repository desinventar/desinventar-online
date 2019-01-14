<?php
namespace DesInventar\Legacy;

use DesInventar\Legacy\Model\DisasterImport;
use DesInventar\Legacy\Model\Event;
use DesInventar\Legacy\Model\Cause;
use DesInventar\Legacy\Model\GeographyItem;

class DIImport
{
    protected $fields = [];
    protected $event = [];
    protected $cause = [];
    protected $geography = [];
    protected $values = [];
    protected $defaultParams = [
        'headerCount' => 3,
        'offset' => 0,
        'lineCount' => 1000000,
        'geography' => [],
        'event' => [],
        'cause' => [],
        'values' => [],
        'fields' => []
    ];

    public function __construct($prmSessionId, $params)
    {
        $this->session = $prmSessionId;
        $this->params = array_merge($this->defaultParams, $params);
        $this->fields = $this->getFieldDef($this->params['fields']);
        $this->geography = $this->params['geography'];
        $this->event = $this->params['event'];
        $this->cause = $this->params['cause'];
        $this->fixedValues = $this->params['values'];
    }

    public static function loadParamsFromFile($fileName)
    {
        if (!file_exists($fileName)) {
            throw new \Exception('Import Error: Cannot find field definition file');
        }
        $json = file_get_contents($fileName);
        $params = json_decode($json, true);
        if (empty($params)) {
            throw new \Exception('Import Error: Cannot import field definitions from file');
        }
        return $params;
    }

    public function getFieldDef($fields)
    {
        $def = [];
        $lastIndex = 0;
        foreach ($fields as $field) {
            $fieldName = $field[0];
            $comment = isset($field[1]) ?  $field[1] : '';
            $index = [ $lastIndex ];
            if (count($field) > 2) {
                $index = array_slice($field, 2);
            }
            $lastIndex = end($index) + 1;
            $def[$fieldName] = [
                'comment' => $comment,
                'index' => array_merge(
                    isset($def[$fieldName]['index']) ? $def[$fieldName]['index'] : [],
                    $index
                )
            ];
        }
        foreach ($def as $fieldName => $values) {
            $list[] = [
                'name'=> $fieldName,
                'comment' => $values['comment'],
                'index' => $values['index']
            ];
        }
        return $list;
    }

    public function validateFromCSV($FileName, $ObjectType)
    {
        return $this->importFromCSV($FileName, $ObjectType, false);
    }

    public function findGeographyIdByName($fullName, $separator)
    {
        $id = '';
        $names = explode($separator, $fullName);
        foreach ($names as $name) {
            $id = GeographyItem::getIdByName($this->session, $name, $id);
        }
        return $id;
    }

    public function getGeographyId($values)
    {
        $type = !empty($this->geography['type']) ? $this->geography['type'] : 'code';
        switch ($type) {
            case 'fullname':
                $name = $values[$this->geography['index']];
                $id = $this->findGeographyIdByName($name, $this->geography['separator']);
                if (empty($id)) {
                    throw new \Exception('Error trying to match geography name: ' . $name);
                }
                return $id;
            case 'code':
            default:
                $code = $values[$this->geography['code']];
                $id = GeographyItem::getIdByCode($this->session, $code, '');
                if (empty($id)) {
                    throw new \Exception('Error trying to match geography code: ' . $code);
                }
                return $id;
        }
    }

    public function getEventId($values)
    {
        $name = $this->getName($values, $this->event);
        $id = Event::getIdByName($this->session, $name);
        if (empty($id)) {
            throw new \Exception('Error trying to match event: ' . $name);
        }
        return $id;
    }

    public function getCauseId($values)
    {
        $name = $this->getName($values, $this->cause);
        $id = $name == '' ? 'UNKNOWN' : Cause::getIdByName($this->session, $name);
        if (empty($id)) {
            throw new \Exception('Error trying to match cause: ' . $name);
        }
        return $id;
    }

    public function getName($values, $data)
    {
        $name = trim($values[$data['name']]);
        if (array_key_exists($name, $data['fixes'])) {
            $name = $data['fixes'][$name];
        }
        return $name;
    }

    public function importFromCSV($prmFileCSV, $prmImport)
    {
        $last_line = $this->params['lineCount'];
        $skipLines = $this->params['headerCount'] + $this->params['offset'];

        $last_line = $last_line + $skipLines + 1;
        $fh = fopen($prmFileCSV, 'r');
        $line = 1;
        while ($line <= $skipLines) {
            $a = fgetcsv($fh, 0, ',');
            $line++;
        }
        while ((! feof($fh)) && ($line < $last_line)) {
            $a = fgetcsv($fh, 0, ',');

            if (!is_array($a) || count($a) < 2) {
                continue;
            }
            for ($i = 0; $i<count($a); $i++) {
                $a[$i] = trim($a[$i]);
            }

            try {
                $d = $this->getImportObjectFromArray($a);
            } catch (\Exception $e) {
                fprintf(STDERR, '%4d,%s,%s' . "\n", $line, $d->get('DisasterSerial'), $e->getMessage());
                $line++;
                continue;
            }

            $DisasterSerial = $d->get('DisasterSerial');
            $line++;

            // Validate Effects and Save as DRAFT if needed
            if ($d->validateEffects(-61, 0) < 0) {
                $d->set('RecordStatus', 'DRAFT');
            }

            $bExist = $d->exist();
            if ($bExist < 0) {
                // Verificar solamente los datos, no importa nada...
                $r = $d->validateCreate(1);
            } else {
                $r = $d->validateUpdate(1);
            }
            if ($r < 0) {
                fprintf(STDERR, 'Error en validación serial: %s Error: ' . "\n", $DisasterSerial, $r);
            }

            if (($line > 0) && (($line % 100) == 0)) {
                fprintf(STDOUT, '%04d' . "\n", $line);
            }

            if (!$prmImport) {
                continue;
            }

            $Cmd = '';
            $i = 0;
            if ($bExist < 0) {
                $i = $d->insert(true, false);
                if ($i < 0) {
                    fprintf(STDERR, '%5d %-10s %3d' . "\n", $line, $DisasterSerial, $i);
                }
                $Cmd = 'INSERT';
                continue;
            }
            $i = $d->update(true, false);
            $Cmd = 'UPDATE';

            if ($i < 0) {
                fprintf(STDERR, '%5d %-10s %3d' . "\n", $line, $DisasterSerial, $i);
            }
        }
    }

    public function getImportObjectFromArray($values)
    {
        $d = new DisasterImport($this->session, '', $this->fields);
        $d->importFromArray($values);

        $disasterId = $d->findIdBySerial($d->get('DisasterSerial'));
        if (!empty($disasterId)) {
            $d->set('DisasterId', $disasterId);
            $d->load();
            $d->importFromArray($values);
        }

        foreach ($this->fixedValues as $fieldName => $value) {
            $d->set($fieldName, $value);
        }

        $geographyId = $this->getGeographyId($values);
        $d->set('GeographyId', $geographyId);

        $d->set('EventId', $this->getEventId($values));
        $d->set('CauseId', $this->getCauseId($values));
        return $d;
    }

    public function loadCSV($ocsv)
    {
        $handle = fopen($ocsv, 'r');
        $res = array();
        while (($data = fgetcsv($handle, 100, ',')) !== false) {
            $res[] = array($data[0], $data[1], $data[2], $data[3], $data[4]);
        }
        fclose($handle);
        return $res;
    }
}
