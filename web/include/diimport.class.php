<?php
/*
  DesInventar - http://www.desinventar.org
 (c) Corporacion OSSO
*/
namespace DesInventar\Legacy;

use DesInventar\Legacy\Model\DisasterImport;
use DesInventar\Legacy\Model\Event;
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

    public function __construct($prmSessionId, $fileName)
    {
        $this->session = $prmSessionId;
        if (!empty($fileName) && file_exists($fileName)) {
            $this->loadParamsFromFile($fileName);
        }
    }

    public function loadParamsFromFile($fileName)
    {
        if (!file_exists($fileName)) {
            throw new \Exception('Import Error: Cannot find field definition file');
        }
        $json = file_get_contents($fileName);
        $params = json_decode($json, true);
        if (empty($params)) {
            throw new \Exception('Import Error: Cannot import field definitions from file');
        }
        $this->params = array_merge($this->defaultParams, $params);
        $this->fields = $this->getFieldDef($params['fields']);
        $this->geography = $params['geography'];
        $this->event = $params['event'];
        $this->cause = $params['cause'];
        $this->fixedValues = $params['values'];
        return true;
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

    public function getGeographyCode($values)
    {
        return $values[$this->geography['code']];
    }

    public function getGeographyId($code)
    {
        return GeographyItem::getIdByCode($this->session, $code, '');
    }

    public function getEventId($values)
    {
        $name = $this->getName($values, $this->event);
        $id = Event::getIdByName($this->session, $name);
        if (empty($id)) {
            throw new \Exception('Event Error: ' . $name);
        }
        return $id;
    }

    public function getCauseId($values)
    {
        $name = $this->getName($values, $this->cause);
        $id = $name == '' ? 'UNKNOWN' : Cause::getIdByName($this->session, $name);
        if (empty($id)) {
            throw new \Exception('Cause Error: ' . $name);
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
            $d = new DisasterImport($this->session, '', $this->fields);
            $d->importFromArray($a);

            $DisasterSerial = $d->get('DisasterSerial');
            // Validate mandatory fields
            if (empty($DisasterSerial)) {
                printf('DisasterSerial is empty. Line: %4d' . "\n", $line);
                continue;
            }

            if (empty($d->get('DisasterBeginTime'))) {
                printf('DisasterBeginTime is empty. Line: %4ds %-10s' ."\n", $line, $DisasterSerial);
                continue;
            }

            // printf("%4d %s\n", $line, $DisasterSerial);
            $disasterId = $d->findIdBySerial($DisasterSerial);
            if (!empty($disasterId)) {
                $d->set('DisasterId', $disasterId);
                $d->load();
                $d->importFromArray($a);
            }

            foreach ($this->fixedValues as $fieldName => $value) {
                $d->set($fieldName, $value);
            }

            $geographyCode = $this->getGeographyCode($a);
            $geographyId = $this->getGeographyId($geographyCode);
            $d->set('GeographyId', $geographyId);
            if ($geographyId == '') {
                printf('GEOGRAPHY ERROR : %4d %-10s %-20s' . "\n", $line, $DisasterSerial, $geographyCode);
            }

            try {
                $d->set('EventId', $this->getEventId($a));
            } catch (Exception $e) {
                printf('EVENT ERROR : %4d %-10s %s' . "\n", $line, $DisasterSerial, $e->getMessage());
            }

            try {
                $d->set('CauseId', $this->getCauseId($a));
            } catch (Exception $e) {
                printf('CAUSE ERROR : %4d %-10s %-20s' . "\n", $line, $DisasterSerial, $e->getMessage());
            }
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
                printf('Error en validación serial: %s Error: ' . "\n", $DisasterSerial, $r);
            }

            if (($line > 0) && (($line % 100) == 0)) {
                printf('%04d' . "\n", $line);
            }

            if (!$prmImport) {
                continue;
            }

            $Cmd = '';
            $i = 0;
            if ($bExist < 0) {
                $i = $d->insert(true, false);
                if ($i < 0) {
                    printf('%5d %-10s %3d' . "\n", $line, $DisasterSerial, $i);
                }
                $Cmd = 'INSERT';
                continue;
            }
            $i = $d->update(true, false);
            $Cmd = 'UPDATE';

            if ($i < 0) {
                printf('%5d %-10s %3d' . "\n", $line, $DisasterSerial, $i);
            }
        }
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
