<?php
namespace DesInventar\Legacy;

use DesInventar\Legacy\Model\DisasterImport;
use DesInventar\Legacy\Model\Event;
use DesInventar\Legacy\Model\Cause;
use DesInventar\Legacy\Model\GeographyItem;
use \Exception;

class DIImport
{
    protected $session = null;
    protected $logger = null;
    protected $params = null;
    protected $fields = [];
    protected $geography = [];
    protected $event = [];
    protected $cause = [];
    protected $begintime = [];
    protected $serial = [];
    protected $values = [];
    protected $fixedValues = [];
    protected $defaultParams = [
        'headerCount' => 3,
        'offset' => 0,
        'lineCount' => 1000000,
        'geography' => [],
        'event' => [],
        'cause' => [],
        'begintime' => [],
        'serial' => [],
        'values' => [],
        'fields' => []
    ];

    public function __construct($prmSessionId, $logger, $params)
    {
        $this->session = $prmSessionId;
        $this->logger = $logger;
        $this->params = array_merge($this->defaultParams, $params);
        $this->fields = $this->getFieldDef($this->params['fields']);
        $this->geography = $this->params['geography'];
        $this->event = $this->params['event'];
        $this->cause = $this->params['cause'];
        $this->begintime = $this->params['begintime'];
        $this->serial = $this->params['serial'];
        $this->fixedValues = $this->params['values'];
    }

    public static function loadParamsFromFile($fileName)
    {
        if (!file_exists($fileName)) {
            throw new Exception('Import Error: Cannot find field definition file');
        }
        $json = (string) file_get_contents($fileName);
        $params = json_decode($json, true);
        if (empty($params)) {
            throw new Exception('Import Error: Cannot import field definitions from file');
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
        $list = [];
        foreach ($def as $fieldName => $values) {
            $list[] = [
                'name'=> $fieldName,
                'comment' => $values['comment'],
                'index' => $values['index']
            ];
        }
        return $list;
    }

    public function validateFromCSV($fileName, $objectType)
    {
        return $this->importFromCSV($fileName, $objectType);
    }

    public function findGeographyIdByName($fullName, $separator)
    {
        $geographyId = '';
        $names = explode($separator, $fullName);
        if (!$names) {
            $names= [];
        }
        foreach ($names as $name) {
            $geographyId = GeographyItem::getIdByName($this->session, $name, $geographyId);
        }
        return $geographyId;
    }

    public function getGeographyId($values)
    {
        $type = !empty($this->geography['type']) ? $this->geography['type'] : 'code';
        switch ($type) {
            case 'fullname':
                $name = $values[$this->geography['index']];
                $id = $this->findGeographyIdByName($name, $this->geography['separator']);
                if (empty($id)) {
                    throw new Exception('Error trying to match geography name: ' . $name);
                }
                return $id;
            case 'code':
            default:
                $code = $values[$this->geography['code']];
                if (isset($this->geography['width'])) {
                    $code = str_pad($code, $this->geography['width'], '0', STR_PAD_LEFT);
                }
                $id = GeographyItem::getIdByCode($this->session, $code);
                if (empty($id)) {
                    throw new Exception('Error trying to match geography code: ' . $code);
                }
                return $id;
        }
    }

    public function getEventId($values)
    {
        $type = !empty($this->event['type']) ? $this->event['type'] : 'name';
        if ($type === 'fixed') {
            return $this->event['id'];
        }

        // search eventId by name
        $name = $this->getName($values, $this->event);
        $id = Event::getIdByName($this->session, $name);
        if (empty($id)) {
            throw new Exception('Error trying to match event: ' . $name);
        }
        return $id;
    }

    public function getCauseId($values)
    {
        $type = !empty($this->cause['type']) ? $this->cause['type'] : 'name';

        if ($type === 'fixed') {
            return $this->cause['id'];
        }

        // search causeId by name
        $name = $this->getName($values, $this->cause);
        $id = $name == '' ? 'UNKNOWN' : Cause::getIdByName($this->session, $name);
        if (empty($id)) {
            throw new Exception('Error trying to match cause: ' . $name);
        }
        return $id;
    }

    public function getDisasterBeginTime($values)
    {
        $datetime = $values[$this->begintime['column']];
        return substr($datetime, 0, 10);
    }

    public function getDisasterSerial($year, $values)
    {
        $number = $values[$this->serial['column']];
        return $year . '-' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    public function getName($values, $data)
    {
        $name = trim($values[$data['name']]);
        if (array_key_exists($name, $data['fixes'])) {
            $name = $data['fixes'][$name];
        }
        return $name;
    }

    public function validateOnCreateOrUpdate($d)
    {
        $bExist = $d->exist();
        if ($bExist < 0) {
            return $d->validateCreate(1);
        }
        return $d->validateUpdate(1);
    }

    public function importFromCSV($prmFileCSV, $prmImport)
    {
        $last_line = $this->params['lineCount'];
        $skipLines = (int) $this->params['headerCount'] + $this->params['offset'];

        $last_line = $last_line + $skipLines + 1;
        $fh = fopen($prmFileCSV, 'r');
        if (!$fh) {
            throw new Exception('Cannot open CSV file');
        }
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
            $aLength = count($a);
            for ($i = 0; $i < $aLength; $i++) {
                $a[$i] = trim($a[$i]);
            }

            $DisasterSerial = '';
            try {
                $d = $this->getImportObjectFromArray($a);
                $DisasterSerial = $d->get('DisasterSerial');
                $this->logger->debug(sprintf('%d %s', $line, $DisasterSerial));
            } catch (\Exception $e) {
                fprintf(STDERR, '%4d,%s,%s' . "\n", $line, $DisasterSerial, $e->getMessage());
                $line++;
                continue;
            }
            $line++;

            // Validate Effects and Save as DRAFT if needed
            if ($d->validateEffects(-61, 0) < 0) {
                $d->set('RecordStatus', 'DRAFT');
            }

            $bExist = $d->exist();
            $r = $this->validateOnCreateOrUpdate($d);
            if ($r < 0) {
                fprintf(STDERR, 'Error en validación serial: %s Error: ' . "\n", $DisasterSerial, $r);
            }

            if (($line > 0) && (($line % 100) == 0)) {
                $this->logger->info(sprintf('LineCount: %04d', $line));
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
        $disasterBeginTime = $this->getDisasterBeginTime($values);
        $d->set('DisasterSerial', $this->getDisasterSerial(substr($disasterBeginTime, 0, 4), $values));
        echo $d->get('DisasterSerial') . "\n";

        $disasterId = $d->findIdBySerial($d->get('DisasterSerial'));
        if (!empty($disasterId)) {
            $d->set('DisasterId', $disasterId);
            $d->load();
            $d->importFromArray($values);
        }
        $d->set('DisasterBeginTime', $disasterBeginTime);

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
        if (!$handle) {
            return [];
        }
        $res = [];
        while (($data = fgetcsv($handle, 100, ',')) !== false) {
            $row = $data ? $data : [];
            if (count($row) > 0) {
                $res[] = array($row[0], $row[1], $row[2], $row[3], $row[4]);
            }
        }
        fclose($handle);
        return $res;
    }
}
